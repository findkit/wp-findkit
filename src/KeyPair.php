<?php
declare(strict_types=1);

namespace Findkit;

use Firebase\JWT\JWT;

if (!defined('ABSPATH')) {
	exit();
}

class KeyPair
{
	public string $public_key;
	public string $private_key;

	function __construct(string $public_key, string $private_key)
	{
		$this->public_key = $public_key;
		$this->private_key = $private_key;
	}

	function create_jwt_token(string $findkit_project_id): ?string
	{
		// seconds since unix epoch
		$now = time();

		return JWT::encode(
			[
				'iat' => $now, // issued at
				'exp' => $now + 60 * 2, // expires in 2 minutes
				'aud' => $findkit_project_id,
			],
			$this->private_key,
			'RS256'
		);
	}

	/**
	 * Request creation of a JWT. Checks user permissions etc.
	 */
	static function request_jwt_token(): ?string
	{
		$keypair = KeyPair::load_from_options();

		if (!$keypair) {
			return null;
		}

		$findkit_project_id = \get_option('findkit_project_id');

		if (!$findkit_project_id) {
			return null;
		}

		$allow = \apply_filters(
			'findkit_allow_jwt',
			\is_user_logged_in(),
			$findkit_project_id
		);

		if (!$allow) {
			return null;
		}

		return $keypair->create_jwt_token($findkit_project_id);
	}

	static function load_from_options(): ?self
	{
		$pubkey = \get_option('findkit_pubkey');
		$privkey = \get_option('findkit_privkey');

		if (!$pubkey || !$privkey) {
			return null;
		}

		return new self($pubkey, $privkey);
	}

	static function generate(): ?self
	{
		$raw_key = openssl_pkey_new([
			'private_key_bits' => 4096,
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
		]);

		if ($raw_key === false) {
			error_log('[findkit] Failed to generate RSA keypair');
			return null;
		}

		$private_key = null;

		openssl_pkey_export($raw_key, $private_key);
		$public_key = openssl_pkey_get_details($raw_key)['key'];

		\update_option('findkit_pubkey', $public_key);
		\update_option('findkit_privkey', $private_key);

		return new self($public_key, $private_key);
	}
}
