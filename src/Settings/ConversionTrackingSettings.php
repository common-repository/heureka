<?php

namespace Heureka\Settings;

use Heureka\Abstracts\AbstractSettings;
use Heureka\Settings;

class ConversionTrackingSettings extends AbstractSettings {

	const INPUT_OPTOUT_NAME = 'wpify_woo_heureka_optout';

	public function setup() {
		if ( ! $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_CONVERSION_TRACKING ) ) {
			return;
		}
		$items = array(
			array(
				'id'    => 'api_key',
				'type'  => 'text',
				'label' => __( 'Public key for conversions', 'heureka' ),
				'desc'  => sprintf( __( 'Enter the public key for the conversion measurement code can be found in the <a href="%s" target="_blank">Heureka administration</a>', 'heureka' ), __( 'https://sluzby.heureka.cz/obchody/mereni-konverzi/', 'heureka' ) ),
			),
			array(
				'id'      => 'country',
				'type'    => 'select',
				'options' => [
					[
						'label' => 'CZ',
						'value' => 'cs',
					],
					[
						'label' => 'SK',
						'value' => 'sk',
					],
				],
				'label'   => __( 'Country', 'heureka' ),
				'desc'    => __( 'Select country for tracking', 'heureka' ),
			),
		);

		$this->wcf->create_woocommerce_settings(
			array(
				'tab'        => array(
					'id'    => 'heureka',
					'label' => __( 'Heureka', 'heureka' ),
				),
				'section'    => array(
					'id'    => Settings::SECTION_CONVERSION_TRACKING,
					'label' => __( 'Conversion tracking', 'heureka' ),
				),
				'page_title' => __( 'Heureka Settings', 'heureka' ),
				'items'      => array(
					array(
						'type'  => 'group',
						'id'    => Settings::SECTION_CONVERSION_TRACKING,
						'items' => $items,
					),
				),
			)
		);
	}
}
