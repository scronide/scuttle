<?php
/***************************************************************************
Copyright (C) 2005 - 2007 Scuttle project
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

header('Content-Type: text/plain; charset=UTF-8');
header('Last-Modified: '. gmdate("D, d M Y H:i:s") .' GMT');
header('Cache-Control: no-cache, must-revalidate');

require_once('header.inc.php');

$title  = '';
$url    = $_GET['url'];
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $url);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT,    2);
curl_setopt($handle, CURLOPT_FOLLOWLOCATION,    true);
curl_setopt($handle, CURLOPT_MAXREDIRS,         2);
curl_setopt($handle, CURLOPT_RETURNTRANSFER,    true);
curl_setopt($handle, CURLOPT_TIMEOUT,           2);
if ($handle) {
    $buffer = curl_exec($handle);

    // Get page title from title tag
    $found  = preg_match('/<title>(.*)<\/title>/si', $buffer, $matches);

    if ($found) {
        $title = $matches[1];

        // Get character encoding from charset attribute
        preg_match('/<meta.*charset=([^;"]*)">/i', $buffer, $matches);
        $encoding = strtoupper($matches[1]);

        // Convert to UTF-8 from the original encoding
        if (function_exists('mb_convert_encoding')) {
            $title = @mb_convert_encoding($title, 'UTF-8', $encoding);
        }
    }
}
curl_close($handle);

// If no title is found, return the filename instead
if (utf8_strlen($title) < 1) {
    $uriparts = explode('/', $url);
    $filename = end($uriparts);
    unset($uriparts);
    $title = $filename;
}

echo $title;
?>