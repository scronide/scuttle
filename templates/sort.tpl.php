<p id="sort">
<?php foreach($sortOrders as $sortOrder): ?>
    <a href="<?php echo $sortOrder['link']; ?>" title="<?php echo $sortOrder['title']; ?>"><?php echo $sortOrder['text']; ?></a>
<?php endforeach; ?>
</p>