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
$userservice =& ServiceFactory::getServiceInstance('UserService');

@list($url, $user) = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : NULL;
if ($userservice->isLoggedOn() && $user) {
    $tplVars = array();
    $pagetitle = '';

    if (is_int($user)) {
        $userid = intval($user);
    } else {
        if (!($userinfo = $userservice->getUserByUsername($user))) {
            $tplVars['error'] = sprintf(T_('User with username %s was not found'), $user);
            $templateservice->loadTemplate('error.404.tpl', $tplVars);
            exit();
        } else {
            $userid =& $userinfo['uId'];
        }
    }

    $watched = $userservice->getWatchStatus($userid, $userservice->getCurrentUserId());
    $changed = $userservice->setWatchStatus($userid);

    if ($watched) {
        $tplVars['msg'] = T_('User removed from your watchlist');
    } else {
        $tplVars['msg'] = T_('User added to your watchlist');
    }

    $currentUser = $userservice->getCurrentUser();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];

    header('Location: '. createURL('watchlist', $currentUsername));
}
?>
