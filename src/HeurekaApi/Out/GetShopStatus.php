<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class GetShopStatus extends AbstractRequest {

	const ENDPOINT = 'shop/status';

	public function do_request() {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_GET
		);
	}
}
