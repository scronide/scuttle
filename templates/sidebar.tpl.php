<div id="sidebar">
    <?php
    $size = count($sidebar_blocks);
    for ($i = 0; $i < $size; $i++) {
        $this->includeTemplate('sidebar.block.'. $sidebar_blocks[$i]);
    }

    $size = count($rsschannels);
    for ($i = 0; $i < $size; $i++) {
        echo '<p><a href="'. $rsschannels[$i][1] .'" title="'. $rsschannels[$i][0] .'"><img src="'. $GLOBALS['root'] .'rss.gif" width="16" height="16" alt="'. $rsschannels[$i][0] .'" /></a></p>'; 
    }
    ?>
</div>
