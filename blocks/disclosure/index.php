<?php
/**
 * Plugin Name: Disclosure Block
 * Plugin URI: https://github.com/WordPress/theme-site-kit
 * Description: An Affiliate Disclosure Block.
 * Version: 1.1.0
 * Author: Kaira
 *
 * @package theme-site-kit
 */
defined( 'ABSPATH' ) || exit;

/**
 * Register Block Assets
 */
function kwtsk_disclosure_register_block() {
	register_block_type( __DIR__ );

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'kwtsk-disclosure-editor-script', 'theme-site-kit', KWTP_PLUGIN_DIR . 'lang' );
	}

}
add_action( 'init', 'kwtsk_disclosure_register_block' );
