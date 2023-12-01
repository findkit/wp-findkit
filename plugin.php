<?php

if (!defined('ABSPATH')) {
	exit();
}

/**
 * Plugin Name: Findkit
 * Plugin URI: https://github.com/findkit/wp-findkit
 * Description: Findkit.com helpers
 * Author: Esa-Matti Suuronen <findkit@findkit.com>
 * Version: 0.1.13
 */

// To make this plugin work properly for both Composer users and non-composer
// users we must detect whether the project is using a global autoloader. We
// can do that by checking whether our autoloadable classes will autoload with
// class_exists(). If not it means there's no global autoloader in place and
// the user is not using composer. In that case we can try to require the
// bundled autoloader code.
if (
	!\class_exists('\Findkit\Loader') &&
	\is_readable(__DIR__ . '/vendor/autoload.php')
) {
	require_once __DIR__ . '/vendor/autoload.php';
}
// This way we can add the vendor/ directory to git and have the plugin "just
// work" when it is cloned to wp-content/plugins. But be careful when checking
// the vendor/ into git so you won't add all development dependencies too. Eg.
// before checking it in you should always run "composer install --no-dev" first.

// If the class still doesn't exist it means the autoloader files are not bundled in the plugin
if (!\class_exists('\Findkit\Loader')) {
	add_action('admin_notices', function () {
		$class = 'notice notice-error';
		$message = __(
			'Failed to fully load the Findkit-plugin. Autoload classes not found. <a target="_blank" href="https://findk.it/wpautoload">Read more</a>.',
			'findkit'
		);

		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	});
	return;
}

\Findkit\Loader::instance();

/////////////////////////
// Public API functions
/////////////////////////

function findkit_full_crawl(array $options = [])
{
	$loader = \Findkit\Loader::instance();
	$loader->api_client->full_crawl($options);
}

function findkit_manual_crawl(array $urls, array $options = [])
{
	$loader = \Findkit\Loader::instance();
	$loader->api_client->manual_crawl($urls, $options);
}

function findkit_partial_crawl(array $options = [])
{
	$loader = \Findkit\Loader::instance();
	$loader->api_client->partial_crawl($options);
}

function findkit_get_page_meta(\WP_post $post)
{
	return Findkit\PageMeta::get($post);
}