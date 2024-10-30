<?php

namespace Heureka\Traits;

use Heureka\Repositories\ProductRepository;
use HeurekaDeps\Wpify\Model\Product;
use WC_Product;

trait ProductDataTrait {

	private $feed_settings;

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_title( $product, $parent_product = null ) {
		if ( $product->get_meta( '_heureka_product_title', true ) ) {
			return $product->get_meta( '_heureka_product_title', true );
		} else if ( $parent_product && $this->get_product_setting( 'title', $parent_product ) ) {
			return $this->get_product_setting( 'title', $parent_product );
		} else if ( ! $parent_product && $this->get_product_setting( 'title', $product ) ) {
			return $this->get_product_setting( 'title', $product );
		}

		return $this->get_product_setting( 'title', $product ) ?: $product->get_name();
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_name( $product, $parent_product = null ) {
		if ( $product->get_meta( '_heureka_product_name', true ) ) {
			return $product->get_meta( '_heureka_product_name', true );
		} else if ( $parent_product && $this->get_product_setting( 'name', $parent_product ) ) {
			return $this->get_product_setting( 'name', $parent_product );
		} else if ( ! $parent_product && $this->get_product_setting( 'name', $product ) ) {
			return $this->get_product_setting( 'name', $product );
		}

		return $this->get_product_setting( 'name', $product ) ?: $product->get_name();
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_description( $product, $parent_product = null ) {
		if ( $parent_product && $this->get_product_setting( 'description', $parent_product ) ) {
			return $this->get_product_setting( 'description', $parent_product );
		} else if ( ! $parent_product && $this->get_product_setting( 'description', $product ) ) {
			return $this->get_product_setting( 'description', $product );
		} else if ( $parent_product ) {
			return $product->get_description() ?: $parent_product->get_description();
		}

		return $product->get_description();
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_item_alternative_image_ids( $product ) {
		return $this->get_product_setting( 'alternative_images', $product );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_video_url( $product ) {
		return $this->get_product_setting( 'video_url', $product ) ?: false;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_heureka_cpc( $product ) {
		$cpc = (string) str_replace( '.', ',', $this->get_product_setting( 'heureka_cpc', $product ) );

		return ( $cpc !== '' && $cpc !== '0' ) ? $cpc : false;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_product_number( $product, $parent_product = null ) {
		if ( $product->get_meta( '_heureka_product_number', true ) ) {
			return $product->get_meta( '_heureka_product_number', true );
		}

		return $this->get_product_setting( 'product_number', $parent_product ?: $product ) ?: false;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_isbn( $product ) {
		return $this->get_product_setting( 'isbn', $product ) ?: false;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_gift( $product ) {
		return $this->get_product_setting( 'gift', $product ) ?: false;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_extended_warranty( $product ) {
		return $this->get_product_setting( 'extended_warranty', $product );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_condition( $product ) {
		return $this->get_product_setting( 'condition', $product ) ?: 'new';
	}


	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_url( $product ) {
		return $product->get_permalink();
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_image_url( $product, $parent_product = false ) {
		$image_id = (int) $this->get_product_setting( 'main_image', $product );

		if ( $image_id === 0 ) {
			$image_id = $product->get_image_id();
		}
		if ( ! $image_id && $parent_product ) {
			$image_id = $parent_product->get_image_id();
		}

		return $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
	public function get_item_exclude( $product ) {
		return $this->get_product_setting( 'exclude', $product );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_item_accessory( $product ) {
		return $this->get_product_setting( 'accessory', $product );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_item_voucher( $product ) {
		return $this->get_product_setting( 'voucher', $product );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_item_special_services( $product ) {
		return $this->get_product_setting( 'special_services', $product );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return array
	 */
	public function get_item_params( $product ) {
		return $this->get_product_setting( 'params', $product );
	}

	/**
	 * @param WC_Product $product
	 * @param WC_Product $parent_product
	 *
	 * @return mixed
	 */
	public function get_item_id( $product, $parent_product = null ) {
		$item_id = '';
		if ( $this->settings_repository->get_feed_setting( 'item_id_custom_field' ) ) {
			$item_id = get_post_meta( $product->get_id(), $this->settings_repository->get_feed_setting( 'item_id_custom_field' ), true );
		}
		if ( ! $item_id && $parent_product && $this->settings_repository->get_feed_setting( 'item_id_custom_field' ) ) {
			$item_id = get_post_meta( $parent_product->get_id(), $this->settings_repository->get_feed_setting( 'item_id_custom_field' ), true );
		}
		if ( ! $item_id && $parent_product ) {
			$item_id = $product->get_meta( '_heureka_product_id', true );
		}
		if ( ! $item_id ) {
			$item_id = $this->get_product_setting( 'id', $product );
		}
		if ( ! $item_id ) {
			$item_id = $product->get_id();
		}

		return $item_id;
	}

	public function get_feeds_settings( $product ) {
		return get_post_meta( $product->get_id(), '_heureka_feed', true ) ?: array();
	}

	public function get_product_setting( $setting, $product ) {
		$settings = $this->get_feeds_settings( $product );

		return $settings[ $setting ] ?? '';
	}

	/**
	 * @param WC_Product $product
	 * @param null $parent_product
	 */
	public function get_item_gallery_images_urls( $product, $parent_product = null ) {
		$ids = $product->get_gallery_image_ids();
		if ( empty( $ids ) && $parent_product ) {
			$ids = $parent_product->get_gallery_image_ids();
		}
		if ( empty( $ids ) ) {
			return array();
		}
		$urls = array();
		foreach ( $ids as $id ) {
			/**
			 * @var int $id
			 */
			$urls[] = wp_get_attachment_image_url( $id, 'full' );
		}

		return $urls;
	}

	/**
	 * @param WC_Product $product
	 * @param WC_Product $parent_product
	 *
	 * @return string
	 */
	public function get_item_ean( $product, $parent_product = null ) {
		$ean = '';
		if ( $this->settings_repository->get_feed_setting( 'ean_custom_field' ) ) {
			$ean = get_post_meta( $product->get_id(), $this->settings_repository->get_feed_setting( 'ean_custom_field' ), true );
			if ( ! $ean && $parent_product ) {
				$ean = get_post_meta( $parent_product->get_id(), $this->settings_repository->get_feed_setting( 'ean_custom_field' ), true );
			}
		}
		if ( ! $ean && $parent_product ) {
			$ean = $product->get_meta( '_heureka_product_ean', true );
			if ( ! $ean && $this->get_product_setting( 'name', $parent_product ) ) {
				$ean = $this->get_product_setting( 'ean', $parent_product );
			}
		} else if ( ! $ean && $this->get_product_setting( 'name', $product ) ) {
			$ean = $this->get_product_setting( 'ean', $product );
		}
		if ( ! $ean ) {
			$ean = substr( $product->get_sku(), 0, 14 );
		}

		return $ean;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return mixed
	 */
	public function get_item_brand( $product ) {
		$brand = $this->get_product_setting( 'brand', $product );
		if ( ! $brand ) {
			$value = $this->settings_repository->get_feed_setting( 'brand' );
			if ( $value ) {
				$type = $this->settings_repository->get_feed_setting( 'brand_type' );
				if ( 'text' === $type ) {
					$brand = $value;
				} elseif ( 'custom_field' === $type ) {
					$brand = get_post_meta( $product->get_id(), $type, true );
				} elseif ( 'taxonomy' === $type ) {
					$terms = wp_get_post_terms( $product->get_id(), $value );
					if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$brand = $terms[0]->name;
					}
				}
			}
		}

		return $brand;
	}

	public function get_item_attributes( $product ): array {
		$attributes = array();
		foreach ( $product->get_attributes() as $tax => $attribute ) {
			$attributes [] = array(
				'label' => wc_attribute_label( $tax ),
				'value' => $product->get_attribute( $tax ),
			);
		}

		return $attributes;
	}

	public function get_item_price_vat_inc( $product ) {
		return wc_get_price_including_tax( $product );
	}

	public function get_item_price_vat_ex( $product ) {
		return wc_get_price_excluding_tax( $product );
	}

	/**
	 * @param float $id Product id
	 * @param string $country
	 *
	 * @return float|null
	 */
	public function get_item_vat_rate( float $id, string $country = 'CZ' ): ?float {
		$product_repository = new ProductRepository();
		$product            = $product_repository->get( $id );

		return $product->get_vat_rate( $country );
	}
}
