<?php
// Implements the del.icio.us API request to delete a post.

// del.icio.us behavior:
// - returns "done" even if the bookmark doesn't exist;
// - does NOT allow the hash for the url parameter;
// - doesn't set the Content-Type to text/xml (we do).

// Force HTTP authentication first!
require_once 'httpauth.inc.php';
require_once '../header.inc.php';

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice     =& ServiceFactory::getServiceInstance('UserService');

// Note that del.icio.us only errors out if no URL was passed in; there's no error on attempting
// to delete a bookmark you don't have.

// Error out if there's no address
if (is_null($_REQUEST['url'])) {
    $deleted = FALSE;
}
else {
    $bookmark = $bookmarkservice->getBookmarkByAddress($_REQUEST['url']);
    $bid = $bookmark['bId'];
    $delete = $bookmarkservice->deleteBookmark($bid);
    $deleted = TRUE;
}

// Set up the XML file and output the result.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<result code="'. ($deleted ? 'done' : 'something went wrong') .'" />';
