<?php
declare(strict_types=1);

namespace Findkit;

class Utils
{
	/**
	 * Trashed, draft and private posts have different permalinks than the public
	 * one. This function gets the permalink as if the post were public.
	 */
	static function get_public_permalink(\WP_Post $post)
	{
		// trashed just has a __trashed suffix
		if (preg_match('/__trashed\/\z/', get_permalink($post))) {
			$url = get_permalink($post);
			return self::clean_trashed_post_name($url);
		}

		// create public clone
		$clone = clone $post;
		$clone->post_status = 'publish';
		// post_name might not be available yet
		$clone->post_name = sanitize_title(
			$clone->post_name ? $clone->post_name : $clone->post_title,
			$clone->ID
		);

		return self::clean_trashed_post_name(get_permalink($clone));
	}

	static function clean_trashed_post_name(string $url)
	{
		return preg_replace('/__trashed\/\z/', '/', $url);
	}

	static function generate_new_project_url()
	{
		$site_domain = parse_url(get_site_url())['host'];
		$site_name = get_bloginfo('name');
		$qs = http_build_query([
			'domain' => $site_domain,
			'name' => $site_name,
		]);
		return 'https://hub-next.findkit.com/new-project?' . $qs;
	}
}
