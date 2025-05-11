<?php
/**
 * Template part appointment service form.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;
use Iwpdev\SimplybookIntegration\Main;

$is_one_service = false;
if ( ! is_array( $atts['service'] ) ) {
	$service_id     = $atts['service'];
	$service        = DBHelpers::get_service_by_id( $service_id );
	$is_one_service = true;
}

if ( is_array( $atts['service'] ) ) {
	$service = $atts['service'];
}
?>
<div class="service">
	<h3><?php esc_attr_e( 'Послуга', 'simplybook-integration' ); ?></h3>
	<?php
	if ( ! empty( $service ) ) {
		foreach ( $service as $key => $item ) {
			$image_url = ! empty( $item->picture_preview ) ? Main::SBIP_BASE_IMAGE_URL . $item->picture_preview : SBIP_URL . '/assets/img/img.png';
			?>
			<input
					id="service-<?php echo esc_attr( $key ); ?>"
					type="radio"
					name="service_id"
					value="<?php echo esc_attr( $item->service_sb_id ); ?>">
			<label
					for="service-<?php echo esc_attr( $key ); ?>"
					class="<?php echo $is_one_service ? 'icon-check' : 'icon-plus'; ?>"
					data-service_id="<?php echo esc_attr( $item->service_sb_id ); ?>">
				<img
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_html( $item->service_name ?? '' ); ?>">
				<h5><?php echo esc_html( $item->service_name ?? '' ); ?></h5>
				<p class="icon-clock"><?php echo esc_html( $item->service_duration . __( ' хв', 'simplybook-integration' ) ); ?></p>
				<p class="icon-price"><?php echo esc_html( $item->service_price . __( ' грн', 'simplybook-integration' ) ); ?></p>
			</label>
			<?php
		}
	}
	?>
</div>
