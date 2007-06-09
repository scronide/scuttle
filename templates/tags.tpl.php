<?php
$this->includeTemplate($GLOBALS['top_include']);
if (isset($tags) && count($tags) > 0) {
?>

<div id="main">
    <p class="tags">
    <?php
    foreach ($tags as $row) {
        $entries = T_ngettext('bookmark', 'bookmarks', $row['bCount']);
        echo '<a href="'. sprintf($cat_url, $user, filter($row['tag'], 'url')) .'" title="'. $row['bCount'] .' '. $entries .'" rel="tag" style="font-size:'. $row['size'] .'">'. filter($row['tag']) .'</a> ';
    }
    ?>
    </p>
</div>

<?php
}
$this->includeTemplate($GLOBALS['bottom_include']);
?>