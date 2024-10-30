<?php

namespace Heureka\PostTypes;

use Heureka\Repositories\ProductItemIdRepository;
use Heureka\Repositories\SettingsRepository;
use HeurekaDeps\Wpify\PostType\AbstractBuiltinPostType;

class ProductPostType extends AbstractBuiltinPostType {

	const KEY = 'product';
	private ProductItemIdRepository $product_item_id_repository;
	private SettingsRepository $settings_repository;

	public function __construct(
		ProductItemIdRepository $product_item_id_repository,
		SettingsRepository $settings_repository
	) {
		parent::__construct();

		$this->product_item_id_repository = $product_item_id_repository;
		$this->settings_repository        = $settings_repository;

		add_action( 'wp_after_insert_post', [ $this, 'save_product_item_id' ], 10, 2 );
	}

	public function setup() {
	}

	public function get_post_type_key(): string {
		return self::KEY;
	}

	public function save_product_item_id( $post_id, $post ) {
		if ( $post->post_type === self::KEY ) {
			$item_id = $this->get_item_id( $post_id );
			if ( $item_id !== '' ) {
				$product_item_id = $this->product_item_id_repository->get_by_product_id( $post_id );
				if ( is_null( $product_item_id ) ) {
					$product_item_id             = $this->product_item_id_repository->create();
					$product_item_id->product_id = $post_id;
					$product_item_id->meta_key   = $this->get_meta_key();
				}
				$product_item_id->item_id = $item_id;
				$this->product_item_id_repository->save( $product_item_id );
			} else {
				$product_item_id = $this->product_item_id_repository->get_by_product_id( $post_id );

				if ( ! is_null( $product_item_id ) ) {
					$this->product_item_id_repository->delete( $product_item_id );
				}
			}
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	private function get_item_id( $post_id ) {
		if ( $this->settings_repository->get_feed_setting( 'item_id_custom_field' ) ) {
			return get_post_meta( $post_id, $this->settings_repository->get_feed_setting( 'item_id_custom_field' ), true );
		}

		$product_settings = get_post_meta( $post_id, '_heureka_feed', true ) ?: array();

		return ( is_array( $product_settings ) && ( ! empty( $product_settings ) ) ) ? $product_settings['id'] : '';
	}

	/**
	 * @return string
	 */
	private function get_meta_key(): string {
		if ( $this->settings_repository->get_feed_setting( 'item_id_custom_field' ) ) {
			return $this->settings_repository->get_feed_setting( 'item_id_custom_field' );
		}

		return '_heureka_feed';
	}

}
