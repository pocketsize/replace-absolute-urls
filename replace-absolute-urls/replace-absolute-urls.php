<?php
/*
Plugin Name: Replace Absolute URLs
Plugin URI: https://github.com/pocketsize/replace-absolute-urls
Description: Eliminates the need to search and replace URLs in your database when moving a site between development and production environments.
Version: 0.0.2
Author: Pocketsize
Author URI: http://www.pocketsize.se/
Author Email: info@pocketsize.se
Copyright: Pocketsize
License: MIT
*/

$scheme = ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ) ? 'https' : 'http';
if ( isset($_SERVER['REQUEST_SCHEME']) ) $scheme = $_SERVER['REQUEST_SCHEME'];
define( 'RAU_SCHEME', $scheme );

define( 'RAU_INITIAL_HOST', get_option('siteurl') );
define( 'RAU_CURRENT_HOST', $scheme . '://' . $_SERVER['SERVER_NAME'] );

define( 'WP_HOME',    RAU_CURRENT_HOST );
define( 'WP_SITEURL', RAU_CURRENT_HOST );

function rau_replace_urls( $buffer, $scheme = true ) {
	$initial = RAU_INITIAL_HOST;
	$current = RAU_CURRENT_HOST;

	if ( $scheme == false ) {
		$initial = substr( RAU_INITIAL_HOST, strpos(RAU_INITIAL_HOST, '//') );
		$current = substr( RAU_CURRENT_HOST, strpos(RAU_CURRENT_HOST, '//') );
	}

	return str_ireplace(
		array(
			$initial,
			urlencode( $initial ),
			trim( json_encode( $initial ), '"' )
		),
		array(
			$current,
			urlencode( $current ),
			trim( json_encode( $current ), '"' )
		),
		$buffer
	);
}

function rau_replace_callback( $buffer ) {
	$buffer = rau_replace_urls( $buffer );
	return rau_replace_urls( $buffer, false );
}

function rau_buffer_start() { ob_start( 'rau_replace_callback' ); }
function rau_buffer_end()   { ob_end_flush(); }

add_action( 'after_setup_theme', 'rau_buffer_start' );
add_action( 'shutdown',          'rau_buffer_end' );