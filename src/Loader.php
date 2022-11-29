<?php

declare(strict_types=1);

namespace Findkit;

class Loader
{
	static $instance = null;

	static function init()
	{
		if (self::$instance === null) {
			self::$instance = new self();
			self::$instance->bind();
		}

		return self::$instance;
	}

	function bind()
	{
		$pair = KeyPair::load_from_options();

		if (!$pair) {
			KeyPair::generate();
		}

		(new JavaScriptGlobal())->bind();
		(new RestApi())->bind();
	}
}
