<?php
/*
 * Plugin Name:          Heureka
 * Description:          Official integration of Heureka services for WooCommerce. Heureka marketplace, product feed, availability feed and Customer Verified.
 * Version:              1.1.0
 * Requires PHP:         7.4.0
 * Requires at least:    5.3.0
 * Author:               Heureka Group
 * Author URI:           https://www.heureka.cz/
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          heureka
 * Domain Path:          /languages
 * WC requires at least: 4.5
 * WC tested up to:      8.0
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Heureka\Plugin;
use HeurekaDeps\DI\Container;
use HeurekaDeps\DI\ContainerBuilder;

if ( ! defined( 'HEUREKA_MIN_PHP_VERSION' ) ) {
	define( 'HEUREKA_MIN_PHP_VERSION', '7.4.0' );
}

/**
 * @return Plugin
 * @throws Exception
 */
function heureka(): Plugin {
	return heureka_container()->get( Plugin::class );
}

/**
 * @return Container
 * @throws Exception
 */
function heureka_container(): Container {
	static $container;

	if ( empty( $container ) ) {
		$is_production    = ! WP_DEBUG;
		$file_data        = get_file_data( __FILE__, array( 'version' => 'Version' ) );
		$definition       = require_once __DIR__ . '/config.php';
		$containerBuilder = new ContainerBuilder();
		$containerBuilder->addDefinitions( $definition );
		$container = $containerBuilder->build();
	}

	return $container;
}

function heureka_activate( $network_wide ) {
	heureka()->activate( $network_wide );
}

function heureka_deactivate( $network_wide ) {
	heureka()->deactivate( $network_wide );
}

function heureka_uninstall() {
	heureka()->uninstall();
}

function heureka_php_upgrade_notice() {
	$info = get_plugin_data( __FILE__ );

	$string = sprintf(
		__( 'Opps! %s requires a minimum PHP version of %s. Your current version is: %s. Please contact your host to upgrade.', 'heureka' ),
		$info['Name'],
		HEUREKA_MIN_PHP_VERSION,
		PHP_VERSION
	);
	printf( '<div class="error notice"><p>%s</p></div>', $string );
}

function heureka_php_vendor_missing() {
	$info = get_plugin_data( __FILE__ );

	$string = sprintf(
		__( 'Opps! %s is corrupted it seems, please re-install the plugin.', 'heureka' ),
		$info['Name']
	);
	printf( '<div class="error notice"><p>%s</p></div>', $string );
}

function heureka_extension_missing() {
	$info = get_plugin_data( __FILE__ );

	$string = sprintf(
		__( 'Opps! %s requires PHP Curl extension installed. Please contact your web hosting to enable it.', 'heureka' ),
		$info['Name']
	);
	printf( '<div class="error notice"><p>%s</p></div>', $string );
}

if ( version_compare( PHP_VERSION, HEUREKA_MIN_PHP_VERSION ) < 0 ) {
	add_action( 'admin_notices', 'heureka_php_upgrade_notice' );
} elseif ( ! extension_loaded( 'curl' ) ) {
	add_action( 'admin_notices', 'heureka_extension_missing' );
} else {
	$deps_loaded   = false;
	$vendor_loaded = false;

	$deps = array_filter( array(
		__DIR__ . '/deps/scoper-autoload.php',
		__DIR__ . '/deps/autoload.php'
	), function ( $path ) {
		return file_exists( $path );
	} );

	foreach ( $deps as $dep ) {
		include_once $dep;
		$deps_loaded = true;
	}

	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		include_once __DIR__ . '/vendor/autoload.php';
		$vendor_loaded = true;
	}

	if ( $deps_loaded && $vendor_loaded ) {
		load_plugin_textdomain( 'heureka', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		add_action( 'plugins_loaded', 'heureka', 11 );
		register_activation_hook( __FILE__, 'heureka_activate' );
		register_deactivation_hook( __FILE__, 'heureka_deactivate' );
		register_uninstall_hook( __FILE__, 'heureka_uninstall' );
	} else {
		add_action( 'admin_notices', 'heureka_php_vendor_missing' );
	}
}

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
	}
} );
