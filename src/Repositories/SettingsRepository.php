<?php

namespace Heureka\Repositories;

use Heureka\Settings;

class SettingsRepository {

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @param string $key
	 * @param null   $default
	 *
	 * @return string|array
	 */
	public function get_option( $section, string $key = '', $default = null ) {
		if ( empty( $this->options[ $section ] ) ) {
			$this->get_options( $section );
		}

		if ( isset( $this->options[ $section ][ $key ] ) ) {
			return $this->options[ $section ][ $key ];
		}

		return $default ?: false;
	}

	/**
	 * Get all options
	 *
	 * @return array|mixed
	 */
	public function get_options( $section ) {
		if ( empty( $this->options[ $section ] ) ) {
			$this->options[ $section ] = get_option( $section, array() );
		}

		return $this->options[ $section ];
	}

	public function get_shipping_method_by_id( $id ) {
		foreach ( $this->get_option( Settings::SECTION_MARKETPLACE, 'shipping_methods', array() ) as $method ) {
			if ( $method['id'] == $id ) {
				return $method;
			}
		}

		return null;
	}

	public function get_payment_method_by_id( $id ) {
		$default_method    = null;
		$default_method_id = $this->get_marketplace_setting( 'default_payment' );
		foreach ( $this->get_option( Settings::SECTION_MARKETPLACE, 'payment_methods', array() ) as $method ) {
			if ( $method['id'] == $id ) {
				return $method;
			}
			if ( $method['id'] == $default_method_id ) {
				$default_method = $method;
			}
		}

		return $default_method;
	}

	public function get_general_settings() {
		return $this->get_options( Settings::SECTION_GENERAL );
	}

	public function get_general_setting( $id ) {
		return $this->get_option( Settings::SECTION_GENERAL, $id );
	}

	public function get_customer_verified_settings() {
		return $this->get_options( Settings::SECTION_CUSTOMER_VERIFIED );
	}

	public function get_customer_verified_setting( $id ) {
		return $this->get_option( Settings::SECTION_CUSTOMER_VERIFIED, $id );
	}

	public function get_marketplace_settings() {
		return $this->get_options( Settings::SECTION_MARKETPLACE );
	}

	public function get_marketplace_setting( $id ) {
		return $this->get_option( Settings::SECTION_MARKETPLACE, $id );
	}

	public function get_feed_settings() {
		return $this->get_options( Settings::SECTION_FEED_PRODUCTS );
	}

	public function get_conversion_tracking_setting( $id ) {
		return $this->get_option( Settings::SECTION_CONVERSION_TRACKING, $id );
	}

	public function get_feed_setting( $id ) {
		return $this->get_option( Settings::SECTION_FEED_PRODUCTS, $id );
	}

	public function get_categories_settings( $section ) {
		return $this->get_options( $section );
	}

	public function get_categories_setting( $section, $id ) {
		return $this->get_option( $section, $id );
	}

	public function is_feature_enabled( string $feature ): bool {
		$enabled_features = $this->get_general_setting( 'enabled_features' ) ?: [];

		return in_array( $feature, $enabled_features, true );
	}
}
