<?php
/**
 * Post Types Setup
 *
 * @package Theme_Site_Kit
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Post Types Class
 * Handles setup of Custom Post Types
 */
class KWTSK_Post_Types {
	/**
	 * Constructor function.
	 */
	public function __construct() {
		$kwtskSavedOptions = get_option( 'kwtsk_options' );
		$kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;
		$isPremium = (bool) kwtsk_fs()->can_use_premium_code__premium_only();

		if ( ! $isPremium ) return;

		// Check if cpts mode is enabled
		if ( isset( $kwtskOptions->cpts->enabled ) && $kwtskOptions->cpts->enabled ) {
			$this->kwtsk_init_post_type_hooks();
		}
	}

	/**
	 * Initialize hooks
	 */
	private function kwtsk_init_post_type_hooks() {
		add_action( 'init', [$this, 'kwtsk_register_custom_post_types'] );
		add_filter( 'post_updated_messages', [$this, 'kwtsk_custom_post_type_messages'] );
		add_filter( 'get_block_template', [$this, 'kwtsk_ensure_cpt_template'], 10, 3 );
		add_filter( 'default_template_types', [$this, 'kwtsk_register_cpt_template_types'], 10, 1 );
	}

	/**
	 * Register template types for our CPTs
	 * 
	 * @param array $default_template_types The default template types.
	 * @return array Modified template types.
	 */
	public function kwtsk_register_cpt_template_types( $default_template_types ) {
		$kwtskSavedOptions = get_option( 'kwtsk_options' );
		$kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

		if ( empty( $kwtskOptions->cpts->post_types ) || ! is_object( $kwtskOptions->cpts->post_types ) ) {
			return $default_template_types;
		}

		foreach ( $kwtskOptions->cpts->post_types as $settings ) {
			$post_type = sanitize_title( $settings->slug );
			$label     = sanitize_text_field( $settings->label );
			$singular  = ! empty( $settings->singular ) ? sanitize_text_field( $settings->singular ) : $label;

			$default_template_types["single-{$post_type}"] = array(
				'title'       => sprintf( __( 'Single %s', 'theme-site-kit' ), $singular ),
				'description' => sprintf( __( 'Displays a single %s.', 'theme-site-kit' ), strtolower( $singular ) ),
			);
		}

		return $default_template_types;
	}

	/**
	 * Ensure WordPress uses our custom template for CPTs
	 * 
	 * @param WP_Block_Template|null $template  The found block template.
	 * @param string                 $id        Template unique identifier (example: theme_slug//template_slug).
	 * @param string                 $template_type wp_template or wp_template_part.
	 * @return WP_Block_Template|null Block template.
	 */
	public function kwtsk_ensure_cpt_template( $template, $id, $template_type ) {
		if ( ! is_singular() || 'wp_template' !== $template_type ) {
			return $template;
		}

		$post_type = get_post_type();
		if ( ! $post_type ) {
			return $template;
		}

		// Check if this is one of our CPTs
		$kwtskSavedOptions = get_option( 'kwtsk_options' );
		$kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

		if ( empty( $kwtskOptions->cpts->post_types ) || ! is_object( $kwtskOptions->cpts->post_types ) ) {
			return $template;
		}

		$is_our_cpt = false;
		foreach ( $kwtskOptions->cpts->post_types as $settings ) {
			if ( sanitize_title( $settings->slug ) === $post_type ) {
				$is_our_cpt = true;
				break;
			}
		}

		if ( ! $is_our_cpt ) {
			return $template;
		}

		// Try to find our custom template
		$theme_slug = wp_get_theme()->get_stylesheet();
		$template_slug = "single-{$post_type}";
		$template_path = "{$theme_slug}//{$template_slug}";

		$found = get_block_template( $template_path, 'wp_template' );
		if ( ! $found ) {
			$found = get_page_by_path( $template_path, OBJECT, 'wp_template' );
		}
		
		return $found ? ( $found instanceof WP_Block_Template ? $found : _build_block_template_result_from_post( $found ) ) : $template;
	}

	/**
	 * Register Custom Post Types
	 */
	public function kwtsk_register_custom_post_types() {
		$kwtskSavedOptions = get_option( 'kwtsk_options' );
		$kwtskOptions      = $kwtskSavedOptions ? json_decode( $kwtskSavedOptions ) : null;

		if ( empty( $kwtskOptions->cpts->post_types ) || ! is_object( $kwtskOptions->cpts->post_types ) ) {
			return;
		}

		foreach ( $kwtskOptions->cpts->post_types as $key => $settings ) {
			$slug     = sanitize_title( $settings->slug );
			$label    = sanitize_text_field( $settings->label );
			$singular = ! empty( $settings->singular ) ? sanitize_text_field( $settings->singular ) : $label;
			$has_archive = isset( $settings->has_archive ) ? $settings->has_archive : true;

			register_post_type( $slug, [
				'labels' => [
					'name'               => $label,
					'singular_name'      => $singular,
					'menu_name'          => $label,
					'add_new'            => __( 'Add New', 'theme-site-kit' ),
					// translators: %s: Post type singular name.
					'add_new_item'       => sprintf( __( 'Add New %s', 'theme-site-kit' ), $singular ),
					// translators: %s: Post type singular name.
					'edit_item'          => sprintf( __( 'Edit %s', 'theme-site-kit' ), $singular ),
					// translators: %s: Post type singular name.
					'new_item'           => sprintf( __( 'New %s', 'theme-site-kit' ), $singular ),
					// translators: %s: Post type singular name.
					'view_item'          => sprintf( __( 'View %s', 'theme-site-kit' ), $singular ),
					// translators: %s: Post type label.
					'view_items'         => sprintf( __( 'View All %s', 'theme-site-kit' ), $label ),
					// translators: %s: Post type label.
					'search_items'       => sprintf( __( 'Search %s', 'theme-site-kit' ), $label ),
					// translators: %s: Post type label.
					'not_found'          => sprintf( __( 'No %s found', 'theme-site-kit' ), $label ),
					// translators: %s: Post type label.
					'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'theme-site-kit' ), $label ),
				],
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'has_archive'        => $has_archive,
				'hierarchical'       => false,
				'rewrite'            => [ 'slug' => $slug ],
				'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ],
				'menu_icon'          => 'dashicons-admin-post',
				'template' => array(
					array( 'core/paragraph', array(
						'content' => __( 'Start building your page!', 'theme-site-kit' ),
					) ),
				),
			] );

			if ( isset( $settings->enable_categories ) && $settings->enable_categories ) {
				$category_slug = isset( $settings->category_slug ) && ! empty( $settings->category_slug )
					? sanitize_title( $settings->category_slug )
					: "{$slug}-category";

				register_taxonomy( "{$slug}-category", $slug, [
					'labels' => [
						'name'          => _x( 'Categories', 'taxonomy general name', 'theme-site-kit' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name', 'theme-site-kit' ),
					],
					'hierarchical'      => true,
					'public'            => true,
					'show_ui'           => true,
					'show_in_rest'      => true,
					'rewrite'           => [ 'slug' => $category_slug ],
				] );
			}

			if ( isset( $settings->enable_tags ) && $settings->enable_tags ) {
				$tag_slug = isset( $settings->tag_slug ) && ! empty( $settings->tag_slug )
					? sanitize_title( $settings->tag_slug )
					: "{$slug}-tag";

				register_taxonomy( "{$slug}-tag", $slug, [
					'labels' => [
						'name'          => _x( 'Tags', 'taxonomy general name', 'theme-site-kit' ),
						'singular_name' => _x( 'Tag', 'taxonomy singular name', 'theme-site-kit' ),
					],
					'hierarchical'      => false,
					'public'            => true,
					'show_ui'           => true,
					'show_in_rest'      => true,
					'rewrite'           => [ 'slug' => $tag_slug ],
				] );
			}
		}
	}

	/**
	 * Customize post type messages
	 */
	public function kwtsk_custom_post_type_messages( $messages ) {
		global $post, $post_ID;

		$post_types = get_post_types( [ '_builtin' => false ], 'objects' );

		foreach ( $post_types as $post_type => $obj ) {
			if ( empty( $obj->labels->singular_name ) ) continue;

			$singular = $obj->labels->singular_name;

			$messages[ $post_type ] = [
				0  => '',
				// translators: %s: Post type singular name.
				1  => sprintf( __( '%s updated.', 'theme-site-kit' ), $singular ),
				2  => __( 'Custom field updated.', 'theme-site-kit' ),
				3  => __( 'Custom field deleted.', 'theme-site-kit' ),
				// translators: %s: Post type singular name.
				4  => sprintf( __( '%s updated.', 'theme-site-kit' ), $singular ),
				// translators: %s: Post type singular name.
				5  => sprintf( __( '%s restored to revision', 'theme-site-kit' ), $singular ),
				// translators: %s: Post type singular name.
				6  => sprintf( __( '%s published.', 'theme-site-kit' ), $singular ),
				// translators: %s: Post type singular name.
				7  => sprintf( __( '%s saved.', 'theme-site-kit' ), $singular ),
				// translators: %s: Post type singular name.
				8  => sprintf( __( '%s submitted.', 'theme-site-kit' ), $singular ),
				9  => sprintf(
					// translators: %1$s: Post type singular name, %2$s: Date and time.
					__( '%1$s scheduled for: <strong>%2$s</strong>.', 'theme-site-kit' ),
					$singular,
					date_i18n( __( 'M j, Y @ G:i', 'theme-site-kit' ), strtotime( $post->post_date ) )
				),
				// translators: %s: Post type singular name.
				10 => sprintf( __( '%s draft updated.', 'theme-site-kit' ), $singular ),
			];
		}

		return $messages;
	}
}

new KWTSK_Post_Types();
