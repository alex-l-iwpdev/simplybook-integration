<?php
/**
 * Plugin Name: Simplybook integration
 * Plugin URI: https://i-wp-dev.com/
 * Description: Integration of simplybook.me into your website.
 * Version: 1.0.2
 * Author: alexlavigin
 * Requires at least: 6.5
 * Tested up to: 6.7
 * Requires PHP: 8.0
 * Text Domain: simplybook-integration
 * Domain Path: /languages/
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Admin\Notification\Notification;
use Iwpdev\SimplybookIntegration\Main;

if ( ! defined( 'ABSPATH' ) ) {
	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

/**
 * Plugin version.
 */
const SBIP_VERSION = '1.0.2';

/**
 * Plugin dir path.
 */
const SBIP_PLUGIN_DIR_PATH = __DIR__;

/**
 * Plugin file path.
 */
const SBIP_PLUGIN_FILE = __FILE__;

/**
 * Plugin minimal required php version.
 */
const SBIP_PHP_REQUIRED_VERSION = '8.0';

/**
 * Class autoload.
 */
require_once SBIP_PLUGIN_DIR_PATH . '/vendor/autoload.php';

/**
 * Plugin url.
 */
define( 'SBIP_URL', untrailingslashit( plugin_dir_url( SBIP_PLUGIN_FILE ) ) );

/**
 * Access to the is_plugin_active function
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! function_exists( 'sbip_is_php_version' ) ) {

	/**
	 * Check php version.
	 *
	 * @return bool
	 */
	function sbip_is_php_version(): bool {
		if ( version_compare( PHP_VERSION, SBIP_PHP_REQUIRED_VERSION, '<' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! sbip_is_php_version() ) {

	add_action(
		'admin_notices',
		[
			Notification::class,
			'php_version_nope',
		]
	);

	if ( is_plugin_active( plugin_basename( BPM_FILE ) ) ) {
		deactivate_plugins( plugin_basename( BPM_FILE ) );
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	return;
}

load_plugin_textdomain( 'simplybook-integration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

new Main();
