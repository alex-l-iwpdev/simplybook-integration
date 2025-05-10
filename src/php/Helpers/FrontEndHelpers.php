<?php
/**
 * FrontEndHelpers class file.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Helpers;

use DateTime;

/**
 * FrontEndHelpers class file.
 */
class FrontEndHelpers {

	/**
	 * Get location select options array
	 *
	 * @return array
	 */
	public static function get_location_select_options_array(): array {
		$locations        = DBHelpers::get_locations();
		$location_options = [];
		if ( empty( $locations ) ) {
			return [];
		}

		foreach ( $locations as $location ) {
			$location_options[] = [
				'id'   => $location->sb_location_id,
				'name' => $location->name,
			];
		}

		return $location_options;
	}

	/**
	 * Get date format for confirm page.
	 *
	 * @param string $date Date.
	 *
	 * @return string
	 */
	public static function get_date_confirm( string $date ) {

		if ( empty( $data ) ) {
			return '';
		}

		$date = new DateTime( $date );

		$days = [
			'Sunday'    => esc_html__( 'Неділя', 'simplybook-integration' ),
			'Monday'    => esc_html__( 'Понеділок', 'simplybook-integration' ),
			'Tuesday'   => esc_html__( 'Вівторок', 'simplybook-integration' ),
			'Wednesday' => esc_html__( 'Середа', 'simplybook-integration' ),
			'Thursday'  => esc_html__( 'Четвер', 'simplybook-integration' ),
			'Friday'    => esc_html__( 'П’ятниця', 'simplybook-integration' ),
			'Saturday'  => esc_html__( 'Субота', 'simplybook-integration' ),
		];

		$months = [
			1  => esc_html__( 'Січня', 'simplybook-integration' ),
			2  => esc_html__( 'Лютого', 'simplybook-integration' ),
			3  => esc_html__( 'Березня', 'simplybook-integration' ),
			4  => esc_html__( 'Квітня', 'simplybook-integration' ),
			5  => esc_html__( 'Травня', 'simplybook-integration' ),
			6  => esc_html__( 'Червня', 'simplybook-integration' ),
			7  => esc_html__( 'Липня', 'simplybook-integration' ),
			8  => esc_html__( 'Серпня', 'simplybook-integration' ),
			9  => esc_html__( 'Вересня', 'simplybook-integration' ),
			10 => esc_html__( 'Жовтня', 'simplybook-integration' ),
			11 => esc_html__( 'Листопада', 'simplybook-integration' ),
			12 => esc_html__( 'Грудня', 'simplybook-integration' ),
		];

		$day_name = $days[ $date->format( 'l' ) ];
		$day      = $date->format( 'j' );
		$month    = $months[ (int) $date->format( 'n' ) ];
		$year     = $date->format( 'Y' );
		$time     = $date->format( 'H:i' );

		return ucfirst( "$day_name $day $month $year о $time" );
	}
}
