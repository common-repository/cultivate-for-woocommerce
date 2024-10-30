<?php
defined( 'ABSPATH' ) || exit;
?>

<div id='cultivate_options' class='panel woocommerce_options_panel'>
  <div class='options_group show_if_made_in_usa'><?php

    global $product_object;

    woocommerce_wp_select( array(
      'id'          => '_cultivate_imported_materials',
      'label'       => __( 'Origin', 'cultivate-for-woocommerce' ),
      'desc_tip'    => true,
      'description' => __( 'By default, products you add to Cultivate are assumed to be Made in USA with imported materials - change here if this product is Fully Made in USA',
        'cultivate-for-woocommerce' ),
      'value'       => $product_object->meta_exists( '_cultivate_imported_materials' )
        ? $product_object->get_meta( '_cultivate_imported_materials' ) : 'yes',
      'options'     => array(
        'yes' => __( 'Made in USA with imported materials', 'cultivate-for-woocommerce' ),
        'no'  => __( 'Fully made in USA', 'cultivate-for-woocommerce' )
      ),
    ) )
    ?></div>
</div>
