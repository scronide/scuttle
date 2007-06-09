<?php
/***************************************************************************
Copyright (C) 2004 - 2007 Scuttle project
http://sourceforge.net/projects/scuttle/
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

require_once('header.inc.php');

$bookmarkservice    =& ServiceFactory::getServiceInstance('BookmarkService');
$cacheservice       =& ServiceFactory::getServiceInstance('CacheService');
$templateservice    =& ServiceFactory::getServiceInstance('TemplateService');
$userservice        =& ServiceFactory::getServiceInstance('UserService');

@list($user, $page) = isset($_GET['query']) ? explode('/', $_GET['query']) : NULL;

// Set user details if logged on
$isLoggedOn = $userservice->isLoggedOn();
if ($isLoggedOn) {
    $currentUser        = $userservice->getCurrentUser();
    $currentUsername    = $currentUser[$userservice->getFieldName('username')];
}

if ($usecache) {
    // Generate hash for caching on
    if ($isLoggedOn) {
        if ($currentUsername != $user) {
            $cachehash = md5($_SERVER['REQUEST_URI'] . $currentUsername);

            // Cache for 5 minutes
            $cacheservice->Start($cachehash);
        }
    } else {
        // Cache for 30 minutes
        $cachehash = md5($_SERVER['REQUEST_URI']);
        $cacheservice->Start($cachehash, 1800);
    }
}

$tplVars = array();

if ($user) {
    if (is_int($user)) {
        $userid = intval($user);
    } else {
        if (!($userinfo = $userservice->getUserByUsername($user) ) ) {
            // Throw a 404 error
            $tplVars['error'] = sprintf(T_('User with username %s was not found'), $user);
            $templateservice->loadTemplate('error.404.tpl', $tplVars);
            exit();
        } else {
            $userid =& $userinfo['uId'];
        }
    }
}

// Header variables
$tplVars['loadjs'] = true;

$template = 'bookmarks.tpl';
if ($user) {
    $tplVars['user']        = $user;
    $tplVars['userid']      = $userid;
    $tplVars['userinfo']    =& $userinfo;

    // Pagination
    $perpage = getPerPageCount();
    if (isset($_GET['page']) && intval($_GET['page']) > 1) {
        $page = $_GET['page'];
        $start = ($page - 1) * $perpage;
    } else {
        $page = 0;
        $start = 0;
    }

    // Set template vars
    $tplVars['page'] = $page;
    $tplVars['start'] = $start;
    $tplVars['bookmarkCount'] = $start + 1;
    
    $bookmarks =& $bookmarkservice->getBookmarks($start, $perpage, $userid, NULL, NULL, getSortOrder(), true);

    $tplVars['sidebar_blocks'] = array('watchlist');
    $tplVars['select_watchlist'] = ' selected="selected"';
    $tplVars['total'] = $bookmarks['total'];
    $tplVars['bookmarks'] =& $bookmarks['bookmarks'];
    $tplVars['cat_url'] = createURL('tags', '%2$s');
    $tplVars['nav_url'] = createURL('watchlist', '%s/%s%s');

    if ($user == $currentUsername) {
        $title = T_('My Watchlist');
    } else {
        $title = T_('Watchlist') .': '. $user;
    }
    $tplVars['pagetitle'] = $title;
    $tplVars['subtitle'] = $title;

    $tplVars['rsschannels'] = array(
        array(filter($sitename .': '. $title), createURL('rss', 'watchlist/'. filter($user, 'url')))
    );
} else {
    $template           = 'error.404.tpl';
    $tplVars['error']   = T_('Username was not specified');
}

// Sorting
$tplVars['sortOrders'] = array(
    array(
        'link'  => '?sort=date_desc',
        'title' => T_('Sort by date'),
        'text'  => T_('Date')
    ),
    array(
        'link'  => '?sort=title_asc',
        'title' => T_('Sort by title'),
        'text'  => T_('Title')
    ),
    array(
        'link'  => '?sort=url_asc',
        'title' => T_('Sort by URL'),
        'text'  => T_('URL')
    )
);

$tplVars['range']           = 'watchlist';
$tplVars['isLoggedOn']      = $isLoggedOn;
$tplVars['currentUsername'] = $currentUsername;
$templateservice->loadTemplate($template, $tplVars);

if ($usecache) {
    // Cache output if existing copy has expired
    $cacheservice->End($hash);
}
?>