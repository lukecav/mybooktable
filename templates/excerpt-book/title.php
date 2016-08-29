<?php if(mbt_get_setting('enable_socialmedia_book_excerpt')) { ?>
	<div class="mbt-book-socialmedia-badges"><?php mbt_the_book_socialmedia_badges(); ?></div>
<?php } ?>
<h2 class="mbt-book-title" itemprop="name">
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</h2>