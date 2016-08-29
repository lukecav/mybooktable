<?php if(mbt_get_setting('enable_socialmedia_single_book')) { ?>
	<div class="mbt-book-socialmedia-badges"><?php mbt_the_book_socialmedia_badges(); ?></div>
<?php } ?>
<?php if(!mbt_is_in_compatability_mode()) { ?>
<h1 itemprop="name" class="mbt-book-title"><?php the_title(); ?></h1>
<?php } ?>