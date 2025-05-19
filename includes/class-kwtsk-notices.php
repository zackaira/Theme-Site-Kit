<?php
/**
 * Scripts & Styles file
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 */
class KWTSK_Notices {
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
					$dismiss_url = wp_nonce_url(
						admin_url( $kwtsk_page . 'kwtsk_dismiss_notice&kwtsk-notice-id=' . $notice['id'] ),
						'kwtsk_dismiss_notice_' . $notice['id']
					);

					if ( current_user_can( 'manage_options' ) && !get_user_meta( $user_id, 'kwtsk_notice_' . $notice['id'] . '_dismissed', true ) ) : ?>
						<div class="kwtsk-admin-notice notice notice-<?php echo isset($notice['type']) ? sanitize_html_class($notice['type']) : 'info'; ?>">

							<a href="<?php echo esc_url( $dismiss_url ); ?>" class="notice-dismiss"></a>

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
		if ( empty( $_GET['kwtsk-notice-id'] ) || empty( $_GET['_wpnonce'] ) ) return;

        $notice_id = sanitize_text_field( wp_unslash( $_GET['kwtsk-notice-id'] ) );

		if ( ! current_user_can( 'manage_options' ) ) return;

        check_admin_referer( 'kwtsk_dismiss_notice_' . $notice_id );

        $user_id = get_current_user_id();
		add_user_meta( $user_id, "kwtsk_notice_{$notice_id}_dismissed", 'true', true );

		$redirect = wp_get_referer() ?: admin_url();
		$redirect = remove_query_arg( [ 'action', 'kwtsk-notice-id', '_wpnonce' ], $redirect );
		wp_safe_redirect( $redirect );
		exit;
    }

	/**
	 * Build Notices Array
	 */
	private function kwtsk_notices() {
		if ( !is_admin() )
			return;

		$settings = array();
		
		$settings['new_features'] = array(
			'id'    => 'kwtsk_001', // Increment this when adding new blocks
			'type'  => 'info', // info | error | warning | success
			'title' => __( 'Thank you for trying out Theme Site Kit!', 'theme-site-kit' ),
			'text'  => __( 'Get started by selecting which Site Kit features you want to enable on your website:', 'theme-site-kit' ),
			'link'  => admin_url( 'options-general.php?page=theme-site-kit-settings' ),
			'link_text' => __( 'View Theme Site Kit Settings', 'theme-site-kit' ),
			'inline' => true, // To display the link & text inline
		);

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
new KWTSK_Notices();
