<?php

namespace Heureka;

use Heureka\Settings\ConversionTrackingSettings;
use Heureka\Settings\CustomerVerifiedSettings;
use Heureka\Settings\FeedProductsCzSettings;
use Heureka\Settings\FeedProductsSkSettings;
use Heureka\Settings\FeedProductsSettings;
use Heureka\Settings\GeneralSettings;
use Heureka\Settings\MarketplaceSettings;
use Heureka\Settings\OrderStatusSettings;
use Heureka\Settings\PaymentSettings;
use Heureka\Settings\ProductSettings;
use Heureka\Settings\ShippingSettings;
use HeurekaDeps\Wpify\CustomFields\CustomFields;

/**
 * Class Settings
 *
 * @package  Wpify\Settings
 * @property Plugin $plugin
 */
class Settings {

	/**
	 * @var CustomFields
	 */
	public $wcf;

	const SECTION_GENERAL = 'heureka_general';
	const SECTION_CUSTOMER_VERIFIED = 'heureka_customer_verified';
	const SECTION_CONVERSION_TRACKING = 'heureka_conversion_tracking';
	const SECTION_MARKETPLACE = 'heureka_marketplace';
	const SECTION_FEED_PRODUCTS = 'heureka_feed_products';
	const SECTION_FEED_PRODUCTS_CZ = 'heureka_feed_products_cz';
	const SECTION_FEED_PRODUCTS_SK = 'heureka_feed_products_sk';

	public function __construct(
		GeneralSettings $general,
		CustomerVerifiedSettings $customer_verified_settings,
		MarketplaceSettings $marketplace_settings,
		FeedProductsSettings $feed_products_settings,
		FeedProductsCzSettings $feed_products_cz_settings,
		FeedProductsSkSettings $feed_products_sk_settings,
		ProductSettings $product_settings,
		ConversionTrackingSettings $conversion_tracking_settings
	) {
		add_action(
			'admin_init',
			function () {
				if ( filter_input( INPUT_GET, 'tab' ) === 'heureka' && is_null( filter_input( INPUT_GET, 'section' ) ) ) {
					wp_redirect(
						add_query_arg(
							array(
								'page'    => 'wc-settings',
								'tab'     => 'heureka',
								'section' => self::SECTION_GENERAL,
							),
							admin_url( 'admin.php' )
						)
					);
					exit();
				}
			}
		);
	}

}
