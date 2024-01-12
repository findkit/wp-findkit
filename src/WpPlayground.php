<?php
declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * Automatically configure the plugin wp playground for easy testing
 */
class WpPlayground
{
	function bind()
	{
		if ($_SERVER['SERVER_NAME'] !== 'playground.wordpress.net') {
			return;
		}

		add_filter(
			'default_option_findkit_project_id',
			[$this, '__filter_default_findkit_project_id'],
			20,
			0
		);
	}

	function __filter_default_findkit_project_id()
	{
		// The same search as in findkit.com and docs.findkit.com
		return 'p68GxRvaA';
	}
}
