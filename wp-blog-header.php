<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/src/functions.php';

$request = Request::createFromGlobals();

ob_start();

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	require_once( dirname(__FILE__) . '/wp-load.php' );

	wp();

	require_once( ABSPATH . WPINC . '/template-loader.php' );

}

$buffer = ob_get_clean();
$response = Response::create($buffer);

$response->send();
