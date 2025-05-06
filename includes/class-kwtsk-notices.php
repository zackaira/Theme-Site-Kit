<?php
/**
 * Scripts & Styles file
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 */
class Theme_Site_Kit_Notices {
	/**
	 * Constructor funtion
	 */
	public function __construct() {
		add_action( 'admin_init', array($this, 'kwtsk_dismiss_notice' ), 0);
		add_action( 'admin_notices', array($this, 'kwtsk_add_update_notice' ));
	} // End __construct ()

	/**
	 * Add notices
	 */
	public function kwtsk_add_update_notice() {
		global $pagenow;
		global $current_user;
        $user_id = $current_user->ID;
		$kwtsk_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $pagenow . '?page=' . $_GET['page'] ) ) . '&' : sanitize_text_field( $pagenow ) . '?';

		$notices = $this->kwtsk_notices();

		$allowed_html = array(
			'b' => array('style' => array()),
		);

		if ( $pagenow == 'index.php' || $pagenow == 'plugins.php' || $pagenow == 'options-general.php' ) :

			if ( $notices ) :
				// Loop over all notices
				foreach ($notices as $notice) :

					if ( current_user_can( 'manage_options' ) && !get_user_meta( $user_id, 'kwtsk_notice_' . $notice['id'] . '_dismissed', true ) ) : ?>
						<div class="kwtsk-admin-notice notice notice-<?php echo isset($notice['type']) ? sanitize_html_class($notice['type']) : 'info'; ?>">
							<a href="<?php echo esc_url(admin_url($kwtsk_page . 'kwtsk_dismiss_notice&kwtsk-notice-id=' . $notice['id'])); ?>" class="notice-dismiss"></a>

							<div class="kwtsk-notice <?php echo isset($notice['inline']) ? esc_attr( 'inline' ) : ''; ?>">
								<?php if (isset($notice['title'])) : ?>
									<h4 class="kwtsk-notice-title"><?php echo wp_kses($notice['title'] ,$allowed_html); ?></h4>
								<?php endif; ?>

								<?php if (isset($notice['text'])) : ?>
									<p class="kwtsk-notice-text"><?php echo wp_kses($notice['text'] ,$allowed_html); ?></p>
								<?php endif; ?>

								<?php if (isset($notice['link']) && isset($notice['link_text'])) : ?>
									<a href="<?php echo esc_url($notice['link']); ?>" class="kwtsk-notice-btn">
										<?php echo esc_html($notice['link_text']); ?>
									</a>
								<?php endif; ?>
							</div>
						</div><?php
					endif;

				endforeach;
			endif;
			
		endif;
	}
	// Make Notice Dismissable
	public function kwtsk_dismiss_notice() {
		global $current_user;
		$user_id = $current_user->ID;

		if ( isset( $_GET['kwtsk_dismiss_notice'] ) ) {
			$kwtsk_notice_id = sanitize_text_field( $_GET['kwtsk-notice-id'] );
			add_user_meta( $user_id, 'kwtsk_notice_' .$kwtsk_notice_id. '_dismissed', 'true', true );
		}
    }

	/**
	 * Build Notices Array
	 */
	private function kwtsk_notices() {
		if ( !is_admin() )
			return;

		$settings = array();
		
		// $settings['new_blocks_added'] = array(
		// 	'id'    => 'newblocks_003', // Increment this when adding new blocks
		// 	'type'  => 'info', // info | error | warning | success
		// 	'title' => __( 'New Advans,fh vsj dfced Slider & Content Toggler blocks have been added to the Theme Site Kit plugin', 'theme-site-kit' ),
		// 	'text'  => __( 'To enable the new blocks and start using them in the WordPress editor:', 'theme-site-kit' ),
		// 	'link'  => admin_url( 'options-general.php?page=kwtsk-settings' ),
		// 	'link_text' => __( 'Go to the Theme Site Kit settings', 'theme-site-kit' ),
		// 	'inline' => true, // To display the link & text inline
		// );

		// $settings['new_settings'] = array(
		// 	'id'    => '01',
		// 	'type'  => 'info',
		// 	'title' => __( 'Theme Site Kit, manually added notice', 'theme-site-kit' ),
		// 	'text'  => __( 'Other notices can be added simply by adding then here in the code', 'theme-site-kit' ),
		// 	// 'link'  => admin_url( 'options-general.php?page=kwtsk-settings' ),
		// 	// 'link_text' => __( 'Go to Settings', 'theme-site-kit' ),
		// 	// 'inline' => true,
		// );

		return $settings;
	}
}
new Theme_Site_Kit_Notices();
