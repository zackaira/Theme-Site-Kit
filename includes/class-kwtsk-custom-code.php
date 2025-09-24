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
            $this->kwtsk_init_code_hooks();
        }
    }

    private function kwtsk_init_code_hooks() {
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

            // PHP Hook Selection (only show for PHP language)
            echo '<div class="kwtsk-code-setting-cols kwtsk-php-hook-settings" id="kwtsk_php_hook_settings">';
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
                
                echo '<div class="kwtsk-code-setting-col right ' . ( !$this->isPro ? 'toggle-disabled' : '' ) . '" id="kwtsk_custom_hook_field">';
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

        // Add custom JavaScript for PHP hook UI interactions
        wp_add_inline_script('wp-codemirror', "
            jQuery(document).ready(function($) {
                // Show/hide PHP hook settings based on language selection
                function togglePHPHookSettings() {
                    var language = $('#kwtsk_code_language').val();
                    if (language === 'php') {
                        $('#kwtsk_php_hook_settings').show();
                    } else {
                        $('#kwtsk_php_hook_settings').hide();
                    }
                }

                // Show/hide custom hook field based on hook selection
                function toggleCustomHookField() {
                    var hookType = $('#kwtsk_php_hook').val();
                    if (hookType === 'custom') {
                        $('#kwtsk_custom_hook_field').show();
                    } else {
                        $('#kwtsk_custom_hook_field').hide();
                    }
                }

                // Reset all selected items when placement type changes
                function resetSelectedItems() {
                    // Clear all multi-select fields
                    $('#kwtsk_selected_pages').val(null).trigger('change');
                    $('#kwtsk_selected_posts').val(null).trigger('change');
                    $('#kwtsk_selected_wc_pages').val(null).trigger('change');
                    $('#kwtsk_selected_wc_products').val(null).trigger('change');
                }

                // Initialize visibility
                togglePHPHookSettings();
                toggleCustomHookField();

                // Bind events
                $('#kwtsk_code_language').on('change', togglePHPHookSettings);
                $('#kwtsk_php_hook').on('change', toggleCustomHookField);
                
                // Reset selected items when placement type changes
                $('#kwtsk_where').on('change', function() {
                    var whereValue = $(this).val();
                    
                    // Only reset if changing to a specific placement type that uses selectors
                    if (['pages', 'posts', 'woocommerce_pages', 'woocommerce_products'].includes(whereValue)) {
                        resetSelectedItems();
                    }
                });
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

            // For PHP snippets, skip the placement check here as it's handled in kwtsk_schedule_php_snippet
            if ($language !== 'php' && !$this->kwtsk_should_run_snippet($where, $selected_items)) {
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
                    // Get PHP hook settings
                    $php_hook = get_post_meta($id, '_kwtsk_php_hook', true) ?: 'wp';
                    $custom_hook = get_post_meta($id, '_kwtsk_custom_hook', true) ?: '';
                    
                    // For PHP snippets, we need to run the placement check at the right time
                    // when WordPress has determined the current page context
                    $this->kwtsk_schedule_php_snippet($code, $where, $selected_items, $priority, $php_hook, $custom_hook);
                    break;
            }
        }
    }

    /**
     * Schedule PHP snippet execution with proper placement logic and hook selection
     */
    private function kwtsk_schedule_php_snippet($code, $where, $selected_items, $priority, $php_hook = 'wp_head', $custom_hook = '') {
        // Determine the actual hook to use
        $hook_name = $php_hook === 'custom' && !empty($custom_hook) ? $custom_hook : $php_hook;
        
        // For admin-only snippets
        if ($where === 'admin') {
            $admin_hook = $this->kwtsk_map_hook_for_admin($hook_name);
            add_action($admin_hook, function() use ($code) {
                eval("?>{$code}");
            }, $priority);
            return;
        }

        // For frontend-only snippets
        if ($where === 'frontend') {
            add_action($hook_name, function() use ($code) {
                eval("?>{$code}");
            }, $priority);
            return;
        }

        // For everywhere snippets, run on both admin and frontend
        if ($where === 'everywhere') {
            // Admin side
            $admin_hook = $this->kwtsk_map_hook_for_admin($hook_name);
            add_action($admin_hook, function() use ($code) {
                eval("?>{$code}");
            }, $priority);
            
            // Frontend side
            add_action($hook_name, function() use ($code) {
                eval("?>{$code}");
            }, $priority);
            return;
        }

        // For specific page/post/WooCommerce placement
        // For placement-specific snippets, we need to ensure WordPress context is available
        $placement_hook = $this->kwtsk_get_placement_hook($hook_name);
        
        // Frontend execution with placement check
        $self = $this; // Capture $this for use in closure
        add_action($placement_hook, function() use ($code, $where, $selected_items, $self) {
            if (!is_admin() && $self->kwtsk_should_run_snippet($where, $selected_items)) {
                eval("?>{$code}");
            }
        }, $priority);

        // Admin execution with placement check (for editing pages/posts)
        add_action('admin_init', function() use ($code, $where, $selected_items, $self) {
            if (is_admin() && $self->kwtsk_should_run_snippet($where, $selected_items)) {
                eval("?>{$code}");
            }
        }, $priority);
    }

    /**
     * Map hooks appropriately for admin use
     */
    private function kwtsk_map_hook_for_admin($hook_name) {
        // Map frontend hooks to admin equivalents
        switch ($hook_name) {
            case 'wp_head':
                return 'admin_head';
            case 'wp_footer':
                return 'admin_footer';
            default:
                // For custom hooks, use admin_init as fallback
                return in_array($hook_name, ['wp_head', 'wp_footer']) ? $hook_name : 'admin_init';
        }
    }

    /**
     * Get appropriate hook for placement-specific snippets
     */
    private function kwtsk_get_placement_hook($requested_hook) {
        // For placement-specific snippets, we need hooks that run after query is set
        // wp_head and wp_footer are fine, but early hooks need to be mapped
        $early_hooks = ['init', 'plugins_loaded', 'after_setup_theme', 'wp_loaded'];
        
        if (in_array($requested_hook, $early_hooks)) {
            // Use 'wp' instead for placement logic to work
            return 'wp';
        }
        
        return $requested_hook;
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
