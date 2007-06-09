<?php
/***************************************************************************
Copyright (C) 2007 Scuttle project
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
$userservice        =& ServiceFactory::getServiceInstance('UserService');

@list($type, $item) = isset($_GET['query']) ? explode('/', $_GET['query']) : NULL;

if ($userservice->isLoggedOn() && $type && $item) {
    $block = false;
    switch ($type) {
        case 'bookmark':
            $block = $bookmarkservice->block($item);
            break;
        case 'user':
            $block = $userservice->block($item);
            break;
    }
}
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: '. $_SERVER['HTTP_REFERER']);
} else {
    header('Location: '. createURL('index'));
}
?>