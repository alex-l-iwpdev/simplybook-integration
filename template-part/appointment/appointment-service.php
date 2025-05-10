<?php
/**
 * Template part appointment service form.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;
use Iwpdev\SimplybookIntegration\Main;

$service_id = $atts['service'];

$service = DBHelpers::get_service_by_id( $service_id );
?>
<div class="service">
	<h3><?php esc_attr_e( 'Послуга', 'simplybook-integration' ); ?></h3>
	<?php
	if ( ! empty( $service ) ) {
		foreach ( $service as $item ) {
			?>
			<label for="service" class="icon-check" data-service_id="<?php echo esc_attr( $item->service_sb_id ); ?>">
				<img
						src="<?php echo esc_url( Main::SBIP_BASE_IMAGE_URL . $item->picture_preview ); ?>"
						alt="<?php echo esc_html( $item->service_name ?? '' ); ?>">
				<h5><?php echo esc_html( $item->service_name ?? '' ); ?></h5>
				<p class="icon-clock"><?php echo esc_html( $item->service_duration . __( ' хв', 'simplybook-integration' ) ); ?></p>
				<p class="icon-price"><?php echo esc_html( $item->service_price . __( ' грн', 'simplybook-integration' ) ); ?></p>
			</label>
			<input type="hidden" name="service_id" value="<?php echo esc_attr( $item->service_sb_id ); ?>">
			<?php
		}
	}
	?>
</div>
