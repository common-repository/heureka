<?php

namespace Heureka\HeurekaApi\Out;

use Heureka\Abstracts\AbstractRequest;

class GetStores extends AbstractRequest {

	const ENDPOINT = 'stores';

	public function do_request() {
		return $this->call(
			self::ENDPOINT,
			self::METHOD_GET
		);
	}
}
