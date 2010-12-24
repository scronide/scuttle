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
$userservice     =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

$tplVars = array();

if ($userservice->isLoggedOn() && sizeof($_FILES) > 0 && $_FILES['userfile']['size'] > 0) {
    $userinfo = $userservice->getCurrentUser();

    if (isset($_POST['status']) && is_numeric($_POST['status'])) {
      $status = intval($_POST['status']);
    }
    else {
      $status = 2;
    }

    // File handle
    $html = file_get_contents($_FILES['userfile']['tmp_name']);
    
    // Create link array
    preg_match_all('/<a\s+(.*?)\s*\/*>([^<]*)/si', $html, $matches);
    $links  = $matches[1];
    $titles = $matches[2];
    
    $size = count($links);
    for ($i = 0; $i < $size; $i++) {
      $bTags   = '';
      $bStatus = $status;

      $attributes = preg_split('/\s+/s', $links[$i]);
      foreach ($attributes as $attribute) {
          $att = preg_split('/\s*=\s*/s', $attribute, 2);
          $attrTitle = $att[0];
          $attrVal   = str_replace('"', '&quot;', preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]));
          switch ($attrTitle) {
            case 'HREF':
              $bAddress = $attrVal;
              break;
            case 'ADD_DATE':
              $bDatetime = gmdate('Y-m-d H:i:s', $attrVal);
              break;
            case 'PRIVATE':
              $bStatus = (intval($attrVal) == 1) ? 2 : $status;
              break;
            case 'TAGS':
              $bTags = strtolower($attrVal);
              break;
          }
      }
      $bTitle = str_replace('"', '&quot;', trim($titles[$i]));

      if ($bookmarkservice->bookmarkExists($bAddress, $userservice->getCurrentUserId())) {
        $tplVars['error'] = T_('You have already submitted this bookmark.');
      } else {
        // If bookmark claims to be from the future, set it to be now instead
        if (strtotime($bDatetime) > time()) {
          $bDatetime = gmdate('Y-m-d H:i:s');
        }

        if ($bookmarkservice->addBookmark($bAddress, $bTitle, NULL, $bStatus, $bTags, $bDatetime, false, true)) {
          $tplVars['msg'] = T_('Bookmark imported.');
        }
        else {
          $tplVars['error'] = T_('There was an error saving your bookmark. Please try again or contact the administrator.');
        }
      }
    }
    header('Location: '. createURL('bookmarks', $userinfo[$userservice->getFieldName('username')]));
}
else {
  $templatename = 'importNetscape.tpl';
  $tplVars['subtitle']   = T_('Import Bookmarks from Browser File');
  $tplVars['formaction'] = createURL('importNetscape');
  $templateservice->loadTemplate($templatename, $tplVars);
}
