<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');

$watching = $userservice->getWatchNames($userid);
if ($watching) {
?>

<div id="watching">
    <h2><?php echo T_('Watching'); ?></h2>
    <ul>
    <?php foreach($watching as $watchuser): ?>
        <li><a href="<?php echo createURL('bookmarks', $watchuser); ?>"><?php echo $watchuser; ?></a> &rarr;</li>
    <?php endforeach; ?>
    </ul>
</div>

<?php
}
?>
