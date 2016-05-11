<?php

function mbt_metaboxes_init() {
	add_action('wp_ajax_mbt_buybuttons_metabox', 'mbt_buybuttons_metabox_ajax');
	add_action('wp_ajax_mbt_metadata_metabox', 'mbt_metadata_metabox_ajax');
	add_action('wp_ajax_mbt_isbn_preview', 'mbt_isbn_preview_ajax');
	add_action('wp_ajax_mbt_asin_preview', 'mbt_asin_preview_ajax');
	add_action('admin_enqueue_scripts', 'mbt_enqueue_metabox_js');

	add_action('save_post', 'mbt_save_metadata_metabox');
	add_action('save_post', 'mbt_save_buybuttons_metabox');
	add_action('save_post', 'mbt_save_series_order_metabox');

	add_action('add_meta_boxes', 'mbt_add_metaboxes', 9);
}
add_action('mbt_init', 'mbt_metaboxes_init');

function mbt_add_metaboxes() {
	add_meta_box('mbt_blurb', __('Book Blurb', 'mybooktable'), 'mbt_book_blurb_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_metadata', __('Book Details', 'mybooktable'), 'mbt_metadata_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_buybuttons', __('Buy Buttons', 'mybooktable'), 'mbt_buybuttons_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_overview', __('Book Overview', 'mybooktable'), 'mbt_overview_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_series_order', __('Series Order', 'mybooktable'), 'mbt_series_order_metabox', 'mbt_book', 'side', 'default');
}

function mbt_enqueue_metabox_js() {
	if(!mbt_is_mbt_admin_page()) { return; }

	wp_enqueue_script('mbt-metaboxes', plugins_url('js/metaboxes.js', dirname(__FILE__)), array('jquery'), MBT_VERSION);
	wp_enqueue_script('mbt-star-ratings', plugins_url('js/lib/jquery.rating.js', dirname(__FILE__)), array('jquery'), MBT_VERSION);
	wp_localize_script('mbt-metaboxes', 'mbt_metabox_i18n', array(
		'author_helptext' => '<p class="description"><a href="'.admin_url('edit-tags.php?taxonomy=mbt_author&post_type=mbt_book').'" target="_blank">'.__('Set the priority (order) of the authors.', 'mybooktable').'</a></p>'
	));
}



/*---------------------------------------------------------*/
/* Book Blurb Metabox                                      */
/*---------------------------------------------------------*/

function mbt_book_blurb_metabox($post) {
?>
	<label class="screen-reader-text" for="excerpt"><?php _e('Excerpt', 'mybooktable'); ?></label><textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo($post->post_excerpt); ?></textarea>
	<p>
	<?php printf(__('Book Blurbs are hand-crafted summaries of your book. The goal of a book blurb is to convince strangers that they need buy your book in 100 words or less. Answer the question "why would I want to read this book?" <a href="%s" target="_blank">Learn more about writing your book blurb.</a>', 'mybooktable'), admin_url('admin.php?page=mbt_help&mbt_video_tutorial=book_blurbs')); ?>
	</p>
<?php
}



/*---------------------------------------------------------*/
/* Overview Metabox                                        */
/*---------------------------------------------------------*/

function mbt_overview_metabox($post) {
	wp_editor($post->post_content, 'content', array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 360) );
	echo('<p>');
	_e('Book Overview is a longer description of your book. This typically includes all the text from the back cover of the book plus, endorsements and any other promotional materials from interior flaps or initial pages. This is also a good place to embed a book trailer if you have one.', 'mybooktable');
	echo('</p>');
}



/*---------------------------------------------------------*/
/* Metadata Metabox                                        */
/*---------------------------------------------------------*/

function mbt_metadata_metabox_ajax() {
	if(isset($_REQUEST['image_id'])) {
		$image = wp_get_attachment_image_src($_REQUEST['image_id'], 'mbt_book_image');
		list($src, $width, $height) = $image ? $image : mbt_get_placeholder_image_src();
		echo('<img src="'.$src.'" class="mbt-book-image">');
	}
	die();
}

function mbt_isbn_preview_ajax() {
	echo(mbt_isbn_preview_feedback($_REQUEST['data']));
	die();
}

function mbt_isbn_preview_feedback($data) {
	$output = '';
	$isbn = $data['mbt_unique_id_isbn'];
	$post_id = $data['mbt_post_id'];

	if(empty($isbn)) {
		if(get_post_status($post_id) === 'publish' and (mbt_get_setting('reviews_box') === 'goodreads' or (mbt_get_setting('reviews_box') === 'amazon' and get_post_meta($post_id, 'mbt_unique_id_asin', true) === ''))) {
			$output = '<span class="mbt_admin_message_warning">'.__('Cannot show reviews without a valid ISBN.', 'mybooktable').'</span>';
		}
	} else {
		$matches = array();
		preg_match("/^([0-9][0-9\-]{8,}[0-9Xx])$/", $isbn, $matches);
		if(!empty($matches[1])) {
			$filtered_isbn = preg_replace("/[^0-9Xx]/", "", $isbn);
			$output = '<span class="mbt_admin_message_success">'.__('Valid ISBN', 'mybooktable').' <a href="http://www.isbnsearch.org/isbn/'.$filtered_isbn.'" target="_blank">'.__('(verify book)', 'mybooktable').'</a></span>';
		} else {
			$output = '<span class="mbt_admin_message_failure">'.__('Invalid ISBN', 'mybooktable').'</span>';
		}
	}

	return $output;
}

function mbt_asin_preview_ajax() {
	echo(mbt_asin_preview_feedback($_REQUEST['data']));
	die();
}

function mbt_asin_preview_feedback($data) {
	$output = '';
	$asin = $data['mbt_unique_id_asin'];
	$post_id = $data['mbt_post_id'];

	if(empty($asin)) {
		if(get_post_status($post_id) === 'publish' and get_post_meta($post_id, 'mbt_show_instant_preview', true) === 'yes') {
			$output = '<span class="mbt_admin_message_warning">'.__('Cannot show Kindle Instant Preview without a valid ASIN.', 'mybooktable').'</span>';
		}
	} else {
		$matches = array();
		preg_match("/^([A-Za-z0-9]{10})$/", $asin, $matches);
		if(!empty($matches[1])) {
			$output = '<span class="mbt_admin_message_success">'.__('Valid ASIN', 'mybooktable').' <a href="http://www.amazon.com/dp/'.$asin.'" target="_blank">'.__('(verify book)', 'mybooktable').'</a></span>';
		} else {
			$output = '<span class="mbt_admin_message_failure">'.__('Invalid ASIN', 'mybooktable').'</span>';
		}
	}

	return $output;
}

function mbt_metadata_text($post_id, $field_id, $data) {
	$value = get_post_meta($post_id, $field_id, true);
	return '<input type="text" name="'.$field_id.'" id="'.$field_id.'" value="'.$value.'" />';
}

function mbt_metadata_checkbox($post_id, $field_id, $data) {
	$value = get_post_meta($post_id, $field_id, true);
	if(!empty($data['default']) and $value === '') { $value = $data['default']; }
	return '<input type="checkbox" name="'.$field_id.'" id="'.$field_id.'" '.checked($value, 'yes', false).'>';
}

function mbt_metadata_upload($post_id, $field_id, $data) {
	$output = '';
	$output .= '<input type="text" name="'.$field_id.'" id="'.$field_id.'" value="'.get_post_meta($post_id, $field_id, true).'" /> ';
	$output .= '<input class="button mbt_upload_button" data-upload-target="'.$field_id.'" data-upload-title="'.__('Choose Sample', 'mybooktable').'" type="button" value="'.__('Upload', 'mybooktable').'" />';
	return $output;
}

function mbt_metadata_star_rating($post_id, $field_id, $data) {
	$star_rating = get_post_meta($post_id, 'mbt_star_rating', true);
	$output = '';
	$output .= '<div class="mbt_star_rating_container">';
	$output .= '<input name="mbt_star_rating" value="1" type="radio" class="mbt-star" '.checked($star_rating, 1, false).'/>';
	$output .= '<input name="mbt_star_rating" value="2" type="radio" class="mbt-star" '.checked($star_rating, 2, false).'/>';
	$output .= '<input name="mbt_star_rating" value="3" type="radio" class="mbt-star" '.checked($star_rating, 3, false).'/>';
	$output .= '<input name="mbt_star_rating" value="4" type="radio" class="mbt-star" '.checked($star_rating, 4, false).'/>';
	$output .= '<input name="mbt_star_rating" value="5" type="radio" class="mbt-star" '.checked($star_rating, 5, false).'/>';
	$output .= '</div>';
	return $output;
}

function mbt_get_metadata_fields() {
	return array(
		'Book Samples' => array(
			'mbt_sample_url' => array(
				'type' => 'mbt_metadata_upload',
				'name' => __('Sample Chapter', 'mybooktable'),
				'desc' => __('Upload a sample chapter from your book to give viewers a preview. We recommend using a .pdf format for the sample chapter.', 'mybooktable'),
			),
			'mbt_sample_audio' => array(
				'type' => 'mbt_metadata_upload',
				'name' => __('Audio Sample', 'mybooktable'),
				'desc' => __('Upload a sample from your audiobook to give viewers a preview. We recommend using a .mp3 format for the sample.', 'mybooktable'),
			),
			'mbt_show_instant_preview' => array(
				'type' => 'mbt_metadata_checkbox',
				'name' => __('Kindle Instant Preview', 'mybooktable'),
				'desc' => __('Displays a free instant preview of your book from Amazon.', 'mybooktable'),
				'default' => 'yes',
			),
		),
		'Price' => array(
			'mbt_price' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('List Price', 'mybooktable'),
				'desc' => __('You can typically find the list price just above the ISBN barcode on the back cover of the book.', 'mybooktable'),
			),
			'mbt_sale_price' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Sale Price', 'mybooktable'),
				'desc' => __('Setting a sale price will cross out the normal price and show the sale price prominently.', 'mybooktable'),
			),
			'mbt_ebook_price' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('E-book Price', 'mybooktable'),
				'desc' => __('If your book is available in multiple formats, you can use this to display the e-book price.', 'mybooktable'),
			),
			'mbt_audiobook_price' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Audiobook Price', 'mybooktable'),
				'desc' => __('If your book is available in multiple formats, you can use this to display the audiobook price.', 'mybooktable'),
			),
		),
		'Publisher' => array(
			'mbt_publisher_name' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Publisher Name', 'mybooktable'),
			),
			'mbt_publisher_url' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Publisher URL', 'mybooktable'),
				'desc' => __('Setting a publisher URL will turn the "Publisher Name" into a link to this address.', 'mybooktable'),
			),
			'mbt_publication_year' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Publication Year', 'mybooktable'),
			),
		),
		'Other' => array(
			'mbt_star_rating' => array(
				'type' => 'mbt_metadata_star_rating',
				'name' => __('Star Rating', 'mybooktable'),
			),
			'mbt_book_format' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Book Format', 'mybooktable'),
				'desc' => __('What format is the book presented in?', 'mybooktable'),
			),
			'mbt_book_length' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Book Length', 'mybooktable'),
				'desc' => __('Is this book a short story, a complete novel, or an epic drama?', 'mybooktable'),
			),
			'mbt_narrator' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Narrator', 'mybooktable'),
				'desc' => __('If applicable, who is the book narrated by?', 'mybooktable'),
			),
			'mbt_illustrator' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Illustrator', 'mybooktable'),
				'desc' => __('If applicable, who is the book illustrated by?', 'mybooktable'),
			),
		),
	);
}

function mbt_metadata_metabox($post) {
	$metadata = mbt_get_metadata_fields();
?>
	<input type="hidden" id="mbt_post_id" value="<?php echo($post->ID); ?>" />
	<table>
		<tr>
			<td rowspan="3" class="mbt_cover_image_container">
				<h4 class="mbt-cover-image-title"><?php _e('Book Cover Image', 'mybooktable'); ?></h4>
				<?php mbt_the_book_image(); ?><br>
				<input type="hidden" id="mbt_book_image_id" name="mbt_book_image_id" value="<?php echo(get_post_meta($post->ID, "mbt_book_image_id", true)); ?>" />
				<input id="mbt_set_book_image_button" class="button mbt_upload_button" data-upload-target="mbt_book_image_id" data-upload-property="id" data-upload-title="<?php _e('Book Cover Image', 'mybooktable'); ?>" type="button" value="<?php _e('Set cover image', 'mybooktable'); ?>" />
			</td>
			<td class="mbt_unique_identifier_container">
				<label><?php _e('ISBN', 'mybooktable'); ?>:</label>
				<?php $isbn = get_post_meta($post->ID, 'mbt_unique_id_isbn', true); ?>
				<div class="mbt_unique_identifier_input">
					<input type="text" name="mbt_unique_id_isbn" id="mbt_unique_id_isbn" value="<?php echo($isbn); ?>" class="mbt_feedback_refresh mbt_feedback_colorize" data-refresh-action="mbt_isbn_preview" data-element="mbt_unique_id_isbn,mbt_post_id"/>
					<div class="mbt_feedback"><?php echo(mbt_isbn_preview_feedback(array('mbt_unique_id_isbn' => $isbn, 'mbt_post_id' => $post->ID))); ?></div>
				</div>
				<p class="description"><?php _e('This is the International Standard Book Number, used to populate GoodReads and Amazon reviews.', 'mybooktable'); ?></p>
			</td>
		</tr>
		<tr>
			<td class="mbt_unique_identifier_container">
				<label><?php _e('ASIN', 'mybooktable'); ?>:</label>
				<?php $asin = get_post_meta($post->ID, 'mbt_unique_id_asin', true); ?>
				<div class="mbt_unique_identifier_input">
					<input type="text" name="mbt_unique_id_asin" id="mbt_unique_id_asin" value="<?php echo($asin); ?>" class="mbt_feedback_refresh mbt_feedback_colorize" data-refresh-action="mbt_asin_preview" data-element="mbt_unique_id_asin,mbt_post_id"/>
					<div class="mbt_feedback"><?php echo(mbt_asin_preview_feedback(array('mbt_unique_id_asin' => $asin, 'mbt_post_id' => $post->ID))); ?></div>
				</div>
				<p class="description"><?php _e('This is the Amazon Standard Identification Number, used to populate Amazon reviews and Kindle Instant Preview.', 'mybooktable'); ?></p>
			</td>
		</tr>
		<tr>
			<td class="mbt_show_unique_identifier_container">
				<?php $show_unique_id = get_post_meta($post->ID, 'mbt_show_unique_id', true) !== 'no' ? 'yes' : 'no'; ?>
				<input type="checkbox" name="mbt_show_unique_id" id="mbt_show_unique_id" <?php checked($show_unique_id, 'yes'); ?> >
				<label for="mbt_show_unique_id"><?php _e('Show ISBN/ASIN on book page?', 'mybooktable'); ?></label>
			</td>
		</tr>
	</table>
	<div class="mbt_metadata_fields">
		<?php foreach($metadata as $section_name => $section) {
			echo('<div class="mbt-accordion"><h4>'.$section_name.'</h4><div>');
			foreach($section as $field_id => $field_data) {
				echo('<div class="mbt_metadata_field">');
				echo('<label for="'.$field_id.'">'.$field_data['name'].':</label>');
				echo(call_user_func_array($field_data['type'], array($post->ID, $field_id, $field_data)));
				if(!empty($field_data['desc'])) { echo('<p class="description">'.$field_data['desc'].'</p>'); }
				echo('</div>');
			}
			echo('</div></div>');
		} ?>
	</div>
<?php
}

function mbt_save_metadata_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft') { return; }

	if(get_post_type($post_id) == 'mbt_book') {
		if(isset($_REQUEST['mbt_unique_id_asin'])) { update_post_meta($post_id, 'mbt_unique_id_asin', preg_replace('/[^A-Za-z0-9]/', '', $_REQUEST['mbt_unique_id_asin'])); }
		if(isset($_REQUEST['mbt_unique_id_isbn'])) { update_post_meta($post_id, 'mbt_unique_id_isbn', preg_replace('/[^0-9Xx]/', '', $_REQUEST['mbt_unique_id_isbn'])); }
		update_post_meta($post_id, 'mbt_show_unique_id', isset($_REQUEST['mbt_show_unique_id']) ? 'yes' : 'no');

		$metadata = mbt_get_metadata_fields();
		foreach($metadata as $section_name => $section) {
			foreach($section as $field_id => $field_data) {
				$value = isset($_REQUEST[$field_id]) ? $_REQUEST[$field_id] : null;
				if($field_data['type'] == 'mbt_metadata_checkbox') { $value = $value === null ? 'no' : 'yes'; }
				update_post_meta($post_id, $field_id, $value);
			}
		}
	}
}



/*---------------------------------------------------------*/
/* Buy Button Metabox                                      */
/*---------------------------------------------------------*/

function mbt_buybuttons_metabox_editor($data, $num, $store) {
	$output  = '<div class="mbt_buybutton_editor">';
	$output .= '<div class="mbt_buybutton_editor_header">';
	$output .= '<button class="mbt_buybutton_remover button">'.__('Remove').'</button>';
	$output .= '<h4 class="mbt_buybutton_title">'.$store['name'].'</h4>';
	$output .= '</div>';
	$output .= '<div class="mbt_buybutton_editor_content">';
	$output .= mbt_buybutton_editor($data, "mbt_buybutton".$num, $store);
	$output .= '</div>';
	$output .= '<div class="mbt_buybutton_editor_footer">';
	$output .= '<span class="mbt_buybutton_display_title">Display as:</span>';
	$display = (empty($data['display'])) ? 'button' : $data['display'];
	$output .= '<label class="mbt_buybutton_display"><input type="radio" name="mbt_buybutton'.$num.'[display]" value="button" '.checked($display, 'button', false).'>'.__('Button', 'mybooktable').'</label>';
	$output .= '<label class="mbt_buybutton_display"><input type="radio" name="mbt_buybutton'.$num.'[display]" value="text" '.checked($display, 'text', false).'>'.__('Text Bullet', 'mybooktable').'</label>';
	$output .= '</div>';
	$output .= '</div>';
	return $output;
}

function mbt_buybuttons_metabox_ajax() {
	$stores = mbt_get_stores();
	if(empty($stores[$_REQUEST['store']])) { die(); }
	echo(mbt_buybuttons_metabox_editor(array('store' => $_REQUEST['store']), 0, $stores[$_REQUEST['store']]));
	die();
}

function mbt_buybuttons_metabox($post) {
	wp_nonce_field(plugin_basename(__FILE__), 'mbt_nonce');

	if(!mbt_get_setting('enable_default_affiliates') and mbt_get_upgrade() === false) {
		echo('<a href="admin.php?page=mbt_settings&mbt_setup_default_affiliates=1">'.__('Activate Amazon and Barnes &amp; Noble Buttons').'</a>');
	}

	echo('<div class="mbt-buybuttons-note">'.mbt_get_upgrade_message(false, __('Want more options? Upgrade your MyBookTable and get the Universal Buy Button.', 'mybooktable'), '').'</div>');

	$stores = mbt_get_stores();
	uasort($stores, create_function('$a,$b', 'return strcasecmp($a["name"],$b["name"]);'));
	echo('<label for="mbt_store_selector">Choose One:</label> ');
	echo('<select id="mbt_store_selector">');
	echo('<option value="">'.__('-- Choose One --').'</option>');
	foreach($stores as $slug => $store) {
		echo('<option value="'.$slug.'">'.$store['name'].'</option>');
	}
	echo('</select> ');
	echo('<button id="mbt_buybutton_adder" class="button">'.__('Add').'</button>');

	echo('<div id="mbt_buybutton_editors">');
	$buybuttons = mbt_query_buybuttons($post->ID);
	if(!empty($buybuttons)) {
		for($i = 0; $i < count($buybuttons); $i++) {
			$buybutton = $buybuttons[$i];
			if(empty($stores[$buybutton['store']])) { continue; }
			echo(mbt_buybuttons_metabox_editor($buybutton, $i+1, $stores[$buybutton['store']]));
		}
	}
	echo('</div>');
}

function mbt_save_buybuttons_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce($_REQUEST['mbt_nonce'], plugin_basename(__FILE__))){return;}

	if(get_post_type($post_id) == 'mbt_book') {
		$stores = mbt_get_stores();
		$buybuttons = array();
		for($i = 1; isset($_REQUEST['mbt_buybutton'.$i]); $i++) {
			$buybutton = $_REQUEST['mbt_buybutton'.$i];
			if(empty($stores[$buybutton['store']])) { continue; }
			$buybutton['url'] = preg_replace('/[\r\n]/', '', $buybutton['url']);
			$buybuttons[] = apply_filters('mbt_buybutton_save', $buybutton, $stores[$buybutton['store']]);
		}
		update_post_meta($post_id, 'mbt_buybuttons', $buybuttons);

		// auto-populate book asin
		if(get_post_meta($post_id, 'mbt_show_instant_preview', true) == 'yes' and get_post_meta($post_id, 'mbt_unique_id_asin', true) == '') {
			foreach($buybuttons as $buybutton) {
				if($buybutton['store'] == 'amazon') {
					$asin = mbt_get_amazon_AISN($buybutton['url']);
					if(!empty($asin)) { update_post_meta($post_id, 'mbt_unique_id_asin', $asin); }
					break;
				}
			}
		}
	}


}



/*---------------------------------------------------------*/
/* Series Order Metabox                                    */
/*---------------------------------------------------------*/

function mbt_series_order_metabox($post) {
?>
	<label for="mbt_series_order"><?php _e('Book Number', 'mybooktable'); ?>: </label><input name="mbt_series_order" type="text" size="4" id="mbt_series_order" value="<?php echo(esc_attr(get_post_meta($post->ID, "mbt_series_order", true))); ?>" />
	<p class="mbt-helper-description"><?php _e('Use this to order books within a series.', 'mybooktable'); ?></p>
<?php
}

function mbt_save_series_order_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce($_REQUEST['mbt_nonce'], plugin_basename(__FILE__))){return;}

	if(get_post_type($post_id) == "mbt_book") {
		update_post_meta($post_id, "mbt_series_order", $_REQUEST["mbt_series_order"]);
	}
}
