<?php
namespace Avidly\SupportClient\Admin;

/**
 * Bootstrap the plugin, adding required actions and filters
 *
 * @action init
 */
function bootstrap() {
	if ( ! is_admin() ) {
		return;
	}

	// Generate an unique site key if not defined
	if ( ! get_option( AVIDLY_SUPPORT_OPTION_KEY ) === false ) {
		add_option( AVIDLY_SUPPORT_OPTION_KEY, wp_generate_password( '25', false ) );
	} else {
		update_option( AVIDLY_SUPPORT_OPTION_KEY, wp_generate_password( '25', false ) );
	}

	$settings_page = new Settings_Page();
	$settings_page->init();

	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_helpscout_beacon' );
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

	$user = wp_get_current_user();
	wp_localize_script(
		'avidly-helpscout-beacon',
		'avidlyHelpScout',
		[
			'beaconId'  => $beacon_id,
		]
	);
}
