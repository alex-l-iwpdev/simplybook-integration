<?php
/**
 * Main class Simplybook Integration.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration;

use Iwpdev\SimplybookIntegration\Admin\Notification\Notification;
use Iwpdev\SimplybookIntegration\Admin\Pages\OptionsPage;
use Iwpdev\SimplybookIntegration\API\SimplyBookApi;
use Iwpdev\SimplybookIntegration\DB\CreateTables;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookBanner;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookStaff;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Main class file.
 */
class Main {
	/**
	 * Allow tags for description.
	 */
	const ALLOW_TAGS_FOR_DESCRIPTION = [
		'a'      => [
			'href'  => [],
			'title' => [],
		],
		'strong' => [],
		'em'     => [],
		'p'      => [],
		'img'    => [
			'src'   => [],
			'title' => [],
			'alt'   => [],
		],
		'b'      => [],
		'li'     => [],
		'ul'     => [],
	];

	/**
	 * Main construct.
	 */
	public function __construct() {
		$this->init();

		new OptionsPage();
	}

	/**
	 * Init actions and filters.
	 *
	 * @return void
	 */
	private function init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'add_scripts_and_styles' ] );
		add_action( 'init', [ $this, 'init_api' ] );
		add_action( 'init', [ $this, 'create_default_pages' ] );
		add_action( 'admin_head', [ $this, 'register_coron' ] );
		add_action( OptionsPage::FIELD_PREFIX . 'refresh_token', [ $this, 'refresh_token_crone' ] );

		add_filter( 'cron_schedules', [ $this, 'cron_add_half_hour' ] );

		new SimplybookBanner();
		new SimplybookStaff();
		new CreateTables();
	}

	/**
	 * Get template part Simplybook integration.
	 *
	 * @param $template_name
	 * @param $args
	 *
	 * @return void
	 */
	public static function sbip_get_template_part( $template_name, $args = null ): void {
		$theme_dir      = get_stylesheet_directory() . '/simplybook-templates';
		$plugin_dir     = SBIP_PLUGIN_DIR_PATH . '/template-part';
		$found_template = '';

		if ( is_dir( $theme_dir ) ) {
			$rii = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $theme_dir ) );
			foreach ( $rii as $file ) {
				if ( ! $file->isDir() && basename( $file ) === $template_name . '.php' ) {
					$found_template = $file->getPathname();
					break;
				}
			}
		}

		if ( ! $found_template ) {
			$found_template = $plugin_dir . '/' . $template_name . '.php';
		}

		if ( file_exists( $found_template ) ) {
			$atts = $args;
			include $found_template;
		}
	}

	/**
	 * Api init.
	 *
	 * @return void
	 */
	public function init_api(): void {
		$login         = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'login' );
		$password      = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'password' );
		$company_login = carbon_get_theme_option( OptionsPage::FIELD_PREFIX . 'company_login' );

		if ( empty( $login ) || empty( $password ) || empty( $company_login ) ) {
			add_action(
				'admin_notices',
				[
					Notification::class,
					'is_empty_api_key_notification',
				]
			);

			return;
		}

		$token = get_transient( OptionsPage::FIELD_PREFIX . 'token' );

		if ( empty( $token ) ) {
			$simply_book_api = new SimplybookAPI();
			$date_token      = $simply_book_api->get_token_data();

			if ( ! empty( $date_token['body'] ) ) {
				set_transient( OptionsPage::FIELD_PREFIX . 'token', $date_token['body']['token'], HOUR_IN_SECONDS / 2 );
				update_option( OptionsPage::FIELD_PREFIX . 'refresh_token', $date_token['body']['refresh_token'], true );
			}
		}
	}

	/**
	 * Add actions and filters.
	 *
	 * @return void
	 */
	public function add_scripts_and_styles(): void {
		$url = SBIP_URL;
		$min = '.min';

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min = '';
		}

		wp_enqueue_script( 'sbip_main', $url . '/assets/js/main' . $min . '.js', [ 'jquery' ], SBIP_PHP_REQUIRED_VERSION, true );

		wp_enqueue_style( 'sbip_main', $url . '/assets/css/main' . $min . '.css', '', SBIP_PHP_REQUIRED_VERSION );
	}

	/**
	 * Create default pages.
	 *
	 * @return void
	 */
	public function create_default_pages(): void {
		$page_personal = get_page_by_path( 'personal', OBJECT, 'page' );
		if ( ! $page_personal ) {
			$content = '<!-- wp:shortcode -->
						[sbip_simplybook_banner image="' . SBIP_URL . '/assets/img/banner-doctors.png" title="Наша команда" button_text="записатись на прийом" button_url="#"]
						<!-- /wp:shortcode -->
						
						<!-- wp:shortcode -->
						[sbip_simplybook_staff title="Обери категорію лікування, <br> по якій тебе цікавить спеціаліст"]
						<!-- /wp:shortcode -->
						
						<!-- wp:shortcode -->
						[sbip_simplybook_job_banner title="вакансії" sub_title="СТАНЬ ЧАСТИНОЮ <br> НАШОЇ <i>команди</i>" button_text="подивитись вакансії" button_url="#" banner_image="' . SBIP_URL . '/assets/img/banner-blur.png"]
						<!-- /wp:shortcode -->
						';

			$post_data = [
				'post_title'   => sanitize_text_field( __( 'Персонал', 'simplybook-integration' ) ),
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
			];

			$post_id = wp_insert_post( $post_data );
		}
	}

	/**
	 * Register crone.
	 *
	 * @return void
	 */
	public function register_coron() {
		if ( ! wp_next_scheduled( OptionsPage::FIELD_PREFIX . 'refresh_token' ) ) {
			wp_schedule_event( time(), 'hourly', OptionsPage::FIELD_PREFIX . 'refresh_token' );
		}
	}

	/**
	 * Add cone scheduled half hour.
	 *
	 * @param array $schedules Schedules.
	 *
	 * @return array
	 */
	public function cron_add_half_hour( array $schedules ): array {
		$schedules['half_hour'] = [
			'interval' => 60 * 2,
			'display'  => __( 'Half hour', 'simplybook-integration' ),
		];

		return $schedules;
	}

	/**
	 * Refresh token crone.
	 *
	 * @return void
	 */
	public function refresh_token_crone(): void {
		$simply_book_api = new SimplybookAPI();
		$date_token      = $simply_book_api->get_refresh_token_data();

		if ( ! empty( $date_token['body'] ) ) {
			set_transient( OptionsPage::FIELD_PREFIX . 'token', $date_token['body']['token'], HOUR_IN_SECONDS / 2 );
			update_option( OptionsPage::FIELD_PREFIX . 'refresh_token', $date_token['body']['refresh_token'], true );
		}
	}
}
