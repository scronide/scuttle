<?php
// Implements the del.icio.us API request for a user's post counts by date (and optionally
// by tag).

// Force HTTP authentication first!
require_once 'httpauth.inc.php';
require_once '../header.inc.php';

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice     =& ServiceFactory::getServiceInstance('UserService');

// Check to see if a tag was specified.
if (isset($_REQUEST['tag']) && (trim($_REQUEST['tag']) != ''))
    $tag = trim($_REQUEST['tag']);
else
    $tag = NULL;

// Get the posts relevant to the passed-in variables.
$bookmarks =& $bookmarkservice->getBookmarks(0, NULL, $userservice->getCurrentUserId(), $tag);

$currentuser = $userservice->getCurrentUser();
$currentusername = $currentuser[$userservice->getFieldName('username')];

//	Set up the XML file and output all the tags.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<dates tag="'. (is_null($tag) ? '' : filter($tag, 'xml')) .'" user="'. filter($currentusername, 'xml') ."\">\r\n";

$count    = 0;
$lastdate = NULL;
foreach ($bookmarks['bookmarks'] as $row) {
    $thisdate = gmdate('Y-m-d', strtotime($row['bDatetime']));
    if ($thisdate != $lastdate && $lastdate != NULL) {
        echo "\t<date count=\"". $count .'" date="'. $lastdate ."\" />\r\n";
        $count = 1;
    }
    else {
        $count++;
    }
    $lastdate = $thisdate;
}

echo "</dates>";
