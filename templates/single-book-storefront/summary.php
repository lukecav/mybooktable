<a class="mbt-book-anchor" name="mbt-book-summary-anchor"></a>
<div class="mbt-book-section mbt-book-summary-section">
	<div class="mbt-book-section-content">
		<?php do_action('mbt_single_book_storefront_images'); ?>
		<div class="mbt-book-right">
			<?php if(mbt_get_setting('enable_socialmedia_single_book')) { mbt_the_book_socialmedia_badges(); } ?>
			<?php do_action('mbt_single_book_storefront_title'); ?>
			<?php do_action('mbt_single_book_storefront_price'); ?>
			<?php do_action('mbt_single_book_storefront_meta'); ?>
			<?php do_action('mbt_single_book_storefront_blurb'); ?>
			<?php do_action('mbt_single_book_storefront_buybuttons'); ?>
		</div>
		<div style="clear:both;"></div>
	</div>
</div>