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
	}

	function __action_head()
	{
		$this->admin_search();
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

	function admin_search()
	{
		if (!\is_admin_bar_showing()) {
			return;
		}

		$public_token = get_option('findkit_project_id');

		if (!$public_token) {
			return;
		}

		$findkit_settings_url = current_user_can('manage_options')
			? Utils::get_findkit_settings_url()
			: null;

		Utils::render_js_module_script(
			'admin-search.js',
			sprintf(
				'new FindkitAdminSearch(%s);',
				wp_json_encode([
					'publicToken' => $public_token,
					'version' => Utils::get_findkit_ui_version(),
					'settingsURL' => $findkit_settings_url,
				])
			)
		);
	}
}
