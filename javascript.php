<?php
header('Content-Type: text/javascript');
require_once('header.inc.php');
require_once('functions.inc.php');
$player_root = $root .'includes/player/';
?>

function _playerAdd(anchor) {
    var url = anchor.href;
    var code = '<object type="application/x-shockwave-flash" data="<?php echo $player_root ?>musicplayer_f6.swf?song_url=' + url +'&amp;b_bgcolor=ffffff&amp;b_fgcolor=000000&amp;b_colors=0000ff,0000ff,ff0000,ff0000&buttons=<?php echo $player_root ?>load.swf,<?php echo $player_root ?>play.swf,<?php echo $player_root ?>stop.swf,<?php echo $player_root ?>error.swf" width="14" height="14">';
    var code = code + '<param name="movie" value="<?php echo $player_root ?>musicplayer.swf?song_url=' + url +'&amp;b_bgcolor=ffffff&amp;b_fgcolor=000000&amp;b_colors=0000ff,0000ff,ff0000,ff0000&amp;buttons=<?php echo $player_root ?>load.swf,<?php echo $player_root ?>play.swf,<?php echo $player_root ?>stop.swf,<?php echo $player_root ?>error.swf" />';
    var code = code + '</object>';
    anchor.parentNode.innerHTML = code +' '+ anchor.parentNode.innerHTML;
}

function deleteBookmark(ele, item){
   var confirmDelete = "<span><?php echo T_('Are you sure?') ?> <a href=\"#\" onclick=\"deleteConfirmed(this, " + item + ", \'\'); return false;\"><?php echo T_('Yes'); ?></a> - <a href=\"#\" onclick=\"deleteCancelled(this); return false;\"><?php echo T_('No'); ?></a></span>";
   ele.style.display = 'none';
   ele.parentNode.innerHTML = ele.parentNode.innerHTML + confirmDelete;
}

function deleteCancelled(ele) {
   var span = $(ele).parents("span");
   span.prev("a").show();
   span.remove();
}

function deleteConfirmed(ele, item) {
   $.ajax({
      type: "POST",
      url:  "<?php echo $root; ?>ajaxDelete.php",
      data: "id=" + item,
      success: function(msg) {
         $(ele).parents("li.xfolkentry").remove();
      }
   });
}

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
   title.style.backgroundImage = 'url(<?php echo $root; ?>loading.gif)';
   $.ajax({
      type: "GET",
      url:  "<?php echo $root; ?>ajaxGetTitle.php",
      data: "url=" + address,
      datatType: "html",
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