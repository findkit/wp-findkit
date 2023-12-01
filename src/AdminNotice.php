<?php
declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

class AdminNotice
{
	function bind()
	{
		\add_action('admin_notices', [$this, '__action_admin_notices'], 10);
	}

	function __action_admin_notices()
	{
		$screen = \get_current_screen();
		$allowed_screens = ['plugins', 'dashboard'];

		if (!$screen || !in_array($screen->base, $allowed_screens)) {
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

		$this->render_notice();
	}

	// prettier-ignore
	function render_notice() {
		$findkit_settings_url = Utils::get_findkit_settings_url();

		?>
        <div class="notice">
            <p>
		        <img style='height: 20px; margin-right: 10px' src='<?php echo esc_attr( Utils::get_logo_url()); ?>' alt='Findkit' />
				<?php
					printf(
						wp_kses_post( __( 'Findkit Plugin activated. Configure the plugin on the <a href="%s">settings page</a>.', 'findkit') ),
						esc_url( $findkit_settings_url )
					);
				?>
            </p>
        </div>
        <?php

    }
}
