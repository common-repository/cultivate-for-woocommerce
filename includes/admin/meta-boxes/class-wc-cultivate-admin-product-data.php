<?php

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Cultivate_Admin_Product_Data', false ) ) {
  return new WC_Cultivate_Admin_Product_Data();
}

/**
 * WC_Cultivate_Admin_Product_Data Class.
 */
class WC_Cultivate_Admin_Product_Data {

  const FIELD_MADE_IN_USA = '_cultivate_made_in_usa';
  const FIELD_IMPORTED_MATERIALS = '_cultivate_imported_materials';

  public function __construct() {
    add_filter( 'product_type_options', array( $this, 'add_product_option' ) );
    add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_tabs' ) );
    add_filter( 'woocommerce_product_data_panels', array( $this, 'product_tab_content' ) );
    add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_option_fields' ) );
    add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_option_fields' ) );
  }

  function add_product_option( $product_type_options ) {
    if ( $this->isReady() ) {
      $product_type_options['cultivate_made_in_usa'] = array(
        'id'            => self::FIELD_MADE_IN_USA,
        'wrapper_class' => 'show_if_simple show_if_variable',
        'label'         => __( 'Made in USA', 'cultivate-for-woocommerce' ),
        'description'   => __( 'Made in USA products are listed on Cultivate.', 'cultivate-for-woocommerce' ),
        'default'       => 'no'
      );
    }

    return $product_type_options;
  }

  function isReady() {
    return ! ! get_user_meta( get_current_user_id(), WC_Cultivate::CULTIVATE_AUTHORIZED_META_KEY, true );
  }

  /**
   * Add a custom product tab.
   */
  function product_tabs( $tabs ) {
    if ( $this->isReady() ) {
      $tabs['cultivate'] = array(
        'label'    => __( 'Cultivate', 'cultivate-for-woocommerce' ),
        'target'   => 'cultivate_options',
        'class'    => array( 'show_if_made_in_usa' ),
        'priority' => 61
      );
    }

    return $tabs;
  }

  /**
   * Contents of the options product tab.
   */
  function product_tab_content() {
    include __DIR__ . '/views/html-product-data-options.php';
  }

  /**
   * Save the custom fields.
   */
  function save_option_fields( $post_id ) {
    if ( ! $this->isReady() ) {
      return;
    }

    $made_in_usa        = ! empty( $_POST[ self::FIELD_MADE_IN_USA ] );
    $imported_materials = ! empty( $_POST[ self::FIELD_IMPORTED_MATERIALS ] )
                          && $_POST[ self::FIELD_IMPORTED_MATERIALS ] === 'yes';

    update_post_meta( $post_id, self::FIELD_MADE_IN_USA, $made_in_usa ? 'yes' : 'no' );
    update_post_meta( $post_id, self::FIELD_IMPORTED_MATERIALS, $imported_materials ? 'yes' : 'no' );
  }
}

return new WC_Cultivate_Admin_Product_Data();
