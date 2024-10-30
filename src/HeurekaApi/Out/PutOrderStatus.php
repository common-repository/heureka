<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class PutOrderStatus extends AbstractRequest {

	const ENDPOINT = 'order/status';

	public function do_request( $order_id, $status ) {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_PUT,
			array(
				'order_id' => $order_id,
				'status'   => $status,
			)
		);
	}
}
