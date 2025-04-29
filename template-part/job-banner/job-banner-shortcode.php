<?php
/**
 * Job banner shortcode.
 *
 * @package iwpdev/simplybook-integration
 */

$banner      = is_numeric( $atts['banner_image'] ) ? wp_get_attachment_url( $atts['banner_image'] ) : $atts['banner_image'];
$title       = $atts['title'] ?? '';
$sub_title   = $atts['sub_title'] ?? '';
$button_text = $atts['button_text'] ?? '';
$button_url  = $atts['button_url'] ?? '#  ';
?>
<div class="banner-blur">
	<img src="<?php echo esc_url( $banner ); ?>" alt="Banner image">
	<div class="banner-blur-description">
		<?php if ( ! empty( $title ) ) { ?>
			<h5><?php echo esc_html( $title ); ?></h5>
		<?php } ?>
		<h2><?php echo wp_kses_post( $sub_title ?? '' ); ?></h2>
		<a href="<?php echo esc_url( $button_url ) ?>" class="button white">
			<?php echo esc_html( $button_text ); ?>
		</a>
	</div>
</div>
