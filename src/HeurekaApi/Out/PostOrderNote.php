<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class PostOrderNote extends AbstractRequest {

	const ENDPOINT = 'order/note';

	public function do_request( $order_id, $note ) {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_POST,
			array(
				'order_id' => $order_id,
				'note'     => $note,
			)
		);
	}
}
