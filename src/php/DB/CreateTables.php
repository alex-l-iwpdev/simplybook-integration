<?php
/**
 * DB helpers class create tables.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\DB;

use Iwpdev\SimplybookIntegration\Admin\Pages\OptionsPage;

/**
 * CreateTables class file.
 */
class CreateTables {
	/**
	 * CreateTables construct.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init actions and filters.
	 *
	 * @return void
	 */
	private function init(): void {
		add_action( 'init', [ $this, 'create_tables' ] );
	}

	/**
	 * Create tables.
	 *
	 * @return void
	 */
	public function create_tables(): void {
		global $wpdb;
		$table_name_services          = $wpdb->prefix . 'sbip_services';
		$table_name_providers         = $wpdb->prefix . 'sbip_providers';
		$table_name_services_category = $wpdb->prefix . 'sbip_services_category';
		$table_name_location          = $wpdb->prefix . 'sbip_location';
		$table_name_location_provider = $wpdb->prefix . 'sbip_location_provider';
		$table_name_clients           = $wpdb->prefix . 'sbip_clients';

		//phpcs:disable
		$service_table_created = get_option( OptionsPage::FIELD_PREFIX . 'services_created', false );
		if ( empty( $service_table_created ) ) {
			$sql    = "CREATE TABLE IF NOT EXISTS $table_name_services (
					  `id` BIGINT(255) NOT NULL AUTO_INCREMENT,
					  `service_sb_id` INT NOT NULL,
					  `service_name` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `service_description` TEXT(255) NULL,
					  `service_price` VARCHAR(45) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `service_duration` INT NULL,
					  `picture` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `picture_preview` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `provider_id_sb` INT NOT NULL,
					  `service_post_id`  BIGINT(255) NOT NULL,
					  `service_is_active` TINYINT NULL DEFAULT 0,
					  `service_is_visible` TINYINT NULL,
					  PRIMARY KEY (`id`, `provider_id_sb`, `service_sb_id`, `service_post_id`));";
			$result = $wpdb->query( $sql );
			if ( $result ) {
				update_option( OptionsPage::FIELD_PREFIX . 'services_created', true );
			}
		}

		$providers_table_created = get_option( OptionsPage::FIELD_PREFIX . 'providers_created', false );
		if ( empty( $providers_table_created ) ) {
			$sql = "CREATE TABLE IF NOT EXISTS $table_name_providers (
					  `id` BIGINT(255) NOT NULL AUTO_INCREMENT,
					  `id_sb` INT NOT NULL,
					  `name` VARCHAR(150) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NOT NULL,
					  `email` VARCHAR(150) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `description` TEXT(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `phone` VARCHAR(45) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `picture` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NOT NULL,
					  `picture_preview` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `is_active` TINYINT NULL DEFAULT 0,
					  `is_visible` TINYINT NULL DEFAULT 0,
					  `update_date` TIMESTAMP GENERATED ALWAYS AS (NOW()),
					  PRIMARY KEY (`id`, `id_sb`));";

			$result = $wpdb->query( $sql );
			if ( $result ) {
				update_option( OptionsPage::FIELD_PREFIX . 'providers_created', true );
			}
		}

		$services_category_table_created = get_option( OptionsPage::FIELD_PREFIX . 'services_category_created', false );
		if ( empty( $services_category_table_created ) ) {
			$sql    = "CREATE TABLE IF NOT EXISTS $table_name_services_category (
					  `id` BIGINT(255)  NOT NULL AUTO_INCREMENT,
					  `category_sb_id` INT NOT NULL,
					  `service_sb_id` INT NOT NULL,
					  `category_name` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci' NULL,
					  `category_is_active` TINYINT NULL DEFAULT 0,
					  PRIMARY KEY (`id`, `category_sb_id`));";
			$result = $wpdb->query( $sql );

			if ( $result ) {
				update_option( OptionsPage::FIELD_PREFIX . 'services_category_created', true );
			}
		}

		$location_table_created = get_option( OptionsPage::FIELD_PREFIX . 'location_created', false );

		if ( empty( $location_table_created ) ) {
			$sql = "CREATE TABLE IF NOT EXISTS $table_name_location (
    				`id` INT NOT NULL AUTO_INCREMENT , 
    				`sb_location_id` INT NOT NULL , 
    				`name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`description` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`picture_preview` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`address1` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`address2` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`phone` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`city` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`zip` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`is_visible` BOOLEAN NOT NULL DEFAULT FALSE , 
    				`lat` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`lng` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				`full_address` VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL , 
    				PRIMARY KEY (`id`), UNIQUE (`sb_location_id`));";

			$result = $wpdb->query( $sql );
			if ( $result ) {
				update_option( OptionsPage::FIELD_PREFIX . 'location_created', true );
			}
		}

		$location_provider_table_created = get_option( OptionsPage::FIELD_PREFIX . 'location_provider_created', false );
		if ( empty( $location_provider_table_created ) ) {
			$sql = "CREATE TABLE IF NOT EXISTS $table_name_location_provider (
    			`id` INT NOT NULL AUTO_INCREMENT , 
    			`location_id` INT NOT NULL , 
    			`provider_id` INT NOT NULL , 
    			PRIMARY KEY (`id`));";

			$result = $wpdb->query( $sql );

			if ( $result ) {
				update_option( OptionsPage::FIELD_PREFIX . 'location_provider_created', true );
			}
		}

		$clients_table_created = get_option( OptionsPage::FIELD_PREFIX . 'clients_created', false );
		if ( empty( $clients_table_created ) ) {
			$sql = "CREATE TABLE $table_name_clients (
					`id` BIGINT NOT NULL AUTO_INCREMENT , 
					`client_id` BIGINT NOT NULL , 
					`client_name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
					`client_phone` VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL , 
					`client_email` VARCHAR(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL ,
					 PRIMARY KEY (`id`));";

			$result = $wpdb->query( $sql );

			if ( $result ) {
				update_option( OptionsPage::FIELD_PREFIX . 'clients_created', true );
			}
		}
		//phpcs:enable
	}
}
