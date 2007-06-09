<div id="sidebar">
    <?php
    $size = count($sidebar_blocks);
    for ($i = 0; $i < $size; $i++) {
        $this->includeTemplate('sidebar.block.'. $sidebar_blocks[$i]);
    }
    ?>
</div>