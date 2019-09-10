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

	// Generate an unique site key if not defined at all
	if ( get_option( AVIDLY_SUPPORT_AUTH ) === false ) {
		add_option( AVIDLY_SUPPORT_AUTH, wp_generate_password( '25', false ) );
	}

	$settings_page = new Settings_Page();
	$settings_page->init();

	if ( current_user_can( 'edit_pages' ) ) {
		add_action( 'admin_enqueue_scripts', 'Avidly\SupportClient\enqueue_helpscout_beacon' );
		add_action( 'admin_enqueue_scripts', 'Avidly\SupportClient\enqueue_custom_styles' );
	}
}

/**
 * Get the site option if it's multisite.
 *
 * @param String $option The name of the option to retrieve.
 * @param Mixed $default A value to return if the option doesn't exist.
 *
 * @return Mixed Current value for the specified option. If the specified option does not exist, returns boolean FALSE.
 */
function get_option( $option, $default = false ) {
	if ( ! $option ) {
		return;
	}
	if ( \is_multisite() ) {
		return \get_site_option( $option, $default );
	} else {
		return \get_option( $option, $default );
	}
}

/**
 * Add option or in multisite add site option
 *
 * @param String $option Name of the option to be added.
 * @param Mixed $value Value for this option name.
 *
 * @return Boolean False if option was not added and true if option was added
 */
function add_option( $option, $value = '' ) {
	if ( ! $option ) {
		return;
	}
	if ( \is_multisite() ) {
		return \add_site_option( $option, $value );
	} else {
		return \add_option( $option, $value );
	}
}

/**
 * Update option or in multisite update site option
 *
 * @param String $option Name of the option to be set.
 * @param Mixed $value Option value.
 *
 * @return Boolean False if value was not updated and true if value was updated.
 */
function update_option( $option, $value = '' ) {
	if ( ! $option ) {
		return;
	}
	if ( \is_multisite() ) {
		return \update_site_option( $option, $value );
	} else {
		return \update_option( $option, $value );
	}
}
