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
    public function __construct() {
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
        $where          = get_post_meta($post->ID, '_kwtsk_where', true) ?: 'everywhere';
        $priority       = get_post_meta($post->ID, '_kwtsk_priority', true) ?: '10';
        $selected_pages = get_post_meta($post->ID, '_kwtsk_selected_pages', true);
        $selected_pages = is_array($selected_pages) ? $selected_pages : [];
        $pages          = get_pages();
		$enabled        = get_post_meta($post->ID, '_kwtsk_enabled', true) !== '0'; // default ON
        $minify         = get_post_meta( $post->ID, '_kwtsk_minify', true ) === '1';

        wp_nonce_field('kwtsk_save_code_metabox', 'kwtsk_code_nonce');

        $languages = [
            'css'        => 'CSS',
            'javascript' => 'JavaScript',
            'php'        => 'PHP',
        ];

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
                    $options = [
                        'everywhere' => esc_html__('Everywhere (Admin & Frontend)', 'theme-site-kit'),
                        'admin'      => esc_html__('Admin', 'theme-site-kit'),
                        'frontend'   => esc_html__('Frontend', 'theme-site-kit'),
                        'specific'   => esc_html__('Specific Pages', 'theme-site-kit'),
                        'all_exclude'   => esc_html__('All Pages Except', 'theme-site-kit'),
                    ];
                    foreach ($options as $key => $label) {
                        echo '<option value="' . esc_attr($key) . '"' . selected($where, $key, false) . '>' . esc_html($label) . '</option>';
                    }
                    echo '</select>';
                echo '</div>'; // .kwtsk-code-setting-col

                echo '<div class="kwtsk-code-setting-col right" id="kwtsk_minify_opt">';
                    // Minify Toggle
                    echo '<label class="label" for="kwtsk_minify">' . esc_html__( 'Minify code when placed:', 'theme-site-kit' ) . '</label>';
                    echo '<label class="kwtsk-toggle-wrapper">';
                        echo '<input type="checkbox" name="kwtsk_minify" id="kwtsk_minify" class="kwtsk-toggle" value="1"' .
                            checked( $minify, true, false ) . '>';
                        echo '<span class="kwtsk-slider"></span>';
                    echo '</label>';
                echo '</div>';
            echo '</div>'; // .kwtsk-code-settings

            // Specific pages dropdown
            echo '<div id="kwtsk_specific_pages" class="kwtsk-select-pages">';
            echo '<div class="label">' . esc_html__('Select Pages:', 'theme-site-kit') . '</div>';
            echo '<div class="kwtsk-code-pages-opts">';

            // WooCommerce Pages checkbox
            $has_wc_all = in_array('woocommerce_all', $selected_pages);
            echo '<label>';
            echo '<input type="checkbox" name="kwtsk_selected_pages[]" value="woocommerce_all" ' . checked($has_wc_all, true, false) . '> ';
            echo esc_html__('All WooCommerce Pages', 'theme-site-kit') . '</label>';

            // Individual page checkboxes
            foreach ($pages as $page) {
                echo '<label>';
                echo '<input type="checkbox" name="kwtsk_selected_pages[]" value="' . esc_attr($page->ID) . '" ' . checked(in_array($page->ID, $selected_pages), true, false) . '> ';
                echo esc_html($page->post_title);
                echo '</label>';
            }

            echo '</div></div>';
        echo '</div>';
    }

    public function kwtsk_save_code_metabox($post_id) {
        if (!isset($_POST['kwtsk_code_nonce']) || !wp_verify_nonce($_POST['kwtsk_code_nonce'], 'kwtsk_save_code_metabox')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if ( ! current_user_can( 'manage_options' ) ) return; // ONLY Admin

        update_post_meta($post_id, '_kwtsk_code_language', sanitize_text_field($_POST['kwtsk_code_language'] ?? 'css'));
		update_post_meta($post_id, '_kwtsk_enabled', isset($_POST['kwtsk_enabled']) ? '1' : '0');
        
        $raw_code = isset( $_POST['kwtsk_code'] ) ? wp_unslash( $_POST['kwtsk_code'] ) : '';
        update_post_meta( $post_id, '_kwtsk_code', $raw_code );
        
        update_post_meta($post_id, '_kwtsk_where', sanitize_text_field($_POST['kwtsk_where'] ?? 'everywhere'));
        update_post_meta($post_id, '_kwtsk_priority', intval($_POST['kwtsk_priority'] ?? 10));
        update_post_meta( $post_id, '_kwtsk_minify', isset( $_POST['kwtsk_minify'] ) ? '1' : '0' );

        if (!empty($_POST['kwtsk_selected_pages']) && is_array($_POST['kwtsk_selected_pages'])) {
            update_post_meta($post_id, '_kwtsk_selected_pages', array_map('intval', $_POST['kwtsk_selected_pages']));
        } else {
            delete_post_meta($post_id, '_kwtsk_selected_pages');
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

    private function kwtsk_should_run_snippet( $where, array $selected_pages ) {
        $is_admin   = is_admin();
        $page_id    = $is_admin ? 0 : get_queried_object_id();      // 0 in admin
        $is_wc_page = function_exists( 'is_woocommerce' ) && is_woocommerce();

        switch ( $where ) {
            case 'everywhere':
                return true;

            case 'admin':
                return $is_admin;

            case 'frontend':
                return ! $is_admin;

            case 'specific':
                if ( ! $is_admin ) {
                    if ( in_array( 'woocommerce_all', $selected_pages, true ) && $is_wc_page ) {
                        return true;
                    }
                    return in_array( $page_id, $selected_pages, true );
                }
                return false;

            case 'all_exclude':
                if ( $is_admin ) {
                    return false; // never run in admin for this option
                }
                if ( in_array( 'woocommerce_all', $selected_pages, true ) && $is_wc_page ) {
                    return false;
                }
                return ! in_array( $page_id, $selected_pages, true );
        }

        return false;
    }

    public function kwtsk_register_enabled_snippets() {
        // Get *all* enabled snippets once.
        $snippets = get_posts( [
            'post_type'   => 'kwtsk_code_snippet',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query'  => [
                [
                    'key'   => '_kwtsk_enabled',
                    'value' => '1',
                ],
            ],
        ] );

        if ( empty( $snippets ) ) {
            return;
        }

        foreach ( $snippets as $snippet ) {
            $id             = $snippet->ID;
            $language       = get_post_meta( $id, '_kwtsk_code_language', true ) ?: 'css';
            $where          = get_post_meta( $id, '_kwtsk_where',         true ) ?: 'everywhere';
            $priority       = (int) get_post_meta( $id, '_kwtsk_priority', true );
            $selected_pages = get_post_meta( $id, '_kwtsk_selected_pages', true );
            $selected_pages = is_array( $selected_pages ) ? $selected_pages : [];

            if ( ! $this->kwtsk_should_run_snippet( $where, $selected_pages ) ) {
                continue;
            }

            $code = get_post_meta( $id, '_kwtsk_code', true );
            $should_minify = get_post_meta( $id, '_kwtsk_minify', true ) === '1';

            switch ( $language ) {
                /* ----------  CSS  ---------- */
                case 'css':
                    if ( is_admin() ) {
                        if ( $should_minify ) {
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
                        if ( $should_minify ) {
                            $code = $this->minify_js( $code );
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
        // Remove /* … */ comments
        $js = preg_replace( '#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#', '', $js );
        // Remove // … end-of-line comments
        $js = preg_replace( '#//.*$#m', '', $js );
        // Collapse whitespace outside quoted strings
        $tokens = preg_split( '/(["\'])(?:(?=(\\\\?))\2.)*?\1/', $js, -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
        for ( $i = 0; $i < count( $tokens ); $i += 3 ) {
            $tokens[ $i ] = preg_replace( '/\s+/', ' ', $tokens[ $i ] );
        }
        return trim( implode( '', $tokens ) );
    }
}

new KWTSK_Custom_Code();
