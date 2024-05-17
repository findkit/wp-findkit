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

	function request(string $method, string $path, $options = [])
	{
		if (!$this->apikey) {
			error_log(
				"Findkit: Api key not set. Cannot make request $method $path"
			);
			return;
		}

		if (!$this->project_id) {
			error_log(
				"Findkit: findkit_project_id not set. Cannot make request $method $path"
			);
			return;
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
			return;
		}

		$code = \wp_remote_retrieve_response_code($response);

		if ($code !== 200) {
			error_log(
				"Findkit: Api invoke error. Code $code, Body: " .
					\wp_remote_retrieve_body($response)
			);
			return;
		}
	}

	private function get_crawls_path()
	{
		$project_id = $this->project_id;
		return "/v1/projects/$project_id/crawls";
	}

	function manual_crawl(array $urls, array $options = [])
	{
		$this->request('POST', $this->get_crawls_path(), [
			'data' => [
				'mode' => 'manual',
				'urls' => $urls,
				'message' =>
					$options['message'] ??
					'Manual crawl started using Findkit WordPress plugin',
			],
		]);
	}

	function partial_crawl(array $options = [])
	{
		$this->request('POST', $this->get_crawls_path(), [
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
		$this->request('POST', $this->get_crawls_path(), [
			'data' => [
				'mode' => 'full',
				'message' =>
					$options['message'] ??
					'Full crawl started using Findkit WordPress plugin',
			],
		]);
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
