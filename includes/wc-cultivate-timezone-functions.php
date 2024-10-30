<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_cultivate_get_timezone' ) ) {
  function wc_cultivate_get_timezone() {
    if ( function_exists( 'wp_timezone' ) ) {
      return wp_timezone();
    }

    # https://wordpress.stackexchange.com/a/283094
    $timezone_string = get_option( 'timezone_string' );
    if ( ! empty( $timezone_string ) ) {
      return new DateTimeZone( $timezone_string );
    }
    $offset  = get_option( 'gmt_offset' );
    $hours   = (int) $offset;
    $minutes = abs( ( $offset - (int) $offset ) * 60 );
    $offset  = sprintf( '%+03d:%02d', $hours, $minutes );

    return new DateTimeZone( $offset );
  }
}
