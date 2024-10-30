<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class PostOrderInvoice extends AbstractRequest {

	const ENDPOINT = 'order/invoice';

	public function do_request( $order_id, $path ) {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_POST_MULTIPART,
			array(
				'order_id' => $order_id,
				'invoice'  => $path,
			)
		);
	}

}
