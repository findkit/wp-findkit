<?php
declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * @phpstan-type FindkitRegisterScriptOptions array{
 *	globals?: mixed,
 *	inline?: bool,
 *	in_footer?: bool
 * }
 */

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

	/**
	 * Register script build using wp-scripts.
	 *
	 * @param string $handle
	 * @param string $filename
	 * @param FindkitRegisterScriptOptions $options
	 */
	static function register_asset_script(
		string $handle,
		string $filename,
		$options = []
	) {
		$asset_path = plugin_dir_path(__DIR__) . "build/$filename.asset.php";
		$file_path = plugin_dir_path(__DIR__) . "build/$filename.js";

		$script_asset = require $asset_path;

		if (
			($options['inline'] ?? false) &&
			// not available in old versions of wp. We can just fallback to
			// normal registration if not
			function_exists('wp_add_inline_script')
		) {
			\wp_register_script($handle, false);
			\wp_add_inline_script($handle, file_get_contents($file_path));
		} else {
			\wp_register_script(
				$handle,
				plugin_dir_url(__DIR__) . "build/$filename.js",
				$script_asset['dependencies'],
				$script_asset['version'],
				$options['in_footer'] ?? true
			);
		}

		if ($options['globals'] ?? false) {
			foreach ($options['globals'] as $name => $value) {
				\wp_localize_script($handle, $name, $value);
			}
		}
	}

	static function register_asset_style(
		string $handle,
		string $filename,
		$options = []
	) {
		$asset_path = plugin_dir_path(__DIR__) . "build/$filename.asset.php";
		$file_path = plugin_dir_path(__DIR__) . "build/$filename.css";

		$style_asset = require $asset_path;
		if (
			($options['inline'] ?? false) &&
			// not available in old versions of wp. We can just fallback to
			// normal registration if not
			function_exists('wp_add_inline_style')
		) {
			\wp_register_style($handle, false);
			\wp_add_inline_style($handle, file_get_contents($file_path));
		} else {
			\wp_register_style(
				$handle,
				plugin_dir_url(__DIR__) . "build/$filename.css",
				[],
				$style_asset['version']
			);
		}
	}

	/**
	 * Echo html with links
	 */
	static function echo_sanitized_html(string $html)
	{
		echo wp_kses($html, [
			'a' => [
				'href' => [],
				'target' => [],
			],
		]);
	}

	static function magifying_class_url()
	{
		return plugin_dir_url(__DIR__) . 'magifying-class.svg';
	}

	static function echo_inner_blocks($content)
	{
		// There is no way to escape the inner blocks at this point. The inner
		// block must be escaped before.
		// https://github.com/WordPress/gutenberg/discussions/37823
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content;
	}
}
