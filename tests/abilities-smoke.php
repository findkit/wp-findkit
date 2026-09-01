<?php

/**
 * Smoke test for the plugin's WordPress Abilities.
 *
 * Run inside wp-env:
 *
 *   npm run test-abilities
 *
 * or directly:
 *
 *   npx @wordpress/env run cli wp eval-file wp-content/plugins/wp-findkit/tests/abilities-smoke.php
 *
 * Asserts only environment independent behavior: registration, input
 * validation and permission handling. Does not assert Findkit API
 * responses so this works without a configured Findkit project.
 */

if (!defined('WP_CLI') || !WP_CLI) {
	echo "This script must be run with wp eval-file\n";
	exit(1);
}

$failures = [];

$check = function (string $label, bool $ok) use (&$failures) {
	if ($ok) {
		WP_CLI::log("ok - $label");
	} else {
		WP_CLI::warning("FAIL - $label");
		$failures[] = $label;
	}
};

if (!function_exists('wp_get_ability')) {
	WP_CLI::error(
		'Abilities API not available. WordPress 6.9 or later is required, got ' .
			get_bloginfo('version')
	);
}

$expected = [
	'findkit/full-crawl',
	'findkit/partial-crawl',
	'findkit/manual-crawl',
	'findkit/delete-pages',
	'findkit/search',
	'findkit/get-page-meta',
	'findkit/link-report',
];

$registered = array_keys(wp_get_abilities());

foreach ($expected as $name) {
	$check("$name is registered", in_array($name, $registered, true));
}

$category = wp_get_ability_category('findkit');
$check('findkit category is registered', (bool) $category);

$search = wp_get_ability('findkit/search');
$page_meta = wp_get_ability('findkit/get-page-meta');
$delete = wp_get_ability('findkit/delete-pages');

if (!$search || !$page_meta || !$delete) {
	WP_CLI::error('Abilities missing, cannot continue');
}

// Input validation happens before the execute callback
wp_set_current_user(1);

$res = $search->execute(['nope' => 1]);
$check(
	'search rejects unknown input props',
	is_wp_error($res) && $res->get_error_code() === 'ability_invalid_input'
);

$res = $delete->execute(['urls' => []]);
$check(
	'delete-pages rejects empty urls',
	is_wp_error($res) && $res->get_error_code() === 'ability_invalid_input'
);

$res = $page_meta->execute(['post_id' => 999999999]);
$check(
	'get-page-meta returns not found for missing post',
	is_wp_error($res) && $res->get_error_code() === 'findkit_post_not_found'
);

// Search delegates to findkit_search(). Without a configured project it
// returns a WP_Error, with one it returns a result array. Both prove the
// wiring.
$res = $search->execute(['terms' => 'test']);
$check(
	'search executes',
	is_wp_error($res) || (is_array($res) && ($res['success'] ?? false) === true)
);

// Permissions
wp_set_current_user(0);

$check(
	'anonymous cannot use search',
	$search->check_permissions(['terms' => 'x']) === false
);

$res = $delete->execute(['urls' => ['https://example.invalid/a']]);
$check(
	'anonymous delete-pages execution is denied',
	is_wp_error($res) &&
		$res->get_error_code() === 'ability_invalid_permissions'
);

// Non-public posts must not be visible to low privilege users
$draft_id = wp_insert_post([
	'post_title' => 'Findkit abilities smoke test draft',
	'post_status' => 'draft',
	'post_content' => 'draft content',
]);

$public_id = wp_insert_post([
	'post_title' => 'Findkit abilities smoke test public',
	'post_status' => 'publish',
	'post_content' => 'public content',
]);

$subscriber_id = wp_insert_user([
	'user_login' => 'findkit_smoke_' . wp_generate_password(8, false),
	'user_pass' => wp_generate_password(),
	'role' => 'subscriber',
]);

wp_set_current_user($subscriber_id);

$res = $page_meta->execute(['post_id' => $draft_id]);
$check(
	'subscriber cannot read draft page meta',
	is_wp_error($res) && $res->get_error_code() === 'findkit_post_not_found'
);

$res = $page_meta->execute(['post_id' => $public_id]);
$check(
	'subscriber can read public page meta',
	is_array($res) &&
		($res['title'] ?? '') === 'Findkit abilities smoke test public'
);

$check(
	'subscriber cannot use delete-pages',
	$delete->check_permissions(['urls' => ['https://example.invalid/a']]) ===
		false
);

wp_set_current_user(1);

$res = $page_meta->execute(['post_id' => $draft_id]);
$check(
	'admin can read draft page meta',
	is_array($res) &&
		($res['title'] ?? '') === 'Findkit abilities smoke test draft'
);

$check(
	'admin can use delete-pages',
	$delete->check_permissions(['urls' => ['https://example.invalid/a']]) ===
		true
);

// Link report. Delegates to findkit_get_link_report(). Without a configured
// project it returns a WP_Error, with one it returns the report array. Both
// prove the wiring.
$link_report = wp_get_ability('findkit/link-report');

$check(
	'admin can use link-report',
	$link_report->check_permissions([]) === true
);

$res = $link_report->execute([]);
$check(
	'link-report executes',
	is_wp_error($res) || (is_array($res) && isset($res['links']))
);

$res = $link_report->execute(['limit' => 99999]);
$check(
	'link-report rejects an out of range limit',
	is_wp_error($res) && $res->get_error_code() === 'ability_invalid_input'
);

$res = $link_report->execute(['bogus' => true]);
$check(
	'link-report rejects unknown properties',
	is_wp_error($res) && $res->get_error_code() === 'ability_invalid_input'
);

wp_set_current_user($subscriber_id);

$check(
	'subscriber cannot use link-report',
	$link_report->check_permissions([]) === false
);

wp_set_current_user(1);

// Cleanup
wp_delete_post($draft_id, true);
wp_delete_post($public_id, true);
wp_delete_user($subscriber_id);

if ($failures) {
	WP_CLI::error(count($failures) . ' assertion(s) failed');
}

WP_CLI::success('All abilities smoke tests passed');
