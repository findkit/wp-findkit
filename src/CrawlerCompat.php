<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class CrawlerCompat
{
	const CRAWLER_USER_AGENT = 'findkit';

	function bind()
	{
		\add_filter(
			'do_redirect_guess_404_permalink',
			[$this, '__filter_disable_404_guess'],
			10,
			1
		);

		\add_filter(
			'old_slug_redirect_post_id',
			[$this, '__filter_disable_old_slug_redirect'],
			10,
			1
		);
	}

	private function is_findkit_crawler(): bool
	{
		$user_agent = isset($_SERVER['HTTP_USER_AGENT'])
			? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']))
			: '';
		$is_crawler = stripos($user_agent, self::CRAWLER_USER_AGENT) !== false;

		return $is_crawler;
	}

	function __filter_disable_404_guess($do_redirect)
	{
		if ($this->is_findkit_crawler()) {
			return false;
		}

		return $do_redirect;
	}

	function __filter_disable_old_slug_redirect($post_id)
	{
		if ($this->is_findkit_crawler()) {
			return 0;
		}

		return $post_id;
	}
}
