<?php

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Cultivate_Admin_Menus', false ) ) {
  return new WC_Cultivate_Admin_Menus();
}

/**
 * WC_Cultivate_Admin_Menus Class.
 */
class WC_Cultivate_Admin_Menus {

  public function __construct() {
    add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_options_tab' ), 50 );
    add_action( 'woocommerce_before_settings_wc_cultivate', array( $this, 'hide_existing_save_button' ) );
    add_action( 'woocommerce_after_settings_wc_cultivate', array( $this, 'options_page' ) );
    $this->add_authorize_page();
    $this->add_authorize_complete_page();
  }

  function add_authorize_page() {
    add_submenu_page(
      null,
      __( 'Authorize', 'cultivate-for-woocommerce' ),
      null,
      'edit_others_shop_orders',
      'wc-cultivate-authorize',
      array( $this, 'authorize_page' )
    );
  }

  function add_authorize_complete_page() {
    add_submenu_page(
      null,
      __( 'Authorize Complete', 'cultivate-for-woocommerce' ),
      null,
      'edit_others_shop_orders',
      'wc-cultivate-authorize-complete',
      array( $this, 'authorize_complete_page' )
    );
  }

  function add_options_tab( $tabs ) {
    $tabs['wc_cultivate'] = __( 'Cultivate', 'cultivate-for-woocommerce' );

    return $tabs;
  }

  function hide_existing_save_button() {
    $GLOBALS['hide_save_button'] = true;
  }

  function options_page() {
    include_once __DIR__ . '/views/html-admin-options-page.php';
  }

  function authorize_page() {
    $user_id = get_current_user_id();

    $authorize_api_url = '/wc-auth/v1/authorize?' . http_build_query( array(
        'app_name' => __( 'Cultivate for WooCommerce', 'cultivate-for-woocommerce' ),
        'scope'    => 'read_write',
        'user_id'  => $user_id
        // return_url, callback_url will be appended by remote before redirect if necessary
      ) );

    $redirect_url = WC_Cultivate::config()->get_authorize_url() . '?' . http_build_query( array(
        'siteUrl'         => get_site_url(),
        'authorizeApiUrl' => get_site_url( null, $authorize_api_url ),
        'keysUrl'         => WC_Cultivate::config()->get_authorize_callback_url(),
        'returnToUrl'     => get_site_url( null, '/wp-admin/admin.php?page=wc-cultivate-authorize-complete' ),
        'pluginVersion'   => WP_CULTIVATE_PLUGIN_VERSION
      ) );


    wp_redirect( esc_url_raw( $redirect_url ) );
  }

  function authorize_complete_page() {
    WC_Admin_Notices::remove_notice( 'wc_cultivate_plugin_installed' );

    include_once __DIR__ . '/views/html-admin-authorize-complete-page.php';
  }
}

return new WC_Cultivate_Admin_Menus();
