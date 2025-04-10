<?php
/**
 * Maintenance_Mode Setup & Functions
 *
 * @package Theme_Site_Kit
 * @since 1.0.0
 */

/**
 * Maintenance_Mode Class
 * Handles all frontend-related functionality.
 */
class Theme_Site_Kit_Maintenance_Mode {
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

        // Check if maintenance mode is enabled.
        if ( isset( $this->settings->enabled ) && $this->settings->enabled ) {
            $this->init_hooks();
        }
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Hook into template_redirect so we can intercept the front-end.
        add_action( 'template_redirect', array( $this, 'display_maintenance_mode' ) );
    }

	/**
     * Initialize admin hooks.
     */
    private function init_admin_hooks() {
        // Add a status node to the admin bar.
        add_action('admin_bar_menu', array($this, 'kwtsk_add_admin_bar_status'), 100);
        // Add a post state label for the selected maintenance mode page.
        add_filter('display_post_states', array($this, 'kwtsk_add_page_post_state'), 10, 2);
    }

    /**
     * Display Maintenance Mode or Coming Soon Page.
     *
     * Intercepts normal page requests and shows a maintenance or coming soon page
     * unless the user is allowed to bypass it.
     */
    public function display_maintenance_mode() {
        // Check if the current user should bypass maintenance mode.
        if ( is_user_logged_in() && isset( $this->settings->access ) ) {
            // If access is set to "loggedin", all logged-in users bypass maintenance mode.
            if ( 'loggedin' === $this->settings->access ) {
                return;
            }
            // If access is set to "custom", then only users whose roles match those in userroles array may bypass.
            elseif ( 'custom' === $this->settings->access ) {
                if ( ! empty( $this->settings->userroles ) && is_array( $this->settings->userroles ) ) {
                    $current_user = wp_get_current_user();
                    foreach ( $current_user->roles as $role ) {
                        if ( in_array( $role, $this->settings->userroles, true ) ) {
                            return; // Allowed to bypass maintenance mode.
                        }
                    }
                }
            }
        }

        // Determine the selected mode. (Empty means no maintenance mode is set up.)
        $mode = isset( $this->settings->mode ) ? $this->settings->mode : '';

        // If no mode is selected, then load normally.
        if ( empty( $mode ) ) {
            return;
        }

        // Set the proper HTTP headers based on the selected mode.
        if ( 'maintenance' === $mode ) {
            header( $_SERVER["SERVER_PROTOCOL"] . ' 503 Service Unavailable', true, 503 );
            header( 'Retry-After: 3600' ); // Advise the client to retry after 1 hour.
        }
        // "Coming Soon" mode stays as HTTP 200.

        // If a custom template page is specified, display that page content.
        if ( ! empty( $this->settings->template ) && get_post_status( $this->settings->template ) ) {
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
				<?php
				// Render the post content through the content filter
				echo apply_filters( 'the_content', $template_post->post_content );
				?>
				<?php wp_footer(); ?>
			</body>
			</html>
			<?php
			exit;
		}

		$bgcolor = isset( $this->settings->bgcolor ) ? $this->settings->bgcolor : '#f5f5f5';
		
		$title = isset( $this->settings->title ) && $this->settings->title != "" ? $this->settings->title :
			( 'coming_soon' === $mode ? esc_html__( 'Coming Soon', 'theme-site-kit' ) : esc_html__( 'Maintenance Mode', 'theme-site-kit' ) );
		$titlecolor = isset( $this->settings->titlecolor ) ? $this->settings->titlecolor : '#f5f5f5';
		
		$text = isset( $this->settings->text ) && $this->settings->text != "" ? $this->settings->text :
		 ( 'coming_soon' === $mode ? esc_html__( 'Our website is launching soon. Stay tuned!', 'theme-site-kit' ) : esc_html__( 'We are currently performing scheduled maintenance. Please check back soon.', 'theme-site-kit' ) );
		$textcolor = isset( $this->settings->textcolor ) ? $this->settings->textcolor : '#f5f5f5';

        // Fallback default content if no custom template is specified.
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>
                <?php esc_html_e($title) ?>
            </title>
            <style>
                    body {
                        background-color: <?php esc_attr_e($bgcolor) ?>;
                        font-family: Arial, sans-serif;
                        text-align: center;
						margin: 0;
						padding: 0;
						font-size: 18px;
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
                        color: <?php esc_attr_e($titlecolor) ?>;
                        margin: 0 0 0.8rem;
						padding: 0;
                    }
                    .kwtsk-mm-template-content p {
						font-size: clamp(15px, 2vw, 18px);
                        color: <?php esc_attr_e($textcolor) ?>;
						margin: 0;
						padding: 0;
                    }
                </style>
        </head>
        <body>
			<div class="kwtsk-mm-template-content">
				<h1>
					<?php esc_html_e($title) ?>
				</h1>
				<p>
					<?php esc_html_e($text) ?>
				</p>
			</div>
        </body>
        </html>
        <?php

        exit; // Stop any further execution.
    }

	/**
     * Add an admin bar node that displays the maintenance mode status.
     *
     * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
     */
    public function kwtsk_add_admin_bar_status( $wp_admin_bar ) {
		// Only display the node if the user is logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}
	
		// Only add the node if a maintenance mode is selected.
		if ( empty( $this->settings->enabled ) ) {
			return;
		}
	
		// Set the status simply based on whether maintenance is enabled or not.
		if ( isset( $this->settings->mode ) && $this->settings->mode != "" ) {
			$status = esc_html__( 'Maintenance Mode: ON', 'theme-site-kit' );
			$additional_class = 'enabled';
		} else {
			$status = esc_html__( 'Maintenance Mode: OFF', 'theme-site-kit' );
			$additional_class = 'disabled';
		}
	
		// Build the complete class string.
		$class = 'kwtsk-maintenance-mode ' . $additional_class;
	
		$args = array(
			'id'    => 'kwtsk_maintenance_status',
			'title' => $status ,
			'href'  => admin_url( 'options-general.php?page=theme-site-kit-settings&tab=maintenance_mode' ),
			'meta'  => array( 'class' => esc_attr( $class ) )
		);
		$wp_admin_bar->add_node( $args );
	}	

    /**
     * Add a custom post state label in the Pages list if a page is used as the maintenance/coming soon template.
     *
     * @param array  $post_states Array of post states.
     * @param object $post        The current post object.
     * @return array Modified array of post states.
     */
    public function kwtsk_add_page_post_state( $post_states, $post ) {
        if (isset($this->settings->template) && absint($this->settings->template) === $post->ID) {
            if (isset($this->settings->mode) && 'coming_soon' === $this->settings->mode) {
                $post_states[] = esc_html__( 'Coming Soon Page', 'theme-site-kit' );
            } else {
                $post_states[] = esc_html__( 'Maintenance Mode Page', 'theme-site-kit' );
            }
        }
        return $post_states;
    }
}

new Theme_Site_Kit_Maintenance_Mode();
