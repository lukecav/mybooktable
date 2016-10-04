<?php

mbt_start_template_context('archive');
do_action('mbt_content_wrapper_start');
?> <div id="mbt-container"> <?php
do_action('mbt_book_archive_content');
?> </div> <?php
do_action('mbt_content_wrapper_end');
mbt_end_template_context();
