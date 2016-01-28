(function( $ ) {
	'use strict';

	$(document).ready(function() {

		// Instantiates the variable that holds the media library frame.
		var media_frame;

		// Bind load media buttons
		$( 'body' ).on('click', '#mockup-add-image-button', function(e){

			// Prevents the default action from occuring.
			e.preventDefault();

			// If the frame already exists, re-open it.
			if ( media_frame ) {
				media_frame.open();
				return;
			}

			// Sets up the media library frame.
			media_frame = wp.media.frames.media_frame = wp.media({
				title: "Choose or Upload an Image",
				button: { text:  'Use this image' },
				library: { type: 'image' }
			});

			// Runs when an image is selected.
			media_frame.on('select', function(){

				// Grabs the attachment selection and creates a JSON representation of the model.
				var attachment = media_frame.state().get( 'selection' ).first().toJSON();

				// Sends the attachment URL to our custom image input field.
				$( '#mockup_image' ).val( attachment.id );
				$( '.mockup-image-src' ).attr( 'src', attachment.url ).removeClass('hide');
			});

			// Opens the media library frame.
			media_frame.open();
		});
	});

})( jQuery );
