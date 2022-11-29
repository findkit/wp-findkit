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
	}

	function __action_wp_head()
	{
		if (!get_option('findkit_enable_jwt')) {
			return;
		}

		$token = KeyPair::request_jwt_token();

		if (!$token) {
			return;
		}

		// We'll use type=module to avoid creating accidental globals
		echo '<script type="module">';
		readfile(__DIR__ . '/jwt.js');
		printf(
			'init(%s);',
			wp_json_encode([
				'endpoint' => \rest_url('findkit/v1/jwt'),
				'nonce' => \wp_create_nonce('wp_rest'),
				// Optimize the first jwt token request by inlining it
				'token' => $token,
			])
		);
		echo '</script>';
	}
}
