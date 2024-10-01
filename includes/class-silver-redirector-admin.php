<?php
class Silver_Redirector_Admin {

  public function __construct() {
    add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    add_action( 'admin_init', array( $this, 'register_settings' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
  }

  public function add_admin_menu() {
    add_submenu_page(
      'tools.php',
      'Silver Redirector',
      'Silver Redirector',
      'manage_options',
      'silver-redirector',
      array( $this, 'admin_page' )
    );
  }

  public function register_settings() {
    register_setting( 'silver_redirector_group', 'silver_redirects' );
  }

  public function enqueue_admin_styles() {
    wp_enqueue_style( 'silver-redirector-admin-styles', plugin_dir_url( __FILE__ ) . 'admin-styles.css' );
  }

  public function admin_page() {
      if ( ! current_user_can( 'manage_options' ) ) {
          return;
      }

      $redirects = get_option( 'silver_redirects', array() );

      if ( isset( $_POST['add_redirect'] ) ) {
          $new_redirect = array(
              'date' => sanitize_text_field( $_POST['redirect_date'] ),
              'from_url' => sanitize_text_field( $_POST['from_url'] ),
              'to_url' => sanitize_text_field( $_POST['to_url'] ),
          );
          $redirects[] = $new_redirect;
          update_option( 'silver_redirects', $redirects );
      }

      if ( isset( $_POST['delete_redirect'] ) ) {
          $index = absint( $_POST['redirect_index'] );
          unset( $redirects[$index] );
          update_option( 'silver_redirects', array_values( $redirects ) );
      }

      ?>

      <div class="wrap">
        <h1>Silver Redirector</h1>
        <p>Redirects pages to other URLs on specific dates.</p>
        <form method="post">
          <table class="form-table">
            <tr>
              <th><label for="redirect_date">Redirect Date</label></th>
              <td><input type="date" id="redirect_date" name="redirect_date" required></td>
            </tr>
            <tr>
              <th><label for="from_url">From URL</label></th>
              <td><input type="text" id="from_url" name="from_url" required placeholder="Please start with https://"></td>
            </tr>
            <tr>
              <th><label for="to_url">To URL</label></th>
              <td><input type="text" id="to_url" name="to_url" required placeholder="Please start with https://"></td>
            </tr>
          </table>
          <button type="submit" name="add_redirect" class="button button-primary">Add Redirect</button>
        </form>

        <h2>Current Redirects</h2>
        
        <?php if ( empty( $redirects ) ) : ?>
          <p>No redirects have been set up yet.</p>
        <?php else : ?>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th>Date</th>
                <th>From URL</th>
                <th>To URL</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ( $redirects as $index => $redirect ) : ?>
                <tr>
                  <td><?php echo esc_html( date( 'd.m.Y', strtotime( $redirect['date'] ) ) ); ?></td>
                  <td><?php echo esc_html( $redirect['from_url'] ); ?></td>
                  <td><?php echo esc_html( $redirect['to_url'] ); ?></td>
                  <td>
                    <form method="post">
                      <input type="hidden" name="redirect_index" value="<?php echo $index; ?>">
                      <button type="submit" name="delete_redirect" class="button button-secondary">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <?php
  }
}