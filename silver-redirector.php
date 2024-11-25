<?php
/**
 * Plugin Name: Silver Redirector
 * Plugin URI: https://github.com/mikkohopia/silver-redirector/
 * Description: Redirects pages to other URLs on specific dates.
 * Version: 1.0
 * Author: Mikko Hopia
 * Author URI: https://www.mikkohopia.net
 * Author Email: mikko@mikkohopia.net
 * Text Domain: silver-redirector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

// Define plugin path
define( 'SILVER_REDIRECTOR_PATH', plugin_dir_path( __FILE__ ) );

// Include admin class
require_once SILVER_REDIRECTOR_PATH . 'includes/class-silver-redirector-admin.php';

// Activate the plugin
function silver_redirector_activate() {
  // Activation logic here if needed
}
register_activation_hook( __FILE__, 'silver_redirector_activate' );

// Deactivate the plugin
function silver_redirector_deactivate() {
  // Deactivation logic here if needed
}
register_deactivation_hook( __FILE__, 'silver_redirector_deactivate' );

// Initialize plugin admin interface
if ( is_admin() ) {
  new Silver_Redirector_Admin();
}

// Redirect logic based on date
function silver_redirector_check_redirects() {
  $redirects = get_option( 'silver_redirects', array() );
  
  // Hae nykyinen päivämäärä käyttäen WordPressin aikavyöhykettä
  $current_date = wp_date( 'Y-m-d' );
  
  // Hae nykyinen URL ja käsittele polku
  $current_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $current_url = rtrim( $current_url, '/' ); // Poistaa lopusta kenoviivan, jos se on
  
  // Erota polku nykyisestä URL:sta ja poista kenoviiva lopusta
  $parsed_current_url = parse_url( $current_url );
  $current_path = isset( $parsed_current_url['path'] ) ? rtrim( $parsed_current_url['path'], '/' ) : '';

  foreach ( $redirects as $redirect ) {
    // Tarkista, onko 'from_url' absoluuttinen vai suhteellinen
    if ( strpos( $redirect['from_url'], 'http' ) === 0 ) {
      // Jos absoluuttinen URL, käsittele sellaisenaan ja poista kenoviiva
      $parsed_from_url = parse_url( $redirect['from_url'] );
      $from_path = isset( $parsed_from_url['path'] ) ? rtrim( $parsed_from_url['path'], '/' ) : '';
    } else {
      // Jos suhteellinen URL, lisää 'home_url' ja poista kenoviiva
      $parsed_from_url = parse_url( home_url( $redirect['from_url'] ) );
      $from_path = isset( $parsed_from_url['path'] ) ? rtrim( $parsed_from_url['path'], '/' ) : '';
    }

    // Vertaa normalisoituja polkuja
    if ( $redirect['date'] === $current_date && $from_path === $current_path ) {
      wp_redirect( home_url( $redirect['to_url'] ), 302 ); // 302 = Väliaikainen uudelleenohjaus
      exit;
    }
  }
}
add_action( 'template_redirect', 'silver_redirector_check_redirects' );