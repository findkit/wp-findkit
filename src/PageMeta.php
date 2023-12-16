<?php
declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class PageMeta
{
	function bind()
	{
		\add_action('wp_head', [$this, '__action_wp_head'], 10);
	}

	function __action_wp_head()
	{
		$post = \get_queried_object();

		if (!($post instanceof \WP_Post)) {
			return;
		}

		$meta = self::get($post);

		if (empty($meta)) {
			return;
		}

		echo "<script type='application/json' class='wordpress escaped' id='findkit'>";
		echo \esc_html(\wp_json_encode($meta));
		echo '</script>';
	}

	/**
	 * Get the blog name handling single and multisite installations
	 */
	static function get_blog_name()
	{
		if (\is_multisite()) {
			return \get_blog_details()->blogname;
		}

		return \get_bloginfo('name');
	}

	/**
	 * Generate "findkit page meta" for a post.
	 * See https://docs.findkit.com/crawler/meta-tag/
	 */
	static function get(\WP_Post $post)
	{
		$public = $post->post_status === 'publish';

		$url_parts = parse_url(\home_url());
		$domain = $url_parts['host'];

		$domain = \apply_filters('findkit_page_meta_domain', $domain, $post);

		$blogname_slug = \sanitize_title(self::get_blog_name());

		$blog_name_tag =
			'domain/' . $domain . '/' . 'wp_blog_name/' . $blogname_slug;

		$tags = [
			'wordpress',
			'domain/' . $domain . '/' . 'wordpress',
			'wp_post_type/' . $post->post_type,
			'domain/' . $domain . '/' . 'wp_post_type/' . $post->post_type,
			'wp_blog_name/' . $blogname_slug,
			$blog_name_tag,
			$public ? 'public' : 'private',
		];

		$public_taxonomies = \get_taxonomies(['public' => true], 'names');
		$post_taxonomies = \get_the_taxonomies($post->ID);

		foreach ($post_taxonomies as $taxonomy_key => $taxonomy_value) {
			// only expose public taxonomies as tags
			if (in_array($taxonomy_key, $public_taxonomies)) {
				$terms = \get_the_terms($post, $taxonomy_key);
				foreach ($terms as $term) {
					array_push(
						$tags,
						'domain/' .
							$domain .
							'/' .
							'wp_taxonomy/' .
							$taxonomy_key .
							'/' .
							$term->slug
					);
					array_push(
						$tags,
						'wp_taxonomy/' . $taxonomy_key . '/' . $term->slug
					);
				}
			}
		}

		$title = \wp_specialchars_decode(
			\is_archive() ? \get_the_archive_title() : $post->post_title
		);

		$created = \get_the_date('c', $post);
		$modified = \get_the_modified_date('c', $post);
		$show_in_search = \is_archive() ? false : $public;

		// Defaults to true. Only used to explicitly disable showing the page in the findkit search.
		if (
			\get_post_meta($post->ID, '_findkit_show_in_search', true) === 'no'
		) {
			$show_in_search = false;
		}

		$meta = [
			'showInSearch' => $show_in_search,
			'title' => \html_entity_decode($title),
			'created' => $created,
			'modified' => $modified,
			'tags' => $tags,
		];

		$superwords = \get_post_meta($post->ID, '_findkit_superwords', true);

		if ($superwords) {
			$superwords = trim($superwords);
			if (!empty($superwords)) {
				$meta['superwords'] = [];
				foreach (preg_split('/\s+/', $superwords) as $word) {
					$word = trim($word);
					if ($word) {
						$meta['superwords'][] = $word;
					}
				}
			}
		}

		// Use the post language if using polylang instead of the blog locale.
		if (function_exists('\pll_get_post_language')) {
			$meta['language'] = \pll_get_post_language($post->ID, 'slug');
		} else {
			$meta['language'] = substr(\get_bloginfo('language'), 0, 2);
		}

		return apply_filters('findkit_page_meta', $meta, $post);
	}
}
