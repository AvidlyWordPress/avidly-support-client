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
			[ $this, 'set_auth_key' ]
		);

		add_settings_section(
			'avidly-support',
			'Auth Settings',
			function() {},
			'avidly-support-settings'
		);

		add_settings_field(
			'key',
			'Key',
			[ $this, 'display_key_form_field' ],
			'avidly-support-settings',
			'avidly-support'
		);
	}

	/**
	 * Display the secret form field.
	 */
	public function display_key_form_field() {

		$key = get_option( AVIDLY_SUPPORT_OPTION_KEY );
		echo '<input type="text" name="avidly-support[key]" value="' . esc_html( $key ) . '">';
	}

	/**
	 * Set the authentication value.
	 *
	 * @param array $input Input field.
	 * @return string Authentication value.
	 */
	public function set_auth_key( $new_settings_values ) {

		error_log( print_r( $new_settings_values, true ) );
		$shared_key_value = $new_settings_values['key'];
		if ( get_option( AVIDLY_SUPPORT_OPTION_KEY ) === false ) {
			add_option( AVIDLY_SUPPORT_OPTION_KEY, $shared_key_value );
		} else {
			update_option( AVIDLY_SUPPORT_OPTION_KEY, $shared_key_value );
		}

		return $shared_key_value;
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

}
