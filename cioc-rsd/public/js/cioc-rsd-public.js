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
	var start_cm_unfilled_detector = function() {
		var handle_submit = function(evt) {
			var self = $(this), unfilled_cm = self.find("select[data-unfilled-prompt=true]");
			if (!unfilled_cm.length) {
				return;
			}
			
			if (unfilled_cm.val()) {
				return;
			}

			evt.preventDefault();
			evt.stopPropagation()	


			var modal = $('#cm-unfilled-prompt');
			if (modal.length === 0) {
				var newdiv = $('<div>').html('<div class="modal fade" id="cm-unfilled-prompt" tabindex="-1" role="dialog" aria-labelledby="cm-unfilled-prompt-label">' +
				'	 <div class="modal-dialog" role="document">' +
					   '<div class="modal-content">' +
						'<div class="modal-header">' +
							'<h4 class="modal-title" id="cm-unfilled-prompt-label">No Community Selected</h4>' +
						'</div>' +
						'<div class="modal-body">' +
							'Your search did not include a community selection. Please select one from below:' + 
							'<div class="ciocrsd-form-input" id="cm-unfilled-community-placeholder">' + 
							'</div>' +
						'</div>' +
						'<div class="modal-footer">' +
							'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
							'<button type="button" class="btn btn-primary" id="cm-unfilled-prompt-search-button">Search</button>' +
						'</div>' +
					'</div>' +
				   '</div>' +
				'</div>');
				$('body').append(newdiv);
				modal = $('#cm-unfilled-prompt').modal({show: false});
				$('#cm-unfilled-prompt-search-button').on('click', function() {
					unfilled_cm.val($('#cm-unfilled-selector').val());
					self[0].submit();
				});
				
			}
			$('#cm-unfilled-community-placeholder').empty().append(unfilled_cm.clone().attr('id', 'cm-unfilled-selector'));
			modal.modal('show');



		};
		$(document).on('submit', 'form', handle_submit)

	};
	$(function() {
		start_cm_unfilled_detector()
		var map_canvas = $('#map_canvas'), 
			api_key = $('.ciocrsd-record-detail').data('mapsKey');
		if (!map_canvas.length || !api_key) {
			return
		}

		window['ciocrsd_start_map'] = function() {

			var myLatlng = new google.maps.LatLng(map_canvas.attr('latitude'), map_canvas.attr('longitude')),
			mapOptions = {
				center: myLatlng,
				zoom: 13,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

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
