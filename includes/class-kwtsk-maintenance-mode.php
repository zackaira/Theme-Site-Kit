<?php
/**
 * Maintenance_Mode Setup & Functions
 *
 * @package Theme_Site_Kit
 * @since 1.0.0
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Maintenance_Mode Class
 * Handles all frontend-related functionality.
 */
class KWTSK_Maintenance_Mode {
    /**
     * Holds maintenance mode settings.
     *
     * @var object|null
     */
    private $settings;

    /**
     * Constructor function.
     */
    public function __construct() {
        // Retrieve saved options from the database.
        $kwtskSavedOptions = get_option( 'kwtsk_options' );
        $kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

        // Store maintenance settings if they exist.
        if ( isset( $kwtskOptions->maintenance ) ) {
            $this->settings = $kwtskOptions->maintenance;
        }

        // Initialize admin-related hooks (for admin bar and post states).
        $this->init_admin_hooks();

        // If maintenance mode is enabled, ensure page/template and front-end hook.
        if ( isset( $this->settings->enabled ) && $this->settings->enabled ) {
            add_action( 'init', [ $this, 'kwtsk_ensure_maintenance_page' ], 20 );
            $this->init_hooks();
        }

        add_action( 'rest_api_init', [ $this, 'kwtsk_register_rest_routes' ] );
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action( 'template_redirect', [ $this, 'kwtsk_display_maintenance_mode' ] );
    }

    /**
     * Initialize admin hooks.
     */
    private function init_admin_hooks() {
        add_action( 'admin_bar_menu',      [ $this, 'kwtsk_add_admin_bar_status' ], 100 );
        add_filter( 'display_post_states', [ $this, 'kwtsk_add_page_post_state' ], 10, 2 );
        add_action( 'pre_get_posts',       [ $this, 'kwtsk_hide_maintenance_page_in_admin' ], 10 );
    }

    /**
     * REST callback: ensure (or create) the maintenance page, then return its ID + edit URL.
     */
    public function kwtsk_register_rest_routes() {
        register_rest_route( 'kwtsk/v1', '/create-template-page', [
            'methods'  => 'POST',
            'callback' => [ $this, 'kwtsk_rest_create_maintenance_page' ],
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        ] );
    }
    public function kwtsk_rest_create_maintenance_page( \WP_REST_Request $req ) {
        // ensure page exists (will create if missing)
        $this->kwtsk_ensure_maintenance_page();

        $page_id = get_option( 'kwtsk_maintenance_page_id', 0 );
        if ( ! $page_id || ! get_post( $page_id ) ) {
            return new \WP_Error( 'cannot_create', 'Could not create maintenance page', [ 'status' => 500 ] );
        }

        return rest_ensure_response( [
            'page_id'   => $page_id,
            'page_title' => get_page( $page_id )->post_title,
        ] );
    }

    /**
     * Ensure our Maintenance Mode page exists and is assigned the template.
     */
    public function kwtsk_ensure_maintenance_page() {
        $page_id = get_option( 'kwtsk_maintenance_page_id', false );

        if ( ! $page_id || ! get_post( $page_id ) ) {
            $page_data = [
                'post_title'   => 'Maintenance Mode',
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ];
            $new_id = wp_insert_post( $page_data );
            if ( ! is_wp_error( $new_id ) ) {
                update_option( 'kwtsk_maintenance_page_id', $new_id );
            }
        }
    }

    /**
     * Exclude our Maintenance Mode page from the admin Pages list.
     *
     * @param WP_Query $query
     */
    public function kwtsk_hide_maintenance_page_in_admin( $query ) {
        if (
            is_admin() 
            && $query->is_main_query() 
            && 'page' === $query->get( 'post_type' ) 
            && ! defined( 'DOING_AJAX' )
        ) {
            $page_id = get_option( 'kwtsk_maintenance_page_id', false );
            if ( $page_id ) {
                $ex = (array) $query->get( 'post__not_in', [] );
                $ex[] = $page_id;
                $query->set( 'post__not_in', array_unique( $ex ) );
            }
        }
    }

    /**
     * Register & inject all maintenance-mode CSS.
     */
    public function kwtsk_get_maintenance_css() {
        $css = '';

        if ( (bool) kwtsk_fs()->can_use_premium_code__premium_only()
             && ! empty( $this->settings->template ) ) {
            $css .= "
                :root {
                    --wp--style--layout--content-size: 1040px;
                    --wp--style--layout--wide-size:    1240px;
                }
            ";
        } else {
            $bg   = $this->settings->bgcolor    ?? '#f5f5f5';
            $tc   = $this->settings->titlecolor ?? '#333';
            $txtc = $this->settings->textcolor  ?? '#666';
    
            $css .= "
                body {
                    background-color: {$bg} !important;
                    font-family: Arial, sans-serif !important;
                    text-align: center !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    font-size: 18px !important;
                }
                .kwtsk-mm-template-content {
                    height: 100vh;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 25px;
                    box-sizing: border-box;
                }
                .kwtsk-mm-template-content h1 {
                    font-size: clamp(34px, 4vw, 48px);
                    color: {$tc};
                    margin: 0 0 0.8rem;
                    padding: 0;
                }
                .kwtsk-mm-template-content p {
                    font-size: clamp(15px, 2vw, 18px);
                    color: {$txtc};
                    margin: 0;
                    padding: 0;
                }
            ";
        }

        return $css;
    }    

    /**
     * Display Maintenance Mode or Coming Soon Page.
     *
     * Intercepts normal page requests and shows a maintenance or coming soon page
     * unless the user is allowed to bypass it.
     */
    public function kwtsk_display_maintenance_mode() {
        $isPremium = (bool) kwtsk_fs()->can_use_premium_code__premium_only();

        // Bypass logic...
        if ( is_user_logged_in() ) {
            $access = $this->settings->access ?? 'loggedin';

            if ( 'loggedin' === $access ) {
                return;
            }

            if ( 'custom' === $access && ! empty( $this->settings->userroles ) && is_array( $this->settings->userroles ) ) {
                $current_user = wp_get_current_user();
                foreach ( $current_user->roles as $role ) {
                    if ( in_array( $role, $this->settings->userroles, true ) ) {
                        return;
                    }
                }
            }
        }

        $mode = isset( $this->settings->mode ) ? $this->settings->mode : '';
        if ( empty( $mode ) ) {
            return;
        }

        if ( 'maintenance' === $mode ) {
            $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PROTOCOL'] ) ) : 'HTTP/1.0';
            header( $protocol . ' 503 Service Unavailable', true, 503 );
            header( 'Retry-After: 3600' );
        }

        // Pro Template: load the registered block template
        if ( $isPremium && ! empty( $this->settings->template ) && get_post_status( $this->settings->template ) ) {
			$template_post = get_post( $this->settings->template );
			?>
			<!DOCTYPE html>
			<html <?php language_attributes(); ?>>
			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<?php wp_head(); // Loads styles and scripts including Gutenberg styles ?>
			</head>
			<body <?php body_class(); ?>>
                <?php wp_body_open(); ?>
                <div class="wp-site-blocks">
                    <div class="entry-content wp-block-post-content is-layout-flow wp-block-post-content-is-layout-flow">
                        <?php
                        // echo apply_filters( 'the_content', $template_post->post_content );
                        $allowed = wp_kses_allowed_html( 'post' );
                        $allowed['div']['data-*'] = true;
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_blocks() outputs trusted post content
                        echo do_blocks( $template_post->post_content );
                        ?>
                    </div>
                </div>
				<?php wp_footer(); ?>
			</body>
			</html>
			<?php
			exit;
		}

        // Fallback basic default template
        $css = $this->kwtsk_get_maintenance_css();

        $title = ! empty( $this->settings->title ) ? $this->settings->title : ( 'coming_soon' === $mode
                            ? esc_html__( 'Coming Soon', 'theme-site-kit' )
                            : esc_html__( 'Maintenance Mode', 'theme-site-kit' )
                        );
        $text = ! empty( $this->settings->text ) ? $this->settings->text : ( 'coming_soon' === $mode
                            ? esc_html__( 'Our website is launching soon. Stay tuned!', 'theme-site-kit' )
                            : esc_html__( 'We are currently performing scheduled maintenance. Please check back soon.', 'theme-site-kit' )
                        );
        wp_enqueue_style( 'kwtsk-frontend-style', false );
        wp_add_inline_style( 'kwtsk-frontend-style', $css );
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo esc_html( $title ); ?></title>
            <?php wp_head(); ?>
        </head>
        <body>
            <div class="kwtsk-mm-template-content">
                <h1><?php echo esc_html( $title ); ?></h1>
                <p><?php echo esc_html( $text ); ?></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Add an admin bar node that displays the maintenance mode status.
     *
     * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
     */
    public function kwtsk_add_admin_bar_status( $wp_admin_bar ) {
        if ( ! is_user_logged_in() ) {
            return;
        }
        if ( empty( $this->settings->enabled ) ) {
            return;
        }
        if ( ! empty( $this->settings->mode ) ) {
            $status           = esc_html__( 'Maintenance Mode: ON', 'theme-site-kit' );
            $additional_class = 'enabled';
        } else {
            $status           = esc_html__( 'Maintenance Mode: OFF', 'theme-site-kit' );
            $additional_class = 'disabled';
        }
        $args = [
            'id'    => 'kwtsk_maintenance_status',
            'title' => $status,
            'href'  => esc_url( admin_url( 'options-general.php?page=theme-site-kit-settings&tab=maintenance_mode' ) ),
            'meta'  => [ 'class' => esc_attr( 'kwtsk-maintenance-mode ' . $additional_class ) ],
        ];
        $wp_admin_bar->add_node( $args );
    }

    /**
     * Add a custom post state label in the Pages list if a page is used
     * as the maintenance/coming soon template.
     *
     * @param array  $post_states Array of post states.
     * @param object $post        The current post object.
     * @return array Modified array of post states.
     */
    public function kwtsk_add_page_post_state( $post_states, $post ) {
        if ( isset( $this->settings->template ) && absint( $this->settings->template ) === $post->ID ) {
            if ( 'coming_soon' === $this->settings->mode ) {
                $post_states[] = esc_html__( 'Coming Soon Page', 'theme-site-kit' );
            } else {
                $post_states[] = esc_html__( 'Maintenance Mode Page', 'theme-site-kit' );
            }
        }
        return $post_states;
    }
}

new KWTSK_Maintenance_Mode();
