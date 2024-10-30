<?php

namespace Heureka\Repositories;

use Heureka\Controllers\ProductItemIdController;
use Heureka\Models\OrderModel;

/**
 * @method OrderModel get( $object = null )
 * @method OrderModel[] find( array $args = array() )
 * @method OrderModel[] all( array $args = array() )
 */
class OrderRepository extends \HeurekaDeps\Wpify\Model\OrderRepository {

	private SettingsRepository $settings_repository;
	private ProductItemIdController $product_item_id_controller;

	public function __construct(
		ProductItemIdController $product_item_id_controller,
		SettingsRepository $settings_repository
	) {
		$this->settings_repository        = $settings_repository;
		$this->product_item_id_controller = $product_item_id_controller;
	}

	/**
	 * @inheritDoc
	 */
	public function model(): string {
		return OrderModel::class;
	}

	/**
	 * @param array $data
	 *
	 * @return bool|OrderModel|\WC_Order|\WP_Error
	 * @throws \HeurekaDeps\Wpify\Model\Exceptions\NotFoundException
	 * @throws \HeurekaDeps\Wpify\Model\Exceptions\NotPersistedException
	 * @throws \WC_Data_Exception
	 */
	public function create_order_from_heureka_data( array $data ) {
		$billing = array(
			'first_name' => $data['customer']['firstname'],
			'last_name'  => $data['customer']['lastname'],
			'company'    => $data['customer']['company'],
			'email'      => $data['customer']['email'],
			'phone'      => $data['customer']['phone'],
			'address_1'  => $data['customer']['street'],
			'city'       => $data['customer']['city'],
			'postcode'   => $data['customer']['postCode'],
			'country'    => $data['customer']['state'],
		);

		$shipping = array(
			'first_name' => $data['deliveryAddress']['firstname'],
			'last_name'  => $data['deliveryAddress']['lastname'],
			'company'    => $data['deliveryAddress']['company'],
			'address_1'  => $data['deliveryAddress']['street'],
			'city'       => $data['deliveryAddress']['city'],
			'postcode'   => $data['deliveryAddress']['postCode'],
			'country'    => $data['deliveryAddress']['state'],
		);

		$order = wc_create_order();

		if ( is_wp_error( $order ) ) {
			return $order;
		}

		$order->set_address( $billing );
		$order->set_address( $shipping, 'shipping' );

		$order->add_meta_data( '_billing_ic', $data['customer']['ic'] );
		$order->add_meta_data( '_billing_dic', $data['customer']['dic'] );

		foreach ( $data['products'] as $product ) {
			$wc_product    = wc_get_product( $this->product_item_id_controller->get_product_id( $product['id'] ) );
			$tax_rate_info = 0;

			if ( $wc_product ) {
				$tax_rates = \WC_Tax::get_rates( $wc_product->get_tax_class() );
				if ( ! empty( $tax_rates ) ) {
					$tax_rate      = reset( $tax_rates );
					$tax_rate_info = (int) $tax_rate['rate'];
				}
			}

			$product_args = array(
				'subtotal' => $this->get_price_ex_vat( (float) $product['price'], $tax_rate_info ),
				'total'    => $this->get_price_ex_vat( (float) $product['totalPrice'], $tax_rate_info ),
			);

			$order->add_product( $wc_product, $product['count'], $product_args );
		}

		$calculate_tax_for = array(
			'country'  => $data['deliveryAddress']['state'],
			'state'    => '', // Can be set (optional)
			'postcode' => '', // Can be set (optional)
			'city'     => '', // Can be set (optional)
		);

		$shipping = $this->settings_repository->get_shipping_method_by_id( $data['deliveryId'] );
		if ( $shipping ) {
			$item = new \WC_Order_Item_Shipping();
			$item->set_props(
				array(
					'method_title' => $shipping['name'],
					'method_id'    => $shipping['method'],
					'total'        => $this->get_price_ex_vat( (float) $data['deliveryPrice'], (float) $shipping['vat'] ),
				)
			);
			$item->calculate_taxes( $calculate_tax_for );
			$item->save();
			$order->add_item( $item );
		}

		$payment = $this->settings_repository->get_payment_method_by_id( $data['paymentId'] );
		if ( $payment ) {
			$order->set_payment_method( $payment['id'] );
			$order->set_payment_method_title( $payment['name'] );

			if ( $data['paymentPrice'] && $shipping['vat'] ) {
				$fee = new \WC_Order_Item_Fee();
				$fee->set_props( array(
					'name'  => $payment['name'],
					'total' => $this->get_price_ex_vat( (float) $data['paymentPrice'], (float) $shipping['vat'] ),
				) );
				$fee->calculate_taxes( $calculate_tax_for );
				$fee->save();
				$order->add_item( $fee );
			}
		}
		if ( $data['note'] ) {
			$order->set_customer_note( $data['note'] );
		}

		$order->calculate_totals();

		$order              = $this->get( $order->get_id() );
		$order->_heureka_id = $data['heureka_id'];

		return $this->save( $order );
	}

	/**
	 * Get shipping vat
	 *
	 * @param float $price Price.
	 * @param float $vat   VAT.
	 *
	 * @return int|float
	 */
	private function get_shipping_vat( float $price, float $vat ) {
		if ( 0.0 !== $vat ) {
			return $price * ( $vat / 100 );
		}

		return 0;
	}

	/**
	 * Get price ex vat
	 *
	 * @param float $price Price.
	 * @param float $vat   VAT.
	 *
	 * @return int|float
	 */
	private function get_price_ex_vat( float $price, float $vat ) {
		if ( 0.0 !== $vat ) {
			return $price / ( 100 + $vat ) * 100;
		}

		return $price;
	}
}
