<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
if (utf8_strlen($userinfo['name']) > 0) {
    $name = $userinfo['name'];
} else {
    $name = $userinfo['username'];
}
?>
<h2><?php echo $name; ?></h2>
<div id="profile">
    <ul>
        <li><a href="<?php echo $userservice->getProfileUrl($userid, $user); ?>"><?php echo T_('Profile'); ?></a> &rarr;</li>
        <li><a href="<?php echo createURL('alltags', $user); ?>"><?php echo T_('Tags'); ?></a> &rarr;</li>
        <li><a href="<?php echo createURL('watchlist', $user); ?>"><?php echo T_('Watchlist'); ?></a> &rarr;</li>
    </ul>
</div>
