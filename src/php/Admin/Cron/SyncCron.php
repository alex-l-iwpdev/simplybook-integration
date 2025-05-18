<?php
/**
 * Sync Cron class file.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\Admin\Cron;

use Iwpdev\SimplybookIntegration\API\SimplyBookApi;
use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;

/**
 * SyncCron class.
 */
class SyncCron {

	/**
	 * Cron hook name.
	 */
	const CRON_HOOK = 'sbip_sync_cron_weekly_event';

	/**
	 * SyncCron construct.
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
		add_action( 'init', [ $this, 'schedule_event' ] );
		add_action( self::CRON_HOOK, [ $this, 'run_task' ] );
	}

	/**
	 * Add sync cron job.
	 *
	 * @return void
	 */
	public function schedule_event(): void {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'weekly', self::CRON_HOOK );
		}
	}

	/**
	 * Run sync cron job.
	 *
	 * @return void
	 */
	public function run_task(): void {
		$api_client = new SimplyBookApi();

		DBHelpers::clear_tables_before_sync();

		$api_client->get_all_service_category();
		$api_client->get_all_services();
		$api_client->get_all_providers();
		$api_client->get_all_location();
	}
}
