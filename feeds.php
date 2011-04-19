<?php
/***************************************************************************
Copyright (C) 2004 - 2010 Marcus Campbell
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
$cacheservice    =& ServiceFactory::getServiceInstance('CacheService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');
$userservice     =& ServiceFactory::getServiceInstance('UserService');

$tplVars = array();

// Check if we need a callback function (JSON format only)
$callback = isset($_GET['callback']) ? $_GET['callback'] : NULL;

// Only accept callback function names containing A-Z, a-z
if ( preg_match('/[^a-zA-Z]/', $callback ) ) {
    $callback = NULL;
}

// Get feed details
@list($url, $format, $user, $cat) = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : NULL;

if ($cat == '') {
    $cat = NULL;
}

if ($format === 'rss') {
    header('Content-Type: application/xml');
}
elseif ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
}
else {
      $tplVars['error'] = sprintf(T_('Feed format %s not supported'), $format);
      $templateservice->loadTemplate('error.404.tpl', $tplVars);
      //throw a 404 error
      exit();
}
// special handling required (e.g., username is tag, recent, popular) [todo]

$loggedon = false;
if ($userservice->isLoggedOn()) {
    $loggedon = true;
    $currentUser = $userservice->getCurrentUser();
    $currentUserID = $userservice->getCurrentUserId();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];
}

$usecache = 0;
$endcache = false;
if ($usecache) {
  // Generate hash for caching on
  $hashtext = $_SERVER['REQUEST_URI'];
  $hash = md5($hashtext);

  // Cache for 30 minutes
  $cacheservice->Start($hash, 1800);
}

$watchlist = NULL;
if ($user && $user != 'all') {
  if ($user == 'watchlist') {
    $user = $cat;
    $cat = NULL;
    $watchlist = TRUE;
  }
  if (is_int($user)) {
    $userid = intval($user);
  } else {
    if ($userinfo = $userservice->getUserByUsername($user)) {
      $userid =& $userinfo['uId'];
    } else {
      $tplVars['error'] = sprintf(T_('User with username %s was not found'), $user);
      $templateservice->loadTemplate('error.404.tpl', $tplVars);
      //throw a 404 error
      exit();
    }
  }
  $pagetitle .= ": ". $user;
} else {
  $userid = NULL;
}

if ($cat) {
  $pagetitle .= ": ". str_replace('+', ' + ', $cat);
}

$tplVars['feedtitle'] = filter($GLOBALS['sitename'] . (isset($pagetitle) ? $pagetitle : ''));
$tplVars['feedlink'] = $GLOBALS['root'];
$tplVars['feeddescription'] = sprintf(T_('Recent bookmarks posted to %s'), $GLOBALS['sitename']);

$bookmarks     =& $bookmarkservice->getBookmarks(0, 15, $userid, $cat, NULL, getSortOrder(), $watchlist);
$bookmarks_tmp =& filter($bookmarks['bookmarks']);

$bookmarks_tpl = array();
foreach(array_keys($bookmarks_tmp) as $key) {
  $row =& $bookmarks_tmp[$key];

  $_link = $row['bAddress'];
  // Redirection option
  if ($GLOBALS['useredir']) {
    $_link = $GLOBALS['url_redir'] . $_link;
  }
  $_pubdate = date("r", strtotime($row['bDatetime']));

  if ($format === 'rss') {
    $uriparts  = explode('.', $_link);
    $extension = end($uriparts);
    unset($uriparts);
  
    $enclosure = array();
    if ($keys = multi_array_search($extension, $GLOBALS['filetypes'])) {
      $enclosure['mime']   = file_get_mimetype($_link);
      $enclosure['length'] = file_get_filesize($_link);
    }

    $bookmarks_tpl[] = array(
      'title'            => $row['bTitle'],
      'link'             => $_link,
      'description'      => $row['bDescription'],
      'creator'          => $row['username'],
      'pubdate'          => $_pubdate,
      'tags'             => $row['tags'],
      'enclosure_mime'   => $enclosure['mime'],
      'enclosure_length' => $enclosure['length']
    );
  }
  elseif ($format === 'json') {
    $bookmarks_json[] = array(
      'd' => $row['bTitle'],
      'u' => $_link,
      'n' => $row['bDescription'],
      'dt' => $_pubdate,
      't'  => $row['tags'],
    );
  }
}
unset($bookmarks_tmp);
unset($bookmarks);
$tplVars['bookmarks'] =& $bookmarks_tpl;

if ($format === 'rss') {
  $templateservice->loadTemplate('rss.tpl', $tplVars);
}
elseif ($format === 'json') {
  if ($callback) {
    print "$callback(" . json_encode( $bookmarks_json ) . ');';
  }
  else {
    print json_encode( $bookmarks_json );
  }
}

if ($usecache) {
  // Cache output if existing copy has expired
  $cacheservice->End($hash);
}
