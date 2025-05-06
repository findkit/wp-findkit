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

		// Send updates on shutdown when we can be sure that post changes have been saved
		\add_action('shutdown', [$this, 'flush_updates']);
	}

	function flush_updates()
	{
		if (empty($this->pending_posts)) {
			return;
		}

		$urls = [];

		foreach ($this->pending_posts as $post) {
			$urls[] = Utils::get_public_permalink($post);
		}

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
		if (
			!$is_rest_request &&
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
}
