<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
$watching = $userservice->getWatchNames($userid);
if ($watching) {
?>
<script type="text/javascript">
jQuery(function($) {
    $("#watching li").mouseover(function() {
        $("a.delete", this).css('display', 'inline');
    });
    $("#watching li").mouseout(function() {
        $("a.delete", this).hide();
    });
});
</script>
<div id="watching" class="box">
    <h2><?php echo T_('Watching'); ?></h2>
    <ul>
    <?php foreach($watching as $watchuser): ?>
        <li><a href="<?php echo createURL('bookmarks', $watchuser); ?>"><?php echo $watchuser; ?></a> <a href="<?php echo createURL('watch', $watchuser); ?>" class="delete"><img src="<?php echo $GLOBALS['root']; ?>images/delete.png" width="16" height"16" alt="<?php echo T_('Remove'); ?>" title="<?php echo T_('Remove from Watchlist'); ?>" /></a></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php
}
?>