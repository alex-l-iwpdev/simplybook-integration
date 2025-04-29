<?php
/**
 * Simply book banner short code.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\ShortCodes;

/**
 * SimplybookBanner class file.
 */
class SimplybookBanner {
	/**
	 * SimplybookBanner construct.
	 */
	public function __construct() {
		add_shortcode( 'sbip_simplybook_banner', [ $this, 'output' ] );

		// Map shortcode to Visual Composer.
		if ( function_exists( 'vc_lean_map' ) ) {
			vc_lean_map( 'sbip_simplybook_banner', [ $this, 'map' ] );
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
		$banner             = is_numeric( $atts['image'] ) ? wp_get_attachment_url( $atts['image'] ) : $atts['image'];
		$banner_title       = $atts['title'];
		$banner_button_text = $atts['button_text'];
		$banner_button_url  = $atts['button_url'];
		?>
		<div class="banner-teams">
			<?php if ( ! empty( $banner ) ) { ?>
				<img src="<?php echo esc_url( $banner ); ?>" alt="Banner image">
			<?php } ?>
			<div class="banner-teams-desc">
				<?php if ( ! empty( $banner_title ) ) { ?>
					<h1><?php echo wp_kses_post( $banner_title ); ?></h1>
				<?php } ?>
				<?php if ( ! empty( $banner_button_text ) ) { ?>
					<a href="<?php echo esc_html( $banner_button_url ?? '#' ); ?>" class="button">
						<?php echo esc_html( $banner_button_text ); ?>
					</a>
				<?php } ?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Map field.
	 *
	 * @return array
	 */
	public function map(): array {
		return [
			'name'                    => esc_html__( 'Simply Book banner', 'simplybook-integration' ),
			'description'             => esc_html__( 'Simply Book banner', 'simplybook-integration' ),
			'base'                    => 'sbip_simplybook_banner',
			'category'                => __( 'Simply Book', 'simplybook-integration' ),
			'show_settings_on_create' => false,
			'icon'                    => '',
			'params'                  => [
				[
					'type'       => 'attach_image',
					'value'      => '',
					'heading'    => __( 'Banner (picture)', 'simplybook-integration' ),
					'param_name' => 'image',
				],
				[
					'type'       => 'textfield',
					'value'      => '',
					'heading'    => __( 'Banner Title', 'simplybook-integration' ),
					'param_name' => 'title',
				],
				[
					'type'       => 'textfield',
					'value'      => '',
					'heading'    => __( 'Button text', 'simplybook-integration' ),
					'param_name' => 'button_text',
				],
				[
					'type'       => 'textfield',
					'value'      => '',
					'heading'    => __( 'Button text', 'simplybook-integration' ),
					'param_name' => 'button_url',
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
