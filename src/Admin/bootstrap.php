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

	$settings_page = new Settings_Page();
	$settings_page->init();
}
