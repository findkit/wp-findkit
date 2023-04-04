<?php
declare(strict_types=1);

namespace Findkit;

class AdminNotice
{
	function bind()
	{
		\add_action('admin_notices', [$this, '__action_admin_notices'], 10);
	}

	function __action_admin_notices()
	{
		$screen = \get_current_screen();
		if ($screen && $screen->base === 'settings_page_findkit_settings') {
			return;
		}

		$show_notice = apply_filters(
			'findkit_show_admin_notice',
			current_user_can('manage_options')
		);

		if (!$show_notice) {
			return;
		}

		if (\get_option('findkit_project_id')) {
			return;
		}

		$findkit_settings_url = \admin_url(
			'options-general.php?page=findkit_settings'
		);
		?>
        <div class="notice notice-info">
            <p>
                <strong>
                    <?php printf(__('Findkit', 'findkit'), ''); ?>:
                </strong>
                <?php printf(
                	__(
                		'Plugin activated. Configure the plugin on the <a href="%s">settings page</a>.',
                		'findkit'
                	),
                	$findkit_settings_url
                ); ?>
            </p>
        </div>
        <?php
	}
}
