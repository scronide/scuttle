<?php
// Implements the del.icio.us API request to add a new post.

// del.icio.us behavior:
// - tags can't have spaces
// - address and description are mandatory

// Scuttle behavior:
// - Additional 'status' variable for privacy
// - No support for 'replace' variable

// Force HTTP authentication
require_once 'httpauth.inc.php';
require_once '../header.inc.php';

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice     =& ServiceFactory::getServiceInstance('UserService');

// Get all the bookmark's passed-in information
if (isset($_REQUEST['url']) && (trim($_REQUEST['url']) != ''))
    $url = trim(urldecode($_REQUEST['url']));
else
    $url = NULL;

if (isset($_REQUEST['description']) && (trim($_REQUEST['description']) != ''))
    $description = trim($_REQUEST['description']);
else
    $description = NULL;

if (isset($_REQUEST['extended']) && (trim($_REQUEST['extended']) != ""))
    $extended = trim($_REQUEST['extended']);
else
    $extended = NULL;

if (isset($_REQUEST['tags']) && (trim($_REQUEST['tags']) != '') && (trim($_REQUEST['tags']) != ','))
    $tags = trim($_REQUEST['tags']);
else
    $tags = NULL;

if (isset($_REQUEST['dt']) && (trim($_REQUEST['dt']) != ''))
    $dt = trim($_REQUEST['dt']);
else
    $dt = NULL;

$status = 0;
if (isset($_REQUEST['status'])) {
    $status_str = trim($_REQUEST['status']);
    if (is_numeric($status_str)) {
        $status = intval($status_str);
        if($status < 0 || $status > 2) {
            $status = 0;
        }
    } else {
        switch ($status_str) {
            case 'private':
                $status = 2;
                break;
            case 'shared':
                $status = 1;
                break;
            default:
                $status = 0;
                break;
        }
    }
}

// Error out if there's no address or description
if (is_null($url) || is_null($description)) {
    $added = FALSE;
} else {
// We're good with info; now insert it!
    if ($bookmarkservice->bookmarkExists($url, $userservice->getCurrentUserId()))
        $added = FALSE;
    else
        $added = $bookmarkservice->addBookmark($url, $description, $extended, $status, $tags, $dt, TRUE);
}

// Set up the XML file and output the result.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<result code="'. ($added ? 'done' : 'something went wrong') .'" />';
