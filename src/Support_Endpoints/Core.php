<?php
/**
 * Core settings listing endpoint for Support Hub
 */

namespace Avidly\Support_Client\Support_Endpoints;

class Core extends Support_Endpoint {
	public function __construct() {
		$this->route = '/core/';
		$this->register();
	}
	public function callback() {
		return [
			'wordpress_version' => get_bloginfo( 'version' ),
			'php_version'       => phpversion(),
			'is_multisite'      => is_multisite(),
			'site_count'        => is_multisite() ? get_blog_count() : 1,
			'wp_install'        => is_multisite() ? network_site_url() : home_url( '/' ),
		];
	}
}