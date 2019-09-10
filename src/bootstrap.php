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
	add_filter( 'auto_update_plugin', __NAMESPACE__ . '\auto_update_plugin', 10, 2 );

	if ( current_user_can( 'publish_posts' ) && 'on' === get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT ) ) {
		add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_helpscout_beacon' );
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

	$site_key = is_multisite() ? get_site_option( AVIDLY_SUPPORT_AUTH ) : get_option( AVIDLY_SUPPORT_AUTH );
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
	$user_info = get_userdata( get_current_user_id() );
	wp_localize_script(
		'avidly-helpscout-beacon',
		'avidlyHelpScout',
		[
			'userEmail'               => $user_info->user_email,
			'userName'                => $user_info->user_nicename,
			'beaconId'                => $beacon_id,
			'text'                    => __( 'Do you need help?', 'avidly-support' ),
			'sendAMessage'            => __( 'Avidly Support', 'avidly-support' ),
			'howCanWeHelp'            => __( 'How can we help?', 'avidly-support' ),
			'responseTime'            => __( 'We usually respond in a few hours', 'avidly-support' ),
			'uploadAnImage'           => __( 'Upload an image', 'avidly-support' ),
			'attachAFile'             => __( 'Attach a file', 'avidly-support' ),
			'continueEditing'         => __( 'Continue writing…', 'avidly-support' ),
			'lastUpdated'             => __( 'Last updated', 'avidly-support' ),
			'you'                     => __( 'You', 'avidly-support' ),
			'nameLabel'               => __( 'Name', 'avidly-support' ),
			'subjectLabel'            => __( 'Subject', 'avidly-support' ),
			'emailLabel'              => __( 'Email address', 'avidly-support' ),
			'messageLabel'            => __( 'How can we help?', 'avidly-support' ),
			'messageSubmitLabel'      => __( 'Send a message', 'avidly-support' ),
			'next'                    => __( 'Next', 'avidly-support' ),
			'weAreOnIt'               => __( 'We’re on it!', 'avidly-support' ),
			'messageConfirmationText' => __( 'You’ll receive an email reply within a few hours.', 'avidly-support' ),
		]
	);
}
/**
 * Add different custom stylesheets depending on the situation.
 */
function enqueue_custom_styles() {
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

	if ( in_array( $tld, $local_tlds, true ) ) {
		wp_enqueue_style(
			'local-styles',
			plugin_dir_url( __DIR__ ) . 'css/local.css'
		);
	} elseif ( in_array( $hostname, $staging_hosts, true ) ) {
		wp_enqueue_style(
			'staging-styles',
			plugin_dir_url( __DIR__ ) . 'css/staging.css'
		);
	}
}

/**
 * Turn auto updating on for this plugin
 */
function auto_update_plugin( $update, $item ) {
	// Array of plugin slugs to always auto-update
	$plugins = [
		'avidly-support',
	];
	if ( in_array( $item->slug, $plugins, true ) ) {
		return true;
	} else {
		return $update;
	}
}

/**
 * Load plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'avidly-support', false, 'avidly-support/languages' );
}
