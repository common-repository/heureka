=== Heureka ===
Contributors: heureka
Tags: WooCommerce
Requires at least: 5.8
Tested up to: 6.3
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Official Heureka integration for WooCommerce

== Description ==

Official integration of Heureka services for WooCommerce.

Supported services:

* XML feed
* Availability XML feed
* Verified by Customers
* Heureka Marketplace

Don't know what to do? Is something not working? Contact us at info@heureka.cz.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/heureka` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the WooCommerce -> Settings -> Heureka screen to configure the plugin

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==
= 1.1.0 =
* Fix CSRF vulnerability on admin actions
* Fix order status request
* Update Heureka IP addresses whitelist for new API
* Update delivery methods for new API
* Add Depot API Store type
* Declare HPOS support
* Update deps

= 1.0.8 =
* Fix generating product variations to feed
* Add option to exclude items without mapped category
* Update deps

= 1.0.7 =
* Fix DepotAPI delivery branches
* Fix get product id
* Fix creating order
* Fix calculate prices and fees
* Fix auto generating feeds after update or add new product
* Fix duplicity products
* Update deps

= 1.0.6 =
* Update readme

= 1.0.5 =
* Update deps
* fix ip request check

= 1.0.4 =
* Update delivery methods

= 1.0.3 =
* Minor fixes
* Add ID, EAN, Name, Title and Number fields into product variations
* Add option to sending to Heureka asynchronously
* Better sending data to Heureka using wp_request
* Better loading categories

= 1.0.2 =
* Fix cancellation of scheduled actions when a module or plugin is deactivated

= 1.0.1 =
* Feed generation fixes

= 1.0.0 =
* Initial release
