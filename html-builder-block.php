<?php
/*
Plugin Name: HTML Builder Block
Plugin URI: http://www.headwaylabs.com
Description: Create blocks using common html elements then position and style them.
Author: Headway Labs
Version: 1.0
Author URI: http://www.headwaylabs.com
License: GNU GPL v2
*/

define('HTML_BUILDER_BLOCK_VERSION', '1.0');

add_action('after_setup_theme', 'html_builder_block_register');
function html_builder_block_register() {

	/* Make sure that Headway is activated, otherwise don't register the block because errors will be thrown. */
	if ( !class_exists('Headway') )
		return;
	
	require_once 'block.php';
	require_once 'block-options.php';

	return headway_register_block('HeadwayHTMLBuilderBlock', plugins_url(false, __FILE__));

}

add_action('init', 'html_builder_block_extend_updater');
function html_builder_block_extend_updater() {

	if ( !class_exists('HeadwayUpdaterAPI') )
		return;

	$updater = new HeadwayUpdaterAPI(array(
		'slug' => 'html-builder-block',
		'path' => plugin_basename(__FILE__),
		'name' => 'HTML Builder Block',
		'type' => 'block',
		'current_version' => HTML_BUILDER_BLOCK_VERSION
	));

}

/* include admin css
***************************************************************/
function builder_admin_css() {
	HeadwayCompiler::register_file(array(
		'name' => 'html-builder-admin-css',
		'format' => 'css',
		'fragments' => array(
			dirname(__FILE__).'/admin/css/admin.css'
		)
	));
}
add_action('headway_visual_editor_styles', 'builder_admin_css', 12);


/* include admin js
***************************************************************/
function builder_admin_js() {
	HeadwayCompiler::register_file(array(
		'name' => 'html_builder-admin-js',
		'format' => 'js',
		'fragments' => array(
			dirname(__FILE__).'/admin/js/admin.js'
		)
	));
}
add_action('headway_visual_editor_scripts', 'builder_admin_js', 12);