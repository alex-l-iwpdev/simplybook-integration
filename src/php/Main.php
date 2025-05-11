<?php
/**
 * Main class Simplybook Integration.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration;

use DOMDocument;
use DOMXPath;
use Iwpdev\SimplybookIntegration\Admin\CustomPostTypes\CreateServicePostType;
use Iwpdev\SimplybookIntegration\Admin\Notification\Notification;
use Iwpdev\SimplybookIntegration\Admin\Pages\OptionsPage;
use Iwpdev\SimplybookIntegration\Ajax\AppointmentAjax;
use Iwpdev\SimplybookIntegration\API\SimplyBookApi;
use Iwpdev\SimplybookIntegration\DB\CreateTables;
use Iwpdev\SimplybookIntegration\Post\AppointmentPost;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookAppointment;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookBanner;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookBoolingConfirm;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookJobBanner;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookServices;
use Iwpdev\SimplybookIntegration\ShortCodes\SimplybookStaff;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Main class file.
 */
class Main {

	/**
	 * Simply Book Base image url.
	 */
	const SBIP_BASE_IMAGE_URL = 'https://coma.clinic/';

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
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );
		//phpcs:disable
		add_filter( 'cron_schedules', [ $this, 'cron_add_half_hour' ] );
		//phpcs:enable
		add_filter( 'provider_filters', [ $this, 'handler_provider_filters' ], 10, 1 );
		add_filter( 'service_filters', [ $this, 'handler_service_filters' ], 10, 1 );
		add_filter( 'specialization_filters', [ $this, 'handler_specialization_filters' ], 10, 1 );
		add_filter( 'service_sub_description', [ $this, 'handler_service_sub_description_filter' ], 10, 1 );

		new SimplybookBanner();
		new SimplybookStaff();
		new CreateTables();
		new SimplybookJobBanner();
		new SimplybookAppointment();
		new SimplybookServices();
		new SimplybookBoolingConfirm();
		new CreateServicePostType();
		new AppointmentAjax();
		new AppointmentPost();
	}

	/**
	 * Get template part Simplybook integration.
	 *
	 * @param string     $template_name Template name.
	 * @param array|null $args          Arguments.
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

		wp_enqueue_script( 'sbip_datepicer', $url . '/assets/js/jquery-ui-datepicker' . $min . '.js', [ 'jquery' ], '1.14.1 ', true );
		wp_enqueue_script( 'sbip_main', $url . '/assets/js/main' . $min . '.js', [ 'jquery' ], SBIP_PHP_REQUIRED_VERSION, true );

		wp_enqueue_style( 'sbip_main', $url . '/assets/css/main' . $min . '.css', '', SBIP_PHP_REQUIRED_VERSION );

		wp_localize_script(
			'sbip_main',
			'sbipObject',
			[
				'ajaxUrl'             => admin_url( 'admin-ajax.php' ),
				'appointmentNonce'    => wp_create_nonce( AppointmentAjax::APPOINTMENTS_ACTIONS_NAME ),
				'appointmentAction'   => AppointmentAjax::APPOINTMENTS_ACTIONS_NAME,
				'slotAction'          => AppointmentAjax::SLOTS_ACTIONS_NAME,
				'slotNonce'           => wp_create_nonce( AppointmentAjax::SLOTS_ACTIONS_NAME ),
				'deleteBookingAction' => AppointmentAjax::DELETE_BOOKING_ACTION,
				'deleteBookingNonce'  => wp_create_nonce( AppointmentAjax::DELETE_BOOKING_ACTION ),
				'mainPageUrl'         => get_bloginfo( 'url' ),
			]
		);
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
				'post_name'    => 'personal',
			];

			$post_id = wp_insert_post( $post_data );
		}

		$page_appointment = get_page_by_path( 'appointment', OBJECT, 'page' );
		if ( ! $page_appointment ) {
			$content   = '<!-- wp:shortcode -->[sbip_simplybook_appointment]<!-- /wp:shortcode -->';
			$post_data = [
				'post_title'   => sanitize_text_field( __( 'Запис на прийом', 'simplybook-integration' ) ),
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
				'post_name'    => 'appointment',
			];
			$post_id   = wp_insert_post( $post_data );
		}

		$page_services = get_page_by_path( 'services', OBJECT, 'page' );
		if ( ! $page_services ) {
			$content   = '<!-- wp:shortcode -->[sbip_simplybook_services title="Послуги" sub_title="<strong>Краса починається з турботи.</strong> Ми знаємо, як зробити вас щасливішими <br> у своєму відображенні."]<!-- /wp:shortcode -->';
			$post_data = [
				'post_title'   => sanitize_text_field( __( 'Послуги', 'simplybook-integration' ) ),
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
				'post_name'    => 'services',
			];
			$post_id   = wp_insert_post( $post_data );
		}

		$page_confirm_booking = get_page_by_path( 'booking-confirm', OBJECT, 'page' );
		if ( ! $page_confirm_booking ) {
			$content   = '<!-- wp:shortcode -->[sbip_simplybook_booking_confirm]<!-- /wp:shortcode -->';
			$post_data = [
				'post_title'   => sanitize_text_field( __( 'Бронювання', 'simplybook-integration' ) ),
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
				'post_name'    => 'booking-confirm',
			];
			$post_id   = wp_insert_post( $post_data );
		}
	}

	/**
	 * Register crone.
	 *
	 * @return void
	 */
	public function register_coron() {
		if ( ! wp_next_scheduled( OptionsPage::FIELD_PREFIX . 'refresh_token' ) ) {
			wp_schedule_event( time(), 'half_hour', OptionsPage::FIELD_PREFIX . 'refresh_token' );
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

	/**
	 * Provider filter.
	 *
	 * @param array $providers Providers.
	 *
	 * @return array
	 */
	public function handler_provider_filters( $providers ): array {
		$temp_providers = [];
		$map            = [];

		foreach ( $providers as $provider ) {
			$name = rtrim( $provider->name, '*' );

			if ( ! isset( $map[ $name ] ) ) {
				$map[ $name ] = [
					'clean'  => null,
					'dublic' => null,
				];
			}

			if ( substr( $provider->name, - 1 ) === '*' ) {
				$map[ $name ]['dublic'] = $provider;
			} else {
				$map[ $name ]['clean'] = $provider;
			}
		}

		foreach ( $map as $name => $group ) {
			if ( $group['clean'] ) {
				$new_provider = clone $group['clean'];

				if ( $group['dublic'] ) {
					$new_provider->id_s_dublicat = $group['dublic']->id_sb;
				}

				$temp_providers[] = $new_provider;
			} elseif ( $group['dublic'] ) {
				$new_provider       = clone $group['dublic'];
				$new_provider->name = rtrim( $new_provider->name, '*' );
				$temp_providers[]   = $new_provider;
			}
		}

		return $temp_providers;
	}

	/**
	 * Service filter.
	 *
	 * @param array $services Services.
	 *
	 * @return array
	 */
	public function handler_service_filters( $services ) {
		$merged = [];

		foreach ( $services as $service ) {
			$service_id = $service->service_sb_id;

			if ( ! isset( $merged[ $service_id ] ) ) {
				$new_service               = clone $service;
				$new_service->providers_id = [ $service->provider_id_sb ];
				$merged[ $service_id ]     = $new_service;
			} else {
				if ( ! in_array( $service->provider_id_sb, $merged[ $service_id ]->providers_id, true ) ) {
					$merged[ $service_id ]->providers_id[] = $service->provider_id_sb;
				}
			}
		}

		return array_values( $merged );
	}

	/**
	 * Specialization filters.
	 *
	 * @param string $description Description.
	 *
	 * @return string
	 */
	public function handler_specialization_filters( string $description ): string {
		$dom = new DOMDocument();
		libxml_use_internal_errors( true );

		$dom->loadHTML( mb_convert_encoding( $description, 'HTML-ENTITIES', 'UTF-8' ) );
		$xpath = new DOMXPath( $dom );

		$nodes = $xpath->query( '//h6[@class="specialization"]' );

		$text = '';
		if ( $nodes->length > 0 ) {
			$text = trim( $nodes[0]->textContent );
		}

		return $text;
	}

	/**
	 * Service sub description filter.
	 *
	 * @param string $description Description.
	 *
	 * @return string
	 */
	public function handler_service_sub_description_filter( string $description ): string {
		$dom = new DOMDocument();
		libxml_use_internal_errors( true );

		$dom->loadHTML( mb_convert_encoding( $description, 'HTML-ENTITIES', 'UTF-8' ) );
		$xpath = new DOMXPath( $dom );

		$nodes = $xpath->query( '//p[@class="headline"]' );

		$text = '';
		if ( $nodes->length > 0 ) {
			$text = trim( $nodes[0]->textContent );
		}

		return $text;
	}

	/**
	 * Add script for admin page.
	 *
	 * @return void
	 */
	public function add_admin_scripts(): void {
		$url = SBIP_URL;
		$min = '.min';

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min = '';
		}

		wp_enqueue_script( 'sbip_admin', $url . '/assets/js/admin' . $min . '.js', [ 'jquery' ], SBIP_PHP_REQUIRED_VERSION, true );

		wp_enqueue_style( 'sbip_admin', $url . '/assets/css/admin' . $min . '.css' );

		wp_localize_script(
			'sbip_admin',
			'sbipObject',
			[
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'syncNonce'  => wp_create_nonce( AppointmentAjax::SYNC_SYMPLIBOOK_DATA_ACTION ),
				'syncAction' => AppointmentAjax::SYNC_SYMPLIBOOK_DATA_ACTION,
			]
		);
	}
}
