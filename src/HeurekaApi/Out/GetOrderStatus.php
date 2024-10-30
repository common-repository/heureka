<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class GetOrderStatus extends AbstractRequest {

	const ENDPOINT = 'order/status';

	public function do_request( $order_id ) {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_GET,
			array(
				'order_id' => $order_id,
			)
		);
	}
}
