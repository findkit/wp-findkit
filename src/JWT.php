<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * Expose FINDKIT_GET_JWT_TOKEN() which is used by the @findkit/ui library to
 * get the JWT tokens. It will automatically use the global when it is available
 */
class JWT
{
	function bind()
	{
		\add_action(
			'admin_enqueue_scripts',
			[$this, '__action_enqueue_scripts'],
			-10
		);

		\add_action(
			'wp_enqueue_scripts',
			[$this, '__action_enqueue_scripts'],
			-10
		);

		\add_action(
			'rest_api_init',
			function () {
				register_rest_route('findkit/v1', 'jwt', [
					'methods' => 'POST',
					'callback' => [$this, '__rest_token_response'],
					// The permissions check is in KeyPair::request_jwt_token()
					'permission_callback' => '__return_true',
				]);
			},
			10
		);
	}

	function __action_enqueue_scripts()
	{
		if (!get_option('findkit_enable_jwt')) {
			return;
		}

		Utils::register_asset_script('findkit-jwt', 'jwt.ts', [
			'inline' => true,
			'globals' => [
				'FINDKIT_JWT' => [
					'endpoint' => \rest_url('findkit/v1/jwt'),
					'nonce' => \wp_create_nonce('wp_rest'),
					// Optimize the first jwt token request by inlining it
					'initialToken' => KeyPair::request_jwt_token(),
				],
			],
		]);

		wp_enqueue_script('findkit-jwt');
	}

	function __rest_token_response(\WP_REST_Request $request)
	{
		$token = KeyPair::request_jwt_token();

		if (!$token) {
			return new \WP_Error('findkit_no_token', 'No token', [
				'status' => 403,
			]);
		}

		return [
			'jwt' => $token,
		];
	}
}
