<?php

function mbt_templates_init() {
	//register image sizes
	list($width, $height) = apply_filters('mbt_book_image_size', array(800, 800));
	add_image_size('mbt_book_image', $width, $height, false);
	list($width, $height) = apply_filters('mbt_endorsement_image_size', array(1000, 400));
	add_image_size('mbt_endorsement_image', $width, $height, false);

	add_action('mbt_get_storefront_content_sections', 'mbt_get_storefront_content_sections');
	add_action('mbt_get_singlecolumn_content_sections', 'mbt_get_singlecolumn_content_sections');

	if(!is_admin()) {
		//enqueue frontend styling
		add_action('wp_enqueue_scripts', 'mbt_enqueue_resources');
		add_action('wp_head', 'mbt_add_custom_css');

		//enable frontend ajax
		add_action('wp_head', 'mbt_enable_frontend_ajax');

		//modify the post query
		add_action('pre_get_posts', 'mbt_pre_get_posts', 20);

		//override page template
		add_filter('template_include', 'mbt_load_book_templates');

		//add body tag
		add_filter('body_class', 'mbt_body_class');

		//general hooks
		add_action('mbt_content_wrapper_start', 'mbt_do_wrapper_start');
		add_action('mbt_content_wrapper_end', 'mbt_do_wrapper_end');
		add_action('mbt_book_excerpt', 'mbt_do_book_excerpt');

		//book archive hooks
		add_action('mbt_book_archive_content', 'mbt_do_book_archive_content');
		add_action('mbt_before_book_archive', 'mbt_do_before_book_archive', 0);
		add_action('mbt_after_book_archive', 'mbt_do_after_book_archive', 100);
		add_action('mbt_book_archive_header', 'mbt_do_book_archive_header');
		add_action('mbt_book_archive_header_image', 'mbt_do_book_archive_header_image');
		add_action('mbt_book_archive_header_title', 'mbt_do_book_archive_header_title');
		add_action('mbt_book_archive_header_description', 'mbt_do_book_archive_header_description');
		add_action('mbt_book_archive_loop', 'mbt_do_book_archive_loop');
		add_action('mbt_book_archive_no_results', 'mbt_do_book_archive_no_results');
		add_action('mbt_after_book_archive_loop', 'mbt_the_book_archive_pagination');

		//single book hooks
		add_action('mbt_single_book_storefront_content', 'mbt_do_single_book_storefront_content');
		add_action('mbt_before_single_book_storefront', 'mbt_do_before_single_book_storefront', 0);
		add_action('mbt_after_single_book_storefront', 'mbt_do_after_single_book_storefront', 100);
		add_action('mbt_single_book_storefront_summary', 'mbt_do_single_book_storefront_summary');
		add_action('mbt_single_book_storefront_images', 'mbt_do_single_book_storefront_images');
		add_action('mbt_single_book_storefront_title', 'mbt_do_single_book_storefront_title');
		add_action('mbt_single_book_storefront_price', 'mbt_do_single_book_storefront_price');
		add_action('mbt_single_book_storefront_meta', 'mbt_do_single_book_storefront_meta');
		add_action('mbt_single_book_storefront_blurb', 'mbt_do_single_book_storefront_blurb');
		add_action('mbt_single_book_storefront_buybuttons', 'mbt_do_single_book_storefront_buybuttons');
		add_action('mbt_single_book_storefront_overview', 'mbt_do_single_book_storefront_overview');
		add_filter('mbt_book_section_title', 'mbt_single_book_storefront_book_reviews_section_title', 10, 3);

		add_action('mbt_single_book_singlecolumn_content', 'mbt_do_single_book_singlecolumn_content');
		add_action('mbt_before_single_book_singlecolumn', 'mbt_do_before_single_book_singlecolumn', 0);
		add_action('mbt_after_single_book_singlecolumn', 'mbt_do_after_single_book_singlecolumn', 100);
		add_action('mbt_single_book_singlecolumn_summary', 'mbt_do_single_book_singlecolumn_summary');
		add_action('mbt_single_book_singlecolumn_title', 'mbt_do_single_book_singlecolumn_title');
		add_action('mbt_single_book_singlecolumn_blurb', 'mbt_do_single_book_singlecolumn_blurb');
		add_action('mbt_single_book_singlecolumn_overview', 'mbt_do_single_book_singlecolumn_overview');
		add_action('mbt_single_book_singlecolumn_meta', 'mbt_do_single_book_singlecolumn_meta');

		if(!mbt_get_setting('hide_domc_notice')) { add_action('mbt_after_single_book', 'mbt_the_domc_notice', 90); }

		//book excerpt hooks
		add_action('mbt_book_excerpt_content', 'mbt_do_book_excerpt_content');
		add_action('mbt_before_book_excerpt', 'mbt_do_before_book_excerpt', 0);
		add_action('mbt_after_book_excerpt', 'mbt_do_after_book_excerpt', 100);
		add_action('mbt_book_excerpt_images', 'mbt_do_book_excerpt_images');
		add_action('mbt_book_excerpt_title', 'mbt_do_book_excerpt_title');
		add_action('mbt_book_excerpt_price', 'mbt_do_book_excerpt_price');
		add_action('mbt_book_excerpt_meta', 'mbt_do_book_excerpt_meta');
		add_action('mbt_book_excerpt_blurb', 'mbt_do_book_excerpt_blurb');
		add_action('mbt_book_excerpt_buybuttons', 'mbt_do_book_excerpt_buybuttons');
	}
}
add_action('mbt_init', 'mbt_templates_init');



/*---------------------------------------------------------*/
/* Template Overload Functions                             */
/*---------------------------------------------------------*/

function mbt_load_book_templates($template) {
	global $post;

	if(mbt_is_booktable_page() or is_post_type_archive('mbt_book') or is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') or is_tax('mbt_tag')) {
		$template = mbt_locate_template('archive-book.php');
	} else if(is_singular('mbt_book')) {
		$template = mbt_locate_template('single-book-'.mbt_get_book_display_mode($post->ID).'.php');
	}

	return $template;
}

function mbt_pre_get_posts($query) {
	if(!is_admin() and $query->is_main_query()) {
		if(mbt_get_setting('booktable_page') and ($booktable_page = get_post(mbt_get_setting('booktable_page')))) {
			if($query->is_post_type_archive('mbt_book')) {
				$paged = $query->get('paged');
				$query->init();
				$query->set('page_id', $booktable_page->ID);
				$query->set('paged', $paged);
				$query->parse_query();
			}
			if($query->is_page() and ($query->get('page_id') == $booktable_page->ID or $query->get('pagename') == $booktable_page->post_name)) {
				global $mbt_is_booktable_page;
				$mbt_is_booktable_page = true;
				$query->set('paged', $query->get('paged') ? $query->get('paged') : get_query_var('page'));
				add_action('mbt_before_book_archive', 'mbt_do_before_booktable_page');
				add_action('mbt_after_book_archive', 'mbt_do_after_booktable_page');
			}
		}
		if($query->is_post_type_archive('mbt_book') or $query->is_tax('mbt_author') or $query->is_tax('mbt_genre') or $query->is_tax('mbt_tag')) {
			$query->set('orderby', 'menu_order');
			$query->set('posts_per_page', mbt_get_posts_per_page());
		}
		if($query->is_tax('mbt_series')) {
			$query->set('orderby', 'meta_value_num');
			$query->set('meta_key', 'mbt_series_order');
			$query->set('order', 'ASC');
			$query->set('posts_per_page', mbt_get_posts_per_page());
		}
	}
}

function mbt_enqueue_resources() {
	wp_enqueue_style('mbt-style', plugins_url('css/frontend-style.css', dirname(__FILE__)), array(), MBT_VERSION);
	$plugin_style_css = mbt_current_style_url('style.css');
	if(!empty($plugin_style_css)) { wp_enqueue_style('mbt-style-pack', $plugin_style_css, array(), MBT_VERSION); }
	if(mbt_is_mbt_page()) { mbt_enqueue_frontend_scripts(); }
}

function mbt_enqueue_frontend_scripts() {
	global $mbt_enqueue_frontend_scripts_enqueued;
	if(!empty($mbt_enqueue_frontend_scripts_enqueued)) { return; }
	add_action('wp_footer', 'mbt_enqueue_scripts');
	$mbt_enqueue_frontend_scripts_enqueued = true;
}

function mbt_enqueue_scripts() {
	wp_enqueue_script('jquery-ui-accordion');
	wp_enqueue_script('mbt-frontend-js', plugins_url('js/frontend.js', dirname(__FILE__)), array('jquery', 'jquery-ui-accordion'), MBT_VERSION, true);
	wp_enqueue_script('mbt-shadowbox', plugins_url('js/lib/jquery.colorbox.min.js', dirname(__FILE__)), array('jquery'), MBT_VERSION, true);
}

function mbt_get_template_folders() {
	return apply_filters('mbt_template_folders', array());
}

function mbt_add_default_template_folder($folders) {
	$folders[] = plugin_dir_path(dirname(__FILE__)).'templates/';
	return $folders;
}
add_filter('mbt_template_folders', 'mbt_add_default_template_folder', 100);

function mbt_add_theme_template_folders($folders) {
	$folders[] = get_stylesheet_directory().'/mybooktable/';
	$folders[] = get_template_directory().'/mybooktable/';
	return $folders;
}
add_filter('mbt_template_folders', 'mbt_add_theme_template_folders', 50);

function mbt_locate_template($name) {
	$locatedtemplate = '';
	$template_folders = mbt_get_template_folders();
	foreach($template_folders as $folder) {
		$locatedtemplate = $folder.$name;
		if(file_exists($locatedtemplate)) { break; }
	}
	return $locatedtemplate;
}

function mbt_include_template($name) {
	$locatedtemplate = mbt_locate_template($name);
	if(file_exists($locatedtemplate)) { include($locatedtemplate); }
}

function mbt_add_custom_css() {
	$image_size = mbt_get_setting('image_size');
	$book_button_size = mbt_get_setting('book_button_size');
	$listing_button_size = mbt_get_setting('listing_button_size');
	$widget_button_size = mbt_get_setting('widget_button_size');
	echo('<style type="text/css">');
	//Book Button Size
	if($book_button_size == 'small') { echo('.mbt-book .mbt-book-buybuttons .mbt-book-buybutton img { width: 144px; height: 25px; } .mbt-book .mbt-book-buybuttons .mbt-book-buybutton { padding: 3px 6px 0px 0px; }'); }
	else if($book_button_size == 'medium') { echo('.mbt-book .mbt-book-buybuttons .mbt-book-buybutton img { width: 172px; height: 30px; } .mbt-book .mbt-book-buybuttons .mbt-book-buybutton { padding: 4px 8px 0px 0px; }'); }
	else { echo('.mbt-book .mbt-book-buybuttons .mbt-book-buybutton img { width: 201px; height: 35px; } .mbt-book .mbt-book-buybuttons .mbt-book-buybutton { padding: 5px 10px 0px 0px; }'); }
	//Listing Button Size
	if($listing_button_size == 'small') { echo('.mbt-book-archive .mbt-book .mbt-book-buybuttons .mbt-book-buybutton img { width: 144px; height: 25px; } .mbt-book-archive .mbt-book .mbt-book-buybuttons .mbt-book-buybutton { padding: 3px 6px 0px 0px; }'); }
	else if($listing_button_size == 'medium') { echo('.mbt-book-archive .mbt-book .mbt-book-buybuttons .mbt-book-buybutton img { width: 172px; height: 30px; } .mbt-book-archive .mbt-book .mbt-book-buybuttons .mbt-book-buybutton { padding: 4px 8px 0px 0px; }'); }
	else { echo('.mbt-book-archive .mbt-book .mbt-book-buybuttons .mbt-book-buybutton img { width: 201px; height: 35px; } .mbt-book-archive .mbt-book .mbt-book-buybuttons .mbt-book-buybutton { padding: 5px 10px 0px 0px; }'); }
	//Widget Button Size
	if($widget_button_size == 'small') { echo('.mbt-featured-book-widget .mbt-book-buybuttons .mbt-book-buybutton img { width: 144px; height: 25px; } .mbt-featured-book-widget .mbt-book-buybuttons .mbt-book-buybutton { padding: 3px 6px 0px 0px; }'); }
	else if($widget_button_size == 'medium') { echo('.mbt-featured-book-widget .mbt-book-buybuttons .mbt-book-buybutton img { width: 172px; height: 30px; } .mbt-featured-book-widget .mbt-book-buybuttons .mbt-book-buybutton { padding: 4px 8px 0px 0px; }'); }
	else { echo('.mbt-featured-book-widget .mbt-book-buybuttons .mbt-book-buybutton img { width: 201px; height: 35px; } .mbt-featured-book-widget .mbt-book-buybuttons .mbt-book-buybutton { padding: 5px 10px 0px 0px; }'); }
	echo('</style>');
}

function mbt_body_class($classes) {
	if(mbt_is_mbt_page()) {
		$classes[] = 'mybooktable';
	}
	return $classes;
}

/*---------------------------------------------------------*/
/* Template Context Functions                              */
/*---------------------------------------------------------*/

function mbt_init_template_context_data() {
	global $mbt_template_context_data;
	if(empty($mbt_template_context_data)) {
		$mbt_template_context_data = array(
			'context' => array(),
			'display_mode' => array(),
		);
	}
}

function mbt_start_template_context($context) {
	global $mbt_template_context_data;
	mbt_init_template_context_data();
	array_push($mbt_template_context_data['context'], $context);
}

function mbt_end_template_context() {
	global $mbt_template_context_data;
	mbt_init_template_context_data();
	array_pop($mbt_template_context_data['context']);
}

function mbt_has_template_context($context) {
	global $mbt_template_context_data;
	mbt_init_template_context_data();
	return in_array($context, $mbt_template_context_data['context']);
}

function mbt_start_template_display_mode($display_mode) {
	global $mbt_template_context_data;
	mbt_init_template_context_data();
	array_push($mbt_template_context_data['display_mode'], $display_mode);
}

function mbt_end_template_display_mode() {
	global $mbt_template_context_data;
	mbt_init_template_context_data();
	array_pop($mbt_template_context_data['display_mode']);
}

function mbt_get_template_display_mode() {
	global $mbt_template_context_data;
	mbt_init_template_context_data();
	return end($mbt_template_context_data['display_mode']);
}



/*---------------------------------------------------------*/
/* General Template Functions                              */
/*---------------------------------------------------------*/
function mbt_do_wrapper_start() {
	mbt_include_template("wrapper-start.php");
}
function mbt_do_wrapper_end() {
	mbt_include_template("wrapper-end.php");
}
function mbt_do_book_excerpt() {
	mbt_include_template("excerpt-book.php");
}
function mbt_do_before_booktable_page() {
	global $wp_query, $posts, $post, $id, $mbt_old_wp_query, $mbt_old_posts, $mbt_old_post, $mbt_old_id;
	if($wp_query->is_main_query()) {
		$mbt_old_wp_query = $wp_query;
		$mbt_old_posts = $posts;
		$mbt_old_post = $post;
		$mbt_old_id = $id;
		$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'paged' => $mbt_old_wp_query->get('paged'), 'orderby' => 'menu_order', 'posts_per_page' => mbt_get_posts_per_page()));
	}
}
function mbt_do_after_booktable_page() {
	global $wp_query, $posts, $post, $id, $mbt_old_wp_query, $mbt_old_posts, $mbt_old_post, $mbt_old_id;
	if($mbt_old_wp_query) {
		$wp_query = $mbt_old_wp_query;
		$posts = $mbt_old_posts;
		$post = $mbt_old_post;
		$id = $mbt_old_id;
	}
}
function mbt_enable_frontend_ajax() {
?>
	<script type="text/javascript">
		window.ajaxurl = "<?php echo(admin_url('admin-ajax.php')); ?>";
	</script>
<?php
}



/*---------------------------------------------------------*/
/* Book Archive Template Functions                         */
/*---------------------------------------------------------*/
function mbt_do_book_archive_content() {
	mbt_include_template("archive-book/content.php");
}
function mbt_do_before_book_archive() {
	mbt_include_template("archive-book/before.php");
}
function mbt_do_after_book_archive() {
	mbt_include_template("archive-book/after.php");
}
function mbt_do_book_archive_header() {
	mbt_include_template("archive-book/header.php");
}
function mbt_do_book_archive_header_image() {
	mbt_include_template("archive-book/header/image.php");
}
function mbt_do_book_archive_header_title() {
	mbt_include_template("archive-book/header/title.php");
}
function mbt_do_book_archive_header_description() {
	mbt_include_template("archive-book/header/description.php");
}
function mbt_do_book_archive_loop() {
	mbt_include_template("archive-book/loop.php");
}
function mbt_do_book_archive_no_results() {
	mbt_include_template("archive-book/no-results.php");
}



/*---------------------------------------------------------*/
/* Single Book Template Functions                          */
/*---------------------------------------------------------*/

/* storefront mode */

function mbt_do_single_book_storefront_content() {
	mbt_include_template("single-book-storefront/content.php");
}
function mbt_do_before_single_book_storefront() {
	mbt_include_template("single-book-storefront/before.php");
}
function mbt_do_after_single_book_storefront() {
	mbt_include_template("single-book-storefront/after.php");
}
function mbt_do_single_book_storefront_images() {
	mbt_include_template("single-book-storefront/images.php");
}
function mbt_do_single_book_storefront_title() {
	mbt_include_template("single-book-storefront/title.php");
}
function mbt_do_single_book_storefront_price() {
	mbt_include_template("single-book-storefront/price.php");
}
function mbt_do_single_book_storefront_meta() {
	mbt_include_template("single-book-storefront/meta.php");
}
function mbt_do_single_book_storefront_blurb() {
	mbt_include_template("single-book-storefront/blurb.php");
}
function mbt_do_single_book_storefront_buybuttons() {
	mbt_include_template("single-book-storefront/buybuttons.php");
}
function mbt_do_single_book_storefront_overview() {
	mbt_include_template("single-book-storefront/overview.php");
}
function mbt_do_single_book_storefront_summary() {
	mbt_include_template("single-book-storefront/summary.php");
}
function mbt_single_book_storefront_book_reviews_section_title($title, $post_id, $section_id) {
	if(mbt_get_book_display_mode($post_id) == 'storefront' and $section_id == 'reviews') { $title = ''; }
	return $title;
}
function mbt_get_storefront_content_sections($sections) {
	$sections['summary'] = array('name'=> 'Summary', 'priority' => 10, 'action' => 'mbt_single_book_storefront_summary');
	$sections['video_sample'] = array('name'=> 'Video Sample', 'priority' => 20, 'function' => 'mbt_do_standard_book_video_sample_section');
	$sections['overview'] = array('name'=> 'Overview', 'priority' => 30, 'action' => 'mbt_single_book_storefront_overview');
	$sections['endorsements'] = array('name'=> 'Endorsements', 'priority' => 40, 'function' => 'mbt_do_standard_book_endorsements_section');
	$sections['bookclub'] = array('name'=> 'Book Club Resources', 'priority' => 50, 'function' => 'mbt_do_standard_book_bookclub_section');
	if(mbt_get_setting('show_about_author')) { $sections['about_author'] = array('name'=> 'About Author', 'priority' => 100, 'function' => 'mbt_do_standard_book_about_author_section'); }
	if(mbt_get_setting('show_series')) { $sections['series'] = array('name'=> 'Books in Series', 'priority' => 110, 'function' => 'mbt_do_standard_book_series_section'); }
	$sections['kindle_instant_preview'] = array('name'=> 'Kindle Instant Preview', 'priority' => 120, 'function' => 'mbt_do_standard_kindle_instant_preview_section');
	if(mbt_get_setting('show_find_bookstore')) { $sections['find_bookstore'] = array('name'=> 'Find Bookstores', 'priority' => 130, 'function' => 'mbt_do_standard_find_bookstore_section'); }
	$sections['reviews'] = array('name'=> 'Reviews', 'priority' => 140, 'function' => 'mbt_do_standard_reviews_section');
	return $sections;
}

/* singlecolumn mode */

function mbt_do_single_book_singlecolumn_content() {
	mbt_include_template("single-book-singlecolumn/content.php");
}
function mbt_do_before_single_book_singlecolumn() {
	mbt_include_template("single-book-singlecolumn/before.php");
}
function mbt_do_after_single_book_singlecolumn() {
	mbt_include_template("single-book-singlecolumn/after.php");
}
function mbt_do_single_book_singlecolumn_title() {
	mbt_include_template("single-book-singlecolumn/title.php");
}
function mbt_do_single_book_singlecolumn_blurb() {
	mbt_include_template("single-book-singlecolumn/blurb.php");
}
function mbt_do_single_book_singlecolumn_meta() {
	mbt_include_template("single-book-singlecolumn/meta.php");
}
function mbt_do_single_book_singlecolumn_overview() {
	mbt_include_template("single-book-singlecolumn/overview.php");
}
function mbt_do_single_book_singlecolumn_summary() {
	mbt_include_template("single-book-singlecolumn/summary.php");
}
function mbt_get_singlecolumn_content_sections($sections) {
	$sections['summary'] = array('name'=> 'Summary', 'priority' => 10, 'action' => 'mbt_single_book_singlecolumn_summary');
	$sections['video_sample'] = array('name'=> 'Video Sample', 'priority' => 20, 'function' => 'mbt_do_standard_book_video_sample_section');
	$sections['overview'] = array('name'=> 'Overview', 'priority' => 30, 'action' => 'mbt_single_book_singlecolumn_overview');
	$sections['meta'] = array('name'=> 'Details', 'priority' => 40, 'action' => 'mbt_single_book_singlecolumn_meta');
	$sections['endorsements'] = array('name'=> 'Endorsements', 'priority' => 50, 'function' => 'mbt_do_standard_book_endorsements_section');
	$sections['bookclub'] = array('name'=> 'Book Club Resources', 'priority' => 60, 'function' => 'mbt_do_standard_book_bookclub_section');
	$sections['purchase'] = array('name'=> 'Buy Buttons', 'priority' => 70, 'function' => 'mbt_do_standard_book_purchase_section');
	if(mbt_get_setting('enable_socialmedia_single_book')) { $sections['socialmedia'] = array('name'=> 'Social Media', 'priority' => 80, 'function' => 'mbt_do_standard_book_socialmedia_section'); }
	if(mbt_get_setting('show_about_author')) { $sections['about_author'] = array('name'=> 'About Author', 'priority' => 100, 'function' => 'mbt_do_standard_book_about_author_section'); }
	if(mbt_get_setting('show_series')) { $sections['series'] = array('name'=> 'Books in Series', 'priority' => 110, 'function' => 'mbt_do_standard_book_series_section'); }
	$sections['preview'] = array('name'=> 'Preview', 'priority' => 125, 'function' => 'mbt_do_standard_book_preview_section');
	if(mbt_get_setting('show_find_bookstore')) { $sections['find_bookstore'] = array('name'=> 'Find Bookstores', 'priority' => 130, 'function' => 'mbt_do_standard_find_bookstore_section'); }
	$sections['reviews'] = array('name'=> 'Reviews', 'priority' => 140, 'function' => 'mbt_do_standard_reviews_section');
	return $sections;
}



/*---------------------------------------------------------*/
/* Book Excerpt Template Functions                         */
/*---------------------------------------------------------*/
function mbt_do_book_excerpt_content() {
	mbt_include_template("excerpt-book/content.php");
}
function mbt_do_before_book_excerpt() {
	mbt_include_template("excerpt-book/before.php");
}
function mbt_do_after_book_excerpt() {
	mbt_include_template("excerpt-book/after.php");
}
function mbt_do_book_excerpt_images() {
	mbt_include_template("excerpt-book/images.php");
}
function mbt_do_book_excerpt_title() {
	mbt_include_template("excerpt-book/title.php");
}
function mbt_do_book_excerpt_price() {
	mbt_include_template("excerpt-book/price.php");
}
function mbt_do_book_excerpt_meta() {
	mbt_include_template("excerpt-book/meta.php");
}
function mbt_do_book_excerpt_blurb() {
	mbt_include_template("excerpt-book/blurb.php");
}
function mbt_do_book_excerpt_buybuttons() {
	mbt_include_template("excerpt-book/buybuttons.php");
}



/*---------------------------------------------------------*/
/* Book Archive Functions                                  */
/*---------------------------------------------------------*/

function mbt_get_book_archive_image() {
	$query_obj = get_queried_object();
	if(empty($query_obj) or empty($query_obj->taxonomy)) { return ''; }
	$img = mbt_get_taxonomy_image($query_obj->taxonomy, $query_obj->term_id);
	return apply_filters('mbt_get_book_archive_image', empty($img) ? '' : '<img class="mbt-book-archive-image" src="'.$img.'">');
}
function mbt_the_book_archive_image() {
	echo(mbt_get_book_archive_image());
}



function mbt_get_book_archive_title($before = '', $after = '') {
	$output = '';

	if(is_tax('mbt_author')) {
		$output = __('Author', 'mybooktable').': '.get_queried_object()->name;
	} else if(is_tax('mbt_genre')) {
		$output = __('Genre', 'mybooktable').': '.get_queried_object()->name;
	} else if(is_tax('mbt_series')) {
		$output = __('Series', 'mybooktable').': '.get_queried_object()->name;
	} else if(is_tax('mbt_tag')) {
		$output = __('Tag', 'mybooktable').': '.get_queried_object()->name;
	} else if(mbt_is_booktable_page()) {
		$booktable_page = get_post(mbt_get_setting('booktable_page'));
		$output = $booktable_page->post_title;
	} else if(is_post_type_archive('mbt_book')) {
		if(mbt_get_setting('booktable_page') and ($booktable_page = get_post(mbt_get_setting('booktable_page')))) {
			$output = $booktable_page->post_title;
		} else {
			$output = mbt_get_product_name();
		}
	}

	return apply_filters('mbt_get_book_archive_title', empty($output) ? '' : $before.$output.$after, $before, $after);
}
function mbt_the_book_archive_title($before = '', $after = '') {
	echo(mbt_get_book_archive_title($before, $after));
}



function mbt_get_book_archive_description($before = '', $after = '') {
	$output = '';

	if(is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') or is_tax('mbt_tag')) {
		$output = do_shortcode(apply_filters('term_description', get_queried_object()->description));
	} else if(mbt_is_booktable_page()) {
		$booktable_page = get_post(mbt_get_setting('booktable_page'));
		if(function_exists('st_remove_st_add_link')) { st_remove_st_add_link(''); }
		$output = apply_filters('the_content', $booktable_page->post_content);
	} else if(is_post_type_archive('mbt_book')) {
		if(mbt_get_setting('booktable_page') and ($booktable_page = get_post(mbt_get_setting('booktable_page')))) {
			if(function_exists('st_remove_st_add_link')) { st_remove_st_add_link(''); }
			$output = apply_filters('the_content', $booktable_page->post_content);
		}
	}

	return apply_filters('mbt_get_book_archive_description', empty($output) ? '' : $before.$output.$after, $before, $after);
}
function mbt_the_book_archive_description($before = '', $after = '') {
	echo(mbt_get_book_archive_description($before, $after));
}



function mbt_get_book_archive_pagination() {
	global $wp_query;

	$posts_per_page = intval($wp_query->get('posts_per_page'));
	$paged = max(1, absint($wp_query->get('paged')));
	$total_pages = max(1, absint($wp_query->max_num_pages));
	if($total_pages < 2) { return; }

	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = max(1, $paged - $half_page_start);

	$end_page = $paged + $half_page_end;

	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}

	if($end_page > $total_pages) {
		$start_page = max(1, $total_pages - $pages_to_show_minus_1);
		$end_page = $total_pages;
	}

	$prev_text = apply_filters('mbt_book_archive_pagination_previous', '&larr; '.__('Back', 'mybooktable'));
	$next_text = apply_filters('mbt_book_archive_pagination_next', __('More Books', 'mybooktable').' &rarr;');

	$output = '<nav class="mbt-book-archive-pagination">';

	if($paged > 1) { $output .= '<a href="'.get_pagenum_link($paged - 1).'" class="mbt-book-archive-pagination-previous">'.$prev_text.'</a>'; }
	if($start_page >= 2 && $pages_to_show < $total_pages) { $output .= '<span class="mbt-book-archive-pagination-delimiter">'.apply_filters('mbt_book_archive_pagination_delimiter', '&hellip;').'</span>'; }

	foreach(range($start_page, $end_page) as $i) {
		if($i == $paged) {
			$output .= '<span class="mbt-book-archive-pagination-page current">'.apply_filters('mbt_book_archive_pagination_page', strval($i)).'</span>';
		} else {
			$output .= '<a href="'.get_pagenum_link($i).'" class="mbt-book-archive-pagination-page">'.apply_filters('mbt_book_archive_pagination_page', strval($i)).'</a>';
		}
	}

	if($end_page < $total_pages) { $output .= '<span class="mbt-book-archive-pagination-delimiter">'.apply_filters('mbt_book_archive_pagination_delimiter', '&hellip;').'</span>'; }
	if($paged < $total_pages) { $output .= '<a href="'.get_pagenum_link($paged + 1).'" class="mbt-book-archive-pagination-next">'.$next_text.'</a>'; }

	$output .= "</nav>";

	return apply_filters('mbt_get_book_archive_pagination', $output);
}
function mbt_the_book_archive_pagination() {
	echo(mbt_get_book_archive_pagination());
}



/*---------------------------------------------------------*/
/* General Book Functions                                  */
/*---------------------------------------------------------*/

function mbt_do_book_content_sections($display_mode) {
	$sections = mbt_get_sorted_content_sections($display_mode);
	echo('<div class="mbt-book-sections">');
	foreach($sections as $section) {
		if(!empty($section['action'])) {
			do_action($section['action']);
		} else if(!empty($section['function'])) {
			call_user_func($section['function']);
		}
	}
	echo('</div>');
}



// Book Class
function mbt_get_book_class($post_id) {
	$context = mbt_has_template_context('excerpt') ? 'excerpt' : (mbt_has_template_context('single') ? 'single' : '');
	$display_mode = $context == 'single' ? mbt_get_template_display_mode() : '';
	return apply_filters('mbt_get_book_class', 'mbt-book'.(empty($context) ? '' : ' mbt-book-'.$context).(empty($display_mode) ? '' : ' mbt-display-mode-'.$display_mode), $post_id);
}
function mbt_the_book_class() {
	global $post;
	echo(mbt_get_book_class($post->ID));
}



// Book Image
function mbt_get_placeholder_image_src() {
	return apply_filters('mbt_get_placeholder_image_src', array(plugins_url('images/book-placeholder.jpg', dirname(__FILE__)), 400, 472));
}
function mbt_get_large_placeholder_image_src() {
	return apply_filters('mbt_get_large_placeholder_image_src', array(plugins_url('images/book-placeholder-large.jpg', dirname(__FILE__)), 1000, 1178));
}
function mbt_get_book_image_src($post_id) {
	//prevent Jetpack Photon from breaking image width/height by disabling their image downsize
	add_filter('jetpack_photon_override_image_downsize', '__return_true');
	$image = wp_get_attachment_image_src(get_post_meta($post_id, 'mbt_book_image_id', true), 'mbt_book_image');
	remove_filter('jetpack_photon_override_image_downsize', '__return_true');
	return apply_filters('mbt_get_book_image_src', $image ? $image : mbt_get_placeholder_image_src(), $post_id);
}
function mbt_get_book_image_srcset($post_id) {
	//prevent Jetpack Photon from breaking image width/height by disabling their image downsize
	add_filter('jetpack_photon_override_image_downsize', '__return_true');
	$srcset = function_exists('wp_get_attachment_image_srcset') ? wp_get_attachment_image_srcset(get_post_meta($post_id, 'mbt_book_image_id', true), 'mbt_book_image') : '';
	if(!$srcset) {
		list($src, $width, $height) = mbt_get_book_image_src($post_id);
		$srcset = $src ? $src.' '.$width.'w' : '';

		//build extra srcset for placeholder image
		$placeholder = mbt_get_placeholder_image_src();
		if($src == $placeholder[0]) {
			$placeholder_large = mbt_get_large_placeholder_image_src();
			$srcset = $placeholder[0].' '.$placeholder[1].'w, '.$placeholder_large[0].' '.$placeholder_large[1].'w';
		}
	}
	remove_filter('jetpack_photon_override_image_downsize', '__return_true');
	return apply_filters('mbt_get_book_image_srcset', $srcset, $post_id);
}
function mbt_get_book_image($post_id, $attrs = '') {
	list($src, $width, $height) = mbt_get_book_image_src($post_id);
	$srcset = mbt_get_book_image_srcset($post_id);

	$image_size = mbt_get_setting('image_size');
	if($image_size == 'small') { $sizes = '15vw'; }
	else if($image_size == 'large') { $sizes = '35vw'; }
	else { $sizes = '25vw'; }

	$attrs = wp_parse_args($attrs, array('alt' => wp_strip_all_tags(get_the_title($post_id)), 'class' => '', 'sizes' => $sizes));
	$attrs['class'] .= ' mbt-book-image';
	$attrs['src'] = esc_url($src);
	$attrs['srcset'] = esc_attr($srcset);

	$attributes = array();
	foreach($attrs as $attr => $value) {
		$attributes[] = $attr.'="'.$value.'"';
	}

	return apply_filters('mbt_get_book_image', '<img itemprop="image" '.implode($attributes, ' ').'>', $post_id, $attrs);
}
function mbt_the_book_image($attrs = '') {
	global $post;
	echo(mbt_get_book_image($post->ID, $attrs));
}
function mbt_get_book_images($post_id, $include_link = false) {
	$image_size_class = '';
	$image_size = mbt_get_setting('image_size');
	if($image_size == 'small') { $image_size_class = 'mbt-book-images-small'; }
	else if($image_size == 'large') { $image_size_class = 'mbt-book-images-large'; }

	$output = '';
	$output .= '<div class="mbt-book-images '.$image_size_class.'">';
	$output .= $include_link ? '<a href="'.get_permalink($post_id).'">'.mbt_get_book_image($post_id).'</a>' : mbt_get_book_image($post_id);
	$output .= '</div>';

	return apply_filters('mbt_get_book_images', $output, $post_id, $include_link);
}
function mbt_the_book_images($include_link = false) {
	global $post;
	echo(mbt_get_book_images($post->ID, $include_link));
}



// Book Price Display
function mbt_get_book_price($post_id) {
	$price = get_post_meta($post_id, 'mbt_price', true);
	if(preg_match("/^[0-9.]+$/", $price)) { $price =  "$".number_format((double)$price, 2); }

	$sale_price = get_post_meta($post_id, 'mbt_sale_price', true);
	if(preg_match("/^[0-9.]+$/", $sale_price)) { $sale_price =  "$".number_format((double)$sale_price, 2); }

	$ebook_price = get_post_meta($post_id, 'mbt_ebook_price', true);
	if(preg_match("/^[0-9.]+$/", $ebook_price)) { $ebook_price =  "$".number_format((double)$ebook_price, 2); }

	$audiobook_price = get_post_meta($post_id, 'mbt_audiobook_price', true);
	if(preg_match("/^[0-9.]+$/", $audiobook_price)) { $audiobook_price =  "$".number_format((double)$audiobook_price, 2); }

	$output = '';
	if(!empty($sale_price) and !empty($price)) {
		$output  = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" class="mbt-old-price">'.$price.'</span><link itemprop="availability" href="http://schema.org/Discontinued"/></span>';
		$output .= '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" class="mbt-new-price">'.$sale_price.'</span><link itemprop="availability" href="http://schema.org/InStock"/></span>';
	} else if(!empty($price)) {
		$output  = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price">'.$price.'</span><link itemprop="availability" href="http://schema.org/InStock"/></span>';
	}

	if(!empty($ebook_price)) {
		$output .= '<span class="mbt-alt-price"><span class="mbt-price-title">'.__('eBook', 'mybooktable').':</span> <span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price">'.$ebook_price.'</span><link itemprop="availability" href="http://schema.org/InStock"/></span></span>';
	}
	if(!empty($audiobook_price)) {
		$output .= '<span class="mbt-alt-price"><span class="mbt-price-title">'.__('Audiobook', 'mybooktable').':</span> <span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price">'.$audiobook_price.'</span><link itemprop="availability" href="http://schema.org/InStock"/></span></span>';
	}

	if(!empty($output)) { $output = '<div class="mbt-book-price">'.$output.'</div>'; }

	return apply_filters('mbt_get_book_price', $output, $post_id);
}
function mbt_the_book_price() {
	global $post;
	echo(mbt_get_book_price($post->ID));
}



// Book Samples
function mbt_get_book_sample($post_id) {
	$output = '';
	$sample_chapter = get_post_meta($post_id, 'mbt_sample_url', true);
	$audio_sample = get_post_meta($post_id, 'mbt_sample_audio', true);
	$audio_type = wp_check_filetype($audio_sample, wp_get_mime_types());
	$output .= empty($sample_chapter) ? '' : '<div class="mbt-book-sample mbt-book-sample-chapter"><a target="_blank" href="'.$sample_chapter.'">'.__('View Book Sample', 'mybooktable').'</a></div>';
	$output .= empty($audio_sample) ? '' : '<div class="mbt-book-sample mbt-book-sample-audio"><audio controls><source src="'.$audio_sample.'" type="'.$audio_type['type'].'">Your browser does not support the audio element.</audio></div>';
	return apply_filters('mbt_get_book_sample', $output, $post_id);
}
function mbt_the_book_sample() {
	global $post;
	echo(mbt_get_book_sample($post->ID));
}



// Video Sample
function mbt_get_book_video_sample($post_id) {
	global $wp_embed;
	$output = '';
	$url = get_post_meta($post_id, 'mbt_sample_video', true);
	if(!empty($url)) { $output .= '<div class="mbt-book-video-sample">'.$wp_embed->shortcode(array('width'=>500, 'height'=>500), $url).'<div style="clear:both;"></div></div>'; }
	return apply_filters('mbt_get_book_video_sample', $output, $post_id);
}
function mbt_the_book_video_sample() {
	global $post;
	echo(mbt_get_book_video_sample($post->ID));
}



// Book Buy Buttons
function mbt_get_buybuttons($post_id, $excerpt=false, $force_shadowbox=null) {
	$output = '';
	$stores = mbt_get_stores();

	$using_shadowbox = (((($excerpt and mbt_get_setting('buybutton_shadowbox') == 'listings') or mbt_get_setting('buybutton_shadowbox') == 'all') and $force_shadowbox !== false) or $force_shadowbox === true);

	$buybuttons = mbt_query_buybuttons($post_id, array('display' => 'button'));
	if(!empty($buybuttons)) {
		foreach($buybuttons as $buybutton) {
			if(empty($stores[$buybutton['store']])) { continue; }
			$output .= mbt_format_buybutton($buybutton, $stores[$buybutton['store']]);
		}
	}

	if(!$excerpt or $using_shadowbox) {
		$textbuybuttons_output = '';
		$textbuybuttons = mbt_query_buybuttons($post_id, array('display' => 'text'));
		if(!empty($textbuybuttons)) {
			$textbuybuttons_output .= '<div class="mbt-book-buybuttons-textonly">';
			$textbuybuttons_output .= '<h3>'.__('Other book sellers', 'mybooktable').':</h3>';
			$textbuybuttons_output .= '<ul>';
			foreach($textbuybuttons as $buybutton) {
				if(empty($stores[$buybutton['store']])) { continue; }
				$textbuybuttons_output .= mbt_format_buybutton($buybutton, $stores[$buybutton['store']]);
			}
			$textbuybuttons_output .= '</ul>';
			$textbuybuttons_output .= '</div>';
		}
		$output .= apply_filters('mbt_textbuybuttons_output', $textbuybuttons_output);
	}

	$output = apply_filters('mbt_buybuttons_output', $output);

	if(!empty($output) and $using_shadowbox) {
		$book_button_size = mbt_get_setting('book_button_size');
		if($book_button_size == 'small') { $buybuttons_width = 2+150*2; }
		else if($book_button_size == 'medium') { $buybuttons_width = 2+180*2; }
		else { $buybuttons_width = 2+211*2; }

		$shadowbox_content  = apply_filters('mbt_buybuttons_shadowbox_title', '<div class="mbt-shadowbox-title">'.__('Buy This Book Online', 'mybooktable').'</div>');
		$shadowbox_content .= apply_filters('mbt_buybuttons_shadowbox_buybuttons', '<div class="'.mbt_get_book_class('shadowbox').'"><div class="mbt-book-buybuttons" style="width:'.$buybuttons_width.'px">'.$output.'</div></div>', $output, $buybuttons_width);
		$shadowbox_content .= apply_filters('mbt_buybuttons_shadowbox_book_image', mbt_get_book_image($post_id));
		$shadowbox_content .= '<div style="clear:both"></div>';
		if(mbt_get_setting('show_find_bookstore_buybuttons_shadowbox')) { $shadowbox_content .= apply_filters('mbt_buybuttons_shadowbox_find_bookstore', mbt_get_find_bookstore_form($post_id)); }
		$shadowbox_content = apply_filters('mbt_buybuttons_shadowbox_content', $shadowbox_content);

		$shadowbox_output  = '<div class="mbt-shadowbox-hidden" style="display:none"><div class="mbt-shadowbox mbt-buybuttons-shadowbox" id="mbt_buybutton_shadowbox_'.$post_id.'">';
		$shadowbox_output .= $shadowbox_content;
		$shadowbox_output .= '</div></div>';

		$shadowbox_button_output  = '<div class="mbt-book-buybutton">';
		$shadowbox_button_output .= '	<div data-href="#mbt_buybutton_shadowbox_'.$post_id.'" class="mbt-shadowbox-buybutton mbt-shadowbox-inline">';
		$shadowbox_button_output .= '		<img src="'.mbt_image_url('shadowbox_button.png').'" border="0" alt="'.__('Buy now!', 'mybooktable').'"/>';
		$shadowbox_button_output .= '	</div>';
		$shadowbox_button_output .= '</div>';
		$shadowbox_button_output = apply_filters('mbt_format_buybutton', $shadowbox_button_output, array('display'=>'button', 'store'=>'shadowbox', 'url'=>''), array('name' => 'Shadow Box'));

		$output = $shadowbox_output.$shadowbox_button_output;
	}

	if(!empty($output)) { $output = '<div class="mbt-book-buybuttons">'.$output.'<div style="clear:both;"></div></div>'; }

	return apply_filters('mbt_get_buybuttons', $output, $post_id, $excerpt, $force_shadowbox);
}
function mbt_the_buybuttons($excerpt=false, $force_shadowbox=null) {
	global $post;
	echo(mbt_get_buybuttons($post->ID, $excerpt, $force_shadowbox));
}



// Book Blurb
function mbt_get_book_blurb($post_id, $read_more = false) {
	$post = get_post($post_id);
	$output = do_shortcode($post->post_excerpt);
	if($read_more) { $output .= apply_filters('mbt_read_more', ' <a href="'.get_permalink($post_id).'" class="mbt-read-more">'.apply_filters('mbt_read_more_text',__('More info', 'mybooktable').' →' ).'</a>'); }
	return apply_filters('mbt_get_book_blurb', $output, $post_id, $read_more);
}
function mbt_the_book_blurb($read_more = false) {
	global $post;
	echo(mbt_get_book_blurb($post->ID, $read_more));
}



// General Book Metadata Display
function mbt_get_book_metadata($post_id, $field_id, $field_title) {
	$value = get_post_meta($post_id, $field_id, true);
	return empty($value) ? '' : '<span class="mbt-meta-item mbt-meta-length"><span class="mbt-meta-title">'.$field_title.':</span> '.$value.'</span><br>';
}
function mbt_the_book_metadata($field_id, $field_title) {
	global $post;
	echo(mbt_get_book_metadata($post->ID, $field_id, $field_title));
}



// Book Publisher
function mbt_get_book_publisher($post_id) {
	$publisher_name = get_post_meta($post_id, 'mbt_publisher_name', true);
	$publisher_url = get_post_meta($post_id, 'mbt_publisher_url', true);
	if(empty($publisher_name)) { return ''; }
	if(empty($publisher_url)) {
		$publisher_string = '<span class="mbt-publisher">'.$publisher_name.'</span>';
	} else {
		$publisher_string = '<a href="'.$publisher_url.'" target="_blank" rel="nofollow" class="mbt-publisher">'.$publisher_name.'</a>';
	}
	$output = '<span class="mbt-meta-item mbt-meta-publisher"><span class="mbt-meta-title">'.__('Publisher', 'mybooktable').':</span> '.$publisher_string.'</span><br>';
	return apply_filters('mbt_get_book_publisher', $output, $post_id);
}
function mbt_the_book_publisher() {
	global $post;
	echo(mbt_get_book_publisher($post->ID));
}



function mbt_get_book_unique_id($post_id) {
	$output = '';
	if(get_post_meta($post_id, 'mbt_show_unique_id', true) !== 'no') {
		$unique_id = get_post_meta($post_id, 'mbt_unique_id_asin', true);
		$output .= empty($unique_id) ? '' : '<span class="mbt-meta-item mbt-meta-asin"><span class="mbt-meta-title">ASIN:</span> <span itemprop="asin">'.$unique_id.'</span></span><br>';
		$unique_id = get_post_meta($post_id, 'mbt_unique_id_isbn', true);
		$output .=  empty($unique_id) ? '' : '<span class="mbt-meta-item mbt-meta-isbn"><span class="mbt-meta-title">ISBN:</span> <span itemprop="isbn">'.$unique_id.'</span></span><br>';
	}
	return apply_filters('mbt_get_book_unique_id', $output, $post_id);
}
function mbt_the_book_unique_id() {
	global $post;
	echo(mbt_get_book_unique_id($post->ID));
}



// Taxonomy Lists
function mbt_get_book_series($post_id) {
	$series = NULL;

	$terms = get_the_terms($post_id, 'mbt_series');
	if(!empty($terms) and !is_wp_error($terms)) {
		foreach($terms as $term) {
			if(empty($series) or $series->term_id == $term->parent) { $series = $term; }
		}
	}

	return $series;
}
function mbt_get_book_series_list($post_id) {
	$output = '';
	$series = mbt_get_book_series($post_id);

	while(!empty($series) and !is_wp_error($series)) {
		$output = '<a itemprop="keywords" href="'.esc_url(get_term_link($series, 'mbt_series')).'">'.$series->name.'</a>'.(empty($output) ? '' : ', '.$output);
		$series = get_term_by('id', $series->parent, 'mbt_series');
	}

	if(!empty($output)) {
		$post = get_post($post_id);
		$series_order = get_post_meta($post->ID, 'mbt_series_order', true);
		$output = '<span class="mbt-meta-item mbt-meta-series"><span class="mbt-meta-title">'.__('Series', 'mybooktable').':</span> '.$output.((!is_string($series_order) or strlen($series_order) < 1) ? '' : ', Book '.$series_order).'</span><br>';
	}

	return apply_filters('mbt_get_book_series_list', $output, $post_id);
}
function mbt_get_the_term_list($post_id, $tax, $name, $name_plural, $type) {
	$terms = get_the_terms($post_id, $tax);
	if(is_wp_error($terms) or empty($terms)){ return ''; }

	if($tax == 'mbt_author') {
		$sortfunc = function($a, $b) {
			$a = mbt_get_author_priority($a->term_id);
			$b = mbt_get_author_priority($b->term_id);
			return ($a > $b) ? -1 : (($a < $b) ? 1 : 0);
		};
		usort($terms, $sortfunc);
	}

	foreach($terms as $term) {
		$link = get_term_link($term, $tax);
		$term_links[] = '<a itemprop="'.$type.'" href="'.esc_url($link).'">'.$term->name.'</a>';
	}

	return '<span class="mbt-meta-item mbt-meta-'.$tax.'"><span class="mbt-meta-title">'.(count($terms) > 1 ? $name_plural : $name).':</span> '.join(', ', $term_links).'</span><br>';
}
function mbt_the_book_series_list() {
	global $post;
	echo(mbt_get_book_series_list($post->ID));
}
function mbt_get_book_authors_list($post_id) {
	return apply_filters('mbt_get_book_authors_list', mbt_get_the_term_list($post_id, 'mbt_author', __('Author', 'mybooktable'), __('Authors', 'mybooktable'), 'author'), $post_id);
}
function mbt_the_book_authors_list() {
	global $post;
	echo(mbt_get_book_authors_list($post->ID));
}
function mbt_get_book_genres_list($post_id) {
	return apply_filters('mbt_get_book_genres_list', mbt_get_the_term_list($post_id, 'mbt_genre', __('Genre', 'mybooktable'), __('Genres', 'mybooktable'), 'genre'), $post_id);
}
function mbt_the_book_genres_list() {
	global $post;
	echo(mbt_get_book_genres_list($post->ID));
}
function mbt_get_book_tags_list($post_id) {
	return apply_filters('mbt_get_book_tags_list', mbt_get_the_term_list($post_id, 'mbt_tag', __('Tag', 'mybooktable'), __('Tags', 'mybooktable'), 'tag'), $post_id);
}
function mbt_the_book_tags_list() {
	global $post;
	echo(mbt_get_book_tags_list($post->ID));
}



// DOMC Notice
function mbt_the_domc_notice() {
	echo('<div class="mbt-affiliate-disclaimer">'.mbt_get_setting('domc_notice_text').'</div>');
}



// Series Books Display
function mbt_get_book_series_books($post_id) {
	$output = '';
	$series = mbt_get_book_series($post_id);
	if(!empty($series)) {
		$series_books = new WP_Query(array('mbt_series' => $series->slug, 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'mbt_series_order', 'post__not_in' => array($post_id), 'posts_per_page' => -1));
		if(!empty($series_books->posts)) {
			$output .= '<div class="mbt-book-series">';
			foreach($series_books->posts as $series_book) {
				list($src, $width, $height) = mbt_get_book_image_src($series_book->ID);
				$output .= '<div class="mbt-series-book">';
				$output .= '<div class="mbt-series-book-images"><a href="'.get_permalink($series_book->ID).'"><img src="'.$src.'"></a></div>';
				$output .= '<div class="mbt-series-book-title"><a href="'.get_permalink($series_book->ID).'">'.$series_book->post_title.'</a></div>';
				$output .= '</div>';
			}
			$output .= '<div style="clear:both"></div>';
			$output .= '</div>';
		}
	}
	return apply_filters('mbt_get_book_series_books', $output, $post_id);
}
function mbt_the_book_series_books() {
	global $post;
	echo(mbt_get_book_series_books($post->ID));
}



// Kindle Instant Preview
function mbt_get_kindle_instant_preview($post_id) {
	$show_instant_preview = get_post_meta($post_id, 'mbt_show_instant_preview', true);
	if($show_instant_preview === "no") { return ''; }

	$output = '';
	$asin = get_post_meta($post_id, 'mbt_unique_id_asin', true);
	if(!empty($asin)) {
		$url = 'https://read.amazon.com/kp/card?asin='.$asin.'&preview=inline&linkCode=kpe&ref_=cm_sw_r_kb_dp_Ol2Ywb1Y4HHHK&tag=ammbt-20';
		$output = '<iframe class="mbt_kindle_instant_preview" src="'.$url.'" frameborder="0"></iframe>';
	}
	return apply_filters('mbt_get_kindle_instant_preview', $output, $post_id);
}
function mbt_the_kindle_instant_preview() {
	global $post;
	echo(mbt_get_kindle_instant_preview($post->ID));
}



// Star Rating
function mbt_get_book_star_rating($post_id) {
	$output = '';
	$star_rating = intval(get_post_meta($post_id, 'mbt_star_rating', true));
	if($star_rating > 0) {
		$output .= '<span class="mbt-meta-item mbt-meta-star-rating"><span class="mbt-meta-title">'.__('Rating', 'mybooktable').':</span> <div class="mbt-star-rating">';
		for($i=0; $i < 5; $i++) {
			$output .= '<span class="mbt-star '.($i < $star_rating ? 'mbt-star-filled' : '').'"></span>';
		}
		$output .= '</div></span><br>';
	}
	return apply_filters('mbt_get_book_star_rating', $output, $post_id);
}
function mbt_the_book_star_rating() {
	global $post;
	echo(mbt_get_book_star_rating($post->ID));
}



// Find Bookstore
function mbt_get_find_bookstore_form($post_id) {
	$key = mbt_get_setting('google_api_key');
	if(empty($key)) { return ''; }
	wp_enqueue_script('mbt-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.$key.'&v=3.exp', array(), MBT_VERSION, true);
	$output = '';
	$output .= '<div class="mbt-find-bookstore">';
	$output .= '<form class="mbt-find-bookstore-form" action="//maps.google.com/maps">';
	$output .= '	<input type="text" class="mbt-city" placeholder="'.__('City', 'mybooktable').'" name="city" size="20">';
	$output .= '	<input type="text" class="mbt-zip" placeholder="'.__('Zip', 'mybooktable').'" name="zip" size="5" maxlength="5">';
	$output .= '	<input type="submit" name="submit" value="'.__('Find Store', 'mybooktable').'">';
	$output .= '</form>';
	$output .= '<div style="clear:both"></div>';
	$output .= '</div>';
	return apply_filters('mbt_get_find_bookstore_form', $output, $post_id);
}
function mbt_the_find_bookstore_form() {
	global $post;
	echo(mbt_get_find_bookstore_form($post->ID));
}



// Reviews
function mbt_get_reviews($post_id) {
	$output = '';
	$reviews_types = mbt_get_reviews_types();
	$current_reviews = mbt_get_setting('reviews_type');
	if(!empty($reviews_types[$current_reviews]['callback'])) {
		$unique_id = get_post_meta($post_id, 'mbt_unique_id_asin', true).get_post_meta($post_id, 'mbt_unique_id_isbn', true);
		$cache_id = 'mbt_'.$current_reviews.'_reviews_'.$post_id.'_'.$unique_id;
		if(false === ($output = get_transient($cache_id))) {
			$output = call_user_func_array($reviews_types[$current_reviews]['callback'], array());
			set_transient($cache_id, $output, HOUR_IN_SECONDS);
		}
	}
	return apply_filters('mbt_get_reviews', $output, $post_id);
}
function mbt_the_reviews() {
	global $post;
	echo(mbt_get_reviews($post->ID));
}



// Endorsements
function mbt_get_book_endorsements($post_id) {
	$endorsements = get_post_meta($post_id, 'mbt_endorsements', true);
	$output = '';
	if(!empty($endorsements)) {
		$output .= '<div class="mbt-book-endorsements">';
		foreach($endorsements as $endorsement) {
			if(empty($endorsement['content'])) { continue; }
			$name_url = $endorsement['name_url'];
			if(!empty($name_url) and substr($name_url, 0, 4) !== 'http') { $name_url = 'http://'.$name_url; }
			$output .= '<div class="mbt-endorsement">';
			$image = wp_get_attachment_image_src($endorsement['image_id'], 'mbt_endorsement_image');
			$srcset = function_exists('wp_get_attachment_image_srcset') ? wp_get_attachment_image_srcset($endorsement['image_id'], 'mbt_endorsement_image') : '';
			if(!empty($image)) { $output .= '<div class="mbt-endorsement-image"><img src="'.$image[0].'"'.(empty($srcset) ? '' : ' srcset="'.$srcset.'"').'></div>'; }
			$output .= '	<div class="mbt-endorsement-content">';
			$output .=			$endorsement['content'];
			$output .= '		<div class="mbt-endorsement-name">&ndash; '.(empty($name_url) ? '' : '<a href="'.$name_url.'" target="_blank">').$endorsement['name'].(empty($name_url) ? '' : '</a>').'</div>';
			$output .= '	</div>';
			$output .= '	<div style="clear:both"></div>';
			$output .= '</div>';
		}
		$output .= '<div style="clear:both"></div>';
		$output .= '</div>';
	}
	return apply_filters('mbt_get_book_endorsements', $output, $post_id);
}
function mbt_the_book_endorsements() {
	global $post;
	echo(mbt_get_book_endorsements($post->ID));
}



// About Author
function mbt_get_book_about_author($post_id) {
	$output = '';

	$terms = get_the_terms($post_id, 'mbt_author');
	if(is_wp_error($terms) or empty($terms)) { return ''; }

	//sort by priority
	$sortfunc = function($a, $b) {
		$a = mbt_get_author_priority($a->term_id);
		$b = mbt_get_author_priority($b->term_id);
		return ($a > $b) ? -1 : (($a < $b) ? 1 : 0);
	};
	usort($terms, $sortfunc);

	//select first author with description and highest priority
	$author = reset($terms);
	foreach($terms as $term) {
		if(mbt_get_author_priority($term->term_id) < mbt_get_author_priority($author->term_id)) { break; }
		if(!empty($term->description)) { $author = $term; break; }
	}
	if(empty($author->description)) { return ''; }

	$image = mbt_get_taxonomy_image('mbt_author', $author->term_id);

	$output .= '<div class="mbt-book-about-author">';
	if(!empty($image)) {
	$output .= '	<div class="mbt-book-about-author-image"><img src="'.$image.'"></div>';
	$output .= '	<div class="mbt-book-about-author-right">';
	}
	$output .= '		<div class="mbt-book-about-author-name">'.$author->name.'</div>';
	$output .= '		<div class="mbt-book-about-author-desc">'.wpautop($author->description).'</div>';
	if(!empty($image)) {
	$output .= '	</div>';
	$output .= '	<div style="clear:both"></div>';
	}
	$output .= '</div>';

	return apply_filters('mbt_get_book_about_author', $output, $post_id);
}
function mbt_the_book_about_author() {
	global $post;
	echo(mbt_get_book_about_author($post->ID));
}



// Book Teaser
function mbt_get_book_teaser($post_id) {
	$output = '';
	$teaser = get_post_meta($post_id, 'mbt_book_teaser', true);
	if($teaser) {
		$output .= '<h2 class="mbt-book-teaser">'.$teaser.'</h2>';
	}
	return apply_filters('mbt_get_book_teaser', $output, $post_id);
}
function mbt_the_book_teaser() {
	global $post;
	echo(mbt_get_book_teaser($post->ID));
}



// Call to Action Button
function mbt_get_book_cta_button($post_id, $button_text='') {
	$output = '';
	$buybuttons = mbt_query_buybuttons($post_id);
	if(!empty($buybuttons)) {
		$text = apply_filters('mbt_book_cta_button_text', empty($button_text) ? __('Order Now!', 'mybooktable') : $button_text);
		$output .= '<a href="#mbt-book-purchase-anchor" class="mbt-book-purchase-button mbt-primary-button">'.$text.'</a>';
	}
	return apply_filters('mbt_get_book_cta_button', $output, $post_id, $button_text);
}
function mbt_the_book_cta_button($button_text='') {
	global $post;
	echo(mbt_get_book_cta_button($post->ID, $button_text));
}



// Book Club Resources
function mbt_get_book_bookclub_resources($post_id) {
	global $wp_embed;

	$links = get_post_meta($post_id, 'mbt_bookclub_links', true);
	$video = get_post_meta($post_id, 'mbt_bookclub_video', true);
	$output = '';
	if(!empty($links) or !empty($video)) {
		$output .= '<div class="mbt-book-bookclub-resources">';
		if(!empty($video)) { $output .= '<div class="mbt-book-bookclub-video">'.$wp_embed->shortcode(array('width'=>400, 'height'=>400), $video).'</div>'; }
		$links_output = '';
		if(!empty($links) and is_array($links)) {
			foreach($links as $link) {
				if(empty($link['url'])) { continue; }
				$links_output .= '<li><a target="_blank" href="'.$link['url'].'">'.(empty($link['text']) ? 'Download Resource' : $link['text']).'</a></li>';
			}
		}
		if(!empty($links_output)) { $output .= '<ul class="mbt-book-bookclub-links">'.$links_output.'</ul>'; }
		$output .= '</div>';
	}
	return apply_filters('mbt_get_book_bookclub_resources', $output, $post_id);
}
function mbt_the_book_bookclub_resources() {
	global $post;
	echo(mbt_get_book_bookclub_resources($post->ID));
}



// Book Overview Image
function mbt_get_book_overview_image($post_id) {
	$output = '';
	$image_id = get_post_meta($post_id, 'mbt_overview_image', true);
	if(empty($image_id)) { $image_id = get_post_meta($post_id, 'mbt_book_image_id', true); }
	$image = wp_get_attachment_image_src($image_id, 'mbt_endorsement_image');
	$srcset = function_exists('wp_get_attachment_image_srcset') ? wp_get_attachment_image_srcset($image_id, 'mbt_endorsement_image') : '';
	if(!empty($image)) { $output .= '<div class="mbt-book-overview-image"><img src="'.$image[0].'"'.(empty($srcset) ? '' : ' srcset="'.$srcset.'"').'></div>'; }
	return apply_filters('mbt_get_book_overview_image', $output, $post_id);
}
function mbt_the_book_overview_image() {
	global $post;
	echo(mbt_get_book_overview_image($post->ID));
}



// Book Preview
function mbt_get_book_preview($post_id) {
	$output = '';
	$output .= mbt_get_kindle_instant_preview($post_id);
	$output .= mbt_get_book_sample($post_id);
	return apply_filters('mbt_get_book_preview', $output, $post_id);
}
function mbt_the_book_preview() {
	global $post;
	echo(mbt_get_book_preview($post->ID));
}



/*---------------------------------------------------------*/
/* Social Media Functions                                  */
/*---------------------------------------------------------*/

// Social Media Badges
function mbt_get_book_socialmedia_badges($post_id) {
	$url = urlencode(get_permalink($post_id));
	$output = '';

	$output .= '<div class="mbt-book-socialmedia-badges">';
	$output .= '<iframe src="https://plusone.google.com/_/+1/fastbutton?url='.$url.'&amp;size=tall&amp;count=true&amp;annotation=bubble" class="mbt-gplusone" style="width: 55px; height: 61px; margin: 0px; border: none; overflow: hidden;" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0" scrolling="no" allowtransparency="true"></iframe>';
	$output .= '<iframe src="https://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout=box_count" class="mbt-fblike" style="width: 50px; height: 61px; margin: 0px; border: none; overflow: hidden;" scrolling="no" frameborder="0" allowtransparency="true"></iframe>';
	$output .= '</div>';

	return apply_filters('mbt_get_book_socialmedia_badges', $output, $post_id);
}
function mbt_the_book_socialmedia_badges() {
	global $post;
	echo(mbt_get_book_socialmedia_badges($post->ID));
}



// Social Media Bar
function mbt_get_book_socialmedia_bar($post_id) {
	$url = urlencode(get_permalink($post_id));
	$output = '';

	$output .= '<div class="mbt-book-socialmedia-bar">';
	if(function_exists('st_makeEntries')) {
		$output .= st_makeEntries();
	} else {
		$output .= '<iframe src="https://plusone.google.com/_/+1/fastbutton?url='.$url.'&amp;size=medium&amp;count=true&amp;annotation=bubble" class="mbt-gplusone" style="width: 75px; height: 20px; margin: 0px; border: none; overflow: hidden;" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0" scrolling="no" allowtransparency="true"></iframe>';
		$output .= '<iframe src="https://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout=button_count" class="mbt-fblike" style="width: 75px; height: 20px; margin: 0px; border: none; overflow: hidden;" scrolling="no" frameborder="0" allowtransparency="true"></iframe>';
		$output .= '<iframe src="https://platform.twitter.com/widgets/tweet_button.html?url='.$url.'&amp;count=horizontal&amp;size=m" class="mbt-twittershare" style="height: 20px; width: 100px; margin: 0px; border: none; overflow: hidden;" allowtransparency="true" frameborder="0" scrolling="no"></iframe>';
	}
	$output .= '</div>';

	return apply_filters('mbt_get_book_socialmedia_bar', $output, $post_id);
}
function mbt_the_book_socialmedia_bar() {
	global $post;
	echo(mbt_get_book_socialmedia_bar($post->ID));
}



// Social Media Buttons
function mbt_get_book_socialmedia_buttons($post_id) {
	$encoded_permalink = urlencode(get_permalink($post_id));
	$output = '';

	$output .= '<div class="mbt-book-socialmedia-buttons">';
	$output .= '	<a href="https://www.facebook.com/sharer/sharer.php?u='.$encoded_permalink.'" target="_blank" class="mbt-book-socialmedia-button mbt-book-socialmedia-facebook">Facebook</a>';
	$output .= '	<a href="https://twitter.com/intent/tweet?text='.urlencode(__('Check out this book!', 'mybooktable')).'&url='.$encoded_permalink.'&original_referer='.$encoded_permalink.'" target="_blank" class="mbt-book-socialmedia-button mbt-book-socialmedia-twitter">Twitter</a>';
	$output .= '	<a href="https://plus.google.com/share?url='.$encoded_permalink.'" target="_blank" class="mbt-book-socialmedia-button mbt-book-socialmedia-googleplus">Google+</a>';
	$output .= '</div>';

	return apply_filters('mbt_get_book_socialmedia_buttons', $output, $post_id);
}
function mbt_the_book_socialmedia_buttons() {
	global $post;
	echo(mbt_get_book_socialmedia_buttons($post->ID));
}



/*---------------------------------------------------------*/
/* Standard Book Sections                                  */
/*---------------------------------------------------------*/

function mbt_do_standard_book_video_sample_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'video-sample', mbt_get_book_video_sample($post->ID)));
}

function mbt_do_standard_book_endorsements_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'endorsements', mbt_get_book_endorsements($post->ID), __('Endorsements', 'mybooktable')));
}

function mbt_do_standard_book_about_author_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'about-author', mbt_get_book_about_author($post->ID), __('About the Author', 'mybooktable')));
}

function mbt_do_standard_book_bookclub_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'bookclub', mbt_get_book_bookclub_resources($post->ID), __('Book Club Resources', 'mybooktable')));
}

function mbt_do_standard_book_purchase_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'purchase', mbt_get_buybuttons($post->ID, false, false), __('Order Now', 'mybooktable')));
}

function mbt_do_standard_book_socialmedia_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'socialmedia', mbt_get_book_socialmedia_buttons($post->ID), __('Share', 'mybooktable')));
}

function mbt_do_standard_book_series_section() {
	global $post;
	$series = mbt_get_book_series($post->ID);
	$title = empty($series) ? '' : sprintf(__('Other %s in "%s"', 'mybooktable'), mbt_get_product_name(), $series->name);
	echo(mbt_render_book_section($post->ID, 'series', mbt_get_book_series_books($post->ID), $title));
}

function mbt_do_standard_book_preview_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'preview', mbt_get_book_preview($post->ID), __('Preview', 'mybooktable')));
}

function mbt_do_standard_kindle_instant_preview_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'kindle-instant-preview', mbt_get_kindle_instant_preview($post->ID), __('Look Inside', 'mybooktable')));
}

function mbt_do_standard_find_bookstore_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'find-bookstore', mbt_get_find_bookstore_form($post->ID), __('Find A Local Bookstore', 'mybooktable')));
}

function mbt_do_standard_reviews_section() {
	global $post;
	echo(mbt_render_book_section($post->ID, 'reviews', mbt_get_reviews($post->ID), __('Reviews', 'mybooktable')));
}
