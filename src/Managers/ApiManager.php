<?php

namespace Heureka\Managers;

use Heureka\Api\OrderApi;
use Heureka\Api\PaymentApi;
use Heureka\Api\ProductsApi;

final class ApiManager {

	const PATH = 'heureka/v1';

	public function __construct(
		ProductsApi $example_api,
		PaymentApi $payment_api,
		OrderApi $order_api
	) {
	}
}
