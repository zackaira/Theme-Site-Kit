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
			$this->init_hooks();
		}
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'init', [$this, 'kwtsk_register_custom_post_types'] );
		add_filter( 'post_updated_messages', [$this, 'kwtsk_custom_post_type_messages'] );
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
				'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
				'menu_icon'          => 'dashicons-admin-post',
			] );

			if ( isset( $settings->enable_categories ) && $settings->enable_categories ) {
				register_taxonomy( "{$slug}-category", $slug, [
					'labels' => [
						'name'          => _x( 'Categories', 'taxonomy general name', 'theme-site-kit' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name', 'theme-site-kit' ),
					],
					'hierarchical'      => true,
					'public'            => true,
					'show_ui'           => true,
					'show_in_rest'      => true,
					'rewrite'           => [ 'slug' => "{$slug}-category" ],
				] );
			}

			if ( isset( $settings->enable_tags ) && $settings->enable_tags ) {
				register_taxonomy( "{$slug}-tag", $slug, [
					'labels' => [
						'name'          => _x( 'Tags', 'taxonomy general name', 'theme-site-kit' ),
						'singular_name' => _x( 'Tag', 'taxonomy singular name', 'theme-site-kit' ),
					],
					'hierarchical'      => false,
					'public'            => true,
					'show_ui'           => true,
					'show_in_rest'      => true,
					'rewrite'           => [ 'slug' => "{$slug}-tag" ],
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
				5  => isset( $_GET['revision'] )
					// translators: %1$s: Post type singular name, %2$s: Revision date.
					? sprintf( __( '%1$s restored to revision from %2$s', 'theme-site-kit' ), $singular, wp_post_revision_title( (int) $_GET['revision'], false ) )
					: false,
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
