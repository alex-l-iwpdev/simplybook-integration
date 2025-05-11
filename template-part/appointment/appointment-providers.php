<?php
/**
 * Template part appointment providers form.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;
use Iwpdev\SimplybookIntegration\Main;

$providers_id = $atts['providers'] ?? [];
$location_id  = $atts['location_id'] ?? 0;

$providers          = apply_filters( 'provider_filters', DBHelpers::get_providers_by_ids( $providers_id, $location_id ) );
$is_single_provider = false;
if ( 1 === count( $providers ) ) {
	$is_single_provider = true;
}
?>
<h3><?php esc_attr_e( 'Фахівець', 'simplybook-integration' ); ?></h3>
<div class="specialist-block">
	<?php
	if ( ! empty( $providers ) ) {
		foreach ( $providers as $key => $provider ) {
			$image_url = ! empty( $provider->picture_preview ) ? Main::SBIP_BASE_IMAGE_URL . $provider->picture_preview : SBIP_URL . '/assets/img/img.png';
			?>
			<div class="specialist">
				<input
						type="radio"
						id="specialist-<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( $provider->id_sb ); ?>"
					<?php checked( $is_single_provider, true ); ?>
						name="specialist">
				<label for="specialist-<?php echo esc_attr( $key ); ?>" class="icon-plus">
					<img
							src="<?php echo esc_url( $image_url ); ?>"
							alt="<?php echo esc_attr( $provider->name ); ?>">
					<h5><?php echo esc_attr( $provider->name ); ?></h5>
				</label>
			</div>
			<?php
		}
	}
	?>
</div>
