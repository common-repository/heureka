<?php

namespace Heureka\Settings;

use Heureka\Enums\DeliveryMethods;
use Heureka\Abstracts\AbstractSettings;
use Heureka\Feed\FeedCategories;
use Heureka\PostTypes\ProductPostType;
use Heureka\Repositories\SettingsRepository;
use HeurekaDeps\Wpify\CustomFields\CustomFields;

class ProductSettings extends AbstractSettings {

	private $feed_categories;

	public function __construct( CustomFields $wcf, SettingsRepository $settings_repository, FeedCategories $feed_categories ) {
		parent::__construct( $wcf, $settings_repository );
		$this->feed_categories = $feed_categories;
	}

	public function setup() {
		if ( ! $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_FEEDS ) ) {
			return;
		}

		add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'add_custom_variations_fields' ], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'save_custom_variation_fields' ], 10, 2 );

		$this->wcf->create_product_options(
			array(
				'tab'   => array(
					'id'       => 'heureka',
					'label'    => __( 'Heureka', 'heureka' ),
					'priority' => 100,
					'class'    => array(),
				),
				'items' => array(
					array(
						'id'    => '_heureka_feed',
						'type'  => 'group',
						'items' => array(
							array(
								'id'    => 'exclude',
								'title' => __( 'Exclude from feed', 'heureka' ),
								'desc'  => __( 'Check for exclude this product from feed.', 'heureka' ),
								'type'  => 'toggle',
							),
							array(
								'id'    => 'id',
								'title' => __( 'Custom feed ID', 'heureka' ),
								'desc'  => __( 'Overwrite ITEM_ID. Product ID or custom field from Global Feed settings is used as default.', 'heureka' ),
								'type'  => 'text',
							),
							array(
								'id'    => 'title',
								'title' => __( 'Product title', 'heureka' ),
								'desc'  => __( 'Product name + information about product distribution or personal collection. Product name is used as default.', 'heureka' ),
								'type'  => 'text',
							),
							array(
								'id'    => 'name',
								'title' => __( 'Product name', 'heureka' ),
								'desc'  => __( 'Overwrite default product name', 'heureka' ),
								'type'  => 'text',
							),
							array(
								'id'    => 'ean',
								'title' => __( 'EAN', 'heureka' ),
								'desc'  => __( 'Set or overwrite global EAN code from custom field.', 'heureka' ),
								'type'  => 'text',
							),
							array(
								'id'    => 'brand',
								'title' => __( 'Brand', 'heureka' ),
								'desc'  => __( 'Overwrite global brand settings from Feed settings', 'heureka' ),
								'type'  => 'text',
							),
							array(
								'id'      => 'category_cs',
								'type'    => 'select',
								'title'   => __( 'Category CZ', 'heureka' ),
								'options' => function ( $args ) {
									return $this->feed_categories->get_options( $args );
								},
								'async'   => true,
							),
							array(
								'id'      => 'category_sk',
								'type'    => 'select',
								'title'   => __( 'Category SK', 'heureka' ),
								'options' => function ( $args ) {
									return $this->feed_categories->get_options( $args, 'sk' );
								},
								'async'   => true,
							),
							array(
								'id'      => 'delivery_methods',
								'type'    => 'group',
								'title'   => __( 'Delivery methods', 'heureka' ),
								'desc'    => __( 'Overwrite global delivery methods settings', 'heureka' ),
								'multi'   => true,
								'min'     => 0,
								'buttons' => array(
									'add'    => __( 'Add method', 'heureka' ),
									'remove' => __( 'Remove method', 'heureka' ),
								),
								'default' => [],
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
							array(
								'id'      => 'main_image',
								'title'   => __( 'Main image', 'heureka' ),
								'type'    => 'attachment',
								'default' => 0,
							),
							array(
								'id'      => 'alternative_images',
								'title'   => __( 'Alternative images', 'heureka' ),
								'type'    => 'multi_attachment',
								'default' => [],
							),
							array(
								'id'    => 'video_url',
								'type'  => 'url',
								'title' => __( 'Video URL', 'heureka' ),
							),
							array(
								'id'    => 'description',
								'type'  => 'textarea',
								'title' => __( 'Description', 'heureka' ),
								'desc'  => __( 'Overwrite default product description.', 'heureka' ),
							),
							array(
								'id'    => 'product_number',
								'type'  => 'text',
								'title' => __( 'Product number', 'heureka' ),
								'desc'  => __( 'Set product number for PRODUCTNO parameter.', 'heureka' ),
							),
							array(
								'id'    => 'isbn',
								'type'  => 'text',
								'title' => __( 'ISBN', 'heureka' ),
								'desc'  => __( 'An alphanumeric code designed to uniquely identify book editions.', 'heureka' ),
							),
							array(
								'id'    => 'heureka_cpc',
								'type'  => 'text',
								'title' => __( 'Heureka CPC', 'heureka' ),
								'desc'  => __( 'Set as decimal number with comma separator greater than zero', 'heureka' ),
							),
							array(
								'id'      => 'params',
								'type'    => 'multi_group',
								'title'   => __( 'Params', 'heureka' ),
								'default' => array(),
								'buttons' => array(
									'add'    => __( 'Add parameter', 'heureka' ),
									'remove' => __( 'Remove parameter', 'heureka' ),
								),
								'items'   => array(
									array(
										'id'      => 'name',
										'type'    => 'text',
										'title'   => __( 'Name', 'heureka' ),
										'default' => '',
									),
									array(
										'id'      => 'value',
										'type'    => 'text',
										'title'   => __( 'Value', 'heureka' ),
										'default' => '',
									)
								)
							),
							array(
								'id'    => 'gift',
								'type'  => 'text',
								'title' => __( 'Gift', 'heureka' ),
							),
							array(
								'id'      => 'extended_warranty',
								'type'    => 'group',
								'title'   => __( 'Extended_warranty', 'heureka' ),
								'default' => array(),
								'items'   => array(
									array(
										'id'    => 'months',
										'type'  => 'number',
										'title' => __( 'Months', 'heureka' ),
										'desc'  => __( 'Length of warranty in number of months.', 'heureka' ),
									),
									array(
										'id'      => 'title',
										'type'    => 'text',
										'title'   => __( 'Title', 'heureka' ),
										'desc'    => __( 'The maximum length of the caption is 128 characters.', 'heureka' ),
										'default' => '',
									),
								)
							),
							array(
								'id'      => 'accessory',
								'type'    => 'multi_group',
								'title'   => __( 'Accessory', 'heureka' ),
								'default' => array(),
								'buttons' => array(
									'add'    => __( 'Add accessory', 'heureka' ),
									'remove' => __( 'Remove accessory', 'heureka' ),
								),
								'items'   => array(
									array(
										'id'        => 'product_id',
										'type'      => 'post',
										'title'     => __( 'Product', 'heureka' ),
										'post_type' => ProductPostType::KEY,
										'default'   => 0,
									),
								)
							),
							array(
								'id'      => 'special_services',
								'type'    => 'multi_group',
								'title'   => __( 'Special services', 'heureka' ),
								'desc'    => __( 'Heureka XML feed will use only max. five services.', 'heureka' ),
								'default' => array(),
								'buttons' => array(
									'add'    => __( 'Add service', 'heureka' ),
									'remove' => __( 'Remove service', 'heureka' ),
								),
								'items'   => array(
									array(
										'id'      => 'title',
										'type'    => 'text',
										'title'   => __( 'Title', 'heureka' ),
										'desc'    => __( 'For example: free service, take out to the floor, etc.', 'heureka' ),
										'default' => '',
									),
								)
							),
							array(
								'id'      => 'voucher',
								'type'    => 'group',
								'title'   => __( 'Sales voucher', 'heureka' ),
								'default' => array(),
								'items'   => array(
									array(
										'id'      => 'code',
										'type'    => 'text',
										'title'   => __( 'Code', 'heureka' ),
										'default' => ''
									),
									array(
										'id'      => 'discount',
										'type'    => 'number',
										'title'   => __( 'Discount in %', 'heureka' ),
										'default' => 0
									),
									array(
										'id'      => 'description',
										'type'    => 'text',
										'title'   => __( 'Description', 'heureka' ),
										'default' => '',
									),
								),
							),
						),
					),
				),
			)
		);
	}

	public function add_custom_variations_fields( $loop, $variation_data, $variation ) {
		echo '<div class="options_group heureka-feed">';
		echo '<hr><h3>' . __( 'Heureka', 'heureka' ) . '</h3>';

		woocommerce_wp_text_input(
			array(
				'id'            => '_heureka_product_id[' . $variation->ID . ']',
				'label'         => __( 'Custom feed ID', 'heureka' ),
				'description'   => __( 'Overwrite ITEM_ID. Product ID or custom field from Global Feed settings is used as default.', 'heureka' ),
				'desc_tip'      => true,
				'value'         => get_post_meta( $variation->ID, '_heureka_product_id', true ),
				'wrapper_class' => 'form-row form-row-first',
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'            => '_heureka_product_ean[' . $variation->ID . ']',
				'label'         => __( 'EAN', 'heureka' ),
				'description'   => __( 'Set or overwrite global EAN code from custom field.', 'heureka' ),
				'desc_tip'      => true,
				'value'         => get_post_meta( $variation->ID, '_heureka_product_ean', true ),
				'wrapper_class' => 'form-row form-row-last',
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'            => '_heureka_product_name[' . $variation->ID . ']',
				'label'         => __( 'Product name', 'heureka' ),
				'description'   => __( 'Overwrite default product name', 'heureka' ),
				'desc_tip'      => true,
				'value'         => get_post_meta( $variation->ID, '_heureka_product_name', true ),
				'wrapper_class' => 'form-row form-row-full',

			)
		);
		woocommerce_wp_text_input(
			array(
				'id'            => '_heureka_product_title[' . $variation->ID . ']',
				'label'         => __( 'Product title', 'heureka' ),
				'description'   => __( 'Product name + information about product distribution or personal collection. Product name is used as default.', 'heureka' ),
				'desc_tip'      => true,
				'value'         => get_post_meta( $variation->ID, '_heureka_product_title', true ),
				'wrapper_class' => 'form-row form-row-full',
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'            => '_heureka_product_number[' . $variation->ID . ']',
				'label'         => __( 'Product number', 'heureka' ),
				'description'   => __( 'Set product number for PRODUCTNO parameter.', 'heureka' ),
				'desc_tip'      => true,
				'value'         => get_post_meta( $variation->ID, '_heureka_product_number', true ),
				'wrapper_class' => 'form-row form-row-full',
			)
		);
		echo '</div>';
	}

	function save_custom_variation_fields( $post_id ) {
		update_post_meta( $post_id, '_heureka_product_id', sanitize_text_field( $_POST['_heureka_product_id'][ $post_id ] ) );
		update_post_meta( $post_id, '_heureka_product_ean', sanitize_text_field( $_POST['_heureka_product_ean'][ $post_id ] ) );
		update_post_meta( $post_id, '_heureka_product_name', sanitize_text_field( $_POST['_heureka_product_name'][ $post_id ] ) );
		update_post_meta( $post_id, '_heureka_product_title', sanitize_text_field( $_POST['_heureka_product_title'][ $post_id ] ) );
		update_post_meta( $post_id, '_heureka_product_number', sanitize_text_field( $_POST['_heureka_product_number'][ $post_id ] ) );
	}
}
