<?php
/**
 * Appointment template.
 *
 * @package iwpdev/simplybook-integration
 */

use Iwpdev\SimplybookIntegration\Helpers\FrontEndHelpers;

$locations = FrontEndHelpers::get_location_select_options_array();

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

		<div class="service">
			<h3>Послуга</h3>
			<label for="specialist-1" class="icon-check">
				<img src="assets/img/curl.png" alt="">
				<h5>Online CURL </h5>
				<p class="icon-clock">30 хв</p>
				<p class="icon-price">700 грн</p>
			</label>
		</div>
		<h3>Фахівець</h3>
		<div class="specialist-block">
			<div class="specialist">
				<input type="radio" id="specialist-1" name="specialist">
				<label for="specialist-1" class="icon-plus">
					<img src="assets/img/photo-1.png" alt="">
					<h5>Юлія Дудій</h5>
					<p class="icon-clock">30 хв</p>
					<p class="icon-price">700 грн</p>
				</label>
			</div>
			<div class="specialist">
				<input type="radio" id="specialist-2" name="specialist">
				<label for="specialist-2" class="icon-plus">
					<img src="assets/img/photo-2.png" alt="">
					<h5>Васильєва Тетяна</h5>
					<p class="icon-clock">30 хв</p>
					<p class="icon-price">700 грн</p>
				</label>
			</div>
			<div class="specialist">
				<input type="radio" id="specialist-3" name="specialist">
				<label for="specialist-3" class="icon-plus">
					<img src="assets/img/photo-3.png" alt="">
					<h5>Юліана Власюк</h5>
					<p class="icon-clock">30 хв</p>
					<p class="icon-price">700 грн</p>
				</label>
			</div>
		</div>
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
