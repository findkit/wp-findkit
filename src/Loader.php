<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

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

		(new PageMeta())->bind();
		(new AdminNotice())->bind();
		(new AdminSearch())->bind();
		(new FindkitMetaBox())->bind();
		(new JWT())->bind();
		(new SearchFormOverride())->bind();
		(new LiveUpdate($this->api_client))->bind();
		(new Settings\Page())->bind();
		(new RegisterBlocks())->bind();
		(new GutenbergSidebar())->bind();
	}
}
