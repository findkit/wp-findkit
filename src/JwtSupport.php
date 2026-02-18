<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class JwtSupport
{
	static function is_php_supported(): bool
	{
		return PHP_VERSION_ID >= 80000;
	}

	static function is_library_available(): bool
	{
		return class_exists(\Firebase\JWT\JWT::class);
	}

	static function is_available(): bool
	{
		return self::is_php_supported() && self::is_library_available();
	}
}
