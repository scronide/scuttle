<?php
$this->includeTemplate($GLOBALS['top_include']);
?>

<p><?php echo sprintf(T_('If you have forgotten your password, %s can generate a new one. Enter the username and e-mail address of your account into the form below and we will e-mail your new password to you.'), $GLOBALS['sitename']); ?></p>

<form action="<?php echo $formaction; ?>" method="post">
    <table>
    <tr>
        <th align="left"><label for="username"><?php echo T_('Username'); ?></label></th>
        <td><input type="text" id="username" name="username" size="20" class="required" /></td>
    </tr>
    <tr>
        <th align="left"><label for="email"><?php echo T_('E-mail'); ?></label></th>
        <td><input type="text" id="email" name="email" size="40" class="required" /></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" name="submitted" value="<?php echo T_('Generate Password'); ?>" /></td>
    </tr>
    </table>
</form>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>