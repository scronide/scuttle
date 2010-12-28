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

// Header variables
$tplVars['subtitle'] = T_('Edit Bookmark');
$tplVars['loadjs']   = TRUE;

list ($url, $bookmark) = explode('/', $_SERVER['PATH_INFO']);

if (!($row = $bookmarkservice->getBookmark(intval($bookmark), true))) {
    $tplVars['error'] = sprintf(T_('Bookmark with id %s was not found'), $bookmark);
    $templateservice->loadTemplate('error.404.tpl', $tplVars);
    exit();
} else {
    if (!$bookmarkservice->editAllowed($row)) {
        $tplVars['error'] = T_('You are not allowed to edit this bookmark');
        $templateservice->loadTemplate('error.500.tpl', $tplVars);
        exit();
    } else if ($_POST['submitted']) {
        if (!$_POST['title'] || !$_POST['address']) {
            $tplVars['error'] = T_('Your bookmark must have a title and an address');
        } else {
            // Update bookmark
            $bId = intval($bookmark);
            $address = trim($_POST['address']);
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $status = intval($_POST['status']);
            $tags = trim($_POST['tags']);
            $logged_on_user = $userservice->getCurrentUser();
            if (!$bookmarkservice->updateBookmark($bId, $address, $title, $description, $status, $tags)) {
                $tplvars['error'] = T_('Error while saving your bookmark');
            } else {
                if (isset($_POST['popup'])) {
                    $tplVars['msg'] = (isset($_POST['popup'])) ? '<script type="text/javascript">window.close();</script>' : T_('Bookmark saved');
                } elseif (isset($_POST['referrer'])) {
                    header('Location: '. $_POST['referrer']);
                } else {
                    header('Location: '. createURL('bookmarks', $logged_on_user[$userservice->getFieldName('username')]));
                }
            }
        }
    } else {
        if ($_POST['delete']) {
            // Delete bookmark
            if ($bookmarkservice->deleteBookmark($bookmark)) {
                $logged_on_user = $userservice->getCurrentUser();
                if (isset($_POST['referrer'])) {
                    header('Location: '. $_POST['referrer']);
                } else {
                    header('Location: '. createURL('bookmarks', $logged_on_user[$userservice->getFieldName('username')]));
                }
                exit();
            } else {
                $tplVars['error'] = T_('Failed to delete the bookmark');
                $templateservice->loadTemplate('error.500.tpl', $tplVars);
                exit();
            }
        }
    }

    $tplVars['popup'] = (isset($_GET['popup'])) ? $_GET['popup'] : null;
    $tplVars['row'] =& $row;
    $tplVars['formaction']  = createURL('edit', $bookmark);
    $tplVars['btnsubmit'] = T_('Save Changes');
    $tplVars['showdelete'] = true;
    $tplVars['referrer'] = $_SERVER['HTTP_REFERER'];
    $templateservice->loadTemplate('editbookmark.tpl', $tplVars);
}
