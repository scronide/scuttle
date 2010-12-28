<?php
// Turn debugging on
define('SCUTTLE_DEBUG', FALSE);

// generic debugging function
// Sample:
//     pc_debug(__FILE__, __LINE__, "This is a debug message.");

function pc_debug($file, $line, $message) {
  if (defined('SCUTTLE_DEBUG') && SCUTTLE_DEBUG) {
    error_log("---DEBUG-". $sitename .": [$file][$line]: $message");
  }
  else {
    error_log("SCUTTLE_DEBUG disabled");
  }
}
