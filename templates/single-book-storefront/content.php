<?php

do_action('mbt_before_single_book_storefront');
do_action('mbt_before_single_book');
do_action('mbt_single_book_storefront_images');
?><div class="mbt-book-right"><?php
do_action('mbt_single_book_storefront_title');
do_action('mbt_single_book_storefront_price');
do_action('mbt_single_book_storefront_meta');
do_action('mbt_single_book_storefront_blurb');
do_action('mbt_single_book_storefront_buybuttons');
?></div><?php
do_action('mbt_single_book_storefront_overview');
do_action('mbt_after_single_book');
do_action('mbt_after_single_book_storefront');
