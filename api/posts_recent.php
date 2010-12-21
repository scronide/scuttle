<?php
// Implements the del.icio.us API request for a user's recent posts, optionally filtered by
// tag and/or number of posts (default 15, max 100, just like del.icio.us).

// Set default and max number of posts
$countDefault = 15;
$countMax     = 100;

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

// Check to see if the number of items was specified.
if (isset($_REQUEST['count']) && (intval($_REQUEST['count']) != 0))  {
    $count = intval($_REQUEST['count']);
    if ($count > $countMax)
        $count = $countMax;
    elseif ($count < 0)
        $count = 0;
} else {
    $count = $countDefault;
}

// Get the posts relevant to the passed-in variables.
$bookmarks =& $bookmarkservice->getBookmarks(0, $count, $userservice->getCurrentUserId(), $tag);

$currentuser = $userservice->getCurrentUser();
$currentusername = $currentuser[$userservice->getFieldName('username')];

// Set up the XML file and output all the tags.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<posts tag="'. (is_null($tag) ? '' : filter($tag, 'xml')) .'" user="'. filter($currentusername, 'xml') ."\">\r\n";

foreach ($bookmarks['bookmarks'] as $row) {
    if (is_null($row['bDescription']) || (trim($row['bDescription']) == ''))
        $description = '';
    else
        $description = 'extended="'. filter($row['bDescription'], 'xml') .'" ';

    $taglist = '';
    if (count($row['tags']) > 0) {
        foreach($row['tags'] as $tag)
            $taglist .= convertTag($tag) .' ';
        $taglist = substr($taglist, 0, -1);
    } else {
        $taglist = 'system:unfiled';
    }

    echo "\t<post href=\"". filter($row['bAddress'], 'xml') .'" description="'. filter($row['bTitle'], 'xml') .'" '. $description .'hash="'. $row['bHash'] .'" tag="'. filter($taglist, 'xml') .'" time="'. gmdate('Y-m-d\TH:i:s\Z', strtotime($row['bDatetime'])) ."\" />\r\n";
}

echo '</posts>';
