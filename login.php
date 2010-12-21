<?php
/***************************************************************************
Copyright (c) 2004 - 2006 Marcus Campbell
http://scuttle.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
***************************************************************************/

require_once 'header.inc.php';
$userservice     =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

$tplVars = array();

$login = false;
if (isset($_POST['submitted']) && isset($_POST['username']) && isset($_POST['password'])) {
    $posteduser = trim(utf8_strtolower($_POST['username']));
    $login      = $userservice->login($posteduser, $_POST['password'], ($_POST['keeppass'] == 'yes'), $path); 
    if ($login) {
        if ($_POST['query'])
            header('Location: '. createURL('bookmarks', $posteduser .'?'. $_POST['query']));
        else
            header('Location: '. createURL('bookmarks', $posteduser));
    } else {
        $tplVars['error'] = T_('The details you have entered are incorrect. Please try again.');
    }
}
if (!$login) { 
    if ($userservice->isLoggedOn()) {
        $cUser = $userservice->getCurrentUser();
        $cUsername = strtolower($cUser[$userservice->getFieldName('username')]);
        header('Location: '. createURL('bookmarks', $cUsername));
    }

    $tplVars['subtitle']    = T_('Log In');
    $tplVars['formaction']  = createURL('login');
    $tplVars['querystring'] = filter($_SERVER['QUERY_STRING']);
    $templateservice->loadTemplate('login.tpl', $tplVars);
}
?>
