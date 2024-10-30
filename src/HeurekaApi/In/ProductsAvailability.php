<?php

namespace Heureka\HeurekaApi\In;

use Heureka\Controllers\ProductItemIdController;
use Heureka\Models\ProductModel;
use Heureka\Repositories\ProductRepository;
use Heureka\Repositories\SettingsRepository;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class ProductsAvailability {

	private ProductRepository $product_repository;
	private SettingsRepository $settings_repository;
	private ProductItemIdController $product_item_id_controller;

	public function __construct(
		ProductRepository $product_repository,
		SettingsRepository $settings_repository,
		ProductItemIdController $product_item_id_controller
	) {
		$this->product_repository         = $product_repository;
		$this->settings_repository        = $settings_repository;
		$this->product_item_id_controller = $product_item_id_controller;
	}

	/**
	 * Obtains data from Heureka, process them and returns response from shop for PRODUCTS/AVAILABILITY
	 *
	 * @param array $receive_data
	 *
	 * @return array
	 */
	public function handle_request( array $receive_data ): array {
		$ids         = array_map(
			function ( $item ) {
				return (int) $item;
			},
			array_column( $receive_data['products'], 'id' )
		);
		$product_ids = $this->product_item_id_controller->get_product_ids( $ids );

		$products = $this->product_repository->find_by_ids( $product_ids );
		$counts   = array();
		foreach ( $receive_data['products'] as $item ) {
			$counts[ $item['id'] ] = $item['count'];
		}
		$total = 0;

		$result = array();
		foreach ( $products as $product ) {
			/**
			 * @var ProductModel $product
			 */
			$count       = ( $product->get_wc_product()->get_manage_stock() ) ? min( $product->stock_quantity, (int) $counts[ $product->id ] ) : (int) $counts[ $product->id ];
			$available   = ( $product->get_wc_product()->get_manage_stock() ) ? $product->stock_quantity >= $count : $product->get_is_in_stock();
			$price_total = $product->price * $count;
			$total       += $price_total;
			$result[]    = array(
				'id'         => (string) $product->id,
				'available'  => $available,
				'count'      => $count,
				'delivery'   => $product->get_is_in_stock() && ! $product->get_wc_product()->is_on_backorder() ? (int) $this->settings_repository->get_feed_setting( 'delivery' ) : (int) $this->settings_repository->get_feed_setting( 'delivery_out_of_stock' ),
				'name'       => $product->name,
				'price'      => $product->price,
				'priceTotal' => $price_total,
			);
		}

		if ( empty( $result ) ) {
			foreach ( $ids as $id ) {
				$result[] = array(
					'id'         => (string) $id,
					'available'  => false,
					'count'      => 0,
					'delivery'   => '-1',
					'name'       => '',
					'price'      => 0.0,
					'priceTotal' => 0.0,
				);
			}
		}

		return array(
			'products' => $result,
			'priceSum' => (float) $total,
		);
	}

}
