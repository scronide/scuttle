<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
if ($userservice->isLoggedOn()) {
    $cUser = $userservice->getCurrentUser();
    $cUsername = $cUser[$userservice->getFieldName('username')];
?>

    <ul id="navigation">
        <li><a href="<?php echo createURL('bookmarks', $cUsername); ?>"><?php echo T_('Bookmarks'); ?></a></li>
        <li><a href="<?php echo createURL('watchlist', $cUsername); ?>"><?php echo T_('Watchlist'); ?></a></li>
        <li><a href="<?php echo createURL('bookmarks', $cUsername . '?action=add'); ?>"><?php echo T_('Add a Bookmark'); ?></a></li>
        <li class="access"><a href="<?php echo $GLOBALS['root']; ?>?action=logout"><?php echo T_('Log Out'); ?></a></li>
    </ul>

<?php
} else {
?>

    <ul id="navigation">
        <li><a href="<?php echo createURL('about'); ?>"><?php echo T_('About'); ?></a></li>
        <li class="access"><a href="<?php echo createURL('login'); ?>"><?php echo T_('Log In'); ?></a></li>
        <li class="access"><a href="<?php echo createURL('register'); ?>"><?php echo T_('Register'); ?></a></li>
    </ul>

<?php
}
?>
