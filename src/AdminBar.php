<?php

declare(strict_types=1);

namespace Findkit;

/**
 * Expose FINDKIT_GET_JWT_TOKEN() which is used by the @findkit/ui library to
 * get the JWT tokens. It will automatically use the global when it is available
 */
class AdminBar
{
	function bind()
	{
		add_action('admin_bar_menu', [$this, '__admin_bar_menu'], 100);
	}

	function __admin_bar_menu($wp_admin_bar)
	{
		if (!get_option('findkit_adminbar')) {
			return;
		}

		$wp_admin_bar->add_node([
			'id' => 'findkit-adminbar',

			'title' => __('Findkit Search', 'findkit'),
			'href' => '#',
			'meta' => [
				'class' => 'findkit-adminbar-search',
			],
		]);
	}
}
