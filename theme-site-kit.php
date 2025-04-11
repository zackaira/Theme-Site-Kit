<?php
/**
 * Plugin Name: Theme Site Kit
 * Version: 1.0.1
 * Plugin URI: https://kairaweb.com/wordpress-plugins/theme-pro/
 * Description: Easily manage essential site customizations with Theme Site Kit - the Swiss-Army-Knife WordPress plugin for disabling comments, enabling SVG uploads, adding social links, and more.
 * Author: Kaira
 * Author URI: https://kairaweb.com/
 * Requires at least: 5.0
 * Tested up to: 6.7
 * Text Domain: theme-site-kit
 * Domain Path: /lang/
 *
 * @package Theme Site Kit
 */
defined( 'ABSPATH' ) || exit;

if ( !defined( 'KWTSK_PLUGIN_VERSION' ) ) {
	define('KWTSK_PLUGIN_VERSION', '1.0.1');
}
if ( !defined( 'KWTSK_PLUGIN_URL' ) ) {
	define('KWTSK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'KWTSK_PLUGIN_DIR' ) ) {
	define('KWTSK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( function_exists( 'kwtsk_fs' ) ) {
	kwtsk_fs()->set_basename( true, __FILE__ );
} else {
	if ( ! function_exists( 'kwtsk_fs' ) ) {
		// Create a helper function for easy SDK access.
		function kwtsk_fs() {
			global $kwtsk_fs;

			if ( ! isset( $kwtsk_fs ) ) {
				// Include Freemius SDK.
				require_once dirname( __FILE__ ) . '/freemius/start.php';
				$kwtsk_fs = fs_dynamic_init( array(
					'id'                  => '18427',
					'slug'                => 'theme-site-kit',
					'premium_slug'        => 'theme-site-kit-pro',
					'type'                => 'plugin',
					'public_key'          => 'pk_5c04a3023f58b4d12779fae2d70ba',
					'is_premium'          => true,
					'premium_suffix'      => 'Pro',
					// If your plugin is a serviceware, set this option to false.
					'has_premium_version' => true,
					'has_addons'          => false,
					'has_paid_plans'      => true,
					'trial'               => array(
						'days'               => 7,
						'is_require_payment' => true,
					),
					'menu'                => array(
						'slug'           => 'theme-site-kit-settings',
						'contact'        => false,
						'support'        => false,
						'parent'         => array(
							'slug' => 'options-general.php',
						),
					),
				) );
			}

			return $kwtsk_fs;
		}

		// Init Freemius.
		kwtsk_fs();
		// Signal that SDK was initiated.
		do_action( 'kwtsk_fs_loaded' );
	}

	require_once 'includes/class-kwtsk-scripts.php';
	require_once 'includes/class-kwtsk-admin.php';
	require_once 'includes/class-kwtsk-block-patterns.php';
	require_once 'includes/class-kwtsk-frontend.php';
	require_once 'includes/class-kwtsk-rest-api.php';
	require_once 'includes/class-kwtsk-notices.php';
	require_once 'includes/class-kwtsk-disable-comments.php';
	require_once 'includes/class-kwtsk-svg-handler.php';
	require_once 'includes/class-kwtsk-post-types.php';
	require_once 'includes/class-kwtsk-maintenance-mode.php';

	/**
	 * Main instance of Theme_Site_Kit_Scripts to prevent the need to use globals
	 *
	 * @since  1.0.0
	 * @return object Theme_Site_Kit_Scripts
	 */
	function kwtsk() {
		$instance = Theme_Site_Kit_Scripts::instance( __FILE__, KWTSK_PLUGIN_VERSION );
		return $instance;
	}
	kwtsk();
}
