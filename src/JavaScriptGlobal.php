<?php

declare(strict_types=1);

namespace Findkit;

/**
 * Expose FINDKIT_GET_JWT_TOKEN() which is used by the @findkit/ui library to
 * get the JWT tokens. It will automatically use the global when it is available
 */
class JavaScriptGlobal
{
	function bind()
	{
		add_action(
			'wp_head',
			[$this, '__action_wp_head'],
			// This should be very early so it is available before the
			// @findkit/ui library loads
			-10
		);

		add_action(
			'admin_head',
			[$this, '__action_admin_head'],
			// This should be very early so it is available before the
			// @findkit/ui library loads
			-10
		);
	}

	function enable_jwt()
	{
		if (!get_option('findkit_enable_jwt')) {
			return;
		}

		$token = KeyPair::request_jwt_token();

		if (!$token) {
			return;
		}

		Utils::render_js_module_script(
			'jwt.js',
			sprintf(
				'init(%s);',
				wp_json_encode([
					'endpoint' => \rest_url('findkit/v1/jwt'),
					'nonce' => \wp_create_nonce('wp_rest'),
					// Optimize the first jwt token request by inlining it
					'token' => $token,
				])
			)
		);
	}

	function override_default_search_form()
	{
		if (!get_option('findkit_override_search_form')) {
			return;
		}

		$public_token = get_option('findkit_project_id');

		if (!$public_token) {
			return;
		}

		Utils::render_js_module_script(
			'search-form-override.js',
			sprintf(
				'new FindkitSearchFormOverride(%s);',
				wp_json_encode([
					'publicToken' => $public_token,
					'version' => Utils::get_findkit_ui_version(),
				])
			)
		);
	}

	function __action_wp_head()
	{
		$this->enable_jwt();
		$this->override_default_search_form();
	}

	function __action_admin_head()
	{
		$this->enable_jwt();
	}
}
