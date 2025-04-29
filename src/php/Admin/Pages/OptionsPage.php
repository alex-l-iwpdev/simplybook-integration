<?php
/**
 * Plugin option page.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Admin\Pages;

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * OptionsPage class file.
 */
class OptionsPage {

	/**
	 * Fields name prefix.
	 */
	const FIELD_PREFIX = 'sbip_';

	/**
	 * OptionsPage construct.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init actions and filters.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'after_setup_theme', [ $this, 'load_boot_class_caron_fields' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'create_options_page' ] );
	}

	/**
	 * Load boot class caron fields.
	 *
	 * @return void
	 */
	public function load_boot_class_caron_fields(): void {
		Carbon_Fields::boot();
	}

	/**
	 * Create options page.
	 *
	 * @return void
	 */
	public function create_options_page(): void {
		Container::make( 'theme_options', __( 'SimplyBook Options', 'simplybook-integration' ) )
			->add_fields(
				[
					Field::make( 'text', self::FIELD_PREFIX . 'company_login', __( 'Company Login', 'simplybook-integration' ) ),
					Field::make( 'text', self::FIELD_PREFIX . 'login', __( 'Login', 'simplybook-integration' ) ),
					Field::make( 'text', self::FIELD_PREFIX . 'password', __( 'Password', 'simplybook-integration' ) )
						->set_attribute( 'type', 'password' ),
				]
			);
	}
}
