jQuery(document).ready(function() {

	/*---------------------------------------------------------*/
	/* Book Display Mode                                       */
	/*---------------------------------------------------------*/

	if(jQuery('#mbt_display_modes').length > 0) {
		var book_display_modes = {};
		try { book_display_modes = JSON.parse(jQuery('#mbt_display_modes').val()); } catch(err) {}

		function update_display_mode() {
			var supports_teaser = false;
			var supports_overview_image = false;

			var display_mode = jQuery('#mbt_display_mode').val();
			if(book_display_modes[display_mode]) {
				var display_mode_supports = book_display_modes[display_mode].supports;
				supports_teaser = display_mode_supports.indexOf('teaser') !== -1;
				supports_overview_image = display_mode_supports.indexOf('overview_image') !== -1;
			}

			if(supports_teaser) {
				jQuery('.mbt_book_teaser_field').show();
			} else {
				jQuery('.mbt_book_teaser_field').hide();
			}

			if(supports_overview_image) {
				jQuery('.mbt_overview_image_field').show();
			} else {
				jQuery('.mbt_overview_image_field').hide();
			}
		}

		update_display_mode();
		jQuery('#mbt_display_mode').change(update_display_mode);
	}

	/*---------------------------------------------------------*/
	/* Buy Buttons Metabox                                     */
	/*---------------------------------------------------------*/

	function mbt_reset_buybutton_numbers() {
		//there needs to be two passes so that ids are never reused, which breaks radio buttons
		jQuery('#mbt_buybutton_editors .mbt_buybutton_editor').each(function(i) {
			jQuery(this).find("div, input, textarea, select").each(function() {
				var ele = jQuery(this);
				if(ele.attr('name')) { ele.attr('name', ele.attr('name').replace(/mbt_buybutton\d*/, "mbt_buybutton_new_"+(i+1))); }
				if(ele.attr('id')) { ele.attr('id', ele.attr('id').replace(/mbt_buybutton\d*/, "mbt_buybutton_new_"+(i+1))); }
			});
		}).each(function() {
			jQuery(this).find("div, input, textarea, select").each(function() {
				var ele = jQuery(this);
				if(ele.attr('name')) { ele.attr('name', ele.attr('name').replace(/mbt_buybutton_new_*/, "mbt_buybutton")); }
				if(ele.attr('id')) { ele.attr('id', ele.attr('id').replace(/mbt_buybutton_new_*/, "mbt_buybutton")); }
			});
		});
	}

	jQuery('#mbt_buybutton_adder').click(function(e) {
		if(!jQuery('#mbt_store_selector').val()){return false;}
		jQuery('#mbt_store_selector').attr('disabled', 'disabled');
		jQuery('#mbt_buybutton_adder').attr('disabled', 'disabled');
		jQuery.post(ajaxurl,
			{
				action: 'mbt_buybuttons_metabox',
				store: jQuery('#mbt_store_selector').val(),
			},
			function(response) {
				jQuery('#mbt_store_selector').removeAttr('disabled');
				jQuery('#mbt_buybutton_adder').removeAttr('disabled');
				element = jQuery(response);
				jQuery("#mbt_buybutton_editors").prepend(element);
				mbt_reset_buybutton_numbers();
			}
		);
		return false;
	});

	jQuery("#mbt_buybutton_editors").on("click", ".mbt_buybutton_remover", function() {
		jQuery(this).parents('.mbt_buybutton_editor').remove();
		mbt_reset_buybutton_numbers();
	});

	jQuery("#mbt_buybutton_editors").sortable({cancel: ".mbt_buybutton_editor_content,.mbt_buybutton_display_selector", stop: function(){mbt_reset_buybutton_numbers();}});

	// need to undisable form inputs or they will not be saved
	jQuery('form#post').submit(function() {
		jQuery("#mbt_buybutton_editors .mbt_buybutton_editor textarea").removeAttr("disabled");
		jQuery("#mbt_unique_id_isbn").removeAttr("disabled");
		jQuery("#mbt_unique_id_asin").removeAttr("disabled");
	});

	/*---------------------------------------------------------*/
	/* Details Metabox                                         */
	/*---------------------------------------------------------*/

	// book image
	jQuery("#mbt_book_image_id").change(function() {
		jQuery.post(ajaxurl,
			{
				action: 'mbt_book_image_preview',
				image_id: jQuery('#mbt_book_image_id').val(),
			},
			function(response) {
				jQuery('#mbt_metadata .mbt-book-image').remove();
				if(response) {
					jQuery('#mbt_metadata .mbt-cover-image-title').after(jQuery('<img src="'+response+'" class="mbt-book-image">'));
				}
			}
		);
	});

	// kindle instant preview asin warning
	if(jQuery('#mbt_show_instant_preview_asin_warning').length > 0) {
		jQuery('#mbt_unique_id_asin').change(function() { jQuery('#mbt_show_instant_preview_asin_warning').remove(); });
	}

	/*---------------------------------------------------------*/
	/* Overview Metabox                                        */
	/*---------------------------------------------------------*/

	jQuery("#mbt_overview_image").change(function() {
		jQuery.post(ajaxurl,
			{
				action: 'mbt_overview_image_preview',
				image_id: jQuery('#mbt_overview_image').val(),
			},
			function(response) {
				jQuery('.mbt_overview_image_preview').html(response);
			}
		);
	});

	/*---------------------------------------------------------*/
	/* Endorsements Metabox                                    */
	/*---------------------------------------------------------*/

	var editors = jQuery('.mbt_endorsements_editors');
	var endorsement_id = 0;

	function new_endorsement() {
		var id = endorsement_id++;

		src = '';
		src += '<div class="mbt_endorsement_editor">';
		src += '	<div class="mbt_endorsement_header">';
		src += '		<button class="mbt_endorsement_remover button">Remove</button>';
		src += '		<div style="clear:both"></div>';
		src += '	</div>';
		src += '	<div class="mbt_endorsement_content">';
		src += '		<div class="mbt_endorsement_image_field">';
		src += '			<label class="mbt_endorsement_image_title">Image:</label>';
		src += '			<div class="mbt_endorsement_image_preview_'+id+'"></div>';
		src += '			<input type="hidden" class="mbt_endorsement_image" id="mbt_endorsement_image_'+id+'" value="" />';
		src += '			<input class="mbt_endorsement_image_upload button" data-upload-property="id" data-upload-target="mbt_endorsement_image_'+id+'" type="button" value="Choose" />';
		src += '			<input class="mbt_endorsement_image_upload_clear button" data-upload-target="mbt_endorsement_image_'+id+'" type="button" value="X" />';
		src += '		</div>';
		src += '		<div class="mbt_endorsement_content_field">';
		src += '			<label>Endorsement:<br><textarea class="mbt_endorsement_content_text"></textarea></label>';
		src += '		</div>';
		src += '		<div class="mbt_endorsement_name_field">';
		src += '			<label>Name: <input type="text" class="mbt_endorsement_name" value="" autocomplete="off"></label>';
		src += '		</div>';
		src += '		<div class="mbt_endorsement_name_url_field">';
		src += '			<label>Name URL: <input type="text" class="mbt_endorsement_name_url" value="" autocomplete="off"></label>';
		src += '		</div>';
		src += '		<div style="clear:both"></div>';
		src += '	</div>';
		src += '</div>';

		new_item = jQuery(src);
		editors.prepend(new_item);

		new_item.find('.mbt_endorsement_image_upload').mbt_upload_button();
		new_item.find('.mbt_endorsement_image_upload_clear').mbt_upload_clear_button();
		new_item.find('.mbt_endorsement_image').change(function() {
			jQuery.post(ajaxurl,
				{
					action: 'mbt_endorsement_image_preview',
					image_id: jQuery('#mbt_endorsement_image_'+id).val(),
				},
				function(response) {
					jQuery('.mbt_endorsement_image_preview_'+id).empty();
					if(response) {
						jQuery('.mbt_endorsement_image_preview_'+id).html('<img src="'+response+'">');
					}
				}
			);
		}).trigger('change');

		return new_item;
	}

	function load_endorsements() {
		var items = JSON.parse(jQuery('.mbt_endorsements').val());
		for(var i = items.length - 1; i >= 0; i--) {
			var element = new_endorsement();

			element.find('.mbt_endorsement_image').val(items[i]['image_id']).trigger('change');
			element.find('.mbt_endorsement_content_text').val(items[i]['content']);
			element.find('.mbt_endorsement_name').val(items[i]['name']);
			element.find('.mbt_endorsement_name_url').val(items[i]['name_url']);
		};
	}

	function save_endorsements() {
		var items = [];
		editors.find('.mbt_endorsement_editor').each(function(i, e) {
			var element = jQuery(e);
			var new_item = {}

			new_item['image_id'] = element.find('.mbt_endorsement_image').val();
			new_item['content'] = element.find('.mbt_endorsement_content_text').val();
			new_item['name'] = element.find('.mbt_endorsement_name').val();
			new_item['name_url'] = element.find('.mbt_endorsement_name_url').val();

			items.push(new_item);
		});
		jQuery('.mbt_endorsements').val(JSON.stringify(items));
	}

	if(jQuery('.mbt_endorsements').length > 0) {
		jQuery('.mbt_endorsement_adder').click(new_endorsement);
		editors.sortable({cancel: '.mbt_endorsement_content,.mbt_endorsement_title,.mbt_endorsement_remover'});
		editors.on('click', '.mbt_endorsement_remover', function() { jQuery(this).parents('.mbt_endorsement_editor').remove(); });
		jQuery('input[type="submit"]').click(save_endorsements);
		load_endorsements();
	}

	/*---------------------------------------------------------*/
	/* Authors Metabox                                         */
	/*---------------------------------------------------------*/

	function update_main_author_link() {
		authors = [];
		jQuery('input[name="tax_input[mbt_author][]"]:checked').each(function(i, e) {
			authors.push(jQuery(e).val());
		});
		console.log(authors);
		jQuery.post(ajaxurl,
			{
				action: 'mbt_main_author_url',
				authors: authors,
			},
			function(response) {
				console.log(response);
				jQuery('#mbt_main_author_link').attr('href', response);
			}
		);
	}
	jQuery('input[name="tax_input[mbt_author][]"]').change(update_main_author_link);
	update_main_author_link();

});
