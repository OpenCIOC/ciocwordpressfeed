(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(function() {
		var map_canvas = $('#map_canvas'), 
			api_key = $('.ciocrsd-record-detail').data('mapsKey');
		if (!map_canvas.length || !api_key) {
			return
		}

		window['ciocrsd_start_map'] = function() {

			var myLatlng = new google.maps.LatLng(parseFloat(map_canvas.attr('latitude')), parseFloat(map_canvas.attr('longitude'))),
			mapOptions = {
				center: myLatlng,
				zoom: 13,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			console.log(myLatlng, myLatlng.lat(), myLatlng.lng());

			map_canvas.show();

			var map = new google.maps.Map(map_canvas[0], mapOptions);
			var marker = new google.maps.Marker({
				position: myLatlng,
				map: map
			});

			return;
		};

		$.getScript('//maps.googleapis.com/maps/api/js?v=3&key=' + api_key + '&sensor=false&callback=ciocrsd_start_map');
	});

})( jQuery );
