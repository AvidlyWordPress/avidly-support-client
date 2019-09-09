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

	if ( current_user_can( 'edit_pages' ) ) {
		add_action( 'admin_enqueue_scripts', 'Avidly\SupportClient\enqueue_helpscout_beacon' );
	}
}
