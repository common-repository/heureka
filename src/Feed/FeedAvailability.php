<?php

namespace Heureka\Feed;

use Heureka\Repositories\SettingsRepository;
use Heureka\Traits\ProductDataTrait;
use WC_Product;
use \WC_Product_Variable;
use Heureka\Abstracts\AbstractFeed;

class FeedAvailability extends AbstractFeed {

	use ProductDataTrait;

	const FEED_NAME = 'heureka_availability';
	const FEED_TITLE = 'Heureka Availability';

	private $delivery_methods = array();
	private $google_categories = array();
	private SettingsRepository $settings_repository;

	public function __construct( SettingsRepository $settings_repository ) {
		$this->settings_repository = $settings_repository;
		parent::__construct();
	}

	/**
	 * @param array $products
	 *
	 * @return array
	 */
	public function data( array $products ): array {
		$items = array();

		$exclude_out_of_stock = $this->settings_repository->get_feed_setting( 'exclude_outofstock' );

		foreach ( $products as $product ) {
			if ( $exclude_out_of_stock && ! $product->is_in_stock() ) {
				continue;
			}

			if ( apply_filters( 'heureka_feed_skip_product', false, $product ) ) {
				continue;
			}

			if ( $product->is_type( 'simple' ) || $product->is_type( 'grouped' ) || $product->is_type( 'external' ) || $product->is_type( 'bundle' ) ) {
				if ( ! $product->get_price() ) {
					continue;
				}

				$data = array_merge(
					array( '_attributes' => array( 'id' => $this->get_item_id( $product ) ) ),
					$this->get_data( $product )
				);

				$items[ '__custom:item:' . $product->get_id() ] = $data;
			} elseif ( $product->is_type( 'variable' ) ) {
				/**
				 * @var $product WC_Product_Variable
				 */
				foreach ( $product->get_available_variations() as $variation ) {
					$var = wc_get_product( $variation['variation_id'] );
					if ( ! $var->get_price() ) {
						continue;
					}

					if ( $exclude_out_of_stock && ! $var->is_in_stock() ) {
						continue;
					}

					$data = array_merge(
						array( '_attributes' => array( 'id' => $this->get_item_id( $var ) ) ),
						$this->get_data( $var, $product )
					);

					$items[ '__custom:item:' . $var->get_id() ] = $data;
				}
			}
		}

		return $items;
	}


	/**
	 * @param $product WC_Product
	 */
	public function get_data( WC_Product $product, $parent_product = null ) {
		$deadline_timestamp = $this->get_order_deadline_timestamp();
		$data               = array(
			'feed_product_id' => $product->get_id(),
			'delivery_time'   => array(
				'_attributes' => array( 'orderDeadline' => date( 'Y-m-d H:i', $deadline_timestamp ) ),
				'_value'      => date( 'Y-m-d H:i', strtotime( ' +' . $this->get_delivery_day_offset( $product ) . ' days', $deadline_timestamp ) ),
			),
		);

		if ( (int) $product->get_stock_quantity() > 0 ) {
			$data['stock_quantity'] = $product->get_stock_quantity();
		}

		return apply_filters( 'heureka_availability_item_data', $data, $product, $parent_product );
	}

	private function get_order_deadline_timestamp() {
		$closing_hour      = $this->settings_repository->get_feed_setting( 'closing_hour' );
		$next_day_distance = $this->get_next_business_day_distance( $closing_hour );

		return strtotime( date( 'Y-m-d', strtotime( $next_day_distance . ' day' ) ) . ' ' . $closing_hour );
	}

	private function get_next_business_day_distance( $closing_hour ) {
		$offset             = ( strtotime( $closing_hour ) > time() ) ? 1 : 0;
		$current_day_number = date( 'w', strtotime( $offset . ' day' ) );
		$business_days      = $this->settings_repository->get_feed_setting( 'business_days' );
		$day_distances      = array();

		if ( ! $business_days || in_array( $current_day_number, $business_days ) ) {
			return $offset;
		}

		foreach ( $business_days as $day ) {
			$distance              = ( ( $day - $current_day_number ) > 0 ) ?: ( $day - $current_day_number + 7 );
			$day_distances[ $day ] = (int) $distance;
		}

		return min( $day_distances );
	}

	private function get_delivery_day_offset( $product ) {
		$delivery = (int) ( $product->is_in_stock() && ! $product->is_on_backorder() ? $this->settings_repository->get_feed_setting( 'delivery' ) : $this->settings_repository->get_feed_setting( 'delivery_out_of_stock' ) );

		return ( $delivery > 0 ) ?: ( $delivery + 1 );
	}

	public function get_root_name(): string {
		return 'item_list';
	}

	public function feed_name() {
		return self::FEED_NAME;
	}

	public function feed_title() {
		return self::FEED_TITLE;
	}

	public function products_per_run(): int {
		return $this->settings_repository->get_feed_setting( 'products_per_run' ) ?: 100;
	}
}
