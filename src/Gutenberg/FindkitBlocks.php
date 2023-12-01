<?php

declare(strict_types=1);

namespace Findkit\Gutenberg;

if (!defined('ABSPATH')) {
	exit();
}

class FindkitBlocks
{
	private $post_types;

	function bind()
	{
		\add_action('init', [$this, '__action_init']);

		\add_action('admin_enqueue_scripts', [
			$this,
			'__action_enqueue_block_editor_assets',
		]);

		\add_action('wp_enqueue_scripts', [
			$this,
			'__action_wp_enqueue_scripts',
		]);
	}

	function __action_wp_enqueue_scripts()
	{
		\wp_localize_script(
			'findkit-search-trigger-view-script',
			'FINDKIT_SEARCH_TRIGGER_VIEW',
			[
				'projectId' => \get_option('findkit_project_id'),
			]
		);

		$scripts = \wp_scripts();
		$scripts->add_data(
			'findkit-search-trigger-view-script',
			'strategy',
			'async'
		);
		// To footer
		// $scripts->add_data('findkit-search-trigger-view-script', 'group', 1);
		// See https://github.com/WordPress/WordPress/blob/2d7e5afa3e2516d3f457160f30a4244c1899b536/wp-includes/functions.wp-scripts.php#L191
	}

	function __action_enqueue_block_editor_assets()
	{
		\wp_localize_script(
			'findkit-sidebar-editor-script',
			'FINDKIT_GUTENBERG_SIDEBAR',
			[
				'postTypes' => $this->post_types,
			]
		);
	}

	function __action_init()
	{
		$this->post_types = \apply_filters('findkit_sidebar_post_types', [
			'post',
			'page',
		]);

		foreach ($this->post_types as $type) {
			\register_post_meta($type, '_findkit_superwords', [
				'show_in_rest' => true,
				'single' => true,
				'type' => 'string',
				'auth_callback' => function () {
					return \current_user_can('edit_posts');
				},
			]);

			\register_post_meta($type, '_findkit_show_in_search', [
				'show_in_rest' => true,
				'single' => true,
				'type' => 'string',
				'auth_callback' => function () {
					return \current_user_can('edit_posts');
				},
			]);
		}

		$success = register_block_type(
			__DIR__ . '/../../build/blocks/Gutenberg/blocks/sidebar'
		);

		if (!$success) {
			error_log('Failed to register Findkit Sidebar');
		}

		$success = register_block_type(
			__DIR__ . '/../../build/blocks/Gutenberg/blocks/search-trigger'
		);

		if (!$success) {
			error_log('Failed to register Findkit Search Trigger block type');
		}
	}
}
