<?php

namespace Heureka\HeurekaApi\In;

use Heureka\Repositories\SettingsRepository;
use Heureka\Settings;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class PaymentDelivery {

	private SettingsRepository $settings_repository;

	public function __construct( SettingsRepository $settings_repository ) {
		$this->settings_repository = $settings_repository;
	}

	/**
	 * Obtains data from Heureka, process them and returns response from shop for PRODUCTS/AVAILABILITY
	 *
	 * @param array $receive_data
	 *
	 * @return array
	 */
	public function handle_request( array $receive_data ): array {
		return array(
			'transport' => array_map(
				function ( $item ) {
					$data = array(
						'id'          => (int) $item['id'],
						'type'        => (int) $item['heureka_method'],
						'name'        => $item['name'],
						'price'       => floatval( $item['price'] ),
						'description' => isset( $item['description'] ) ? $item['description'] : '',
					);

					if ( ! empty( $item['store'] ) ) {
						$stores = $this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'stores' );
						foreach ( $stores as $store ) {
							if ( $store['id'] === $item['store'] ) {
								$store_data['type'] = 1;
								$store_data['id']   = (int) $store['id'];
								$data['store']      = $store_data;
								break;
							} elseif ( $store['shipper_id'] === $item['store'] ) {
								$store_data['type'] = 3;
								$store_data['id']   = (int) $store['shipper_id'];
								$data['store']      = $store_data;
							}
						}
					}

					return $data;
				},
				$this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'shipping_methods', array() )
			),

			'payment' => array_map(
				function ( $item ) {
					return array(
						'id'    => (int) $item['id'],
						'type'  => (int) $item['heureka_method'],
						'price' => floatval( $item['price'] ),
						'name'  => $item['name'],
					);
				},
				$this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'payment_methods', array() )
			),
			'binding' => array_map(
				function ( $item ) {
					return array(
						'id'          => (int) $item['id'],
						'transportId' => (int) $item['shipping'],
						'paymentId'   => (int) $item['payment'],
					);
				},
				$this->settings_repository->get_option( Settings::SECTION_MARKETPLACE, 'shipping_payment_bindings', array() )
			),
		);
	}
}
