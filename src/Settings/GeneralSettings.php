<?php

namespace Heureka\Settings;

use Heureka\Abstracts\AbstractSettings;
use Heureka\Settings;

class GeneralSettings extends AbstractSettings {
	const FEATURE_FEEDS = 'feeds';
	const FEATURE_MARKETPLACE = 'marketplace';
	const FEATURE_CUSTOMERS_VERIFIED = 'customers_verified';
	const FEATURE_CONVERSION_TRACKING = 'conversion_tracking';

	public function setup() {
		$items = array(
			array(
				'title'   => __( 'Enabled features', 'heureka' ),
				'id'      => 'enabled_features',
				'type'    => 'multi_toggle',
				'options' => [
					[
						'label' => __( 'Feeds', 'heureka' ),
						'value' => self::FEATURE_FEEDS,
					],
					[
						'label' => __( 'Marketplace', 'heureka' ),
						'value' => self::FEATURE_MARKETPLACE,
					],
					[
						'label' => __( 'Verified by customers', 'heureka' ),
						'value' => self::FEATURE_CUSTOMERS_VERIFIED,
					],
					[
						'label' => __( 'Conversion tracking', 'heureka' ),
						'value' => self::FEATURE_CONVERSION_TRACKING,
					],
				],
			),
		);

		$this->wcf->create_woocommerce_settings(
			array(
				'tab'        => array(
					'id'    => 'heureka',
					'label' => __( 'Heureka', 'heureka' ),
				),
				'section'    => array(
					'id'    => Settings::SECTION_GENERAL,
					'label' => __( 'General settings', 'heureka' ),
				),
				'page_title' => __( 'Heureka Settings', 'heureka' ),
				'items'      => array(
					array(
						'type'  => 'group',
						'id'    => Settings::SECTION_GENERAL,
						'items' => $items,
					),
				),
			)
		);
	}
}
