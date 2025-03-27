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
}
new Theme_Site_Kit_API_Rest_Routes();
