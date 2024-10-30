<?php

namespace Heureka\Settings;

use Heureka\Abstracts\AbstractSettings;
use Heureka\Enums\DeliveryTypes;
use Heureka\Enums\OrderStatuses;
use Heureka\Enums\PaymentTypes;
use Heureka\Enums\StoreTypes;
use Heureka\Managers\ApiManager;
use Heureka\Settings;
use HeurekaDeps\Hcapi\Codes\StoreType;

class MarketplaceSettings extends AbstractSettings {

	public function setup() {
		if ( ! $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_MARKETPLACE ) ) {
			return;
		}

		$api_endpoints = array(
			'',
			'products/availability',
			'payment/delivery',
			'order/send',
			'order/status',
			'order/cancel',
			'payment/status',
		);

		$api_urls_info = [];
		foreach ( $api_endpoints as $endpoint ) {
			$api_urls_info[] = sprintf( __( 'E-shop API %s URL: <code style="-webkit-user-select: all;user-select: all;">%s/wp-json/%s</code>', 'heureka' ), $endpoint, get_site_url(), ApiManager::PATH . ( $endpoint ? '/' . $endpoint : '' ) );
		}


		$this->wcf->create_woocommerce_settings(
			array(
				'tab'        => array(
					'id'    => 'heureka',
					'label' => __( 'Heureka', 'heureka' ),
				),
				'section'    => array(
					'id'    => Settings::SECTION_MARKETPLACE,
					'label' => __( 'Marketplace', 'heureka' ),
				),
				'page_title' => __( 'Heureka Settings', 'heureka' ),
				'items'      => array(
					array(
						'id'    => Settings::SECTION_MARKETPLACE,
						'type'  => 'group',
						'items' => array(
							array(
								'desc' => implode( '</br>', $api_urls_info ),
								'id'   => 'shop_url',
								'type' => 'title',
							),
							array(
								'title' => __( 'Heureka API URL', 'heureka' ),
								'desc'  => sprintf( __( 'The API URL to connect to Heureka can be found in the <a href="%s" target="_blank">Heureka Marketplace settings</a>', 'heureka' ), __( 'https://sluzby.heureka.cz/n/obchody/kosik-nastaveni/api/', 'heureka' ) ),
								'id'    => 'api_url',
								'type'  => 'url',
							),
							array(
								'title'   => __( 'Stores', 'heureka' ),
								'id'      => 'stores',
								'type'    => 'multi_group',
								'buttons' => array(
									'add'    => __( 'Add store', 'heureka' ),
									'remove' => __( 'Remove store', 'heureka' ),
								),
								'items'   => array(
									array(
										'title' => __( 'Label', 'heureka' ),
										'id'    => 'label',
										'desc'  => __( 'Enter arbitrary label', 'heureka' ),
										'type'  => 'text',
										'async' => true,
									),
									array(
										'title'   => __( 'Type', 'heureka' ),
										'id'      => 'type',
										'type'    => 'select',
										'options' => function () {
											return StoreTypes::get();
										},
										'async'   => true,
									),
									array(
										'title' => __( 'ID', 'heureka' ),
										'desc'  => sprintf( __( 'If you selected Internal or Depot API, enter the Shipper ID that can be found in the <a href="%s" target="_blank">Heureka administration</a>', 'heureka' ), __( 'https://sluzby.heureka.cz/obchody/pobocky/', 'heureka' ) ),
										'id'    => 'id',
										'type'  => 'text',
									),
									array(
										'title'   => __( 'Shipper ID', 'heureka' ),
										'desc'    => sprintf( __( 'If you selected Pickup point, choose the Shipper here.', 'heureka' ) ),
										'id'      => 'shipper_id',
										'type'    => 'select',
										'options' => function () {
											$data = json_decode( wp_remote_retrieve_body( wp_remote_get( 'https://api.heureka.cz/depot-api/v1/delivery-places/getshippers' ) ) );
											if ( ! $data ) {
												return [];
											}

											return array_map( function ( $item ) {
												return [
													'label' => $item->name,
													'value' => $item->shipperId,
												];
											}, $data->shippers );
										},
										'async'   => true,
									),
								),
							),
							array(
								'title'   => __( 'Shipping', 'heureka' ),
								'id'      => 'shipping_methods',
								'type'    => 'multi_group',
								'buttons' => array(
									'add'    => __( 'Add shipping', 'heureka' ),
									'remove' => __( 'Remove shipping', 'heureka' ),
								),
								'items'   => array(
									array(
										'title'   => __( 'WoocCommerce Method', 'heureka' ),
										'id'      => 'method',
										'type'    => 'select',
										'options' => function () {
											$zones            = \WC_Shipping_Zones::get_zones();
											$shipping_methods = array();
											foreach ( $zones as $zone ) {
												$name = $zone['zone_name'];

												foreach ( $zone['shipping_methods'] as $shipping ) {
													/**
													 * @var $shipping \WC_Shipping_Flat_Rate
													 */
													$shipping_methods[] = array(
														'label' => sprintf( '%s: %s', $name, $shipping->get_title() ),
														'value' => $shipping->get_rate_id(),
													);
												}
											}

											return $shipping_methods;
										},
										'async'   => true,
									),
									array(
										'title' => __( 'ID', 'heureka' ),
										'desc'  => __( 'Enter any unique ID of your choice.', 'heureka' ),
										'id'    => 'id',
										'type'  => 'number',
									),
									array(
										'title'   => __( 'Heureka Type', 'heureka' ),
										'id'      => 'heureka_method',
										'type'    => 'select',
										'options' => function () {
											return DeliveryTypes::get();
										},
										'async'   => true,
									),
									array(
										'title' => __( 'Name', 'heureka' ),
										'desc'  => __( 'Enter name for delivery.', 'heureka' ),
										'id'    => 'name',
										'type'  => 'text',
									),
									array(
										'title' => __( 'Price', 'heureka' ),
										'desc'  => __( 'Enter price for delivery.', 'heureka' ),
										'id'    => 'price',
										'type'  => 'text',
									),
									array(
										'title'   => __( 'VAT', 'heureka' ),
										'desc'    => __( 'Select VAT class for delivery.', 'heureka' ),
										'id'      => 'vat',
										'type'    => 'select',
										'default' => '0',
										'options' => function () {
											if ( wc_tax_enabled() ) {
												$tax_classes = \WC_Tax::get_rates();

												return array_map(
													function ( $item ) {
														return array(
															'label' => $item['label'],
															'value' => (string) $item['rate'],
														);
													},
													$tax_classes
												);
											} else {
												return array(
													array(
														'label' => __( 'E-shop has no taxes allowed', 'heureka' ),
														'value' => '0',
													),
												);
											}
										},
										'async'   => true,
									),
									array(
										'title' => __( 'Description', 'heureka' ),
										'desc'  => __( 'Enter description for delivery.', 'heureka' ),
										'id'    => 'description',
										'type'  => 'text',
									),
									array(
										'title'   => __( 'Store', 'heureka' ),
										'desc'    => __( 'Select store defined above.', 'heureka' ) . ' ' . __( 'Save changes if you have just added them.', 'heureka' ),
										'id'      => 'store',
										'type'    => 'select',
										'options' => function () {
											$stores = $this->settings_repository->get_marketplace_setting( 'stores' ) ?: array();

											return array_map(
												function ( $item ) {
													return array(
														'value' => $this->get_store_id( $item ),
														'label' => $item['label'],
													);
												},
												$stores
											);
										},
										'async'   => true,
									),
								),
							),
							array(
								'title'   => __( 'Payment', 'heureka' ),
								'id'      => 'payment_methods',
								'type'    => 'multi_group',
								'buttons' => array(
									'add'    => __( 'Add payment', 'heureka' ),
									'remove' => __( 'Remove pament', 'heureka' ),
								),
								'items'   => array(
									array(
										'title'   => __( 'WoocCommerce Method', 'heureka' ),
										'id'      => 'method',
										'type'    => 'select',
										'options' => function () {
											$gateways = array();
											foreach ( WC()->payment_gateways()->payment_gateways() as $key => $gateway ) {
												$gateways[] = array(
													'label' => $gateway->title,
													'value' => $key,
												);
											}

											return $gateways;
										},
										'async'   => true,
									),
									array(
										'title' => __( 'ID', 'heureka' ),
										'desc'  => __( 'Enter any unique ID of your choice.', 'heureka' ),
										'id'    => 'id',
										'type'  => 'number',
									),
									array(
										'title'   => __( 'Heureka Type', 'heureka' ),
										'id'      => 'heureka_method',
										'type'    => 'select',
										'options' => function () {
											return PaymentTypes::get();
										},
										'async'   => true,
									),
									array(
										'title' => __( 'Name', 'heureka' ),
										'desc'  => __( 'Enter name for payment.', 'heureka' ),
										'id'    => 'name',
										'type'  => 'text',
									),
									array(
										'title' => __( 'Price', 'heureka' ),
										'desc'  => __( 'Enter price for payment.', 'heureka' ),
										'id'    => 'price',
										'type'  => 'text',
									),
								),
							),
							array(
								'title'   => __( 'Default Heureka payment', 'heureka' ),
								'desc'    => __( 'Payment method used if Heureka returns unknown payment method.', 'heureka' ) . ' ' . __( 'Save changes if you have just added them.', 'heureka' ),
								'id'      => 'default_payment',
								'type'    => 'select',
								'options' => function () {
									$result = array();
									foreach ( $this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'payment_methods', array() ) as $item ) {
										$result[] = array(
											'label' => $item['name'],
											'value' => $item['id'],
										);
									}

									return $result;
								},
								'async'   => true,
							),
							array(
								'title'   => __( 'Shipping / Payment combinations', 'heureka' ),
								'id'      => 'shipping_payment_bindings',
								'type'    => 'multi_group',
								'buttons' => array(
									'add'    => __( 'Add combination', 'heureka' ),
									'remove' => __( 'Remove combination', 'heureka' ),
								),
								'items'   => array(
									array(
										'title'   => __( 'Shipping', 'heureka' ),
										'desc'    => __( 'Select shipping defined above.', 'heureka' ) . ' ' . __( 'Save changes if you have just added them.', 'heureka' ),
										'id'      => 'shipping',
										'type'    => 'select',
										'options' => function () {
											$result = array();
											foreach ( $this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'shipping_methods', array() ) as $item ) {
												$result[] = array(
													'label' => $item['name'],
													'value' => $item['id'],
												);
											}

											return $result;
										},
										'async'   => true,
									),
									array(
										'title'   => __( 'Payment', 'heureka' ),
										'desc'    => __( 'Select payment defined above.', 'heureka' ) . ' ' . __( 'Save changes if you have just added them.', 'heureka' ),
										'id'      => 'payment',
										'type'    => 'select',
										'options' => function () {
											$result = array();
											foreach ( $this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'payment_methods', array() ) as $item ) {
												$result[] = array(
													'label' => $item['name'],
													'value' => $item['id'],
												);
											}

											return $result;
										},
										'async'   => true,
									),
									array(
										'title' => __( 'ID', 'heureka' ),
										'desc'  => __( 'Enter any unique ID of your choice.', 'heureka' ),
										'id'    => 'id',
										'type'  => 'number',
									),

								),
							),
							array(
								'title'   => __( 'Statuses', 'heureka' ),
								'id'      => 'statuses',
								'type'    => 'multi_group',
								'buttons' => array(
									'add'    => __( 'Add status', 'heureka' ),
									'remove' => __( 'Remove status', 'heureka' ),
								),
								'items'   => array(
									array(
										'title'   => __( 'WooCommerce Status', 'heureka' ),
										'id'      => 'status',
										'type'    => 'select',
										'options' => function () {
											$result = array();
											foreach ( wc_get_order_statuses() as $key => $status ) {
												$result[] = array(
													'label' => $status,
													'value' => $key,
												);
											}

											return $result;
										},
										'async'   => true,
									),
									array(
										'title'   => __( 'Heureka Status', 'heureka' ),
										'id'      => 'heureka_status',
										'type'    => 'select',
										'options' => function () {
											return OrderStatuses::get();
										},
										'async'   => true,
									),
								),
							),
							array(
								'title'   => __( 'Paid order - statuses', 'heureka' ),
								'desc'    => __( 'Select all order statuses that are paid.', 'heureka' ),
								'id'      => 'paid_order_statuses',
								'type'    => 'multi_select',
								'options' => function () {
									$result = array();
									foreach ( wc_get_order_statuses() as $key => $status ) {
										$result[] = array(
											'label' => $status,
											'value' => $key,
										);
									}

									return $result;
								},
								'async'   => true,
							),
							array(
								'id'    => 'invoice_path_custom_field',
								'type'  => 'text',
								'label' => __( 'Invoice path custom field', 'heureka' ),
								'desc'  => __( 'Custom order field key with invoice path.', 'heureka' ),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Get store ID
	 *
	 * @param array $store
	 *
	 * @return string
	 */
	public function get_store_id( array $store ): string {
		if ( $store['type'] === StoreType::STORE_TYPE_INTERNAL ) {
			return $store['id'];
		}

		return $store['shipper_id'];
	}
}
