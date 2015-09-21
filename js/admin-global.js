jQuery(document).ready(function() {

	/*---------------------------------------------------------*/
	/* Wordpress Sidebar Link                                  */
	/*---------------------------------------------------------*/

	jQuery('a[href="admin.php?page=mbt_upgrade_link"]').on('click', function() { jQuery(this).attr('target', '_blank'); });

	/*---------------------------------------------------------*/
	/* Ajax Event Tracking                                     */
	/*---------------------------------------------------------*/

	jQuery('*[data-mbt-track-event]').click(function() {
		mbt_track_event(jQuery(this).attr('data-mbt-track-event'));
	});

	jQuery('a[data-mbt-track-event-override]').click(function(e) {
		if(event.which == 1) {
			var element = jQuery(this);
			mbt_track_event(element.attr('data-mbt-track-event-override'), function() {
				window.location = element.attr('href');
			});
			return false;
		} else {
			mbt_track_event(jQuery(this).attr('data-mbt-track-event-override'));
		}
	});
});

function mbt_track_event(event_name, after) {
	var jqxhr = jQuery.post(ajaxurl, {action: 'mbt_track_event', event_name: event_name});
	if(typeof after !== 'undefined') { jqxhr.always(after); }
}
