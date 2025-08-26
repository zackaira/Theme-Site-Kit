<?php
/**
 * Scripts & Styles file
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin class.
 */
class KWTSK_Scripts {
	/**
	 * The single instance of KWTSK_Scripts {
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * The version number
	 */
	public $_version; //phpcs:ignore

	/**
	 * The main plugin file.
	 */
	public $file;

	/**
	 * Constructor funtion
	 */
	public function __construct($file = '', $version = KWTSK_PLUGIN_VERSION) {
		$this->file     = $file;
		$this->_version = $version;

		register_activation_hook( $this->file, array( $this, 'kwtsk_on_plugin_activation' ) );
		// Ensure install() runs when the plugin updates too
		add_action( 'admin_init', array( $this, 'kwtsk_check_plugin_updates' ) );

		// Register Scripts for plugin.
		add_action( 'init', array( $this, 'kwtsk_register_scripts' ), 10 );

		// Update/fix defaults on plugins_loaded hook
		add_action( 'plugins_loaded', array( $this, 'kwtsk_update_plugin_defaults' ) );

		// Load Frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'kwtsk_frontend_scripts' ), 10 );

		// Load Admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'kwtsk_admin_scripts' ), 10, 1 );

		// Load Editor JS & CSS.
		add_action( 'enqueue_block_editor_assets', array( $this, 'kwtsk_block_editor_scripts' ), 10, 1 );
	} // End __construct ()

	/**
	 * Register Scripts & Styles
	 */
	public function kwtsk_register_scripts() {
		$suffix = (defined('WP_DEBUG') && true === WP_DEBUG) ? '' : '.min';
		$isPro = (boolean)kwtsk_fs()->can_use_premium_code__premium_only();
		$kwtskSavedOptions = get_option('kwtsk_options');
		$kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : '';

		// Font Awesome Free
		wp_register_style('kwtsk-fontawesome', esc_url(KWTSK_PLUGIN_URL . 'assets/font-awesome/css/all.min.css'), array(), KWTSK_PLUGIN_VERSION);
		
		// Frontend
		wp_register_style('kwtsk-frontend-style', esc_url(KWTSK_PLUGIN_URL . 'dist/frontend' . $suffix . '.css'), array('kwtsk-fontawesome'), KWTSK_PLUGIN_VERSION);
		wp_register_script('kwtsk-frontend-script', esc_url(KWTSK_PLUGIN_URL . 'dist/frontend' . $suffix . '.js'), array('wp-i18n'), KWTSK_PLUGIN_VERSION);

		// Custom Mobile Menu (add custom-mobile-menu class to Nav Block)
		if ($isPro) {
			wp_register_style( 'kwtsk-mobile-menu-style', esc_url(KWTSK_PLUGIN_URL) . 'dist/pro/mobile-menu.min.css', array(), KWTSK_PLUGIN_VERSION );
			wp_register_script( 'kwtsk-mobile-menu-script', esc_url(KWTSK_PLUGIN_URL) . 'dist/pro/mobile-menu.min.js', array(), KWTSK_PLUGIN_VERSION, true );
		}

		// Admin
		wp_register_style('kwtsk-admin-style', esc_url(KWTSK_PLUGIN_URL . 'dist/admin' . $suffix . '.css'), array(), KWTSK_PLUGIN_VERSION);
		wp_register_script('kwtsk-admin-script', esc_url(KWTSK_PLUGIN_URL . 'dist/admin' . $suffix . '.js'), array(), KWTSK_PLUGIN_VERSION, true);

		// Settings
		wp_register_style('kwtsk-admin-settings-style', esc_url(KWTSK_PLUGIN_URL . 'dist/settings' . $suffix . '.css'), array('kwtsk-fontawesome'), KWTSK_PLUGIN_VERSION);
		wp_register_script('kwtsk-admin-settings-script', esc_url(KWTSK_PLUGIN_URL . 'dist/settings' . $suffix . '.js'), array('wp-element', 'wp-i18n'), KWTSK_PLUGIN_VERSION, true);

		// Page Layouts
		wp_register_style('kwtsk-admin-layouts-style', esc_url(KWTSK_PLUGIN_URL . 'dist/layouts' . $suffix . '.css'), array('kwtsk-fontawesome'), KWTSK_PLUGIN_VERSION);
		wp_register_script('kwtsk-admin-layouts-script', esc_url(KWTSK_PLUGIN_URL . 'dist/layouts' . $suffix . '.js'), array('wp-element', 'wp-components', 'wp-i18n'), KWTSK_PLUGIN_VERSION, true);

		// Editor
		wp_register_style('kwtsk-admin-editor-style', esc_url(KWTSK_PLUGIN_URL . 'dist/editor' . $suffix . '.css'), array('kwtsk-fontawesome'), KWTSK_PLUGIN_VERSION);
		wp_register_script('kwtsk-admin-editor-script', esc_url(KWTSK_PLUGIN_URL . 'dist/editor' . $suffix . '.js'), array('wp-edit-post', 'lodash'), KWTSK_PLUGIN_VERSION, true);

		// Code Snippets
		if (isset($kwtskOptions->code->enabled) && $kwtskOptions->code->enabled == true) {
			wp_register_style('kwtsk-code-snippets-style', esc_url(KWTSK_PLUGIN_URL . 'dist/code-snippets' . $suffix . '.css'), array(), KWTSK_PLUGIN_VERSION);
			wp_register_script('kwtsk-code-snippets-script', esc_url(KWTSK_PLUGIN_URL . 'dist/code-snippets' . $suffix . '.js'), array(), KWTSK_PLUGIN_VERSION, true);
		}
	} // End kwtsk_register_scripts ()

	/**
	 * Load frontend Scripts & Styles
	 */
	public function kwtsk_frontend_scripts() {
		$suffix = (defined('WP_DEBUG') && true === WP_DEBUG) ? '' : '.min';
		$isPro = (boolean)kwtsk_fs()->can_use_premium_code__premium_only();
		$kwtskSavedOptions = get_option('kwtsk_options');
		$kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : '';

		// Frontend CSS
		wp_enqueue_style('kwtsk-frontend-style');
		wp_enqueue_script('kwtsk-frontend-script');
		wp_localize_script('kwtsk-frontend-script', 'kwtskFObj', array(
			'apiUrl' => esc_url(get_rest_url()),
			'nonce' => wp_create_nonce('wp_rest'),
			'kwtskOptions' => $kwtskOptions->social,
			'isPremium' => $isPro,
		));

		// Custom Mobile Menu
        if ( $isPro && isset($kwtskOptions->mobilemenu->enabled) && $kwtskOptions->mobilemenu->enabled == true ) {
			wp_enqueue_style( 'kwtsk-mobile-menu-style' );
			wp_enqueue_script( 'kwtsk-mobile-menu-script' );
			wp_localize_script('kwtsk-mobile-menu-script', 'kwtskMMObj', array(
				'isPremium' => $isPro,
				'menuWidth' => isset($kwtskOptions->mobilemenu->width) ? $kwtskOptions->mobilemenu->width : '250px',
			));
        }
	} // End kwtsk_frontend_scripts ()

	/**
	 * Admin enqueue Scripts & Styles
	 */
	public function kwtsk_admin_scripts( $hook = '') {
		global $pagenow;
		global $kwtsk_fs;
		$adminPage = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : $pagenow;
		$suffix = (defined('WP_DEBUG') && true === WP_DEBUG) ? '' : '.min';
		$isPro = (boolean)kwtsk_fs()->can_use_premium_code__premium_only();

		$kwtskSavedOptions = get_option('kwtsk_options');
		$kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : '';
		$kwtskDefaults = get_option('kwtsk_default_options');

		// Admin CSS
		wp_enqueue_style('kwtsk-admin-style');

		// Admin JS
		wp_enqueue_script('kwtsk-admin-script');
		wp_localize_script('kwtsk-admin-script', 'kwtskAObj', array(
			'apiUrl' => esc_url( get_rest_url() ),
			'nonce' => wp_create_nonce('wp_rest'),
		));
		

		wp_set_script_translations('kwtsk-admin-script', 'theme-site-kit', KWTSK_PLUGIN_DIR . 'lang');

		// Admin Settings Page
		if ('theme-site-kit-settings' == $adminPage) {
			wp_enqueue_style('kwtsk-frontend-style');
			wp_enqueue_style('kwtsk-admin-settings-style');
		
			// Get all post types
			$all_post_types = get_post_types(array('public' => true), 'objects');
			$excluded_types = array('product', 'attachment', 'media', 'revision', 'nav_menu_item', 'linkt', 'elementor_library');
			// Filter out the excluded post types
			$filtered_post_types = array_filter($all_post_types, function($post_type, $post_type_name) use ($excluded_types) {
				return ! in_array($post_type_name, $excluded_types);
			}, ARRAY_FILTER_USE_BOTH);

			// Get all page templates
			// $templates = get_page_templates();
			// Filter out unwanted templates
			// $templates = array_filter($templates, function($template_file, $template_name) {
			// 	$excluded_terms = ['elementor', 'archive', 'order-confirmation', 'single-product'];
			// 	foreach ($excluded_terms as $term) {
			// 		if (stripos($template_file, $term) !== false || stripos($template_name, $term) !== false) {
			// 			return false;
			// 		}
			// 	}
			// 	return true;
			// }, ARRAY_FILTER_USE_BOTH);
			
			$pages_for_maintenance_mode = $this->kwtsk_get_maintenance_mode_page();
		
			// Get all available user roles and format as { role_slug: Role Name, ... }
			$user_roles_raw = function_exists('get_editable_roles') ? get_editable_roles() : array();
			$user_roles = array();
			foreach ($user_roles_raw as $role_key => $role_data) {
				$user_roles[$role_key] = $role_data['name'];
			}
		
			wp_enqueue_script('kwtsk-admin-settings-script');
			wp_localize_script('kwtsk-admin-settings-script', 'kwtskSObj', array(
				'apiUrl'         => esc_url(get_rest_url()),
				'nonce'          => wp_create_nonce('wp_rest'),
				'adminUrl'       => esc_url(admin_url()),
				'isPremium'      => $isPro,
				'post_types'     => $filtered_post_types,
				'kwtskOptions'   => $kwtskOptions,
				'accountUrl'     => esc_url($kwtsk_fs->get_account_url()),
				'upgradeUrl'     => esc_url($kwtsk_fs->get_upgrade_url()),
				'publishedPages' => $pages_for_maintenance_mode,
				'userRoles'      => $user_roles,
			));
			// wp_enqueue_media();
		}

		// Page layouts admin page
		if ('theme-site-kit-layouts' == $adminPage) {
			wp_enqueue_style('kwtsk-admin-layouts-style');
			wp_enqueue_script('kwtsk-admin-layouts-script');
			wp_localize_script('kwtsk-admin-layouts-script', 'kwtskLObj', array(
				'apiUrl' => esc_url(get_rest_url()),
				'adminUrl' => esc_url(admin_url()),
				'nonce' => wp_create_nonce('wp_rest'),
				'isPremium' => $isPro,
				'canSvg' => $kwtskOptions->svgupload->enabled,
				'upgradeUrl' => esc_url($kwtsk_fs->get_upgrade_url()),
			));
			// wp_enqueue_media();
		}

		// Code Snippets
		if (
			( 'edit.php' === $pagenow || 'post-new.php' === $pagenow || 'post.php' === $pagenow ) &&
			( 'kwtsk_code_snippet' === get_post_type((int) $_GET['post']) )
		) {
			if ( isset( $kwtskOptions->code->enabled ) && $kwtskOptions->code->enabled == true ) {
				wp_enqueue_style('kwtsk-code-snippets-style');
				wp_enqueue_script('kwtsk-code-snippets-script');
			}
		}
		
		// Update the language file with this line in the terminal - "wp i18n make-pot ./ lang/theme-site-kit.pot"
		wp_set_script_translations('kwtsk-admin-settings-script', 'theme-site-kit', KWTSK_PLUGIN_DIR . 'lang');
		// wp_set_script_translations('kwtsk-dashboard-script', 'theme-site-kit', KWTSK_PLUGIN_DIR . 'lang');
	} // End kwtsk_admin_scripts ()

	/**
	 * Get all published pages excluding WooCommerce, static pages, privacy policy, and returns page.
	 *
	 * @return array
	 */
	public function kwtsk_get_maintenance_mode_page() {
		$page_id = get_option( 'kwtsk_maintenance_page_id', false );
		$page = get_page($page_id);

		// Format result
		$pages_for_js = array();
		$pages_for_js[$page->ID] = $page->post_title;

		return $page_id ? $pages_for_js : false;
	}

	/**
	 * Load Block Editor Scripts & Styles
	 */
	public function kwtsk_block_editor_scripts() {
		$suffix = (defined('WP_DEBUG') && true === WP_DEBUG) ? '' : '.min';
		$kwtskSavedOptions = get_option('kwtsk_options');
		$kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : '';
		
		wp_enqueue_style('kwtsk-admin-editor-style');

		wp_enqueue_script('kwtsk-admin-editor-script');
		wp_localize_script('kwtsk-admin-editor-script', 'kwtskEObj', array(
			'kwtskOptions' => $kwtskOptions,
		));
		
	} // End kwtsk_block_editor_scripts ()

	/**
	 * Main KWTSK_Scripts { Instance
	 * Ensures only one instance of KWTSK_Scripts {is loaded or can be loaded.
	 */
	public static function instance( $file = '', $version = KWTSK_PLUGIN_VERSION) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	public static function kwtskDefaults() {
		$defaultSettings = array(
				"disablecomments" => array(
					"disable" => false,
					"post_types" => [],
				),
				"mobilemenu" => array(
					"enabled" => false,
					"position" => "right",
					"style" => "dark",
					"bgcolor" => "#1d2327",
					"textcolor" => "#b4b4b4",
					"selectedcolor" => "#ffffff",
					"width" => "250px",
				),
				"svgupload" => array(
					"enabled" => true,
				),
				"social" => array(
					"enabled" => false,
					"preview" => false,
					"position" => "right-top",
					"offset" => 80,
					"style" => "rounder",
					"spacing" => 8,
					"icons" => [],
					"iconbgcolor" => "#ffffff",
					"iconcolor" => "#000000",
					"iconsize" => "medium",
					"showbg" => true,
					"showtext" => false,
					"iconorigcolor" => false,
				),
				"cpts" => array(
					"enabled" => false,
					"post_types" => array(),
				),
				"maintenance" => array(
					"enabled" => false,
					"mode" => "",
					"access" => "loggedin",
					"userroles" => array(),
					"template" => "",
					"bgcolor" => "#ffffff",
					"title" => "",
					"titlecolor" => "#333",
					"text" => "",
					"textcolor" => "#666",
				),
				"code" => array(
					"enabled" => false,
				),
		);
		return $defaultSettings;
	}

	/**
	 * Update/Save the plugin version, defaults and settings if none exist | Run on 'plugins_loaded' hook
	 */
	public function kwtsk_update_plugin_defaults() {
		$defaultOptions = (object)$this->kwtskDefaults();
		$objDefaultOptions = json_encode($defaultOptions);

		// Saved current Plugin Version if no version is saved
		if (!get_option('kwtsk_plugin_version') || (get_option('kwtsk_plugin_version') != KWTSK_PLUGIN_VERSION)) {
			update_option('kwtsk_plugin_version', KWTSK_PLUGIN_VERSION);
		}
		// Fix/Update Defaults if no defaults are saved or if defaults are different to previous version defaults
		if (!get_option('kwtsk_default_options') || (get_option('kwtsk_default_options') != $defaultOptions)) {
			update_option('kwtsk_default_options', $objDefaultOptions);
		}
		// Save new Plugin Settings from defaults if no settings are saved
		if (!get_option('kwtsk_options')) {
			update_option('kwtsk_options', $objDefaultOptions);
		}
	}

	/**
	 * Installation. Runs on activation.
	 */
	public function kwtsk_on_plugin_activation() {
		$this->kwtsk_update_plugin_version();
		$this->kwtsk_update_default_settings();
	}

	/**
	 * Checks if the plugin version or DB version has changed
	 */
	public function kwtsk_check_plugin_updates() {
		$saved_plugin_version = get_option('kwtsk_plugin_version');

		// Update plugin version if it has changed
		if ($saved_plugin_version !== KWTSK_PLUGIN_VERSION) {
			$this->kwtsk_update_plugin_version();
		}
	}

	/**
	 * Updates the stored plugin version
	 */
	private function kwtsk_update_plugin_version() {
		update_option('kwtsk_plugin_version', KWTSK_PLUGIN_VERSION);
	}
	
	/**
	 * Save Initial Default Settings.
	 */
	private function kwtsk_update_default_settings() { //phpcs:ignore
		$defaultOptions = (object)$this->kwtskDefaults();
		
		update_option('kwtsk_options', json_encode($defaultOptions));
		update_option('kwtsk_default_options', json_encode($defaultOptions));
	}
}
