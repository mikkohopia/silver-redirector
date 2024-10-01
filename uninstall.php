<?php
// If uninstall is not called by WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

// Remove the option 'silver_redirects' from the database.
delete_option( 'silver_redirects' );