<?php

declare(strict_types=1);

namespace Findkit;

class LiveUpdate
{
	/**
	 * Posts to be live updated
	 */
	private $pending_posts = null;

	private $log_respones = false;

	function __construct()
	{
		$this->log_respones = \get_option('findkit_log_api_responses', false);
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

		$apikey = self::get_api_key();
		if (!$apikey) {
			error_log(
				'Findkit: ERROR No API key is set. Cannot flush live updates.'
			);
			return;
		}

		$project_id = get_option('findkit_project_id');

		$endpoint = apply_filters(
			'findkit_manual_crawl_endpoint',
			"https://api.findkit.com/v1/projects/$project_id/crawls",
			$project_id
		);

		if (!$project_id) {
			error_log(
				'Findkit: ERROR Live update is enabled but no project ID is set'
			);
			return;
		}

		$urls = [];

		foreach ($this->pending_posts as $post) {
			$urls[] = Utils::get_public_permalink($post);
		}

		if (empty($urls)) {
			return;
		}

		$json = wp_json_encode([
			'mode' => 'manual',
			'urls' => $urls,
			'message' => 'Live update from WordPress plugin v0.0.0',
		]);

		$response = wp_remote_request($endpoint, [
			'headers' => [
				'content-type' => 'application/json',
				'authorization' => 'Bearer ' . $apikey,
				'user-agent' => 'Findkit WordPress Plugin v0.0.0',
			],
			'method' => 'POST',
			'body' => $json,
			'timeout' => 20,
			'blocking' => $this->log_respones,
		]);

		if (\is_wp_error($response)) {
			error_log(
				'Findkit: Live update error: ' . $response->get_error_message()
			);
			return;
		}

		if ($this->log_respones) {
			$body = wp_remote_retrieve_body($response);
			error_log(
				'Findkit: Live update response code: ' .
					wp_remote_retrieve_response_code($response)
			);
			error_log('Findkit: Live update response body: ' . $body);
		}
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

		// Revision are not public
		if (\wp_is_post_revision($post)) {
			return;
		}

		$this->enqueue_post($post);
	}

	function enqueue_post(\WP_Post $post)
	{
		$can_live_update = apply_filters(
			'findkit_can_live_update',
			# By default do not equeue post in cli because integrations might
			# cause unwanted live updates
			php_sapi_name() !== 'cli',
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

	static function get_api_key(): ?string
	{
		if (defined('FINDKIT_API_KEY')) {
			return FINDKIT_API_KEY;
		}

		$findkit_api_key = get_option('findkit_api_key');
		return $findkit_api_key;
	}
}
