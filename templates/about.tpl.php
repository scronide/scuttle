<?php $this->includeTemplate($GLOBALS['top_include']); ?>

<ul>
  <li><?php echo T_('<strong>Store</strong> all your favourite links in one place, accessible from anywhere.'); ?></li>
  <li><?php echo T_('<strong>Share</strong> your bookmarks with everyone, with friends on your watchlist or just keep them private.') ;?></li>
  <li><?php echo T_('<strong>Tag</strong> your bookmarks with as many labels as you want, instead of wrestling with folders.'); ?></li>
  <li><?php echo sprintf(T_('<strong><a href="register.php">Register now</a></strong> to start using %s!'), $GLOBALS['sitename']); ?></li>
</ul>

<h3><?php echo T_('Geek Stuff'); ?></h3>
<ul>
  <li><?php echo sprintf(T_('%s is based on <a href="http://sourceforge.net/projects/scuttle/">an open-source project</a> licensed under the <a href="http://www.gnu.org/copyleft/gpl.html"><acronym title="GNU\'s Not Unix">GNU</acronym> General Public License</a>. This means you can host it on your own web server for free, whether it is on the Internet, a private network or just your own computer.'), $GLOBALS['sitename']); ?></li>
  <li><?php echo sprintf(T_('%1$s supports most of the <a href="http://delicious.com/help/api">delicious <abbr title="Application Programming Interface">API</abbr></a>. Almost all of the neat tools made for that system can be modified to work with %1$s instead. If you find a tool that won\'t let you change the API address, ask the creator to add this setting. You never know, they might just do it.'), $GLOBALS['sitename']); ?></li>
</ul>

<?php $this->includeTemplate($GLOBALS['bottom_include']); ?>