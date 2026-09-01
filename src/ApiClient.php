<?php

declare(strict_types=1);

namespace Findkit;

if (!defined('ABSPATH')) {
	exit();
}

/**
 * API client for the Findkit REST API
 */
class ApiClient
{
	private $apikey = null;
	private $endpoint = null;
	private $project_id = false;
	private $log_requests = false;

	function __construct($options = [])
	{
		$this->apikey = $options['apikey'] ?? self::get_api_key();

		$this->endpoint = $options['endpoint'] ?? null;

		if (!$this->endpoint) {
			$this->endpoint = get_option('findkit_api_endpoint');
		}

		if (!$this->endpoint) {
			$this->endpoint = 'https://api.findkit.com';
		}

		$this->project_id =
			$options['project_id'] ?? get_option('findkit_project_id');

		$this->log_requests = \get_option('findkit_log_api_requests', false);
	}

	/**
	 * Make a request to the Findkit API.
	 *
	 * @return bool true when the API responded with a 200 status
	 */
	function request(string $method, string $path, $options = []): bool
	{
		if (!$this->apikey) {
			error_log(
				"Findkit: Api key not set. Cannot make request $method $path"
			);
			return false;
		}

		if (!$this->project_id) {
			error_log(
				"Findkit: findkit_project_id not set. Cannot make request $method $path"
			);
			return false;
		}

		$args = [
			'headers' => [
				'authorization' => 'Bearer ' . $this->apikey,
				'user-agent' => 'Findkit WordPress Plugin v0.0.0',
			],
			'method' => $method,
			'timeout' => 20,
		];

		if ($method === 'POST') {
			$args['headers']['content-type'] = 'application/json';
			$args['body'] = wp_json_encode($options['data']);
		}

		$invoke_url = $this->endpoint . $path;

		if ($this->log_requests) {
			error_log(
				"Findkit: Api request $method $invoke_url " .
					print_r($args, true)
			);
		}

		$response = wp_remote_request($invoke_url, $args);

		if (\is_wp_error($response)) {
			error_log(
				'Findkit: Api invoke error: ' . $response->get_error_message()
			);
			return false;
		}

		$code = \wp_remote_retrieve_response_code($response);

		if ($code !== 200) {
			error_log(
				"Findkit: Api invoke error. Code $code, Body: " .
					\wp_remote_retrieve_body($response)
			);
			return false;
		}

		return true;
	}

	private function get_crawls_path()
	{
		$project_id = $this->project_id;
		return "/v1/projects/$project_id/crawls";
	}

	/**
	 * Start manual crawls for the given urls. The API accepts at most 10
	 * urls per crawl so the urls are sent in chunks of 10.
	 *
	 * @return bool true when all requests succeeded
	 */
	function manual_crawl(array $urls, array $options = []): bool
	{
		$ok = true;

		foreach (array_chunk($urls, 10) as $chunk) {
			$res = $this->request('POST', $this->get_crawls_path(), [
				'data' => [
					'mode' => 'manual',
					'urls' => $chunk,
					'message' =>
						$options['message'] ??
						'Manual crawl started using Findkit WordPress plugin',
				],
			]);

			if (!$res) {
				$ok = false;
			}
		}

		return $ok;
	}

	/**
	 * Delete the given urls from the search index without crawling. The
	 * urls are sent in chunks of 50.
	 *
	 * @return bool true when all requests succeeded
	 */
	function delete_pages(array $urls, array $options = []): bool
	{
		$project_id = $this->project_id;
		$ok = true;

		foreach (array_chunk($urls, 50) as $chunk) {
			$res = $this->request(
				'POST',
				"/v1/projects/$project_id/pages/delete",
				[
					'data' => [
						'urls' => $chunk,
					],
				]
			);

			if (!$res) {
				$ok = false;
			}
		}

		return $ok;
	}

	function partial_crawl(array $options = [])
	{
		return $this->request('POST', $this->get_crawls_path(), [
			'data' => [
				'mode' => 'partial',
				'message' =>
					$options['message'] ??
					'Partial crawl started using Findkit WordPress plugin',
			],
		]);
	}

	function full_crawl(array $options = [])
	{
		return $this->request('POST', $this->get_crawls_path(), [
			'data' => [
				'mode' => 'full',
				'message' =>
					$options['message'] ??
					'Full crawl started using Findkit WordPress plugin',
			],
		]);
	}

	/**
	 * Make a request to the Findkit API and return the decoded JSON body.
	 *
	 * Unlike request() this returns a WP_Error instead of false so the
	 * caller can tell the user what went wrong.
	 *
	 * @param array $query Query string parameters
	 * @return array|\WP_Error
	 */
	function request_json(string $method, string $path, array $query = [])
	{
		if (!$this->apikey) {
			return new \WP_Error(
				'findkit_api_key_missing',
				__(
					'Findkit API key is not set. Add it in the Findkit settings.',
					'findkit'
				)
			);
		}

		if (!$this->project_id) {
			return new \WP_Error(
				'findkit_project_id_missing',
				__(
					'Findkit public token is not set. Add it in the Findkit settings.',
					'findkit'
				)
			);
		}

		$invoke_url = $this->endpoint . $path;

		if ($query) {
			$invoke_url = add_query_arg(
				array_map('rawurlencode', $query),
				$invoke_url
			);
		}

		$args = [
			'headers' => [
				'authorization' => 'Bearer ' . $this->apikey,
				'user-agent' => 'Findkit WordPress Plugin v0.0.0',
			],
			'method' => $method,
			'timeout' => 20,
		];

		if ($this->log_requests) {
			error_log("Findkit: Api request $method $invoke_url");
		}

		$response = wp_remote_request($invoke_url, $args);

		if (\is_wp_error($response)) {
			return $response;
		}

		$code = \wp_remote_retrieve_response_code($response);
		$body = \wp_remote_retrieve_body($response);

		if ($code !== 200) {
			return new \WP_Error(
				'findkit_api_request_failed',
				sprintf(
					/* translators: %d: http status code */
					__('Findkit API responded with status %d', 'findkit'),
					$code
				),
				['status' => $code, 'body' => $body]
			);
		}

		$data = json_decode($body, true);

		if (!is_array($data)) {
			return new \WP_Error(
				'findkit_api_invalid_response',
				__('Findkit API returned an invalid response', 'findkit')
			);
		}

		return $data;
	}

	/**
	 * Read the link report of the project. Lists the links pointing at
	 * pages that are missing, forbidden, failing or redirected.
	 *
	 * @param array $options target: limit the report to a single target
	 *                       host, limit: how many links to return
	 * @return array|\WP_Error
	 */
	function link_report(array $options = [])
	{
		$project_id = $this->project_id;
		$query = [];

		if (!empty($options['target'])) {
			$query['target'] = (string) $options['target'];
		}

		if (isset($options['limit'])) {
			$query['limit'] = (string) (int) $options['limit'];
		}

		return $this->request_json(
			'GET',
			"/v1/projects/$project_id/link-report",
			$query
		);
	}

	static function get_api_key(): ?string
	{
		if (defined('FINDKIT_API_KEY')) {
			return FINDKIT_API_KEY;
		}

		$findkit_api_key = get_option('findkit_api_key');
		return $findkit_api_key ? $findkit_api_key : null;
	}
}
