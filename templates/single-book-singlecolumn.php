<?php

mbt_start_template_context('single');
mbt_start_template_display_mode('singlecolumn');
do_action('mbt_content_wrapper_start');
?> <div id="mbt-container"> <?php
do_action('mbt_single_book_singlecolumn_content');
?> </div> <?php
do_action('mbt_content_wrapper_end');
mbt_end_template_display_mode();
mbt_end_template_context();
