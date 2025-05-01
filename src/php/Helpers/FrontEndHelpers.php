<?php
/**
 * FrontEndHelpers class file.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Helpers;

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
}
