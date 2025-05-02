<?php
/**
 * Appointment template.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\FrontEndHelpers;
use Iwpdev\SimplybookIntegration\Main;

$locations = FrontEndHelpers::get_location_select_options_array();
//phpcs:disable
$providers = ! empty( $_GET['providers'] ) ? explode( ',', $_GET['providers'] ) : false;
$provider  = ! empty( $_GET['provider'] ) ? (int) $_GET['provider'] : false;
$service   = ! empty( $_GET['service'] ) ? (int) $_GET['service'] : false;
$services  = ! empty( $_GET['services'] ) ? explode( ',', $_GET['services'] ) : false;
$location  = ! empty( $_GET['location'] ) ? (int) $_GET['location'] : $locations[0]['id'];
//phpcs:enable
?>
<form class="appointment">
	<div class="left-block">
		<h1><?php esc_attr_e( 'запис на прийом', 'simplybook-integration' ); ?></h1>
		<div class="select icon-marker">
			<select name="sbip_location" id="sbip-location">
				<?php foreach ( $locations as $location ) { ?>
					<option value="<?php echo esc_attr( $location['id'] ); ?>">
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
					'location_id' => $location['id'],
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
		<h2>Ще трошки...</h2>
		<div class="datepicker-block">
			<h3>Дата</h3>
			<div class="datepicker"></div>
		</div>
		<div class="clock-block">
			<h3>Час</h3>
			<div class="clocks-radio">
				<div class="clock-radio">
					<input id="clock-1" type="radio" name="clock">
					<label for="clock-1">09:00</label>
				</div>
				<div class="clock-radio">
					<input id="clock-2" type="radio" name="clock">
					<label for="clock-2">10:00</label>
				</div>
				<div class="clock-radio">
					<input id="clock-3" type="radio" name="clock">
					<label for="clock-3">11:00</label>
				</div>
				<div class="clock-radio">
					<input id="clock-4" type="radio" name="clock">
					<label for="clock-4">12:00</label>
				</div>
				<div class="clock-radio">
					<input id="clock-5" type="radio" name="clock">
					<label for="clock-5">15:00</label>
				</div>
				<div class="clock-radio">
					<input id="clock-6" type="radio" name="clock">
					<label for="clock-6">17:30</label>
				</div>
				<div class="clock-radio">
					<input id="clock-7" type="radio" name="clock">
					<label for="clock-7">19:00</label>
				</div>
				<div class="clock-radio">
					<input id="clock-8" type="radio" name="clock">
					<label for="clock-8">22:00</label>
				</div>
			</div>
		</div>
		<h3 class="price">Всього:<span>700 грн</span></h3>
		<p>Онлайн консультація трихолога, <br> Юлія Дудій, 10:00</p>
		<input type="submit" class="button" value="Записатись на прийом">
	</div>
</form>
