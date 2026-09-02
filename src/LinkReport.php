<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * Read the Findkit link report and flatten it into a shape that is
 * convenient to loop over in PHP.
 *
 * The API groups the links by the section they belong to and then by the
 * page they were found on. Here they become a single list where each row
 * carries the reason, so a caller can render the report without walking
 * three levels of nesting.
 */
class LinkReport
{
	const CACHE_PREFIX = 'findkit_link_report_';

	/**
	 * The API sections mapped to the reason of each flattened link.
	 */
	const SECTIONS = [
		'notFound' => 'not_found',
		'forbidden' => 'forbidden',
		'serverError' => 'server_error',
		'redirect' => 'redirect',
		'unknown' => 'unknown',
	];

	/**
	 * Read the link report of the project.
	 *
	 * The report only changes when the project is crawled, so it is
	 * cached for a while. Pass refresh to skip the cache.
	 *
	 * @param array $options target: limit to a single target host,
	 *                       limit: how many links to return,
	 *                       refresh: bypass the cache
	 * @return array|\WP_Error summary, links, targets, truncated and
	 *                         last_full_crawl: when the crawl behind the
	 *                         report finished, or null when unknown
	 */
	static function get(array $options = [])
	{
		$loader = Loader::instance();

		$target = isset($options['target']) ? (string) $options['target'] : '';
		$limit = isset($options['limit']) ? (int) $options['limit'] : 0;
		$refresh = !empty($options['refresh']);

		$cache_key = self::get_cache_key($target, $limit);

		if (!$refresh) {
			$cached = get_transient($cache_key);

			if (is_array($cached)) {
				return $cached;
			}
		}

		$raw = $loader->api_client->link_report($options);

		if (is_wp_error($raw)) {
			return $raw;
		}

		$report = self::format($raw);

		set_transient($cache_key, $report, 15 * MINUTE_IN_SECONDS);

		return $report;
	}

	private static function get_cache_key(string $target, int $limit): string
	{
		$project_id = (string) get_option('findkit_project_id');

		return self::CACHE_PREFIX . md5("$project_id|$target|$limit");
	}

	/**
	 * Flatten the API response.
	 *
	 * Every field is treated as optional. The report is remote data and a
	 * missing key must not fatal the site that renders it.
	 */
	static function format(array $raw): array
	{
		$summary = [];
		$links = [];

		foreach (self::SECTIONS as $section_name => $reason) {
			$section = isset($raw[$section_name])
				? (array) $raw[$section_name]
				: [];

			$summary[$reason] = isset($section['count'])
				? (int) $section['count']
				: 0;

			$pages = isset($section['pages']) ? (array) $section['pages'] : [];

			foreach ($pages as $page) {
				$page = (array) $page;
				$page_url = isset($page['url']) ? (string) $page['url'] : '';
				$page_links = isset($page['links'])
					? (array) $page['links']
					: [];

				foreach ($page_links as $link) {
					$link = (array) $link;

					$links[] = [
						'page' => $page_url,
						'link' => isset($link['url'])
							? (string) $link['url']
							: '',
						'http_status' => isset($link['httpStatus'])
							? (int) $link['httpStatus']
							: 0,
						'reason' => $reason,
						'message' => isset($link['message'])
							? (string) $link['message']
							: '',
						'redirects_to' => isset($link['redirectsTo'])
							? (string) $link['redirectsTo']
							: null,
					];
				}
			}
		}

		return [
			'summary' => $summary,
			'links' => $links,
			'targets' => self::format_targets($raw),
			'truncated' => !empty($raw['truncated']),
			// Null on older Findkit deployments which do not send it, and on
			// projects with no completed full crawl. Both mean "unknown".
			'last_full_crawl' => isset($raw['lastFullCrawl'])
				? (string) $raw['lastFullCrawl']
				: null,
		];
	}

	/**
	 * The targets tell which link tracking mode the crawler used. Without
	 * track_links = "all" only pdf links are tracked, which is the usual
	 * reason for an empty report.
	 */
	private static function format_targets(array $raw): array
	{
		$targets = isset($raw['targets']) ? (array) $raw['targets'] : [];
		$formatted = [];

		foreach ($targets as $target) {
			$target = (array) $target;

			$formatted[] = [
				'host' => isset($target['host'])
					? (string) $target['host']
					: '',
				'link_tracking' => isset($target['linkTracking'])
					? (string) $target['linkTracking']
					: '',
				'walk_links' => isset($target['walkLinks'])
					? (bool) $target['walkLinks']
					: null,
			];
		}

		return $formatted;
	}
}
