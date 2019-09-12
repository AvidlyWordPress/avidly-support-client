<?php
/**
 * HelpScout Beacon
 */
namespace Avidly\Support_Client;

class Beacon {
	public $beacon_id;

	public function __construct() {
		$this->beacon_id = get_site_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON );
		if ( ! $this->beacon_id ) {
			return;
		}

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'localize' ] );
		} else {
			add_action( 'enqueue_scripts', [ $this, 'enqueue' ] );
			add_action( 'enqueue_scripts', [ $this, 'localize' ] );
		}
	}
	/**
	 * Embed HelpScout beacon
	 */
	public function enqueue() {
		$plugin_data    = get_plugin_data( __FILE__ );
		$plugin_version = $plugin_data['Version'] ?: '1.0.0';

		wp_enqueue_script(
			'avidly-helpscout-beacon',
			plugin_dir_url( __DIR__ ) . 'js/helpscout-beacon.js',
			[],
			$plugin_version,
			true
		);
	}
	/**
	 * Localize parameters and translations in JS
	 */
	public function localize() {
		$user_info = get_userdata( get_current_user_id() );
		wp_localize_script(
			'avidly-helpscout-beacon',
			'avidlyHelpScout',
			[
				'userEmail'    => $user_info->user_email,
				'userName'     => $user_info->user_nicename,
				'beaconId'     => $this->beacon_id,
				'signature'    => hash_hmac(
					'sha256',
					$user_info->user_emails,
					BEACON_SECRET
				),
				'translations' => [
					'text'                    => __( 'Do you need help?', 'avidly-support-client' ),
					'sendAMessage'            => __( 'Avidly Support', 'avidly-support-client' ),
					'howCanWeHelp'            => __( 'How can we help?', 'avidly-support-client' ),
					'responseTime'            => __( 'We usually respond in a few hours', 'avidly-support-client' ),
					'uploadAnImage'           => __( 'Upload an image', 'avidly-support-client' ),
					'attachAFile'             => __( 'Attach a file', 'avidly-support-client' ),
					'continueEditing'         => __( 'Continue writing…', 'avidly-support-client' ),
					'lastUpdated'             => __( 'Last updated', 'avidly-support-client' ),
					'you'                     => __( 'You', 'avidly-support-client' ),
					'nameLabel'               => __( 'Name', 'avidly-support-client' ),
					'subjectLabel'            => __( 'Subject', 'avidly-support-client' ),
					'emailLabel'              => __( 'Email address', 'avidly-support-client' ),
					'messageLabel'            => __( 'How can we help?', 'avidly-support-client' ),
					'messageSubmitLabel'      => __( 'Send a message', 'avidly-support-client' ),
					'next'                    => __( 'Next', 'avidly-support-client' ),
					'weAreOnIt'               => __( 'We’re on it!', 'avidly-support-client' ),
					'messageConfirmationText' => __( 'You’ll receive an email reply within a few hours.', 'avidly-support-client' ),
				]
			]
		);
	}
}
