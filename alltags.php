<?php
/***************************************************************************
Copyright (c) 2004 - 2010 Marcus Campbell
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
$tagservice      =& ServiceFactory::getServiceInstance('TagService');
$userservice     =& ServiceFactory::getServiceInstance('UserService');
$cacheservice    =& ServiceFactory::getServiceInstance('CacheService');

@list($url, $user) = explode('/', $_SERVER['PATH_INFO']);
if (!$user) {
    header('Location: '. createURL('populartags'));
    exit;
}

if ($usecache) {
    // Generate hash for caching on
    $hashtext = $_SERVER['REQUEST_URI'];
    if ($userservice->isLoggedOn()) {
        $hashtext .= $userservice->getCurrentUserID();
    }
    $hash = md5($hashtext);

    // Cache for an hour
    $cacheservice->Start($hash, 3600);
}

// Header variables
$tplvars = array();
$pagetitle = T_('All Tags');

if (isset($user) && $user != '') {
    if (is_int($user)) {
      $userid = intval($user);
    } else {
        if ($userinfo = $userservice->getUserByUsername($user)) {
            $userid =& $userinfo[$userservice->getFieldName('primary')];
        } else {
            $tplVars['error'] = sprintf(T_('User with username %s was not found'), $user);
            $templateservice->loadTemplate('error.404.tpl', $tplVars);
            //throw a 404 error
            exit();
        }
    }
    $pagetitle .= ': '. ucfirst($user);
} else {
    $userid = NULL;
}

$tags =& $tagservice->getTags($userid);
$tplVars['tags'] =& $tagservice->tagCloud($tags, 5, 90, 225, getSortOrder()); 
$tplVars['user'] = $user;

if (isset($userid)) {
    $tplVars['cat_url'] = createURL('bookmarks', '%s/%s');
} else {
    $tplVars['cat_url'] = createURL('tags', '%2$s');
}

$tplVars['subtitle'] = $pagetitle;
$templateservice->loadTemplate('tags.tpl', $tplVars);

if ($usecache) {
    // Cache output if existing copy has expired
    $cacheservice->End($hash);
}
?>
