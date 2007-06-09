<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
   <title><?php echo filter($GLOBALS['sitename'] . (isset($pagetitle) ? ': ' . $pagetitle : '')); ?></title>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <link rel="icon" type="image/png" href="<?php echo $GLOBALS['root']; ?>images/icon.png" />
   <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['root']; ?>general.css" />
    <?php
    $size = count($rsschannels);
    for ($i = 0; $i < $size; $i++) {
        echo '<link rel="alternate" type="application/rss+xml" title="'. $rsschannels[$i][0] .'" href="'. $rsschannels[$i][1] .'" />';
    }
    if ($loadjs) {
        echo '<script type="text/javascript" src="'. $GLOBALS['root'] .'includes/jquery.js"></script>'; 
        echo '<script type="text/javascript" src="'. $GLOBALS['root'] .'javascript.php"></script>';
    }
    if ($GLOBALS['nofollow']) {
        echo '<meta name="robots" content="index, nofollow" />';
    }
    ?>
</head>
<body>
<div id="header">
    <?php if ($isLoggedOn): ?>
    <p id="access">
        <?php echo T_('Logged in as'); ?>
        <a href="<?php echo createURL('profile', $currentUsername); ?>"><?php echo $currentUsername; ?></a> &mdash;
        <a href="<?php echo $GLOBALS['root']; ?>?action=logout" class="logout"><?php echo T_('Log Out'); ?></a>
    </p>
    <h1><a href="<?php echo $GLOBALS['root']; ?>"><?php echo $GLOBALS['sitename']; ?></a></h1>
    <p id="toolbar">
        <a href="<?php echo createURL('bookmarks', $currentUsername); ?>"><?php echo T_('Bookmarks'); ?></a>
        <a href="<?php echo createURL('watchlist', $currentUsername); ?>"><?php echo T_('Watchlist'); ?></a>
        <a href="#" class="add"><?php echo T_('Add a Bookmark'); ?></a>
    </p>
    <?php else: ?>
    <p id="access">
        <a href="<?php echo createURL('register'); ?>"><?php echo T_('Register'); ?></a> &mdash;
        <a href="<?php echo createURL('login'); ?>" class="login"><?php echo T_('Log In'); ?></a>
    </p>
    <h1><a href="<?php echo $GLOBALS['root']; ?>"><?php echo $GLOBALS['sitename']; ?></a></h1>
    <p id="toolbar">
        <a href="<?php echo createURL('about'); ?>"><?php echo T_('About'); ?></a>
    </p>
    <?php endif; ?>
</div>
<div id="add">
   <h2><?php echo T_('Add a Bookmark'); ?></h2>
   <form action="<?php echo createURL('bookmarks', $currentUsername); ?>" method="post">
   <table>
   <tr>
       <th align="left"><?php echo T_('Address'); ?></th>
       <td><input type="text" id="address" name="address" size="75" maxlength="65535" value="" onblur="useAddress(this)" /></td>
       <td>&larr; <?php echo T_('Required'); ?></td>
   </tr>
   <tr>
       <th align="left">Title</th>
       <td><input type="text" id="titleField" name="title" size="75" maxlength="255" value="" onkeypress="this.style.backgroundImage = 'none';" /></td>
       <td>&larr; <?php echo T_('Required'); ?></td>
   </tr>
   <tr>
       <th align="left"><?php echo T_('Description'); ?></th>
       <td><input type="text" name="description" size="75" maxlength="255" value="" /></td>
       <td></td>
   </tr>
   <tr>
       <th align="left"><?php echo T_('Tags'); ?></th>
       <td><input type="text" id="tags" name="tags" size="75" value="" /></td>
       <td>&larr; Comma-separated</td>
   </tr>
   <tr>
       <th align="left"><?php echo T_('Privacy'); ?></th>
       <td>
           <select name="status">
               <option value="0" selected="selected"><?php echo T_('Public'); ?></option>
               <option value="1"><?php echo T_('Shared with Watch List'); ?></option>
               <option value="2"><?php echo T_('Private'); ?></option>
           </select>
       </td>
       <td></td>
   </tr>
   <tr>
       <td></td>
       <td><input type="submit" name="submitted" value="<?php echo T_('Add Bookmark'); ?>" /></td>
       <td></td>
   </tr>
   </table>
   </form>
</div>
<div id="title">
    <h2><?php echo $subtitle; ?></h2>
    <?php
    if (count($sortOrders) > 0) {
        include_once('sort.tpl.php');
    }
    ?>
</div>
<?php
if (isset($error)) {
    echo '<p class="error">'. $error ."</p>\n";
}
if (isset($msg)) {
    echo '<p class="success">'. $msg ."</p>\n";
}
?>