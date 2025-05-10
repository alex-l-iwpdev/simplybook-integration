<?php
/**
 * Template part ui clock slot.
 *
 * @package iwpdev/simplybook-integration
 */

$key       = $atts['key'];
$time      = $atts['time'];
$full_date = $atts['full_date'];
?>
<div class="clock-radio">
	<input
			id="clock-<?php echo esc_attr( $key ); ?>"
			type="radio"
			value="<?php echo esc_attr( $time ); ?>"
			data-full_date="<?php echo esc_attr( $full_date ); ?>"
			name="clock">
	<label for="clock-<?php echo esc_attr( $key ); ?>">
		<?php echo esc_html( $time ); ?>
	</label>
</div>
