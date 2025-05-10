<?php
/**
 * SimplybookBoolingConfirm class file.
 *
 * @package iwpdev/simplybook-integration
 */

namespace Iwpdev\SimplybookIntegration\ShortCodes;

use Iwpdev\SimplybookIntegration\Helpers\DBHelpers;
use Iwpdev\SimplybookIntegration\Helpers\FrontEndHelpers;
use Iwpdev\SimplybookIntegration\Main;

/**
 * SimplybookBoolingConfirm class.
 */
class SimplybookBoolingConfirm {

	/**
	 * SimplybookBoolingConfirm construct.
	 */
	public function __construct() {
		add_shortcode( 'sbip_simplybook_booking_confirm', [ $this, 'output' ] );

		// Map shortcode to Visual Composer.
		if ( function_exists( 'vc_lean_map' ) ) {
			vc_lean_map( 'sbip_simplybook_booking_confirm', [ $this, 'map' ] );
		}
	}

	/**
	 * Output Short Code template
	 *
	 * @param mixed       $atts    Attributes.
	 * @param string|null $content Content.
	 *
	 * @return string
	 */
	public function output( $atts, string $content = null ): string {
		ob_start();

		//phpcs:disable
		$data = json_decode( wp_unslash( $_COOKIE['booking_confirm'] ), true );
		//phpcs:enable
		$date         = FrontEndHelpers::get_date_confirm( $data['start_datetime'] ?? '' );
		$service_data = DBHelpers::get_service_by_id( $data['service_id'] ?? 0 );
		$location     = DBHelpers::get_location_by_provider( (int) $data['provider_id'] ?? 0 );
		?>
		<div class="confirmation">
			<h1><?php echo esc_html( $date ); ?></h1>
			<div class="icon-check confirm">
				<?php esc_attr_e( 'Підтверджено', 'simplybook-integration' ); ?>
			</div>
			<div class="pswp__preloader__icn" style="display: none;">
				<div class="pswp__preloader__cut">
					<div class="pswp__preloader__donut"></div>
				</div>
			</div>
			<?php
			if ( ! empty( $service_data ) ) {
				foreach ( $service_data as $service_item ) {
					?>
					<div class="procedure">
						<img
								src="<?php echo esc_url( Main::SBIP_BASE_IMAGE_URL . $service_item->picture_preview ); ?>"
								alt="<?php echo esc_html( $service_item->service_name ?? '' ); ?>">
						<div class="desc-confirm">
							<h3><?php echo esc_html( $item->service_name ?? '' ); ?> </h3>
							<p><?php echo esc_html( $location->full_address ?? '' ); ?></p>
						</div>
						<a
								href="<?php echo esc_url( 'https://www.google.com/maps/?q=' . $location->lat . ',' . $location->lng ) ?>"
								target="_blank"
								rel="nofollow noindex"
								class="icon-marker">
							<?php esc_attr_e( 'Локація', 'simplybook-integration' ); ?>
						</a>
						<a
								href="#"
								class="icon-xmark delete-booking"
								data-booking_id="<?php echo esc_attr( $data['booking_id'] ?? '' ); ?>">
							<?php esc_attr_e( 'Відмінити', 'simplybook-integration' ); ?>
						</a>
					</div>

					<ul class="procedure-invoice">
						<li>
							<h4>
								<?php echo esc_html( $service_item->service_name ?? '' ); ?>
								<i><?php echo esc_html( $service_item->service_duration . esc_html__( ' хв', 'simplybook-integration' ) ); ?></i>
							</h4>
							<span><?php echo esc_html( $service_item->service_price . esc_html__( ' грн', 'simplybook-integration' ) ); ?></span>
						</li>
						<li>
							<h4><?php esc_html_e( 'Всього:', 'simplybook-integration' ); ?></h4>
							<span><?php echo esc_html( $service_item->service_price . esc_html__( ' грн', 'simplybook-integration' ) ); ?></span>
						</li>
					</ul>
					<?php
				}
			}
			?>
			<h5><?php esc_attr_e( 'Як відмінити запис?', 'simplybook-integration' ); ?></h5>
			<p><?php esc_attr_e( 'Напишіть нашому менеджеру, якщо змінились плани' ); ?></p>
			<h5>
				<a href="mailto:infocoma@ukr.net">
					<?php esc_attr_e( 'НАПИСАТИ МЕНЕДЖЕРУ', 'simplybook-integration' ); ?>
				</a>
			</h5>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Map field.
	 *
	 * @return array
	 */
	public function map(): array {
		return [
			'name'                    => esc_html__( 'Simply Book confirm', 'simplybook-integration' ),
			'description'             => esc_html__( 'Simply Book confirm', 'simplybook-integration' ),
			'base'                    => 'sbip_simplybook_booking_confirm',
			'category'                => __( 'Simply Book', 'simplybook-integration' ),
			'show_settings_on_create' => false,
			'icon'                    => '',
			'params'                  => [
				[
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'simplybook-integration' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'simplybook-integration' ),
				],
			],
		];
	}
}
