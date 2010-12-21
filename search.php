<?php
/***************************************************************************
Copyright (c) 2005 - 2006 Marcus Campbell
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

// POST
if (isset($_POST['terms'])) {
    // Redirect to GET
    header('Location: '. createURL('search', $_POST['range'] .'/'. filter($_POST['terms'], 'url')));

// GET
} else {
    $bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
    $templateservice =& ServiceFactory::getServiceInstance('TemplateService');
    $userservice =& ServiceFactory::getServiceInstance('UserService');

    $logged_on_userid = $userservice->getCurrentUserId();
    list($url, $range, $terms, $page) = explode('/', $_SERVER['PATH_INFO']);

    $tplvars = array();
    $tplVars['loadjs'] = true;
    
    // Pagination
    $perpage = getPerPageCount();
    if (isset($_GET['page']) && intval($_GET['page']) > 1) {
        $page = $_GET['page'];
        $start = ($page - 1) * $perpage;
    } else {
        $page = 0;
        $start = 0;
    }
    
    $s_user = NULL;
    $s_start = NULL;
    $s_end = NULL;
    $s_watchlist = NULL;

    // No search terms
    if (is_null($terms)) {
        $tplVars['subtitle'] = T_('Search Bookmarks');
        $s_start = date('Y-m-d H:i:s', strtotime($dtend .' -'. $defaultRecentDays .' days'));
        $s_end = date('Y-m-d H:i:s', strtotime('tomorrow'));
    
    // Search terms
    } else {
        $tplVars['subtitle'] = T_('Search Results');
        $selected = ' selected="selected"';

         switch ($range) {
            case 'all':
                $tplVars['select_all'] = $selected;
                $s_user = NULL;
                break;
            case 'watchlist':
                $tplVars['select_watchlist'] = $selected;
                $s_user = $logged_on_userid;
                $s_watchlist = true;
                break;
            default:
                $s_user = $range;
                break;
        }

        if (isset($s_user)) {
            if (is_numeric($s_user)) {
                $s_user = intval($s_user);
            } else {
                if (!($userinfo = $userservice->getUserByUsername($s_user) ) ) {
                    $tplVars['error'] = sprintf(T_('User with username %s was not found'), $s_user);
                    $templateservice->loadTemplate('error.404.tpl', $tplVars);
                    exit();
                } else {
                    $s_user =& $userinfo[$userservice->getFieldName('primary')];
                }
            }
        }
    }
    $bookmarks =& $bookmarkservice->getBookmarks($start, $perpage, $s_user, NULL, $terms, getSortOrder(), $s_watchlist, $s_start, $s_end);
    
    $tplVars['page'] = $page;
    $tplVars['start'] = $start;
    $tplVars['popCount'] = 25;
    $tplVars['sidebar_blocks'] = array('recent');
    $tplVars['range'] = $range;
    $tplVars['terms'] = $terms;
    $tplVars['pagetitle'] = T_('Search Bookmarks');
    $tplVars['bookmarkCount'] = $start + 1;
    $tplVars['total'] = $bookmarks['total'];
    $tplVars['bookmarks'] =& $bookmarks['bookmarks'];
    $tplVars['cat_url'] = createURL('tags', '%2$s');
    $tplVars['nav_url'] = createURL('search', $range .'/'. $terms .'/%3$s');
    
    $templateservice->loadTemplate('bookmarks.tpl', $tplVars);
}
?>
