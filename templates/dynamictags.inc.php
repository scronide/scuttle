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

$tagservice  =& ServiceFactory::getServiceInstance('TagService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

$logged_on_userid = $userservice->getCurrentUserId();

$userPopularTags        =& $tagservice->getPopularTags($logged_on_userid, 25, $logged_on_userid);
$userPopularTagsCloud   =& $tagservice->tagCloud($userPopularTags, 5, 90, 175); 
$userPopularTagsCount   = count($userPopularTags);

if ($userPopularTagsCount > 0) {
?>

<script type="text/javascript">
Array.prototype.contains = function (ele) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == ele) {
            return true;
        }
    }
    return false;
};

Array.prototype.remove = function (ele) {
    var arr = new Array();
    var count = 0;
    for (var i = 0; i < this.length; i++) {
        if (this[i] != ele) {
            arr[count] = this[i];
            count++;
        }
    }
    return arr;
};

function addonload(addition) {
    var existing = window.onload;
    window.onload = function () {
        existing();
        addition();
    }
}

addonload(
    function () {
        var taglist = document.getElementById('tags');
        var tags = taglist.value.split(', ');
        
        var populartags = document.getElementById('popularTags').getElementsByTagName('span');
        
        for (var i = 0; i < populartags.length; i++) {
            if (tags.contains(populartags[i].innerHTML)) {
                populartags[i].className = 'selected';
            }
        }
    }
);

function addTag(ele) {
    var thisTag = ele.innerHTML;
    var taglist = document.getElementById('tags');
    var tags = taglist.value.split(', ');
    
    // If tag is already listed, remove it
    if (tags.contains(thisTag)) {
        tags = tags.remove(thisTag);
        ele.className = 'unselected';
        
    // Otherwise add it
    } else {
        tags.splice(0, 0, thisTag);
        ele.className = 'selected';
    }
    
    taglist.value = tags.join(', ');
    
    document.getElementById('tags').focus();
}

document.write('<div class="collapsible">');
document.write('<h3><?php echo T_('Popular Tags'); ?><\/h3>');
document.write('<p id="popularTags" class="tags">');

<?php
$taglist = '';
foreach(array_keys($userPopularTagsCloud) as $key) {
    $row =& $userPopularTagsCloud[$key];
    $entries = T_ngettext('bookmark', 'bookmarks', $row['bCount']);
    $taglist .= '<span title="'. $row['bCount'] .' '. $entries .'" style="font-size:'. $row['size'] .'" onclick="addTag(this)">'. filter($row['tag']) .'<\/span> ';
}
?>

document.write('<?php echo $taglist ?>');
document.write('<\/p>');
document.write('<\/div>');
</script>

<?php } ?>