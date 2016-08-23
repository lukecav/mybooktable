<?php

function mbt_metaboxes_init() {
	add_action('wp_ajax_mbt_buybuttons_metabox', 'mbt_buybuttons_metabox_ajax');
	add_action('wp_ajax_mbt_book_image_preview', 'mbt_book_image_preview_ajax');
	add_action('wp_ajax_mbt_endorsement_image_preview', 'mbt_endorsement_image_preview_ajax');
	add_action('wp_ajax_mbt_isbn_preview', 'mbt_isbn_preview_ajax');
	add_action('wp_ajax_mbt_asin_preview', 'mbt_asin_preview_ajax');
	add_action('wp_ajax_mbt_overview_image_preview', 'mbt_overview_image_preview_ajax');
	add_action('wp_ajax_mbt_main_author_url', 'mbt_main_author_url_ajax');
	add_action('wp_ajax_mbt_change_booksections_displaymode', 'mbt_change_booksections_displaymode_ajax');
	add_action('admin_enqueue_scripts', 'mbt_enqueue_metabox_js');

	add_action('save_post', 'mbt_save_book_blurb_metabox');
	add_action('save_post', 'mbt_save_metadata_metabox');
	add_action('save_post', 'mbt_save_buybuttons_metabox');
	add_action('save_post', 'mbt_save_series_order_metabox');
	add_action('save_post', 'mbt_save_endorsements_metabox');
	add_action('save_post', 'mbt_save_bookclub_metabox');
	add_action('save_post', 'mbt_save_overview_metabox');
	add_action('save_post', 'mbt_save_display_mode_field');
	add_action('save_post', 'mbt_save_post_author_field');
	add_action('save_post', 'mbt_save_sectionsorting_metabox');

	add_action('add_meta_boxes', 'mbt_add_metaboxes', 9);
	add_action('post_submitbox_misc_actions', 'mbt_add_post_author_field');
	add_action('post_submitbox_misc_actions', 'mbt_add_display_mode_field');
}
add_action('mbt_init', 'mbt_metaboxes_init');

function mbt_add_metaboxes() {
	add_meta_box('mbt_blurb', __('Book Blurb', 'mybooktable'), 'mbt_book_blurb_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_metadata', __('Book Details', 'mybooktable'), 'mbt_metadata_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_buybuttons', __('Buy Buttons', 'mybooktable'), 'mbt_buybuttons_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_overview', __('About the Book', 'mybooktable'), 'mbt_overview_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_endorsements', __('Endorsements', 'mybooktable'), 'mbt_endorsements_metabox', 'mbt_book', 'normal', 'high');
	add_meta_box('mbt_bookclub', __('Book Club Resources', 'mybooktable'), 'mbt_bookclub_metabox', 'mbt_book', 'normal', 'low');
	add_meta_box('mbt_series_order', __('Series Order', 'mybooktable'), 'mbt_series_order_metabox', 'mbt_book', 'side', 'default');
	add_meta_box('mbt_sectionsorting', __('Section Order', 'mybooktable'), 'mbt_sectionsorting_metabox', 'mbt_book', 'side', 'default');
}

function mbt_enqueue_metabox_js() {
	if(!mbt_is_mbt_admin_page()) { return; }

	wp_enqueue_script('mbt-metaboxes', plugins_url('js/metaboxes.js', dirname(__FILE__)), array('jquery'), MBT_VERSION);
	wp_enqueue_script('mbt-star-ratings', plugins_url('js/lib/jquery.rating.js', dirname(__FILE__)), array('jquery'), MBT_VERSION);
	wp_enqueue_script('mbt-colorpicker', plugins_url('js/lib/spectrum.js', dirname(__FILE__)), array('jquery'), MBT_VERSION, true);
	wp_enqueue_style('mbt-colorpicker', plugins_url('css/lib/spectrum.css', dirname(__FILE__)), array(), MBT_VERSION);
	add_action('admin_head', 'mbt_override_authors_metabox');
}

function mbt_override_authors_metabox() {
	?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#mbt_authordiv .inside').append(jQuery('<p class="description"><a href="<?php echo(admin_url('edit-tags.php?taxonomy=mbt_author&post_type=mbt_book')); ?>" target="_blank"><?php _e('Set the priority (order) of the authors', 'mybooktable'); ?></a></p>'));
				jQuery('#mbt_authordiv .inside').append(jQuery('<p class="description"><a id="mbt_main_author_link" href="<?php echo(admin_url('edit-tags.php?taxonomy=mbt_author&post_type=mbt_book')); ?>" target="_blank"><?php _e('Edit the main author bio', 'mybooktable'); ?></a></p>'));
			});
		</script>
	<?php
}

function mbt_main_author_url_ajax() {
	$main_author = NULL;
	if(!empty($_REQUEST['authors'])) {
		$authors = $_REQUEST['authors'];
		$sortfunc = function($a, $b) {
			$a = mbt_get_author_priority($a);
			$b = mbt_get_author_priority($b);
			return ($a > $b) ? -1 : (($a < $b) ? 1 : 0);
		};
		usort($authors, $sortfunc);
		$main_author = $authors[0];
	}
	if(empty($main_author)) {
		echo(admin_url('edit-tags.php?taxonomy=mbt_author&post_type=mbt_book'));
	} else {
		echo(admin_url('term.php?taxonomy=mbt_author&post_type=mbt_book&tag_ID='.$main_author));
	}
	die();
}






/*---------------------------------------------------------*/
/* Publish Metabox                                         */
/*---------------------------------------------------------*/

function mbt_add_display_mode_field($post) {
	$display_modes = mbt_get_book_display_modes();
	if(empty($display_modes)) { return; }
	$current_mode = get_post_meta($post->ID, 'mbt_display_mode', true);
	if(empty($display_modes[$current_mode])) { $current_mode = mbt_get_default_book_display_mode(); }
	?>
	<div class="misc-pub-section misc-pub-display-mode" id="mbt_display_mode_field">
	<input type="hidden" id="mbt_display_modes" value='<?php echo(str_replace('\'', '&#39;', json_encode($display_modes))); ?>'>
	<label for="mbt_display_mode">Display Mode:</span>
	<select name="mbt_display_mode" id="mbt_display_mode">
		<?php foreach($display_modes as $display_mode_id => $display_mode) { ?>
			<option value="<?php echo($display_mode_id); ?>" <?php selected($display_mode_id, $current_mode); ?> ><?php echo($display_mode['name']); ?></option>
		<?php } ?>
	</select>
	</div>
	<?php
}

function mbt_save_display_mode_field($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }
	if(isset($_REQUEST['mbt_display_mode'])) { update_post_meta($post_id, 'mbt_display_mode', $_REQUEST['mbt_display_mode']); }
}

function mbt_add_post_author_field($post) {
	?>
	<div class="misc-pub-section misc-pub-post-author" id="mbt_post_author_field">
	<label for="mbt_post_author">Post Author:</span>
	<select name="mbt_post_author" id="mbt_post_author">
		<?php foreach(get_users() as $author) { ?>
			<option value="<?php echo($author->ID); ?>" <?php selected($author->ID, $post->post_author); ?> ><?php echo($author->display_name); ?> (<?php echo($author->user_login); ?>)</option>
		<?php } ?>
	</select>
	</div>
	<?php
}

function mbt_save_post_author_field($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }
	if(isset($_REQUEST['mbt_post_author'])) { global $wpdb; $wpdb->update($wpdb->posts, array('post_author' => $_REQUEST['mbt_post_author']), array('ID' => $post_id), array('%d'), array('%d')); }
}



/*---------------------------------------------------------*/
/* Book Blurb Metabox                                      */
/*---------------------------------------------------------*/

function mbt_book_blurb_metabox($post) {
?>
	<div class="mbt_book_teaser_field" data-mbt-supports="teaser">
		<label for="mbt_book_teaser"><?php _e('Teaser Text:', 'mybooktable'); ?></label>
		<div class="mbt_book_teaser_input"><input type="text" name="mbt_book_teaser" id="mbt_book_teaser" value="<?php echo(get_post_meta($post->ID, 'mbt_book_teaser', true)); ?>"></div>
	</div>
	<label class="screen-reader-text" for="excerpt"><?php _e('Excerpt', 'mybooktable'); ?></label><textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo($post->post_excerpt); ?></textarea>
	<p class="description">
	<?php printf(__('Book Blurbs are hand-crafted summaries of your book. The goal of a book blurb is to convince strangers that they need buy your book in 100 words or less. Answer the question "why would I want to read this book?" <a href="%s" target="_blank">Learn more about writing your book blurb.</a>', 'mybooktable'), admin_url('admin.php?page=mbt_help&mbt_video_tutorial=book_blurbs')); ?>
	</p>
<?php
}

function mbt_save_book_blurb_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }
	if(isset($_REQUEST['mbt_book_teaser'])) { update_post_meta($post_id, 'mbt_book_teaser', $_REQUEST['mbt_book_teaser']); }
}



/*---------------------------------------------------------*/
/* Overview Metabox                                        */
/*---------------------------------------------------------*/

function mbt_overview_metabox($post) {
	?>
	<div class="mbt_overview_image_field" data-mbt-supports="overview_image">
		<label for="mbt_overview_image"><?php _e('Image:', 'mybooktable'); ?></label>
		<div class="mbt_overview_image_preview"><?php echo(mbt_get_overview_image_preview(get_post_meta($post->ID, 'mbt_overview_image', true))); ?></div>
		<input type="hidden" id="mbt_overview_image" name="mbt_overview_image" value="<?php echo(get_post_meta($post->ID, 'mbt_overview_image', true)); ?>" />
		<input class="button mbt_upload_button" data-upload-target="mbt_overview_image" data-upload-property="id" type="button" value="<?php _e('Choose', 'mybooktable'); ?>" />
		<input class="button mbt_upload_clear_button" data-upload-target="mbt_overview_image" type="button" value="X" />
	</div>
	<?php
	wp_editor($post->post_content, 'content', array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 360) );
	echo('<p class="description">');
	_e('The About the Book section is a longer description of your book. This typically includes all the text from the back cover of the book plus, endorsements and any other promotional materials from interior flaps or initial pages. This is also a good place to embed a book trailer if you have one.', 'mybooktable');
	echo('</p>');
}

function mbt_save_overview_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }
	if(isset($_REQUEST['mbt_overview_image'])) { update_post_meta($post_id, 'mbt_overview_image', $_REQUEST['mbt_overview_image']); }
}

function mbt_get_overview_image_preview($image_id) {
	list($src, $width, $height) = wp_get_attachment_image_src($image_id);
	return $src ? basename($src) : 'None';
}

function mbt_overview_image_preview_ajax() {
	if(isset($_REQUEST['image_id'])) { echo(mbt_get_overview_image_preview($_REQUEST['image_id'])); }
	die();
}



/*---------------------------------------------------------*/
/* Metadata Metabox                                        */
/*---------------------------------------------------------*/

function mbt_book_image_preview_ajax() {
	if(isset($_REQUEST['image_id'])) {
		$image = wp_get_attachment_image_src($_REQUEST['image_id'], 'mbt_book_image');
		list($src, $width, $height) = $image ? $image : mbt_get_placeholder_image_src();
		echo($src);
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
			$output = '<span class="mbt_admin_message_warning">'.__('Cannot show Kindle Instant Preview without a valid ASIN.', 'mybooktable').' <a href="https://www.amazon.com/gp/help/customer/display.html?nodeId=200202190#find_asins" target="_blank">'.__('(Find your ASIN)', 'mybooktable').'</a>'.'</span>';
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

function mbt_metadata_kindle_instant_preview($post_id, $field_id, $data) {
	$output = mbt_metadata_checkbox($post_id, $field_id, $data);
	$asin = get_post_meta($post_id, 'mbt_unique_id_asin', true);
	$output .= empty($asin) ? '<br><span class="mbt_admin_message_warning" id="mbt_show_instant_preview_asin_warning">'.__('Cannot show Kindle Instant Preview without a valid ASIN.', 'mybooktable').' <a href="https://www.amazon.com/gp/help/customer/display.html?nodeId=200202190#find_asins" target="_blank">'.__('(Find your ASIN)', 'mybooktable').'</a>'.'</span>' : '';
	return $output;
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

function mbt_metadata_colorpicker($post_id, $field_id, $data) {
	$output = '';
	$output .= '<input type="text" class="mbt-colorpicker" name="'.$field_id.'" id="'.$field_id.'" value="'.get_post_meta($post_id, $field_id, true).'"'.(empty($data['default']) ? '' : ' data-default-color="'.$data['default'].'"').'/>';
	$output .= '<input class="button mbt-colorpicker-clear" type="button" value="'.__('Clear', 'mybooktable').'" />';
	return $output;
}

function mbt_get_metadata_fields() {
	return array(
		'Book Samples' => array(
			'mbt_show_instant_preview' => array(
				'type' => 'mbt_metadata_kindle_instant_preview',
				'name' => __('Kindle Instant Preview', 'mybooktable'),
				'desc' => __('Displays a free instant preview of your book from Amazon.', 'mybooktable'),
				'default' => 'yes',
			),
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
			'mbt_sample_video' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('Book Trailer', 'mybooktable'),
				'desc' => __('Paste in the URL of a YouTube or Vimeo video that shows off your book.', 'mybooktable'),
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
				'supports' => 'sale_price',
			),
			'mbt_ebook_price' => array(
				'type' => 'mbt_metadata_text',
				'name' => __('eBook Price', 'mybooktable'),
				'desc' => __('If your book is available in multiple formats, you can use this to display the eBook price.', 'mybooktable'),
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
		'Section Colors' => array(
			'supports' => 'section_colors',
			'mbt_bg_color' => array(
				'type' => 'mbt_metadata_colorpicker',
				'name' => __('Background Color', 'mybooktable'),
				'default' => '#fff',
			),
			'mbt_bg_color_alt' => array(
				'type' => 'mbt_metadata_colorpicker',
				'name' => __('Alternate Background Color', 'mybooktable'),
				'default' => '#ddd',
			),
			'mbt_button_color' => array(
				'type' => 'mbt_metadata_colorpicker',
				'name' => __('Button Color', 'mybooktable'),
				'default' => '#64ACF3',
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
				<input id="mbt_clear_book_image_button" class="button mbt_upload_clear_button" data-upload-target="mbt_book_image_id" type="button" value="X" />
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
				<label for="mbt_show_unique_id"><?php _e('Show ISBN/ASIN on book page', 'mybooktable'); ?></label>
			</td>
		</tr>
	</table>
	<div class="mbt_metadata_fields">
		<?php foreach($metadata as $section_name => $section) {
			echo('<div class="mbt-accordion"'.(empty($section['supports']) ? '' : ' data-mbt-supports="'.$section['supports'].'"').'><h4>'.$section_name.'</h4><div>');
			foreach($section as $field_id => $field_data) {
				if($field_id == 'supports') { continue; }
				echo('<div class="mbt_metadata_field mbt_metadata_field_'.$field_id.'"'.(empty($field_data['supports']) ? '' : ' data-mbt-supports="'.$field_data['supports'].'"').'>');
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
		if(isset($_REQUEST['mbt_book_image_id'])) { update_post_meta($post_id, 'mbt_book_image_id', $_REQUEST['mbt_book_image_id']); }
		if(isset($_REQUEST['mbt_unique_id_asin'])) { update_post_meta($post_id, 'mbt_unique_id_asin', preg_replace('/[^A-Za-z0-9]/', '', $_REQUEST['mbt_unique_id_asin'])); }
		if(isset($_REQUEST['mbt_unique_id_isbn'])) { update_post_meta($post_id, 'mbt_unique_id_isbn', preg_replace('/[^0-9Xx]/', '', $_REQUEST['mbt_unique_id_isbn'])); }
		update_post_meta($post_id, 'mbt_show_unique_id', isset($_REQUEST['mbt_show_unique_id']) ? 'yes' : 'no');

		$metadata = mbt_get_metadata_fields();
		foreach($metadata as $section_name => $section) {
			foreach($section as $field_id => $field_data) {
				$value = isset($_REQUEST[$field_id]) ? $_REQUEST[$field_id] : null;
				if($field_data['type'] == 'mbt_metadata_checkbox' or $field_data['type'] == 'mbt_metadata_kindle_instant_preview') { $value = $value === null ? 'no' : 'yes'; }
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
		if(get_post_meta($post_id, 'mbt_unique_id_asin', true) == '') {
			foreach($buybuttons as $buybutton) {
				if($buybutton['store'] == 'amazon' or $buybutton['store'] == 'kindle') {
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



/*---------------------------------------------------------*/
/* Endorsements Metabox                                    */
/*---------------------------------------------------------*/

function mbt_endorsement_image_preview_ajax() {
	if(isset($_REQUEST['image_id'])) {
		$image = wp_get_attachment_image_src($_REQUEST['image_id'], 'mbt_endorsement_image');
		echo($image ? $image[0] : plugins_url('images/person-placeholder.png', dirname(__FILE__)));
	}
	die();
}

function mbt_endorsements_metabox($post) {
	$endorsements = get_post_meta($post->ID, 'mbt_endorsements', true);
	if(empty($endorsements)) { $endorsements = array(); }
	?>
		<div class="mbt_endorsements_metabox">
			<div class="button button-primary mbt_endorsement_adder">Add Endorsement</div>
			<input type="hidden" class="mbt_endorsements" name="mbt_endorsements" value='<?php echo(str_replace('\'', '&#39;', json_encode($endorsements))); ?>'>
			<div class="mbt_endorsements_editors"></div>
			<p class="description">This is where you post endorsements from folks who have reccommended your book.</p>
		</div>
	<?php
}

function mbt_save_endorsements_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }

	if(isset($_REQUEST['mbt_endorsements'])) { update_post_meta($post_id, 'mbt_endorsements', json_decode(str_replace('\\\\', '\\', str_replace('\"', '"', str_replace('\\\'', '\'', $_REQUEST['mbt_endorsements']))), true)); }
}



/*---------------------------------------------------------*/
/* Book Club Metabox                                       */
/*---------------------------------------------------------*/

function mbt_bookclub_metabox($post) {
	$video = get_post_meta($post->ID, 'mbt_bookclub_video', true);
	$resources = get_post_meta($post->ID, 'mbt_bookclub_resources', true);
	if(empty($resources)) { $resources = array(); }
	?>
		<div class="mbt_bookclub_metabox">
			<div class="mbt_bookclub_resources_container">
				<div class="mbt_bookclub_resources_description">
					<p>The more resources you provide book clubs, the more they will want to read your book. Suggested materials include:</p>
					<ul>
						<li>PDF Download (Discussion Questions)</li>
						<li>Bulk Ordering Link</li>
						<li>Powerpoint Companion</li>
						<li>Chapter Excerpts PDF</li>
					</ul>
				</div>
				<div class="button button-primary mbt_bookclub_resource_adder">Add Resource</div>
				<input type="hidden" class="mbt_bookclub_resources" name="mbt_bookclub_resources" value='<?php echo(str_replace('\'', '&#39;', json_encode($resources))); ?>'>
				<div class="mbt_bookclub_resource_editors"></div>
			</div>
			<div class="mbt_bookclub_video_container">
				<h4 class="mbt_bookclub_video_title">Video Companion</h4>
				<p>This is where you can include a short YouTube or Vimeo video for Book Groups to show as part of the book discussion.</p>
				<label>Video URL: <input type="text" name="mbt_bookclub_video" id="mbt_bookclub_video" value="<?php echo($video); ?>" /></label>
			</div>
		</div>
	<?php
}

function mbt_save_bookclub_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }

	if(isset($_REQUEST['mbt_bookclub_resources'])) { update_post_meta($post_id, 'mbt_bookclub_resources', json_decode(str_replace('\\\\', '\\', str_replace('\"', '"', str_replace('\\\'', '\'', $_REQUEST['mbt_bookclub_resources']))), true)); }
	if(isset($_REQUEST['mbt_bookclub_video'])) { update_post_meta($post_id, 'mbt_bookclub_video', $_REQUEST['mbt_bookclub_video']); }
}



/*---------------------------------------------------------*/
/* Section Sorting Metabox                                 */
/*---------------------------------------------------------*/

function mbt_change_booksections_displaymode_ajax() {
	if(isset($_REQUEST['display_mode'])) {
		if(isset($_REQUEST['reset']) and $_REQUEST['reset'] === "true") {
			mbt_update_setting('book_section_order_'.$_REQUEST['display_mode'], array());
		}
		$sections = mbt_get_sorted_content_sections($_REQUEST['display_mode']);
		echo(str_replace('\'', '&#39;', json_encode($sections)));
	}
	die();
}

function mbt_sectionsorting_metabox($post) {
	$display_mode = mbt_get_book_display_mode($post->ID);
	$sections = mbt_get_sorted_content_sections($display_mode);
	if(empty($sections)) { $sections = array(); }
	?>
		<div class="mbt_sectionsorting_metabox">
			<input type="hidden" class="mbt_booksections_displaymode" name="mbt_booksections_displaymode" value="<?php echo($display_mode); ?>">
			<input type="hidden" class="mbt_booksections" name="mbt_booksections" value='<?php echo(str_replace('\'', '&#39;', json_encode($sections))); ?>'>
			<div class="mbt_booksections_sorters"></div>
			<p class="description" style="margin-bottom: 6px;">Use this to rearrange the display order of the sections on the book page.</p>
			<div class="button mbt_booksections_reset">Restore Default Section Order</div>
		</div>
		<script type="text/javascript">jQuery('.mbt_sectionsorting_metabox').parents('#mbt_sectionsorting').attr('data-mbt-supports', 'sortable_sections');</script>
	<?php
}

function mbt_save_sectionsorting_metabox($post_id) {
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || get_post_status($post_id) == 'auto-draft' || get_post_type($post_id) !== 'mbt_book') { return; }

	if(isset($_REQUEST['mbt_booksections']) and isset($_REQUEST['mbt_booksections_displaymode'])) {
		mbt_update_setting('book_section_order_'.$_REQUEST['mbt_booksections_displaymode'], json_decode(str_replace('\\\\', '\\', str_replace('\"', '"', str_replace('\\\'', '\'', $_REQUEST['mbt_booksections']))), true));
	}
}
