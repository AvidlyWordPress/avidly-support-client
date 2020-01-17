<?php
namespace Avidly\Support_Client\Admin;

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

	$settings_page = new Settings_Page();
	$settings_page->init();
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
	// remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	// remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	remove_action( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
	remove_action( 'welcome_panel', 'wp_welcome_panel' );

	if ( ! current_user_can( 'update_core' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
}

function avidly_dashboard_widgets() {
	wp_add_dashboard_widget(
		'avidly_dashboard_widget',
		__( 'Avidly Support', 'avidly-support-client' ),
		__NAMESPACE__ . '\avidly_dashboard_widget_general_info'
	);
}

function avidly_dashboard_widget_general_info() {
	?>
	<p><?php echo wp_kses_post( __( 'This site is maintained and updated by Avidly Support team.', 'avidly-support-client' ) ); ?></p>
	<h3><?php esc_html_e( 'Do you need help?', 'avidly-support-client' ); ?></h3>
	<p>
		<ul>
			<?php if ( get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON ) ) : ?>
			<li><?php esc_html_e( 'Send us a ticket by clicking', 'avidly-support-client' ); ?> <a href="#" onClick="window.Beacon('open')"><?php esc_html_e( 'Do you need help', 'avidly-support-client' ); ?></a> <?php esc_html_e( 'or', 'avidly-support-client' ); ?></li>
			<?php endif; ?>
			<li><?php esc_html_e( 'Send email to:', 'avidly-support-client' ); ?> <a href="mailto:help@avidlyagency.com">help@avidlyagency.com</a></li>
		</ul>
	</p>
	<?php
}
