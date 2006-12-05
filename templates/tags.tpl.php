<?php
$this->includeTemplate($GLOBALS['top_include']);
if ($tags && count($tags) > 0) {
?>

<p id="sort">
    <?php echo T_("Sort by:"); ?>
    <a href="?sort=alphabet_asc"><?php echo T_("Alphabet"); ?></a><span> / </span>
    <a href="?sort=popularity_asc"><?php echo T_("Popularity"); ?></a>
</p>
<p class="tags">

<?php
$contents = '';
foreach ($tags as $row) {
    $entries = T_ngettext('bookmark', 'bookmarks', $row['bCount']);
    $contents .= '<a href="'. sprintf($cat_url, $user, filter($row['tag'], 'url')) .'" title="'. $row['bCount'] .' '. $entries .'" rel="tag" style="font-size:'. $row['size'] .'">'. filter($row['tag']) .'</a> ';
}
echo $contents ."\n";
?>

</p>

<?php
}
$this->includeTemplate($GLOBALS['bottom_include']);
?>