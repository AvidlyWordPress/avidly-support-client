<?php
namespace Avidly\SupportClient\Admin;

class Settings_Page {

	/**
	 * Bootstrap the Admin screen.
	 */
	public function init() {

		add_action( 'admin_init', [ $this, 'init_settings' ] );
		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
	}

	/**
	 * Add the plugin options page to the Settings menu.
	 */
	public function add_settings_page() {

		// dashicons-carrot
		add_options_page( 'Settings Admin', 'Avidly Support', 'manage_options', 'avidly-support', [ $this, 'create_settings_page' ] );
	}

	/**
	 * Create the settings page.
	 */
	public function create_settings_page() {

		$this->display_settings_page();
	}

	/**
	 * Initialize the settings page.
	 */
	public function init_settings() {

		register_setting(
			'avidly-support-option-group',
			'avidly-support',
			[ $this, 'set_options' ]
		);

		add_settings_section(
			'avidly-support-auth',
			'Support site authentication',
			function() {},
			'avidly-support-settings'
		);

		add_settings_field(
			'avidly-support-auth',
			'Unique site key',
			[ $this, 'display_auth_form_field' ],
			'avidly-support-settings',
			'avidly-support-auth'
		);

		add_settings_section(
			'avidly-support-helpscout',
			'Help Scout settings',
			function() {},
			'avidly-support-settings'
		);

		add_settings_field(
			'avidly-support-helpscout-beacon',
			'HelpScout Beacon ID',
			[ $this, 'display_helpscout_beacon_form_field' ],
			'avidly-support-settings',
			'avidly-support-helpscout'
		);

		add_settings_field(
			'avidly-support-helpscout-beacon-front',
			'Show HelpScout beacon in front end?',
			[ $this, 'display_helpscout_beacon_front_form_field' ],
			'avidly-support-settings',
			'avidly-support-helpscout'
		);
	}

	/**
	 * Display the secret form field.
	 */
	public function display_auth_form_field() {
		$key = get_option( AVIDLY_SUPPORT_AUTH );
		echo '<input type="text" name="avidly-support[auth]" required value="' . esc_html( $key ) . '">';
	}

	/**
	 * Set the authentication value.
	 *
	 * @param array $input Input field.
	 * @return string Authentication value.
	 */
	public function set_options( $new_settings_values ) {
		$auth_key_value = isset( $new_settings_values['auth'] ) ? $new_settings_values['auth'] : '';
		if ( get_option( AVIDLY_SUPPORT_AUTH ) === false ) {
			add_option( AVIDLY_SUPPORT_AUTH, $auth_key_value );
		} else {
			update_option( AVIDLY_SUPPORT_AUTH, $auth_key_value );
		}

		$helpscout_beacon_value = isset( $new_settings_values['helpscout'] ) ? $new_settings_values['helpscout'] : '';
		if ( get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON ) === false ) {
			add_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON, $helpscout_beacon_value );
		} else {
			update_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON, $helpscout_beacon_value );
		}

		$helpscout_beacon_front_value = isset( $new_settings_values['helpscout-front'] ) ? $new_settings_values['helpscout-front'] : 'off';
		if ( get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT ) === false ) {
			add_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT, $helpscout_beacon_front_value );
		} else {
			update_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT, $helpscout_beacon_front_value );
		}

		return $auth_key_value;
	}

	/**
	 * Display the settings page.
	 *
	 */
	protected function display_settings_page() {
	?>
		<div class="wrap">
			<h2>Avidly Support Settings</h2>

			<form method="POST" action="options.php">
				<?php
				settings_fields( 'avidly-support-option-group' );
				do_settings_sections( 'avidly-support-settings' );
				submit_button();
				?>
			</form>
		</div>
	<?php
	}

	/**
	 * Display the HelpScout beacon key form field.
	 */
	public function display_helpscout_beacon_form_field() {
		$id = get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON );
		echo '<input type="text" length="30" name="avidly-support[helpscout]" value="' . esc_html( $id ) . '">';
		echo '<p class="description">Leave empty to hide beacon</p>';
	}

	/**
	 * Display the HelpScout beacon key form field.
	 */
	public function display_helpscout_beacon_front_form_field() {
		$id = get_option( AVIDLY_SUPPORT_HELPSCOUT_BEACON_FRONT );
		$checked = 'on' === $id ? 'checked="checked"' : '';
		echo '<input type="checkbox" length="30" name="avidly-support[helpscout-front]"' . esc_attr( $checked ) . '>';
	}
}
