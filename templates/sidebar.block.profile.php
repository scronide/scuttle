<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
if (utf8_strlen($userinfo['name']) > 0) {
    $name = $userinfo['name'];
} else {
    $name = $userinfo['username'];
}
?>
<div id="profile" class="box">
    <p class="avatar"><img src="" width="64" height="64" alt="" /></p>
    <ol>
        <li class="name"><?php echo $name; ?></li>
        <li class="bio">Test test test test.</li>
        <li class="info">Test test test test.</li>
    </ol>
</div>