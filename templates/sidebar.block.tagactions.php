<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
if ($userservice->isLoggedOn()) {
    $currentUser = $userservice->getCurrentUser();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];

    if ($currentUsername == $user) {
        $tags = explode('+', $currenttag);
        $renametext = T_ngettext('Rename Tag', 'Rename Tags', count($tags));
        $renamelink = createURL('tagrename', $currenttag);
        $deletelink = createURL('tagdelete', $currenttag);
?>

<h2><?php echo T_('Actions'); ?></h2>
<div id="tagactions">
    <ul>
        <li><a href="<?php echo $renamelink; ?>"><?php echo $renametext ?></a></li>
        <?php if (count($tags) == 1): ?>
        <li><a href="<?php echo $deletelink; ?>"><?php echo T_('Delete Tag') ?></a></li>
        <?php endif; ?>
    </ul>
</div>

<?php
    }
}
?>