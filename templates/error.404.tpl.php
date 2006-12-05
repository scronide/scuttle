<?php
header('HTTP/1.x 404 Not Found');
$this->includeTemplate($GLOBALS['top_include']);
if (!$error) {
    echo '<h1>'. T_('Not Found') .'</h1>';
    echo '<p>'. T_('The requested URL was not found on this server') .'</p>';
}
$this->includeTemplate($GLOBALS['bottom_include']);
?>