<?php

namespace Heureka\Repositories;

use Heureka\Models\ProductModel;

/**
 * @method ProductModel get( $object = null )
 */
class ProductRepository extends \HeurekaDeps\Wpify\Model\ProductRepository {

	/**
	 * @inheritDoc
	 */
	public function model(): string {
		return ProductModel::class;
	}

	/**
	 * Find by ids
	 *
	 * @param array $ids IDs.
	 *
	 * @return array
	 */
	public function find_by_ids( array $ids ): array {
		$products = $this->find( array( 'include' => $ids ) );

		if ( empty( $products ) ) { // May be variable.
			$collection = array();
			foreach ( $ids as $id ) {
				$product = wc_get_product( $id );
				if ( $product instanceof \WC_Product_Variation ) {
					$collection[] = $this->factory( $product );
				}
			}

			return $this->collection_factory( $collection );
		}

		return $products;
	}
}
