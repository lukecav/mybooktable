<?php

/*---------------------------------------------------------*/
/* Compatibility Mode Functions                            */
/*---------------------------------------------------------*/

function mbt_compat_init() {
	if(mbt_get_setting('compatibility_mode')) {
		//modify the post query
		add_action('pre_get_posts', 'mbt_compat_pre_get_posts', 30);
		add_action('wp', 'mbt_compat_override_query_posts', -999);

		//override page template
		remove_filter('template_include', 'mbt_load_book_templates');
		add_filter('template_include', 'mbt_compat_load_book_templates');
	}
}
add_action('mbt_init', 'mbt_compat_init', 11);



/*---------------------------------------------------------*/
/* Template Overload Functions                             */
/*---------------------------------------------------------*/

function mbt_compat_custom_page_content($content) {
	global $post;

	if(mbt_has_template_context('compatability') or mbt_has_template_context('shortcode')) { return $content; }

	$context = '';

	if((mbt_is_booktable_page() and $post->ID == mbt_get_setting('booktable_page')) or (mbt_is_archive_query() and $post->ID == -1)) {
		$context = 'archive';
	} else if(is_singular('mbt_book')) {
		$context = 'single';
	}

	if($context) {
		//tweak $wp_current_filter in order to allow jetpack sharing to work
		global $wp_current_filter;
		$content_filter = array_pop($wp_current_filter);

		ob_start();

		mbt_start_template_context('compatability');
		if($context == 'archive') {
			mbt_load_archive_query();
			mbt_start_template_context('archive');
			?> <div id="mbt-container"> <?php
			do_action('mbt_book_archive_content');
			?> </div> <?php
			mbt_end_template_context();
			mbt_unload_archive_query();
		} else if($context == 'single') {
			$display_mode = mbt_get_book_display_mode($post->ID);
			mbt_start_template_context('single');
			mbt_start_template_display_mode($display_mode);
			?> <div id="mbt-container"> <?php
			do_action('mbt_single_book_'.$display_mode.'_content');
			?> </div> <?php
			mbt_end_template_display_mode();
			mbt_end_template_context();
		}
		mbt_end_template_context();

		$content = ob_get_contents();
		ob_end_clean();

		//undo $wp_current_filter tweak
		$wp_current_filter[] = $content_filter;
	}

	return $content;
}

function mbt_compat_load_book_templates($template) {
	global $post;
	if(is_singular('mbt_book') or mbt_is_archive_query()) {
		if(is_singular('mbt_book') and !mbt_book_display_mode_supports(mbt_get_book_display_mode($post->ID), 'embedding')) { return mbt_load_book_templates($template); }
		$template = locate_template('page.php');
		if(empty($template)) { $template = locate_template('index.php'); }
		add_filter('the_content', 'mbt_compat_custom_page_content', 999, 2);
	} else if(mbt_is_booktable_page()) {
		add_filter('the_content', 'mbt_compat_custom_page_content', 999, 2);
	}
	return $template;
}

function mbt_compat_pre_get_posts($query) {
	if(!is_admin() and $query->is_main_query() and ($query->is_post_type_archive('mbt_book') or $query->is_tax('mbt_author') or $query->is_tax('mbt_series') or $query->is_tax('mbt_genre') or $query->is_tax('mbt_tag'))) {
		global $mbt_archive_query;
		if($query->get('mbt_author')) {
			$mbt_archive_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_author' => $query->get('mbt_author'), 'paged' => $query->get('paged'), 'orderby' => 'menu_order', 'posts_per_page' => mbt_get_posts_per_page()));
		} else if($query->get('mbt_series')) {
			$mbt_archive_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_series' => $query->get('mbt_series'), 'paged' => $query->get('paged'), 'orderby' => 'meta_value_num', 'meta_key' => 'mbt_series_order', 'order' => 'ASC', 'posts_per_page' => mbt_get_posts_per_page()));
		} else if($query->get('mbt_genre')) {
			$mbt_archive_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_genre' => $query->get('mbt_genre'), 'paged' => $query->get('paged'), 'orderby' => 'menu_order', 'posts_per_page' => mbt_get_posts_per_page()));
		} else if($query->get('mbt_tag')) {
			$mbt_archive_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_tag' => $query->get('mbt_tag'), 'paged' => $query->get('paged'), 'orderby' => 'menu_order', 'posts_per_page' => mbt_get_posts_per_page()));
		} else {
			$mbt_archive_query = new WP_Query(array('post_type' => 'mbt_book', 'paged' => $query->get('paged'), 'orderby' => 'menu_order', 'posts_per_page' => mbt_get_posts_per_page()));
		}
	}
}

function mbt_compat_override_query_posts() {
	if(mbt_is_archive_query()) {
		//ID must be -1, not 0, or get_post_meta will (inexplicably) return false instead of "", which some themes (such as Divi theme) can't handle. There is no hook in get_post_meta to prevent this.
		$post = new WP_Post((object)array(
			"ID" => -1,
			"post_author" => "1",
			"post_date" => "",
			"post_date_gmt" => "",
			"post_modified" => "",
			"post_modified_gmt" => "",
			"post_content" => "",
			"post_content_filtered" => "",
			"post_excerpt" => "",
			"post_title" => mbt_get_book_archive_title(),
			"post_status" => "publish",
			"comment_status" => "closed",
			"ping_status" => "closed",
			"post_password" => "",
			"post_name" => "",
			"to_ping" => "",
			"pinged" => "",
			"post_parent" => 0,
			"guid" => "",
			"menu_order" => 0,
			"post_type" => "page",
			"post_mime_type" => "",
			"comment_count" => "0",
			"filter" => "raw",
		));

		// This solves the get_post($post->ID) error when ID is -1!
		wp_cache_set(-1, $post, 'posts');

		global $wp_query;
		$wp_query->post = $post;
		$wp_query->posts = array($post);
		$wp_query->post_count = 1;
		$wp_query->is_page = true;
		$wp_query->is_singular = true;
		$wp_query->is_tax = false;
		$wp_query->is_archive = false;
		$wp_query->is_post_type_archive = false;
		$wp_query->queried_object = $post;
		$wp_query->queried_object_id = -1;
		$GLOBALS['post'] = $wp_query->post;
		$GLOBALS['posts'] = &$wp_query->posts;
	}
}

function mbt_is_archive_query() {
	global $mbt_archive_query;
	return !empty($mbt_archive_query);
}

function mbt_load_archive_query() {
	global $wp_query, $posts, $post, $id, $mbt_old_query, $mbt_archive_query;
	if(!empty($mbt_archive_query)) {
		$mbt_old_query = array(
			'wp_query' => $wp_query,
			'posts' => $posts,
			'post' => $post,
			'id' => $id,
		);
		$wp_query = $mbt_archive_query;
		$posts = $mbt_archive_query->posts;
		$post = $mbt_archive_query->post;
		$id = $mbt_archive_query->post->ID;
	}
}

function mbt_unload_archive_query() {
	global $wp_query, $posts, $post, $id, $mbt_old_query;
	if(!empty($mbt_old_query)) {
		$wp_query = $mbt_old_query['wp_query'];
		$posts = $mbt_old_query['posts'];
		$post = $mbt_old_query['post'];
		$id = $mbt_old_query['id'];
	}
}
