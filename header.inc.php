<?php
session_start();

require_once dirname(__FILE__) .'/services/servicefactory.php';
require_once dirname(__FILE__) .'/config.inc.php';
require_once dirname(__FILE__) .'/functions.inc.php';

// Determine the base URL
if (!isset($root)) {
  $pieces = explode('/', $_SERVER['SCRIPT_NAME']);
  $root   = '/';
  foreach ($pieces as $piece) {
    if ($piece != '' && !strstr($piece, '.php')) {
      $root .= $piece .'/';
    }
  }
  if (($root != '/') && (substr($root, -1, 1) != '/')) {
    $root .= '/';
  }
  $path = $root;

  $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
  $root     = $protocol .'://'. $_SERVER['HTTP_HOST'] . $root;
}

define('GENERAL_MESSAGE',  200);
define('GENERAL_ERROR',    202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR',   204);

if (defined('SCUTTLE_DEBUG') && SCUTTLE_DEBUG) {
  ini_set('display_errors',   '1');
  ini_set('mysql.trace_mode', '1');
  error_reporting(E_ALL);
}
else {
  ini_set('display_errors',   '0');
  ini_set('mysql.trace_mode', '0');
  error_reporting(E_ALL);
}