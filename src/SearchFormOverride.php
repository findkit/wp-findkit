<?php

declare(strict_types=1);

namespace Findkit;

class SearchFormOverride
{
	function bind()
	{
		\add_action(
			'wp_enqueue_scripts',
			[$this, '__action_enqueue_scripts'],
			10
		);
	}

	function __action_enqueue_scripts()
	{
		Utils::register_asset_script(
			'findkit-form-override',
			'form-override.ts',
			[
				'inline' => true,
				'globals' => [
					'FINDKIT_SEARCH_FORM_OVERRIDE' => [
						'publicToken' => \get_option('findkit_project_id'),
					],
				],
			]
		);

		if (get_option('findkit_override_search_form')) {
			wp_enqueue_script('findkit-form-override');
		}
	}
}
