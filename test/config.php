<?php

/**
 * Sample configuration file for Anax webroot.
 */


/**
 * Define essential Anax paths, end with /
 */
define("ANAX_INSTALL_PATH", realpath(__DIR__ . "/.."));



/**
 * Include autoloader.
 */
require ANAX_INSTALL_PATH . "/vendor/autoload.php";



/**
 * Use $di as global identifier (used in views by viewhelpers).
 */
$di = null;
