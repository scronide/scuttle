<?php
header('Content-Type: text/javascript');
header('Cache-Control: public, max-age=86400, must-revalidate');

require_once('header.inc.php');
require_once('functions.inc.php');

$cacheservice   =& ServiceFactory::getServiceInstance('CacheService');
$player_root    = $root .'includes/player/';

// Caching
$endcache = false;
if ($usecache) {
    // Generate hash for caching on
    $hash = md5($_SERVER['REQUEST_URI']);

    // Cache for 24 hours
    $cacheservice->Start($hash, 86400);
    $endcache = true;
}
?>

function _playerAdd(anchor) {
    var url = anchor.href;
    var code = '<object type="application/x-shockwave-flash" data="<?php echo $player_root ?>musicplayer_f6.swf?song_url=' + url +'&amp;b_bgcolor=ffffff&amp;b_fgcolor=000000&amp;b_colors=000000,000000,ff0000,ff0000&buttons=<?php echo $player_root ?>load.swf,<?php echo $player_root ?>play.swf,<?php echo $player_root ?>stop.swf,<?php echo $player_root ?>error.swf" width="14" height="14">';
    var code = code + '<param name="movie" value="<?php echo $player_root ?>musicplayer.swf?song_url=' + url +'&amp;b_bgcolor=ffffff&amp;b_fgcolor=000000&amp;b_colors=000000,000000,ff0000,ff0000&amp;buttons=<?php echo $player_root ?>load.swf,<?php echo $player_root ?>play.swf,<?php echo $player_root ?>stop.swf,<?php echo $player_root ?>error.swf" />';
    var code = code + '</object>';
    anchor.parentNode.innerHTML = code +' '+ anchor.parentNode.innerHTML;
}

function deleteBookmark(ele, item){
    var link = $(ele).parents("li.link");
    if (link.children("span.confirm").length < 1) {
        var confirmDelete = "<span class='confirm'><?php echo T_('Are you sure?') ?> <a href=\"#\" onclick=\"deleteConfirmed(this, " + item + ", \'\'); return false;\"><?php echo T_('Yes'); ?></a> - <a href=\"#\" onclick=\"deleteCancelled(this); return false;\"><?php echo T_('No'); ?></a></span>";
        link.append(confirmDelete);
    }
}

function deleteCancelled(ele) {
    $(ele).parents('span.confirm').remove();
}

function deleteConfirmed(ele, item) {
    $.ajax({
        type: 'POST',
        url:  '<?php echo $root; ?>ajaxDelete.php',
        data: { 'id': item },
        success: function(msg) {
           $(ele).parents('li.xfolkentry').remove();
        }
    });
}

function htmlentities(text) {
    text = text.replace(/'/g, '&apos;');
    text = text.replace(/"/g, '&quot;');
    return text;
}

// TODO: Convert to jQuery
function isAvailable(input, response){
    var usernameField = document.getElementById("username");
    var username = usernameField.value;
    username = username.toLowerCase();
    username = $.trim(username);
    var availability = document.getElementById("availability");
    if (username != '') {
        usernameField.style.backgroundImage = 'url(<?php echo $root; ?>loading.gif)';
        if (response != '') {
            usernameField.style.backgroundImage = 'none';
            if (response == 'true') {
                availability.className = 'available';
                availability.innerHTML = '<?php echo T_('Available'); ?>';
            } else {
                availability.className = 'not-available';
                availability.innerHTML = '<?php echo T_('Not Available'); ?>';
            }
        } else {
            loadXMLDoc('<?php echo $root; ?>ajaxIsAvailable.php?username=' + username);
        }
    }
}

function useAddress(ele) {
    var address = ele.value;
    if (address != '') {
        if (address.indexOf(':') < 0) {
            address = 'http:\/\/' + address;
        }
        getTitle(address, null);
        ele.value = address;
    }
}

function getTitle(address) {
   var title = document.getElementById("titleField");
   title.style.backgroundImage = "url(<?php echo $root; ?>images/loading.gif)";
   $.ajax({
      type: 'GET',
      url:  '<?php echo $root; ?>ajaxGetTitle.php',
      data: { 'url': address },
      dataType: 'text',
      complete: function(obj, response) {
         title.style.backgroundImage = 'none';
      },
      success: function(response) {
         title.value = response;
      }
   });
}

var xmlhttp;
function loadXMLDoc(url) {
    // Native
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = processStateChange;
        xmlhttp.open("GET", url, true);
        xmlhttp.send(null);
    // ActiveX
    } else if (window.ActiveXObject) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        if (xmlhttp) {
            xmlhttp.onreadystatechange = processStateChange;
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
        }
    }
}

function processStateChange() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        response = xmlhttp.responseXML.documentElement;
        method = response.getElementsByTagName('method')[0].firstChild.data;
        result = response.getElementsByTagName('result')[0].firstChild.data;
        eval(method + '(\'\', result)');
    }
}

function playerLoad() {
    var links         = $("a.taggedlink[@href$=.mp3]");
    var links_length  = links.length;
    for (var i = 0; i < links_length; i++) {
       _playerAdd(links[i]);
    }
}

// Onload events
jQuery(function($) {
    // Toggle the 'Add a Bookmark' form
    $("p#toolbar a.add").click(function() {
        $("div#add").slideToggle("slow");
    });

    // Toggle tag display
    $("li.xfolkentry").click(function() {
        $("li.tags", this).slideToggle("fast");
    });

    // Display options on hover
    $("li.xfolkentry").hover(function() {
        $("a.block", this).css("display", "inline");
        $("a.delete", this).css("display", "inline");
        $("a.edit", this).css("display", "inline");
    },function() {
        $("a.block", this).hide();
        $("a.delete", this).hide();
        $("a.edit", this).hide();
        $("span.confirm", this).remove();
    });

    // Hover effect for Delete buttons
    $("a.delete").hover(function() {
        $("img", this).attr("src", "<?php echo $root; ?>images/delete_on.png");
    },function() {
        $("img", this).attr("src", "<?php echo $root; ?>images/delete.png");
    });

    // Hover effect for Edit buttons
    $("a.edit").hover(function() {
        $("img", this).attr("src", "<?php echo $root; ?>images/edit_on.png");
    },function() {
        $("img", this).attr("src", "<?php echo $root; ?>images/edit.png");
    });

    // Hover effect for Block buttons
    $("a.block").hover(function() {
        $("img", this).attr("src", "<?php echo $root; ?>images/block_on.png");
    },function() {
        $("img", this).attr("src", "<?php echo $root; ?>images/block.png");
    });

    // Edit click
    /*
    $("a.edit").click(function() {
        // Remove existing edit forms and show all hidden bookmarks
        $('li.edit').remove();
        $('li.xfolkentry').show();

        var li = $(this).parents('li.xfolkentry');
        li.before('<li class="xfolkentry edit"></li>');
        $('li.edit').hide();
        var ul          = $(this).parents('ul');
        var address     = htmlentities($(this).prev().attr('href'));
        var title       = htmlentities($(this).prev().text());
        var description = htmlentities(ul.children('li.description').text());
        var tags        = htmlentities(ul.children('li.tags').text());
        $('li.edit').load(
            "<?php echo $root; ?>templates/editform.tpl.php",
            {
                'address':      address,
                'title':        title,
                'description':  description,
                'tags':         tags,
                'status':       "2"
            },
            function() {
                li.hide();
                $('li.edit').show();
            }
        );
        return false;
    });
    */

    // Hide search label on focus
    $("input[@name=terms]").focus(function() {
        $('../label', this).hide();
    });

    // Tag completion
    /*
    $("#tags").keyup(function() {
        
    });
    */
});

<?php
if ($usecache && $endcache) {
    // Cache output if existing copy has expired
    $cacheservice->End($hash);
}
?>