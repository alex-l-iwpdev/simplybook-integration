<?php
/**
 * Simply book staff short code.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\ShortCodes;

use Iwpdev\SimplybookIntegration\Main;

/**
 * SimplybookStaff class file.
 */
class SimplybookStaff {

	/**
	 * SimplybookStaff construct.
	 */
	public function __construct() {
		add_shortcode( 'sbip_simplybook_staff', [ $this, 'output' ] );

		// Map shortcode to Visual Composer.
		if ( function_exists( 'vc_lean_map' ) ) {
			vc_lean_map( 'sbip_simplybook_staff', [ $this, 'map' ] );
		}
	}

	/**
	 * Output Short Code template
	 *
	 * @param mixed       $atts    Attributes.
	 * @param string|null $content Content.
	 *
	 * @return string
	 */
	public function output( $atts, string $content = null ): string {
		ob_start();
		Main::sbip_get_template_part( 'staff/staff-shortcode', $atts );

		return ob_get_clean();
	}

	/**
	 * Map field.
	 *
	 * @return array
	 */
	public function map(): array {
		return [
			'name'                    => esc_html__( 'Simply Book Staff', 'simplybook-integration' ),
			'description'             => esc_html__( 'Simply Book Staff', 'simplybook-integration' ),
			'base'                    => 'sbip_simplybook_staff',
			'category'                => __( 'Simply Book', 'simplybook-integration' ),
			'show_settings_on_create' => false,
			'icon'                    => '',
			'params'                  => [
				[
					'type'       => 'textfield',
					'value'      => '',
					'heading'    => __( 'Title', 'simplybook-integration' ),
					'param_name' => 'title',
				],
				[
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'coma' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'coma' ),
				],
			],
		];
	}
}
