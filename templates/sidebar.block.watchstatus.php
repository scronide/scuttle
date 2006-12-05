<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
if ($userservice->isLoggedOn()) {
    $currentUser = $userservice->getCurrentUser();
    $currentUsername = $currentUser[$userservice->getFieldName('username')];

    if ($currentUsername != $user) {
        $result = $userservice->getWatchStatus($userid, $userservice->getCurrentUserId());
        if ($result) {
            $linkText = T_('Remove from Watchlist');
        } else {
            $linkText = T_('Add to Watchlist');
        }
        $linkAddress = createURL('watch', $user);
?>

<h2><?php echo T_('Actions'); ?></h2>
<div id="watchlist">
    <ul>
        <li><a href="<?php echo $linkAddress ?>"><?php echo $linkText ?></a></li>
    </ul>
</div>

<?php
    }
}
?>