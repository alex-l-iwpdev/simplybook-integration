<?php
/**
 * Staff Shortcode template.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\DB\DBHelpers;
use Iwpdev\SimplybookIntegration\Main;

$service_categories = DBHelpers::get_all_service_category();
$staff_title        = $atts['title'];
//phpcs:disable
$active = (int) $_GET['category_id'] ?? $service_categories[0]->service_sb_id;
//phpcs:enable
?>
<div class="doctors-category">
	<div class="doctors-category-filter">
		<h2><?php echo wp_kses_post( $staff_title ); ?></h2>
		<div class="doctors-category-wrapper">
			<?php if ( ! empty( $service_categories ) ) { ?>
				<ul class="doctors-category-menu">
					<?php foreach ( $service_categories as $key => $service_category ) { ?>
						<li class="<?php echo $service_category->service_sb_id === $active ? 'active' : ''; ?>">
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
					?>
					<div class="doctors-profile">
						<div class="photo">
							<img
									src="<?php echo esc_url( 'https://coma.clinic/' . $doctor->picture_preview ); ?>"
									alt="<?php echo esc_html( $doctor->name ); ?>">
							<div class="tag">ТОП-ЛІКАР</div>
						</div>
						<div class="doctors-profile-description">
							<h3><?php echo esc_html( $doctor->name ); ?></h3>
							<h6 class="specialization">Спеціалізація: Лікар дерматолог, трихолог, косметолог.</h6>
							<div class="hide">
								<?php echo wp_kses( $doctor->description, Main::ALLOW_TAGS_FOR_DESCRIPTION ); ?>
							</div>
							<a href="#" class="more">
								<?php esc_attr_e( 'Читати детальніше', 'simplybook-integration' ); ?>
							</a>
							<a
									href="#"
									class="button"
									data-provider_ids="
									<?php
									echo esc_attr(
										implode(
											',',
											[
												$doctor->id_sb,
												$doctor->id_s_dublicat ?? '',
											]
										)
									);
									?>
">
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
