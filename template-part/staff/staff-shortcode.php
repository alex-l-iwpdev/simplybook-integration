<?php
/**
 * Staff Shortcode template.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;
use Iwpdev\SimplybookIntegration\Main;

$service_categories = DBHelpers::get_all_service_category();
$staff_title        = $atts['title'];
//phpcs:disable
$active = ! empty( $_GET['category_id'] ) ? (int) $_GET['category_id'] : $service_categories[0]->service_sb_id;
//phpcs:enable
?>
<div class="doctors-category">
	<div class="doctors-category-filter">
		<h2><?php echo wp_kses_post( $staff_title ); ?></h2>
		<div class="doctors-category-wrapper">
			<?php if ( ! empty( $service_categories ) ) { ?>
				<ul class="doctors-category-menu">
					<?php foreach ( $service_categories as $key => $service_category ) { ?>
						<li class="<?php echo (int) $service_category->service_sb_id === $active ? 'active' : ''; ?>">
							<a
									href="#"
									data-service_sb_id="<?php echo esc_attr( $service_category->service_sb_id ); ?>">
								<?php echo esc_html( $service_category->category_name ); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
	</div>
	<div class="doctors-profiles">
		<?php
		$doctors = apply_filters( 'provider_filters', DBHelpers::get_all_providers_by_service_category_id( (int) $active ) );
		if ( ! empty( $doctors ) ) {
			foreach ( $doctors as $key => $doctor ) {
				if ( $doctor->is_active ) {
					$specialization = apply_filters( 'specialization_filters', $doctor->description );
					?>
					<div class="doctors-profile">
						<div class="photo">
							<img
									src="<?php echo esc_url( Main::SBIP_BASE_IMAGE_URL . $doctor->picture_preview ); ?>"
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
										)
									);
									?>"
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
