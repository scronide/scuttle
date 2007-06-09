<?php
// Provides HTTP Basic authentication of a user

function authenticate() {
    header('WWW-Authenticate: Basic realm="del.icio.us API"');
    header('HTTP/1.0 401 Unauthorized');
    die("Use of the API calls requires authentication.");
}

require_once('../header.inc.php');
if (isset($_GET['Authorization'])) {
    if (preg_match('/Basic\s+(.*)$/i', $_GET['Authorization'], $Authorization)) {
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode($Authorization[1]));
    }
}
if (isset($_SERVER['PHP_AUTH_USER']) && strlen($_SERVER['PHP_AUTH_PW']) > 0) {
    $userservice    =& ServiceFactory::getServiceInstance('UserService');
    $login          = $userservice->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    if ($login['result'] === false) {
        authenticate();
    }
} else {
    authenticate();
}
?>