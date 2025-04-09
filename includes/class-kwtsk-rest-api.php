<?php
/*
 * Create Custom Rest API Endpoints
 */
class Theme_Site_Kit_API_Rest_Routes {
	public function __construct() {
		add_action('rest_api_init', [$this, 'kwtsk_create_rest_routes']);
	}

	/*
	 * Create REST API routes for get & save
	 */
	public function kwtsk_create_rest_routes() {
		register_rest_route('kwtsk/v1', '/settings', [
			'methods' => 'GET',
			'callback' => [$this, 'kwtsk_get_settings'],
			'permission_callback' => [$this, 'kwtsk_get_settings_permission'],
		]);
		register_rest_route('kwtsk/v1', '/settings', [
			'methods' => 'POST',
			'callback' => [$this, 'kwtsk_save_settings'],
			'permission_callback' => [$this, 'kwtsk_save_settings_permission'],
		]);
		register_rest_route('kwtsk/v1', '/delete', [
			'methods' => 'DELETE',
			'callback' => [$this, 'kwtsk_delete_settings'],
			'permission_callback' => [$this, 'kwtsk_save_settings_permission'],
		]);
		register_rest_route( 'kwtsk/v1', '/remove-data/(?P<id>\d+)', [
			'methods' => 'DELETE',
			'callback' => [$this, 'kwtsk_remove_click_data'],
			'permission_callback' => [$this, 'kwtsk_save_settings_permission'],
		]);

		register_rest_route('kwtsk/v1', '/layouts', [
			'methods'             => 'GET',
			'callback'            => [$this, 'kwtsk_get_layouts'],
			'permission_callback' => '__return_true',
		]);
		register_rest_route('kwtsk/v1', '/import-layout', [
			'methods'             => 'POST',
			'callback'            => [$this, 'kwtsk_import_layout'],
			'permission_callback' => [$this, 'kwtsk_save_settings_permission'],
		]);
		register_rest_route('kwtsk/v1', '/installed-plugins', [
			'methods'             => 'GET',
			'callback'            => [$this, 'kwtsk_get_installed_plugins'],
			'permission_callback' => [$this, 'kwtsk_get_settings_permission'],
		]);
		
		register_rest_route('kwtsk/v1', '/install-plugin', [
			'methods'             => 'POST',
			'callback'            => [$this, 'kwtsk_install_plugin'],
			'permission_callback' => [$this, 'kwtsk_save_settings_permission'],
		]);

		register_rest_route('kwtsk/v1', '/check-post-type', [
			'methods'             => 'GET',
			'callback'            => [$this, 'kwtsk_check_post_type_post_count'],
			'permission_callback' => [$this, 'kwtsk_get_settings_permission'],
		]);
	}

	/*
	 * Get saved options from database
	 */
	public function kwtsk_get_settings() {
		$kwtskOptions = get_option('kwtsk_options');

		if (!$kwtskOptions)
			return;

		return rest_ensure_response($kwtskOptions);
	}

	/*
	 * Allow permissions for get options
	 */
	public function kwtsk_get_settings_permission() {
		return true;
	}

	/*
	 * Save settings as JSON string
	 */
	public function kwtsk_save_settings() {
		$req = file_get_contents('php://input');
		$reqData = json_decode($req, true);

		update_option('kwtsk_options', $reqData['kwtskOptions']);

		return rest_ensure_response(get_option('kwtsk_options'));
	}

	/*
	 * Set save permissions for admin users
	 */
	public function kwtsk_save_settings_permission() {
		return current_user_can('publish_posts') ? true : false;
	}

	/*
	 * Delete the plugin settings
	 */
	public function kwtsk_delete_settings() {
		delete_option('kwtsk_options');

		return rest_ensure_response('Success!');
	}

	/**
     * Retrieve all layouts from the remote site and group them by collection.
     *
     * Each collection is a top-level page (parent == 0) which includes:
     * - collectionName: the title of the parent page.
     * - collectionCategory: the category assigned via ACF (if available).
     * - previewImage: the featured image of the parent page.
     * - layouts: an array of child pages (sub-level layouts) with their own fields.
     *
     * Also compiles a list of unique non-empty categories and tags.
     */
    public function kwtsk_get_layouts( WP_REST_Request $request ) {
		// Set the remote URL with _embed so that featured media and taxonomy terms are included.
		$remote_url = 'https://layouts.kaira.co/wp-json/wp/v2/page-layouts?per_page=100&_embed';
		
		// Get the remote posts.
		$response = wp_remote_get( $remote_url );
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'remote_error', 'Unable to retrieve layouts', array( 'status' => 500 ) );
		}
		
		$body = wp_remote_retrieve_body( $response );
		$posts = json_decode( $body, true );
		
		// Check if the decoded response is an array.
		if ( ! is_array( $posts ) ) {
			return new WP_Error( 'invalid_response', 'Invalid response from remote layouts API: ' . $body, array( 'status' => 500 ) );
		}
		
		// Prepare arrays for collections, categories, and tags.
		$collections = array();
		$categories  = array();
		$tags        = array();
		
		// Loop through posts to pick out top-level posts (parent == 0) as collections.
		foreach ( $posts as $post ) {
			if ( intval( $post['parent'] ) === 0 ) {
				$collection_id   = $post['id'];
				$collection_name = $post['title']['rendered'];

				// Read the custom field from the parent.
				$isProLayout = false;
				if ( isset( $post['is_pro_layout'] ) ) {
					$isProLayout = filter_var( $post['is_pro_layout'], FILTER_VALIDATE_BOOLEAN );
				}
				
				// Extract category from the embedded taxonomy terms for "page-layout-categories".
				$collection_category = '';
				// Also extract required plugins from "required-plugins".
				$required_plugins = array();
				if ( isset( $post['_embedded']['wp:term'] ) && is_array( $post['_embedded']['wp:term'] ) ) {
					foreach ( $post['_embedded']['wp:term'] as $term_group ) {
						foreach ( $term_group as $term ) {
							if ( isset( $term['taxonomy'] ) ) {
								if ( $term['taxonomy'] === 'page-layout-categories' ) {
									$collection_category = $term['name'];
								}
								if ( $term['taxonomy'] === 'required-plugins' ) {
									$required_plugins[] = array(
										'name' => $term['name'],
										'slug' => $term['slug'],
									);
								}
							}
						}
					}
				}
				
				// Extract featured image from the embedded data.
				$previewImage = '';
				if ( isset( $post['_embedded']['wp:featuredmedia'][0]['source_url'] ) ) {
					$previewImage = $post['_embedded']['wp:featuredmedia'][0]['source_url'];
				}
		
				$collections[ $collection_id ] = array(
					'collectionName'     => $collection_name,
					'collectionCategory' => $collection_category,
					'requiredPlugins'    => $required_plugins,
					'previewImage'       => $previewImage,
					'isProLayout'        => $isProLayout,
					'layouts'            => array(),
				);
		
				// Only add non-empty category and avoid duplicates.
				if ( $collection_category && ! in_array( $collection_category, $categories ) ) {
					$categories[] = $collection_category;
				}
			}
		}
		
		// Loop again to assign child posts (layouts) to the correct collection.
		foreach ( $posts as $post ) {
			if ( intval( $post['parent'] ) !== 0 ) {
				$parent_id = $post['parent'];
				if ( isset( $collections[ $parent_id ] ) ) {
					// Extract featured image for child layout.
					$previewImage = '';
					if ( isset( $post['_embedded']['wp:featuredmedia'][0]['source_url'] ) ) {
						$previewImage = $post['_embedded']['wp:featuredmedia'][0]['source_url'];
					}
		
					// Use the excerpt if available; strip <p> tags.
					$raw_excerpt = trim( $post['excerpt']['rendered'] );
					$description = !empty($raw_excerpt)
						? str_replace(['<p>', '</p>'], '', $raw_excerpt)
						: '';
		
					// Extract tags from the embedded taxonomy data for "page-layout-tags".
					$child_tags = array();
					if ( isset( $post['_embedded']['wp:term'] ) && is_array( $post['_embedded']['wp:term'] ) ) {
						foreach ( $post['_embedded']['wp:term'] as $term_group ) {
							foreach ( $term_group as $term ) {
								if ( isset( $term['taxonomy'] ) && $term['taxonomy'] === 'page-layout-tags' ) {
									$child_tags[] = $term['name'];
								}
							}
						}
					}
		
					$layout = array(
						'id'           => $post['id'],
						'title'        => $post['title']['rendered'],
						'description'  => $description,
						'previewImage' => $previewImage,
						'tags'         => $child_tags,
					);
					
					$collections[ $parent_id ]['layouts'][] = $layout;
		
					// Collect tags globally, avoiding duplicates.
					if ( ! empty( $layout['tags'] ) && is_array($layout['tags']) ) {
						foreach ( $layout['tags'] as $tag ) {
							if ( $tag && ! in_array( $tag, $tags ) ) {
								$tags[] = $tag;
							}
						}
					}
				}
			}
		}
		
		// Format the final output.
		$result = array(
			'layouts'    => array_values( $collections ),
			'categories' => $categories,
			'tags'       => $tags,
		);
		
		return rest_ensure_response( $result );
	}

	/**
	 * Import a layout:
	 * - Receives a layout object (including remote layout id) via POST.
	 * - Fetches the full remote layout post (with raw content and _embed data) using context=edit.
	 * - Sideloads images found in the content and replaces their URLs.
	 *   * If an image is an SVG and SVG uploads are enabled, it uses media_handle_sideload.
	 * - Parses and re-serializes the content as blocks.
	 * - Creates a new local page with the imported content, title, and excerpt.
	 * - Does not set any featured image.
	 */
	public function kwtsk_import_layout( WP_REST_Request $request ) {
		// Load plugin options.
		$kwtskSavedOptions = get_option('kwtsk_options');
		$kwtskOptions = $kwtskSavedOptions ? json_decode($kwtskSavedOptions) : '';
		$svgEnabled = isset($kwtskOptions->svgupload->enabled) && $kwtskOptions->svgupload->enabled;
		
		$params = $request->get_json_params();
		if ( empty($params['id']) ) {
			return new WP_Error('missing_layout', 'Layout id is missing', array('status' => 400));
		}
		
		$remote_id = $params['id'];
		// Use context=edit to get raw block markup.
		$remote_url = 'https://layouts.kaira.co/wp-json/wp/v2/page-layouts/' . $remote_id . '?_embed&context=edit';
		
		$username = 'zackaira';
		$app_password = '9ivp EfEa hCmm dNFT XKFC z2BG';
		$headers = array(
			'Authorization' => 'Basic ' . base64_encode( $username . ':' . $app_password ),
		);
		
		$response = wp_remote_get( $remote_url, array( 'headers' => $headers ) );
		if ( is_wp_error($response) ) {
			return new WP_Error('remote_error', 'Unable to retrieve remote layout', array('status' => 500));
		}
		
		$body = wp_remote_retrieve_body($response);
		$remote_post = json_decode($body, true);
		if ( ! is_array($remote_post) ) {
			return new WP_Error('invalid_response', 'Invalid response from remote layout API', array('status' => 500));
		}
		
		// Use raw fields if available.
		$title = isset($remote_post['title']['raw']) ? $remote_post['title']['raw'] : (isset($remote_post['title']['rendered']) ? $remote_post['title']['rendered'] : 'Imported Layout');
		$content = isset($remote_post['content']['raw']) ? $remote_post['content']['raw'] : (isset($remote_post['content']['rendered']) ? $remote_post['content']['rendered'] : '');
		$excerpt = isset($remote_post['excerpt']['raw']) ? strip_tags(trim($remote_post['excerpt']['raw'])) : (isset($remote_post['excerpt']['rendered']) ? strip_tags(trim($remote_post['excerpt']['rendered'])) : '');
		
		// Ensure necessary WP files are loaded.
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		
		// Process images found in the content.
		if ( !empty($content) ) {
			preg_match_all('/<img[^>]+src="([^">]+)"/i', $content, $matches);
			if ( !empty($matches[1]) ) {
				foreach ($matches[1] as $image_url) {
					$ext = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
					if ( $ext === 'svg' && $svgEnabled ) {
						// For SVG, use media_handle_sideload.
						$tmp = download_url($image_url);
						if ( is_wp_error($tmp) ) {
							continue;
						}
						$file_array = array(
							'name'     => basename($image_url),
							'tmp_name' => $tmp,
						);
						$attachment_id = media_handle_sideload($file_array, 0);
						if ( is_wp_error($attachment_id) ) {
							@unlink($file_array['tmp_name']);
							continue;
						}
						$sideload = wp_get_attachment_url($attachment_id);
					} else {
						// For other image types, use media_sideload_image.
						$sideload = media_sideload_image($image_url, 0, '', 'src');
					}
					if ( !is_wp_error($sideload) && $sideload ) {
						$content = str_replace($image_url, $sideload, $content);
					}
				}
			}
		}
		
		// Parse and reserialize the content as blocks.
		if ( function_exists('parse_blocks') && function_exists('serialize_blocks') ) {
			$blocks = parse_blocks($content);
			$content = serialize_blocks($blocks);
		}
		
		// Create a new page with the imported content.
		$new_post = array(
			'post_title'   => wp_strip_all_tags($title),
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);
		$new_post_id = wp_insert_post($new_post);
		if ( is_wp_error($new_post_id) ) {
			return new WP_Error('post_error', 'Error creating new page', array('status' => 500));
		}
		
		// Do not set any featured image.
		
		return rest_ensure_response(array(
			'post_id'  => $new_post_id,
			'post_url' => get_permalink($new_post_id),
		));
	}

	/**
	 * Return a list of installed plugins with their slug and active status.
	 */
	public function kwtsk_get_installed_plugins( WP_REST_Request $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new WP_Error( 'no_permission', 'You do not have permission to view installed plugins', array( 'status' => 403 ) );
		}
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$installed_plugins = get_plugins();
		$result = array();
		foreach ( $installed_plugins as $plugin_key => $plugin_data ) {
			// The plugin slug is usually the folder name (the first part of the key)
			$slug = explode( '/', $plugin_key )[0];
			$result[] = array(
				'plugin_key' => $plugin_key,
				'slug'       => $slug,
				'active'     => is_plugin_active( $plugin_key ),
			);
		}
		return rest_ensure_response( $result );
	}


	/**
	 * Install and activate a plugin from the WordPress Plugin Repository.
	 */
	public function kwtsk_install_plugin( WP_REST_Request $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new WP_Error( 'no_permission', 'You do not have permission to install plugins', array( 'status' => 403 ) );
		}
		
		$params = $request->get_json_params();
		if ( empty($params['slug']) ) {
			return new WP_Error( 'missing_plugin', 'Plugin slug is missing', array( 'status' => 400 ) );
		}
		
		$plugin_slug = sanitize_text_field( $params['slug'] );
		
		// Load necessary plugin functions.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		
		// Check if the plugin is already installed.
		$installed_plugins = get_plugins();
		$found_plugin_key = '';
		foreach ( $installed_plugins as $plugin_key => $plugin_data ) {
			// If the plugin key starts with the plugin slug and a slash, assume it's our plugin.
			if ( strpos( $plugin_key, $plugin_slug . '/' ) === 0 ) {
				$found_plugin_key = $plugin_key;
				break;
			}
		}
		
		if ( ! empty( $found_plugin_key ) ) {
			// Plugin is installed.
			if ( is_plugin_active( $found_plugin_key ) ) {
				return rest_ensure_response( array(
					'installed'   => true,
					'plugin_file' => $found_plugin_key,
					'message'     => 'Plugin is already installed and active.'
				) );
			} else {
				// Activate the installed plugin.
				$activate = activate_plugin( $found_plugin_key );
				if ( is_wp_error( $activate ) ) {
					return $activate;
				}
				return rest_ensure_response( array(
					'installed'   => true,
					'plugin_file' => $found_plugin_key,
					'message'     => 'Plugin is installed but inactive. It has now been activated.'
				) );
			}
		} else {
			// Plugin is not installed, so proceed with installation.
			$skin = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$download_link = "https://downloads.wordpress.org/plugin/{$plugin_slug}.latest-stable.zip";
			
			$result = $upgrader->install( $download_link );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			
			$plugin_file = $upgrader->plugin_info();
			if ( $plugin_file ) {
				$activate = activate_plugin( $plugin_file );
				if ( is_wp_error( $activate ) ) {
					return $activate;
				}
			}
			
			return rest_ensure_response( array(
				'installed'   => true,
				'plugin_file' => $plugin_file,
				'message'     => 'Plugin installed and activated successfully.'
			) );
		}
	}

	/**
	 * Check dynamic post type count before deleting.
	 */
	public function kwtsk_check_post_type_post_count(WP_REST_Request $request) {
		$post_type = sanitize_key( $request->get_param( 'type' ) );
	
		if ( ! post_type_exists( $post_type ) ) {
			return new WP_Error( 'invalid_post_type', 'Post type not found', [ 'status' => 404 ] );
		}
	
		$count = wp_count_posts( $post_type );
		// Sum all statuses (publish, draft, pending, etc.)
		$total = array_sum( (array) $count );
	
		return rest_ensure_response( [ 'count' => $total ] );
	}	
}
new Theme_Site_Kit_API_Rest_Routes();
