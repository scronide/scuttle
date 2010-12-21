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

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');
$userservice     =& ServiceFactory::getServiceInstance('UserService');
$cacheservice    =& ServiceFactory::getServiceInstance('CacheService');

$tplVars = array();

if (isset($_GET['action']) && ($_GET['action'] == "add") && !$userservice->isLoggedOn()) {
    $loginqry = str_replace("'", '%27', stripslashes($_SERVER['QUERY_STRING']));
    header('Location: '. createURL('login', '?'. $loginqry));
    exit();
}

@list($url, $user, $cat) = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : NULL;

$loggedon = false;
if ($userservice->isLoggedOn()) {
    $loggedon = true;
    $currentUser = $userservice->getCurrentUser();
    $currentUserID = $userservice->getCurrentUserId();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];
}

$endcache = false;
if ($usecache) {
    // Generate hash for caching on
    $hash = md5($_SERVER['REQUEST_URI'] . $user);

    // Don't cache if its users' own bookmarks
    if ($loggedon) {
        if ($currentUsername != $user) {
            // Cache for 5 minutes
            $cacheservice->Start($hash);
            $endcache = true;
        }
    } else {
        // Cache for 30 minutes
        $cacheservice->Start($hash, 1800);
        $endcache = true;
    }
}

$pagetitle = $rssCat = $catTitle = '';
if ($user) {
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
    $pagetitle .= ': '. $user;
}
if ($cat) {
    $catTitle = ': '. str_replace('+', ' + ', $cat);
    $pagetitle .= $catTitle;
}
$pagetitle = substr($pagetitle, 2);

// Header variables
$tplVars['loadjs'] = true;

// ADD A BOOKMARK
$saved = false;
$templatename = 'bookmarks.tpl';
if ($loggedon && isset($_POST['submitted'])) {
    if (!$_POST['title'] || !$_POST['address']) {
        $tplVars['error'] = T_('Your bookmark must have a title and an address');
        $templatename = 'editbookmark.tpl';
    } else {
        $address = trim($_POST['address']);

        // If the bookmark exists already, edit the original
        if ($bookmarkservice->bookmarkExists($address, $currentUserID)) {
            $bookmark =& $bookmarkservice->getBookmarkByAddress($address);
            header('Location: '. createURL('edit', $bookmark['bId']));
            exit();

        // If it's new, save it
        } else {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $status = intval($_POST['status']);
            $categories = trim($_POST['tags']);
            $saved = true;
            if ($bookmarkservice->addBookmark($address, $title, $description, $status, $categories)) {
                if (isset($_POST['popup'])) {
                    $tplVars['msg'] = '<script type="text/javascript">window.close();</script>';
                } else {
                    $tplVars['msg'] = T_('Bookmark saved');
                }
            } else {
                $tplVars['error'] = T_('There was an error saving your bookmark. Please try again or contact the administrator.');
                $templatename = 'editbookmark.tpl';
                $saved = false;
            }
        }
    }
}

if (isset($_GET['action']) && ($_GET['action'] == "add")) {
    // If the bookmark exists already, edit the original
    if ($bookmarkservice->bookmarkExists(stripslashes($_GET['address']), $currentUserID)) {
        $bookmark =& $bookmarkservice->getBookmarkByAddress(stripslashes($_GET['address']));
        $popup = (isset($_GET['popup'])) ? '?popup=1' : '';
        header('Location: '. createURL('edit', $bookmark['bId'] . $popup));
        exit();
    }
    $templatename = 'editbookmark.tpl';
}
 
if ($templatename == 'editbookmark.tpl') {
    if ($loggedon) {
        $tplVars['formaction']  = createURL('bookmarks', $currentUsername);
        if (isset($_POST['submitted'])) {
            $tplVars['row'] = array(
                'bTitle' => stripslashes($_POST['title']),
                'bAddress' => stripslashes($_POST['address']),
                'bDescription' => stripslashes($_POST['description']),
                'tags' => ($_POST['tags'] ? explode(',', stripslashes($_POST['tags'])) : array())
            );
            $tplVars['tags'] = $_POST['tags'];
        } else {
            $tplVars['row'] = array(
                'bTitle' => stripslashes($_GET['title']),
                'bAddress' => stripslashes($_GET['address']),
                'bDescription' => stripslashes($_GET['description']),
                'tags' => ($_GET['tags'] ? explode(',', stripslashes($_GET['tags'])) : array())
            );
        }
        $title = T_('Add a Bookmark');
        $tplVars['pagetitle'] = $title;
        $tplVars['subtitle'] = $title;
        $tplVars['btnsubmit'] = T_('Add Bookmark');
        $tplVars['popup'] = (isset($_GET['popup'])) ? $_GET['popup'] : null;
    } else {
        $tplVars['error'] = T_('You must be logged in before you can add bookmarks.');
    }
} else if ($user && !isset($_GET['popup'])) {
        
    $tplVars['sidebar_blocks'] = array('profile', 'watchstatus');

    if (!$cat) {
        $cat = NULL;
        $tplVars['currenttag'] = NULL; 
    } else {
        $rssCat = '/'. filter($cat, 'url');
        $tplVars['currenttag'] = $cat;
        $tplVars['sidebar_blocks'][] = 'related';
        $tplVars['sidebar_blocks'][] = 'tagactions';
    }
    $tplVars['popCount'] = 30;
    $tplVars['sidebar_blocks'][] = 'popular';
    
    $tplVars['userid'] = $userid;
    $tplVars['userinfo'] =& $userinfo;
    $tplVars['user'] = $user;
    $tplVars['range'] = 'user';
    
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
    $tplVars['rsschannels'] = array(
        array(filter($sitename .': '. $pagetitle), createURL('rss', filter($user, 'url') . $rssCat))
    );

    $tplVars['page'] = $page;
    $tplVars['start'] = $start;
    $tplVars['bookmarkCount'] = $start + 1;
    
    $bookmarks =& $bookmarkservice->getBookmarks($start, $perpage, $userid, $cat, $terms, getSortOrder());
    $tplVars['total'] = $bookmarks['total'];
    $tplVars['bookmarks'] =& $bookmarks['bookmarks'];
    $tplVars['cat_url'] = createURL('bookmarks', '%s/%s');
    $tplVars['nav_url'] = createURL('bookmarks', '%s/%s%s');
    if ($user == $currentUsername) {
        $title = T_('My Bookmarks') . filter($catTitle);
    } else {
        $title = filter($pagetitle);
    }
    $tplVars['pagetitle'] = $title;
    $tplVars['subtitle'] = $title;
}
$templateservice->loadTemplate($templatename, $tplVars);

if ($usecache && $endcache) {
    // Cache output if existing copy has expired
    $cacheservice->End($hash);
}
