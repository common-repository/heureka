<?php

namespace Heureka\HeurekaApi\In;

use Heureka\Enums\PaymentStatuses;
use Heureka\Repositories\OrderRepository;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class PaymentStatus {

	private OrderRepository $order_repository;

	public function __construct(
		OrderRepository $order_repository
	) {
		$this->order_repository = $order_repository;
	}

	/**
	 * Obtains data from Heureka, process them and returns response from shop for PRODUCTS/AVAILABILITY
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function handle_request( array $data ): array {
		if ( PaymentStatuses::PAID === (int) $data['status'] ) {
			$order = $this->order_repository->get( (int) $data['order_id'] );
			if ( is_null( $order ) ) {
				return array(
					'status' => false,
				);
			}
			$order->get_wc_order()->payment_complete();
		}

		return array(
			'status' => true,
		);
	}
}
