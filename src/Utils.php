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
			$clone->post_name ? $clone->post_name : $clone->post_title
		);

		return self::clean_trashed_post_name(get_permalink($clone));
	}

	static function clean_trashed_post_name(string $url)
	{
		return preg_replace('/__trashed\/\z/', '/', $url);
	}

	static function get_domain()
	{
		return parse_url(get_site_url())['host'];
	}

	static function generate_new_project_url()
	{
		$site_name = get_bloginfo('name');
		$qs = http_build_query([
			'domain' => Utils::get_domain(),
			'name' => $site_name,
		]);
		return 'https://hub.findkit.com/new-project?' . $qs;
	}

	static function get_logo_url()
	{
		return plugins_url('logo.svg', __DIR__);
	}

	static function get_findkit_settings_url()
	{
		return \admin_url('admin.php?page=findkit_settings');
	}

	static function render_js_module_script(string $filename, ?string $extra_js)
	{
		// We'll use type=module to avoid creating accidental globals
		echo '<script type="module">';
		readfile(__DIR__ . '/' . $filename);
		if ($extra_js) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $extra_js;
		}
		echo '</script>';
	}

	static function get_findkit_ui_version()
	{
		$version = get_option('findkit_ui_version');

		if (!$version) {
			return '0.5.1';
		}

		return $version;
	}
}
