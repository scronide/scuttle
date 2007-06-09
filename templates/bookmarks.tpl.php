<?php
$bookmarkservice    =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice        =& ServiceFactory::getServiceInstance('UserService');

$logged_on_userid = $userservice->getCurrentUserId();
$this->includeTemplate($GLOBALS['top_include']);

$access = array('public', 'shared', 'private');

include('search.inc.php');
if ($user) {
    include('profilebar.tpl.php');
}
if (count($bookmarks) > 0) {
?>

<script type="text/javascript">
window.onload = playerLoad;
</script>

<div id="main" class="with-sidebar">
    <ol<?php echo ($start > 0 ? ' start="'. ++$start .'"' : ''); ?> id="bookmarks">

    <?php
    foreach(array_keys($bookmarks) as $key) {
        $row =& $bookmarks[$key];

        $classes = array('xfolkentry', $access[$row['bStatus']]);

        $cats = '';
        $tags = $row['tags'];
        foreach(array_keys($tags) as $key) {
            $tag    =& $tags[$key];
            $cats   .= '<a href="'. sprintf($cat_url, filter($user, 'url'), filter($tag, 'url')) .'" rel="tag">'. filter($tag) .'</a><span>, </span>';
        }
        $cats = substr($cats, 0, -15);

        $options = '';
        // Edit and delete links
        if ($bookmarkservice->editAllowed($row['bId'])) {
            array_push($classes, 'editable');
            $options = ' <a href="'. createURL('edit', $row['bId']) .'" class="edit"><img src="'. $GLOBALS['root'] .'images/edit.png" width="16" height="16" alt="'. T_('Edit') .'" title="'. T_('Edit') .'" /></a><script type="text/javascript">document.write(" <a href=\"#\" onclick=\"deleteBookmark(this, '. $row['bId'] .'); return false;\" class=\"delete\"><img src=\"'. $GLOBALS['root'] .'images/delete.png\" width=\"16\" height=\"16\" alt=\"'. T_('Delete') .'\" title=\"'. T_('Delete') .'\" /><\/a>");</script>';
        } elseif ($userservice->isLoggedOn()) {
            array_push($classes, 'blockable');
            $options = ' <a href="'. createURL('block', 'bookmark/'. $row['bId']) .'" class="block"><img src="'. $GLOBALS['root'] .'images/block.png" width="16" height="16" alt="'. T_('Block') .'" title="'. T_('Block') .'" /></a>';
        }

        // Udders!
        $popularity     = $bookmarkservice->countOthers($row['bAddress']) + 1;
        $url_history    = createURL('history', $row['bHash']);

        // Copy link
        $copy = '';
        if ($userservice->isLoggedOn() && ($logged_on_userid != $row['uId'])) {
            // Get the username of the current user
            $currentUser = $userservice->getCurrentUser();
            $currentUsername = $currentUser[$userservice->getFieldName('username')];
            $copy .= ' - <a href="'. createURL('bookmarks', $currentUsername .'?action=add&amp;address='. urlencode($row['bAddress']) .'&amp;title='. urlencode($row['bTitle'])) .'">'. T_('Copy') .'</a>';   
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
        ?>
        <li class="<?php echo implode(' ', $classes); ?>">
            <ul>
                <li class="link"><a href="<?php echo $address; ?>"<?php echo $rel; ?> class="taggedlink"><?php echo filter($row['bTitle']); ?></a><?php echo $options; ?></li>
                <?php if ($row['bDescription'] != ''): ?>
                <li class="description"><?php echo filter($row['bDescription']); ?></li>
                <?php
                endif;
                if ($cats != ''):
                ?>
                <li class="tags"><?php echo $cats; ?></li>
                <?php endif; ?>
                <li class="popularity"><a href="<?php echo $url_history; ?>"><?php echo $popularity; ?></a></li>
                <li class="byline"><?php echo date($GLOBALS['shortdate'], strtotime($row['bDatetime'])) ?> &middot; <a href="<?php echo createURL('bookmarks', $row['username']); ?>"><?php echo $row['username']; ?></a></li>
            </ul>
        </li>
    <?php
    }
    ?>

    </ol>

    <?php
    // PAGINATION
    
    // Ordering
    $sortOrder = '';
    if (isset($_GET['sort'])) {
        $sortOrder = 'sort='. $_GET['sort'];
    }
    
    $sortAmp = (($sortOrder) ? '&amp;'. $sortOrder : '');
    $sortQue = (($sortOrder) ? '?'. $sortOrder : '');
    
    // Previous
    $perpage = getPerPageCount();
    $tfirst = '&laquo; '. T_('First');
    $tprev = '&lsaquo; '. T_('Previous');
    if (!$page || $page < 2) {
        $page = 1;
        $start = 0;
        $bfirst = '<span class="disable">'. $tfirst .'</span>';
        $bprev = '<span class="disable">'. $tprev .'</span>';
    } else {
        $prev = $page - 1;
        $prev = 'page='. $prev;
        $start = ($page - 1) * $perpage;
        $bfirst= '<a href="'. sprintf($nav_url, $user, $currenttag, '') . $sortQue .'">'. $tfirst .'</a>';
        $bprev = '<a href="'. sprintf($nav_url, $user, $currenttag, '?') . $prev . $sortAmp .'">'. $tprev .'</a>';
    }
    
    // Next
    $next = $page + 1;
    $totalpages = ceil($total / $perpage);
    $tnext = T_('Next') .' &rsaquo;';
    $tlast = T_('Last') .' &raquo;';
    if (count($bookmarks) < $perpage || $perpage * $page == $total) {
        $bnext = '<span class="disable">'. $tnext .'</span>';
        $blast = '<span class="disable">'. $tlast .'</span>';
    } else {
        $bnext = '<a href="'. sprintf($nav_url, $user, $currenttag, '?page=') . $next . $sortAmp .'">'. $tnext .'</a>';
        $blast = '<a href="'. sprintf($nav_url, $user, $currenttag, '?page=') . $totalpages . $sortAmp .'">'. $tlast .'</a>';
    }
    echo '<p class="paging">'. $bfirst .' '. $bprev .' '. $bnext .' '. $blast .' '. sprintf(T_('Page %d of %d'), $page, $totalpages) .'</p>';
} else {
?>

    <p class="error"><?php echo T_('No bookmarks available'); ?>.</p>

<?php
}
?>

</div>

<?php
$this->includeTemplate('sidebar.tpl');
$this->includeTemplate($GLOBALS['bottom_include']);
?>