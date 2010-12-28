<?php $this->includeTemplate($GLOBALS['top_include']); ?> 

<p><?php echo sprintf(T_('Sign up here to create a free %s account. All the information requested below is required'), $GLOBALS['sitename']); ?>.</p>

<form action="<?php echo $formaction; ?>" method="post">
<table>
<tr>
  <th align="left"><label for="username"><?php echo T_('Username'); ?></label></th>
  <td><input type="text" id="username" name="username" size="20" maxlength="25" class="required" /></td>
  <td id="availability"></td>
</tr>
<tr>
  <th align="left"><label for="password"><?php echo T_('Password'); ?></label></th>
  <td><input type="password" id="password" name="password" size="20" class="required" /></td>
  <td></td>
</tr>
<tr>
  <th align="left"><label for="passconf"><?php echo T_('Confirm Password'); ?></label></th>
  <td><input type="password" id="passconf" name="passconf" size="20" class="required" /></td>
  <td></td>
</tr>
<tr>
  <th align="left"><label for="email"><?php echo T_('E-mail'); ?></label></th>
  <td><input type="text" id="email" name="email" size="40" maxlength="50" class="required" /></td>
  <td></td>
</tr>
<tr>
  <td><input type="hidden" name="token" value="<?php echo $token; ?>" /></td>
  <td><input type="submit" name="submitted" value="<?php echo T_('Register'); ?>" /></td>
  <td></td>
</tr>
</table>
</form>
<script type="text/javascript">
$(function() {
  $("#username").focus()
                .keydown(function() {
                  clearTimeout(self.searching);
                  self.searching = setTimeout(function() {
                    $.get("<?php echo $GLOBALS['root']; ?>ajaxIsAvailable.php?username=" + $("#username").val(), function(data) {
                        if (data) {
                          $("#availability").removeClass()
                                            .html("<?php echo T_('Available'); ?>");
                        } else {
                          $("#availability").removeClass()
                                            .addClass("not-available")
                                            .html("<?php echo T_('Not Available'); ?>");
                        }
                      }
                    );
                  }, 300);
                });
});
</script>

<?php $this->includeTemplate($GLOBALS['bottom_include']); ?>