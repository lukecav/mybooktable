<div class="mbt-book-share">
	<h3 class="mbt-book-share-title"><?php _e('Share', 'mybooktable'); ?></h3>
	<div class="mbt-book-share-buttons">
		<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo(urlencode(get_permalink())); ?>" target="_blank" class="mbt-book-share-button mbt-book-share-facebook">Facebook</a>
		<a href="https://twitter.com/intent/tweet?text=<?php echo(urlencode(__('Check out this book!', 'mybooktable'))); ?>&url=<?php echo(urlencode(get_permalink())); ?>&original_referer=<?php echo(urlencode(get_permalink())); ?>" target="_blank" class="mbt-book-share-button mbt-book-share-twitter">Twitter</a>
		<a href="https://plus.google.com/share?url=<?php echo(urlencode(get_permalink())); ?>" target="_blank" class="mbt-book-share-button mbt-book-share-googleplus">Google+</a>
	</div>
</div>