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
	 * @var ApiClient
	 */
	private $api_client = null;

	function __construct(ApiClient $api_client)
	{
		$this->api_client = $api_client;
	}

	function bind()
	{
		// This is called always when post is being saved even when the post status does
		// not actually change.
		\add_action(
			'transition_post_status',
			[$this, '__action_transition_post_status'],
			10,
			3
		);

		add_filter(
			'wp_insert_post_data',
			[__CLASS__, 'pre_save_store_old_permalink'],
			10,
			2
		);

		add_action('save_post', [$this, 'on_save_post'], 10, 3);

		// Send updates on shutdown when we can be sure that post changes have been saved
		\add_action('shutdown', [$this, 'flush_updates']);
	}

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

		return $this->api_client->manual_crawl($urls);
	}

	function __action_transition_post_status($new_status, $old_status, $post)
	{
		if (!$post) {
			return;
		}

		if (!self::is_live_update_enabled()) {
			return;
		}

		// We can bail out if the status is not publish or is not transitioning from or
		// to it eg. it's a draft or draft being moved to trash for example
		if ('publish' !== $new_status && 'publish' !== $old_status) {
			return;
		}

		// Gutenberg fires transition_post_status twice when saving a post.
		// Once to /wp-admin/post.php and once to the REST API. Ignore the
		// former to avoid duplicate live updates. We cannot ignore the latter
		// because then we would ignore legit standalone updates via REST API.
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

		// If not REST, and not a bulk/trash action, and using block editor, skip to avoid duplicate
		if (
			!$is_rest_request &&
			!$is_bulk_or_trash_action &&
			\use_block_editor_for_post_type($post->post_type)
		) {
			return;
		}

		// Revision are not public
		if (\wp_is_post_revision($post)) {
			return;
		}

		$this->enqueue_post($post);
	}

	function enqueue_post(\WP_Post $post)
	{
		$is_development = defined('WP_ENV') && WP_ENV === 'development';

		$can_live_update = apply_filters(
			'findkit_can_live_update_post',
			// By default, do not enqueue post in cli because integrations might
			// cause unwanted live updates
			php_sapi_name() !== 'cli' &&
				// Disable live update when in explicit development mode
				!$is_development,
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

	public static function pre_save_store_old_permalink($data, $postarr)
	{
		if (empty($postarr['ID'])) {
			return $data;
		}
		$post_id = (int) $postarr['ID'];
		$old_post = get_post($post_id);
		if ($old_post) {
			$old_permalink = get_permalink($old_post);
			self::$old_permalinks[$post_id] = $old_permalink;
		}
		return $data;
	}

	public function on_save_post($post_id, $post, $update)
	{
		// Only for published posts
		if ($post->post_status !== 'publish') {
			return;
		}

		$old_permalink = self::$old_permalinks[$post_id] ?? null;
		$new_permalink = get_permalink($post);

		if ($old_permalink && $old_permalink !== $new_permalink) {
			$this->enqueue_url($old_permalink);
		}
	}

	function enqueue_url(string $url)
	{
		$is_development = defined('WP_ENV') && WP_ENV === 'development';

		$can_live_update = apply_filters(
			'findkit_can_live_update_post',
			php_sapi_name() !== 'cli' && !$is_development,
			null // No post object
		);

		if (!$can_live_update) {
			return;
		}

		if ($this->pending_posts === null) {
			$this->pending_posts = [];
		}

		// Store as a string URL
		$this->pending_posts[] = $url;
	}
}
