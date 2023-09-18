<?php

/**
 * Plugin Name: Findkit.com
 * Plugin URI: https://github.com/findkit/wp-findkit
 * Description: Findkit.com helpers
 * Author: Esa-Matti Suuronen <findkit@findkit.com>
 * Version: 0.1.4
 */

// To make this plugin work properly for both Composer users and non-composer
// users we must detect whether the project is using a global autoloader. We
// can do that by checking whether our autoloadable classes will autoload with
// class_exists(). If not it means there's no global autoloader in place and
// the user is not using composer. In that case we can safely require the
// bundled autoloader code.
if (!\class_exists('\Findkit\Loader')) {
	require_once __DIR__ . '/vendor/autoload.php';
}
// This way we can add the vendor/ directory to git and have the plugin "just
// work" when it is cloned to wp-content/plugins. But be careful when checking
// the vendor/ into git so you won't add all development dependencies too. Eg.
// before checking it in you should always run "composer install --no-dev" first.

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