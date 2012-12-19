<?php

function bss_add_metaboxes()
{
	add_meta_box('mbt_blurb', 'Book Blurb', 'mbt_book_blurb_metabox', 'mbt_books', 'normal', 'high');
	add_meta_box('mbt_overview', 'Book Overview', 'mbt_overview_metabox', 'mbt_books', 'normal', 'high');
	add_meta_box('mbt_metadata', 'Book Metadata', 'mbt_metadata_metabox', 'mbt_books', 'normal', 'high');
	if(mbt_is_seo_active()) { add_meta_box('mbt_seo', 'SEO Information', 'mbt_seo_metabox', 'mbt_books', 'normal', 'high'); }
	add_meta_box('mbt_affiliates', 'Affiliates', 'mbt_affiliates_metabox', 'mbt_books', 'normal');
}
add_action('add_meta_boxes', 'bss_add_metaboxes', 9);


/*---------------------------------------------------------*/
/* Book Blurb Metabox                                      */
/*---------------------------------------------------------*/

function mbt_book_blurb_metabox($post)
{
?>
	<label class="screen-reader-text" for="excerpt">Excerpt</label><textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo($post->post_excerpt); ?></textarea>
	<p>Book Blurbs are hand-crafted summaries of your book. <a href="<?php echo(admin_url('edit.php?post_type=mbt_books&page=mbt_help')); ?>" target="_blank">Learn more about writing your book blurb.</a></p>
<?php
}

/*---------------------------------------------------------*/
/* Overview Metabox                                        */
/*---------------------------------------------------------*/

function mbt_overview_metabox($post)
{
	wp_editor($post->post_content, 'content2', array('dfw' => true, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 360) );
}

/*---------------------------------------------------------*/
/* Metadata Metabox                                        */
/*---------------------------------------------------------*/

function mbt_metadata_metabox($post)
{
?>
	<table class="form-table mbt_metadata_metabox">
		<tr>
			<th><label for="mbt_book_id">Book ID</label></th>
			<td>
				<input type="text" name="mbt_book_id" id="mbt_book_id" value="<?php echo(get_post_meta($post->ID, "mbt_book_id", true)); ?>" />
				<p class="description">SKU or Unique ID</p>
			</td>
		</tr>
		<tr>
			<th><label for="mbt_price">Book Price</label></th>
			<td>
				$ <input type="text" name="mbt_price" id="mbt_price" value="<?php echo(get_post_meta($post->ID, "mbt_price", true)); ?>" />
				<p class="description">Optional</p>
			</td>
		</tr>
	</table>
<?php
}

function mbt_save_metadata_metabox($post_id)
{
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}

	if(get_post_type($post_id) == "mbt_books")
	{
		if(isset($_POST['mbt_book_id'])) { update_post_meta($post_id, "mbt_book_id", $_POST['mbt_book_id']); }
		if(isset($_POST['mbt_price'])) { update_post_meta($post_id, "mbt_price", $_POST['mbt_price']); }
	}
}
add_action('save_post', 'mbt_save_metadata_metabox');

/*---------------------------------------------------------*/
/* SEO Metabox                                             */
/*---------------------------------------------------------*/

function mbt_seo_metabox($post)
{
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#mbt_seo_title").keydown(function() {
				if(70-jQuery(this).val().length <= 0 && event.keyCode != 8) { return false; }
				jQuery("#mbt_seo_title-length").text(70-jQuery(this).val().length);
			});
			jQuery("#mbt_seo_title").keyup(function() {
				jQuery("#mbt_seo_title-length").text(70-jQuery(this).val().length);
			});

			jQuery("#mbt_seo_metadesc").keydown(function() {
				if(156-jQuery(this).val().length <= 0 && event.keyCode != 8) { return false; }
				jQuery("#mbt_seo_metadesc-length").text(156-jQuery(this).val().length);
			});
			jQuery("#mbt_seo_metadesc").keyup(function() {
				jQuery("#mbt_seo_metadesc-length").text(156-jQuery(this).val().length);
			});
		});
	</script>

	<table class="form-table mbt_seo_metabox">
		<tbody>
			<tr>
				<th scope="row">
					<label for="mbt_seo_title">SEO Title:</label>
				</th>
				<td>
					<input type="text" placeholder="" id="mbt_seo_title" name="mbt_seo_title" value="" class="large-text"><br>
					<p>Title display in search engines is limited to 70 chars, <span id="mbt_seo_title-length">70</span> chars left.</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mbt_seo_metadesc">Meta Description:</label></th>
				<td>
					<textarea class="large-text" rows="3" id="mbt_seo_metadesc" name="mbt_seo_metadesc"></textarea>
					<p>The <code>meta</code> description will be limited to 156 chars, <span id="mbt_seo_metadesc-length">156</span> chars left.</p>
				</td>
			</tr>
		</tbody>
	</table>
<?php
}

function mbt_save_seo_metabox($post_id)
{
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return;}

	if(get_post_type($post_id) == "mbt_books")
	{
		if(isset($_POST['mbt_seo_title'])) { update_post_meta($post_id, "mbt_seo_title", $_POST['mbt_seo_title']); }
		if(isset($_POST['mbt_seo_metadesc'])) { update_post_meta($post_id, "mbt_seo_metadesc", $_POST['mbt_seo_metadesc']); }
	}
}
add_action('save_post', 'mbt_save_seo_metabox');

/*---------------------------------------------------------*/
/* Affiliates Metabox                                      */
/*---------------------------------------------------------*/

function mbt_affiliates_metabox_ajax() {
	echo('<div class="mbt_affiliate_editor">');
	echo('<button class="mbt_affiliate_remover" style="float:right">Remove</button>');
	$affiliates = mbt_get_affiliates();
	echo($affiliates[$_POST['type']]['editor'](array('type' => $_POST['type'], 'value' => ''), "mbt_affiliate".$_POST['num'], $affiliates));
	echo('</div>');
	die();
}
add_action('wp_ajax_mbt_affiliates_metabox', 'mbt_affiliates_metabox_ajax');

function mbt_affiliates_metabox($post)
{
	wp_nonce_field(plugin_basename(__FILE__), 'mbt_nonce');

	?>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			var adding = false;

			function reset_numbers() {
				jQuery('#mbt_affiliate_editors .mbt_affiliate_editor').each(function(i) {
					jQuery(this).find("input, textarea, select").each(function() {
						jQuery(this).attr('name', jQuery(this).attr('name').replace(/mbt_affiliate\d*\[([A-Za-z0-9]*)\]/, "mbt_affiliate"+i+"[$1]"));
					});
				});
			}

			jQuery('#mbt_affiliate_adder').click(function() {
				if(!adding) {
					adding = true;
					jQuery.post(ajaxurl,
						{
							action: 'mbt_affiliates_metabox',
							type: jQuery('#mbt_affiliate_selector').val(),
							num: 0
						},
						function(response) {
							var element = jQuery(response);
							jQuery("#mbt_affiliate_editors").prepend(element);
							reset_numbers();
							adding = false;
						}
					);
				}
				return false;
			});

			jQuery("#mbt_affiliate_editors").on("click", ".mbt_affiliate_remover", function() {
				jQuery(this).parent().remove();
				reset_numbers();
			});
		});
	</script>

	<?php

	$affiliates = mbt_get_affiliates();
	echo('Choose One:');
	echo('<select id="mbt_affiliate_selector">');
	foreach($affiliates as $slug => $affiliate) {
  		echo('<option value="'.$slug.'">'.$affiliate['name'].'</option>');
  	}
	echo('</select>');
	echo('<button id="mbt_affiliate_adder">Add</button>');

	echo('<div id="mbt_affiliate_editors">');
	$post_affiliates = get_post_meta($post->ID, "mbt_affiliates", true);
	if(!empty($post_affiliates)) {
		for($i = 0; $i < count($post_affiliates); $i++)
		{
			echo('<div class="mbt_affiliate_editor">');
			echo('<button class="mbt_affiliate_remover" style="float:right">Remove</button>');
			echo($affiliates[$post_affiliates[$i]['type']]['editor']($post_affiliates[$i], "mbt_affiliate".$i, $affiliates));
			echo('</div>');
		}
	}
	echo('</div>');
}

function mbt_save_affiliates_metabox($post_id)
{
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !isset($_POST['mbt_nonce']) || !wp_verify_nonce($_POST['mbt_nonce'], plugin_basename(__FILE__))){return;}

	if(get_post_type($post_id) == "mbt_books")
	{
		$mydata = array();
		for($i = 0; isset($_POST['mbt_affiliate'.$i]); $i++)
		{
			$mydata[] = $_POST['mbt_affiliate'.$i];
		}
		update_post_meta($post_id, "mbt_affiliates", $mydata);
	}
}
add_action('save_post', 'mbt_save_affiliates_metabox');
