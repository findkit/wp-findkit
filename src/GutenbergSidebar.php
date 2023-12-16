<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class GutenbergSidebar
{
	private $post_types;

	function bind()
	{
		\add_action('init', [$this, '__action_init']);

		\add_action('admin_enqueue_scripts', [
			$this,
			'__action_enqueue_block_editor_assets',
		]);
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

		Utils::register_asset_script('findkit-sidebar', 'sidebar.tsx', [
			'globals' => [
				'FINDKIT_GUTENBERG_SIDEBAR' => [
					'postTypes' => $this->post_types,
				],
			],
		]);

		\wp_enqueue_script('findkit-sidebar');
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

			\register_post_meta($type, '_findkit_content_no_highlight', [
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
	}
}
