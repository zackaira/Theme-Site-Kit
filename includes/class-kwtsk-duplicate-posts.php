<?php
/**
 * Duplicate posts, pages, and custom post types.
 *
 * @package Theme_Site_Kit
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a Duplicate action and clones a post as a draft.
 */
class KWTSK_Duplicate_Posts {

	/**
	 * Post types that should never show a Duplicate action.
	 *
	 * @var string[]
	 */
	private $excluded_types = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'wp_template',
		'wp_template_part',
		'wp_navigation',
		'wp_block',
		'wp_global_styles',
		'custom_css',
		'wp_font_family',
		'wp_font_face',
	);

	/**
	 * Constructor function.
	 */
	public function __construct() {
		$kwtsk_saved_options = get_option( 'kwtsk_options' );
		$kwtsk_options       = $kwtsk_saved_options ? json_decode( $kwtsk_saved_options ) : null;

		if ( isset( $kwtsk_options->duplicate->enabled ) && $kwtsk_options->duplicate->enabled ) {
			$this->kwtsk_init_duplicate_post_hooks();
		}
	}

	/**
	 * Initialize the hooks.
	 */
	private function kwtsk_init_duplicate_post_hooks() {
		add_filter( 'post_row_actions', array( $this, 'kwtsk_add_duplicate_link' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'kwtsk_add_duplicate_link' ), 10, 2 );

		add_action( 'admin_action_theme_site_kit_duplicate_post', array( $this, 'kwtsk_duplicate_post_as_draft' ) );
		add_action( 'admin_notices', array( $this, 'kwtsk_duplicate_admin_notice' ) );
		add_action( 'admin_footer-post.php', array( $this, 'kwtsk_classic_editor_duplicate_link' ) );
		add_action( 'admin_footer-post-new.php', array( $this, 'kwtsk_classic_editor_duplicate_link' ) );

		add_action( 'rest_api_init', array( $this, 'kwtsk_register_rest_route' ) );
	}

	/**
	 * Register the block editor duplicate route.
	 */
	public function kwtsk_register_rest_route() {
		register_rest_route(
			'kwtsk/v1',
			'/duplicate-post',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'kwtsk_rest_duplicate_post' ),
				'permission_callback' => array( $this, 'kwtsk_rest_duplicate_permission' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * Whether the current user may duplicate this post.
	 *
	 * @param WP_Post|null $post Post object.
	 * @return bool
	 */
	private function kwtsk_can_duplicate( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		if ( in_array( $post->post_status, array( 'trash', 'auto-draft' ), true ) ) {
			return false;
		}

		if ( in_array( $post->post_type, $this->excluded_types, true ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object || empty( $post_type_object->show_ui ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return false;
		}

		$create_cap = isset( $post_type_object->cap->create_posts ) ? $post_type_object->cap->create_posts : 'edit_posts';

		return current_user_can( $create_cap );
	}

	/**
	 * Build the admin URL that duplicates a post.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $redirect Where to go afterwards. `edit` or `stay`.
	 * @return string
	 */
	private function kwtsk_get_duplicate_url( $post_id, $redirect = 'edit' ) {
		return add_query_arg(
			array(
				'action'   => 'theme_site_kit_duplicate_post',
				'post'     => $post_id,
				'redirect' => 'stay' === $redirect ? 'stay' : 'edit',
				'_wpnonce' => wp_create_nonce( 'theme_site_kit_duplicate_post_' . $post_id ),
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Add a Duplicate link underneath each row.
	 *
	 * @param array   $actions Row actions.
	 * @param WP_Post $post    Current post.
	 * @return array
	 */
	public function kwtsk_add_duplicate_link( $actions, $post ) {
		if ( ! $this->kwtsk_can_duplicate( $post ) ) {
			return $actions;
		}

		$edit_url = $this->kwtsk_get_duplicate_url( $post->ID, 'edit' );
		$stay_url = $this->kwtsk_get_duplicate_url( $post->ID, 'stay' );

		$actions['duplicate'] = sprintf(
			'<a href="%1$s" class="kwtsk-duplicate-toggle" data-edit-url="%2$s" data-stay-url="%1$s" aria-haspopup="true" aria-expanded="false">%3$s</a>',
			esc_url( $stay_url ),
			esc_url( $edit_url ),
			esc_html__( 'Duplicate', 'theme-site-kit' )
		);

		return $actions;
	}

	/**
	 * Place a Duplicate link after Move to Trash in the classic editor.
	 */
	public function kwtsk_classic_editor_duplicate_link() {
		global $post;

		if ( ! $post instanceof WP_Post || ! function_exists( 'use_block_editor_for_post' ) || use_block_editor_for_post( $post ) || ! $this->kwtsk_can_duplicate( $post ) ) {
			return;
		}

		$data = wp_json_encode(
			array(
				'editUrl' => $this->kwtsk_get_duplicate_url( $post->ID, 'edit' ),
				'stayUrl' => $this->kwtsk_get_duplicate_url( $post->ID, 'stay' ),
				'label'   => __( 'Duplicate', 'theme-site-kit' ),
			),
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
		);

		if ( ! $data ) {
			return;
		}
		?>
		<script>
		( function () {
			var data = <?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded above. ?>;
			var trash = document.querySelector( '#delete-action .submitdelete' );
			if ( ! trash || ! data || ! data.editUrl || trash.parentNode.querySelector( '.kwtsk-duplicate-classic' ) ) {
				return;
			}
			var link = document.createElement( 'a' );
			link.href = data.stayUrl || data.editUrl;
			link.className = 'kwtsk-duplicate-classic kwtsk-duplicate-toggle';
			link.dataset.editUrl = data.editUrl;
			link.dataset.stayUrl = data.stayUrl || data.editUrl;
			link.setAttribute( 'aria-haspopup', 'true' );
			link.setAttribute( 'aria-expanded', 'false' );
			link.textContent = data.label || 'Duplicate';
			link.style.marginLeft = '8px';
			trash.insertAdjacentElement( 'afterend', link );
		} )();
		</script>
		<?php
	}

	/**
	 * Handle the list and classic editor duplicate request.
	 */
	public function kwtsk_duplicate_post_as_draft() {
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		check_admin_referer( 'theme_site_kit_duplicate_post_' . $post_id );

		$new_post_id = $this->kwtsk_duplicate_post( $post_id );

		if ( is_wp_error( $new_post_id ) ) {
			wp_die( esc_html( $new_post_id->get_error_message() ) );
		}

		$redirect = isset( $_GET['redirect'] ) ? sanitize_key( wp_unslash( $_GET['redirect'] ) ) : 'edit';

		if ( 'stay' === $redirect ) {
			$new_post = get_post( $new_post_id );
			$fallback = admin_url( 'edit.php' );
			if ( $new_post ) {
				$fallback = admin_url( 'edit.php?post_type=' . $new_post->post_type );
			}
			$referer = wp_get_referer();
			$target  = $referer ? wp_validate_redirect( $referer, $fallback ) : $fallback;
			$target  = remove_query_arg( 'kwtsk_duplicated', $target );

			wp_safe_redirect( add_query_arg( 'kwtsk_duplicated', $new_post_id, $target ) );
			exit;
		}

		wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	}

	/**
	 * Confirm a duplicate that was created without leaving the current screen.
	 */
	public function kwtsk_duplicate_admin_notice() {
		if ( ! isset( $_GET['kwtsk_duplicated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only notice.
			return;
		}

		$new_post_id = absint( wp_unslash( $_GET['kwtsk_duplicated'] ) );
		$post        = get_post( $new_post_id );

		if ( ! $post || ! current_user_can( 'edit_post', $new_post_id ) ) {
			return;
		}

		$edit_link = get_edit_post_link( $new_post_id );
		if ( ! $edit_link ) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: URL of the new draft. */
					wp_kses(
						__( 'Duplicate created. <a href="%s">Edit the draft</a>.', 'theme-site-kit' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url( $edit_link )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * REST permission check for the block editor button.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool
	 */
	public function kwtsk_rest_duplicate_permission( WP_REST_Request $request ) {
		$post = get_post( absint( $request->get_param( 'id' ) ) );

		return $this->kwtsk_can_duplicate( $post );
	}

	/**
	 * Duplicate a post from the block editor.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function kwtsk_rest_duplicate_post( WP_REST_Request $request ) {
		$new_post_id = $this->kwtsk_duplicate_post( absint( $request->get_param( 'id' ) ) );

		if ( is_wp_error( $new_post_id ) ) {
			return $new_post_id;
		}

		return rest_ensure_response(
			array(
				'id'      => $new_post_id,
				'editUrl' => admin_url( 'post.php?action=edit&post=' . $new_post_id ),
			)
		);
	}

	/**
	 * Page title for a copy, so About becomes About # Duplicate.
	 *
	 * @param string $title Original page title.
	 * @return string
	 */
	private function kwtsk_duplicate_page_title( $title ) {
		$title = trim( $title );
		if ( '' === $title ) {
			return $title;
		}

		return $title . ' ' . __( '# Duplicate', 'theme-site-kit' );
	}

	/**
	 * Copy a post, its terms, and its meta into a new draft.
	 *
	 * @param int $post_id Post ID.
	 * @return int|WP_Error New post ID.
	 */
	public function kwtsk_duplicate_post( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post || ! $this->kwtsk_can_duplicate( $post ) ) {
			return new WP_Error(
				'kwtsk_duplicate_forbidden',
				__( 'You are not allowed to duplicate this item.', 'theme-site-kit' ),
				array( 'status' => 403 )
			);
		}

		$new_post_id = wp_insert_post(
			wp_slash(
				array(
					'post_title'            => 'page' === $post->post_type ? $this->kwtsk_duplicate_page_title( $post->post_title ) : $post->post_title,
					'post_content'          => $post->post_content,
					'post_content_filtered' => $post->post_content_filtered,
					'post_excerpt'          => $post->post_excerpt,
					'post_status'           => 'draft',
					'post_type'             => $post->post_type,
					'comment_status'        => $post->comment_status,
					'ping_status'           => $post->ping_status,
					'menu_order'            => $post->menu_order,
					'post_parent'           => $post->post_parent,
					'post_password'         => $post->post_password,
					'post_author'           => $post->post_author,
				)
			),
			true
		);

		if ( is_wp_error( $new_post_id ) ) {
			return $new_post_id;
		}

		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				wp_set_object_terms( $new_post_id, $terms, $taxonomy );
			}
		}

		$skip_meta = array(
			'_edit_lock',
			'_edit_last',
			'_wp_old_slug',
			'_wp_trash_meta_status',
			'_wp_trash_meta_time',
			'_wp_trash_meta_comments_status',
		);
		$post_meta = get_post_meta( $post_id );

		foreach ( $post_meta as $key => $values ) {
			if ( in_array( $key, $skip_meta, true ) ) {
				continue;
			}
			foreach ( $values as $value ) {
				// get_post_meta() without a key returns raw stored values.
				add_post_meta( $new_post_id, $key, wp_slash( maybe_unserialize( $value ) ) );
			}
		}

		return $new_post_id;
	}
}
new KWTSK_Duplicate_Posts();
