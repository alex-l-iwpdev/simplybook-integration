<?php
/**
 * Appointment Ajax handler class.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Ajax;

use Iwpdev\SimplybookIntegration\API\SimplyBookApi;
use Iwpdev\SimplybookIntegration\Main;

/**
 * AppointmentAjax class file.
 */
class AppointmentAjax {

	/**
	 * Action and nonce name appointment.
	 */
	const APPOINTMENTS_ACTIONS_NAME = 'sbip_appointments_action_name';

	/**
	 * Action and nonce name get slot.
	 */
	const SLOTS_ACTIONS_NAME = 'sbip_appointments_action_slots';

	/**
	 * Action and nonce delete booking.
	 */
	const DELETE_BOOKING_ACTION = 'sbip_delete_booking';

	/**
	 * AppointmentAjax construct.
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
		add_action( 'wp_ajax_' . self::APPOINTMENTS_ACTIONS_NAME, [ $this, 'get_appointments_schedule' ] );
		add_action( 'wp_ajax_nopriv_' . self::APPOINTMENTS_ACTIONS_NAME, [ $this, 'get_appointments_schedule' ] );

		add_action( 'wp_ajax_' . self::SLOTS_ACTIONS_NAME, [ $this, 'get_schedule_slots' ] );
		add_action( 'wp_ajax_nopriv_' . self::SLOTS_ACTIONS_NAME, [ $this, 'get_schedule_slots' ] );

		add_action( 'wp_ajax_' . self::DELETE_BOOKING_ACTION, [ $this, 'delete_booking' ] );
		add_action( 'wp_ajax_nopriv_' . self::DELETE_BOOKING_ACTION, [ $this, 'delete_booking' ] );
	}

	/**
	 * Get appointments schedule.
	 *
	 * @return void
	 */
	public function get_appointments_schedule(): void {
		$nonce = ! empty( $_POST['nonce'] ) ? filter_var( wp_unslash( $_POST['nonce'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( ! wp_verify_nonce( $nonce, self::APPOINTMENTS_ACTIONS_NAME ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid security nonce.', 'simplybook-integration' ),
				]
			);
		}

		$location = ! empty( $_POST['location'] ) ? filter_var( wp_unslash( $_POST['location'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $location ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid location.', 'simplybook-integration' ),
				]
			);
		}

		$service = ! empty( $_POST['service'] ) ? filter_var( wp_unslash( $_POST['service'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $service ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid service.', 'simplybook-integration' ),
				]
			);
		}

		$provider = ! empty( $_POST['provider'] ) ? filter_var( wp_unslash( $_POST['provider'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $provider ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid provider.', 'simplybook-integration' ),
				]
			);
		}

		$today     = gmdate( 'Y-m-d' );
		$end_month = gmdate( 'Y-m-t' );

		$api_client = new SimplyBookApi();
		$response   = $api_client->get_schedule(
			[
				'date_from'   => $today,
				'date_to'     => $end_month,
				'provider_id' => $provider,
				'service_id'  => $service,
			]
		);

		if ( empty( $response ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid schedule.', 'simplybook-integration' ),
				]
			);
		}
		$day_off = [];

		foreach ( $response as $day ) {
			if ( $day['is_day_off'] ) {
				$day_off[] = $day['date'];
			}
		}

		wp_send_json_success(
			[
				'success' => true,
				'message' => __( 'Appointment scheduled.', 'simplybook-integration' ),
				'date'    => $day_off,
			]
		);
	}

	/**
	 * Get schedule slots.
	 *
	 * @return void
	 */
	public function get_schedule_slots(): void {
		$nonce = ! empty( $_POST['nonce'] ) ? filter_var( wp_unslash( $_POST['nonce'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( ! wp_verify_nonce( $nonce, self::SLOTS_ACTIONS_NAME ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid security nonce.', 'simplybook-integration' ),
				]
			);
		}

		$service = ! empty( $_POST['service'] ) ? filter_var( wp_unslash( $_POST['service'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $service ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid service.', 'simplybook-integration' ),
				]
			);
		}

		$provider = ! empty( $_POST['provider'] ) ? filter_var( wp_unslash( $_POST['provider'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $provider ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid provider.', 'simplybook-integration' ),
				]
			);
		}

		$date = ! empty( $_POST['date'] ) ? filter_var( wp_unslash( $_POST['date'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( empty( $date ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid date.', 'simplybook-integration' ),
				]
			);
		}

		$api_client = new SimplyBookApi();
		$response   = $api_client->get_slot(
			[
				'date'        => $date,
				'provider_id' => $provider,
				'service_id'  => $service,
				'count'       => 1,
			]
		);

		if ( empty( $response ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => '<p>' . __( 'На цю дату немає вільних слотів, спробуйте змінити дату чи місце обслуговування.', 'simplybook-integration' ) . '</p>',
				]
			);
		}

		ob_start();
		foreach ( $response as $key => $slot ) {
			Main::sbip_get_template_part(
				'UI/Datepicker/time-slot',
				[
					'key'       => $key,
					'time'      => preg_replace( '/:00$/', '', $slot['time'] ),
					'full_date' => $slot['id'],
				]
			);
		}

		$html = ob_get_clean();

		wp_send_json_success(
			[
				'success' => true,
				'slots'   => $html,
			]
		);
	}

	/**
	 * Delete booking ajax handler.
	 *
	 * @return void
	 */
	public function delete_booking(): void {
		$nonce = ! empty( $_POST['nonce'] ) ? filter_var( wp_unslash( $_POST['nonce'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		if ( ! wp_verify_nonce( $nonce, self::DELETE_BOOKING_ACTION ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid security nonce.', 'simplybook-integration' ),
				]
			);
		}

		$booking_id = ! empty( $_POST['booking_id'] ) ? filter_var( wp_unslash( $_POST['booking_id'] ), FILTER_SANITIZE_NUMBER_INT ) : null;
		if ( empty( $booking_id ) ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => __( 'Invalid booking.', 'simplybook-integration' ),
				]
			);
		}

		$api_client = new SimplyBookApi();
		$response   = $api_client->delete_booking( $booking_id );

		if ( true === $response['success'] ) {
			wp_send_json_success();
		}

		wp_send_json_error(
			[
				'success' => false,
				'message' => $response,
			]
		);
	}
}
