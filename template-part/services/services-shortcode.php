<?php
/**
 * Services short code.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;

$service_title      = $atts['title'] ?? '';
$sub_title          = $atts['sub_title'] ?? '';
$service_categories = DBHelpers::get_all_service_category();
//phpcs:disable
$active = ! empty( $_GET['category_id'] ) ? (int) $_GET['category_id'] : (int) $service_categories[0]->category_sb_id;
//phpcs:enable

$services = apply_filters( 'service_filters', DBHelpers::get_services_by_category( $active ) );
?>
<div class="services-wrapper">
	<h2>
		<?php echo wp_kses_post( $sub_title ); ?>
	</h2>
	<div class="doctors-category-wrapper brown">
		<h3><?php esc_attr_e( 'Обери категорію', 'simplybook-integration' ); ?></h3>
		<?php if ( ! empty( $service_categories ) ) { ?>
			<ul class="doctors-category-menu">
				<?php foreach ( $service_categories as $key => $service_category ) { ?>
					<li class="<?php echo (int) $service_category->category_sb_id === $active ? 'active' : ''; ?>">
						<a
								href="#"
								data-service_sb_id="<?php echo esc_attr( $service_category->category_sb_id ); ?>">
							<?php echo esc_html( $service_category->category_name ); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
	<div class="doctors-category-table">
		<?php
		if ( ! empty( $services ) ) {
			foreach ( $services as $service ) {
				?>
				<div class="tr">
					<div class="th">
						<h3><?php echo esc_html( $service->service_name ); ?></h3>
					</div>
					<div class="td">
						<?php echo wp_kses_post( $service->service_description ); ?>
						<!--						<a href="-->
						<?php //echo esc_url( get_the_permalink( $service->service_post_id ) ); ?><!--" class="more">-->
						<!--							--><?php //esc_attr_e( 'детальніше', 'simplybook-integration' ); ?>
						<!--						</a>-->
					</div>
					<div class="td">
						<p class="clock"><?php echo esc_html( $service->service_duration . __( ' хв', 'simplybook-integration' ) ); ?></p>
						<p class="price"><?php echo esc_html( $service->service_price . __( ' грн', 'simplybook-integration' ) ); ?></p>
					</div>
					<div class="td">
						<a
								href="<?php echo esc_url( get_bloginfo( 'url' ) . '/appointment/?providers=' . implode( ',', $service->providers_id ) . '&service=' . $service->service_sb_id ); ?>"
								class="button">
							<?php esc_attr_e( 'записатись на прийом', 'simplybook-integration' ); ?>
						</a>
					</div>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>
