<?php
/***************************************************************************
Copyright (c) 2006 Marcus Campbell
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
$tagservice = & ServiceFactory :: getServiceInstance('TagService');
$templateservice = & ServiceFactory :: getServiceInstance('TemplateService');
$userservice = & ServiceFactory :: getServiceInstance('UserService');

list ($url, $tag) = explode('/', $_SERVER['PATH_INFO']);

if ($_POST['confirm']) {
	if (isset($_POST['old']) && (trim($_POST['old']) != ''))
	    $old = trim($_REQUEST['old']);
	else
	    $old = NULL;

	if (isset($_POST['new']) && (trim($_POST['new']) != ''))
	    $new = trim($_POST['new']);
	else
	    $new = NULL;

	if (is_null($old) || is_null($new)) {
	     $tplVars['error'] = T_('Failed to rename the tag');
	     $templateservice->loadTemplate('error.500.tpl', $tplVars);
	     exit();
	} else {
	    // Rename the tag.
	    if($tagservice->renameTag($userservice->getCurrentUserId(), $old, $new, true)) {
		     $tplVars['msg'] = T_('Tag renamed');
		     $logged_on_user = $userservice->getCurrentUser();
		     header('Location: '. createURL('bookmarks', $logged_on_user[$userservice->getFieldName('username')]));
		} else {
		     $tplVars['error'] = T_('Failed to rename the tag');
		     $templateservice->loadTemplate('error.500.tpl', $tplVars);
		     exit();
		}
	}
} elseif ($_POST['cancel']) {
    $logged_on_user = $userservice->getCurrentUser();
    header('Location: '. createURL('bookmarks', $logged_on_user[$userservice->getFieldName('username')] .'/'. $tags));
}

$tplVars['subtitle']    = T_('Rename Tag') .': '. $tag;
$tplVars['formaction']  = $_SERVER['SCRIPT_NAME'] .'/'. $tag;
$tplVars['referrer']    = $_SERVER['HTTP_REFERER'];
$tplVars['old'] = $tag;
$templateservice->loadTemplate('tagrename.tpl', $tplVars);
?>
 	  	 
