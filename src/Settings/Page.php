<?php

declare(strict_types=1);

namespace Findkit\Settings;

use Findkit\Utils;

class Page
{
	private $page = 'findkit_settings';
	private $sections = null;

	function __construct()
	{
		$this->sections = [];
	}

	function bind()
	{
		$this->add_section('findkit_settings', [
			'title' => __('WordPress Settings', 'findkit'),
		])
			->add_field([
				'name' => 'findkit_project_id',
				'type' => 'input',
				'default' => '',
				'title' => __('Findkit Public Token', 'findkit'),
				'description' => __(
					'The Findkit public token for your project. You can find it from the <a href="https://hub.findkit.com" target="_blank">Findkit Hub</a>.',
					'findkit'
				),
			])
			->add_field([
				'name' => 'findkit_enable_live_update',
				'type' => 'checkbox',
				'default' => '0',
				'title' => __('Enable live update', 'findkit'),
				'description' => __(
					'Automatically update the search index when content is updated. Requires a Findkit API key.',
					'findkit'
				),
			])
			->add_field([
				'name' => 'findkit_api_key',
				'type' => 'input',
				'default' => '',
				'title' => __('Findkit API Key', 'findkit'),
				'description' => __('Used for the live update', 'findkit'),
			]);

		\add_action('admin_menu', [$this, '__action_admin_menu']);
	}

	function add_section($section_name, $options): Section
	{
		$section = new Section($this->page, $section_name, $options);
		$this->sections[] = $section;
		return $section;
	}

	function __action_admin_menu()
	{
		add_options_page('Findkit', 'Findkit', 'manage_options', $this->page, [
			$this,
			'__options_page_findkit_settings',
		]);
	}

	function render_hub_link()
	{
		$project_id = \get_option('findkit_project_id');
		if (!$project_id) {
			return;
		}

		$hub_url = "https://hub.findkit.com/p/$project_id";
		?>
                <h2>
                    <?php _e('Findkit Hub', 'findkit'); ?>
                </h2>

                <p>
                    <?php _e(
                    	'The Findkit Project is managed in the Findkit Hub. You can access the Findkit Hub by clicking the button below.',
                    	'findkit'
                    ); ?>
                </p>
                <p>
                    <a href="<?php echo $hub_url; ?>" target="_blank" class="button button-primary">
						<?php _e('Open Project in Findkit Hub', 'findkit'); ?>
                    </a>
                </p>
        <?php
	}

	function render_create_findkit_project_button()
	{
		if (\get_option('findkit_project_id')) {
			return;
		}

		$hub_url = \Findkit\Utils::generate_new_project_url();
		?>
                <h2>
                    <?php _e('Create Findkit Project', 'findkit'); ?>
                </h2>

                <p>
                    <?php _e(
                    	' To get started, you need to create a project in the Findkit Hub.',
                    	'findkit'
                    ); ?>
                </p>
                <p>
                    <a href="<?php echo $hub_url; ?>" target="_blank" class="button button-primary">
						<?php _e('Create Findkit Project', 'findkit'); ?>
                    </a>
                </p>
        <?php
	}

	// prettier-ignore
	function __options_page_findkit_settings()
	{
		$logo_url = esc_attr(Utils::get_logo_url());

		?>
		<div class="wrap">
			<img alt="<?php _e( 'Findkit Settings', 'findkit'); ?>" style='height: 50px; margin-top: 10px; margin-bottom: 20px;' src='<?php echo $logo_url; ?>' alt='Findkit' />

            <p>
                <?php _e( 'Findkit is a site search toolkit that helps your users find the right content on your website.', 'findkit'); ?>
                <?php $this->render_create_findkit_project_button(); ?>
                <?php $this->render_hub_link(); ?>
            </p>

			<form method="post" action="options.php">
				<?php
					settings_fields($this->page);
					do_settings_sections($this->page);
					submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
