<?php
/**
 * Maintenance mode setup and front-end handling.
 *
 * @package Theme_Site_Kit
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shows a maintenance or coming soon screen, and keeps the template page private.
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
		$kwtsk_saved_options = get_option( 'kwtsk_options' );
		$kwtsk_options       = $kwtsk_saved_options ? json_decode( $kwtsk_saved_options ) : null;

		if ( isset( $kwtsk_options->maintenance ) ) {
			$this->settings = $kwtsk_options->maintenance;
		}

		$this->kwtsk_init_maintenance_admin_hooks();

		add_action( 'init', array( $this, 'kwtsk_privatize_maintenance_page' ), 20 );
		add_action( 'rest_api_init', array( $this, 'kwtsk_register_rest_routes' ) );
		add_action( 'template_redirect', array( $this, 'kwtsk_display_maintenance_mode' ) );

		if ( $this->kwtsk_is_active() ) {
			$this->kwtsk_init_maintenance_frontend_hooks();
		}
	}

	/**
	 * Front-end hooks. Registered only while maintenance or coming soon is on.
	 */
	private function kwtsk_init_maintenance_frontend_hooks() {
		add_action( 'init', array( $this, 'kwtsk_mark_do_not_cache' ), 0 );
		add_filter( 'rest_pre_dispatch', array( $this, 'kwtsk_restrict_rest_api' ), 10, 3 );
		add_filter( 'wp_sitemaps_enabled', '__return_false' );
	}

	/**
	 * Admin and page-visibility hooks. These stay on so a template page cannot leak.
	 */
	private function kwtsk_init_maintenance_admin_hooks() {
		add_action( 'admin_bar_menu', array( $this, 'kwtsk_add_admin_bar_status' ), 100 );
		add_filter( 'display_post_states', array( $this, 'kwtsk_add_page_post_state' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'kwtsk_hide_maintenance_page_in_admin' ), 10 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'kwtsk_exclude_page_from_menus' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'kwtsk_exclude_page_from_sitemap' ), 10, 2 );
	}

	/**
	 * Whether visitors should see the splash screen.
	 *
	 * Both the enabled flag and a real mode are required. An enabled toggle with
	 * an empty mode used to register hooks that then did nothing.
	 *
	 * @return bool
	 */
	private function kwtsk_is_active() {
		if ( ! is_object( $this->settings ) || empty( $this->settings->enabled ) ) {
			return false;
		}

		return '' !== $this->kwtsk_mode();
	}

	/**
	 * Active mode, or an empty string when the setting is off or unknown.
	 *
	 * @return string
	 */
	private function kwtsk_mode() {
		if ( ! is_object( $this->settings ) || empty( $this->settings->mode ) ) {
			return '';
		}

		$mode = $this->settings->mode;

		return in_array( $mode, array( 'maintenance', 'coming_soon' ), true ) ? $mode : '';
	}

	/**
	 * Logged-in administrators always get through. Other people only get through
	 * when the access setting says so.
	 *
	 * @return bool
	 */
	private function kwtsk_user_can_bypass() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$access = ( is_object( $this->settings ) && ! empty( $this->settings->access ) )
			? $this->settings->access
			: 'administrators';

		if ( 'loggedin' === $access ) {
			return true;
		}

		if ( 'custom' === $access && ! empty( $this->settings->userroles ) && is_array( $this->settings->userroles ) ) {
			$user = wp_get_current_user();
			foreach ( (array) $user->roles as $role ) {
				if ( in_array( $role, $this->settings->userroles, true ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Ask page caches not to store this response. Logged-in bypass users still
	 * see the normal site, which cache plugins already vary separately.
	 */
	public function kwtsk_mark_do_not_cache() {
		if ( $this->kwtsk_user_can_bypass() ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		do_action( 'litespeed_control_set_nocache', 'theme-site-kit maintenance mode' );
	}

	/**
	 * Seconds a client should wait before requesting the site again.
	 *
	 * @return int
	 */
	private function kwtsk_retry_after() {
		$retry_after = (int) apply_filters( 'kwtsk_maintenance_retry_after', HOUR_IN_SECONDS );

		return $retry_after > 0 ? $retry_after : HOUR_IN_SECONDS;
	}

	/**
	 * Status, robots, and cache headers for the splash response.
	 */
	private function kwtsk_send_maintenance_headers() {
		if ( 'maintenance' === $this->kwtsk_mode() ) {
			status_header( 503 );
			header( 'Retry-After: ' . $this->kwtsk_retry_after() );
		} else {
			status_header( 200 );
		}

		header( 'X-Robots-Tag: noindex, nofollow' );
		nocache_headers();
	}

	/**
	 * REST routes used by the settings screen.
	 */
	public function kwtsk_register_rest_routes() {
		register_rest_route(
			'kwtsk/v1',
			'/create-template-page',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'kwtsk_rest_create_maintenance_page' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Create the private template page, store it on the settings, and return edit details.
	 *
	 * @param WP_REST_Request $req Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function kwtsk_rest_create_maintenance_page( WP_REST_Request $req ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$page_id = $this->kwtsk_ensure_maintenance_page();
		if ( ! $page_id || ! get_post( $page_id ) ) {
			return new WP_Error( 'cannot_create', __( 'Could not create maintenance page', 'theme-site-kit' ), array( 'status' => 500 ) );
		}

		$this->kwtsk_save_template_selection( $page_id );

		return rest_ensure_response(
			array(
				'page_id'    => $page_id,
				'page_title' => get_the_title( $page_id ),
				'edit_url'   => admin_url( 'post.php?post=' . $page_id . '&action=edit' ),
			)
		);
	}

	/**
	 * Ensure the maintenance page exists and is private.
	 *
	 * The page is only created when someone asks for a custom template. It is
	 * not published, so it stays out of search and the core sitemap.
	 *
	 * @return int Page ID, or 0 when the page could not be created.
	 */
	public function kwtsk_ensure_maintenance_page() {
		$page_id = (int) get_option( 'kwtsk_maintenance_page_id', 0 );
		$page    = $page_id ? get_post( $page_id ) : null;

		if ( $page instanceof WP_Post && 'page' === $page->post_type && 'trash' !== $page->post_status ) {
			if ( 'private' !== $page->post_status ) {
				wp_update_post(
					array(
						'ID'          => $page->ID,
						'post_status' => 'private',
					)
				);
			}

			return (int) $page->ID;
		}

		$new_id = wp_insert_post(
			array(
				'post_title'     => __( 'Maintenance Mode', 'theme-site-kit' ),
				'post_content'   => '',
				'post_status'    => 'private',
				'post_type'      => 'page',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			),
			true
		);

		if ( is_wp_error( $new_id ) || ! $new_id ) {
			return 0;
		}

		update_option( 'kwtsk_maintenance_page_id', (int) $new_id );

		return (int) $new_id;
	}

	/**
	 * Turn an older public template page into a private one.
	 *
	 * Drafts are left alone so an in-progress edit is not forced live in the editor.
	 */
	public function kwtsk_privatize_maintenance_page() {
		$page_id = (int) get_option( 'kwtsk_maintenance_page_id', 0 );
		if ( ! $page_id ) {
			return;
		}

		$page = get_post( $page_id );
		if ( ! $page instanceof WP_Post || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
			return;
		}

		wp_update_post(
			array(
				'ID'          => $page_id,
				'post_status' => 'private',
			)
		);
	}

	/**
	 * Persist the template page on the saved settings so it survives a refresh.
	 *
	 * @param int $page_id Template page ID.
	 */
	private function kwtsk_save_template_selection( $page_id ) {
		$saved   = get_option( 'kwtsk_options' );
		$options = is_string( $saved ) ? json_decode( $saved, true ) : null;
		if ( ! is_array( $options ) ) {
			return;
		}

		if ( ! isset( $options['maintenance'] ) || ! is_array( $options['maintenance'] ) ) {
			$options['maintenance'] = array();
		}

		$options['maintenance']['template'] = (int) $page_id;
		update_option( 'kwtsk_options', wp_json_encode( $options ) );
	}

	/**
	 * Page ID of the hidden template, or 0.
	 *
	 * @return int
	 */
	private function kwtsk_maintenance_page_id() {
		return (int) get_option( 'kwtsk_maintenance_page_id', 0 );
	}

	/**
	 * Keep the template page out of the Pages list, the menu editor, and front-end search.
	 *
	 * Queries for this exact page are left alone so the block editor can still open it.
	 *
	 * @param WP_Query $query Query being prepared.
	 */
	public function kwtsk_hide_maintenance_page_in_admin( $query ) {
		if ( ! $query instanceof WP_Query ) {
			return;
		}

		$page_id = $this->kwtsk_maintenance_page_id();
		if ( ! $page_id || $this->kwtsk_query_requests_page( $query, $page_id ) ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		$types     = $post_type ? (array) $post_type : array();
		$is_page   = in_array( 'page', $types, true ) || in_array( 'any', $types, true );

		global $pagenow;
		$hide = false;

		if ( is_admin() && $query->is_main_query() && $is_page && ! wp_doing_ajax() ) {
			$hide = true;
		}

		if ( is_admin() && isset( $pagenow ) && 'nav-menus.php' === $pagenow && $is_page ) {
			$hide = true;
		}

		if ( ! is_admin() && $query->is_search() ) {
			$hide = true;
		}

		if ( ! $hide ) {
			return;
		}

		$excluded   = (array) $query->get( 'post__not_in', array() );
		$excluded[] = $page_id;
		$query->set( 'post__not_in', array_unique( array_map( 'intval', $excluded ) ) );
	}

	/**
	 * Whether this query is fetching the template page itself.
	 *
	 * @param WP_Query $query   Query being prepared.
	 * @param int      $page_id Template page ID.
	 * @return bool
	 */
	private function kwtsk_query_requests_page( $query, $page_id ) {
		if ( (int) $query->get( 'p' ) === $page_id || (int) $query->get( 'page_id' ) === $page_id ) {
			return true;
		}

		$post_in = $query->get( 'post__in' );
		if ( ! is_array( $post_in ) ) {
			return false;
		}

		$post_in = array_map( 'intval', $post_in );

		return array( $page_id ) === $post_in;
	}

	/**
	 * Drop the template page from rendered navigation menus.
	 *
	 * @param array $items Menu items.
	 * @return array
	 */
	public function kwtsk_exclude_page_from_menus( $items ) {
		$page_id = $this->kwtsk_maintenance_page_id();
		if ( ! $page_id || ! is_array( $items ) ) {
			return $items;
		}

		return array_values(
			array_filter(
				$items,
				function ( $item ) use ( $page_id ) {
					return ! ( isset( $item->object, $item->object_id ) && 'page' === $item->object && (int) $item->object_id === $page_id );
				}
			)
		);
	}

	/**
	 * Drop the template page from the core sitemap.
	 *
	 * @param array  $args      Sitemap query args.
	 * @param string $post_type Post type being queried.
	 * @return array
	 */
	public function kwtsk_exclude_page_from_sitemap( $args, $post_type ) {
		if ( 'page' !== $post_type ) {
			return $args;
		}

		$page_id = $this->kwtsk_maintenance_page_id();
		if ( ! $page_id ) {
			return $args;
		}

		if ( ! isset( $args['post__not_in'] ) || ! is_array( $args['post__not_in'] ) ) {
			$args['post__not_in'] = array();
		}

		$args['post__not_in'][] = $page_id;

		return $args;
	}

	/**
	 * Premium template page when it is safe to render, otherwise null.
	 *
	 * Drafts and trashed pages fall back to the basic screen.
	 *
	 * @return WP_Post|null
	 */
	private function kwtsk_get_usable_template_post() {
		if ( ! (bool) kwtsk_fs()->can_use_premium_code__premium_only() ) {
			return null;
		}

		if ( ! is_object( $this->settings ) || empty( $this->settings->template ) ) {
			return null;
		}

		$post = get_post( absint( $this->settings->template ) );
		if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
			return null;
		}

		if ( ! in_array( $post->post_status, array( 'publish', 'private' ), true ) ) {
			return null;
		}

		return $post;
	}

	/**
	 * Hex color from settings, or the fallback when the value is empty or invalid.
	 *
	 * @param mixed  $value    Saved color.
	 * @param string $fallback Fallback hex color.
	 * @return string
	 */
	private function kwtsk_sanitize_color( $value, $fallback ) {
		$color = sanitize_hex_color( is_string( $value ) ? $value : '' );

		return $color ? $color : $fallback;
	}

	/**
	 * CSS for the basic screen, or the layout widths used by a custom template.
	 *
	 * @return string
	 */
	public function kwtsk_get_maintenance_css() {
		// Custom templates use the theme's global styles and the layout CSS
		// WordPress generates for each block. Don't add a second layout layer.
		if ( $this->kwtsk_get_usable_template_post() ) {
			return '';
		}

		$settings   = is_object( $this->settings ) ? $this->settings : new stdClass();
		$background = $this->kwtsk_sanitize_color( $settings->bgcolor ?? '', '#f5f5f5' );
		$title      = $this->kwtsk_sanitize_color( $settings->titlecolor ?? '', '#333333' );
		$text       = $this->kwtsk_sanitize_color( $settings->textcolor ?? '', '#666666' );

		return "
			body {
				background-color: {$background} !important;
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
				color: {$title};
				margin: 0 0 0.8rem;
				padding: 0;
			}
			.kwtsk-mm-template-content p {
				font-size: clamp(15px, 2vw, 18px);
				color: {$text};
				margin: 0;
				padding: 0;
			}
		";
	}

	/**
	 * Attach the splash CSS so wp_head prints it.
	 */
	private function kwtsk_enqueue_maintenance_css() {
		$css = $this->kwtsk_get_maintenance_css();
		if ( '' === trim( $css ) ) {
			return;
		}

		wp_enqueue_style( 'kwtsk-frontend-style' );
		wp_add_inline_style( 'kwtsk-frontend-style', $css );
	}

	/**
	 * Use the splash title instead of the queried page, which is often a 404.
	 *
	 * WordPress appends the site name with the normal title separator.
	 *
	 * @param string $title Page title, without the site name.
	 */
	private function kwtsk_prepare_document_title( $title ) {
		$title = trim( wp_strip_all_tags( (string) $title ) );
		if ( '' === $title ) {
			$title = 'coming_soon' === $this->kwtsk_mode()
				? __( 'Coming Soon', 'theme-site-kit' )
				: __( 'Maintenance Mode', 'theme-site-kit' );
		}

		add_filter(
			'pre_get_document_title',
			static function () use ( $title ) {
				$site = get_bloginfo( 'name', 'display' );
				if ( '' === $site || $site === $title ) {
					return $title;
				}

				$separator = apply_filters( 'document_title_separator', '-' );

				return $title . ' ' . $separator . ' ' . $site;
			},
			9999
		);
	}

	/**
	 * Shared document head for both splash screens.
	 */
	private function kwtsk_render_document_head() {
		$this->kwtsk_enqueue_maintenance_css();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="robots" content="noindex, nofollow">
			<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
				<title><?php echo esc_html( wp_get_document_title() ); ?></title>
			<?php endif; ?>
			<?php wp_head(); ?>
		</head>
		<?php
	}

	/**
	 * Whether this request is for a core sitemap.
	 *
	 * @return bool
	 */
	private function kwtsk_is_sitemap_request() {
		return (bool) get_query_var( 'sitemap' ) || (bool) get_query_var( 'sitemap-stylesheet' );
	}

	/**
	 * Block public REST requests while the splash screen is up.
	 *
	 * People who can edit in wp-admin still need REST. Their front end stays hidden.
	 *
	 * @param mixed           $result  Response to short-circuit with, or null.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 * @return mixed
	 */
	public function kwtsk_restrict_rest_api( $result, $server, $request ) {
		unset( $server );

		if ( ! $this->kwtsk_is_active() || $this->kwtsk_user_can_bypass() || current_user_can( 'edit_posts' ) ) {
			return $result;
		}

		if ( $request instanceof WP_REST_Request && is_user_logged_in() ) {
			$route = $request->get_route();
			if ( 0 === strpos( $route, '/wp/v2/users/me' ) ) {
				return $result;
			}
		}

		if ( 'maintenance' === $this->kwtsk_mode() ) {
			header( 'Retry-After: ' . $this->kwtsk_retry_after() );
		}
		header( 'X-Robots-Tag: noindex, nofollow' );

		return new WP_Error(
			'kwtsk_maintenance_mode',
			__( 'The site is temporarily unavailable.', 'theme-site-kit' ),
			array( 'status' => 503 )
		);
	}

	/**
	 * ID of the selected custom template page, or 0.
	 *
	 * @return int
	 */
	private function kwtsk_template_page_id() {
		if ( ! is_object( $this->settings ) || empty( $this->settings->template ) ) {
			return 0;
		}

		return absint( $this->settings->template );
	}

	/**
	 * Whether this front-end request is the template page itself, including preview.
	 *
	 * @return bool
	 */
	private function kwtsk_is_viewing_template_page() {
		$page_id = $this->kwtsk_template_page_id();
		if ( ! $page_id ) {
			return false;
		}

		if ( is_page( $page_id ) || (int) get_queried_object_id() === $page_id ) {
			return true;
		}

		if ( ! is_preview() ) {
			return false;
		}

		$preview_id = absint( get_query_var( 'preview_id' ) );
		if ( ! $preview_id && isset( $_GET['preview_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preview identity only, WordPress already verified the preview nonce.
			$preview_id = absint( wp_unslash( $_GET['preview_id'] ) );
		}

		return $preview_id === $page_id;
	}

	/**
	 * Replace the front end with the maintenance or coming soon screen.
	 *
	 * Feeds and sitemaps get the status code and no HTML. robots.txt and the
	 * favicon are left for WordPress so they are not replaced by the splash page.
	 * Logged-in visitors who can bypass the rest of the site still see this
	 * template page without the theme header and footer, matching the public splash.
	 */
	public function kwtsk_display_maintenance_mode() {
		if ( $this->kwtsk_is_viewing_template_page() && ( ! $this->kwtsk_is_active() || $this->kwtsk_user_can_bypass() ) ) {
			$template_post = get_queried_object();
			if ( ! $template_post instanceof WP_Post || (int) $template_post->ID !== $this->kwtsk_template_page_id() ) {
				$template_post = $this->kwtsk_get_usable_template_post();
			}

			if ( $template_post instanceof WP_Post ) {
				status_header( 200 );
				header( 'X-Robots-Tag: noindex, nofollow' );
				nocache_headers();
				// The admin bar is initialized earlier on template_redirect, so the
				// flag, stylesheet, and render callbacks all have to be removed.
				$GLOBALS['show_admin_bar'] = false;
				add_filter( 'show_admin_bar', '__return_false' );
				wp_dequeue_style( 'admin-bar' );
				wp_deregister_style( 'admin-bar' );
				remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );
				remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
				$this->kwtsk_render_template_document( $template_post );
			}
		}

		if ( ! $this->kwtsk_is_active() || $this->kwtsk_user_can_bypass() ) {
			return;
		}

		if ( is_robots() || is_favicon() ) {
			return;
		}

		if ( is_feed() || is_trackback() || $this->kwtsk_is_sitemap_request() ) {
			$this->kwtsk_send_maintenance_headers();
			exit;
		}

		$this->kwtsk_send_maintenance_headers();

		$mode          = $this->kwtsk_mode();
		$template_post = $this->kwtsk_get_usable_template_post();

		if ( $template_post instanceof WP_Post ) {
			$this->kwtsk_render_template_document( $template_post );
		}

		$title = ! empty( $this->settings->title )
			? $this->settings->title
			: ( 'coming_soon' === $mode
				? __( 'Coming Soon', 'theme-site-kit' )
				: __( 'Maintenance Mode', 'theme-site-kit' ) );
		$text  = ! empty( $this->settings->text )
			? $this->settings->text
			: ( 'coming_soon' === $mode
				? __( 'Our website is launching soon. Stay tuned!', 'theme-site-kit' )
				: __( 'We are currently performing scheduled maintenance. Please check back soon.', 'theme-site-kit' ) );

		$this->kwtsk_prepare_document_title( $title );
		$this->kwtsk_render_document_head();
		?>
		<body <?php body_class(); ?>>
			<?php wp_body_open(); ?>
			<div class="kwtsk-mm-template-content">
				<h1><?php echo esc_html( $title ); ?></h1>
				<p><?php echo esc_html( $text ); ?></p>
			</div>
			<?php wp_footer(); ?>
		</body>
		</html>
		<?php
		exit;
	}

	/**
	 * Print the custom template as its own document, without the theme header and footer.
	 *
	 * Block layout CSS is generated while the content renders, and block themes only
	 * print that CSS during wp_enqueue_scripts, so the content is rendered first.
	 *
	 * @param WP_Post $template_post Template page to render.
	 */
	private function kwtsk_render_template_document( WP_Post $template_post ) {
		$GLOBALS['post'] = $template_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- template page is the document being rendered.
		setup_postdata( $template_post );

		$content = apply_filters( 'the_content', $template_post->post_content );

		$this->kwtsk_prepare_document_title( $template_post->post_title );
		$this->kwtsk_render_document_head();
		?>
		<body <?php body_class(); ?>>
			<?php wp_body_open(); ?>
			<div class="wp-site-blocks">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content runs on the saved template page.
				echo $content;
				?>
			</div>
			<?php wp_footer(); ?>
		</body>
		</html>
		<?php
		wp_reset_postdata();
		exit;
	}

	/**
	 * Admin bar notice for people who can turn maintenance mode off.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 */
	public function kwtsk_add_admin_bar_status( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) || ! $this->kwtsk_is_active() ) {
			return;
		}

		$status = 'coming_soon' === $this->kwtsk_mode()
			? esc_html__( 'Coming Soon: ON', 'theme-site-kit' )
			: esc_html__( 'Maintenance Mode: ON', 'theme-site-kit' );

		$wp_admin_bar->add_node(
			array(
				'id'    => 'kwtsk_maintenance_status',
				'title' => $status,
				'href'  => admin_url( 'options-general.php?page=theme-site-kit-settings&tab=maintenance_mode' ),
				'meta'  => array( 'class' => 'kwtsk-maintenance-mode enabled' ),
			)
		);
	}

	/**
	 * Label the template page when it shows up in an admin list.
	 *
	 * @param array   $post_states Post states.
	 * @param WP_Post $post        Current post.
	 * @return array
	 */
	public function kwtsk_add_page_post_state( $post_states, $post ) {
		if ( ! $post instanceof WP_Post || ! is_object( $this->settings ) || empty( $this->settings->template ) ) {
			return $post_states;
		}

		if ( absint( $this->settings->template ) !== (int) $post->ID ) {
			return $post_states;
		}

		if ( 'coming_soon' === $this->settings->mode ) {
			$post_states[] = esc_html__( 'Coming Soon Page', 'theme-site-kit' );
		} else {
			$post_states[] = esc_html__( 'Maintenance Mode Page', 'theme-site-kit' );
		}

		return $post_states;
	}
}

new KWTSK_Maintenance_Mode();
