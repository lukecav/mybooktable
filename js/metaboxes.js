jQuery(document).ready(function() {

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
	/* Book Image                                              */
	/*---------------------------------------------------------*/

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
		});

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

	jQuery('.mbt_endorsement_adder').click(new_endorsement);
	editors.sortable({cancel: '.mbt_endorsement_content,.mbt_endorsement_title,.mbt_endorsement_remover'});
	editors.on('click', '.mbt_endorsement_remover', function() { jQuery(this).parents('.mbt_endorsement_editor').remove(); });
	jQuery('input[type="submit"]').click(save_endorsements);
	load_endorsements();

});
