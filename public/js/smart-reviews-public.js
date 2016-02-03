(function( $ ) {
	'use strict';

	var SmartReviews = {
		defaults: {
			el_mockup_viewport					: '#sr-mockup-viewport',
			el_mockup_wrapper					: '.sr-mockup-wrapper',
			el_mockup_discussion 				: '.sr-mockup-discussion',
			el_mockup_image_wrapper 			: '.sr-mockup-image',
			el_mockup_dots						: '.sr-mockup-dots',
			el_mockup_header					: '#sr-header',
			el_feedback_wrapper 				: '.sr-feedback',
			el_feedback_dot						: '.sr-dot',
			el_feedback_content					: '.sr-feedback-content',
			el_feedback_action_edit				: '.feedback-edit',
			el_feedback_action_delete			: '.feedback-delete',
			el_feedback_action_close			: '.feedback-close',
			el_feedback_comment_form			: '.feedback-comment-form',
			el_feedback_comment_textarea		: '.feedback-field-comment',
			el_feedback_comment_submit			: '.feedback-field-submit',
			el_feedback_wrapper_field			: '.feedback-field-wrapper',
			el_feedback_wrapper_submit			: '.feedback-field-wrapper-submit',
			el_feedback_template				: '#sr-feedback-template',
			el_feedback_loader_wrapper			: '#sr-feedback-loader',
			el_feedback_preload					: '.sr-feedback-preload',
			el_feedback_comment_list_wrapper 	: '.feedback-comment-list',
			el_button_toggle_feedbacks			: '.sr-toggle-feedbacks',
			el_button_toggle_discussion 		: '.sr-toggle-discussion-panel'
		},
		settings: {},
		state: {
			dots: 0
		},
		init: function(options) {
			$.extend(this.settings, this.defaults, options);

			this.setup();
		},
		setup: function() {
			this._loadFeedbacks();
			this._bindElements();
		},
		_getXPercentagePosition: function(x) {
			var that = this;
			var wrapperWidth = $( that.settings.el_mockup_wrapper ).width();

			return ( 100 / wrapperWidth ) * x;
		},
		_getYPercentagePosition: function(y) {
			var that = this;
			var wrapperHeight = $( that.settings.el_mockup_wrapper ).height();

			return ( 100 / wrapperHeight ) * y;
		},
		_loadFeedbacks: function() {
			 var that = this;

			$(that.settings.el_feedback_preload).each(function() {
				var feedback = {
					x 			: $(this).attr('data-x'),
					y 			: $(this).attr('data-y'),
					feedback_id : $(this).attr('data-id')
				};

				var dot = that.addFeedback(feedback);

				dot.removeClass('empty').removeClass('new').addClass('saved');

				dot.find( that.settings.el_feedback_comment_list_wrapper ).append( $( this ).find( '.comments' ).html() );
			});
		},
		_bindElements: function() {
			var that = this;

			// Add Feedback
			$( 'body' ).on('click', this.settings.el_mockup_image_wrapper, function(e) {
				e.preventDefault();

				if ( ! that.settings.feedbacks_enabled )
					return false;

				if ( that.hasDraftFeedback() )
					return false;

				that.removeEmptyFeedbacks();

				var wrapperOffset = $(this).offset();
				var X = e.pageX - wrapperOffset.left;
				var Y = e.pageY - wrapperOffset.top;

				var feedback = {
					x 			: that._getXPercentagePosition(X) + '%',
					y 			: that._getYPercentagePosition(Y) + '%',
					feedback_id : null
				};

				var dot = that.addFeedback(feedback);

				that.openFeedback(dot);
			});

			$( 'body' ).on('click', that.settings.el_feedback_dot, function(e) {
				e.preventDefault();
				e.stopPropagation();

				if ( that.hasDraftFeedback() )
					return false;

				that.openFeedback( $( this ).parents( that.settings.el_feedback_wrapper ) );
			});

			$( 'body' ).on('mouseover', that.settings.el_feedback_dot, function(e) {
				e.preventDefault();

				$( this ).parents( that.settings.el_feedback_wrapper ).addClass('hover');
				that.updateFeedbackOrientation( $( this ).parents( that.settings.el_feedback_wrapper ) );
			}).on('mouseout', that.settings.el_feedback_dot, function(e) {
				e.preventDefault();

				$( this ).parents( that.settings.el_feedback_wrapper ).removeClass('hover');
			});

			$( 'body' ).on('click', that.settings.el_feedback_wrapper, function(e) {
				e.preventDefault();
				e.stopPropagation();
			});

			// Close Feedback
			$( 'body' ).on('click', that.settings.el_feedback_action_close, function(e) {
				e.preventDefault();

				if ( that.hasDraftFeedback() )
					return false;

				that.closeFeedback( $( this ).parents( that.settings.el_feedback_wrapper ) );
			});

			// Delete Feedback
			$( 'body' ).on('click', that.settings.el_feedback_action_delete, function(e) {
				e.preventDefault();

				var dot = $( this ).parents( that.settings.el_feedback_wrapper );

				if ( !dot.hasClass( 'empty' ) ) {
					if ( confirm('Are you sure?' ) != true)
						return false;
				}

				that.deleteFeedback( dot );

			});

			// Activate Comment Submit
			$( 'body' ).on('keyup change', that.settings.el_feedback_comment_textarea, function(e) {
				e.preventDefault();

				if ( e.keyCode == 27 )
					return false;

				var $submit_wrapper = $( this ).parents( that.settings.el_feedback_comment_form ).find( that.settings.el_feedback_wrapper_submit );
				var $feedback_wrapper = $( this ).parents( that.settings.el_feedback_wrapper );

				$feedback_wrapper.removeClass('draft empty');

				// If something is written mark the feedback as "draft"
				if ( $( this ).val() != '' ) {
					$feedback_wrapper.addClass('draft');
					$submit_wrapper.addClass('active');
				}

				// If nothing is written mark the feedback as "empty"
				else {
					if ( ! $feedback_wrapper.hasClass('saved') )
						$feedback_wrapper.addClass('empty');

					$submit_wrapper.removeClass('active');
				}
			});

			// Submit CTRL + Enter
			$( 'body' ).on( 'keydown', that.settings.el_feedback_comment_textarea, function (e) {
				if ( e.metaKey && (e.keyCode == 13 || e.keyCode == 10) ) {

					var dot = $( this ).parents( that.settings.el_feedback_wrapper );

					that.saveFeedback( dot );
				}
			});

			// Submit Click
			$( 'body' ).on( 'click', that.settings.el_feedback_comment_submit, function(e) {
				e.preventDefault();

				var dot = $( this ).parents( that.settings.el_feedback_wrapper );

				that.saveFeedback( dot );
			});

			// Esc Key
			$( document ).on('keydown', function(e) {
				if (e.keyCode == 27) {
					var dot = $( that.settings.el_feedback_wrapper + '.open:not(.saved)' );

					if ( that.isEmptyFeedback( dot ) )
						that.deleteFeedback( dot );

					return false;
				}
			});

			$( 'body' ).on('mouseover', that.settings.el_feedback_dot, function(e) {
				e.preventDefault();

				if ( ! that.settings.feedbacks_enabled )
					return false;

				$( '.ui-draggable' ).draggable('destroy');

				if ( that.hasEmptyFeedback() )
					return false;

				if ( that.hasDraftFeedback() )
					return false;

				$( this ).parents( that.settings.el_feedback_wrapper ).not('.empty').draggable({
					containment: that.settings.el_mockup_wrapper,
					handle: that.settings.el_feedback_dot,
					start: function( e, ui ) {},
					stop: function( e, ui ) {
						var X = that._getXPercentagePosition( ui.position.left ) + '%';
						var Y = that._getYPercentagePosition( ui.position.top ) + '%';
						$(this).css('left', X).attr( 'data-y', Y );
						$(this).css('top', Y).attr( 'data-x', X );
						that.updateFeedbackPosition( $(this) );
						that.openFeedback( $(this) );
					}
				});
			});

			// Prevent unwanted selection caused by the draggable
			$( 'body' ).on('mousedown', that.settings.el_mockup_image_wrapper, function(e) {
				e.preventDefault();
			});

			$( 'body' ).on( 'click', that.settings.el_mockup_header + ' li > a', function(e) {
				$(this).parent().toggleClass('active');
			});

			// Toggle Feedback Button
			$( 'body' ).on( 'click', that.settings.el_button_toggle_feedbacks, function(e) {
				e.preventDefault();
				$( that.settings.el_mockup_viewport ).toggleClass('hide-feedbacks');
			});

			// Toggle Discussion Button
			$( 'body' ).on( 'click', that.settings.el_button_toggle_discussion, function(e) {
				e.preventDefault();

				// Workaround
				if ( $( that.settings.el_mockup_viewport ).hasClass('show-discussion') )
					$( that.settings.el_mockup_discussion ).removeAttr('style');
				else
					setTimeout(function() { $( that.settings.el_mockup_discussion ).css({ 'z-index': '30', 'overflow': 'scroll'}); }, 400);

				setTimeout(function() {
					$( that.settings.el_mockup_viewport ).toggleClass('show-discussion').toggleClass('discussion-hidden');
				}, 100);
			});

		},
		hasEmptyFeedback: function() {
			return $( this.settings.el_feedback_wrapper + '.empty:not(.template)' ).length;
		},
		isEmptyFeedback: function(dot) {
			return dot.hasClass('empty');
		},
		hasDraftFeedback: function() {
			return $( this.settings.el_feedback_wrapper + '.draft:not(.template)' ).length;
		},
		isDraftFeedback: function(dot) {
			return dot.hasClass('draft');
		},
		addFeedback: function(feedback) {
			var that = this;

			var dot = $(that.settings.el_feedback_template).clone()
							.removeAttr('id')
							.removeClass('template')
							.css({
								left: feedback.x,
								top: feedback.y
							});

			$(that.settings.el_mockup_dots).append(dot);
			dot.attr({
				'data-x' : feedback.x,
				'data-y' : feedback.y
			});

			if ( feedback.feedback_id )
				dot.attr('id', feedback.feedback_id);

			// Increase and set dot count
			dot.find( that.settings.el_feedback_dot + ' span' ).html( ++that.state.dots );

			autosize( dot.find( that.settings.el_feedback_comment_textarea ) );

			return dot;
		},
		openFeedback: function(dot) {
			var that = this;

			that.removeEmptyFeedbacks();

			// Hide active feedback
			$( that.settings.el_feedback_wrapper + '.open' ).removeClass('open');

			// Show this feedback
			dot.addClass('open');

			dot.find( that.settings.el_feedback_comment_textarea ).focus();

			dot.removeClass('new');

			that.updateFeedbackOrientation(dot);
		},
		closeFeedback: function(dot) {
			var that = this;

			// Remove dot if marked as "empty"
			if ( dot.hasClass('empty') )
				that.deleteFeedback(dot);
			else
				dot.removeClass('open');
		},
		deleteFeedback: function(dot) {
			dot.remove();
			this.state.dots--;

			if ( typeof dot.attr('id') === 'undefined' )
				return false;

			var feedback_id = dot.attr('id');

			var request = $.ajax({
				url: ajax_url,
				method: 'POST',
				data: {
					action 		: 'delete_feedback',
					post_id		: post_id,
					feedback_id : feedback_id
				},
				dataType: 'json'
			});

			request.done(function( data ) {
				switch ( data.status ) {
					case 'feedback_deleted':
					default:
						break;
				}
			});
		},
		removeEmptyFeedbacks: function() {
			var that = this;

			$( that.settings.el_feedback_wrapper + '.empty:not(.new)').each(function() {
				that.deleteFeedback( $(this) );
			});
		},
		saveFeedback: function(dot) {
			var that = this;

			if ( ! that.settings.feedbacks_enabled )
				return false;

			if ( dot.find( that.settings.el_feedback_comment_textarea ).val() == '' )
				return false;

			var feedback_id = null;
			if ( dot.attr('id') ) {
				feedback_id = dot.attr('id');
			}

			var request = $.ajax({
				url: ajax_url,
				method: 'POST',
				data: {
					action 		: 'save_feedback',
					post_id		: post_id,
					x 			: dot.attr('data-x'),
					y 			: dot.attr('data-y'),
					feedback_id : feedback_id,
					comment 	: dot.find( that.settings.el_feedback_comment_textarea ).val()
				},
				dataType: 'json'
			});

			request.done(function( data ) {
				switch ( data.status ) {
					case 'new_feedback_saved':
						dot.attr( 'id', data.feedback_id );

					case 'feedback_updated':
					default:
						that.addFeedbackComment(data.feedback_id, data.comment);
						dot.addClass( 'saved' ).removeClass( 'draft' );
						break;
				}

				var dot_textarea = dot.find( that.settings.el_feedback_comment_textarea );
				dot_textarea.val('');
				autosize.update( dot_textarea );
			});
		},
		updateFeedbackPosition: function(dot) {
			var that = this;

			if ( ! that.settings.feedbacks_enabled )
				return false;

			if ( typeof dot.attr('id') === 'undefined' )
				return false;

			var feedback_id = dot.attr('id');

			var request = $.ajax({
				url: ajax_url,
				method: 'POST',
				data: {
					action 		: 'update_feedback_position',
					post_id		: post_id,
					x 			: dot.attr('data-x'),
					y 			: dot.attr('data-y'),
					feedback_id : feedback_id
				},
				dataType: 'json'
			});

			request.done(function( data ) {
				switch ( data.status ) {
					case 'feedback_position_updated':
					default:
						dot.addClass( 'saved' ).removeClass( 'draft' );
						break;
				}
			});
		},
		addFeedbackComment: function(feedback_id, feedback_comment) {
			$( '#' + feedback_id ).find( this.settings.el_feedback_comment_list_wrapper ).append( feedback_comment );
		},
		updateFeedbackOrientation: function(dot) {
			var that = this;

			var wrapperW = $(that.settings.el_mockup_wrapper).width();
			var wrapperH = $(that.settings.el_mockup_wrapper).height();
			var dotOffsetR = dot.position().left + dot.find( that.settings.el_feedback_content ).width() + 65;
			var dotOffsetB = dot.position().top + dot.find( that.settings.el_feedback_content ).height() + 65;

			dot.removeClass('invert-horizontal');
			if ( dotOffsetR > wrapperW ) {
				dot.addClass('invert-horizontal');
			}
		}
	};

	$(document).ready(function() {
		SmartReviews.init( window.mockup_options );
	});

})( jQuery );
