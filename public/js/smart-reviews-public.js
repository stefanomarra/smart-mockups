(function( $ ) {
	'use strict';

	var SmartReviews = {
		defaults: {
			el_mockup_wrapper				: '.sr-mockup-wrapper',
			el_mockup_dots					: '.sr-mockup-dots',
			el_feedback_wrapper 			: '.dot-feedback',
			el_feedback_dot					: '.dot',
			el_feedback_content				: '.feedback-content',
			el_feedback_action_edit			: '.feedback-edit',
			el_feedback_action_delete		: '.feedback-delete',
			el_feedback_action_close		: '.feedback-close',
			el_feedback_comment_form		: '.feedback-comment-form',
			el_feedback_comment_textarea	: '.feedback-field-comment',
			el_feedback_comment_submit		: '.feedback-field-submit',
			el_feedback_wrapper_field		: '.feedback-field-wrapper',
			el_feedback_wrapper_submit		: '.feedback-field-wrapper-submit',
			el_dot_template					: '#dot_template'
		},
		settings: {},
		init: function(options) {
			$.extend(this.settings, this.defaults, options);

			this.setup();
		},
		setup: function() {
			this._bindElements();
		},
		_bindElements: function() {
			var that = this;

			// Add Feedback
			$( 'body' ).on('click', this.settings.el_mockup_wrapper, function(e) {
				e.preventDefault();

				if ( that.hasDraftFeedback() )
					return false;

				that.removeEmptyFeedbacks();

				var wrapperOffset = $(this).offset();
				var X = e.pageX - wrapperOffset.left;
				var Y = e.pageY - wrapperOffset.top;

				var dot = $(that.settings.el_dot_template).clone()
								.removeAttr('id')
								.removeClass('template')
								.css({
									left: X,
									top: Y
								});

				$(that.settings.el_mockup_dots).append(dot);
				autosize( dot.find( that.settings.el_feedback_comment_textarea ) );

				that.openFeedback(dot);
			});

			$( 'body' ).on('click', that.settings.el_feedback_dot, function(e) {
				e.preventDefault();
				e.stopPropagation();

				that.openFeedback( $( this ).parents( that.settings.el_feedback_wrapper ) );
			});

			$( 'body' ).on('mouseover', that.settings.el_feedback_dot, function(e) {
				e.preventDefault();

				$( this ).parents( that.settings.el_feedback_wrapper ).addClass('hover');
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

				that.deleteFeedback( $( this ).parents( that.settings.el_feedback_wrapper ) );
			});

			// Activate Comment Submit
			$( 'body' ).on('keyup change', that.settings.el_feedback_comment_textarea, function(e) {
				e.preventDefault();

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
					$feedback_wrapper.addClass('empty');
					$submit_wrapper.removeClass('active');
				}
			});

			// Submit CTRL + Enter
			$( 'body' ).on( 'keydown', that.settings.el_feedback_comment_textarea, function (e) {
				if ( e.metaKey && (e.keyCode == 13 || e.keyCode == 10) ) {

					// If the feedback is marked as "draft", save the feedback
					if ( that.isDraftFeedback( $( this ).parents( that.settings.el_feedback_wrapper ) ) )
						that.saveFeedback();
				}
			});

			$( 'body' ).on( 'click', that.settings.el_feedback_comment_submit, function(e) {
				e.preventDefault();

				// If the feedback is marked as "draft", save the feedback
				if ( that.isDraftFeedback( $( this ).parents( that.settings.el_feedback_wrapper ) ) )
					that.saveFeedback();
			});

		},
		hasDraftFeedback: function() {
			return $( this.settings.el_feedback_wrapper + '.draft' ).length;
		},
		isDraftFeedback: function(dot) {
			return dot.hasClass('draft');
		},
		openFeedback: function(dot) {
			var that = this;

			that.removeEmptyFeedbacks();

			// Hide active feedback
			$( that.settings.el_feedback_wrapper + '.open' ).removeClass('open');

			// Show this feedback
			dot.addClass('open');

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
		},
		removeEmptyFeedbacks: function() {
			var that = this;

			$( that.settings.el_feedback_wrapper + '.empty:not(.new)').each(function() {
				that.deleteFeedback( $(this) );
			});
		},
		saveFeedback: function() {
			console.log('SAVE FEEDBACK');
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
		SmartReviews.init();
	});

})( jQuery );
