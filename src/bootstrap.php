<?php
namespace Avidly\SupportClient;

/**
 * Bootstrap the plugin, adding required actions and filters.
 *
 * @action init
 */
function bootstrap() {

	add_action( 'wp', __NAMESPACE__ . '\run' );
	add_action( 'rest_api_init', __NAMESPACE__ . '\register_rest_routes' );

	if ( current_user_can( 'edit_pages' ) && 'on' === get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT ) ) {
		add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_helpscout_beacon' );
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

	register_rest_route( 'avidly-support/v1', '/plugins/(?P<key>.+)', [
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\support_get_plugins',
		'args'     => [
			'key' => [
				'validate_callback' => __NAMESPACE__ . '\rest_validation',
			],
		],
	] );

	register_rest_route( 'avidly-support/v1', '/themes/(?P<key>.+)', [
		'methods'  => 'GET',
		'callback' => __NAMESPACE__ . '\support_get_themes',
		'args'     => [
			'key' => [
				'validate_callback' => __NAMESPACE__ . '\rest_validation',
			],
		],
	] );

	register_rest_route( 'avidly-support/v1', '/updates/(?P<key>.+)', [
		'methods' => 'GET',
		'callback' => __NAMESPACE__ . '\support_get_updates',
		'args' => [
			'key' => [
				'validate_callback' => __NAMESPACE__ . '\rest_validation',
			],
		],
	] );

	register_rest_route( 'avidly-support/v1', '/core/(?P<key>.+)', [
		'methods' => 'GET',
		'callback' => __NAMESPACE__ . '\support_get_core',
		'args' => [
			'key' => [
				'validate_callback' => __NAMESPACE__ . '\rest_validation',
			],
		],
	] );
}

/**
 * Register the REST routes.
 */
function rest_validation( $param, $request, $key ) {

	$site_key = is_multisite() ? get_site_option( AVIDLY_SUPPORT_OPTION_KEY ) : get_option( AVIDLY_SUPPORT_OPTION_KEY );
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
 * Embed HelpScout beacon
 */
function enqueue_helpscout_beacon() {
	$plugin_data    = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'] ?: '1.0.0';
	$beacon_id      = is_multisite() ? get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON ) : get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON );

	if ( ! $beacon_id ) {
		return;
	}

	wp_enqueue_script(
		'avidly-helpscout-beacon',
		plugin_dir_url( __DIR__ ) . 'js/helpscout-beacon.js',
		[],
		$plugin_version,
		true
	);

	wp_localize_script(
		'avidly-helpscout-beacon',
		'avidlyHelpScout',
		[
			'beaconId' => $beacon_id,
		]
	);
}
/**
 * Add different custom stylesheets depending on the situation.
 */
function enqueue_custom_styles() {
	if ( strpos( site_url(), 'testbox.fi' ) ) {
		wp_enqueue_style(
			'staging-styles',
			plugin_dir_url( __DIR__ ) . 'css/staging.css'
		);
	}
	if ( strpos( site_url(), '.test/' ) ) {
		wp_enqueue_style(
			'local-styles',
			plugin_dir_url( __DIR__ ) . 'css/local.css'
		);
	}
}
