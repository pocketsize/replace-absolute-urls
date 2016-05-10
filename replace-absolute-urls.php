<?php
/*
Plugin Name: Replace Absolute URLs
Plugin URI: https://github.com/pocketsize/replace-absolute-urls
Description: Eliminates the need to search and replace URLs in your database when moving a site between development and production environments.
Version: 0.0.1
Author: Pocketsize
Author URI: http://www.pocketsize.se/
Author Email: info@pocketsize.se
Copyright: Pocketsize
License: MIT
*/

$scheme = ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) ? 'https' : 'http';
if ( isset($_SERVER['REQUEST_SCHEME']) ) $scheme = $_SERVER['REQUEST_SCHEME'];

define('RAU_INITIAL_HOST', get_option('siteurl'));
define('RAU_CURRENT_HOST', $scheme . '://' . $_SERVER['SERVER_NAME']);

define('WP_HOME',    RAU_CURRENT_HOST);
define('WP_SITEURL', RAU_CURRENT_HOST);

function rau_replace_urls( $buffer ) {
	return str_ireplace(
		array(
			RAU_INITIAL_HOST,
			urlencode( RAU_INITIAL_HOST ),
			trim( json_encode( RAU_INITIAL_HOST ), '"' )
		),
		array(
			RAU_CURRENT_HOST,
			urlencode( RAU_CURRENT_HOST ),
			trim( json_encode( RAU_CURRENT_HOST ), '"' )
		),
		$buffer
	);
}

function rau_buffer_start() { ob_start( 'rau_replace_urls' ); }
function rau_buffer_end()   { ob_end_flush(); }

add_action('after_setup_theme', 'rau_buffer_start');
add_action('shutdown',          'rau_buffer_end');