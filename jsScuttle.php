<?php
header('Content-Type: text/javascript');
require_once 'header.inc.php';
require_once 'functions.inc.php';
$player_root = $root .'includes/player/';

$userservice     =& ServiceFactory::getServiceInstance('UserService');
if ($userservice->isLoggedOn()) {
    $currentUser = $userservice->getCurrentUser();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];
}
?>

var deleted = false;
function deleteBookmark(ele, input) {
  $(ele).hide();
  $(ele).parent().append("<span><?php echo T_('Are you sure?') ?> <a href=\"#\" onclick=\"deleteConfirmed(this, " + input + "); return false;\"><?php echo T_('Yes'); ?></a> - <a href=\"#\" onclick=\"deleteCancelled(this); return false;\"><?php echo T_('No'); ?></a></span>");
  return false;
}
function deleteCancelled(ele) {
  $(ele).parent().prev().show();
  $(ele).parent().remove();
  return false;
}
function deleteConfirmed(ele, input) {
  $.get("<?php echo $root; ?>ajaxDelete.php?id=" + input, function(data) {
    if (1 === parseInt(data)) {
      $(ele).parents(".xfolkentry").slideUp();
    }
  });
  return false;
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

function getTitle(input) {
  var title = $("#titleField").val();
  if (title.length < 1) {
    $("#titleField").css("background-image", "url(<?php echo $root; ?>loading.gif)");
    if (input.indexOf("http") > -1) {
      $.get("<?php echo $root; ?>ajaxGetTitle.php?url=" + input, function(data) {
        $("#titleField").css("background-image", "none")
                        .val(data);
      });
    }
  }
}

function autocomplete() {
	$.ajax({
		url: '<?php echo $root?>alltags/<?php echo $currentUsername?>',
		success: function(data) {
			//console.log($(data));
			var availableTags = new Array();
			$(data).find('a').each(function() {
				availableTags.push($(this).html());
				//console.log($(this).html());
			});
			
			$( ".autocomplete" )
				// don't navigate away from the field on tab when selecting an item
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
							$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					minLength: 0,
					source: function( request, response ) {
						// delegate back to autocomplete, but extract the last term
						response( $.ui.autocomplete.filter(
							availableTags, extractLast( request.term ) ) );
					},
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					select: function( event, ui ) {
						var terms = split( this.value );
						// remove the current input
						terms.pop();
						// add the selected item
						terms.push( ui.item.value );
						// add placeholder to get the comma-and-space at the end
						terms.push( "" );
						this.value = terms.join( ", " );
						return false;
					}
				});
		}
	});
	
}

function split( val ) {
		return val.split( /,\s*/ );
	}
function extractLast( term ) {
	return split( term ).pop();
}

/* Page load */
$(function() {
	
	autocomplete();
	
  // Insert Flash player for MP3 links
  if ($("#bookmarks").length > 0) {
    $("a[href$=.mp3].taggedlink").each(function() {
      var url  = this.href;
      var code = '<object type="application/x-shockwave-flash" data="<?php echo $player_root ?>musicplayer_f6.swf?song_url=' + url +'&amp;b_bgcolor=ffffff&amp;b_fgcolor=000000&amp;b_colors=0000ff,0000ff,ff0000,ff0000&buttons=<?php echo $player_root ?>load.swf,<?php echo $player_root ?>play.swf,<?php echo $player_root ?>stop.swf,<?php echo $player_root ?>error.swf" width="14" height="14">';
          code = code + '<param name="movie" value="<?php echo $player_root ?>musicplayer.swf?song_url=' + url +'&amp;b_bgcolor=ffffff&amp;b_fgcolor=000000&amp;b_colors=0000ff,0000ff,ff0000,ff0000&amp;buttons=<?php echo $player_root ?>load.swf,<?php echo $player_root ?>play.swf,<?php echo $player_root ?>stop.swf,<?php echo $player_root ?>error.swf" />';
          code = code + '</object> ';
      $(this).prepend(code);
    });
  }
})
