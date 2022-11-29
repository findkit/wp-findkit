<?php
declare(strict_types=1);

namespace Findkit;

class RestApi
{
	function bind()
	{
		\add_action(
			'rest_api_init',
			function () {
				register_rest_route('findkit/v1', 'jwt', [
					'methods' => 'POST',
					'callback' => [$this, '__rest_token_response'],
					'permission_callback' => '__return_true',
				]);
			},
			10
		);
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
			'token' => $token,
		];
	}
}
