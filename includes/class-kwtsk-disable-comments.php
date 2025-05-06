<?php
/**
 * Disable WordPress comments functionality
 *
 * @package Theme_Site_Kit
 */

/**
 * Comments Class
 * Handles all comments disable functionality
 */
class Theme_Site_Kit_Disable_Comments {
    /**
     * The disable mode: "everywhere" or "post_types"
     *
     * @var string
     */
    private $disableMode;

    /**
     * Array of post type slugs for which comments should be disabled (only in "post_types" mode)
     *
     * @var array
     */
    private $disabled_post_types = array();

    /**
     * Initialize the comment handler.
     */
    public function __construct() {
        $kwtskSavedOptions = get_option('kwtsk_options');
        $kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : null;

        // If the disablecomments option is not set or disable is false, do nothing.
        if (
            !isset($kwtskOptions->disablecomments) ||
            !isset($kwtskOptions->disablecomments->disable) ||
            $kwtskOptions->disablecomments->disable === false ||
            $kwtskOptions->disablecomments->disable === 'false' ||
            empty($kwtskOptions->disablecomments->disable)
        ) {
            return;
        }

        $this->disableMode = $kwtskOptions->disablecomments->disable;

        if ($this->disableMode === 'post_types') {
            // Get the array of post types to disable (or an empty array if none exist)
            $this->disabled_post_types = isset($kwtskOptions->disablecomments->post_types) && is_array($kwtskOptions->disablecomments->post_types)
                ? $kwtskOptions->disablecomments->post_types
                : array();

            // Add hooks only for the specified post types.
            add_action('init', array($this, 'kwtsk_remove_comment_support'), 100);
            add_action('after_setup_theme', array($this, 'kwtsk_disable_all_comments'));
            // For dynamically registered post types.
            add_filter('register_post_type_args', array($this, 'kwtsk_filter_post_type_args'), 99, 2);
        } elseif ($this->disableMode === 'everywhere') {
            // Full site‑wide disable: add all hooks.
            add_action('init', array($this, 'kwtsk_remove_comment_support'), 100);
            add_action('admin_menu', array($this, 'kwtsk_remove_comments_admin_menu'));
            add_action('after_setup_theme', array($this, 'kwtsk_disable_all_comments'));
            add_action('wp_before_admin_bar_render', array($this, 'kwtsk_remove_comments_admin_bar'));
            add_filter('rest_endpoints', array($this, 'kwtsk_disable_comments_endpoints'));
            add_action('admin_init', array($this, 'kwtsk_cleanup_admin'));
            add_filter('xmlrpc_methods', array($this, 'kwtsk_disable_xmlrpc_comments'));
            add_filter('rest_pre_serve_request', array($this, 'kwtsk_block_rest_api_comments'), 10, 4);
            add_filter('rest_prepare_post', array($this, 'kwtsk_remove_rest_api_comment_links'));
            add_filter('rest_prepare_page', array($this, 'kwtsk_remove_rest_api_comment_links'));
            add_action('admin_menu', array($this, 'kwtsk_remove_discussion_settings'), 999);
            add_action('admin_init', array($this, 'kwtsk_block_discussion_access'));
        }
    }

    /**
     * Filter arguments for dynamically registered post types in "post_types" mode.
     *
     * @param array  $args
     * @param string $post_type
     * @return array
     */
    public function kwtsk_filter_post_type_args($args, $post_type) {
        if (in_array($post_type, $this->disabled_post_types)) {
            if (isset($args['supports']) && is_array($args['supports'])) {
                $args['supports'] = array_diff($args['supports'], array('comments', 'trackbacks'));
            }
        }
        return $args;
    }

    /**
     * Remove comment support from post types.
     */
    public function kwtsk_remove_comment_support() {
        if ($this->disableMode === 'everywhere') {
            $post_types = get_post_types(array('public' => true), 'names');
            foreach ($post_types as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
            // For post types registered later.
            add_filter('register_post_type_args', function($args, $post_type) {
                if (isset($args['supports']) && is_array($args['supports'])) {
                    $args['supports'] = array_diff($args['supports'], array('comments', 'trackbacks'));
                }
                return $args;
            }, 99, 2);
        } elseif ($this->disableMode === 'post_types') {
            $post_types = get_post_types(array('public' => true), 'names');
            foreach ($post_types as $post_type) {
                if (in_array($post_type, $this->disabled_post_types)) {
                    if (post_type_supports($post_type, 'comments')) {
                        remove_post_type_support($post_type, 'comments');
                        remove_post_type_support($post_type, 'trackbacks');
                    }
                }
            }
            // For dynamically registered post types.
            add_filter('register_post_type_args', function($args, $post_type) {
                if (in_array($post_type, $this->disabled_post_types)) {
                    if (isset($args['supports']) && is_array($args['supports'])) {
                        $args['supports'] = array_diff($args['supports'], array('comments', 'trackbacks'));
                    }
                }
                return $args;
            }, 99, 2);
        }
    }

    /**
     * Remove comments from admin menu.
     * (Only used in "everywhere" mode.)
     */
    public function kwtsk_remove_comments_admin_menu() {
        remove_menu_page('edit-comments.php');
    }

    /**
     * Close comments on posts for affected post types.
     */
    public function kwtsk_disable_all_comments() {
        if ($this->disableMode === 'everywhere') {
            update_option('default_comment_status', 'closed');
            update_option('default_ping_status', 'closed');
            global $wpdb;
            $wpdb->query("UPDATE $wpdb->posts SET comment_status = 'closed', ping_status = 'closed'");
        } elseif ($this->disableMode === 'post_types') {
            if (!empty($this->disabled_post_types)) {
                update_option('default_comment_status', 'closed');
                update_option('default_ping_status', 'closed');
                global $wpdb;
                $types_in = "'" . implode("','", array_map('esc_sql', $this->disabled_post_types)) . "'";
                $wpdb->query("UPDATE $wpdb->posts SET comment_status = 'closed', ping_status = 'closed' WHERE post_type IN ($types_in)");
            }
        }
    }

    /**
     * Remove comments from admin bar.
     */
    public function kwtsk_remove_comments_admin_bar() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    }

    /**
     * Disable comments REST API endpoints.
     */
    public function kwtsk_disable_comments_endpoints($endpoints) {
        foreach ($endpoints as $route => $endpoint) {
            if (strpos($route, '/wp/v2/comments') === 0) {
                unset($endpoints[$route]);
            }
        }
        return $endpoints;
    }

    /**
     * Block REST API comments completely.
     */
    public function kwtsk_block_rest_api_comments($served, $result, $request, $server) {
        $route = $request->get_route();
        if (strpos($route, '/wp/v2/comments') === 0) {
            return new WP_Error(
                'rest_comments_disabled',
                __('Comments are disabled on this site.', 'theme-site-kit'),
                array('status' => 403)
            );
        }
        return $served;
    }

    /**
     * Remove comment-related links from REST API responses.
     */
    public function kwtsk_remove_rest_api_comment_links($response) {
        if (!empty($response->data['_links'])) {
            unset($response->data['_links']['replies']);
            unset($response->data['_links']['comments']);
        }
        return $response;
    }

    /**
     * Disable XML-RPC comment methods.
     */
    public function kwtsk_disable_xmlrpc_comments($methods) {
        unset($methods['wp.newComment']);
        unset($methods['wp.getCommentCount']);
        unset($methods['wp.getComment']);
        unset($methods['wp.getComments']);
        unset($methods['wp.deleteComment']);
        unset($methods['wp.editComment']);
        unset($methods['wp.getCommentStatusList']);
        unset($methods['wp.getRecentComments']);
        return $methods;
    }

    /**
     * Clean up admin dashboard and settings.
     */
    public function kwtsk_cleanup_admin() {
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);
        add_filter('comments_array', '__return_empty_array', 10, 2);
        add_filter('feed_links_show_comments_feed', '__return_false');
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    /**
     * Remove Discussion settings page.
     */
    public function kwtsk_remove_discussion_settings() {
        remove_submenu_page('options-general.php', 'options-discussion.php');
    }

    /**
     * Block access to discussion settings and display a message.
     */
    public function kwtsk_block_discussion_access() {
        global $pagenow;
        if ($pagenow === 'options-discussion.php' || (isset($_GET['page']) && $_GET['page'] === 'options-discussion.php')) {
            wp_die(
                '<p>' . esc_html__('Comments have been disabled.', 'theme-site-kit') . '</p>' .
                '<a href="' . admin_url() . '">' . esc_html__('Back to Dashboard', 'theme-site-kit') . '</a>'
            );
        }
    }
}
new Theme_Site_Kit_Disable_Comments();
