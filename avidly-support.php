<?php /*
Plugin Name: Avidly Support plugin
Plugin URI: http://avidlyagency.com
Description: Creates a REST API endpoint for monitoring the status of core and plugin updates of our client sites
Version: 0.1
Author: Niku Hietanen / Avidly
Author URI: http://avidlyagency.com
License: GPL3
*/

/* Load plugin.php if not logged in */
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/update.php';
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'avidly-support/v1', '/plugins/(?P<key>.+)', array(
			'methods' => 'GET',
			'callback' => 'avidly_support_get_plugins',
			'args' => array(
				'key' => array(
					'validate_callback' => 'avidly_support_validation',
				),
			),
		)
	);
	register_rest_route( 'avidly-support/v1', '/themes/(?P<key>.+)', array(
			'methods' => 'GET',
			'callback' => 'avidly_support_get_themes',
			'args' => array(
				'key' => array(
					'validate_callback' => 'avidly_support_validation',
				),
			),
		)
	);
	register_rest_route( 'avidly-support/v1', '/updates/(?P<key>.+)', array(
			'methods' => 'GET',
			'callback' => 'avidly_support_get_updates',
			'args' => array(
				'key' => array(
					'validate_callback' => 'avidly_support_validation',
				),
			),
		)
	);
	register_rest_route( 'avidly-support/v1', '/core/(?P<key>.+)', array(
			'methods' => 'GET',
			'callback' => 'avidly_support_get_core',
			'args' => array(
				'key' => array(
					'validate_callback' => 'avidly_support_validation',
				),
			),
		)
	);
} );

function avidly_support_validation( $param, $request, $key ) {
	$safe_key = sanitize_text_field( $param );
	if ( '7immrMbgU9Qw62DcF7PizX$2LA' === $safe_key ) {
		return true;
	} else {
		return new WP_Error( 'authentication-error', 'Authentication failed, check key' );
	}
}

function avidly_support_get_plugins() {
	return get_plugins();
}

function avidly_support_get_themes() {
	$theme_objects = wp_get_themes();
	$themes = [];
	foreach ($theme_objects as $slug => $theme_object) {
		$themes[$slug] = [
			'name' => $theme_object->name,
			'version' => $theme_object->version,
			'parent_theme' => $theme_object->parent_theme,
			'template_dir' => $theme_object->template_dir,
			'description' => $theme_object->description,
			'author' => $theme_object->author,
		];
	}
	return $themes;
}

function avidly_support_get_updates() {
	return [
		'plugins' => get_plugin_updates(),
		'themes'  => get_theme_updates(),
	];
}

function avidly_support_get_core() {
	return [
		'wordpress_version' => get_bloginfo( 'version' ),
		'php_version'       => phpversion(),
		'is_multisite'      => is_multisite(),
		'site_count'        => is_multisite() ? get_blog_count() : 1,
		'wp_install'        => is_multisite() ? network_site_url() : home_url( '/' ),
		'core_updates'      => get_core_updates(),
	];
}
