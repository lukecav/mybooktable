<div class="mbt-book-section mbt-book-overview-section" name="mbt-book-overview-anchor">
	<div class="mbt-book-section-title"><?php _e('About the Book', 'mybooktable'); ?></div>
	<div class="mbt-book-section-content">
		<div class="mbt-book-overview">
			<?php
				if(function_exists('st_remove_st_add_link')) { st_remove_st_add_link(''); }
				global $post; echo(apply_filters('the_content', $post->post_content));
			?>
		</div>
		<?php if(mbt_get_setting('enable_socialmedia_single_book')) { ?>
			<div class="mbt-book-socialmedia-bar"><?php mbt_the_book_socialmedia_bar(); ?></div>
		<?php } ?>
	</div>
</div>