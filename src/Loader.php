<?php

declare(strict_types=1);

namespace Findkit;

class Loader
{
	static $instance = null;

	/**
	 * @var ApiClient
	 */
	public $api_client = null;

	static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	function __construct()
	{
		$this->api_client = new ApiClient();
	}

	function init()
	{
		$pair = KeyPair::load_from_options();

		if (!$pair) {
			KeyPair::generate();
		}

		(new JavaScriptGlobal())->bind();
		(new RestApi())->bind();
		(new PageMeta())->bind();
		(new AdminNotice())->bind();
		(new LiveUpdate($this->api_client))->bind();
		(new Settings\Page())->bind();
	}
}
