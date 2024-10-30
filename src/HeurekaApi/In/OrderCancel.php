<?php

namespace Heureka\HeurekaApi\In;

use Heureka\Repositories\OrderRepository;
use HeurekaDeps\Wpify\Log\RotatingFileLog;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class OrderCancel {

	private OrderRepository $order_repository;
	private RotatingFileLog $log;

	public function __construct(
		OrderRepository $order_repository,
		RotatingFileLog $log
	) {
		$this->order_repository = $order_repository;
		$this->log              = $log;
	}

	/**
	 * Obtains data from Heureka, process them and returns response from shop for PRODUCTS/AVAILABILITY
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function handle_request( array $data ): array {
		$order = $this->order_repository->get( $data['order_id'] );
		if ( is_null( $order ) ) {
			$this->log->error(
				'Order cancel failed',
				array(
					'order_id' => $data['order_id'],
					'api_data' => $data
				)
			);

			return array(
				'status' => false,
			);
		}
		$order->get_wc_order()->add_order_note( sprintf( __( 'Cancelled (Heureka): %s', 'heureka' ), $data['reason'] ) );
		$order->get_wc_order()->update_status( 'cancelled' );

		return array(
			'status' => true,
		);
	}
}
