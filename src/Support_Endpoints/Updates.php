<?php
/**
 * Updates listing endpoint for Support Hub
 */

namespace Avidly\Support_Client\Support_Endpoints;

class Updates extends Support_Endpoint {
	public function __construct() {
		$this->route = '/updates/';
		$this->register();
	}
	public function callback() {
		return [
			'plugins' => get_plugin_updates(),
			'themes'  => get_theme_updates(),
			'core'    => get_core_updates(),
		];
	}
}