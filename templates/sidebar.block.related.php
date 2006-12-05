<?php
$tagservice =& ServiceFactory::getServiceInstance('TagService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

$logged_on_userid = $userservice->getCurrentUserId();
if ($logged_on_userid === false) {
    $logged_on_userid = NULL;
}
if ($currenttag) {
    $relatedTags = $tagservice->getRelatedTags($currenttag, $userid, $logged_on_userid);
    if (sizeof($relatedTags) > 0) {
?>

<h2><?php echo T_('Related Tags'); ?></h2>
<div id="related">
    <table>
    <?php foreach($relatedTags as $row): ?>
    <tr>
        <td><a href="<?php echo sprintf($cat_url, filter($user, 'url'), filter($currenttag, 'url') .'+'. filter($row['tag'], 'url')); ?>">+</a></td>
        <td><a href="<?php echo sprintf($cat_url, filter($user, 'url'), filter($row['tag'], 'url')); ?>" rel="tag"><?php echo filter($row['tag']); ?></a></td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>

<?php
    }
}
?>