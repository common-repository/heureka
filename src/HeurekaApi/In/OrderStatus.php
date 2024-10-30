<?php

namespace Heureka\HeurekaApi\In;

use Heureka\Repositories\OrderRepository;
use Heureka\Repositories\SettingsRepository;
use Heureka\Settings;
use HeurekaDeps\Wpify\Log\RotatingFileLog;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class OrderStatus {

	private SettingsRepository $settings_repository;
	private OrderRepository $order_repository;
	private RotatingFileLog $log;

	public function __construct(
		OrderRepository $order_repository,
		SettingsRepository $settings_repository,
		RotatingFileLog $log
	) {
		$this->settings_repository = $settings_repository;
		$this->order_repository    = $order_repository;
		$this->log                 = $log;
	}

	/**
	 * Obtains data from Heureka, process them and returns response from shop for PRODUCTS/AVAILABILITY
	 *
	 * @param array $receive_data
	 *
	 * @return array
	 */
	public function handle_request( array $receive_data ): array {
		$order = $this->order_repository->get( (int) $receive_data['order_id'] );
		if ( is_null( $order ) ) {
			$this->log->error(
				'Order status failed',
				array(
					'order_id' => $receive_data['order_id'],
					'api_data' => $receive_data
				)
			);

			return array(
				'order_id' => (int) $receive_data['order_id'],
				'status'   => 8,
			);
		}
		$statuses     = $this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'statuses', array() );
		$order_status = 0;
		foreach ( $statuses as $status ) {
			if ( $status['status'] === sprintf( 'wc-%s', $order->wc_order->get_status() ) ) {
				$order_status = $status['heureka_status'];
				break;
			}
		}

		return array(
			'order_id' => $order->wc_order->get_id(),
			'status'   => (int) $order_status,
		);
	}

}
