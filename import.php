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

    $depth = array();
    $xml_parser = xml_parser_create();
    xml_set_element_handler($xml_parser, "startElement", "endElement");

    if (!($fp = fopen($_FILES['userfile']['tmp_name'], "r")))
        die(T_("Could not open XML input"));

    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf(T_("XML error: %s at line %d"),
                xml_error_string(xml_get_error_code($xml_parser)),
                xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);
    header('Location: '. createURL('bookmarks', $userinfo[$userservice->getFieldName('username')]));
}
else {
  $templatename = 'importDelicious.tpl';
  $tplVars['subtitle'] = T_('Import Bookmarks from del.icio.us');
  $tplVars['formaction']  = createURL('import');
  $templateservice->loadTemplate($templatename, $tplVars);
}

function startElement($parser, $name, $attrs) {
    global $depth, $status, $tplVars, $userservice;

    $bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
    $userservice =& ServiceFactory::getServiceInstance('UserService');

    if ($name == 'POST') {
        while(list($attrTitle, $attrVal) = each($attrs)) {
            switch ($attrTitle) {
                case 'HREF':
                    $bAddress = $attrVal;
                    break;
                case 'DESCRIPTION':
                    $bTitle = $attrVal;
                    break;
                case 'EXTENDED':
                    $bDescription = $attrVal;
                    break;
                case 'TIME':
                    $bDatetime = $attrVal;
                    break;
                case 'TAG':
                    $tags = strtolower($attrVal);
                    break;
            }
        }
        if ($bookmarkservice->bookmarkExists($bAddress, $userservice->getCurrentUserId())) {
            $tplVars['error'] = T_('You have already submitted this bookmark.');
        } else {
            // Strangely, PHP can't work out full ISO 8601 dates, so we have to chop off the Z.
            $bDatetime = substr($bDatetime, 0, -1);

            // If bookmark claims to be from the future, set it to be now instead
            if (strtotime($bDatetime) > time()) {
                $bDatetime = gmdate('Y-m-d H:i:s');
            }

            if ($bookmarkservice->addBookmark($bAddress, $bTitle, $bDescription, $status, $tags, $bDatetime, true, true))
                $tplVars['msg'] = T_('Bookmark imported.');
            else
                $tplVars['error'] = T_('There was an error saving your bookmark. Please try again or contact the administrator.');
        }
    }
    $depth[$parser]++;
}

function endElement($parser, $name) {
    global $depth;
    $depth[$parser]--;
}
?>
