<?php
// Implements the del.icio.us API request for a user's last update time and date.

// Force HTTP authentication first!
require_once('httpauth.inc.php');

$bookmarkservice    =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice        =& ServiceFactory::getServiceInstance('UserService');

// Get the most recent bookmark
$bookmarks =& $bookmarkservice->getBookmarks(0, 1, $userservice->getCurrentUserId());

// Output the XML file
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
foreach($bookmarks['bookmarks'] as $row) {
    echo '<update time="'. date('Y-m-d\TH:i:s\Z', strtotime($row['bDatetime'])) .'" />';
}
?>