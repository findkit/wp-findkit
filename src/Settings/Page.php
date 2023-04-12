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
				'name' => 'findkit_override_search_form',
				'type' => 'checkbox',
				'default' => '0',
				'title' => __(
					'Override the default frontend search form',
					'findkit'
				),
				'description' => __(
					'Looks for the default search form with role=search and replaces it with the Findkit search. Findkit Public Token must be defined. For more personalized search experience we recommend manually integrating the search interface to your theme. Please refer to the Findkit <a target="_blank" href="https://docs.findkit.com/ui/">documentation</a> for more information.',
					'findkit'
				),
			])
			->add_field([
				'name' => 'findkit_adminbar',
				'type' => 'checkbox',
				'default' => '1',
				'title' => __('Show admin bar search button', 'findkit'),
				'description' => __(
					'Show Findkit Search in the WP Admin top adminbar',
					'findkit'
				),
			])
			->add_field([
				'name' => 'findkit_enable_live_update',
				'type' => 'checkbox',
				'default' => '0',
				'title' => __('Enable live update', 'findkit'),
				'description' => __(
					'Automatically update the search index when the content is updated. Requires a Findkit API key.',
					'findkit'
				),
			])
			->add_field([
				'name' => 'findkit_api_key',
				'type' => 'input',
				'default' => '',
				'title' => __('Findkit API Key', 'findkit'),
				'description' =>
					__('Used for the live update.', 'findkit') .
					$this->get_apikeys_link_description(),
				'disabled' => defined('FINDKIT_API_KEY'),
				'placeholder' => defined('FINDKIT_API_KEY')
					? __('Defined in wp-config.php', 'findkit')
					: '',
			]);

		\add_action('admin_menu', [$this, '__action_admin_menu']);
	}

	function get_apikeys_link_description()
	{
		$project_id = \get_option('findkit_project_id');

		if (!$project_id) {
			return ' ' .
				sprintf(
					__(
						'You can create the API key in the Findkit Hub once you have created the Findkit Project.',
						'findkit'
					),
					$url
				);
		}

		$domain = Utils::get_domain();
		$qs = http_build_query([
			'apikey' => "Live update key for $domain",
		]);

		$url = "https://hub.findkit.com/p/$project_id?$qs";

		return ' ' .
			sprintf(
				__(
					'You can create the API key in the <a target="_blank" href="%s">Findkit project settings in the Findkit Hub</a>.',
					'findkit'
				),
				$url
			);
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

	// prettier-ignore
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
				<?php _e( 'The Findkit Project is managed in the Findkit Hub. You can access the Findkit Hub by clicking the button below.', 'findkit'); ?>
			</p>
			<p>
				<a href="<?php echo $hub_url; ?>" target="_blank" class="button button-primary">
					<?php _e('Open Project in Findkit Hub', 'findkit'); ?>
				</a>
			</p>
        <?php
	}

	// prettier-ignore
	function render_search_button()
	{
		$project_id = \get_option('findkit_project_id');
		if (!$project_id) {
			return;
		}

		?>
			<button type="button" class="findkit-admin-search button button-primary">
				<?php _e('Open Findkit Search', 'findkit'); ?>
			</button>
        <?php
	}

	// prettier-ignore
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
                    <?php _e( ' To get started, you need to create a project in the Findkit Hub.', 'findkit'); ?>
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
                <?php _e( 'See the plugin documentation <a target="_blank" href="https://findk.it/wp">here</a> and general Findkit documentation on <a target="_blank" href="https://docs.findkit.com/">docs.findkit.com</a>.', 'findkit'); ?>
            </p>

			<?php $this->render_search_button(); ?>
			<?php $this->render_create_findkit_project_button(); ?>
			<?php $this->render_hub_link(); ?>

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
