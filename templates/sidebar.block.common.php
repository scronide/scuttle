<?php
$tagservice =& ServiceFactory::getServiceInstance('TagService');
$commonTags =& $tagservice->getRelatedTagsByHash($hash);
$commonTags =& $tagservice->tagCloud($commonTags, 5, 90, 225, 'alphabet_asc'); 

if ($commonTags && count($commonTags) > 0) {
?>

<h2><?php echo T_('Popular Tags'); ?></h2>
<div id="common">
    <p class="tags">
    <?php
    $contents = '';
    foreach ($commonTags as $row) {
        $entries = T_ngettext('bookmark', 'bookmarks', $row['bCount']);
        $contents .= '<a href="'. sprintf($cat_url, $user, filter($row['tag'], 'url')) .'" title="'. $row['bCount'] .' '. $entries .'" rel="tag" style="font-size:'. $row['size'] .'">'. filter($row['tag']) .'</a> ';
    }
    echo $contents ."\n";
    ?>
    </p>
</div>

<?php
}
?>