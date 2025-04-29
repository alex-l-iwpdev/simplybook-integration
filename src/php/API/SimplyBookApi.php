<?php
/**
 * SimplyBookApi class.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\API;

use Iwpdev\SimplybookIntegration\Admin\Notification\Notification;
use Iwpdev\SimplybookIntegration\Admin\Pages\OptionsPage;
use Iwpdev\SimplybookIntegration\DB\DBHelpers;

/**
 * SimplyBookApi class file.
 */
class SimplyBookApi extends SimplyBookApiAbstract {
	/**
	 * Api base endpoint.
	 */
	const  API_ENDPOINT = 'https://user-api-v2.simplybook.me';

	/**
	 * SimplyBookApi construct.
	 */
	public function __construct() {
		//phpcs:disable
		parent::__construct();
		//phpcs:enable
	}

	/**
	 * Get token.
	 *
	 * @return array
	 */
	public function get_token_data(): array {
		$company_name = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'company_login' );
		$login        = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'login' );
		$password     = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'password' );

		$response = $this->send_post_query(
			'/admin/auth',
			[
				'company'  => $company_name,
				'login'    => $login,
				'password' => $password,
			]
		);

		if ( ! $response['success'] ) {
			$message = $response['message'];
			add_action(
				'admin_notices',
				function () use ( $message ) {
					Notification::is_empty_api_login_notification( $message );
				}
			);
		}

		if ( isset( $response['body'] ) ) {
			return [
				'success' => true,
				'body'    => $response['body'],
			];
		}

		return [];
	}

	/**
	 * Get all services category.
	 *
	 * @return void
	 */
	public function get_all_service_category(): void {
		$all_service_category = [];
		$auth_header          = $this->get_aut_headers();

		if ( empty( $auth_header ) ) {
			$date_token = $this->get_refresh_token_data();
			if ( ! empty( $date_token['body'] ) ) {
				set_transient( OptionsPage::FIELD_PREFIX . 'token', $date_token['body']['token'], HOUR_IN_SECONDS / 2 );
				update_option( OptionsPage::FIELD_PREFIX . 'refresh_token', $date_token['body']['refresh_token'], true );
			}

			$auth_header = $this->get_aut_headers();
		}

		$response = $this->send_get_query(
			'/admin/categories',
			[],
			$auth_header
		);
		if ( $response['success'] && ! empty( $response['body']['data'] ) ) {
			$all_service_category = $response['body']['data'];
			foreach ( $all_service_category as $service_category ) {
				foreach ( $service_category['services'] as $service ) {
					DBHelpers::set_service_category( $service_category['id'], $service_category['name'], $service, $service_category['is_visible'] );
				}
			}
		}
	}

	/**
	 * Refresh token.
	 *
	 * @return array
	 */
	public function get_refresh_token_data(): array {
		$company_name  = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'company_login' );
		$refresh_token = get_option( OptionsPage::FIELD_PREFIX . 'refresh_token', true );

		if ( empty( $refresh_token ) ) {
			return [
				'success' => false,
				'message' => esc_html__( 'Refresh token is missing.', 'simplybook-integration' ),
			];
		}

		$response = $this->send_post_query(
			'/admin/auth/refresh-token',
			[
				'company'       => $company_name,
				'refresh_token' => $refresh_token,
			]
		);

		if ( ! $response['success'] ) {
			return [
				'success' => false,
				'message' => $response['message'],
			];
		}

		return [
			'success' => true,
			'body'    => $response['body'],
		];
	}

	/**
	 * Get all service.
	 *
	 * @return void
	 */
	public function get_all_services(): void {
		$auth_header  = $this->get_aut_headers();
		$all_services = [];

		if ( empty( $auth_header ) ) {
			$date_token = $this->get_refresh_token_data();
			if ( ! empty( $date_token['body'] ) ) {
				set_transient( OptionsPage::FIELD_PREFIX . 'token', $date_token['body']['token'], HOUR_IN_SECONDS / 2 );
				update_option( OptionsPage::FIELD_PREFIX . 'refresh_token', $date_token['body']['refresh_token'], true );
			}

			$auth_header = $this->get_aut_headers();
		}

		$response = $this->send_get_query(
			'/admin/services',
			[],
			$auth_header
		);

		if ( $response['success'] && ! empty( $response['body']['data'] ) ) {
			$all_services = $response['body']['data'];
			foreach ( $all_services as $service ) {
				foreach ( $service['providers'] as $provider ) {
					$service['provider'] = $provider;
					DBHelpers::set_service( $service );
				}
			}
		}
	}

	/**
	 * Get all providers.
	 *
	 * @return void
	 */
	public function get_all_providers(): void {
		$auth_header   = $this->get_aut_headers();
		$all_providers = [];

		if ( empty( $auth_header ) ) {
			$date_token = $this->get_refresh_token_data();
			if ( ! empty( $date_token['body'] ) ) {
				set_transient( OptionsPage::FIELD_PREFIX . 'token', $date_token['body']['token'], HOUR_IN_SECONDS / 2 );
				update_option( OptionsPage::FIELD_PREFIX . 'refresh_token', $date_token['body']['refresh_token'], true );
			}

			$auth_header = $this->get_aut_headers();
		}

		$response = $this->send_get_query(
			'/admin/providers',
			[],
			$auth_header
		);

		if ( $response['success'] && ! empty( $response['body']['data'] ) ) {
			$all_providers = $response['body']['data'];
			foreach ( $all_providers as $provider ) {
				DBHelpers::set_provider( $provider );
			}
		}
	}
}
