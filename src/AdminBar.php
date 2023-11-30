<?php

declare(strict_types=1);

namespace Findkit;

/**
 * Expose FINDKIT_GET_JWT_TOKEN() which is used by the @findkit/ui library to
 * get the JWT tokens. It will automatically use the global when it is available
 */
class AdminBar
{
	function bind()
	{
		\add_action('admin_bar_menu', [$this, '__admin_bar_menu'], 100);
		\add_action('admin_head', [$this, '__action_head'], 10);
		\add_action('wp_head', [$this, '__action_head'], 10);
		\add_action('admin_init', [$this, '__action_handle_edit_redirect'], 10);
		\add_action('init', [$this, '__action_handle_edit_redirect'], 10);
		\add_action(
			'admin_enqueue_scripts',
			[$this, '__action_admin_enqueue_scripts'],
			10
		);
	}

	function __action_admin_enqueue_scripts()
	{
		$handle = Utils::register_asset_script('admin.ts', [
			'FINDKIT_ADMIN_SEARCH' => [
				'publicToken' => \get_option('findkit_project_id'),
				'settingsURL' => \current_user_can('manage_options')
					? Utils::get_findkit_settings_url()
					: null,
			],
		]);

		if (\is_admin_bar_showing()) {
			wp_enqueue_script($handle);
		}
	}

	// prettier-ignore
	function __action_head()
	{
		?>
		<style>
			#wp-admin-bar-findkit-adminbar a::before {
				content: "\f179";
				top: 2px;
			}
		</style>
		<?php
	}

	function __admin_bar_menu($wp_admin_bar)
	{
		if (!get_option('findkit_adminbar')) {
			return;
		}

		if (!get_option('findkit_project_id')) {
			return;
		}

		$wp_admin_bar->add_node([
			'id' => 'findkit-adminbar',
			'title' => __('Findkit Search', 'findkit'),
			// Ensures middle click opens in new tab with the search
			'href' => add_query_arg(['findkit_wp_admin_q' => '']),
			'meta' => [
				'class' => 'findkit-adminbar-search',
			],
		]);
	}

	function __action_handle_edit_redirect()
	{
		if (!\current_user_can('edit_posts')) {
			return;
		}

		$url = filter_input(
			INPUT_GET,
			'findkit_edit_redirect',
			FILTER_SANITIZE_URL
		);

		if (empty($url)) {
			return;
		}

		// Handle site.example/?p=123 style urls
		$parsed = parse_url($url);
		if (!empty($parsed['query'])) {
			parse_str($parsed['query'], $qs);
			if (!empty($qs['p'])) {
				$this->redirect_to_post_id($qs['p']);
			}
		}

		$this->redirect_to_post_id(url_to_postid($url));
	}

	function redirect_to_post_id($post_id)
	{
		$url = filter_input(
			INPUT_GET,
			'findkit_edit_redirect',
			FILTER_SANITIZE_URL
		);

		if (!get_post($post_id)) {
			http_response_code(404);
			printf('No post found for %s', esc_url($url));

			echo '<p><a onclick="history.back()" href="#">Go back</a></p>';

			die();
		}

		wp_redirect(
			get_site_url() . "/wp-admin/post.php?post=$post_id&action=edit"
		);
		die();
	}
}
