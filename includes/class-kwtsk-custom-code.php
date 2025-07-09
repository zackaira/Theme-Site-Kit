<?php
/**
 * Custom_Code Setup & Functions
 *
 * @package Theme_Site_Kit
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom_Code Class
 * Handles all frontend-related functionality
 */
class KWTSK_Custom_Code {
    private $isPro;
    
    public function __construct() {
        $this->isPro = (boolean)kwtsk_fs()->can_use_premium_code__premium_only();
        
        $kwtskSavedOptions = get_option('kwtsk_options');
        $kwtskOptions      = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : null;

        if (isset($kwtskOptions->code->enabled) && $kwtskOptions->code->enabled) {
            $this->kwtsk_init_hooks();
        }
    }

    private function kwtsk_init_hooks() {
        add_action('init', array($this, 'kwtsk_register_code_snippets_post_types'));
        add_action('admin_menu', array($this, 'kwtsk_add_snippets_admin_menu'));
        add_action('add_meta_boxes', array($this, 'kwtsk_add_code_metabox'));
        add_action('save_post', array($this, 'kwtsk_save_code_metabox'));
        add_action('admin_enqueue_scripts', array($this, 'kwtsk_enqueue_codemirror_editor'));
        // Display Snippets
        add_action( 'wp',          [ $this, 'kwtsk_register_enabled_snippets' ] );
        add_action( 'admin_init',  [ $this, 'kwtsk_register_enabled_snippets' ] );
    }

    public function kwtsk_register_code_snippets_post_types() {
        // $admin_cap = 'manage_options'; // ONLY Admin can edit

        register_post_type('kwtsk_code_snippet', array(
            'labels' => array(
                'name'          => __('Code Snippets', 'theme-site-kit'),
                'singular_name' => __('Code Snippet', 'theme-site-kit'),
                'add_new'       => __('Add New', 'theme-site-kit'),
                'add_new_item'  => __('Add New Code Snippet', 'theme-site-kit'),
                'edit_item'     => __('Edit Code Snippet', 'theme-site-kit'),
                'new_item'      => __('New Code Snippet', 'theme-site-kit'),
                'view_item'     => __('View Code Snippet', 'theme-site-kit'),
                'search_items'  => __('Search Code Snippets', 'theme-site-kit'),
                'not_found'     => __('No Code Snippets found', 'theme-site-kit'),
            ),
            'public'           => false,
            'show_ui'          => true,
            'show_in_menu'     => false,
            'show_in_rest'     => false,
            'supports'         => array('title'),
            'capability_type'  => 'post',
            'has_archive'      => false,
            'hierarchical'     => false,
            'rewrite'          => false,
            // 'capability_type'   => 'kwtsk_code_snippet',
            // 'map_meta_cap'      => true,
            // 'capabilities'      => [ // ONLY Admin
            //     'create_posts'         => $admin_cap,
            //     'edit_post'            => $admin_cap,
            //     'read_post'            => $admin_cap,
            //     'delete_post'          => $admin_cap,
            //     'edit_posts'           => $admin_cap,
            //     'edit_others_posts'    => $admin_cap,
            //     'delete_posts'         => $admin_cap,
            //     'delete_others_posts'  => $admin_cap,
            //     'publish_posts'        => $admin_cap,
            //     'read_private_posts'   => $admin_cap,
            // ],
        ));
    }

    public function kwtsk_add_snippets_admin_menu() {
        add_menu_page(
            __('Code Snippets', 'theme-site-kit'),
            __('Code Snippets', 'theme-site-kit'),
            'manage_options',
            'edit.php?post_type=kwtsk_code_snippet',
            '',
            'dashicons-editor-code',
            99
        );

        add_submenu_page(
            'edit.php?post_type=kwtsk_code_snippet',
            __('Import Snippets', 'theme-site-kit'),
            __('Import Snippets', 'theme-site-kit'),
            'manage_options',
            'kwtsk-import-snippets',
            array($this, 'kwtsk_render_import_snippets')
        );
    }

    public function kwtsk_render_import_snippets() {
        echo '<div class="wrap"><h1>' . esc_html__('Import Code Snippets', 'theme-site-kit') . '</h1>';
        echo '<p>' . esc_html__('Upload or paste your code snippets here to import.', 'theme-site-kit') . '</p>';
        echo '</div>';
    }

    public function kwtsk_add_code_metabox() {
        add_meta_box(
            'kwtsk_custom_code_box',
            __('Code Snippet', 'theme-site-kit'),
            array($this, 'kwtsk_render_code_metabox'),
            'kwtsk_code_snippet',
            'normal',
            'default'
        );
    }

    public function kwtsk_render_code_metabox($post) {
        $code           = get_post_meta($post->ID, '_kwtsk_code', true);
        $language       = get_post_meta($post->ID, '_kwtsk_code_language', true) ?: 'css';
        $where          = get_post_meta($post->ID, '_kwtsk_where', true) ?: '';
        $priority       = get_post_meta($post->ID, '_kwtsk_priority', true) ?: '10';
        $selected_items = get_post_meta($post->ID, '_kwtsk_selected_items', true); // Changed meta key
        $selected_items = is_array($selected_items) ? $selected_items : [];
        $enabled        = get_post_meta($post->ID, '_kwtsk_enabled', true) !== '0'; // default ON
        $minify         = get_post_meta($post->ID, '_kwtsk_minify', true) === '1';

        // Get all available content
        $pages = get_pages();
        $posts = get_posts(['posts_per_page' => -1]);
        $wc_pages = [];
        $wc_products = [];
        
        if (class_exists('WooCommerce')) {
            // Get published products
            $wc_products = get_posts([
                'post_type' => 'product',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ]);
            
            // Get WooCommerce specific pages
            $wc_pages = [
                ['id' => 'shop', 'title' => __('Shop Page', 'theme-site-kit')],
                ['id' => 'cart', 'title' => __('Cart Page', 'theme-site-kit')],
                ['id' => 'checkout', 'title' => __('Checkout Page', 'theme-site-kit')],
                ['id' => 'myaccount', 'title' => __('My Account Page', 'theme-site-kit')]
            ];
        }

        wp_nonce_field('kwtsk_save_code_metabox', 'kwtsk_code_nonce');

        $languages = [
            'css'        => 'CSS',
            'javascript' => 'JavaScript',
            'php'        => 'PHP',
        ];

        // Build where options array
        $options = [
            'everywhere' => esc_html__('Everywhere (Admin & Frontend)', 'theme-site-kit'),
            'admin'      => esc_html__('Admin', 'theme-site-kit'),
            'frontend'   => esc_html__('Frontend', 'theme-site-kit'),
            'pages'      => esc_html__('Select Pages', 'theme-site-kit') . (!$this->isPro ? ' (Pro)' : ''),
            'posts'      => esc_html__('Select Posts', 'theme-site-kit') . (!$this->isPro ? ' (Pro)' : ''),
        ];
            
        // Add WooCommerce options if active
        if (class_exists('WooCommerce')) {
            $options['woocommerce_pages'] = esc_html__('Select WooCommerce Pages', 'theme-site-kit') . (!$this->isPro ? ' (Pro)' : '');
            $options['woocommerce_products'] = esc_html__('Select WooCommerce Products', 'theme-site-kit') . (!$this->isPro ? ' (Pro)' : '');
        }

        echo '<div class="kwtsk-code-settings">';
            echo '<div class="kwtsk-code-setting-cols">';
                echo '<div class="kwtsk-code-setting-col">';
                    // Language Select
                    echo '<label class="label" for="kwtsk_code_language">' . esc_html__('Code Language:', 'theme-site-kit') . '</label>';
                    
                    echo '<select name="kwtsk_code_language" id="kwtsk_code_language">';
                    foreach ($languages as $value => $label) {
                        echo '<option value="' . esc_attr($value) . '"' . selected($language, $value, false) . '>' . esc_html($label) . '</option>';
                    }
                    echo '</select>';
                echo '</div>'; // .kwtsk-code-setting-col

                echo '<div class="kwtsk-code-setting-col right">';
                    // Priority Field
                    echo '<label class="label" for="kwtsk_priority">' . esc_html__('Priority:', 'theme-site-kit') . '</label>';
                    echo '<input type="number" id="kwtsk_priority" name="kwtsk_priority" value="' . esc_attr($priority) . '" style="width:80px;">';
                echo '</div>'; // .kwtsk-code-setting-col

                echo '<div class="kwtsk-code-setting-col right">';
                    // Enabled Toggle
                    echo '<label class="label" for="kwtsk_enabled">' . esc_html__('Enable Snippet:', 'theme-site-kit') . '</label>';
                    echo '<label class="kwtsk-toggle-wrapper">';
                        echo '<input type="checkbox" name="kwtsk_enabled" id="kwtsk_enabled" class="kwtsk-toggle" value="1"' . checked($enabled, true, false) . '>';
                        echo '<span class="kwtsk-slider"></span>';
                    echo '</label>';
                echo '</div>'; // .kwtsk-code-setting-col
            echo '</div>'; // .kwtsk-code-settings

            // CODE EDITOR
            echo '<div class="kwtsk-code-editor">';
                echo '<pre class="code-prefix"></pre>';
                echo '<textarea name="kwtsk_code" id="kwtsk_code">' . esc_textarea($code) . '</textarea>';
                echo '<pre class="code-suffix"></pre>';
            echo '</div>';
            

            echo '<div class="kwtsk-code-setting-cols">';
                echo '<div class="kwtsk-code-setting-col">';
                    // Where to display
                    echo '<label class="label" for="kwtsk_where">' . esc_html__('Where to add this code:', 'theme-site-kit') . '</label>';
                    echo '<select name="kwtsk_where" id="kwtsk_where">';
                    echo '<option value="">' . esc_html__('Select where to add this code', 'theme-site-kit') . '</option>';
                    foreach ($options as $key => $label) {
                        $is_pro_option = in_array($key, ['pages', 'posts', 'woocommerce_pages', 'woocommerce_products']);
                        echo '<option value="' . esc_attr($key) . '"' . 
                             selected($where, $key, false) . 
                             ($is_pro_option && !$this->isPro ? ' disabled' : '') . '>' . 
                             esc_html($label) . '</option>';
                    }
                    echo '</select>';
                echo '</div>'; // .kwtsk-code-setting-col

                
                echo '<div class="kwtsk-code-setting-col right ' . ( !$this->isPro ? 'toggle-disabled' : '' ) . '" id="kwtsk_minify_opt">';
                    // Minify Toggle
                    echo '<label class="label" for="kwtsk_minify">' . esc_html__( 'Minify code when placed:', 'theme-site-kit' ) . '</label>';
                    echo '<label class="kwtsk-toggle-wrapper">';
                        echo '<input type="checkbox" name="kwtsk_minify" id="kwtsk_minify" class="kwtsk-toggle" value="1" ' . ( !$this->isPro ? 'disabled' : '' ) . '"' .
                            checked( $minify, true, false ) . '>';
                        echo '<span class="kwtsk-slider"></span>';
                    echo '</label>';
                echo '</div>';
            echo '</div>'; // .kwtsk-code-settings

            if ($this->isPro) {
                // Pages selector
                echo '<div id="kwtsk_pages_select" class="kwtsk-select-pages">';
                echo '<div class="label">' . esc_html__('Select Pages:', 'theme-site-kit') . '</div>';
                echo '<p>' . esc_html__('If no pages selected, the code will be added to all pages.', 'theme-site-kit') . '</p>';
                echo '<select id="kwtsk_selected_pages" name="kwtsk_selected_items[]" multiple="multiple">'; // Changed name
                foreach ($pages as $page) {
                    echo '<option value="page_' . esc_attr($page->ID) . '" ' . 
                        selected(in_array('page_' . $page->ID, $selected_items), true, false) . '>' . 
                        esc_html($page->post_title) . '</option>';
                }
                echo '</select>';
                echo '</div>';

                // Posts selector
                echo '<div id="kwtsk_posts_select" class="kwtsk-select-pages">';
                echo '<div class="label">' . esc_html__('Select Posts:', 'theme-site-kit') . '</div>';
                echo '<p>' . esc_html__('If no posts selected, the code will be added to all posts.', 'theme-site-kit') . '</p>';
                echo '<select id="kwtsk_selected_posts" name="kwtsk_selected_items[]" multiple="multiple">'; // Changed name
                foreach ($posts as $post_item) {
                    echo '<option value="post_' . esc_attr($post_item->ID) . '" ' . 
                        selected(in_array('post_' . $post_item->ID, $selected_items), true, false) . '>' . 
                        esc_html($post_item->post_title) . '</option>';
                }
                echo '</select>';
                echo '</div>';

                // WooCommerce pages selector
                if (class_exists('WooCommerce')) {
                    // WooCommerce Pages
                    echo '<div id="kwtsk_wc_pages_select" class="kwtsk-select-pages">';
                    echo '<div class="label">' . esc_html__('Select WooCommerce Pages:', 'theme-site-kit') . '</div>';
                    echo '<p>' . esc_html__('If no pages selected, the code will be added to all WooCommerce pages.', 'theme-site-kit') . '</p>';
                    echo '<select id="kwtsk_selected_wc_pages" name="kwtsk_selected_items[]" multiple="multiple">'; // Changed name
                    foreach ($wc_pages as $wc_page) {
                        echo '<option value="wc_' . esc_attr($wc_page['id']) . '" ' . 
                            selected(in_array('wc_' . $wc_page['id'], $selected_items), true, false) . '>' . 
                            esc_html($wc_page['title']) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';

                    // WooCommerce Products
                    echo '<div id="kwtsk_wc_products_select" class="kwtsk-select-pages">';
                    echo '<div class="label">' . esc_html__('Select WooCommerce Products:', 'theme-site-kit') . '</div>';
                    echo '<p>' . esc_html__('If no products selected, the code will be added to all WooCommerce products.', 'theme-site-kit') . '</p>';
                    echo '<select id="kwtsk_selected_wc_products" name="kwtsk_selected_items[]" multiple="multiple">'; // Changed name
                    foreach ($wc_products as $product) {
                        echo '<option value="wc_product_' . esc_attr($product->ID) . '" ' . 
                            selected(in_array('wc_product_' . $product->ID, $selected_items), true, false) . '>' . 
                            esc_html($product->post_title) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                }
            }

            if (!$this->isPro) {
                echo '<div class="kwtsk-upgrade-pro">';
                    echo '<p class="kwtsk-code-setting-col-inner-text">' . esc_html__('Upgrade to Theme Site Kit Pro to unlock more advanced features!', 'theme-site-kit') . '</p>';
                    echo '<p class="kwtsk-code-setting-col-inner-text">' . esc_html__('Load your custom code only on the pages you choose!, Select posts, pages, or specificWooCommerce screens, and optimize your site even more with the added feature of minifying CSS and JavaScript file on the page.', 'theme-site-kit') . '</p>';
                    echo '<a href="' . esc_url(kwtsk_fs()->get_upgrade_url()) . '" target="_blank" class="pronote-btn">' . esc_html__('Upgrade to Pro', 'theme-site-kit') . '</a>';
                echo '</div>';
            }
        echo '</div>';
    }

    public function kwtsk_save_code_metabox($post_id) {
        if (!isset($_POST['kwtsk_code_nonce']) || !wp_verify_nonce($_POST['kwtsk_code_nonce'], 'kwtsk_save_code_metabox')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (!current_user_can('manage_options')) return; // ONLY Admin

        update_post_meta($post_id, '_kwtsk_code_language', sanitize_text_field($_POST['kwtsk_code_language'] ?? 'css'));
        update_post_meta($post_id, '_kwtsk_enabled', isset($_POST['kwtsk_enabled']) ? '1' : '0');
        
        $raw_code = isset($_POST['kwtsk_code']) ? wp_unslash($_POST['kwtsk_code']) : '';
        update_post_meta($post_id, '_kwtsk_code', $raw_code);
        
        update_post_meta($post_id, '_kwtsk_where', sanitize_text_field($_POST['kwtsk_where'] ?? ''));
        update_post_meta($post_id, '_kwtsk_priority', intval($_POST['kwtsk_priority'] ?? 10));
        update_post_meta($post_id, '_kwtsk_minify', isset($_POST['kwtsk_minify']) ? '1' : '0');

        // Save selected items
        if (!empty($_POST['kwtsk_selected_items']) && is_array($_POST['kwtsk_selected_items'])) {
            // Sanitize each item
            $sanitized_items = array_map(function($item) {
                return sanitize_text_field($item);
            }, $_POST['kwtsk_selected_items']);
            
            update_post_meta($post_id, '_kwtsk_selected_items', $sanitized_items);
        } else {
            delete_post_meta($post_id, '_kwtsk_selected_items');
        }
    }

    public function kwtsk_enqueue_codemirror_editor($hook) {
        global $post;
        if (!isset($post) || $post->post_type !== 'kwtsk_code_snippet') return;

        $language = get_post_meta($post->ID, '_kwtsk_code_language', true) ?: 'css';
        $mode     = $language;

        wp_enqueue_code_editor([
            'type'       => $mode,
            'codemirror' => [
                'mode'        => $mode,
                'lineNumbers' => true,
                'lineWrapping' => true,
                'extraKeys'   => ['Ctrl-Space' => 'autocomplete'],
            ]
        ]);
        wp_enqueue_script('wp-codemirror');
        wp_enqueue_style('wp-codemirror');
    }

    private function kwtsk_get_current_page_id() {
        if ( is_admin() ) {
            return 0;
        }

        if ( is_front_page() ) {
            return (int) get_option( 'page_on_front' );
        }

        if ( is_home() && get_option( 'page_for_posts' ) ) {
            return (int) get_option( 'page_for_posts' );
        }

        return (int) get_queried_object_id();
    }

    private function kwtsk_should_run_snippet($where, array $selected_items) {
        $is_admin = is_admin();
        $page_id = $this->kwtsk_get_current_page_id();

        switch ($where) {
            case 'everywhere':
                return true;

            case 'admin':
                return $is_admin;

            case 'frontend':
                return !$is_admin;

            case 'pages':
                if (!$is_admin && (is_page() || is_front_page() || (is_home() && !is_front_page()))) {
                    // If no pages are selected, run on all pages
                    if (empty($selected_items)) {
                        return true;
                    }
                    return in_array('page_' . $page_id, $selected_items);
                }
                return false;

            case 'posts':
                if (!$is_admin && is_single() && !is_singular('product')) {
                    // If no posts are selected, run on all posts
                    if (empty($selected_items)) {
                        return true;
                    }
                    return in_array('post_' . $page_id, $selected_items);
                }
                return false;

            case 'woocommerce_pages':
                if (!$is_admin && function_exists('is_woocommerce')) {
                    // Check if we're on a WooCommerce page
                    $is_wc_page = is_shop() || is_cart() || is_checkout() || is_account_page();
                    
                    if ($is_wc_page) {
                        // If no pages are selected, run on all WC pages
                        if (empty($selected_items)) {
                            return true;
                        }
                        
                        // Check specific WC pages
                        return (
                            (is_shop() && in_array('wc_shop', $selected_items)) ||
                            (is_cart() && in_array('wc_cart', $selected_items)) ||
                            (is_checkout() && in_array('wc_checkout', $selected_items)) ||
                            (is_account_page() && in_array('wc_myaccount', $selected_items))
                        );
                    }
                }
                return false;

            case 'woocommerce_products':
                if (!$is_admin && function_exists('is_product') && is_product()) {
                    // If no products are selected, run on all products
                    if (empty($selected_items)) {
                        return true;
                    }
                    return in_array('wc_product_' . $page_id, $selected_items);
                }
                return false;
        }

        return false;
    }

    public function kwtsk_register_enabled_snippets() {
        // Get *all* enabled snippets once.
        $snippets = get_posts([
            'post_type'   => 'kwtsk_code_snippet',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query'  => [
                [
                    'key'   => '_kwtsk_enabled',
                    'value' => '1',
                ],
            ],
        ]);

        if (empty($snippets)) {
            return;
        }

        foreach ($snippets as $snippet) {
            $id             = $snippet->ID;
            $language       = get_post_meta($id, '_kwtsk_code_language', true) ?: 'css';
            $where          = get_post_meta($id, '_kwtsk_where', true) ?: '';
            $priority       = (int) get_post_meta($id, '_kwtsk_priority', true);
            $selected_items = get_post_meta($id, '_kwtsk_selected_items', true); // Changed meta key
            $selected_items = is_array($selected_items) ? $selected_items : [];

            if (!$this->kwtsk_should_run_snippet($where, $selected_items)) {
                continue;
            }

            $code = get_post_meta($id, '_kwtsk_code', true);
            $should_minify = get_post_meta($id, '_kwtsk_minify', true) === '1';

            switch ( $language ) {
                /* ----------  CSS  ---------- */
                case 'css':
                    if ( is_admin() ) {
                        if ( $this->isPro && $should_minify ) {
                            $code = $this->minify_css( $code );
                        }

                        add_action(
                            'admin_head',
                            function() use ( $code, $id ) {
                                echo "<style id='kwtsk-snippet-{$id}'>{$code}</style>";
                            },
                            $priority
                        );
                    } else {
                        if ( $this->isPro && $should_minify ) {
                            $code = $this->minify_css( $code );
                        }

                        add_action(
                            'wp_head',
                            function() use ( $code, $id ) {
                                echo "<style id='kwtsk-snippet-{$id}'>{$code}</style>";
                            },
                            $priority
                        );
                    }
                    break;

                /* ----------  JavaScript  ---------- */
                case 'javascript':
                    $hook = is_admin() ? 'admin_footer' : 'wp_footer';

                    if ( $should_minify ) {
                        $code = $this->minify_js( $code );
                    }

                    add_action(
                        $hook,
                        function() use ( $code, $id ) {
                            echo "<script id='kwtsk-snippet-{$id}'>\n{$code}\n</script>";
                        },
                        $priority
                    );
                    break;

                /* ----------  PHP  ---------- */
                case 'php':
                default:
                    // Run ASAP but *after* plugins_loaded so WP is bootstrapped.
                    add_action(
                        'init',
                        function() use ( $code ) {
                            // Devs: consider sandboxing / try..catch.
                            eval( "?>{$code}" );
                        },
                        $priority
                    );
                    break;
            }
        }
    }

    /* ---------------------------------------------------------------------
    * Naïve-but-safe minifiers (no dependencies)
    * ------------------------------------------------------------------- */
    private function minify_css( $css ) {
        // Remove comments
        $css = preg_replace( '!/\*.*?\*/!s', '', $css );
        // Remove space after , : ; { } and before { }
        $css = preg_replace( '/\s*([{:;,}])\s*/', '$1', $css );
        // Collapse multiple spaces/newlines/tabs
        return trim( preg_replace( '/\s+/', ' ', $css ) );
    }

    private function minify_js( $js ) {
        // Remove comments and preserve newlines to handle regex correctly
        $js = preg_replace('!/\*.*?\*/!s', '', $js);
        $js = preg_replace('#^\s*//.*$#m', '', $js);
        
        // Preserve strings
        $strings = array();
        $placeholder = 'STRING_PLACEHOLDER_';
        
        // Preserve double-quoted strings
        $js = preg_replace_callback('/"([^"\\\\]|\\\\.)*"/', function($matches) use (&$strings, $placeholder) {
            $key = $placeholder . count($strings);
            $strings[$key] = $matches[0];
            return $key;
        }, $js);
        
        // Preserve single-quoted strings
        $js = preg_replace_callback('/\'([^\'\\\\]|\\\\.)*\'/', function($matches) use (&$strings, $placeholder) {
            $key = $placeholder . count($strings);
            $strings[$key] = $matches[0];
            return $key;
        }, $js);
        
        // Collapse whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove spaces around operators
        $js = preg_replace('/\s*([=+\-*\/%&|^<>!?:.,{}()[\];])\s*/', '$1', $js);
        
        // Restore strings
        foreach ($strings as $key => $string) {
            $js = str_replace($key, $string, $js);
        }
        
        return trim($js);
    }
}

new KWTSK_Custom_Code();
