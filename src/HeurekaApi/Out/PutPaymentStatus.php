<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class PutPaymentStatus extends AbstractRequest {

	const ENDPOINT = 'payment/status';

	public function do_request( $order_id, $status, $date = null ) {
		if ( ! $date ) {
			$date = date( 'Y-m-d' );
		}

		return $this->call(
			self::ENDPOINT,
			self::METHOD_PUT,
			array(
				'order_id' => $order_id,
				'status'   => $status,
				'date'     => $date,
			)
		);
	}
}
