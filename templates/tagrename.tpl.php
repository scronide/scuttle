<?php
$this->includeTemplate($GLOBALS['top_include']);
?>
<script type="text/javascript">
window.onload = function() {
    document.getElementById("new").focus();
}
</script>
<form action="<?= $formaction ?>" method="post">
<table>
<tr>
    <th align="left"><?php echo T_('Old'); ?></th>
    <td><input type="text" name="old" id="old" value="<?= $old ?>" /></td>
    <td>&larr; <?php echo T_('Required'); ?></td>
</tr>
<tr>
    <th align="left"><?php echo T_('New'); ?></th>
    <td><input type="text" name="new" id="new" value="" /></td>
    <td>&larr; <?php echo T_('Required'); ?></td>
</tr>
<tr>
    <td></td>
    <td>
    <input type="submit" name="confirm" value="<?php echo T_('Rename'); ?>" />
    <input type="submit" name="cancel" value="<?php echo T_('Cancel'); ?>" />
    </td>
    <td></td>
</tr>

</table>
</p>

<?php if (isset($referrer)): ?>
<div><input type="hidden" name="referrer" value="<?php echo $referrer; ?>" /></div>
<?php endif; ?>

</form>

<?php
$this->includeTemplate($GLOBALS['bottom_include']); 
?>
 	  	 
