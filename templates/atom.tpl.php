<?php echo '<?xml version="1.0" encoding="utf-8" ?'.">\n"; ?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id></id>
	<title><?php echo $feedtitle; ?></title>
	<updated></updated>
	<link href="<?php echo $feedlink; ?>" />
	<subtitle><?php echo $feeddescription; ?></subtitle>
<?php foreach($bookmarks as $bookmark): ?>
	<entry>
		<id></id>
		<title><?php echo $bookmark['title']; ?></title>
		<updated><?php echo $bookmark['pubdate']; ?></updated>
		<author>
			<name><?php echo $bookmark['creator']; ?></name>
		</author>
		<link href="<?php echo $bookmark['link']; ?>" />
		<summary><?php echo $bookmark['description']; ?></summary>
	<?php foreach($bookmark['tags'] as $tag): ?>
		<category term="<?php echo $tag; ?>" />
	<?php endforeach; ?>
	</entry>
<?php endforeach; ?>
</feed>