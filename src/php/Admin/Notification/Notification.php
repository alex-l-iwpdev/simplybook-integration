<?php
/**
 * Admin notification.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Admin\Notification;

/**
 * Notification class file.
 */
class Notification {

	/**
	 * Incorrect PHP Version
	 *
	 * @return void
	 */
	public static function php_version_nope(): void {
		printf(
			'<div id="bpm-php-nope" class="notice notice-error is-dismissible"><p>%s</p></div>',
			wp_kses(
				sprintf(
				/* translators: 1: Required PHP version number, 2: Current PHP version number, 3: URL of PHP update help page */
					__( 'The Simplybook integration plugin requires PHP version %1$s or higher. This site is running PHP version %2$s. <a href="%3$s">Learn about updating PHP</a>.', 'simplybook-integration' ),
					BPM_PHP_REQUIRED_VERSION,
					PHP_VERSION,
					'https://wordpress.org/support/update-php/'
				),
				[
					'a' => [
						'href' => [],
					],
				]
			)
		);
	}

	/**
	 * Is empty api key notification.
	 *
	 * @return void
	 */
	public static function is_empty_api_key_notification(): void {
		printf(
			'<div id="bpm-php-nope" class="notice notice-error is-dismissible"><p>%s</p></div>',
			wp_kses(
				sprintf(
				//phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
					__( 'You are not filled with the API Key for your integration. To get the key, follow the <a href="%s">Link</a>', 'simplybook-integration' ),
					'https://your_login.secure.simplybook.it/v2/management/#plugins/api/'
				),
				[
					'a' => [
						'href' => [],
					],
				]
			)
		);
	}

	/**
	 * Is empty api login notification.
	 *
	 * @return void
	 */
	public static function is_empty_api_login_notification(): void {
		printf(
			'<div id="bpm-php-nope" class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html( __( 'Fill in access for connection to SimplyBook', 'simplybook-integration' ) )
		);
	}

	/**
	 * Api error notification.
	 *
	 * @return void
	 */
	public static function api_error_notification(): void {
		printf(
			'<div id="bpm-php-nope" class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html( __( 'The API returned the error when trying to get token.', 'simplybook-integration' ) )
		);
	}

	/**
	 * Api error notification.
	 *
	 * @param string $message Message.
	 *
	 * @return void
	 */
	public static function api_error_notice( string $message ): void {
		printf(
			'<div id="bpm-php-nope" class="notice notice-error is-dismissible"><p>%s</p></div>',
			esc_html( $message )
		);
	}
}
