<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Cultivate_Admin_Scripts', false ) ) {

  /**
   * WC_Cultivate_Admin_Scripts Class.
   */
  class WC_Cultivate_Admin_Scripts {

    function __construct() {
      $this->register_options_page_script();
      $this->register_post_page_script();
      $this->register_jquery_fileupload_lib_script();
    }

    public function enqueueOptionsPageScript() {
      wp_enqueue_script( 'wc-cultivate-options-page' );
    }

    public function enqueuePostPageScript() {
      wp_enqueue_script( 'wc-cultivate-wc-post-page' );
    }

    private function register_options_page_script() {
      $this->register_script(
        'js/options-page.js',
        'wc-cultivate-options-page',
        array( 'wc-cultivate-jquery-ui-fileupload' )
      );
    }

    private function register_post_page_script() {
      $this->register_script( 'js/post-page.js', 'wc-cultivate-wc-post-page' );
    }

    private function register_jquery_fileupload_lib_script() {
      $this->register_script(
        'assets/lib/jquery.iframe-transport.js',
        'wc-cultivate-jquery-iframe-transport',
        array( 'jquery' )
      );

      $this->register_script(
        'assets/lib/jquery.fileupload.js',
        'wc-cultivate-jquery-ui-fileupload',
        array( 'jquery-ui-widget', 'wc-cultivate-jquery-iframe-transport' )
      );
    }

    private function register_script( $path, $handle, $deps = array( 'jquery' ) ) {
      wp_register_script(
        $handle,
        plugin_dir_url( WC_Cultivate::plugin_file() ) . $path,
        $deps,
        filemtime( plugin_dir_path( WC_Cultivate::plugin_file() ) . $path ),
        true
      );
    }
  }
}
