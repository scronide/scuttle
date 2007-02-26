<?php
// Implements the del.icio.us API request for a user's posts, optionally filtered by tag and/or
// date. Note that when using a date to select the posts returned, del.icio.us uses GMT dates --
// so we do too.

// del.icio.us behavior:
// - Does not appear to filter on tag alone

// Force HTTP authentication first!
require_once('httpauth.inc.php');

$bookmarkservice    =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice        =& ServiceFactory::getServiceInstance('UserService');

// Filter by tag
if (isset($_REQUEST['tag']) && strlen($_REQUEST['tag']) > 0) {
    $tag = $_REQUEST['tag'];
} else {
    $tag = NULL;
}

// Filter by date; the format should be YYYY-MM-DD
if (isset($_REQUEST['dt']) && strlen($_REQUEST['dt']) > 0) {
    $dtstart = trim($_REQUEST['dt']);
} else {
    $bookmarks  =& $bookmarkservice->getBookmarks(0, 1, $userservice->getCurrentUserId());
    $dtstart    = date('Y-m-d', strtotime($bookmarks['bookmarks'][0]['bDatetime']));
}
$dtend = date('Y-m-d H:i:s', strtotime($dtstart .'+1 day'));

// Filter by URL
if (isset($_REQUEST['url']) && strlen($_REQUEST['url']) > 0) {
    $hash = sha1($_REQUEST['url']);
} else {
    $hash = NULL;
}

// Get the posts relevant to the passed-in variables.
$bookmarks =& $bookmarkservice->getBookmarks(0, NULL, $userservice->getCurrentUserId(), $tag, NULL, NULL, NULL, $dtstart, $dtend, $hash);

$currentuser        = $userservice->getCurrentUser();
$currentusername    = $currentuser[$userservice->getFieldName('username')];

// Set up the XML file and output all the tags.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<posts dt="'. $_REQUEST['dt'] .'" tag="'. (is_null($tag) ? '' : filter($tag, 'xml')) .'" user="'. filter($currentusername, 'xml') ."\">\r\n";

foreach($bookmarks['bookmarks'] as $row) {
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

    echo "\t<post href=\"". filter($row['bAddress'], 'xml') .'" description="'. filter($row['bTitle'], 'xml') .'" '. $description .'hash="'. $row['bHash'] .'" others="'. $bookmarkservice->countOthers($row['bAddress']) .'" tag="'. filter($taglist, 'xml') .'" time="'. date('Y-m-d\TH:i:s\Z', strtotime($row['bDatetime'])) ."\" />\r\n";
}

echo '</posts>';
?>