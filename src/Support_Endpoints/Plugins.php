<?php
/**
 * Plugins listing endpoint for Support Hub
 */

namespace Avidly\Support_Client\Support_Endpoints;

class Plugins extends Support_Endpoint {
	public function __construct() {
		$this->route = '/plugins/';
		$this->register();
	}
	public function callback() {
		return get_plugins();
	}
}