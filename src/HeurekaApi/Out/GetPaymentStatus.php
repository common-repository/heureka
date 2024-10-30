<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class GetPaymentStatus extends AbstractRequest {

	const ENDPOINT = 'payment/status';

	public function do_request( $order_id ) {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_GET,
			array( 'order_id' => $order_id )
		);
	}
}
