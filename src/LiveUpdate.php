<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class LiveUpdate
{
	/**
	 * Posts to be live updated
	 */
	private $pending_posts = null;

	/**
	 * Old permalinks to be removed from the index
	 */
	private static $old_permalinks = [];

	/**
	 * Old statuses to detect unpublishing
	 */
	private static $old_statuses = [];

	/**
	 * @var ApiClient
	 */
	private $api_client = null;

	function __construct(ApiClient $api_client)
	{
		$this->api_client = $api_client;
	}

	function bind()
	{
		// Primary live update hook using save_post instead of transition_post_status.
		// This ensures all post meta fields are saved before crawling.
		// Priority 999 guarantees this runs after all other save_post handlers.
		\add_action(
			'save_post',
			[$this, '__action_save_post_live_update'],
			999,
			3
		);

		// Handle posts moved to trash
		\add_action('trashed_post', [$this, '__action_trashed_post'], 10, 1);

		// Handle permanently deleted posts
		\add_action('deleted_post', [$this, '__action_deleted_post'], 10, 2);

		// Store old permalink and status before save to detect changes
		add_filter(
			'wp_insert_post_data',
			[__CLASS__, 'pre_save_store_old_data'],
			10,
			2
		);

		// Handle permalink changes
		add_action(
			'save_post',
			[$this, 'on_save_post_permalink_change'],
			10,
			3
		);

		// Send all queued updates at the end of request
		\add_action('shutdown', [$this, 'flush_updates']);
	}

	/**
	 * Handle live update in save_post hook.
	 */
	function __action_save_post_live_update($post_id, $post, $update)
	{
		if (!$post) {
			return;
		}

		if (!self::is_live_update_enabled()) {
			return;
		}

		if (\wp_is_post_revision($post_id)) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		$old_status = self::$old_statuses[$post_id] ?? null;
		$new_status = $post->post_status;

		// Handle unpublishing: was published, now is not
		if ($old_status === 'publish' && $new_status !== 'publish') {
			$permalink = Utils::get_public_permalink($post);
			if ($permalink) {
				$this->enqueue_url($permalink);
			}
			return;
		}

		// Only process currently published posts
		if ($new_status !== 'publish') {
			return;
		}

		// Gutenberg fires save_post multiple times when saving a post.
		// We only want to process the REST API request to avoid duplicates.
		$is_rest_request = defined('REST_REQUEST') && REST_REQUEST;

		// Detect bulk or trash actions from post list (not REST)
		$is_bulk_or_trash_action = false;
		if (
			isset($_REQUEST['action']) &&
			in_array($_REQUEST['action'], ['trash', 'delete', 'edit'], true)
		) {
			$is_bulk_or_trash_action = true;
		}
		if (
			isset($_REQUEST['action2']) &&
			in_array($_REQUEST['action2'], ['trash', 'delete', 'edit'], true)
		) {
			$is_bulk_or_trash_action = true;
		}

		// For block editor posts: only process REST API requests
		if (\use_block_editor_for_post_type($post->post_type)) {
			if (!$is_rest_request && !$is_bulk_or_trash_action) {
				return;
			}

			// Skip meta-box-loader requests
			if ($is_rest_request && isset($_GET['meta-box-loader'])) {
				return;
			}
		}

		// Skip CLI unless running cron (for scheduled posts)
		if (php_sapi_name() === 'cli' && !wp_doing_cron()) {
			return;
		}

		$this->enqueue_post($post);
	}

	/**
	 * Handle posts moved to trash.
	 */
	function __action_trashed_post($post_id)
	{
		$post = get_post($post_id);

		if (!$post) {
			return;
		}

		if (!self::is_live_update_enabled()) {
			return;
		}

		$permalink = Utils::get_public_permalink($post);
		if ($permalink) {
			$this->enqueue_url($permalink);
		}
	}

	/**
	 * Handle permanently deleted posts.
	 */
	function __action_deleted_post($post_id, $post)
	{
		if (!$post) {
			return;
		}

		if (!self::is_live_update_enabled()) {
			return;
		}

		if (
			$post->post_status === 'publish' ||
			$post->post_status === 'trash'
		) {
			$permalink = Utils::get_public_permalink($post);
			if ($permalink) {
				$this->enqueue_url($permalink);
			}
		}
	}

	/**
	 * Send all queued updates to Findkit API.
	 */
	function flush_updates()
	{
		if (empty($this->pending_posts)) {
			return;
		}

		$urls = [];

		foreach ($this->pending_posts as $item) {
			if ($item instanceof \WP_Post) {
				$urls[] = Utils::get_public_permalink($item);
			} elseif (is_string($item)) {
				$urls[] = $item;
			}
		}

		$urls = array_unique($urls);
		$urls = array_filter($urls);
		$urls = array_values($urls);

		if (empty($urls)) {
			return;
		}

		return $this->api_client->manual_crawl($urls);
	}

	/**
	 * Add a post to the update queue.
	 */
	function enqueue_post(\WP_Post $post)
	{
		$can_live_update = apply_filters(
			'findkit_can_live_update_post',
			php_sapi_name() !== 'cli' || wp_doing_cron(),
			$post
		);

		if (!$can_live_update) {
			return;
		}

		if ($this->pending_posts === null) {
			$this->pending_posts = [];
		}

		$this->pending_posts[] = $post;
	}

	/**
	 * Add a URL to the update queue.
	 */
	function enqueue_url(string $url)
	{
		$can_live_update = apply_filters(
			'findkit_can_live_update_post',
			php_sapi_name() !== 'cli' || wp_doing_cron(),
			null
		);

		if (!$can_live_update) {
			return;
		}

		if ($this->pending_posts === null) {
			$this->pending_posts = [];
		}

		$this->pending_posts[] = $url;
	}

	/**
	 * Check if live update is enabled.
	 */
	static function is_live_update_enabled(): bool
	{
		if (get_option('findkit_enable_live_update')) {
			return true;
		}

		if (
			defined('FINDKIT_ENABLE_LIVE_UPDATE') &&
			FINDKIT_ENABLE_LIVE_UPDATE
		) {
			return true;
		}

		return false;
	}

	/**
	 * Store old permalink and status before saving.
	 */
	public static function pre_save_store_old_data($data, $postarr)
	{
		if (empty($postarr['ID'])) {
			return $data;
		}

		$post_id = (int) $postarr['ID'];
		$old_post = get_post($post_id);

		if ($old_post) {
			if ($old_post->post_status === 'publish') {
				self::$old_permalinks[$post_id] = get_permalink($old_post);
			}
			self::$old_statuses[$post_id] = $old_post->post_status;
		}

		return $data;
	}

	/**
	 * Handle permalink changes.
	 */
	public function on_save_post_permalink_change($post_id, $post, $update)
	{
		if ($post->post_status !== 'publish') {
			return;
		}

		$old_permalink = self::$old_permalinks[$post_id] ?? null;
		$new_permalink = get_permalink($post);

		if ($old_permalink && $old_permalink !== $new_permalink) {
			$this->enqueue_url($old_permalink);
		}
	}
}
