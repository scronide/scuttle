<?php
//  Provides HTTP Basic authentication of a user, and sets two variables, sId and username,
//  with the user's info.

function authenticate() {
    header('WWW-Authenticate: Basic realm="del.icio.us API"');
    header('HTTP/1.0 401 Unauthorized');
    die(T_('Use of the API calls requires authentication.'));
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    authenticate();
} else {
    require_once('../header.inc.php');
    $userservice =& ServiceFactory::getServiceInstance('UserService');

    $login = $userservice->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']); 
    if (!$login) {
        authenticate();
    }
}
?>