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
  $current_date = wp_date( 'Y-m-d' );
  $current_url = ( isset($_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $current_url = rtrim( $current_url, '/' ); // Remove slash if there is one
  foreach ( $redirects as $redirect ) {
    $from_url = rtrim( $redirect['from_url'], '/' ); // Remove slash if there is one
    if ( $redirect['date'] === $current_date && $from_url === $current_url ) {
      wp_redirect( home_url( $redirect['to_url'] ), 302 ); // 302 = Moved Temporarily
      exit;
    }
  }
}
add_action( 'template_redirect', 'silver_redirector_check_redirects' );