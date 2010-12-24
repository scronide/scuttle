<?php $this->includeTemplate($GLOBALS['top_include']); ?>

<div id="bookmarks">
    <form id="import" enctype="multipart/form-data" action="<?php echo $formaction; ?>" method="post">
    <table>
    <tr valign="top">
        <th align="left"><?php echo T_('File'); ?></th>
        <td>
            <input type="hidden" name="MAX_FILE_SIZE" value="1024000" />
            <input type="file" name="userfile" size="50" />
        </td>
    </tr>
    <tr valign="top">
        <th align="left"><?php echo T_('Privacy'); ?></th>
        <td>
            <select name="status">
                <option value="0"><?php echo T_('Public'); ?></option>
                <option value="1"><?php echo T_('Shared with Watchlist'); ?></option>
                <option value="2"><?php echo T_('Private'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td />
        <td><input type="submit" value="<?php echo T_('Import'); ?>" /></td>
    </tr>
    </table>
    </form>

    <h3><?php echo T_('Instructions'); ?></h3>
    <ol>
        <li>
            <p><?php echo T_('Export your bookmarks from your browser to a file'); ?>:</p>
            <ul>
                <li><?php echo T_('Internet Explorer: <kbd>File &rarr; Import and Export&hellip; &rarr; Export Favorites</kbd>'); ?></li>
                <li><?php echo T_('Mozilla Firefox: <kbd>Bookmarks &rarr; Manage Bookmarks&hellip; &rarr; File &rarr; Export&hellip;</kbd>'); ?></li>
                <li><?php echo T_('Google Chrome: <kbd>Bookmark Manager &rarr; Organize &rarr; Export Bookmarks&hellip;</kbd>'); ?></li>
            </ul>
        </li>
        <li><?php echo T_('Click <kbd>Browse&hellip;</kbd> to find the saved bookmark file on your computer. The maximum size the file can be is 1MB.'); ?></li>
        <li><?php echo T_('Select the default privacy setting for your imported bookmarks.'); ?></li>
        <li><?php echo T_('Click <kbd>Import</kbd> to start importing the bookmarks; it may take a minute.'); ?></li>
    </ol>
</div>

<?php $this->includeTemplate($GLOBALS['bottom_include']); ?>