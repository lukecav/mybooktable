<div class="mbt-book-meta">
	<?php mbt_the_book_authors_list(); ?>
	<?php mbt_the_book_series_list(); ?>
	<?php mbt_the_book_genres_list(); ?>
	<?php mbt_the_book_tags_list(); ?>
	<?php mbt_the_book_publisher(); ?>
	<?php mbt_the_book_metadata('mbt_publication_year', __('Publication Year', 'mybooktable')); ?>
	<?php mbt_the_book_metadata('mbt_book_format', __('Format', 'mybooktable')); ?>
	<?php mbt_the_book_metadata('mbt_book_length', __('Length', 'mybooktable')); ?>
	<?php mbt_the_book_metadata('mbt_narrator', __('Narrator', 'mybooktable')); ?>
	<?php mbt_the_book_metadata('mbt_illustrator', __('Illustrator', 'mybooktable')); ?>
	<?php mbt_the_book_unique_id(); ?>
	<?php mbt_the_book_star_rating(); ?>
</div>