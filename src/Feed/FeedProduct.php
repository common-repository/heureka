<?php

namespace Heureka\Feed;

use Heureka\Repositories\SettingsRepository;
use Heureka\Traits\ProductDataTrait;
use WC_Product;
use \WC_Product_Variable;
use Heureka\Abstracts\AbstractFeed;

abstract class FeedProduct extends AbstractFeed {

	use ProductDataTrait;

	private SettingsRepository $settings_repository;

	public function __construct( SettingsRepository $settings_repository ) {
		$this->settings_repository = $settings_repository;
		parent::__construct();
	}

	abstract public function global_settings_section();

	abstract public function language_suffix();

	abstract public function get_country_code(): string;

	/**
	 * @param array $products
	 *
	 * @return array
	 */
	public function data( array $products ): array {
		$items = array();

		$exclude_out_of_stock = $this->settings_repository->get_feed_setting( 'exclude_outofstock' );
		$exclude_without_category = $this->settings_repository->get_feed_setting( 'exclude_without_category' );

		foreach ( $products as $product ) {
			if ( $this->get_item_exclude( $product ) ) {
				continue;
			}

			if ( $exclude_out_of_stock && ! $product->is_in_stock() ) {
				continue;
			}

			if ( $exclude_without_category && ! $this->has_set_category($product) ) {
				continue;
			}

			if ( apply_filters( 'heureka_feed_skip_product', false, $product ) ) {
				continue;
			}

			if ( $product->is_type( 'simple' ) || $product->is_type( 'grouped' ) || $product->is_type( 'external' ) || $product->is_type( 'bundle' ) ) {
				if ( ! $product->get_price() ) {
					continue;
				}

				$items[ '__custom:SHOPITEM:' . $product->get_id() ] = $this->get_data( $product );
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

					$items[ '__custom:SHOPITEM:' . $var->get_id() ] = $this->get_data( $var, $product );
				}
			}
		}

		return $items;
	}


	/**
	 * @param $product WC_Product
	 */
	public function get_data( WC_Product $product, $parent_product = null ) {
		$main_product = ( $parent_product ) ?: $product;
		$data         = array(
			'feed_product_id' => $product->get_id(),
			'ITEM_ID'         => $this->get_item_id( $product, $parent_product ),
			'PRODUCT'         => $this->get_item_title( $product, $parent_product ),
			'PRODUCTNAME'     => $this->get_item_name( $product, $parent_product ),
			'DESCRIPTION'     => array( '_cdata' => $this->get_item_description( $product, $parent_product ) ),
			'URL'             => array( '_cdata' => $this->get_item_url( $product ) ),
			'IMGURL'          => array( '_cdata' => $this->get_item_image_url( $product, $parent_product ) ),
			'PRICE_VAT'       => $this->get_item_price_vat_inc( $product ),
			'DELIVERY_DATE'   => $product->is_in_stock() && ! $product->is_on_backorder() ? $this->settings_repository->get_feed_setting( 'delivery' ) : $this->settings_repository->get_feed_setting( 'delivery_out_of_stock' ),
		);

		$vat = $this->get_item_vat_rate( $product->get_id(), $this->get_country_code() );
		if ( $vat ) {
			$data['VAT'] = $vat . '%';
		}

		$category = $this->get_item_category( $main_product );
		if ( $category ) {
			$data['CATEGORYTEXT'] = $category;
		}

		$alternative_images = $this->get_alternative_images( $main_product );
		if ( ! empty( $alternative_images ) ) {
			$data = array_merge( $data, $alternative_images );
		}

		$video_url = $this->get_item_video_url( $main_product );
		if ( $video_url ) {
			$data['VIDEO_URL'] = $video_url;
		}

		$heureka_cpc = $this->get_item_heureka_cpc( $main_product );
		if ( $heureka_cpc ) {
			$data['HEUREKA_CPC'] = $heureka_cpc;
		}

		$ean = $this->get_item_ean( $product, $parent_product );
		if ( $ean ) {
			$data['EAN'] = $ean;
		}

		$product_number = $this->get_item_product_number( $product, $parent_product );
		if ( $product_number ) {
			$data['PRODUCTNO'] = $product_number;
		}

		$isbn = $this->get_item_isbn( $main_product );
		if ( $isbn ) {
			$data['ISBN'] = $isbn;
		}

		$gift = $this->get_item_gift( $main_product );
		if ( $gift ) {
			$data['GIFT'] = $gift;
		}

		$brand = $this->get_item_brand( $main_product );
		if ( $brand ) {
			$data['MANUFACTURER'] = $brand;
		}

		$extended_warranty = $this->get_extended_warranty( $main_product );
		if ( is_array( $extended_warranty ) ) {
			$data['EXTENDED_WARRANTY'] = $extended_warranty;
		}

		$special_services = $this->get_special_services( $main_product );
		if ( ! empty( $special_services ) ) {
			$data = array_merge( $data, $special_services );
		}

		$params = $this->get_params( $product );
		if ( ! empty( $params ) ) {
			$data = array_merge( $data, $params );
		}

		$delivery_methods = $this->get_delivery_methods( $main_product );
		if ( ! empty( $delivery_methods ) ) {
			$data = array_merge( $data, $delivery_methods );
		}

		$accessory = $this->get_accessory( $main_product );
		if ( ! empty( $accessory ) ) {
			$data = array_merge( $data, $accessory );
		}

		$voucher = $this->get_voucher( $main_product );
		if ( ! empty( $voucher ) ) {
			$discount = $voucher['__custom:SALES_VOUCHER:']['discount'];
			unset( $voucher['__custom:SALES_VOUCHER:']['discount'] );
			$data['PRICE_VAT'] = ( $data['PRICE_VAT'] * ( 100 - $discount ) / 100 );
			$data              = array_merge( $data, $voucher );
		}

		if ( $parent_product ) {
			$data['ITEMGROUP_ID'] = $this->get_item_id( $parent_product );
		}

		return apply_filters( 'heureka_feed_item_data', $data, $product, $parent_product );
	}

	public function get_item_category( $product ) {
		$categories      = [];
		$custom_category = $this->get_product_setting( 'category_' . $this->language_suffix(), $product );
		if ( $custom_category ) {
			return $custom_category;
		}

		foreach ( $product->get_category_ids() as $id ) {
			$global_category = $this->settings_repository->get_categories_setting( $this->global_settings_section(), $this->category_key() . $id );
			if ( 'undefined' === $global_category ) {
				continue;
			}

			if ( $global_category ) {
				return $global_category;
			}

			$category     = get_term_by( 'id', $id, 'product_cat' );
			$categories[] = $category->name;
		}

		return implode( ' | ', $categories );
	}

	public function has_set_category( $product ) {
		$custom_category = $this->get_product_setting( 'category_' . $this->language_suffix(), $product );
		if ( $custom_category ) {
			return true;
		}

		foreach ( $product->get_category_ids() as $id ) {
			$global_category = $this->settings_repository->get_categories_setting( $this->global_settings_section(), $this->category_key() . $id );
			if ( 'undefined' === $global_category ) {
				continue;
			}

			if ( $global_category ) {
				return true;
			}
		}

		return false;
	}

	public function get_alternative_images( $product ) {
		$images_ids = $this->get_item_alternative_image_ids( $product );
		$images     = array();
		if ( ! empty( $images_ids ) && is_array( $images_ids ) ) {
			foreach ( $images_ids as $key => $image_id ) {
				$images[ '__custom:IMGURL_ALTERNATIVE:' . $key ] = wp_get_attachment_image_url( $image_id, 'full' );
			}
		}

		return $images;
	}

	public function get_extended_warranty( $product ) {
		$extended_warranty = $this->get_item_extended_warranty( $product );
		if ( ! empty( $extended_warranty ) && is_array( $extended_warranty ) && (int) $extended_warranty['months'] > 0 ) {
			return array(
				'VAL'  => $extended_warranty['months'],
				'DESC' => $extended_warranty['title'],
			);
		}

		return false;
	}

	public function get_accessory( $product ) {
		$product_accessory = $this->get_item_accessory( $product );
		$accessory         = array();
		if ( ! empty( $product_accessory ) && is_array( $product_accessory ) ) {
			foreach ( $product_accessory as $index => $item ) {
				if ( (int) $item['product_id'] > 0 ) {
					$accessory[ '__custom:ACCESSORY:' . $index ] = $this->get_item_id( new \WC_Product( $item['product_id'] ) );
				}
			}
		}

		return $accessory;
	}

	public function get_voucher( $product ) {
		$product_voucher = $this->get_item_voucher( $product );
		$voucher         = array();
		if ( ! empty( $product_voucher ) && is_array( $product_voucher ) ) {
			if ( (int) $product_voucher['discount'] > 0 ) {
				$voucher['__custom:SALES_VOUCHER:'] = array(
					'discount' => $product_voucher['discount'],
					'CODE'     => $product_voucher['code'],
					'DESC'     => $product_voucher['description'],
				);
			}
		}

		return $voucher;
	}

	public function get_special_services( $product ) {
		$product_special_services = $this->get_item_special_services( $product );
		$special_services         = array();

		if ( ! empty( $product_special_services ) && is_array( $product_special_services ) ) {
			foreach ( $product_special_services as $index => $service ) {
				if ( $service['title'] !== '' ) {
					$special_services[ '__custom:SPECIAL_SERVICE:' . $index ] = $service['title'];
				}
			}
		}

		return array_slice( $special_services, 0, 5 );
	}

	public function get_params( $product ) {
		$attributes     = $this->get_item_attributes( $product );
		$product_params = $this->get_item_params( $product );
		$params         = array();

		if ( ! empty( $attributes ) && is_array( $attributes ) ) {
			foreach ( $attributes as $index => $attribute ) {
				$values = explode( ', ', $attribute['value'] );
				foreach ( $values as $subindex => $value ) {
					$params[ '__custom:PARAM:' . $index . '-' . $subindex ] = array(
						'PARAM_NAME' => $attribute['label'],
						'VAL'        => $value,
					);
				}
			}
		}
		if ( ! empty( $product_params ) && is_array( $product_params ) ) {
			foreach ( $product_params as $index => $param ) {
				$params[ '__custom:PARAM:custom-' . $index ] = array(
					'PARAM_NAME' => $param['name'],
					'VAL'        => $param['value'],
				);
			}
		}

		return $params;
	}

	public function get_delivery_methods( $product ) {
		$custom_delivery_methods = $this->get_product_setting( 'delivery_methods', $product );
		$delivery_methods        = ( ! empty( $custom_delivery_methods ) && is_array( $custom_delivery_methods ) ) ? $custom_delivery_methods : $this->settings_repository->get_categories_setting( $this->global_settings_section(), 'delivery_methods' );

		$methods = array();
		if ( ! empty( $delivery_methods ) && is_array( $delivery_methods ) ) {
			foreach ( $delivery_methods as $key => $method ) {
				$methods[ '__custom:DELIVERY:' . $key ] = array(
					'DELIVERY_ID'        => $method['method'],
					'DELIVERY_PRICE'     => $method['price'],
					'DELIVERY_PRICE_COD' => $method['price_cod'],
				);
			}
		}

		return $methods;
	}

	public function get_root_name(): string {
		return 'SHOP';
	}

	public function products_per_run(): int {
		return $this->settings_repository->get_feed_setting( 'products_per_run' ) ?: 100;
	}

	public function category_key() {
		return 'category_';
	}
}
