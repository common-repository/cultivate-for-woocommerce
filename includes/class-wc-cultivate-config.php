<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Cultivate_Config', false ) ) {

  /**
   * WC_Cultivate_Config Class.
   */
  class WC_Cultivate_Config {

    private $authorize_url;

    private $authorize_callback_url;

    private $api_base_url;

    private $client_api_base_url;

    public function __construct() {
      $this->authorize_url          = defined( 'WC_CULTIVATE_AUTHORIZE_URL' ) ? WC_CULTIVATE_AUTHORIZE_URL
        : 'https://www.wecultivate.us/woocommerce/authorize/user';
      $this->authorize_callback_url = defined( 'WC_CULTIVATE_AUTHORIZE_CALLBACK_URL' )
        ? WC_CULTIVATE_AUTHORIZE_CALLBACK_URL : 'https://www.wecultivate.us/woocommerce/authorize/keys';
      $this->api_base_url           = defined( 'WC_CULTIVATE_API_BASE_URL' ) ? WC_CULTIVATE_API_BASE_URL
        : 'https://www.wecultivate.us/api/woocommerce/v1';
      $this->client_api_base_url    = defined( 'WC_CULTIVATE_CLIENT_API_BASE_URL' ) ? WC_CULTIVATE_CLIENT_API_BASE_URL
        : $this->api_base_url;
    }

    /**
     * @return string
     */
    public function get_authorize_url() {
      return $this->authorize_url;
    }

    /**
     * @return string
     */
    public function get_authorize_callback_url() {
      return $this->authorize_callback_url;
    }

    /**
     * @return string
     */
    public function get_api_base_url() {
      return $this->api_base_url;
    }

    /**
     * @return string
     */
    public function get_client_api_base_url() {
      return $this->client_api_base_url;
    }
  }
}
