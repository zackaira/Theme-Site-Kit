<?php
/**
 * Maintenance_Mode Setup & Functions
 *
 * @package Theme_Site_Kit
 * @since 1.0.0
 */

/**
 * Maintenance_Mode Class
 * Handles all frontend-related functionality
 */
class Theme_Site_Kit_Maintenance_Mode {
	/**
     * Constructor function.
     */
    public function __construct() {
        $kwtskSavedOptions = get_option( 'kwtsk_options' );
		$kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

		// Check if maintenance mode is enabled
		if ( isset( $kwtskOptions->maintenance->enabled ) && $kwtskOptions->maintenance->enabled ) {
			$this->kwtsk_init_hooks();
		}
    }

    /**
     * Initialize hooks
     */
    private function kwtsk_init_hooks() {
		// This hook will check for maintenance mode on each page load
		add_action( 'template_redirect', array( $this, 'display_maintenance_mode' ) );
	}

	/**
	 * Display Maintenance Mode Page.
	 *
	 * This method intercepts normal page requests and shows a maintenance mode page.
	 */
	public function display_maintenance_mode() {
		// Here you would build your logic to display the maintenance page.
		// For example, you could load a custom template and then exit.
		// If a user is allowed (e.g., based on IP or login status), you might bypass this.
		if ( ! current_user_can( 'edit_posts' ) ) { // Example condition
			include( plugin_dir_path( __FILE__ ) . 'templates/maintenance-page.php' );
			exit;
		}
	}
}
