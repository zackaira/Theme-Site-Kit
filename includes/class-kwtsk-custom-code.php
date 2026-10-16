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
    private $snippets_registered = false;
    
    public function __construct() {
        $this->isPro = (boolean)kwtsk_fs()->can_use_premium_code__premium_only();
        
        $kwtskSavedOptions = get_option('kwtsk_options');
        $kwtskOptions      = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : null;

        if (isset($kwtskOptions->code->enabled) && $kwtskOptions->code->enabled) {
            $this->kwtsk_init_code_hooks();
        }
    }

    private function kwtsk_init_code_hooks() {
        add_action('init', array($this, 'kwtsk_register_code_snippets_post_types'));
        add_action('admin_menu', array($this, 'kwtsk_add_snippets_admin_menu'));
        add_action('add_meta_boxes', array($this, 'kwtsk_add_code_metabox'));
        add_action('save_post', array($this, 'kwtsk_save_code_metabox'));
        add_action('admin_enqueue_scripts', array($this, 'kwtsk_enqueue_codemirror_editor'));
        // Register before init/wp so PHP snippets can attach to early hooks.
        // CSS and JS still print from wp_head/wp_footer, after the query exists.
        add_action('plugins_loaded', array($this, 'kwtsk_register_enabled_snippets'), 0);
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
        if ('' === $where && 'auto-draft' === $post->post_status) {
            $where = 'frontend';
        }
        $priority       = get_post_meta($post->ID, '_kwtsk_priority', true) ?: '10';
        $selected_items = get_post_meta($post->ID, '_kwtsk_selected_items', true); // Changed meta key
        $selected_items = is_array($selected_items) ? $selected_items : [];
        $enabled        = get_post_meta($post->ID, '_kwtsk_enabled', true) !== '0'; // default ON
        $minify         = get_post_meta($post->ID, '_kwtsk_minify', true) === '1';
        $php_hook       = get_post_meta($post->ID, '_kwtsk_php_hook', true) ?: 'wp_head';
        $custom_hook    = get_post_meta($post->ID, '_kwtsk_custom_hook', true) ?: '';

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
                    if ('' === $where) {
                        echo '<option value="" selected="selected">' . esc_html__('Select where to add this code', 'theme-site-kit') . '</option>';
                    }
                    foreach ($options as $key => $label) {
                        $is_pro_option = in_array($key, ['pages', 'posts', 'woocommerce_pages', 'woocommerce_products']);
                        echo '<option value="' . esc_attr($key) . '"' . 
                             selected($where, $key, false) . 
                             ($is_pro_option && !$this->isPro ? ' disabled' : '') . '>' . 
                             esc_html($label) . '</option>';
                    }
                    echo '</select>';
                echo '</div>'; // .kwtsk-code-setting-col

                
                echo '<div class="kwtsk-code-setting-col right ' . ( !$this->isPro ? 'toggle-disabled' : '' ) . ( 'php' !== $language ? ' is-active' : '' ) . '" id="kwtsk_minify_opt">';
                    // Minify Toggle
                    echo '<label class="label" for="kwtsk_minify">' . esc_html__( 'Minify code when placed:', 'theme-site-kit' ) . '</label>';
                    echo '<label class="kwtsk-toggle-wrapper">';
                        echo '<input type="checkbox" name="kwtsk_minify" id="kwtsk_minify" class="kwtsk-toggle" value="1"' . ( !$this->isPro ? ' disabled' : '' ) . checked( $minify, true, false ) . '>';
                        echo '<span class="kwtsk-slider"></span>';
                    echo '</label>';
                echo '</div>';
            echo '</div>'; // .kwtsk-code-settings

            if ($this->isPro) {
                // Pages selector
                echo '<div id="kwtsk_pages_select" class="kwtsk-select-pages' . ( 'pages' === $where ? ' is-active' : '' ) . '">';
                echo '<div class="label">' . esc_html__('Select Pages:', 'theme-site-kit') . '</div>';
                echo '<p>' . esc_html__('If no pages selected, the code will be added to all pages.', 'theme-site-kit') . '</p>';
                echo '<select id="kwtsk_selected_pages"' . ( 'pages' === $where ? ' name="kwtsk_selected_items[]"' : '' ) . ' multiple="multiple">';
                foreach ($pages as $page) {
                    echo '<option value="page_' . esc_attr($page->ID) . '" ' . 
                        selected(in_array('page_' . $page->ID, $selected_items), true, false) . '>' . 
                        esc_html($page->post_title) . '</option>';
                }
                echo '</select>';
                echo '</div>';

                // Posts selector
                echo '<div id="kwtsk_posts_select" class="kwtsk-select-pages' . ( 'posts' === $where ? ' is-active' : '' ) . '">';
                echo '<div class="label">' . esc_html__('Select Posts:', 'theme-site-kit') . '</div>';
                echo '<p>' . esc_html__('If no posts selected, the code will be added to all posts.', 'theme-site-kit') . '</p>';
                echo '<select id="kwtsk_selected_posts"' . ( 'posts' === $where ? ' name="kwtsk_selected_items[]"' : '' ) . ' multiple="multiple">';
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
                    echo '<div id="kwtsk_wc_pages_select" class="kwtsk-select-pages' . ( 'woocommerce_pages' === $where ? ' is-active' : '' ) . '">';
                    echo '<div class="label">' . esc_html__('Select WooCommerce Pages:', 'theme-site-kit') . '</div>';
                    echo '<p>' . esc_html__('If no pages selected, the code will be added to all WooCommerce pages.', 'theme-site-kit') . '</p>';
                    echo '<select id="kwtsk_selected_wc_pages"' . ( 'woocommerce_pages' === $where ? ' name="kwtsk_selected_items[]"' : '' ) . ' multiple="multiple">';
                    foreach ($wc_pages as $wc_page) {
                        echo '<option value="wc_' . esc_attr($wc_page['id']) . '" ' . 
                            selected(in_array('wc_' . $wc_page['id'], $selected_items), true, false) . '>' . 
                            esc_html($wc_page['title']) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';

                    // WooCommerce Products
                    echo '<div id="kwtsk_wc_products_select" class="kwtsk-select-pages' . ( 'woocommerce_products' === $where ? ' is-active' : '' ) . '">';
                    echo '<div class="label">' . esc_html__('Select WooCommerce Products:', 'theme-site-kit') . '</div>';
                    echo '<p>' . esc_html__('If no products selected, the code will be added to all WooCommerce products.', 'theme-site-kit') . '</p>';
                    echo '<select id="kwtsk_selected_wc_products"' . ( 'woocommerce_products' === $where ? ' name="kwtsk_selected_items[]"' : '' ) . ' multiple="multiple">';
                    foreach ($wc_products as $product) {
                        echo '<option value="wc_product_' . esc_attr($product->ID) . '" ' . 
                            selected(in_array('wc_product_' . $product->ID, $selected_items), true, false) . '>' . 
                            esc_html($product->post_title) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                }
            }

            // PHP Hook Selection (only show for PHP language)
            echo '<div class="kwtsk-code-setting-cols kwtsk-php-hook-settings' . ( 'php' === $language ? ' is-active' : '' ) . '" id="kwtsk_php_hook_settings">';
                echo '<div class="kwtsk-code-setting-col">';
                    echo '<label class="label" for="kwtsk_php_hook">' . esc_html__('PHP Hook (where specifically to run):', 'theme-site-kit') . '</label>';
                    echo '<select name="kwtsk_php_hook" id="kwtsk_php_hook">';
                        // Show all hooks for both free and pro versions
                        $hook_options = [
                            'wp_head' => esc_html__('wp_head (In HTML head)', 'theme-site-kit'),
                            'wp_footer' => esc_html__('wp_footer (Before closing body)', 'theme-site-kit'),
                            'custom' => esc_html__('Custom Hook', 'theme-site-kit')
                        ];
                        
                        foreach ($hook_options as $value => $label) {
                            $is_pro_option = !in_array($value, ['wp_head', 'wp_footer']);
                            echo '<option value="' . esc_attr($value) . '"' . 
                                 selected($php_hook, $value, false) . 
                                 ($is_pro_option && !$this->isPro ? ' disabled' : '') . '>' . 
                                 esc_html($label) . ($is_pro_option && !$this->isPro ? ' (Pro)' : '') . '</option>';
                        }
                    echo '</select>';
                echo '</div>'; // .kwtsk-code-setting-col
                
                echo '<div class="kwtsk-code-setting-col right ' . ( !$this->isPro ? 'toggle-disabled' : '' ) . ( 'custom' === $php_hook ? ' is-active' : '' ) . '" id="kwtsk_custom_hook_field">';
                    echo '<label class="label" for="kwtsk_custom_hook">' . esc_html__('Custom Hook Name:', 'theme-site-kit') . '</label>';
                    echo '<input type="text" id="kwtsk_custom_hook" name="kwtsk_custom_hook" value="' . esc_attr($custom_hook) . '" placeholder="' . esc_attr__('e.g. plugins_loaded', 'theme-site-kit') . '" ' . ( !$this->isPro ? 'disabled' : '' ) . '>';
                echo '</div>'; // .kwtsk-code-setting-col
            echo '</div>'; // .kwtsk-code-settings

            if (!$this->isPro) {
                echo '<div class="kwtsk-upgrade-pro">';
                    echo '<p class="kwtsk-code-setting-col-inner-text">' . esc_html__('Upgrade to Theme Site Kit Pro to unlock more advanced features!', 'theme-site-kit') . '</p>';
                    echo '<p class="kwtsk-code-setting-col-inner-text">' . esc_html__('Load your custom code only on the pages you choose! Select posts, pages, or specific WooCommerce screens. Get advanced PHP custom hook options for precise control over when your code runs, and optimize your site even more with the added feature of minifying CSS and JavaScript files on the page.', 'theme-site-kit') . '</p>';
                    echo '<a href="' . esc_url(kwtsk_fs()->get_upgrade_url()) . '" target="_blank" class="pronote-btn">' . esc_html__('Upgrade to Pro', 'theme-site-kit') . '</a>';
                echo '</div>';
            }
        echo '</div>';
    }

    public function kwtsk_save_code_metabox($post_id) {
        if (get_post_type($post_id) !== 'kwtsk_code_snippet') return;
        if (!isset($_POST['kwtsk_code_nonce']) || !wp_verify_nonce($_POST['kwtsk_code_nonce'], 'kwtsk_save_code_metabox')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (!current_user_can('manage_options')) return; // ONLY Admin

        update_post_meta($post_id, '_kwtsk_code_language', sanitize_text_field($_POST['kwtsk_code_language'] ?? 'css'));
        update_post_meta($post_id, '_kwtsk_enabled', isset($_POST['kwtsk_enabled']) ? '1' : '0');
        
        $raw_code = isset($_POST['kwtsk_code']) ? wp_unslash($_POST['kwtsk_code']) : '';
        update_post_meta($post_id, '_kwtsk_code', $raw_code);

        $where = sanitize_text_field(wp_unslash($_POST['kwtsk_where'] ?? ''));
        $pro_locations = array('pages', 'posts', 'woocommerce_pages', 'woocommerce_products');
        if (!$this->isPro && in_array($where, $pro_locations, true)) {
            $where = 'frontend';
        }
        if ('' === $where) {
            $where = 'frontend';
        }
        
        update_post_meta($post_id, '_kwtsk_where', $where);
        update_post_meta($post_id, '_kwtsk_priority', intval($_POST['kwtsk_priority'] ?? 10));
        update_post_meta($post_id, '_kwtsk_minify', ($this->isPro && isset($_POST['kwtsk_minify'])) ? '1' : '0');
        
        // Save PHP hook settings
        $php_hook = sanitize_text_field($_POST['kwtsk_php_hook'] ?? 'wp_head');
        
        // Restrict hook options based on Pro status
        $allowed_hooks = ['wp_head', 'wp_footer'];
        if ($this->isPro) {
            $allowed_hooks[] = 'custom';
        }
        
        if (!in_array($php_hook, $allowed_hooks)) {
            $php_hook = 'wp_head'; // Default fallback
        }
        
        update_post_meta($post_id, '_kwtsk_php_hook', $php_hook);
        
        // Save custom hook only if Pro and it's being used and is valid
        if ($this->isPro && $php_hook === 'custom') {
            $custom_hook = sanitize_text_field($_POST['kwtsk_custom_hook'] ?? '');
            // Basic validation for hook name (alphanumeric, underscore, dash)
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $custom_hook)) {
                update_post_meta($post_id, '_kwtsk_custom_hook', $custom_hook);
            } else {
                // Clear invalid custom hook
                delete_post_meta($post_id, '_kwtsk_custom_hook');
            }
        } else {
            // Clear custom hook if not Pro or not using custom option
            delete_post_meta($post_id, '_kwtsk_custom_hook');
        }

        // Save selected items for the active placement only. Every selector posts
        // the same field name, so values from a hidden selector have to be dropped.
        $selected_items = array();
        if (!empty($_POST['kwtsk_selected_items']) && is_array($_POST['kwtsk_selected_items'])) {
            $selected_items = array_map('sanitize_text_field', wp_unslash($_POST['kwtsk_selected_items']));
            $selected_items = $this->kwtsk_filter_selected_items($where, $selected_items);
        }

        if (!empty($selected_items)) {
            update_post_meta($post_id, '_kwtsk_selected_items', $selected_items);
        } else {
            delete_post_meta($post_id, '_kwtsk_selected_items');
        }
    }

    public function kwtsk_enqueue_codemirror_editor($hook) {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || 'kwtsk_code_snippet' !== $screen->post_type || 'post' !== $screen->base) {
            return;
        }

        global $post;
        $language = 'css';
        if (isset($post->ID)) {
            $language = get_post_meta($post->ID, '_kwtsk_code_language', true) ?: 'css';
        }

        $mime_types = array(
            'css'        => 'text/css',
            'javascript' => 'application/javascript',
            'php'        => 'application/x-httpd-php',
        );
        $mode = isset($mime_types[$language]) ? $language : 'css';

        wp_enqueue_code_editor(array(
            'type'       => $mime_types[$mode],
            'codemirror' => array(
                'mode'         => $mode,
                'lineNumbers'  => true,
                'lineWrapping' => true,
                'extraKeys'    => array('Ctrl-Space' => 'autocomplete'),
            ),
        ));
        wp_enqueue_script('wp-codemirror');
        wp_enqueue_style('wp-codemirror');

        // Toggle classes instead of jQuery show/hide so the stylesheet keeps the flex layout.
        wp_add_inline_script('wp-codemirror', "
            jQuery(document).ready(function($) {
                function togglePHPHookSettings() {
                    var language = $('#kwtsk_code_language').val();
                    $('#kwtsk_php_hook_settings').toggleClass('is-active', language === 'php');
                }

                function toggleCustomHookField() {
                    var hookType = $('#kwtsk_php_hook').val();
                    $('#kwtsk_custom_hook_field').toggleClass('is-active', hookType === 'custom');
                }

                togglePHPHookSettings();
                toggleCustomHookField();

                $('#kwtsk_code_language').on('change', togglePHPHookSettings);
                $('#kwtsk_php_hook').on('change', toggleCustomHookField);
            });
        ");
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
        $pro_locations = array('pages', 'posts', 'woocommerce_pages', 'woocommerce_products');

        if (!$this->isPro && in_array($where, $pro_locations, true)) {
            return false;
        }

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
        if ($this->snippets_registered) {
            return;
        }
        $this->snippets_registered = true;

        $snippets = get_posts(array(
            'post_type'              => 'kwtsk_code_snippet',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'no_found_rows'          => true,
            'update_post_term_cache' => false,
            'meta_query'             => array(
                array(
                    'key'   => '_kwtsk_enabled',
                    'value' => '1',
                ),
            ),
        ));

        if (empty($snippets)) {
            return;
        }

        foreach ($snippets as $snippet) {
            $id             = $snippet->ID;
            $language       = get_post_meta($id, '_kwtsk_code_language', true) ?: 'css';
            $where          = get_post_meta($id, '_kwtsk_where', true) ?: '';
            $priority_raw   = get_post_meta($id, '_kwtsk_priority', true);
            $priority       = ('' === $priority_raw || false === $priority_raw) ? 10 : (int) $priority_raw;
            $selected_items = get_post_meta($id, '_kwtsk_selected_items', true);
            $selected_items = is_array($selected_items) ? $selected_items : array();
            $code           = get_post_meta($id, '_kwtsk_code', true);

            if (!is_string($code) || '' === trim($code)) {
                continue;
            }

            $should_minify = $this->isPro && get_post_meta($id, '_kwtsk_minify', true) === '1';

            switch ($language) {
                case 'css':
                    if ($should_minify) {
                        $code = $this->minify_css($code);
                    }
                    $code = $this->kwtsk_prepare_inline_css($code);
                    $callback = function() use ($code, $id, $where, $selected_items) {
                        if (!$this->kwtsk_should_run_snippet($where, $selected_items)) {
                            return;
                        }
                        echo '<style id="kwtsk-snippet-' . (int) $id . "\">\n" . $code . "\n</style>\n";
                    };
                    add_action('wp_head', $callback, $priority);
                    add_action('admin_head', $callback, $priority);
                    break;

                case 'javascript':
                    if ($should_minify) {
                        $code = $this->minify_js($code);
                    }
                    $code = $this->kwtsk_prepare_inline_js($code);
                    $callback = function() use ($code, $id, $where, $selected_items) {
                        if (!$this->kwtsk_should_run_snippet($where, $selected_items)) {
                            return;
                        }
                        echo '<script id="kwtsk-snippet-' . (int) $id . "\">\n" . $code . "\n</script>\n";
                    };
                    add_action('wp_footer', $callback, $priority);
                    add_action('admin_footer', $callback, $priority);
                    break;

                case 'php':
                    $php_hook    = get_post_meta($id, '_kwtsk_php_hook', true) ?: 'wp_head';
                    $custom_hook = get_post_meta($id, '_kwtsk_custom_hook', true) ?: '';
                    $this->kwtsk_schedule_php_snippet($code, $where, $selected_items, $priority, $php_hook, $custom_hook);
                    break;
            }
        }
    }

    /**
     * Schedule PHP snippet execution with proper placement logic and hook selection.
     */
    private function kwtsk_schedule_php_snippet($code, $where, $selected_items, $priority, $php_hook = 'wp_head', $custom_hook = '') {
        if (!$this->isPro && 'custom' === $php_hook) {
            $php_hook = 'wp_head';
            $custom_hook = '';
        }

        $hook_name = ('custom' === $php_hook && '' !== $custom_hook) ? $custom_hook : $php_hook;
        if (!is_string($hook_name) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $hook_name)) {
            $hook_name = 'wp_head';
        }

        $run = function() use ($code, $where, $selected_items) {
            if (!$this->kwtsk_should_run_snippet($where, $selected_items)) {
                return;
            }
            $this->kwtsk_run_php($code);
        };

        $specific = array('pages', 'posts', 'woocommerce_pages', 'woocommerce_products');
        if (in_array($where, $specific, true)) {
            add_action($this->kwtsk_get_placement_hook($hook_name), $run, $priority);
            return;
        }

        if ('admin' === $where || 'everywhere' === $where) {
            add_action($this->kwtsk_map_hook_for_admin($hook_name), function() use ($run) {
                if (is_admin()) {
                    $run();
                }
            }, $priority);
        }

        if ('frontend' === $where || 'everywhere' === $where) {
            add_action($hook_name, function() use ($run) {
                if (!is_admin()) {
                    $run();
                }
            }, $priority);
        }
    }

    /**
     * Run a PHP snippet without taking the site down when the snippet has an error.
     */
    private function kwtsk_run_php($code) {
        try {
            eval('?>' . $code);
        } catch (Throwable $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Theme Site Kit code snippet error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Map hooks appropriately for admin use.
     */
    private function kwtsk_map_hook_for_admin($hook_name) {
        switch ($hook_name) {
            case 'wp_head':
                return 'admin_head';
            case 'wp_footer':
                return 'admin_footer';
            default:
                return $hook_name;
        }
    }

    /**
     * Page-specific snippets need a hook that runs after the main query is set.
     */
    private function kwtsk_get_placement_hook($requested_hook) {
        $early_hooks = array('init', 'plugins_loaded', 'after_setup_theme', 'wp_loaded', 'wp');

        if (in_array($requested_hook, $early_hooks, true)) {
            return 'wp';
        }

        return $requested_hook;
    }

    /**
     * Keep only the selected items that belong to the active placement.
     */
    private function kwtsk_filter_selected_items($where, array $items) {
        $filtered = array();

        foreach ($items as $item) {
            $item = (string) $item;
            $keep = false;

            switch ($where) {
                case 'pages':
                    $keep = 0 === strpos($item, 'page_');
                    break;
                case 'posts':
                    $keep = 0 === strpos($item, 'post_');
                    break;
                case 'woocommerce_pages':
                    $keep = (bool) preg_match('/^wc_(shop|cart|checkout|myaccount)$/', $item);
                    break;
                case 'woocommerce_products':
                    $keep = 0 === strpos($item, 'wc_product_');
                    break;
            }

            if ($keep) {
                $filtered[] = $item;
            }
        }

        return array_values(array_unique($filtered));
    }

    /**
     * Drop wrapped style tags so a pasted stylesheet cannot close the snippet tag early.
     */
    private function kwtsk_prepare_inline_css($css) {
        $css = preg_replace('#</?style\b[^>]*>#i', '', $css);
        return is_string($css) ? $css : '';
    }

    /**
     * Keep a snippet from breaking out of its script tag.
     */
    private function kwtsk_prepare_inline_js($js) {
        $js = preg_replace('#<script\b[^>]*>#i', '', $js);
        $js = preg_replace('#</script#i', '<\\/script', $js);
        return is_string($js) ? $js : '';
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
