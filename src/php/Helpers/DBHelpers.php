<?php
/**
 * DB helpers class.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Helpers;

use stdClass;

/**
 * DBHelpers class file.
 */
class DBHelpers {

	/**
	 * Set service category.
	 *
	 * @param int    $id            Simply Book category id.
	 * @param string $category_name Simply Book category name.
	 * @param int    $service_id    Simply Book service id.
	 * @param bool   $is_visible    Simply Book category visible.
	 *
	 * @return bool
	 */
	public static function set_service_category( int $id, string $category_name, int $service_id, bool $is_visible = false ): bool {
		global $wpdb;
		$table_name_services_category = $wpdb->prefix . 'sbip_services_category';
		//phpcs:disable
		$service_category_isset = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name_services_category WHERE category_id = %d AND service_sb_id = %d", $id, $service_id )
		);

		if ( empty( $service_category_isset ) ) {
			$response = $wpdb->insert(
				$table_name_services_category,
				[
					'category_sb_id'     => $id,
					'category_name'      => $category_name,
					'service_sb_id'      => $service_id,
					'category_is_active' => $is_visible,
				],
				[
					'%d',
					'%s',
					'%d',
					'%d',
				]
			);
		}

		if ( ! empty( $service_category_isset ) ) {
			$response = $wpdb->update(
				$table_name_services_category,
				[
					'category_sb_id'     => $id,
					'category_name'      => $category_name,
					'service_sb_id'      => $service_id,
					'category_is_active' => $is_visible,
				],
				[
					'id' => $service_category_isset->id,
				],
				[
					'%d',
					'%s',
					'%d',
					'%d',
				],
				[
					'%d',
				]
			);
		}
		//phpcs:enable
		if ( ! $response ) {
			return false;
		}

		return true;
	}

	/**
	 * Set Service.
	 *
	 * @param array $service_data Service data.
	 *
	 * @return bool
	 */
	public static function set_service( array $service_data ): bool {
		global $wpdb;
		$table_name_services = $wpdb->prefix . 'sbip_services';
		//phpcs:disable
		$service_isset = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name_services WHERE service_sb_id = %d AND provider_id_sb = %d", $service_data['id'], $service_data['provider'] )
		);

		if ( empty( $service_isset ) ) {
			$response = $wpdb->insert(
				$table_name_services,
				[
					'service_sb_id'       => $service_data['id'],
					'service_name'        => $service_data['name'],
					'service_description' => $service_data['description'],
					'service_price'       => $service_data['price'],
					'service_duration'    => $service_data['duration'],
					'provider_id_sb'      => $service_data['provider'],
					'picture'             => $service_data['picture'],
					'picture_preview'     => $service_data['picture_preview'],
					'service_is_active'   => $service_data['is_active'],
					'service_is_visible'  => $service_data['is_visible'],
					'service_post_id'     => $service_data['service_post_id'],
				],
				[
					'%d',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		if ( ! empty( $service_isset ) ) {
			$response = $wpdb->update(
				$table_name_services,
				[
					'service_sb_id'       => $service_data['id'],
					'service_name'        => $service_data['name'],
					'service_description' => $service_data['description'],
					'service_price'       => $service_data['price'],
					'service_duration'    => $service_data['duration'],
					'provider_id_sb'      => $service_data['provider'],
					'picture'             => $service_data['picture'],
					'picture_preview'     => $service_data['picture_preview'],
					'service_is_active'   => $service_data['is_active'],
					'service_is_visible'  => $service_data['is_visible'],
					'service_post_id'     => $service_data['service_post_id'],
				],
				[
					'id' => $service_isset->id,
				],
				[
					'%d',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
				],
				[
					'%d',
				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		//phpcs:enable
		return true;
	}

	/**
	 * Set provider.
	 *
	 * @param array $providers_data Providers data.
	 *
	 * @return bool
	 */
	public static function set_provider( array $providers_data ): bool {
		global $wpdb;
		$table_name_providers = $wpdb->prefix . 'sbip_providers';

		//phpcs:disable
		$providers_isset = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name_providers WHERE id_sb = %d", $providers_data['id'] )
		);

		if ( empty( $providers_isset ) ) {
			$response = $wpdb->insert(
				$table_name_providers,
				[
					'id_sb'           => $providers_data['id'],
					'name'            => $providers_data['name'],
					'email'           => $providers_data['email'],
					'description'     => $providers_data['description'],
					'phone'           => $providers_data['phone'],
					'picture'         => $providers_data['picture'] ?? '',
					'picture_preview' => $providers_data['picture_preview'] ?? '',
					'is_active'       => $providers_data['is_active'],
					'is_visible'      => $providers_data['is_visible'],
				],
				[
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		if ( ! empty( $providers_isset ) ) {
			$response = $wpdb->update(
				$table_name_providers,
				[
					'id_sb'           => $providers_data['id'],
					'name'            => $providers_data['name'],
					'email'           => $providers_data['email'],
					'description'     => $providers_data['description'],
					'phone'           => $providers_data['phone'],
					'picture'         => $providers_data['picture'],
					'picture_preview' => $providers_data['picture_preview'],
					'is_active'       => $providers_data['is_active'],
					'is_visible'      => $providers_data['is_visible'],
				],
				[
					'id' => $providers_isset->id,
				],
				[
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
				],
				[
					'%d',
				]
			);
		}

		//phpcs:enable
		return true;
	}

	/**
	 * Get all Service category.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_all_service_category() {
		global $wpdb;
		$table_name_services_category = $wpdb->prefix . 'sbip_services_category';

		//phpcs:disable
		$results = $wpdb->get_results( "SELECT * FROM $table_name_services_category WHERE `category_is_active` = 1 GROUP BY `category_sb_id`;" );
		//phpcs:enable
		if ( empty( $results ) ) {
			return [];
		}

		return $results;
	}

	/**
	 * Get all providers by service category id.
	 *
	 * @param int $service_category_id Service id.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_all_providers_by_service_category_id( int $service_category_id ) {
		global $wpdb;
		$table_name_providers         = $wpdb->prefix . 'sbip_providers';
		$table_name_services          = $wpdb->prefix . 'sbip_services';
		$table_name_location_provider = $wpdb->prefix . 'sbip_location_provider';

		$sql = "SELECT p.*, lp.location_id
				FROM {$table_name_services} AS s
				INNER JOIN {$table_name_providers} AS p ON p.id_sb = s.provider_id_sb
				INNER JOIN {$table_name_location_provider} AS lp ON lp.provider_id = p.id_sb
				WHERE s.service_sb_id = %s";
		//phpcs:disable
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $service_category_id ) );
		//phpcs:enable
		if ( empty( $results ) ) {
			return [];
		}

		return $results;
	}

	/**
	 * Set all location.
	 *
	 * @param array $location_data Location data.
	 *
	 * @return bool
	 */
	public static function set_all_location( $location_data ) {
		global $wpdb;
		$table_name_location = $wpdb->prefix . 'sbip_location';
		//phpcs:disable
		$providers_isset = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name_location WHERE sb_location_id = %d", $location_data['id'] )
		);

		if ( empty( $providers_isset ) ) {
			$response = $wpdb->insert(
				$table_name_location,
				[
					'sb_location_id'  => $location_data['id'],
					'name'            => $location_data['name'],
					'description'     => $location_data['description'],
					'picture_preview' => $location_data['picture_preview'],
					'address1'        => $location_data['address1'],
					'address2'        => $location_data['address2'] ?? '',
					'phone'           => $location_data['phone'] ?? '',
					'city'            => $location_data['city'],
					'zip'             => $location_data['zip'],
					'is_visible'      => $location_data['is_visible'],
					'lat'             => $location_data['lat'],
					'lng'             => $location_data['lng'],
					'full_address'    => $location_data['full_address'],
				],
				[
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',

				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		if ( ! empty( $providers_isset ) ) {
			$response = $wpdb->update(
				$table_name_location,
				[
					'sb_location_id'  => $location_data['id'],
					'name'            => $location_data['name'],
					'description'     => $location_data['description'],
					'picture_preview' => $location_data['picture_preview'],
					'address1'        => $location_data['address1'],
					'address2'        => $location_data['address2'] ?? '',
					'phone'           => $location_data['phone'] ?? '',
					'city'            => $location_data['city'],
					'zip'             => $location_data['zip'],
					'is_visible'      => $location_data['is_visible'],
					'lat'             => $location_data['lat'],
					'lng'             => $location_data['lng'],
					'full_address'    => $location_data['full_address'],
				],
				[
					'id' => $providers_isset->id,
				],
				[
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%s',
				],
				[
					'%d',
				]
			);
		}

		//phpcs:enable
		return true;
	}

	/**
	 * Set provider location.
	 *
	 * @param int $location_id Location id.
	 * @param int $provider_id Provider id.
	 *
	 * @return bool
	 */
	public static function set_provider_location( int $location_id, int $provider_id ) {
		global $wpdb;
		$table_name_location_provider = $wpdb->prefix . 'sbip_location_provider';
		//phpcs:disable
		$providers_isset = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name_location_provider WHERE location_id = %d AND provider_id = %d", $location_id, $provider_id )
		);

		if ( empty( $providers_isset ) ) {
			$response = $wpdb->insert(
				$table_name_location_provider,
				[
					'location_id' => $location_id,
					'provider_id' => $provider_id,
				],
				[
					'%d',
					'%d',
				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		if ( ! empty( $providers_isset ) ) {
			$response = $wpdb->update(
				$table_name_location_provider,
				[
					'location_id' => $location_id,
					'provider_id' => $provider_id,
				],
				[
					'id' => $providers_isset->id,
				],
				[
					'%d',
					'%d',
				],
				[
					'%d',
				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		//phpcs:enable
		return true;
	}

	/**
	 * Get location.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_locations() {
		global $wpdb;
		$table_name_location_provider = $wpdb->prefix . 'sbip_location';

		//phpcs:disable
		$location = $wpdb->get_results( "SELECT * FROM $table_name_location_provider" );
		//phpcs:enable

		if ( empty( $location ) ) {
			return [];
		}

		return $location;
	}

	/**
	 * Get services by category.
	 *
	 * @param int $category_id Category id.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_services_by_category( int $category_id ) {
		global $wpdb;
		$table_name_services_category = $wpdb->prefix . 'sbip_services_category';
		$table_name_services          = $wpdb->prefix . 'sbip_services';

		$sql = "SELECT s.* FROM $table_name_services AS s INNER JOIN $table_name_services_category AS p ON p.service_sb_id = s.service_sb_id WHERE p.category_sb_id = %d;";

		//phpcs:disable
		$result = $wpdb->get_results( $wpdb->prepare( $sql, $category_id ) );
		//phpcs:enable

		if ( empty( $result ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Get service by id.
	 *
	 * @param int $service_id Service id.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_service_by_id( int $service_id ) {
		if ( empty( $service_id ) ) {
			return [];
		}

		global $wpdb;
		$table_name_services = $wpdb->prefix . 'sbip_services';
		//phpcs:disable
		$sql = "SELECT * FROM $table_name_services WHERE service_sb_id = %d LIMIT 1";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $service_id ) );
		//phpcs:enable
		if ( empty( $result ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Get provider by ids.
	 *
	 * @param array $ids         Providers id.
	 * @param int   $location_id Location id.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_providers_by_ids( array $ids, int $location_id ) {
		global $wpdb;
		$table_name_providers         = $wpdb->prefix . 'sbip_providers';
		$table_name_location_provider = $wpdb->prefix . 'sbip_location_provider';

		$prepare_in = self::prepare_in( $ids );
		//phpcs:disable
		$sql = "SELECT p.* 
				FROM $table_name_providers AS p
				LEFT JOIN $table_name_location_provider AS lp ON lp.provider_id = p.id_sb
				WHERE p.id_sb IN ($prepare_in) AND lp.location_id = %s;";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $location_id ) );
		//phpcs:enable
		if ( empty( $result ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Changes array of items into string of items, separated by comma and sql-escaped
	 *
	 * @see https://coderwall.com/p/zepnaw
	 * @global wpdb       $wpdb
	 *
	 * @param mixed|array $items  item(s) to be joined into string.
	 * @param string      $format %s or %d.
	 *
	 * @return string Items separated by comma and sql-escaped
	 */
	public static function prepare_in( $items, string $format = '%s' ): string {
		global $wpdb;

		$prepared_in = '';
		$items       = (array) $items;
		$how_many    = count( $items );

		if ( $how_many > 0 ) {
			$placeholders    = array_fill( 0, $how_many, $format );
			$prepared_format = implode( ',', $placeholders );
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$prepared_in = $wpdb->prepare( $prepared_format, $items );
		}

		return $prepared_in;
	}

	/**
	 * Get Simply books user.
	 *
	 * @param string $email Email.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_simply_book_user( string $email ) {
		global $wpdb;
		$table_name_clients = $wpdb->prefix . 'sbip_clients';

		//phpcs:disable
		$sql = "SELECT * FROM $table_name_clients WHERE client_email = %s";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $email ) );
		//phpcs:enable

		if ( empty( $result ) ) {
			return [];
		}

		return (array) $result[0];
	}

	/**
	 * Set Simply Book user.
	 *
	 * @param array $data Data.
	 *
	 * @return bool
	 */
	public static function set_simply_book_user( array $data ): bool {
		global $wpdb;
		$table_name_clients = $wpdb->prefix . 'sbip_clients';
		//phpcs:disable
		$response = $wpdb->insert(
			$table_name_clients,
			[
				'client_id'    => $data['id'],
				'client_name'  => $data['name'],
				'client_phone' => $data['phone'],
				'client_email' => $data['email'],
			],
			[
				'%d',
				'%s',
				'%s',
				'%s',
			]
		);
		//phpcs:enable
		if ( ! $response ) {
			return false;
		}

		return true;
	}

	/**
	 * Get location by provider.
	 *
	 * @param int $provider_id Provider Id.
	 *
	 * @return mixed|object|stdClass
	 */
	public static function get_location_by_provider( int $provider_id ) {

		if ( empty( $provider_id ) ) {
			return (object) [];
		}

		global $wpdb;
		$table_name_location          = $wpdb->prefix . 'sbip_location';
		$table_name_location_provider = $wpdb->prefix . 'sbip_location_provider';

		//phpcs:disable
		$sql = "SELECT l.*
				FROM $table_name_location_provider lp
				JOIN $table_name_location l ON l.sb_location_id = lp.location_id
				WHERE lp.provider_id = %d
				ORDER BY lp.location_id ASC";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $provider_id ) );
		//phpcs:enable

		if ( empty( $result ) ) {
			return (object) [];
		}

		return $result[0];
	}

	/**
	 * Get services by provider id.
	 *
	 * @param int $provider_id Provider id.
	 *
	 * @return array|object|stdClass[]
	 */
	public static function get_services_by_provider( int $provider_id ) {
		if ( empty( $provider_id ) ) {
			return (object) [];
		}

		global $wpdb;
		$table_name_providers = $wpdb->prefix . 'sbip_services';

		//phpcs:disable
		$sql    = "SELECT * FROM $table_name_providers WHERE `provider_id_sb` = %d ORDER BY `service_sb_id` ASC";
		$result = $wpdb->get_results( $wpdb->prepare( $sql, $provider_id ) );
		//phpcs:enable

		if ( empty( $result ) ) {
			return (object) [];
		}

		return $result;
	}
}
