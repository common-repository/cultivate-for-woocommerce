<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Cultivate', false ) ) {

  /**
   * WC_Cultivate Class.
   */
  class WC_Cultivate {

    const COOKIE_NAME = 'cultivating-wc';
    const TRACKING_TOKEN_PARAMETER_NAME = 'cultivating';
    const CULTIVATE_API_TOKEN_PARAMETER_NAME = 'token';
    const CULTIVATE_AUTHORIZED_META_KEY = 'wc_cultivate_authorized';
    const TRACKING_TOKEN_FIELD_NAME = '_cultivate_tracking_token';
    const SHOW_ACTIVATION_NOTICE_OPTION_NAME = 'wc_cultivate_show_activation_notice';

    private static $plugin_file;
    private static $config;

    public function __construct( $plugin_file ) {
      WC_Cultivate::$plugin_file = $plugin_file;
      WC_Cultivate::$config      = new WC_Cultivate_Config();

      register_activation_hook( $plugin_file, array( $this, 'activation_hook' ) );

      add_action( 'init', array( $this, 'init_action' ) );
      add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta_action' ) );

      add_action( 'admin_init', array( $this, 'admin_init_action' ) );
      add_action( 'admin_menu', array( $this, 'admin_menu_action' ) );

      add_action( 'admin_head', array( $this, 'admin_head_action' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_action' ) );

      $this->set_up_metaboxes();

      register_deactivation_hook( $plugin_file, array( $this, 'deactivation_hook' ) );

      register_uninstall_hook( $plugin_file, array( 'WC_Cultivate', 'uninstall_hook' ) );
    }

    /**
     * @return string
     */
    static public function plugin_file() {
      return WC_Cultivate::$plugin_file;
    }

    /**
     * @return WC_Cultivate_Config
     */
    static public function config() {
      return WC_Cultivate::$config;
    }

    function activation_hook() {
      add_option( self::SHOW_ACTIVATION_NOTICE_OPTION_NAME, true );
    }

    function init_action() {
      if ( isset( $_GET[ self::TRACKING_TOKEN_PARAMETER_NAME ] ) ) {
        $expiration = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * 7 );
        setcookie( self::COOKIE_NAME, $_GET[ self::TRACKING_TOKEN_PARAMETER_NAME ], $expiration );
      }
    }

    function update_order_meta_action( $order_id ) {
      if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
        update_post_meta(
          $order_id,
          self::TRACKING_TOKEN_FIELD_NAME,
          sanitize_text_field( $_COOKIE[ self::COOKIE_NAME ] )
        );
      }
    }

    function admin_init_action() {
      if ( get_option( self::SHOW_ACTIVATION_NOTICE_OPTION_NAME, false ) ) {
        delete_option( self::SHOW_ACTIVATION_NOTICE_OPTION_NAME );

        $non_default_permalinks_enabled = ! ! get_option( 'permalink_structure' );

        if ( $non_default_permalinks_enabled ) {
          WC_Admin_Notices::add_custom_notice(
            'wc_cultivate_plugin_installed',
            '<div data-test-id="cultivateAuthorizeNotice">' .
            '<p><strong>' . __( 'Cultivate for WooCommerce is installed', 'cultivate-for-woocommerce' )
            . '</strong></p>' .
            '<p>' . __( 'Please sign in to Cultivate to connect your store.', 'cultivate-for-woocommerce' ) . '</p>' .
            '<p>' . __( 'Your store name, URL and timezone will be sent to our servers.', 'cultivate-for-woocommerce' )
            . '</p>' .
            '<p><a href="' . admin_url( 'admin.php?page=wc-cultivate-authorize' )
            . '" class="button-primary" data-test-id="authorizeButton">' . __( 'Sign In to Cultivate',
              'cultivate-for-woocommerce' ) . '</a></p>' .
            '</div>'
          );
        } else {
          WC_Admin_Notices::add_custom_notice(
            'wc_cultivate_plugin_installed',
            '<div data-test-id="cultivateInvalidPermalinksNotice">' .
            '<p><strong>' . __( 'Cultivate for WooCommerce is installed', 'cultivate-for-woocommerce' )
            . '</strong></p>' .
            '<p>'
            . __( 'WooCommerce for Cultivate requires that you set up pretty permalinks. Default permalinks will not work, because they are not supported by the WooCommerce REST API.',
              'cultivate-for-woocommerce' ) . '</p>' .
            '<p><a href="' . admin_url( 'options-permalink.php' )
            . '" class="button-primary" data-test-id="openPermalinksButton">' . __( 'Open permalinks settings',
              'cultivate-for-woocommerce' ) . '</a></p>' .
            '</div>'
          );
        }
      }

      if ( isset( $_GET[ self::CULTIVATE_API_TOKEN_PARAMETER_NAME ] ) ) {
        update_user_meta(
          get_current_user_id(),
          self::CULTIVATE_AUTHORIZED_META_KEY,
          'yes'
        );
      }
    }

    function admin_menu_action() {
      require_once __DIR__ . '/admin/class-wc-cultivate-admin-menus.php';
    }

    function admin_head_action() {
      require_once __DIR__ . '/admin/class-wc-cultivate-admin-styles.php';
      WC_Cultivate_Admin_Styles::register();
    }

    function admin_enqueue_scripts_action( $hook ) {
      require_once __DIR__ . '/admin/class-wc-cultivate-admin-scripts.php';

      $scripts = new WC_Cultivate_Admin_Scripts();

      if ( $hook === 'woocommerce_page_wc-settings' ) {
        $scripts->enqueueOptionsPageScript();
      } else if ( $hook === 'post.php' ) {
        $scripts->enqueuePostPageScript();
      }
    }

    function set_up_metaboxes() {
      require_once __DIR__ . '/admin/meta-boxes/class-wc-cultivate-admin-product-data.php';
    }

    function deactivation_hook() {
      self::delete_user_api_tokens();
    }

    static function uninstall_hook() {
      self::delete_user_api_tokens();
    }

    private static function delete_user_api_tokens() {
      delete_metadata(
        'user',
        0,
        self::CULTIVATE_AUTHORIZED_META_KEY,
        '',
        true
      );
    }
  }
}
