<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class FindkitMetaBox
{
	function bind()
	{
		\add_action('add_meta_boxes', [$this, '__action_add_meta_boxes'], 100);
		\add_action('save_post', [$this, '__action_save_post'], 100);
	}

	function __action_add_meta_boxes()
	{
		$post_types = \apply_filters('findkit_sidebar_post_types', [
			'post',
			'page',
		]);

		foreach ($post_types as $post_type) {
			// Gutenberg plugin is used in Gutenberg contexts
			if (\use_block_editor_for_post_type($post_type)) {
				continue;
			}

			add_meta_box(
				'findkit',
				'Findkit',
				[$this, '__render_meta_box'],
				$post_type,
				'side'
			);
		}
	}

	// prettier-ignore
	function __render_meta_box($post)
	{
        $superwords = get_post_meta( $post->ID, '_findkit_superwords', true );	
        ?>


        <div class="inside">
            <input type="hidden" name="_wpnonce_findkit" value="<?php echo \esc_attr( \wp_create_nonce('findkit_superwords') ); ?>" />
            <style>
            textarea[name=findkit_superwords] {
                display: block;
                margin: 12px 0 0;
                height: 4em;
                width: 100%;	
            }
            </style>
            <label for="findkit_superwords">Superwords</label>
            <textarea name="findkit_superwords" id="findkit_superwords"><?php
                echo \esc_textarea($superwords);
            ?></textarea>
            <p>
                A space-separated list of words which will promote this page to
                the top of the search results when these words are searched.
            </p>
        </div>

        <?php
	}

	function __action_save_post($post_id)
	{
		if (
			!\wp_verify_nonce(
				sanitize_text_field(
					wp_unslash($_POST['_wpnonce_findkit'] ?? null)
				),
				'findkit_superwords'
			)
		) {
			return;
		}

		if (!array_key_exists('findkit_superwords', $_POST)) {
			return;
		}

		\update_post_meta(
			$post_id,
			'_findkit_superwords',
			sanitize_text_field(wp_unslash($_POST['findkit_superwords'] ?? ''))
		);
	}
}
