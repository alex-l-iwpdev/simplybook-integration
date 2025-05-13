<?php
/**
 * Staff Shortcode template.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;
use Iwpdev\SimplybookIntegration\Main;

?>
<div class="doctors-profiles">
	<?php
	$doctors = apply_filters( 'provider_filters', DBHelpers::get_all_providers() );
	if ( ! empty( $doctors ) ) {
		foreach ( $doctors as $key => $doctor ) {
			if ( $doctor->is_active ) {
				$specialization = apply_filters( 'specialization_filters', $doctor->description );

				$image_url = Main::SBIP_BASE_IMAGE_URL . $doctor->picture_preview;
				if ( empty( $doctor->picture_preview ) ) {
					$image_url = SBIP_URL . '/assets/img/no-image.jpg';
				}
				?>
				<div class="doctors-profile">
					<div class="photo">
						<img
								src="<?php echo esc_url( $image_url ); ?>"
								alt="<?php echo esc_html( $doctor->name ); ?>">
					</div>
					<div class="doctors-profile-description">
						<h3><?php echo esc_html( $doctor->name ); ?></h3>
						<h6 class="specialization"><?php echo esc_html( $specialization ?? '' ); ?></h6>
						<div class="hide">
							<?php echo wp_kses_post( $doctor->description ); ?>
						</div>
						<a href="#" class="more">
							<?php esc_attr_e( 'Читати детальніше', 'simplybook-integration' ); ?>
						</a>
						<a
								href="
									<?php
								echo esc_url(
									get_bloginfo( 'url' ) . '/appointment/?provider=' . implode(
										',',
										[
											$doctor->id_sb,
											$doctor->id_s_dublicat ?? '',
										]
									) . '&location=' . $doctor->locations
								);
								?>
									"
								class="button">
							<?php esc_attr_e( 'записатись на прийом', 'simplybook-integration' ); ?>
						</a>
					</div>
				</div>
				<?php
			}
		}
	}
	?>
</div>
</div>
