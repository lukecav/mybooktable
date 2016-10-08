<?php

/*---------------------------------------------------------*/
/* Custom MyBookTable Importer/Exporter                    */
/*---------------------------------------------------------*/

function mbt_custom_importer_init() {
	add_filter('mbt_importers', 'mbt_add_custom_importer');
	add_action('admin_init', 'mbt_detect_custom_export_download');
	add_filter('mbt_pre_import_book', 'mbt_custom_importer_filter_book', 10, 2);
}
add_action('mbt_init', 'mbt_custom_importer_init');

function mbt_add_custom_importer($importers) {
	$importers['mybooktable'] = array(
		'name' => __('MyBookTable Import/Export', 'mybooktable'),
		'desc' => __('Import or Export your book data for backups or site transfer.', 'mybooktable'),
		'get_book_list' => array(
			'render_import_form' => 'mbt_render_custom_importer_form',
			'parse_import_form' => 'mbt_parse_custom_importer_form',
		),
	);
	return $importers;
}

function mbt_render_custom_importer_form() {
	?>
		<h3 style="margin-bottom: 0px;"><?php _e('Export', 'mybooktable'); ?></h3>
		<div style="margin: 4px 0;">Click the 'Export' button below and save the MyBookTable Backup file to an appropriate location.</div>
		<div style="margin-bottom: 4px;"><input type="checkbox" id="mbt_export_settings"> <span style="font-weight:bold;">Include Settings data</span> (includes potentially sensitive info such as affiliate codes and integration credentials)</div>
		<button class="button button-primary" onclick="window.open(window.location+'&export='+(jQuery('#mbt_export_settings').is(':checked') ? 'books,settings' : 'books')); return false;">Export</button>
		<h3 style="margin-bottom: 0px;margin-top:30px;"><?php _e('Import', 'mybooktable'); ?></h3>
		<div style="margin: 4px 0;">Choose your MyBookTable Backup file with the file picker below then click "Import".</div>
		<input name="mbt_custom_import_file" type="file">
	<?php
}

function mbt_parse_custom_importer_form() {
	$file_text = file_get_contents($_FILES["mbt_custom_import_file"]["tmp_name"]);
	$data = json_decode($file_text, true);

	// load settings
	if(!empty($data['settings'])) {
		global $mbt_settings;
		$mbt_settings = array_merge($mbt_settings, $data['settings']);
		update_option('mbt_settings', $mbt_settings);
	}

	// load taxonomies
	foreach(array('mbt_author' => 'authors', 'mbt_series' => 'series', 'mbt_genre' => 'genres', 'mbt_tag' => 'tags') as $tax_slug => $tax_name) {
		if(empty($data[$tax_name])) { continue; }
		$terms = $data[$tax_name];
		foreach($terms as $key => $term) {
			$term_obj = get_term_by('name', $term['name'], $tax_slug);
			if($term_obj === false) {
				$term_data = wp_insert_term($term['name'], $tax_slug, array(
					'slug' => $term['slug'],
					'description' => $term['description'],
				));
				if(!empty($term['image'])) { mbt_save_taxonomy_image($tax_slug, $term_data['term_id'], $term['image']); }
				if($tax_name == 'authors' and !empty($term['priority'])) { mbt_save_author_priority($term_data['term_id'], $term['priority']); }
				$terms[$key]['imported_id'] = $term_data['term_id'];
			}
		}
		foreach($terms as $term) {
			if(empty($term['imported_id']) or empty($term['parent'])) { continue; }
			$parent = get_term_by('name', $term['parent'], $tax_slug);
			if($parent !== false) {
				wp_update_term($term['imported_id'], $tax_slug, array('parent' => $parent->term_id));
			}
		}
	}

	return $data['books'];
}

function mbt_custom_importer_filter_book($book, $import_type) {
	if($import_type !== 'mybooktable') { return $book; }

	//if a book exists with the EXACT same data, don't import a copy
	$book_query = new WP_Query(array('post_type' => 'mbt_book', 'name' => $book['name']));
	if(!empty($book_query->posts)) {
		$existing_book = $book_query->posts[0];
		$book_matches = true;
		$book_matches = ($book_matches and ($book['title'] === $existing_book->post_title));
		$book_matches = ($book_matches and ($book['content'] === $existing_book->post_content));
		$book_matches = ($book_matches and ($book['excerpt'] === $existing_book->post_excerpt));
		$extract_names = function($term) { return $term->name; };
		$book_matches = ($book_matches and ($book['authors'] === array_map($extract_names, wp_get_object_terms($existing_book->ID, 'mbt_author'))));
		$book_matches = ($book_matches and ($book['series'] === array_map($extract_names, wp_get_object_terms($existing_book->ID, 'mbt_series'))));
		$book_matches = ($book_matches and ($book['genres'] === array_map($extract_names, wp_get_object_terms($existing_book->ID, 'mbt_genre'))));
		$book_matches = ($book_matches and ($book['tags'] === array_map($extract_names, wp_get_object_terms($existing_book->ID, 'mbt_tag'))));
		$book_matches = ($book_matches and ($book['price'] === get_post_meta($existing_book->ID, 'mbt_price', true)));
		$book_matches = ($book_matches and ($book['unique_id_isbn'] === get_post_meta($existing_book->ID, 'mbt_unique_id_isbn', true)));
		$book_matches = ($book_matches and ($book['unique_id_asin'] === get_post_meta($existing_book->ID, 'mbt_unique_id_asin', true)));
		$book_matches = ($book_matches and ($book['buybuttons'] === get_post_meta($existing_book->ID, 'mbt_buybuttons', true)));
		$book_matches = ($book_matches and ($book['publisher_name'] === get_post_meta($existing_book->ID, 'mbt_publisher_name', true)));
		$book_matches = ($book_matches and ($book['publisher_url'] === get_post_meta($existing_book->ID, 'mbt_publisher_url', true)));
		$book_matches = ($book_matches and ($book['publication_year'] === get_post_meta($existing_book->ID, 'mbt_publication_year', true)));
		$book_matches = ($book_matches and ($book['image_filename'] === wp_basename(get_attached_file(get_post_meta($existing_book->ID, 'mbt_book_image_id', true)))));
		$book_matches = ($book_matches and ($book['sample_url'] === get_post_meta($existing_book->ID, 'mbt_sample_url', true)));
		$book_matches = ($book_matches and ($book['book_length'] === get_post_meta($existing_book->ID, 'mbt_book_length', true)));
		$book_matches = ($book_matches and ($book['sale_price'] === get_post_meta($existing_book->ID, 'mbt_sale_price', true)));
		$book_matches = ($book_matches and ($book['show_instant_preview'] === get_post_meta($existing_book->ID, 'mbt_show_instant_preview', true)));
		$book_matches = ($book_matches and ($book['series_order'] === get_post_meta($existing_book->ID, 'mbt_series_order', true)));
		$book_matches = ($book_matches and ($book['display_mode'] === get_post_meta($existing_book->ID, 'mbt_display_mode', true)));
		if($book_matches) { return sprintf(__('Book "%s" already exists', 'mybooktable'), $book['title']); }
	}

	//test if image id matches desired image
	$image_file = get_attached_file($book['image_id']);
	if(empty($image_file) or $book['image_filename'] !== wp_basename($image_file)) {
		$book['image_id'] = null;
	}

	return $book;
}

function mbt_detect_custom_export_download() {
	global $pagenow;
	if(!is_admin() or empty($pagenow) or $pagenow !== 'admin.php') { return; }
	if(empty($_GET['page']) or $_GET['page'] !== 'mbt_import') { return; }
	if(empty($_GET['mbt_import_type']) or $_GET['mbt_import_type'] !== 'mybooktable') { return; }
	if(empty($_GET['export']) or ($_GET['export'] !== 'books' and $_GET['export'] !== 'books,settings')) { return; }
	if(!current_user_can('edit_posts')) { return; }

	header('Content-type: application/json');
	header('Content-Disposition: attachment; filename="mybooktable_export"');

	$data = array('version' => MBT_VERSION);

	// save settings
	if($_GET['export'] === 'books,settings') {
		global $mbt_settings;
		$data['settings'] = $mbt_settings;
		unset($data['settings']['version']);
		unset($data['settings']['author_priorities']);
	}

	// save taxonomies
	foreach(array('mbt_author' => 'authors', 'mbt_series' => 'series', 'mbt_genre' => 'genres', 'mbt_tag' => 'tags') as $tax_slug => $tax_name) {
		$raw_terms = get_terms($tax_slug, array('hide_empty' => false));
		$terms = array();
		foreach($raw_terms as $term) {
			$parent = get_term_by('id', $term->parent, $tax_slug);
			$new_term = array(
				'id' => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
				'description' => $term->description,
				'parent' => empty($parent->name) ? '' : $parent->name,
				'image' => mbt_get_taxonomy_image($tax_slug, $term->term_id),
			);
			if($tax_name == 'authors') { $new_term['priority'] = mbt_get_author_priority($term->term_id); }
			$terms[] = $new_term;
		}
		$data[$tax_name] = $terms;
	}

	// save books
	$books = array();
	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	foreach($books_query->posts as $book) {
		$new_book = array();
		$new_book['id'] = $book->ID;
		$new_book['name'] = $book->post_name;
		$new_book['title'] = $book->post_title;
		$new_book['content'] = $book->post_content;
		$new_book['excerpt'] = $book->post_excerpt;
		$extract_names = function($term) { return $term->name; };
		$new_book['authors'] = array_map($extract_names, wp_get_object_terms($book->ID, 'mbt_author'));
		$new_book['series'] = array_map($extract_names, wp_get_object_terms($book->ID, 'mbt_series'));
		$new_book['genres'] = array_map($extract_names, wp_get_object_terms($book->ID, 'mbt_genre'));
		$new_book['tags'] = array_map($extract_names, wp_get_object_terms($book->ID, 'mbt_tag'));
		$new_book['price'] = get_post_meta($book->ID, 'mbt_price', true);
		$new_book['unique_id_isbn'] = get_post_meta($book->ID, 'mbt_unique_id_isbn', true);
		$new_book['unique_id_asin'] = get_post_meta($book->ID, 'mbt_unique_id_asin', true);
		$new_book['buybuttons'] = get_post_meta($book->ID, 'mbt_buybuttons', true);
		$new_book['publisher_name'] = get_post_meta($book->ID, 'mbt_publisher_name', true);
		$new_book['publisher_url'] = get_post_meta($book->ID, 'mbt_publisher_url', true);
		$new_book['publication_year'] = get_post_meta($book->ID, 'mbt_publication_year', true);
		$new_book['image_id'] = get_post_meta($book->ID, 'mbt_book_image_id', true);
		$new_book['image_filename'] = wp_basename(get_attached_file($new_book['image_id']));
		$new_book['sample_url'] = get_post_meta($book->ID, 'mbt_sample_url', true);
		$new_book['book_length'] = get_post_meta($book->ID, 'mbt_book_length', true);
		$new_book['sale_price'] = get_post_meta($book->ID, 'mbt_sale_price', true);
		$new_book['show_instant_preview'] = get_post_meta($book->ID, 'mbt_show_instant_preview', true);
		$new_book['series_order'] = get_post_meta($book->ID, 'mbt_series_order', true);
		$new_book['display_mode'] = get_post_meta($book->ID, 'mbt_display_mode', true);
		$books[] = $new_book;
	}
	$data['books'] = $books;

	echo(json_encode($data));

	die();
}
