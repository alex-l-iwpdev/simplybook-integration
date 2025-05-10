<?php
/**
 * Simply Book Api Abstract.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Iwpdev\SimplybookIntegration\Admin\Pages\OptionsPage;

/**
 * SimplyBookApiAbstract class file.
 */
abstract class SimplyBookApiAbstract {

	/**
	 * Client JsonRpcClient.
	 *
	 * @var string $client Client.
	 */
	public $client;

	/**
	 * SimplyBookApiAbstract construct.
	 */
	public function __construct() {
		$this->client = new Client(
			[
				'base_uri' => SimplyBookApi::API_ENDPOINT,
				'timeout'  => 10,
			]
		);
	}

	/**
	 * Send POST query.
	 *
	 * @param string $url     Url.
	 * @param array  $data    Date.
	 * @param array  $headers Headers.
	 *
	 * @return array
	 */
	public function send_post_query( string $url, array $data = [], array $headers = [] ) {
		if ( ! empty( $headers ) ) {
			$request_data['headers'] = $headers;
		}

		if ( ! empty( $data ) ) {
			$request_data['json'] = $data;
		}

		try {
			$response = $this->client->request(
				'POST',
				$url,
				$request_data
			);
			$body     = $response->getBody();
			if ( $response->getStatusCode() !== 200 ) {
				return [
					'success' => false,
					'code'    => $response->getStatusCode(),
					'message' => json_decode( $body->getContents(), true ),
				];
			}

			return [
				'success' => true,
				'code'    => $response->getStatusCode(),
				'body'    => json_decode( $body->getContents(), true ),
			];
		} catch ( ClientException | ServerException $e ) {
			return [
				'success' => false,
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * Send GET query.
	 *
	 * @param string $url     Url.
	 * @param array  $data    Date.
	 * @param array  $headers Headers.
	 *
	 * @return array
	 */
	public function send_get_query( string $url, array $data = [], array $headers = [] ) {
		if ( ! empty( $headers ) ) {
			$request_data['headers'] = $headers;
		}

		if ( ! empty( $data ) ) {
			$request_data['query'] = $data;
		}
		try {
			$response = $this->client->request(
				'GET',
				$url,
				$request_data
			);
			$body     = $response->getBody();
			if ( $response->getStatusCode() !== 200 ) {
				return [
					'success' => false,
					'code'    => $response->getStatusCode(),
					'message' => json_decode( $body->getContents(), true ),
				];
			}

			return [
				'success' => true,
				'code'    => $response->getStatusCode(),
				'body'    => json_decode( $body->getContents(), true ),
			];
		} catch ( ClientException | ServerException $e ) {
			return [
				'success' => false,
				'message' => $e->getResponse(),
			];
		}
	}

	/**
	 * Send delete request.
	 *
	 * @param string $url     Url.
	 * @param string $data    Data.
	 * @param array  $headers Headers.
	 *
	 * @return array
	 * @throws GuzzleException
	 */
	public function send_delete_request( string $url, string $data = '', array $headers = [] ) {
		if ( ! empty( $headers ) ) {
			$request_data['headers'] = $headers;
		}

		if ( ! empty( $data ) ) {
			$url .= $data;
		}

		try {
			$response = $this->client->request(
				'DELETE',
				$url,
				$request_data
			);
			$body     = $response->getBody();
			if ( $response->getStatusCode() !== 200 ) {
				return [
					'success' => false,
					'code'    => $response->getStatusCode(),
					'message' => json_decode( $body->getContents(), true ),
				];
			}

			return [
				'success' => true,
				'code'    => $response->getStatusCode(),
				'body'    => json_decode( $body->getContents(), true ),
			];
		} catch ( ClientException | ServerException $e ) {
			return [
				'success' => false,
				'message' => $e->getResponse(),
			];
		}
	}

	/**
	 * Get Aut headers.
	 *
	 * @return array
	 */
	protected function get_aut_headers(): array {
		$company_name = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'company_login' );
		$token        = get_transient( OptionsPage::FIELD_PREFIX . 'token' );

		if ( empty( $token ) ) {
			return [];
		}

		return [
			'X-Company-Login' => $company_name,
			'X-Token'         => $token,
		];
	}
}
