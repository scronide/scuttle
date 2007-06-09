<?php
$range_text = T_("Search all bookmarks...");
if (isset($range)) {
    if ($userservice->isLoggedOn()) {
        $currentUser        = $userservice->getCurrentUser();
        $currentUsername    = $currentUser[$userservice->getFieldName('username')];
    }
    switch ($range) {
        case 'all':
            break;
        case 'watchlist':
            $range_text = T_("Search my watchlist...");
            break;
        case $currentUsername:
            $range_text = T_("Search my bookmarks...");
            break;
        default:
            $range_text = T_("Search this user's bookmarks...");
            break;
    }
} else {
    $range = 'all';
}
?>
<form id="search" action="<?php echo createURL('search'); ?>" method="post">
    <table>
    <tr>
        <td><input type="hidden" name="range" value="<?php echo $range; ?>" /></td>
        <td>
            <label for="terms"><?php echo $range_text; ?></label>
            <input type="text" id="terms" name="terms" size="50" />
        </td>
        <td><input type="submit" value="<?php echo T_('Search' /* Submit button */); ?>" /></td>
    </tr>
    </table>
</form>