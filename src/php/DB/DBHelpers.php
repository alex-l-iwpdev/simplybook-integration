<?php
/**
 * DB helpers class.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\DB;

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
				]
			);

			if ( ! $response ) {
				return false;
			}
		}

		if ( ! empty( $service_category_isset ) ) {
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
				],
				[
					'id' => $service_category_isset->id,
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
		$table_name_providers = $wpdb->prefix . 'sbip_providers';
		$table_name_services  = $wpdb->prefix . 'sbip_services';

		$sql = "SELECT p.* FROM $table_name_services AS s INNER JOIN $table_name_providers AS p ON p.id_sb = s.provider_id_sb WHERE s.service_sb_id = %s;";
		//phpcs:disable
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $service_category_id ) );
		//phpcs:enable
		if ( empty( $results ) ) {
			return [];
		}

		return $results;
	}
}
