(function( $ ) {
	'use strict';

	$(document).ready(function() {

		var checkRequire = function() {

			$('.form-table tr[data-require]').each(function() {
				var require = $(this).data('require');
				var require_val = null;

				if ( $(require).is(':checkbox') )
					require_val = $(require).prop('checked');
				else
					require_val = $(require).val();

				if ( require_val != '' )
					$(this).show();
				else
					$(this).hide();
			});
		}

		checkRequire();
		$( 'body' ).on('change', 'input, select', checkRequire);

		// Initialize wp color picker
		$( '.wp-color-picker' ).wpColorPicker();

		// Instantiates the variable that holds the media library frame.
		var media_frame;

		// Bind load media buttons
		$( 'body' ).on('click', '.load_media', function(e){

			var that = this;

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

				var target = $( that ).attr('data-target');
				var preview = $( that ).attr('data-preview');

				// Grabs the attachment selection and creates a JSON representation of the model.
				var attachment = media_frame.state().get( 'selection' ).first().toJSON();

				// Sends the attachment URL to our custom image input field.
				$( target ).val( attachment.id );
				$( preview ).attr( 'src', attachment.url ).removeClass('hide');
			});

			// Opens the media library frame.
			media_frame.open();
		});

	});

})( jQuery );
