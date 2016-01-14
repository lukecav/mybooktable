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

	/*---------------------------------------------------------*/
	/* Authormedia Shortcode Inserter Tracking                 */
	/*---------------------------------------------------------*/

	jQuery('.authormedia-insert-shortcode-button').on('click', function() {
		mbt_track_event('authormedia_shortcode_inserter_open');
	});
	setTimeout(function() {
		if(window.authormedia_shortcode_form_events) {
			window.authormedia_shortcode_form_events.on('insert', function(shortcode) {
				mbt_track_event('authormedia_shortcode_insert', {shortcode: shortcode});
			});
		}
	}, 0);

});

function mbt_track_event(event_name, instance, after) {
	if(typeof after === 'undefined' && _.isFunction(instance)) { after = instance; instance = false; }
	if(!_.isObject(instance)) { instance = false; }
	var jqxhr = jQuery.post(ajaxurl, {action: 'mbt_track_event', event_name: event_name, instance: JSON.stringify(instance)});
	if(typeof after !== 'undefined') { jqxhr.always(after); }
}
