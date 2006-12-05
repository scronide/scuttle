<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
$this->includeTemplate($GLOBALS['top_include']);
?>

<dl id="profile">
<dt><?php echo T_('Username'); ?></dt>
    <dd><?php echo $user; ?></dd>
<?php
if ($row['name'] != "") {
?>
<dt><?php echo T_('Name'); ?></dt>
    <dd><?php echo $row['name']; ?></dd>
<?php
}
if ($row['homepage'] != "") {
?>
<dt><?php echo T_('Homepage'); ?></dt>
    <dd><a href="<?php echo $row['homepage']; ?>"><?php echo $row['homepage']; ?></a></dd>
<?php
}
?>
<dt><?php echo T_('Member Since'); ?></dt>
    <dd><?php echo date($GLOBALS['longdate'], strtotime($row['uDatetime'])); ?></dd>
<?php
if ($row['uContent'] != "") {
?>
<dt><?php echo T_('Description'); ?></dt>
    <dd><?php echo $row['uContent']; ?></dd>
<?php
}
$watching = $userservice->getWatchNames($userid);
if ($watching) {
?>
    <dt><?php echo T_('Watching'); ?></dt>
        <dd>
            <?php
            $list = '';
            foreach($watching as $watchuser) {
                $list .= '<a href="'. createURL('bookmarks', $watchuser) .'">'. $watchuser .'</a>, ';
            }
            echo substr($list, 0, -2);
            ?>
        </dd>
<?php
}
$watchnames = $userservice->getWatchNames($userid, true);
if ($watchnames) {
?>
    <dt><?php echo T_('Watched By'); ?></dt>
        <dd>
            <?php
            $list = '';
            foreach($watchnames as $watchuser) {
                $list .= '<a href="'. createURL('bookmarks', $watchuser) .'">'. $watchuser .'</a>, ';
            }
            echo substr($list, 0, -2);
            ?>
        </dd>
<?php
}
?>
</dl>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>