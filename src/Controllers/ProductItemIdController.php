<?php

namespace Heureka\Controllers;

use Heureka\Repositories\ProductItemIdRepository;

class ProductItemIdController {

	private ProductItemIdRepository $product_item_id_repository;

	public function __construct(
		ProductItemIdRepository $product_item_id_repository
	) {
		$this->product_item_id_repository = $product_item_id_repository;
	}

	/**
	 * @param $ids
	 *
	 * @return array
	 */
	public function get_product_ids( $ids ): array {
		$product_ids = [];

		foreach ( $ids as $id ) {
			$product_ids[] = $this->get_product_id( $id );
		}

		return $product_ids;
	}

	/**
	 * @param $id
	 *
	 * @return int
	 */
	public function get_product_id( $id ): int {
		$product_item_id = $this->product_item_id_repository->get_by_item_id( $id );

		return intval( ! is_null( $product_item_id ) ? $product_item_id->product_id : $id );
	}
}
