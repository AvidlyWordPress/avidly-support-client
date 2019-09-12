<?php
namespace Avidly\Support_Client;

/**
 * Bootstrap the plugin, adding required actions and filters.
 *
 * @action init
 */
function bootstrap() {

	add_action( 'wp', __NAMESPACE__ . '\run' );
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_rest_routes' );
	add_filter( 'auto_update_plugin', __NAMESPACE__ . '\auto_update_plugin', 10, 2 );
	add_action( 'init', __NAMESPACE__ . 'set_default_values' );

	if ( show_beacon() ) {
		$beacon = new Beacon();
	}

	if ( current_user_can( 'publish_posts' ) ) {
		add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_custom_styles' );
	}
}


function run() {

	if ( ! dependencies_exist() ) {
		error_log( 'Some dependencies could not be fulfilled. Check the list of dependencies.' );
		return;
	}
}

/**
 * Checks if all the dependencies are set.
 */
function dependencies_exist() {
	return true;
}

/**
 * Register the REST routes.
 */
function register_rest_routes() {

	register_rest_route(
		'avidly-support-client/v1',
		'/plugins/(?P<key>.+)',
		[
			'methods'  => 'GET',
			'callback' => __NAMESPACE__ . '\support_get_plugins',
			'args'     => [
				'key' => [
					'validate_callback' => __NAMESPACE__ . '\rest_validation',
				],
			],
		]
	);

	register_rest_route(
		'avidly-support-client/v1',
		'/themes/(?P<key>.+)',
		[
			'methods'  => 'GET',
			'callback' => __NAMESPACE__ . '\support_get_themes',
			'args'     => [
				'key' => [
					'validate_callback' => __NAMESPACE__ . '\rest_validation',
				],
			],
		]
	);

	register_rest_route(
		'avidly-support-client/v1',
		'/updates/(?P<key>.+)',
		[
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\support_get_updates',
			'args' => [
				'key' => [
					'validate_callback' => __NAMESPACE__ . '\rest_validation',
				],
			],
		]
	);

	register_rest_route(
		'avidly-support-client/v1',
		'/core/(?P<key>.+)',
		[
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\support_get_core',
			'args' => [
				'key' => [
					'validate_callback' => __NAMESPACE__ . '\rest_validation',
				],
			],
		]
	);
}

/**
 * Register the REST routes.
 */
function rest_validation( $param, $request, $key ) {

	$site_key = get_site_option( AVIDLY_SUPPORT_AUTH );
	$safe_key = sanitize_text_field( $param );

	if ( $site_key === $safe_key ) {
		return true;
	} else {
		return new \WP_Error( 'authentication-error', 'Authentication failed' );
	}
}

/**
 * Get the plugin information.
 *
 * @return array {
 * }
 */
function support_get_plugins() {
	return get_plugins();
}

/**
 * Get the theme information.
 *
 * @return array {
 * }
 */
function support_get_themes() {

	$theme_objects = wp_get_themes();
	$themes = [];
	foreach ( $theme_objects as $slug => $theme_object ) {

		$themes[ $slug ] = [
			'name'         => $theme_object->name,
			'version'      => $theme_object->version,
			'parent_theme' => $theme_object->parent_theme,
			'template_dir' => $theme_object->template_dir,
			'description'  => $theme_object->description,
			'author'       => $theme_object->author,
		];
	}
	return $themes;
}

/**
 * Get the available core, plugin, and theme updates.
 *
 * @return array {
 *
 * }
 */
function support_get_updates() {
	return [
		'plugins' => get_plugin_updates(),
		'themes'  => get_theme_updates(),
		'core'    => get_core_updates(),
	];
}

/**
 * Get the core information.
 *
 * @return array {
 * }
 */
function support_get_core() {

	return [
		'wordpress_version' => get_bloginfo( 'version' ),
		'php_version'       => phpversion(),
		'is_multisite'      => is_multisite(),
		'site_count'        => is_multisite() ? get_blog_count() : 1,
		'wp_install'        => is_multisite() ? network_site_url() : home_url( '/' ),
	];
}

/**
 * Add different custom stylesheets depending on the environment.
 */
function enqueue_custom_styles() {
	if ( 'production' === current_environment() ) {
		return;
	}

	if ( 'local' === current_environment() ) {
		wp_enqueue_style(
			'local-styles',
			plugin_dir_url( __DIR__ ) . 'css/local.css'
		);
	} elseif ( 'staging' === current_environment() ) {
		wp_enqueue_style(
			'staging-styles',
			plugin_dir_url( __DIR__ ) . 'css/staging.css'
		);
	}
}

/**
 * Figure out which environment we are in currently
 *
 * @return String Current environment
 */
function current_environment() {
	$site_url     = site_url();
	$parsed_url   = wp_parse_url( $site_url );
	$exploded_url = explode( '.', $parsed_url['host'] );
	$tld          = array_pop( $exploded_url );
	$hostname     = array_pop( $exploded_url );

	// Set the local tld:s
	$local_tlds = [
		'test',
		'local',
	];

	// Set the staging hosts to match url
	$staging_hosts = [
		'testbox',
		'wptest',
	];
/*
	if ( in_array( $tld, $local_tlds, true ) ) {
		return 'local';
	}

	if ( in_array( $hostname, $staging_hosts, true ) ) {
		return 'staging';
	}*/
	return 'production';
}

/**
 * Turn auto updating on for this plugin
 */
function auto_update_plugin( $update, $item ) {
	if ( 'avidly-support-client' === $item->slug ) {
		return true;
	} else {
		return $update;
	}
}

/**
 * Load plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'avidly-support-client', false, 'avidly-support/languages' );
}

/**
 * Set default values in the first run
 */
function set_default_values_to_options() {
	// Generate an unique site key if not defined at all
	if ( get_site_option( AVIDLY_SUPPORT_AUTH ) === false ) {
		add_site_option( AVIDLY_SUPPORT_AUTH, bin2hex( random_bytes( 20 ) ) );
	}
	// Set default HelpScout Beacon ID
	if ( get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON ) === false ) {
		add_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON, BEACON_ID );
	}
	// Set default value if to show Beacon in front end.
	if ( get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT ) === false ) {
		add_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT, 'off' );
	}
}

/**
 * Whether to show Beacon
 *
 * @return Boolean
 */
function show_beacon() {
	if ( ! get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON ) ) {
		return false;
	}
	if ( ! current_user_can( 'publish_posts' ) ) {
		return false;
	}
	if ( ! is_admin() && 'off' === get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT ) ) {
		return false;
	}
	if ( 'production' !== current_environment() ) {
		return false;
	}
	return true;
}
