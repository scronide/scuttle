<?php
/***************************************************************************
Copyright (C) 2004 - 2006 Marcus Campbell
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
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

$tplVars = array();

@list($url, $user) = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : NULL;

$loggedon = false;
if ($userservice->isLoggedOn()) {
    $loggedon = true;
    $currentUser = $userservice->getCurrentUser();
    $currentUserID = $userservice->getCurrentUserId();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];
}

if ($user) {
    if (is_int($user)) {
        $userid = intval($user);
    } else {
        $user = urldecode($user);
        if (!($userinfo = $userservice->getUserByUsername($user))) {
            $tplVars['error'] = sprintf(T_('User with username %s was not found'), $user);
            $templateservice->loadTemplate('error.404.tpl', $tplVars);
            exit();
        } else {
            $userid =& $userinfo['uId'];
        }
    }
} else {
    $tplVars['error'] = T_('Username was not specified');
    $templateservice->loadTemplate('error.404.tpl', $tplVars);
    exit();
}

if ($user == $currentUsername) {
    $title = T_('My Profile');
} else {
    $title = T_('Profile') .': '. $user;
}
$tplVars['pagetitle'] = $title;
$tplVars['subtitle'] = $title;

$tplVars['user'] = $user;
$tplVars['userid'] = $userid;

if (isset($_POST['submitted'])) {
    $error = false;
    $detPass = trim($_POST['pPass']);
    $detPassConf = trim($_POST['pPassConf']);
    $detName = trim($_POST['pName']);
    $detMail = trim($_POST['pMail']);
    $detPage = trim($_POST['pPage']);
    $detDesc = filter($_POST['pDesc']);
    if ($detPass != $detPassConf) {
        $error = true;
        $tplVars['error'] = T_('Password and confirmation do not match.');
    }
    if ($detPass != "" && strlen($detPass) < 6) {
        $error = true;
        $tplVars['error'] = T_('Password must be at least 6 characters long.');
    }
    if (!$userservice->isValidEmail($detMail)) {
        $error = true;
        $tplVars['error'] = T_('E-mail address is not valid.');
    }
    if (!$error) {
        if (!$userservice->updateUser($userid, $detPass, $detName, $detMail, $detPage, $detDesc)) {
            $tplvars['error'] = T_('An error occurred while saving your changes.');
        } else {
            $tplVars['msg'] = T_('Changes saved.');
        }
    }
    $userinfo = $userservice->getUserByUsername($user);
}

if ($currentUserID != $userid) {
    $templatename = 'profile.tpl.php';
} else {
    $templatename = 'editprofile.tpl.php';
    $tplVars['formaction']  = createURL('profile', $user);
}

$tplVars['row'] = $userinfo;
$templateservice->loadTemplate($templatename, $tplVars);
?>
