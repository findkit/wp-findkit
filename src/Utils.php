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
			return preg_replace('/__trashed\/\z/', '/', $url);
		}

		// create public clone
		$clone = clone $post;
		$clone->post_status = 'publish';
		// post_name might not be available yet
		$clone->post_name = sanitize_title(
			$clone->post_name ? $clone->post_name : $clone->post_title,
			$clone->ID
		);

		return get_permalink($clone);
	}
}
