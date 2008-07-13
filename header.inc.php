<?php
ini_set('display_errors', '1');
ini_set('mysql.trace_mode', '0');

error_reporting(E_ALL ^ E_NOTICE);

define('DEBUG', true);
session_start();

require_once(dirname(__FILE__) .'/services/servicefactory.php');
require_once(dirname(__FILE__) .'/config.inc.php');
require_once(dirname(__FILE__) .'/functions.inc.php');

// Determine the base URL
if (!isset($root)) {
    $pieces = explode('/', $_SERVER['SCRIPT_NAME']);
    $root = '/';
    foreach($pieces as $piece) {
        if ($piece != '' && !strstr($piece, '.php')) {
            $root .= $piece .'/';
        }
    }
    if (($root != '/') && (substr($root, -1, 1) != '/')) {
        $root .= '/';
    }
    $path = $root;
    $root = 'http://'. $_SERVER['HTTP_HOST'] . $root;
}

// Error codes
define('GENERAL_MESSAGE', 200);
define('GENERAL_ERROR', 202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR', 204);
?>