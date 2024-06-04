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
		$object = \get_queried_object();

		$meta = self::get($object);

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
	static function get($object)
	{
		$public = true;

		if ($object instanceof \WP_Post) {
			$public = $object->post_status === 'publish';
		}

		$meta = [
			// Do not show archive pages in search by default because commonly
			// they contain only a lists of posts which is already indexed as
			// the individual posts.
			'showInSearch' => \is_archive() ? false : $public,
		];

		$domain = \apply_filters(
			'findkit_page_meta_domain',
			parse_url(\home_url())['host'],
			$object
		);

		$blogname_slug = \sanitize_title(self::get_blog_name());

		$blog_name_tag =
			'domain/' . $domain . '/' . 'wp_blog_name/' . $blogname_slug;

		$tags = [
			'wordpress',
			'domain/' . $domain . '/' . 'wordpress',
			'wp_blog_name/' . $blogname_slug,
			$blog_name_tag,
			$public ? 'public' : 'private',
		];

		if ($object instanceof \WP_Post) {
			$meta['title'] = apply_filters(
				'the_title',
				$object->post_title,
				$object->ID
			);
			$meta['created'] = \get_the_date('c', $object);
			$meta['modified'] = \get_the_modified_date('c', $object);
			$meta['customFields'] = [
				'wpPostId' => ['type' => 'number', 'value' => $object->ID],
			];

			// Defaults to true. Only used to explicitly disable showing the page in the findkit search.
			if (
				\get_post_meta($object->ID, '_findkit_show_in_search', true) ===
				'no'
			) {
				$meta['showInSearch'] = false;
			}

			$tags[] = 'wp_post_type/' . $object->post_type;
			$tags[] =
				'domain/' .
				$domain .
				'/' .
				'wp_post_type/' .
				$object->post_type;

			$public_taxonomies = \get_taxonomies(['public' => true], 'names');
			$post_taxonomies = \get_the_taxonomies($object->ID);

			foreach ($post_taxonomies as $taxonomy_key => $taxonomy_value) {
				// only expose public taxonomies as tags
				if (in_array($taxonomy_key, $public_taxonomies)) {
					$terms = \get_the_terms($object, $taxonomy_key);
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

			$superwords = \get_post_meta(
				$object->ID,
				'_findkit_superwords',
				true
			);

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

			$content_no_highlight = \get_post_meta(
				$object->ID,
				'_findkit_content_no_highlight',
				true
			);

			if ($content_no_highlight) {
				$content_no_highlight = trim($content_no_highlight);
				if (!empty($content_no_highlight)) {
					$meta['contentNoHighlight'] = $content_no_highlight;
				}
			}

			// Use the post language if using polylang instead of the blog locale.
			if (function_exists('\pll_get_post_language')) {
				$meta['language'] = \pll_get_post_language($object->ID, 'slug');
			}
		} elseif (\is_archive()) {
			$meta['title'] = \get_the_archive_title();
		}

		$meta['title'] ??= \get_the_title();
		$meta['language'] ??= substr(\get_bloginfo('language'), 0, 2);

		$meta['title'] = \wp_specialchars_decode($meta['title']);
		$meta['tags'] = $tags;

		return apply_filters('findkit_page_meta', $meta, $object);
	}
}
