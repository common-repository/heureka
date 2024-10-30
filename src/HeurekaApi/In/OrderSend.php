<?php

namespace Heureka\HeurekaApi\In;

use Heureka\Repositories\OrderRepository;
use HeurekaDeps\Wpify\Log\RotatingFileLog;
use HeurekaDeps\Wpify\Model\Exceptions\NotFoundException;
use HeurekaDeps\Wpify\Model\Exceptions\NotPersistedException;
use WC_Data_Exception;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class OrderSend {

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
	 * @throws NotFoundException
	 * @throws NotPersistedException
	 * @throws WC_Data_Exception
	 */
	public function handle_request( array $data ): array {
		$order = $this->order_repository->create_order_from_heureka_data( $data );

		if ( is_wp_error( $order ) ) {
			$this->log->error(
				'Order send failed',
				array(
					'api_data' => $data,
					'error'    => $order->get_error_message(),
				)
			);

			return array();
		}

		return array(
			'order_id'       => $order->get_wc_order()->get_id(),
			'internal_id'    => $order->wc_order->get_order_number(),
			'variableSymbol' => (int) $order->wc_order->get_order_number(),
		);
	}
}
