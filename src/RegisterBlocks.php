<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class RegisterBlocks
{
	function bind()
	{
		\add_action('init', [$this, '__action_init']);

		\add_action('enqueue_block_assets', [
			$this,
			'__action_enqueue_block_editor_assets',
		]);
	}

	function __action_enqueue_block_editor_assets()
	{
		if (\is_admin()) {
			Utils::register_asset_script(
				'findkit-search-blocks-editor',
				'search-blocks-editor.tsx',
				[
					'globals' => [
						'FINDKIT_SEARCH_BLOCK' => [
							'publicToken' => \get_option('findkit_project_id'),
						],
					],
				]
			);

			Utils::register_asset_style(
				'findkit-search-blocks-editor',
				'search-blocks-editor.tsx'
			);
		}
	}

	function __action_init()
	{
		Utils::register_asset_script(
			'findkit-search-blocks-view',
			'search-blocks-view.tsx',
			[
				'globals' => [
					'FINDKIT_SEARCH_BLOCK' => [
						'publicToken' => \get_option('findkit_project_id'),
					],
				],
			]
		);

		Utils::register_asset_style(
			'findkit-search-blocks-view',
			'search-blocks-view.tsx',
			[
				'inline' => true,
			]
		);

		foreach (glob(__DIR__ . '/../build/blocks/*/block.json') as $block) {
			$success = register_block_type($block);

			if (!$success) {
				error_log('Failed to register Findkit block: ' . $block);
			}
		}
	}
}
