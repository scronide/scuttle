<?php
/***************************************************************************
Copyright (c) 2005 - 2010 Marcus Campbell
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
require_once 'header.inc.php';

function getTitle($url) {
    $fd = @fopen($url, 'r');
    if ($fd) {
        $html = fread($fd, 1750);
        fclose($fd);

        // Get title from title tag
        preg_match_all('/<title>(.*)<\/title>/si', $html, $matches);
        $title = $matches[1][0];

        // Get encoding from charset attribute
        preg_match_all('/<meta.*charset=([^;"]*)">/i', $html, $matches);
        $encoding = strtoupper($matches[1][0]);

        // Convert to UTF-8 from the original encoding
        if (function_exists('mb_convert_encoding')) {
            $title = @mb_convert_encoding($title, 'UTF-8', $encoding);
        }

        if (utf8_strlen($title) > 0) {
            return $title;
        } else {
            // No title, so return filename
            $uriparts = explode('/', $url);
            $filename = end($uriparts);
            unset($uriparts);

            return $filename;
        }
    } else {
        return false;
    }
}
echo getTitle($_GET['url']);
