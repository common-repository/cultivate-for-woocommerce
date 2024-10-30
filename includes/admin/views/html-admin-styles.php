<?php
defined( 'ABSPATH' ) || exit;
?>

<style>
  #woocommerce-product-data ul.wc-tabs li.cultivate_options a::before {
    background: url('<?php echo plugins_url('assets/images/wc-product-data-tab-inactive.svg', WC_Cultivate::plugin_file() ); ?>') no-repeat center;
    background-size: contain;
    margin-bottom: -2px;
    content: " " !important;
    width: 13px;
    height: 13px;
    display: inline-block;
    line-height: 1;
    fill: black;
  }

  #woocommerce-product-data ul.wc-tabs li.cultivate_options.active a::before {
    background-image: url('<?php echo plugins_url('assets/images/wc-product-data-tab-active.svg', WC_Cultivate::plugin_file() ); ?>');
  }
</style>
