<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title><?php echo filter($GLOBALS['sitename'] . (isset($pagetitle) ? ': ' . $pagetitle : '')); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="<?php echo $GLOBALS['root']; ?>icon.png" />
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['root']; ?>scuttle.css" />
    <?php
    $size = count($rsschannels);
    for ($i = 0; $i < $size; $i++) {
        echo '<link rel="alternate" type="application/rss+xml" title="'. $rsschannels[$i][0] .'" href="'. $rsschannels[$i][1] .'" />';
    }
    ?>
    <?php if ($loadjs): ?>
      <script type="text/javascript" src="<?php echo $GLOBALS['root']; ?>includes/jquery-1.4.4.min.js"></script>
      <script type="text/javascript" src="<?php echo $GLOBALS['root']; ?>jsScuttle.php"></script>
    <?php endif; ?>
</head>
<body>

<?php
$headerstyle = '';
if (isset($_GET['popup'])) {
    $headerstyle = ' class="popup"';
}
?>

<div id="header"<?php echo $headerstyle; ?>>
    <h1><a href="<?php echo $GLOBALS['root']; ?>"><?php echo $GLOBALS['sitename']; ?></a></h1>
    <?php
    if (!isset($_GET['popup'])) {
        $this->includeTemplate('toolbar.inc');
    }
    ?>
</div>

<?php
if (isset($subtitle)) {
    echo '<h2>'. $subtitle ."</h2>\n";
}
if (isset($error)) {
    echo '<p class="error">'. $error ."</p>\n";
}
if (isset($msg)) {
    echo '<p class="success">'. $msg ."</p>\n";
}
?>
