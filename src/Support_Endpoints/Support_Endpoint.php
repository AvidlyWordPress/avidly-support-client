<?php
/**
 * Generate REST API endpoint for Support Hub
 */

namespace Avidly\Support_Client\Support_Endpoints;

class Support_Endpoint {
	/**
	 * URL namespace to use
	 * @var String
	 */
	public $namespace = 'avidly-support-client/v1';

	/**
	 * Endpoint route name
	 * @var String
	 */
	public $route;

	/**
	 * Register REST endpoint
	 */
	public function register() {
		$route = $this->route . '(?P<key>.+)';
		register_rest_route(
			$this->namespace,
			$route,
			[
				'methods' => 'GET',
				'callback' => [ $this, 'callback' ],
				'args' => [
					'key' => [
						'validate_callback' => [ $this, 'validate' ],
					],
				],
			]
		);
	}

	/**
	 * The API callback function
	 */
	public function callback() {}

	/**
	 * Endpoint validation
	 */
	public function validate( $param ) {
		$site_key = get_site_option( AVIDLY_SUPPORT_AUTH );
		$safe_key = sanitize_text_field( $param );

		if ( $site_key === $safe_key ) {
			return true;
		} else {
			return new \WP_Error( 'authentication-error', 'Authentication failed' );
		}
	}
}
