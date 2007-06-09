<?php require_once('../header.inc.php'); ?>
<form action="<?php echo $formaction; ?>" method="post" class="edit">
    <p>
        <label for="addressField"><?php echo T_('Address'); ?></label>
        <input type="text" id="addressField" name="addressField" size="75" maxlength="65535" value="<?php echo $address; ?>" />
        &larr; <?php echo T_('Required'); ?><br />
 
        <label for="titleField"><?php echo T_('Title'); ?></label>
        <input type="text" id="titleField" name="titleField" size="75" maxlength="255" value="<?php echo $title; ?>" />
        &larr; <?php echo T_('Required'); ?><br />
 
        <label for="descriptionField"><?php echo T_('Description'); ?></label>
        <input type="text" id="descriptionField" name="descriptionField" size="75" maxlength="255" value="<?php echo $description; ?>" /><br />
 
        <label for="tagsField"><?php echo T_('Tags'); ?></label>
        <input type="text" id="tagsField" name="tagsField" size="75" value="<?php echo $tags; ?>" />
        &larr; <?php echo T_('Comma-separated'); ?>
        <br />
 
        <label for="statusField"><?php echo T_('Privacy'); ?></label>
        <select id="statusField" name="statusField">
            <option value="0"><?php echo T_('Public'); ?></option>
            <option value="1"><?php echo T_('Shared with Watch List'); ?></option>
            <option value="2"><?php echo T_('Private'); ?></option>
        </select>
    </p>
    <p>
        <input type="submit" name="submitted" value="<?php echo $submitButton; ?>" />
        <input type="reset" name="cancelled" value="<?php echo T_('Cancel'); ?>" />
    </p>
</form>
<script type="text/javascript">
$('input[@name=cancelled]').click(function() {
    var li = $(this).parents('li.edit');
    li.next().show();
    li.remove();
});
</script>