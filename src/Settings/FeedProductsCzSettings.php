<?php

namespace Heureka\Settings;

use Heureka\Abstracts\AbstractSettings;
use Heureka\Enums\DeliveryMethods;
use Heureka\Feed\FeedCategories;
use Heureka\Repositories\SettingsRepository;
use Heureka\Settings;
use HeurekaDeps\Wpify\CustomFields\CustomFields;

class FeedProductsCzSettings extends AbstractSettings {

	protected $feed_categories;

	public function __construct( CustomFields $wcf, SettingsRepository $settings_repository, FeedCategories $feed_categories ) {
		parent::__construct( $wcf, $settings_repository );
		$this->feed_categories = $feed_categories;
	}

	public function setup() {
		add_action( 'init', array( $this, 'register_settings' ) );
	}

	public function register_settings() {
		if ( ! $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_FEEDS ) ) {
			return [];
		}

		$load = ( ! empty( $_GET['section'] ) && $_GET['section'] === Settings::SECTION_FEED_PRODUCTS_CZ ) || ( wp_is_json_request() && strpos( $_SERVER['REQUEST_URI'], 'wcf' ) !== false );

		if ( ! $load ) {
			$items = [];
		} else {
			$this->feed_categories->update_heureka_categories();
			$items = array();

			$categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);

			if ( ! is_wp_error( $categories ) ) {
				foreach ( $categories as $category ) {
					$items[] = array(
						'id'          => 'category_' . $category->term_id,
						'label'       => sprintf( __( 'Heureka shopping category for %s', 'heureka' ), $category->name ),
						'description' => sprintf( __( 'Category full path: %s', 'heureka' ), $this->get_category_full_path( $category ) ),
						'type'        => 'select',
						'options'     => function ( $args ) {
							return $this->feed_categories->get_options( $args );
						},
						'list_id'     => 'heureka_cz_category_list',
						'async'       => true,
					);
				}
			}

			$items = array_merge( $items, array(
				array(
					'id'    => 'delivery_methods_title',
					'type'  => 'title',
					'label' => __( 'Delivery methods', 'heureka' ),
					'desc'  => __( 'Select the delivery methods and prices', 'heureka' ),
				),
				array(
					'id'      => 'delivery_methods',
					'type'    => 'group',
					'label'   => __( 'Delivery methods', 'heureka' ),
					'multi'   => true,
					'min'     => 0,
					'buttons' => array(
						'add'    => __( 'Add method', 'heureka' ),
						'remove' => __( 'Remove method', 'heureka' ),
					),
					'items'   => array(
						array(
							'id'      => 'method',
							'type'    => 'select',
							'options' => DeliveryMethods::get(),
							'label'   => __( 'Delivery method', 'heureka' ),
							'desc'    => __( 'Select delivery method', 'heureka' ),
						),
						array(
							'id'    => 'price',
							'type'  => 'text',
							'label' => __( 'Price', 'heureka' ),
							'desc'  => __( 'Enter price for delivery.', 'heureka' ),
						),
						array(
							'id'    => 'price_cod',
							'type'  => 'text',
							'label' => __( 'Price COD', 'heureka' ),
							'desc'  => __( 'Enter price for delivery with COD.', 'heureka' ),
						),
					),
				),
			) );

		}


		$this->wcf->create_woocommerce_settings(
			array(
				'tab'     => array(
					'id'    => 'heureka',
					'label' => __( 'Heureka', 'heureka' ),
				),
				'section' => array(
					'id'    => Settings::SECTION_FEED_PRODUCTS_CZ,
					'label' => __( 'Product feed CZ settings', 'heureka' ),
				),
				'items'   => array(
					array(
						'id'    => 'map_categories_title',
						'type'  => 'title',
						'label' => __( 'Map categories', 'heureka' ),
						'desc'  => __( 'Map WooCommerce categories to Heureka categories', 'heureka' ),
					),
					array(
						'type'  => 'group',
						'id'    => Settings::SECTION_FEED_PRODUCTS_CZ,
						'items' => $items,
					),
				),
			)
		);
	}

}
