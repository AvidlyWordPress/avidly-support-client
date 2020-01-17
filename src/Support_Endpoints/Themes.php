<?php
/**
 * Updates listing endpoint for Support Hub
 */

namespace Avidly\Support_Client\Support_Endpoints;

class Themes extends Support_Endpoint {
	public function __construct() {
		$this->route = '/themes/';
		$this->register();
	}
	public function callback() {
		$theme_objects = wp_get_themes();
		$themes = [];
		foreach ( $theme_objects as $slug => $theme_object ) {

			$themes[ $slug ] = [
				'name'         => $theme_object->name,
				'version'      => $theme_object->version,
				'parent_theme' => $theme_object->parent_theme,
				'template_dir' => $theme_object->template_dir,
				'description'  => $theme_object->description,
				'author'       => $theme_object->author,
			];
		}
		return $themes;
	}
}