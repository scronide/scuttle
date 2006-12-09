<?php
$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$this->includeTemplate($GLOBALS['top_include']);
?>

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['root']; ?>user.css" />

<div id="profile">
    <p id="picture">
        <img src="<?php echo $GLOBALS['root']; ?>logo.png" width="64" height="64" alt="" />
    </p>
    <ul id="user">
        <li id="username"><?php echo $user; ?></li>

        <?php if ($row['name'] != ''): ?>
        <li id="name"><?php echo $row['name']; ?></li>
        <?php endif; ?>

        <?php if ($row['location'] != ''): ?>
        <li id="location"><?php echo $row['location']; ?></li>
        <?php endif; ?>
    </ul>

    <?php if ($row['uContent'] != ''): ?>
    <div id="bio">
        <h3><?php echo T_('Bio'); ?></h3>
        <p><?php echo $row['uContent']; ?></p>
    </div>
    <?php endif; ?>

    <?php if ($watching): ?>
    <div id="watching">
        <h3><?php echo T_('Watching'); ?> <span class="count">(<?php echo sizeof($watching) ?>)</span></h3>
        <p>
        <?php
        foreach($watching as $watchuser) {
            echo '<a href="'. createURL('bookmarks', $watchuser) .'"><img src="'. $GLOBALS['root'] .'logo.png" width="48" height="48" alt="'. $watchuser .'" title="'. $watchuser .'" /></a> ';
        }
        ?>
        </p>
    </div>
    <?php endif; ?>

    <h3><?php echo T_('Account'); ?></h3>
    <table id="dates">
    <tr id="since">
        <th><?php echo T_('Member Since'); ?></th>
        <td><?php echo date($GLOBALS['longdate'], strtotime($row['uDatetime'])); ?></td>
    </tr>
    </table>
</div>
<div id="bookmarks">
    <h3><?php echo T_('Bookmarks'); ?> <span class="count">(<?php echo $total ?>)</span></h3>
    <ol id="bookmarks">
    <?php
    foreach(array_keys($bookmarks) as $key) {
        $row =& $bookmarks[$key];
        switch ($row['bStatus']) {
            case 0:
                $access = '';
                break;
            case 1:
                $access = ' shared';
                break;
            case 2:
                $access = ' private';
                break;
        }

        $cats = '';
        $tags = $row['tags'];
        foreach(array_keys($tags) as $key) {
            $tag =& $tags[$key];
            $cats .= '<a href="'. sprintf($cat_url, filter($user, 'url'), filter($tag, 'url')) .'" rel="tag">'. filter($tag) .'</a>, ';
        }
        $cats = substr($cats, 0, -2);
        if ($cats != '') {
            $cats = ' to '. $cats;
        }

        $copy = '';

        // Udders!
        if (!isset($hash)) {
            $others = $bookmarkservice->countOthers($row['bAddress']);
            $ostart = '<a href="'. createURL('history', $row['bHash']) .'">';
            $oend = '</a>';
            switch ($others) {
                case 0:
                    break;
                case 1:
                    $copy .= sprintf(T_(' and %s1 other%s'), $ostart, $oend);
                    break;
                default:
                    $copy .= sprintf(T_(' and %2$s%1$s others%3$s'), $others, $ostart, $oend);
            }
        }

        // Nofollow option
        $rel = '';
        if ($GLOBALS['nofollow']) {
            $rel = ' rel="nofollow"';
        }

        $address = filter($row['bAddress']);
        
        // Redirection option
        if ($GLOBALS['useredir']) {
            $address = $GLOBALS['url_redir'] . $address;
        }
        
        // Output
        echo '<li class="xfolkentry'. $access .'">'."\n";
        echo '<div class="link"><a href="'. $address .'"'. $rel .' class="taggedlink">'. filter($row['bTitle']) ."</a></div>\n";
        if ($row['bDescription'] != '') {
            echo '<div class="description">'. filter($row['bDescription']) ."</div>\n";
        }
        echo '<div class="meta">'. date($GLOBALS['shortdate'], strtotime($row['bDatetime'])) . $cats . $copy ."</div>\n";
        echo "</li>\n";
    }
    ?>
    </ol>
</div>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>
