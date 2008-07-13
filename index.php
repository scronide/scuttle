<?php
/***************************************************************************
Copyright (C) 2004 - 2006 Scuttle project
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
$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');
$userservice =& ServiceFactory::getServiceInstance('UserService');
$cacheservice =& ServiceFactory::getServiceInstance('CacheService');

$tplvars = array();
if (isset($_GET['action'])){
    if ($_GET['action'] == "logout") {
        $userservice->logout($path);
        $tplvars['msg'] = T_('You have now logged out');
    }
}

// Header variables
$tplVars['loadjs'] = true;
$tplVars['rsschannels'] = array(
    array(sprintf(T_('%s: Recent bookmarks'), $sitename), createURL('rss'))
);

if ($usecache) {
    // Generate hash for caching on
    $hashtext = $_SERVER['REQUEST_URI'];
    if ($userservice->isLoggedOn()) {
        $hashtext .= $userservice->getCurrentUserID();
    }
    $hash = md5($hashtext);

    // Cache for 15 minutes
    $cacheservice->Start($hash, 900);
}

// Pagination
$perpage = getPerPageCount();
if (isset($_GET['page']) && intval($_GET['page']) > 1) {
    $page = $_GET['page'];
    $start = ($page - 1) * $perpage;
} else {
    $page = 0;
    $start = 0;
}

$dtend = date('Y-m-d H:i:s', strtotime('tomorrow'));
$dtstart = date('Y-m-d H:i:s', strtotime($dtend .' -'. $defaultRecentDays .' days'));

$tplVars['page'] = $page;
$tplVars['start'] = $start;
$tplVars['popCount'] = 30;
$tplVars['sidebar_blocks'] = array('recent');
$tplVars['range'] = 'all';
$tplVars['pagetitle'] = T_('Store, share and tag your favourite links');
$tplVars['subtitle'] = T_('Recent Bookmarks');
$tplVars['bookmarkCount'] = $start + 1;
$bookmarks =& $bookmarkservice->getBookmarks($start, $perpage, NULL, NULL, NULL, getSortOrder(), NULL, $dtstart, $dtend);
$tplVars['total'] = $bookmarks['total'];
$tplVars['bookmarks'] =& $bookmarks['bookmarks'];
$tplVars['cat_url'] = createURL('tags', '%2$s');
$tplVars['nav_url'] = createURL('index', '%3$s');

$templateservice->loadTemplate('bookmarks.tpl', $tplVars);

if ($usecache) {
    // Cache output if existing copy has expired
    $cacheservice->End($hash);
}
?>