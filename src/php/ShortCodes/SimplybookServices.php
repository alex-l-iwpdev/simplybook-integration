<?php
/**
 * SimplybookServices class.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\ShortCodes;

use Iwpdev\SimplybookIntegration\Main;

/**
 * SimplybookServices class file.
 */
class SimplybookServices {
	/**
	 * SimplybookServices construct.
	 */
	public function __construct() {
		add_shortcode( 'sbip_simplybook_services', [ $this, 'output' ] );

		// Map shortcode to Visual Composer.
		if ( function_exists( 'vc_lean_map' ) ) {
			vc_lean_map( 'sbip_simplybook_services', [ $this, 'map' ] );
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

		Main::sbip_get_template_part( 'services/services-shortcode', $atts );

		return ob_get_clean();
	}

	/**
	 * Map field.
	 *
	 * @return array
	 */
	public function map(): array {
		return [
			'name'                    => esc_html__( 'Simply Book services', 'simplybook-integration' ),
			'description'             => esc_html__( 'Simply Book services', 'simplybook-integration' ),
			'base'                    => 'sbip_simplybook_services',
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
					'type'       => 'textarea',
					'value'      => '',
					'heading'    => __( 'Sub title', 'simplybook-integration' ),
					'param_name' => 'sub_title',
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
