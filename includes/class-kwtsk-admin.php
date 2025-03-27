<?php
/**
 * Admin Settings & Setup file.
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Admin class.
 */
class Theme_Site_Kit_Admin {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		// Load blocks
		// add_action('init', array( $this, 'kwtsk_load_blocks' ));
		
		add_action('admin_menu', array( $this, 'kwtsk_create_admin_menu' ), 10, 1);
		add_filter('plugin_action_links_theme-site-kit/theme-site-kit.php', array($this, 'kwtsk_add_plugins_settings_link'));

		// add_filter('block_categories_all', array($this, 'kwtsk_blocks_custom_category'), 10, 2);

		add_filter('admin_body_class', array($this, 'kwtsk_admin_body_classes'));

		// Add Dashboard Widget
		add_action('wp_dashboard_setup', array( $this, 'kwtsk_dashboard_widget' ));
	}

	/**
	 * Load blocks based on the options set.
	 */
	// public function kwtsk_load_blocks() {
	// 	$blocks = array(
	// 		"disclosure" => false, // 1
	// 	);

	// 	if ($blocks) :
	// 		foreach ($blocks as $blockName => $enabled) {
	// 			$prefix = substr($blockName, 0, 3);

	// 			if (file_exists(KWTP_PLUGIN_DIR . 'build/' . str_replace("_", "-", $blockName) . '/index.php')) {
	// 				require KWTP_PLUGIN_DIR . 'build/' . str_replace("_", "-", $blockName) . '/index.php';
	// 			}
	// 		}
	// 	endif;
	// }

	/**
	 * Create an Admin Sub-Menu under WooCommerce
	 */
	public function kwtsk_create_admin_menu() {
		$capability = 'manage_options';

		add_submenu_page(
			'options-general.php',
			__('Theme Site Kit', 'theme-site-kit'),
			__('Theme Site Kit', 'theme-site-kit'),
			$capability,
			'theme-site-kit-settings',
			array($this, 'kwtsk_menu_page_template')
		);

		add_submenu_page(
			'themes.php',
			__('Theme Site Kit', 'theme-site-kit'),
			__('Theme Site Kit', 'theme-site-kit'),
			$capability,
			'theme-site-kit-layouts',
			array($this, 'kwtsk_menu_layouts_page_template')
		);
	}

	/**
	 * Create a Setting link on Plugins.php page
	 */
	public function kwtsk_add_plugins_settings_link($links) {
		$settings_link = '<a href="options-general.php?page=theme-site-kit-settings">' . esc_html__('Settings', 'theme-site-kit') . '</a>';
		array_push( $links, $settings_link );
		
  		return $links;
	}

	/**
	 * Create the Page Template html for React
	 * Settings created in ../src/backend/settings/admin.js
	 */
	public function kwtsk_menu_page_template() {
		$allowed_html = array(
			'div' => array('class' => array(), 'id' => array()),
			'h2' => array(),
		);

		$html  = '<div class="wrap">' . "\n";
		$html .= '<h2> </h2>' . "\n";
		$html .= '<div id="kwtsk-root"></div>' . "\n";
		$html .= '</div>' . "\n";

		echo wp_kses($html ,$allowed_html);
	}

	/**
	 * Create the Page Template html for React
	 * Settings created in ../src/backend/settings/admin.js
	 */
	public function kwtsk_menu_layouts_page_template() {
		$allowed_html = array(
			'div' => array('class' => array(), 'id' => array()),
			'h2' => array(),
		);

		$html  = '<div class="wrap">' . "\n";
		$html .= '<h2> </h2>' . "\n";
		$html .= '<div id="kwtsk-layouts-root"></div>' . "\n";
		$html .= '</div>' . "\n";

		echo wp_kses($html ,$allowed_html);
	}

	/**
	 * Create Theme Site Kit blocks Category
	 */
	// public function kwtsk_blocks_custom_category($categories, $post) {
	// 	return array_merge(
	// 		$categories,
	// 		array(
	// 			array(
	// 				"slug" => "kwtsk-category",
	// 				"title" => __("Theme Site Kit Blocks", "theme-site-kit"),
	// 			)
	// 		)
	// 	);
	// }

	/**
	 * Function to check for active plugins
	 */
	public static function kwtsk_is_plugin_active($plugin_name) {
		// Get Active Plugin Setting
		$active_plugins = (array) get_option('active_plugins', array());
		if (is_multisite()) {
			$active_plugins = array_merge($active_plugins, array_keys(get_site_option( 'active_sitewide_plugins', array())));
		}

		$plugin_filenames = array();
		foreach ($active_plugins as $plugin) {
			if (false !== strpos( $plugin, '/') ) {
				// normal plugin name (plugin-dir/plugin-filename.php)
				list(, $filename ) = explode( '/', $plugin);
			} else {
				// no directory, just plugin file
				$filename = $plugin;
			}
			$plugin_filenames[] = $filename;
		}
		return in_array($plugin_name, $plugin_filenames);
	}

	/**
	 * Function to check for active plugins
	 */
	public function kwtsk_admin_body_classes($admin_classes) {
		$admin_classes .= ' ' . sanitize_html_class('kwtsk-pro');

		return $admin_classes;
	}

	/**
	 * Create a Dashboard Widget for Theme Site Kit
	 */
	public function kwtsk_dashboard_widget() {
		$kwtskSavedOptions = get_option('kwtsk_options');
		$kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : '';

		wp_add_dashboard_widget(
			'kwtsk_dashboard_widget',
			'WIDGET TITLE', array( $this, 'kwtsk_render_dashboard_widget' )
		);
	}

	/**
	 * Render the Dashboard Widget info
	 */
	public function kwtsk_render_dashboard_widget() {
		echo '<div id="kwtsk-dashboard-widget"></div>';
	}
}
new Theme_Site_Kit_Admin();
