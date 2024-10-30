<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Cultivate_Admin_Styles', false ) ) {

  /**
   * WC_Cultivate_Admin_Styles Class.
   */
  class WC_Cultivate_Admin_Styles {

    static function register() {
      include __DIR__ . '/views/html-admin-styles.php';
    }
  }
}
