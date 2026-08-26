<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * Expose the plugin's public API functions as WordPress Abilities.
 *
 * The Abilities API ships in WordPress core since 6.9. The registration
 * hooks do not exist on older WordPress versions so the add_action calls
 * are inert there and the plain findkit_*() functions in plugin.php keep
 * working as before.
 */
class Abilities
{
	function bind()
	{
		\add_action('wp_abilities_api_categories_init', [
			$this,
			'__action_register_category',
		]);

		\add_action('wp_abilities_api_init', [
			$this,
			'__action_register_abilities',
		]);
	}

	function __action_register_category()
	{
		\wp_register_ability_category('findkit', [
			'label' => __('Findkit', 'findkit'),
			'description' => __(
				'Findkit site search: search the index and manage its contents.',
				'findkit'
			),
		]);
	}

	function __action_register_abilities()
	{
		$empty_input_schema = [
			'type' => 'object',
			'properties' => [],
			'additionalProperties' => false,
			'default' => [],
		];

		$urls_input_schema = function (?int $max_items = null) {
			$urls = [
				'type' => 'array',
				'items' => ['type' => 'string'],
				'minItems' => 1,
				'description' => __(
					'Full urls of pages on this site',
					'findkit'
				),
			];

			if ($max_items !== null) {
				$urls['maxItems'] = $max_items;
			}

			return [
				'type' => 'object',
				'properties' => ['urls' => $urls],
				'required' => ['urls'],
				'additionalProperties' => false,
			];
		};

		$success_output_schema = [
			'type' => 'object',
			'properties' => [
				'success' => ['type' => 'boolean'],
			],
		];

		\wp_register_ability('findkit/full-crawl', [
			'label' => __('Findkit Full Crawl', 'findkit'),
			'description' => __(
				'Start a full crawl which re-indexes the whole site to the Findkit search index.',
				'findkit'
			),
			'category' => 'findkit',
			'input_schema' => $empty_input_schema,
			'output_schema' => $success_output_schema,
			'execute_callback' => [self::class, 'execute_full_crawl'],
			'permission_callback' => [self::class, 'can_manage'],
			'meta' => ['show_in_rest' => true],
		]);

		\wp_register_ability('findkit/partial-crawl', [
			'label' => __('Findkit Partial Crawl', 'findkit'),
			'description' => __(
				'Start a partial crawl which indexes recently modified pages to the Findkit search index.',
				'findkit'
			),
			'category' => 'findkit',
			'input_schema' => $empty_input_schema,
			'output_schema' => $success_output_schema,
			'execute_callback' => [self::class, 'execute_partial_crawl'],
			'permission_callback' => [self::class, 'can_manage'],
			'meta' => ['show_in_rest' => true],
		]);

		\wp_register_ability('findkit/manual-crawl', [
			'label' => __('Findkit Manual Crawl', 'findkit'),
			'description' => __(
				'Crawl the given urls to the Findkit search index. Adds, updates or removes the index entries based on how the site responds.',
				'findkit'
			),
			'category' => 'findkit',
			'input_schema' => $urls_input_schema(),
			'output_schema' => $success_output_schema,
			'execute_callback' => [self::class, 'execute_manual_crawl'],
			'permission_callback' => [self::class, 'can_manage'],
			'meta' => ['show_in_rest' => true],
		]);

		\wp_register_ability('findkit/delete-pages', [
			'label' => __('Findkit Delete Pages', 'findkit'),
			'description' => __(
				'Delete the given urls from the Findkit search index without crawling them. A later crawl adds a url back if it still resolves on the site.',
				'findkit'
			),
			'category' => 'findkit',
			'input_schema' => $urls_input_schema(50),
			'output_schema' => $success_output_schema,
			'execute_callback' => [self::class, 'execute_delete_pages'],
			'permission_callback' => [self::class, 'can_manage'],
			'meta' => [
				'show_in_rest' => true,
				'annotations' => ['destructive' => true],
			],
		]);

		\wp_register_ability('findkit/search', [
			'label' => __('Findkit Search', 'findkit'),
			'description' => __(
				'Search site content from the Findkit search index.',
				'findkit'
			),
			'category' => 'findkit',
			'input_schema' => [
				'type' => 'object',
				'properties' => [
					'terms' => [
						'type' => 'string',
						'description' => __('Search terms', 'findkit'),
					],
					'search_params' => [
						'type' => 'object',
						'description' => __(
							'Optional Findkit search params. See https://docs.findkit.com/ui-api/ui.searchparams/',
							'findkit'
						),
					],
				],
				'required' => ['terms'],
				'additionalProperties' => false,
			],
			'output_schema' => [
				'type' => 'object',
				'properties' => [
					'success' => ['type' => 'boolean'],
					'results' => ['type' => 'object'],
				],
			],
			'execute_callback' => [self::class, 'execute_search'],
			'permission_callback' => [self::class, 'can_read'],
			'meta' => [
				'show_in_rest' => true,
				'annotations' => ['readonly' => true],
			],
		]);

		\wp_register_ability('findkit/get-page-meta', [
			'label' => __('Findkit Page Meta', 'findkit'),
			'description' => __(
				'Get the Findkit page meta of a post. This is the data the Findkit crawler indexes for the post.',
				'findkit'
			),
			'category' => 'findkit',
			'input_schema' => [
				'type' => 'object',
				'properties' => [
					'post_id' => [
						'type' => 'integer',
						'description' => __('Post ID', 'findkit'),
					],
				],
				'required' => ['post_id'],
				'additionalProperties' => false,
			],
			'output_schema' => ['type' => 'object'],
			'execute_callback' => [self::class, 'execute_get_page_meta'],
			'permission_callback' => [self::class, 'can_read'],
			'meta' => [
				'show_in_rest' => true,
				'annotations' => ['readonly' => true],
			],
		]);
	}

	static function can_manage(): bool
	{
		return \current_user_can('manage_options');
	}

	static function can_read(): bool
	{
		return \current_user_can('read');
	}

	/**
	 * @param mixed $result
	 * @return array|\WP_Error
	 */
	private static function to_success_output($result)
	{
		if (\is_wp_error($result)) {
			return $result;
		}

		return ['success' => (bool) $result];
	}

	static function execute_full_crawl()
	{
		return self::to_success_output(\findkit_full_crawl());
	}

	static function execute_partial_crawl()
	{
		return self::to_success_output(\findkit_partial_crawl());
	}

	/**
	 * @param mixed $input
	 */
	static function execute_manual_crawl($input)
	{
		$input = (array) $input;

		return self::to_success_output(\findkit_manual_crawl($input['urls']));
	}

	/**
	 * @param mixed $input
	 */
	static function execute_delete_pages($input)
	{
		$input = (array) $input;

		return self::to_success_output(\findkit_delete_pages($input['urls']));
	}

	/**
	 * @param mixed $input
	 */
	static function execute_search($input)
	{
		$input = (array) $input;

		$search_params = isset($input['search_params'])
			? (array) $input['search_params']
			: null;

		$result = \findkit_search($input['terms'], $search_params);

		if (\is_wp_error($result)) {
			return $result;
		}

		return [
			'success' => true,
			'results' => $result,
		];
	}

	/**
	 * @param mixed $input
	 */
	static function execute_get_page_meta($input)
	{
		$input = (array) $input;

		$post = \get_post((int) $input['post_id']);

		// Non-public posts are visible only to users who can read the
		// post itself. Use the same error as for missing posts to avoid
		// leaking which post ids exist.
		$can_view =
			$post &&
			(\is_post_publicly_viewable($post) ||
				\current_user_can('read_post', $post->ID));

		if (!$can_view) {
			return new \WP_Error(
				'findkit_post_not_found',
				__('Post not found', 'findkit'),
				['status' => 404]
			);
		}

		return \findkit_get_page_meta($post);
	}
}
