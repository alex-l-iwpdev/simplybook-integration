<?php
/**
 * Appointment template.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\FrontEndHelpers;
use Iwpdev\SimplybookIntegration\Main;
use Iwpdev\SimplybookIntegration\Post\AppointmentPost;

$locations = FrontEndHelpers::get_location_select_options_array();
//phpcs:disable
$providers         = ! empty( $_GET['providers'] ) ? explode( ',', $_GET['providers'] ) : false;
$provider          = ! empty( $_GET['provider'] ) ? (int) $_GET['provider'] : false;
$service           = ! empty( $_GET['service'] ) ? (int) $_GET['service'] : false;
$services          = ! empty( $_GET['services'] ) ? explode( ',', $_GET['services'] ) : false;
$location_selected = ! empty( $_GET['location'] ) ? (int) $_GET['location'] : $locations[0]['id'];
//phpcs:enable
?>
<form
		class="appointment"
		method="post"
		action="<?php echo admin_url( 'admin-post.php' ); ?>">
	<div class="left-block">
		<h1><?php esc_attr_e( 'запис на прийом', 'simplybook-integration' ); ?></h1>
		<div class="select icon-marker">
			<select name="sbip_location" id="sbip-location">
				<?php foreach ( $locations as $location ) { ?>
					<option value="<?php echo esc_attr( $location['id'] ); ?>" <?php selected( $location_selected, $location['id'] ) ?>>
						<?php echo esc_html( $location['name'] ); ?>
					</option>
				<?php } ?>
			</select>
		</div>
		<?php
		if ( ! empty( $providers ) ) {
			Main::sbip_get_template_part(
				'appointment/appointment-service',
				[
					'service' => $service,
				]
			);
			Main::sbip_get_template_part(
				'appointment/appointment-providers',
				[
					'providers'   => $providers,
					'location_id' => $location_selected,
				]
			);
		}

		if ( empty( $providers ) ) {
			Main::sbip_get_template_part( 'appointment/appointment-providers' );
			Main::sbip_get_template_part( 'appointment/appointment-service' );
		}
		?>

	</div>
	<div class="right-block">
		<h2><?php esc_attr_e( 'Ще трошки...', 'simplybook-integration' ); ?></h2>
		<div class="datepicker-block">
			<div class="pswp__preloader__icn" style="display: none;">
				<div class="pswp__preloader__cut">
					<div class="pswp__preloader__donut"></div>
				</div>
			</div>
			<h3><?php esc_html_e( 'Дата', 'simplybook-integration' ); ?></h3>
			<div class="datepicker"></div>
		</div>
		<div class="clock-block">
			<h3><?php esc_html_e( 'Час', 'simplybook-integration' ); ?></h3>
			<div class="clocks-radio">
				<div class="pswp__preloader__icn" style="display: none;">
					<div class="pswp__preloader__cut">
						<div class="pswp__preloader__donut"></div>
					</div>
				</div>

			</div>
		</div>
		<h3 class="price"><?php esc_html_e( 'Всього:', 'simplybook-integration' ); ?><span></span></h3>
		<p class="provider"><br><span></span></p>
		<div class="input">
			<input
					type="text"
					name="name"
					id="sbi-name"
					value=""
					placeholder="<?php esc_attr_e( 'ПІБ', 'simplybook-integration' ); ?>"
					required>
		</div>
		<div class="input">
			<input
					type="email"
					name="email"
					id="sbi-email"
					value=""
					placeholder="<?php esc_attr_e( 'Електронна пошта', 'simplybook-integration' ); ?>"
					required>
		</div>
		<div class="input">
			<input
					type="tel"
					name="phone"
					id="sbi-phone"
					value=""
					placeholder="<?php esc_attr_e( 'Телефон', 'simplybook-integration' ); ?>"
					required>
		</div>
		<?php wp_nonce_field( AppointmentPost::APOINTMENT_POST_ACTION, AppointmentPost::APOINTMENT_POST_ACTION ); ?>
		<input type="hidden" name="action" value="<?php echo esc_attr( AppointmentPost::APOINTMENT_POST_ACTION ); ?>">
		<input type="hidden" name="date_and_time" value="">
		<input
				type="submit"
				class="button"
				disabled
				value="<?php esc_attr_e( 'Записатись на прийом', 'simplybook-integration' ); ?>">
	</div>
</form>
