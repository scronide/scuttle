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

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');
$userservice =& ServiceFactory::getServiceInstance('UserService');
$cacheservice =& ServiceFactory::getServiceInstance('CacheService');

$tplVars = array();

list($url, $cat) = explode('/', $_SERVER['PATH_INFO']);
if (!$cat) {
    header('Location: '. createURL('populartags'));
    exit;
} else {
    $cattitle = str_replace('+', ' + ', $cat);
}
$pagetitle = T_('Tags') .': '. $cattitle;

if ($usecache) {
    // Generate hash for caching on
    if ($userservice->isLoggedOn()) {
        $hash = md5($_SERVER['REQUEST_URI'] . $userservice->getCurrentUserID());
    } else {
        $hash = md5($_SERVER['REQUEST_URI']);
    }

    // Cache for 30 minutes
    $cacheservice->Start($hash, 1800);
}

// Header variables
$tplVars['pagetitle'] = $pagetitle;
$tplVars['loadjs'] = true;
$tplVars['rsschannels'] = array(
    array(filter($sitename .': '. $pagetitle), createURL('rss', 'all/'. filter($cat, 'url')))
);

// Pagination
$perpage = getPerPageCount();
if (isset($_GET['page']) && intval($_GET['page']) > 1) {
    $page = $_GET['page'];
    $start = ($page - 1) * $perpage;
} else {
    $page = 0;
    $start = 0;
}

$tplVars['page'] = $page;
$tplVars['start'] = $start;
$tplVars['popCount'] = 25;
$tplVars['currenttag'] = $cat;
$tplVars['sidebar_blocks'] = array('related', 'popular');
$tplVars['subtitle'] = filter($pagetitle);
$tplVars['bookmarkCount'] = $start + 1;
$bookmarks =& $bookmarkservice->getBookmarks($start, $perpage, NULL, $cat, NULL, getSortOrder());
$tplVars['total'] = $bookmarks['total'];
$tplVars['bookmarks'] =& $bookmarks['bookmarks'];
$tplVars['cat_url'] = createURL('tags', '%2$s');
$tplVars['nav_url'] = createURL('tags', '%2$s%3$s');

$templateservice->loadTemplate('bookmarks.tpl', $tplVars);

if ($usecache) {
    // Cache output if existing copy has expired
    $cacheservice->End($hash);
}
?>
