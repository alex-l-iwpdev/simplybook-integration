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
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid security nonce.', 'simplybook-integration' ),
				]
			);
		}

		/**
		 * @todo Try it into one check through an array.
		 */

		$sbip_location = ! empty( $_POST['sbip_location'] ) ? filter_var( wp_unslash( $_POST['sbip_location'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $sbip_location ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid location.', 'simplybook-integration' ),
				]
			);
		}

		$service_id = ! empty( $_POST['service_id'] ) ? filter_var( wp_unslash( $_POST['service_id'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $sbip_location ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid Service.', 'simplybook-integration' ),
				]
			);
		}

		$specialist = ! empty( $_POST['specialist'] ) ? filter_var( $_POST['specialist'], FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $sbip_location ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid Specialist.', 'simplybook-integration' ),
				]
			);
		}

		$time = ! empty( $_POST['date_and_time'] ) ? filter_var( wp_unslash( $_POST['date_and_time'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $time ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid Time.', 'simplybook-integration' ),
				]
			);
		}

		$client_name = ! empty( $_POST['name'] ) ? filter_var( wp_unslash( $_POST['name'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $client_name ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid Client Name.', 'simplybook-integration' ),
				]
			);
		}

		$email = ! empty( $_POST['email'] ) ? filter_var( wp_unslash( $_POST['email'] ), FILTER_SANITIZE_EMAIL ) : null;
		if ( empty( $email ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid Email.', 'simplybook-integration' ),
				]
			);
		}

		$phone = ! empty( $_POST['phone'] ) ? filter_var( wp_unslash( $_POST['phone'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $phone ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid phone.', 'simplybook-integration' ),
				]
			);
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
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'User creation error.', 'simplybook-integration' ),
				]
			);
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

				$json = json_encode( $book_date );

				setcookie( 'booking_confirm', $json, time() + 3600, '/', COOKIE_DOMAIN, is_ssl(), true );
				wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
				exit;
			}
		}

		setcookie( 'booking_confirm', json_encode( $response[0] ), time() + 3600, '/' );
		wp_safe_redirect( get_bloginfo( 'url' ) . '/booking-confirm', 301 );
		exit;
	}
}
