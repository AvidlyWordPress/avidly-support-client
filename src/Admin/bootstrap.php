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
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\clean_dashboard', 99 );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\avidly_dashboard_widgets' );
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

/**
 * Clean dashboard
 */
function clean_dashboard() {
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	remove_action( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
	remove_action( 'welcome_panel', 'wp_welcome_panel' );

	if ( ! current_user_can( 'update_core' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
}

function avidly_dashboard_widgets() {
	wp_add_dashboard_widget( 'avidly_dashboard_widget', __( 'Avidly Support', 'avidly-support' ), __NAMESPACE__ . '\avidly_dashboard_widget_general_info' );
}

function avidly_dashboard_widget_general_info() {
	?>
	<p><?php echo wp_kses_post( __( 'This site is maintained and updated by Avidly Support team.', 'avidly-support' ) ); ?></p>
	<h3><?php esc_html_e( 'Do you need help?', 'avidly-support' ); ?></h3>
	<p>
		<ul>
			<?php if ( get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON ) ) : ?>
			<li><?php esc_html_e( 'Send us a ticket by clicking', 'avidly-support' ); ?> <a href="#" onClick="window.Beacon('open')"><?php esc_html_e( 'Do you need help', 'avidly-support' ); ?></a> <?php esc_html_e( 'or', 'avidly-support' ); ?></li>
			<?php endif; ?>
			<li><?php esc_html_e( 'Send email to:', 'avidly-support' ); ?> <a href="mailto:help@avidlyagency.com">help@avidlyagency.com</a></li>
		</ul>
	</p>
	<?php
}
