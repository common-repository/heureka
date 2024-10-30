<?php

namespace Heureka\Settings;

use Heureka\Abstracts\AbstractSettings;
use Heureka\Settings;

class CustomerVerifiedSettings extends AbstractSettings {

	const INPUT_OPTOUT_NAME = 'heureka_optout';

	public function setup() {
		if ( ! $this->settings_repository->is_feature_enabled( GeneralSettings::FEATURE_CUSTOMERS_VERIFIED ) ) {
			return;
		}
		$items = array(
			array(
				'id'      => 'country',
				'type'    => 'select',
				'label'   => __( 'Country', 'heureka' ),
				'desc'    => __( 'Select country', 'heureka' ),
				'options' => array(
					array(
						'label' => __( 'Heureka CZ', 'heureka' ),
						'value' => 'CZ',
					),
					array(
						'label' => __( 'Heureka SK', 'heureka' ),
						'value' => 'SK',
					),
				),
			),
			array(
				'id'    => 'api_key',
				'type'  => 'text',
				'label' => __( 'Secret Key', 'heureka' ),
				'desc'  => sprintf( __( 'Enter the Secret Key for Verified Customers can be found in the <a href="%s" target="_blank">Verified Customers settings</a>', 'heureka' ), __( 'https://sluzby.heureka.cz/n/sluzby/certifikat-spokojenosti/', 'heureka' ) ),
			),
			array(
				'id'    => 'enable_optout',
				'type'  => 'switch',
				'label' => __( 'Enable Opt-Out', 'heureka' ),
				'desc'  => __( 'Check if you want to enable opt out on the checkout', 'heureka' ),
			),
			array(
				'id'      => 'enable_optout_text',
				'type'    => 'text',
				'label'   => __( 'Opt-Out Text', 'heureka' ),
				'desc'    => __( 'Enter the text of the Opt-out checkbox to refuse the questionnaire. Default text: ', 'heureka' ) .
				             __( "I don't want to receive survey from Heureka ověřeno zákazníky", 'heureka' ),
				'default' => __( "I don't want to receive survey from Heureka ověřeno zákazníky", 'heureka' ),
			),
			array(
				'id'    => 'widget_enabled',
				'type'  => 'switch',
				'label' => __( 'Enable Certification Widget', 'heureka' ),
				'desc'  => __( 'Check if you want to enable certification widget.', 'heureka' ),
			),
			array(
				'id'    => 'widget_code',
				'type'  => 'textarea',
				'label' => __( 'Certification widget code', 'heureka' ),
				'desc'  => __( 'Copy the code from your Heureka account.', 'heureka' ),
			),
			array(
				'id'    => 'send_async',
				'type'  => 'toggle',
				'label' => __( 'Send asynchronously', 'heureka' ),
				'desc'  => __( 'By default the order is sent to Heureka synchronously, which is required. Under some circumstances this can cause issues - toggle on if you want to schedule the event and send it asynchronously.', 'heureka' ),
			),
		);

		$this->wcf->create_woocommerce_settings(
			array(
				'tab'        => array(
					'id'    => 'heureka',
					'label' => __( 'Heureka', 'heureka' ),
				),
				'section'    => array(
					'id'    => Settings::SECTION_CUSTOMER_VERIFIED,
					'label' => __( 'Customer verified', 'heureka' ),
				),
				'page_title' => __( 'Heureka Settings', 'heureka' ),
				'items'      => array(
					array(
						'type'  => 'group',
						'id'    => Settings::SECTION_CUSTOMER_VERIFIED,
						'items' => $items,
					),
				),
			)
		);
	}
}
