<?php

declare(strict_types=1);

namespace Findkit\Settings;

use Findkit\Utils;

if (!defined('ABSPATH')) {
	exit();
}

class Page
{
	private $page = 'findkit_settings';

	function bind()
	{
		$this->add_section('findkit_settings', [
			'title' => function () {
				return __('WordPress Settings', 'findkit');
			},
		])
			->add_field([
				'name' => 'findkit_project_id',
				'type' => 'input',
				'default' => '',
				'title' => function () {
					return __('Findkit Public Token', 'findkit');
				},
				'description' => function () {
					Utils::echo_sanitized_html(
						__(
							'The Findkit public token for your project. You can find it from the <a href="https://hub.findkit.com" target="_blank">Findkit Hub</a>.',
							'findkit'
						)
					);
				},
			])
			->add_field([
				'name' => 'findkit_override_search_form',
				'type' => 'checkbox',
				'default' => '0',
				'title' => function () {
					return __(
						'Override the default frontend search form',
						'findkit'
					);
				},
				'description' => function () {
					Utils::echo_sanitized_html(
						__(
							'Look for the default search form with role=search and replace it with the Findkit search. ' .
								'Alternatively there is also a Gutenberg Block for adding a Findkit search button or form. ' .
								'It is also possible to add the search programmatically if you are a theme developer. ' .
								'See the <a target="_blank" href="https://docs.findkit.com/ui/">Findkit UI Documentation</a> for more information.',
							'findkit'
						)
					);
				},
			])
			->add_field([
				'name' => 'findkit_adminbar',
				'type' => 'checkbox',
				'default' => '1',
				'title' => function () {
					return __('Show admin bar search button', 'findkit');
				},
				'description' => function () {
					esc_html_e(
						'Show Findkit Search in the WP Admin top adminbar',
						'findkit'
					);
				},
			])
			->add_field([
				'name' => 'findkit_enable_live_update',
				'type' => 'checkbox',
				'default' => '0',
				'title' => function () {
					return __('Enable live update', 'findkit');
				},
				'description' => function () {
					esc_html_e(
						'Automatically update the search index when the content is updated. Requires a Findkit API key.',
						'findkit'
					);
				},
			])
			->add_field([
				'name' => 'findkit_api_key',
				'type' => 'password',
				'default' => '',
				'title' => function () {
					return __('Findkit API Key', 'findkit');
				},
				'description' => function () {
					esc_html_e('Used for the live update.', 'findkit');
					$this->render_apikeys_link_description();
				},
				'disabled' => defined('FINDKIT_API_KEY'),
				'placeholder' => defined('FINDKIT_API_KEY')
					? function () {
						esc_html_e('Defined in wp-config.php', 'findkit');
					}
					: '',
			])
			->add_field([
				'name' => 'findkit_enable_jwt',
				'type' => 'checkbox',
				'default' => '0',
				'title' => function () {
					return __('Authorize search requests', 'findkit');
				},
				'description' => function () {
					esc_html_e(
						'Generate JWT tokens for the search requests for signed-in users. Set `private = true` and `public_key` in the findkit.toml file for the search endpoint to require authorization.',
						'findkit'
					);
				},
			]);

		\add_action('admin_menu', [$this, '__action_admin_menu']);
	}

	function render_apikeys_link_description()
	{
		$project_id = \get_option('findkit_project_id');

		if (!$project_id) {
			esc_html_e(
				'You can create the API key in the Findkit Hub once you have created the Findkit Project.',
				'findkit'
			);
			return;
		}

		$domain = Utils::get_domain();
		$qs = http_build_query([
			'apikey' => "Live update key for $domain",
		]);

		$url = "https://hub.findkit.com/p/$project_id?$qs";

		echo ' ';
		Utils::echo_sanitized_html(
			sprintf(
				__(
					'You can create the API key in the <a target="_blank" href="%s">Findkit project settings in the Findkit Hub</a>.',
					'findkit'
				),
				esc_attr($url)
			)
		);
	}

	function add_section($section_name, $options): Section
	{
		return new Section($this->page, $section_name, $options);
	}

	function __action_admin_menu()
	{
		$args = apply_filters('findkit_options_page', [
			'title' => __('Findkit', 'findkit'),
			'capability' => 'manage_options',
		]);

		if (!$args) {
			return;
		}

		add_options_page(
			'findkit',
			$args['title'],
			$args['capability'],
			$this->page,
			[$this, '__options_page_findkit_settings']
		);
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
          <?php esc_html_e('Findkit Hub', 'findkit'); ?>
			</h2>

			<p>
          <?php esc_html_e( 'The Findkit Project is managed in the Findkit Hub. You can access the Findkit Hub by clicking the button below.', 'findkit'); ?>
			</p>
			<p>
				<a href="<?php echo esc_url($hub_url); ?>" target="_blank" class="button">
            <?php esc_html_e('Open Project in Findkit Hub', 'findkit'); ?>
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
			<button type="button" class="findkit-admin-search button">
          <?php esc_html_e('Open Findkit Search', 'findkit'); ?>
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
          <?php esc_html_e('Create Findkit Project', 'findkit'); ?>
			</h2>

			<p>
          <?php esc_html_e( ' To get started, you need to create a project in the Findkit Hub.', 'findkit'); ?>
			</p>
			<p>
				<a href="<?php echo esc_url($hub_url); ?>" target="_blank" class="button">
            <?php esc_html_e('Create Findkit Project', 'findkit'); ?>
				</a>
			</p>
        <?php
    }

	function render_jwt_toml()
	{
		echo "[search-endpoint]\n";
		echo "private = true\n";
		echo 'public_key = """' . "\n";
		echo esc_html(get_option('findkit_pubkey'));
		echo '"""' . "\n";
	}

	// prettier-ignore
	function __options_page_findkit_settings()
    {
        $logo_url = esc_attr(Utils::get_logo_url());

        ?>
			<div class="wrap">
				<img alt="<?php esc_html_e( 'Findkit Settings', 'findkit'); ?>" style='height: 50px; margin-top: 10px; margin-bottom: 20px;' src='<?php echo esc_url($logo_url); ?>' alt='Findkit' />

				<p>
            <?php
            Utils::echo_sanitized_html(__('Findkit is a site search toolkit that helps your users find the right content on your website. See the plugin documentation <a target="_blank" href="https://findk.it/wp">here</a> and general Findkit documentation on <a target="_blank" href="https://docs.findkit.com/">docs.findkit.com</a>.', 'findkit' ));
            ?>
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

					<h2>JWT Public key</h2>

					<p>
						Add the following to the findkit.toml to require JWT authenticated search requests.
						Note that changing search endpoint settings in the TOML may take up to 10 minutes to take effect.
					</p>

					<pre id="findkit-private-toml"
							 style="background-color: white;
							overflow: auto;
							border: 1px dashed black;
							padding: 10px"><?php $this->render_jwt_toml(); ?></pre>

					<button id="findkit-copy" class="button" type="button">Copy to clipboard</button>

					<script type="module">
						const button = document.getElementById('findkit-copy');
						button.addEventListener('click',  () => {
							const text =  document.getElementById('findkit-private-toml').innerText.trim();
							navigator.clipboard.writeText(text).then(() => {
								button.innerText = 'Copied!';
								setTimeout(() => {
									button.innerText = 'Copy to clipboard';
								}, 2000);
							});
						});
					</script>

				</form>
			</div>
        <?php
    }
}
