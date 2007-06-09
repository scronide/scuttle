<?php
$this->includeTemplate($GLOBALS['top_include']);
?>

<script type="text/javascript">
jQuery(function($) {
    $("#username").focus();
});
</script>
<div id="main">
    <form action="<?php echo $formaction; ?>" method="post">
        <div><input type="hidden" name="query" value="<?php echo $querystring; ?>" /></div>
        <table>
        <tr>
            <th align="left"><label for="username"><?php echo T_('Username'); ?></label></th>
            <td><input type="text" id="username" name="username" size="20" /></td>
            <td></td>
        </tr>
        <tr>
            <th align="left"><label for="password"><?php echo T_('Password'); ?></label></th>
            <td><input type="password" id="password" name="password" size="20" /></td>
            <td><input type="checkbox" name="keeppass" value="yes" /> <?php echo T_("Keep me logged in for 2 weeks."); ?></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="submitted" value="<?php echo T_('Log In'); ?>" /></td>
            <td></td>
        </tr>
        </table>
        <p>&raquo; <a href="<?php echo $GLOBALS['root'] ?>password.php"><?php echo T_('Forgotten your password?') ?></p>
    </form>
</div>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>