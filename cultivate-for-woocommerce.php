<?php

/**
 * Plugin Name: Cultivate for WooCommerce
 * Plugin URI: https://www.wecultivate.us/install/woocommerce
 * Description: Cultivate integration for WooCommerce stores.
 * Version: 0.1.0
 * Author: Cultivate
 * Author URI: https://www.wecultivate.us/
 * Developer: Cultivate
 * Developer URI: https://www.wecultivate.us/
 * Text Domain: cultivate-for-woocommerce
 * Domain Path: /languages
 *
 * WC requires at least: 3.9.0
 * WC tested up to: 6.1.1
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Cultivate\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  define( 'WP_CULTIVATE_PLUGIN_VERSION', '0.1.0' );

  require_once __DIR__ . '/includes/wc-cultivate-context.php';

  new WC_Cultivate( __FILE__ );
}
