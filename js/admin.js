jQuery(document).ready(function() {

	/*---------------------------------------------------------*/
	/* Feedback Boxes                                          */
	/*---------------------------------------------------------*/

	jQuery('.mbt_feedback_refresh').mbt_feedback();

	/*---------------------------------------------------------*/
	/* Settings Page                                           */
	/*---------------------------------------------------------*/

	jQuery('#mbt-tabs').tabs({active: jQuery('#mbt_current_tab').val()-1});
	jQuery('#mbt-help-link').off();
	jQuery('.mbt-accordion').accordion({collapsible: true, active: false, heightStyle: 'content'});

 	jQuery('#mbt_settings_form input[type="submit"]').click(function() { jQuery('#mbt_current_tab').val(jQuery(this).parents('.mbt-tab').attr('id').substring(8)); });

	/*---------------------------------------------------------*/
	/* Help Page                                               */
	/*---------------------------------------------------------*/

	if(jQuery('.mbt_help #mbt_selected_tutorial_video').length > 0) {
		jQuery('.mbt_help #mbt_video_'+jQuery('.mbt_help #mbt_selected_tutorial_video').val()).show();
		jQuery('html, body').animate({scrollTop: jQuery('.mbt_help .mbt_video_tutorials').offset().top-42}, 2000);
	} else {
		jQuery('.mbt_help .mbt_video_display .mbt_video:first').show();
	}

	jQuery('.mbt_help .mbt_video_selector a').click(function() {
		var video_id = jQuery(this).attr('data-video-id');
		jQuery('.mbt_help .mbt_video_display .mbt_video').hide().detach().appendTo(jQuery('.mbt_help .mbt_video_display'));
		jQuery('#'+video_id).show();
		return false;
	});

	/*---------------------------------------------------------*/
	/* Media Upload Buttons                                    */
	/*---------------------------------------------------------*/

	jQuery.fn.mbt_upload_button = function() {
		jQuery(this).each(function(i, e) {
			var file_frame;

			var element = jQuery(this);

			element.on('click', function(event) {

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if(file_frame) {
					file_frame.open();
					return;
				}

				// Create the media frame.
				var options = {
					button: { text: mbt_media_upload_i18n.select },
					multiple: false  // Set to true to allow multiple files to be selected
				};
				if(element.attr('data-upload-title')) { options.title = element.attr('data-upload-title'); }
				file_frame = wp.media.frames.file_frame = wp.media();

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();

					// Save the desired data
					var desired_data = 'url';
					if(element.attr('data-upload-property')) { desired_data = element.attr('data-upload-property'); }
					jQuery('#'+element.attr('data-upload-target')).val(attachment[desired_data]).trigger('change');
				});

				// Finally, open the modal
				file_frame.open();
			});
		});
	};

	jQuery('.mbt_upload_button').mbt_upload_button();

	/*---------------------------------------------------------*/
	/* Book Import Page                                        */
	/*---------------------------------------------------------*/

	jQuery('.mbt-book-importer .import-submit').click(function(e) {
		var el = jQuery(this);
		setTimeout(function() { el.attr('disabled', 'disabled'); }, 0);
		el.after('<div id="mbt-book-import-spinner"></div>');
	});

	/*---------------------------------------------------------*/
	/* Review Checker                                          */
	/*---------------------------------------------------------*/

	function mbt_reviews_box_display() {
		jQuery('.mbt-check-reviews-begin').show();
		jQuery('.mbt-check-reviews-checking').hide();
		jQuery('.mbt-check-reviews-results').hide();

		if(jQuery('input[name=mbt_reviews_box]:checked').val() == 'none') {
			jQuery('.mbt-check-reviews').hide();
		} else {
			jQuery('.mbt-check-reviews').show();
		}
	}

	jQuery('input[name=mbt_reviews_box]:radio').change(mbt_reviews_box_display);
	mbt_reviews_box_display();

	jQuery('.mbt-check-reviews-button').click(function() {
		jQuery('.mbt-check-reviews-begin').hide();
		jQuery('.mbt-check-reviews-checking').show();
		jQuery.post(ajaxurl,
			{
				action: 'mbt_check_reviews',
				reviews_type: jQuery('input[name=mbt_reviews_box]:checked').val(),
			},
			function(response) {
				jQuery('.mbt-check-reviews-checking').hide();
				jQuery('.mbt-check-reviews-results').show().html(response);
			}
		);
	});
});

/*---------------------------------------------------------*/
/* Feedback Boxes                                          */
/*---------------------------------------------------------*/

function mbt_do_feedback_colorize(element) {
	var feedback = element.parent().find('.mbt_feedback').html();
	element.removeClass('mbt_admin_input_success');
	element.removeClass('mbt_admin_input_failure');
	element.removeClass('mbt_admin_input_warning');
	if(feedback.indexOf('mbt_admin_message_success') !== -1) {
		element.addClass('mbt_admin_input_success');
	} else if(feedback.indexOf('mbt_admin_message_failure') !== -1) {
		element.addClass('mbt_admin_input_failure');
	} else if(feedback.indexOf('mbt_admin_message_warning') !== -1) {
		element.addClass('mbt_admin_input_warning');
	}
}

function mbt_make_feedback_spinner(element) {
	var loading_size = {'width': 18, 'height': 18};
	if(element.children().length > 0) {
		child = jQuery(element.children()[0]);
		loading_size = {'width': Math.max(child.width(), loading_size['width']), 'height': Math.max(child.height(), loading_size['height'])};
	}
	element.empty().append(jQuery('<div class="mbt_feedback_loading"><div class="mbt_feedback_spinner"></div></div>').css(loading_size));
}

function mbt_do_feedback_refresh(element) {
	if(!element.attr('disabled')) {
		element.attr('disabled', 'disabled');
		if(element.attr('type') == 'radio') { jQuery('input[name='+element.attr('name')+']').attr('disabled', 'disabled'); }
		var feedback = element.parent().find('.mbt_feedback');
		mbt_make_feedback_spinner(feedback);

		var data = null;
		if(element.attr('data-element') === 'self') {
			data = element.val();
		} else if(element.attr('data-element').search(",") === -1) {
			data = jQuery('#'+element.attr('data-element')).val();
		} else {
			elements = element.attr('data-element').split(",");
			data = {};
			for(var i = elements.length - 1; i >= 0; i--) {
				data[elements[i]] = jQuery('#'+elements[i]).val();
			}
		}

		jQuery.post(ajaxurl,
			{
				action: element.attr('data-refresh-action'),
				data: data,
			},
			function(response) {
				element.removeAttr('disabled');
				if(element.attr('type') == 'radio') { jQuery('input[name='+element.attr('name')+']').removeAttr('disabled', 'disabled'); }
				feedback.html(response);
				if(element.hasClass('mbt_feedback_colorize')) { mbt_do_feedback_colorize(element); }
			}
		);
	}
}

jQuery.fn.mbt_feedback = function() {
	jQuery(this).each(function(i, e) {
		var element = jQuery(this);

		if(element.hasClass('mbt_feedback_refresh_initial')) { mbt_do_feedback_refresh(element); }
		if(element.hasClass('mbt_feedback_colorize')) { mbt_do_feedback_colorize(element); }

		if(element.prop("tagName") == "DIV") {
			element.click(function() {
				mbt_do_feedback_refresh(element);
				return false;
			});
		} else {
			element.change(function() { mbt_do_feedback_refresh(element); });
		}
	});
}
