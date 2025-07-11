<?php
/**
 * Patterns Setup.
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Patterns.
 */
class KWTSK_Duplicate_Posts {
	private $isPro;

	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->isPro = (boolean)kwtsk_fs()->can_use_premium_code__premium_only();
		
		$kwtskSavedOptions = get_option('kwtsk_options');
        $kwtskOptions      = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : null;

        if (isset($kwtskOptions->code->enabled) && $kwtskOptions->code->enabled) {
            $this->kwtsk_init_duplicate_post_hooks();
        }
	}

	/**
	 * Initialize the hooks.
	 */
	private function kwtsk_init_duplicate_post_hooks() {
        // Add "Duplicate" row-action.
		add_filter( 'post_row_actions',  [ $this, 'kwtsk_add_duplicate_link' ], 10, 2 );
		add_filter( 'page_row_actions',  [ $this, 'kwtsk_add_duplicate_link' ], 10, 2 );

		// Handle the click.
		add_action( 'admin_action_theme_site_kit_duplicate_post', [ $this, 'kwtsk_duplicate_post_as_draft' ] );
    }

	/**
	 * Add a “Duplicate” link underneath each row.
	 */
	public function kwtsk_add_duplicate_link( $actions, $post ) {
		// Only for users who can edit the post.
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$nonce = wp_create_nonce( 'theme_site_kit_duplicate_post_' . $post->ID );

		$url   = add_query_arg(
			[
				'action'   => 'theme_site_kit_duplicate_post',
				'post'     => $post->ID,
				'_wpnonce' => $nonce,
			],
			admin_url( 'admin.php' )
		);

		$actions['duplicate'] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr__( 'Duplicate this item', 'theme-site-kit' ) . '">' . __( 'Duplicate', 'theme-site-kit' ) . '</a>';

		return $actions;
	}

	/**
	 * Perform the duplication and redirect to the new draft.
	 */
	public function kwtsk_duplicate_post_as_draft() {
		// 1. Basic validation.
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( ! $post_id || ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'theme_site_kit_duplicate_post_' . $post_id ) ) {
			wp_die( __( 'Invalid request.', 'theme-site-kit' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( __( 'You are not allowed to duplicate this item.', 'theme-site-kit' ) );
		}

		// 2. Copy the post object.
		$new_post_args = [
			'post_title'     => $post->post_title,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_status'    => 'draft',
			'post_type'      => $post->post_type,
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'menu_order'     => $post->menu_order,
			// Set author to current user.  To keep original author, use: $post->post_author
			'post_author'    => get_current_user_id(),
		];

		$new_post_id = wp_insert_post( $new_post_args );

		if ( is_wp_error( $new_post_id ) ) {
			wp_die( $new_post_id->get_error_message() );
		}

		// 3. Copy taxonomies.
		$taxonomies = get_object_taxonomies( $post->post_type, 'names' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				wp_set_object_terms( $new_post_id, $terms, $taxonomy );
			}
		}

		// 4. Copy all post-meta, excluding core meta such as '_wp_old_slug'.
		$skip_meta = [
			'_wp_old_slug',      // old permalinks
			'_edit_lock',        // block-editor edit lock
			'_edit_last',        // last editor user-ID
			'_wp_trash_meta_status', // trash helper
		];
		$post_meta = get_post_meta( $post_id );
		
		foreach ( $post_meta as $key => $values ) {
			if ( in_array( $key, $skip_meta, true ) ) {
				continue; // don’t copy this key
			}
			foreach ( $values as $value ) {
				add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
			}
		}

		// 5. Copy featured image.
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( $thumb_id ) {
			set_post_thumbnail( $new_post_id, $thumb_id );
		}

		// 6. Redirect to the edit screen of the new draft.
		wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	}
}
new KWTSK_Duplicate_Posts();
