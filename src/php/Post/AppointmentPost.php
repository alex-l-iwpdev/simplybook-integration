<?php
/**
 * Appointment Post Handler class.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Post;

use Iwpdev\SimplybookIntegration\API\SimplyBookApi;
use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;

/**
 * AppointmentPost class file.
 */
class AppointmentPost {

	const APOINTMENT_POST_ACTION = 'sbip_appointment_post';

	/**
	 * AppointmentPost construct.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init actions and filters.
	 *
	 * @return void
	 */
	private function init(): void {
		add_action( 'admin_post_nopriv_' . self::APOINTMENT_POST_ACTION, [ $this, 'appointment_post_handler' ] );
		add_action( 'admin_post_' . self::APOINTMENT_POST_ACTION, [ $this, 'appointment_post_handler' ] );
	}

	/**
	 * Appointment post handler.
	 *
	 * @return void
	 */
	public function appointment_post_handler(): void {
		$nonce = ! empty( $_POST[ self::APOINTMENT_POST_ACTION ] ) ? filter_var( wp_unslash( $_POST[ self::APOINTMENT_POST_ACTION ] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( ! wp_verify_nonce( $nonce, self::APOINTMENT_POST_ACTION ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсна безпека Nonce.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		/**
		 * Todo Try it into one check through an array.
		 *
		 * @todo Try it into one check through an array.
		 */

		$sbip_location = ! empty( $_POST['sbip_location'] ) ? filter_var( wp_unslash( $_POST['sbip_location'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $sbip_location ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсне розташування.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$service_id = ! empty( $_POST['service_id'] ) ? filter_var( wp_unslash( $_POST['service_id'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $sbip_location ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсна послуга.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$specialist = ! empty( $_POST['specialist'] ) ? filter_var( wp_unslash( $_POST['specialist'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $sbip_location ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсний фахівець.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$time = ! empty( $_POST['date_and_time'] ) ? filter_var( wp_unslash( $_POST['date_and_time'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $time ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недопустимое время.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$client_name = ! empty( $_POST['name'] ) ? filter_var( wp_unslash( $_POST['name'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $client_name ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсне ім\'я клієнта.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$email = ! empty( $_POST['email'] ) ? filter_var( wp_unslash( $_POST['email'] ), FILTER_SANITIZE_EMAIL ) : null;
		if ( empty( $email ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсний електронний лист.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$phone = ! empty( $_POST['phone'] ) ? filter_var( wp_unslash( $_POST['phone'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $phone ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Недійсний телефон.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		$is_user_isset = DBHelpers::get_simply_book_user( $email );
		$api           = new SimplyBookApi();
		$user_data     = $is_user_isset;

		if ( empty( $is_user_isset ) ) {
			$user_data = $api->create_client(
				[
					'email' => $email,
					'phone' => $phone,
					'name'  => $client_name,
				]
			);

			if ( ! empty( $user_data['id'] ) ) {
				DBHelpers::set_simply_book_user(
					[
						'email' => $email,
						'phone' => $phone,
						'name'  => $client_name,
						'id'    => $user_data['id'],
					]
				);
			}
		}

		if ( empty( $user_data ) ) {
			$json = wp_json_encode(
				[
					'success' => false,
					'message' => __( 'Помилка створення користувача.', 'simplybook-integration' ),
				]
			);
			setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
			exit;
		}

		if ( ! empty( $user_data ) ) {
			$response = $api->create_new_book(
				[
					'count'          => 1,
					'start_datetime' => $time,
					'location_id'    => $sbip_location,
					'provider_id'    => $specialist,
					'service_id'     => $service_id,
					'client_id'      => ! empty( $user_data['client_id'] ) ? $user_data['client_id'] : $user_data['id'],
				]
			);

			if ( ! empty( $response[0]['id'] ) ) {
				$book_date = [
					'start_datetime' => $response[0]['start_datetime'],
					'location_id'    => $response[0]['location_id'],
					'service_id'     => $response[0]['service_id'],
					'provider_id'    => $response[0]['provider_id'],
					'booking_id'     => $response[0]['id'],
				];

				$json = wp_json_encode( $book_date );

				setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
				wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
				exit;
			}
		}

		setcookie( 'booking_confirm', wp_json_encode( $response[0] ), time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
		wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
		exit;
	}
}
