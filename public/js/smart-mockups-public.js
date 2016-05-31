(function( $ ) {
	'use strict';

	var SmartMockups = {
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
			el_feedback_action_prev				: '.feedback-prev',
			el_feedback_action_next				: '.feedback-next',
			el_feedback_comment_form			: '.feedback-comment-form',
			el_feedback_comment_textarea		: '.feedback-field-comment',
			el_feedback_guest_display_name 	 	: '.feedback-field-guest-display-name',
			el_feedback_comment_submit			: '.feedback-field-submit',
			el_feedback_wrapper_field			: '.feedback-field-wrapper',
			el_feedback_wrapper_submit			: '.feedback-field-wrapper-submit',
			el_feedback_template				: '#sr-feedback-template',
			el_feedback_loader_wrapper			: '#sr-feedback-loader',
			el_feedback_preload					: '.sr-feedback-preload',
			el_feedback_comment_list_wrapper 	: '.feedback-comment-list',
			el_discussion_comment_textarea		: '.discussion-field-comment',
			el_discussion_comment_form			: '.discussion-comment-form',
			el_discussion_wrapper_submit		: '.discussion-field-wrapper-submit',
			el_discussion_comment_submit		: '.discussion-field-submit',
			el_discussion_comment_list_wrapper 	: '.discussion-comment-list',
			el_approval_signature 				: '.sr-approval-signature-input',
			el_approval_submit 					: '.sr-approval-signature-submit',
			el_button_toggle_feedbacks			: '.sr-toggle-feedbacks',
			el_button_toggle_discussion 		: '.sr-toggle-discussion-panel'
		},
		settings: {},
		user_feedbacks: [],
		state: {
			dots: 0
		},
		init: function(options, user_feedbacks) {
			$.extend(this.settings, this.defaults, options);

			this.user_feedbacks = user_feedbacks;

			this.setup();
		},
		setup: function() {
			var that = this;

			// On image load
			$( that.settings.el_mockup_image_wrapper + ' img' ).one('load', function() {
				$( that.settings.el_mockup_viewport ).addClass( 'loaded discussion-hidden' );
				that._loadFeedbacks();
				that._bindElements();
			}).each(function() {
				if(this.complete) $(this).load();
			});

			$('[data-tip]').tipr({mode: 'bottom'});
		},
		_scrollToBottom: function( $context, el ) {
			var that = this;
			setTimeout(function() {
				var height = 200 + ( $context.find( el + ' li' ).length * 100 );
				$context.find( el ).animate({
					scrollTop: height
				}, 300);
			}, 150);
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

			$( that.settings.el_feedback_preload ).each(function() {
				var feedback = {
					x 			: $(this).attr('data-x'),
					y 			: $(this).attr('data-y'),
					feedback_id : $(this).attr('data-id'),
					is_owner 	: parseInt( $(this).attr('data-is-owner') ),
					can_delete 	: parseInt( $(this).attr('data-user-can-delete') ),
					class 		: 'preloaded'
				};

				var dot = that.addFeedback(feedback);

				dot.removeClass('empty').removeClass('new').addClass('saved');

				dot.find( that.settings.el_feedback_comment_list_wrapper + ' ul' ).append( $( this ).find( '.comments' ).html() );

			}).promise().done(function() {

				// For each preloaded feedback, add class .loaded and remove class .preloaded
				$( that.settings.el_feedback_wrapper + '.preloaded' ).each(function(i, el) {
					setTimeout(function() {
						$( el ).addClass('loaded');
						setTimeout( function() { $( el ).removeClass( 'preloaded' ); }, ((100*(i+1))+100) );
					}, (100*(i+1)) );
				});
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
					is_owner 	: 1,
					can_delete 	: 1,
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

			// prev/next Feedback Actions
			$( 'body' ).on('click', that.settings.el_feedback_action_prev + ',' + that.settings.el_feedback_action_next, function(e) {
				e.preventDefault();

				var feedback = $( this ).parents( that.settings.el_feedback_wrapper );

				if ( $( this ).hasClass( that.settings.el_feedback_action_prev.replace( '.','' ) ) )
					var target_feedback = feedback.prev();
				else
					var target_feedback = feedback.next();

				$('html, body').animate({
					scrollTop: target_feedback.offset().top - $( that.settings.el_mockup_header ).height() - 40
				}, 250, function () {
					that.openFeedback( target_feedback );
				});

			});

			// Close Feedback
			$( 'body' ).on('click', that.settings.el_feedback_action_close, function(e) {
				e.preventDefault();

				var feedback = $( this ).parents( that.settings.el_feedback_wrapper );

				if ( that.hasDraftFeedback() && !that.isSavedFeedback( feedback ) )
					return false;

				that.closeFeedback( feedback );
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

			// Activate Feedback Comment Submit
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

			// Activate Discussion Comment Submit
			$( 'body' ).on('keyup change', that.settings.el_discussion_comment_textarea, function(e) {
				e.preventDefault();

				if ( e.keyCode == 27 )
					return false;

				var $submit_wrapper = $( this ).parents( that.settings.el_discussion_comment_form ).find( that.settings.el_discussion_wrapper_submit );
				var $discussion_wrapper = $( this ).parents( that.settings.el_mockup_discussion );

				$discussion_wrapper.removeClass('draft empty');

				// If something is written mark the feedback as "draft"
				if ( $( this ).val() != '' ) {
					$discussion_wrapper.addClass('draft');
					$submit_wrapper.addClass('active');
				}

				// If nothing is written mark the feedback as "empty"
				else {
					if ( ! $discussion_wrapper.hasClass('saved') )
						$discussion_wrapper.addClass('empty');

					$submit_wrapper.removeClass('active');
				}
			});

			// Feedback CTRL + Arrow Left/Right
			$( 'body' ).on( 'keydown', that.settings.el_feedback_wrapper + '.open:not(.empty)', function (e) {

				var arrow = {left: 37, right: 39};
				var feedback = $( that.settings.el_feedback_wrapper + '.open' );

				if ( that.hasSavedFeedback() && !that.isDraftFeedback( feedback ) && ( e.keyCode == arrow.left || e.keyCode == arrow.right) ) {

					e.preventDefault();


					if ( e.metaKey ) {

						switch ( e.keyCode ) {
							case arrow.left:
								if ( !feedback.is(':first-of-type') ) {
									var target_feedback = feedback.prev();
								}
								break;

							case arrow.right:
								if ( !feedback.is(':last-of-type') ) {
									var target_feedback = feedback.next();
								}
								break;

							default:
									var target_feedback = null;
								break;
						}

						if ( target_feedback ) {
							$('html, body').animate({
								scrollTop: target_feedback.offset().top - $( that.settings.el_mockup_header ).height() - 40
							}, 250, function () {
								that.openFeedback( target_feedback );
							});
						}
					}
				}
			});

			// Submit Feedback CTRL + Enter
			$( 'body' ).on( 'keydown', that.settings.el_feedback_comment_textarea, function (e) {
				if ( e.metaKey && (e.keyCode == 13 || e.keyCode == 10) ) {

					var dot = $( this ).parents( that.settings.el_feedback_wrapper );

					that.saveFeedback( dot );
				}
			});

			// Submit Feedback Click
			$( 'body' ).on( 'click', that.settings.el_feedback_comment_submit, function(e) {
				e.preventDefault();

				var dot = $( this ).parents( that.settings.el_feedback_wrapper );

				that.saveFeedback( dot );
			});

			// Submit Discussion CTRL + Enter
			$( 'body' ).on( 'keydown', that.settings.el_discussion_comment_textarea, function (e) {
				if ( e.metaKey && (e.keyCode == 13 || e.keyCode == 10) ) {

					that.saveDiscussionComment();
				}
			});

			// Submit Discussion Click
			$( 'body' ).on( 'click', that.settings.el_discussion_comment_submit, function(e) {
				e.preventDefault();

				that.saveDiscussionComment();
			});

			// Submit Discussion Click
			$( 'body' ).on( 'click', that.settings.el_approval_submit, function(e) {
				e.preventDefault();

				that.saveApproval();
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

				// Open Discussion Panel
				if ( !$( that.settings.el_mockup_viewport ).hasClass('show-discussion') ) {
					setTimeout(function() {
						$( that.settings.el_mockup_discussion ).css({ 'z-index': '30'});
						autosize( $( that.settings.el_discussion_comment_textarea ) );
						$( that.settings.el_discussion_comment_textarea ).focus();
						$( that.settings.el_discussion_comment_list_wrapper ).perfectScrollbar();
						that._scrollToBottom( $(that.settings.el_mockup_discussion), that.settings.el_discussion_comment_list_wrapper );
					}, 400);
				}
				// Close Discussion Panel
				else {
					$( that.settings.el_mockup_discussion ).removeAttr('style');
				}

				setTimeout(function() {
					$( that.settings.el_mockup_viewport ).toggleClass('show-discussion').toggleClass('discussion-hidden');
				}, 100);
			});

			$( 'body' ).on('click', that.settings.el_feedback_comment_list_wrapper + ' li a, ' + that.settings.el_discussion_comment_list_wrapper + ' li a', function(e) {
				e.preventDefault();
				window.open( $(this).attr('href'), '_blank' );
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
		isSavedFeedback: function(dot) {
			return dot.hasClass('saved');
		},
		hasSavedFeedback: function() {
			return $( this.settings.el_feedback_wrapper + '.saved:not(.template)' ).length;
		},
		isGuestDisplayNameRequired: function() {
			if ( this.settings.guest_enabled )
				return false;

			return $( this.settings.el_feedback_template ).find( this.settings.el_feedback_guest_display_name ).length;
		},
		addFeedback: function(feedback) {
			var that = this;

			var classes = '';
			// Check if there are classes to add
			if ( typeof feedback.class !== 'undefined' )
				classes = feedback.class;

			var dot = $(that.settings.el_feedback_template).clone()
							.removeAttr('id')
							.removeClass('template')
							.addClass(classes)
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

			// Check if this feedback can be deleted by the user
			if ( !feedback.can_delete ) {
				dot.find( that.settings.el_feedback_action_delete ).remove();
			}

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

			dot.find( that.settings.el_feedback_comment_list_wrapper ).perfectScrollbar();

			that._scrollToBottom( dot, that.settings.el_feedback_comment_list_wrapper );
		},
		closeFeedback: function(dot) {
			var that = this;

			dot.removeClass('draft');

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
					action 		: 'delete_feedback_post',
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

			if ( that.isGuestDisplayNameRequired() && dot.find( that.settings.el_feedback_guest_display_name ).val() == '' ) {
				dot.find( that.settings.el_feedback_guest_display_name ).addClass('error').focus();

				setTimeout(function() {
					dot.find( that.settings.el_feedback_guest_display_name ).removeClass('error');
				}, 3000);
				return false;
			}

			var feedback_id = null;
			if ( dot.attr('id') ) {
				feedback_id = dot.attr('id');
			}

			var data_params = {
				action 		: 'save_feedback',
				post_id		: post_id,
				x 			: dot.attr('data-x'),
				y 			: dot.attr('data-y'),
				feedback_id : feedback_id,
				comment 	: dot.find( that.settings.el_feedback_comment_textarea ).val()
			};

			if ( that.isGuestDisplayNameRequired() ) {
				data_params.guest_display_name = dot.find( that.settings.el_feedback_guest_display_name ).val();
			}

			var request = $.ajax({
				url: ajax_url,
				method: 'POST',
				data: data_params,
				dataType: 'json'
			});

			request.done(function( data ) {
				switch ( data.status ) {
					case 'new_feedback_saved':
						dot.attr( 'id', data.feedback_id );

						// Add feedback id to user_feedbacks
						that.user_feedbacks.push(data.feedback_id);

					case 'feedback_updated':
					default:
						that.addFeedbackComment(data.feedback_id, data.comment);
						dot.addClass( 'saved' ).removeClass( 'draft' );
						break;
				}

				if ( that.isGuestDisplayNameRequired() && dot.find( that.settings.el_feedback_guest_display_name ).is('[type="text"]') ) {
					$( that.settings.el_feedback_guest_display_name + '[type="text"]' ).attr( { type: 'hidden', value: data_params.guest_display_name } );
					$( '.sm-guest-display-name').text( data_params.guest_display_name );
				}

				var dot_textarea = dot.find( that.settings.el_feedback_comment_textarea );
				dot_textarea.val('');
				autosize.update( dot_textarea );
				that._scrollToBottom( dot, that.settings.el_feedback_comment_list_wrapper );
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
			$( '#' + feedback_id ).find( this.settings.el_feedback_comment_list_wrapper + ' ul' ).append( feedback_comment );
		},
		saveDiscussionComment: function() {
			var that = this;

			if ( ! that.settings.discussion_enabled )
				return false;

			if ( $( that.settings.el_discussion_comment_textarea ).val() == '' )
				return false;

			var request = $.ajax({
				url: ajax_url,
				method: 'POST',
				data: {
					action 		: 'save_discussion_comment',
					post_id		: post_id,
					comment 	: $( that.settings.el_discussion_comment_textarea ).val()
				},
				dataType: 'json'
			});

			request.done(function( data ) {
				switch ( data.status ) {
					case 'new_discussion_comment_saved':
					default:
						that.addDiscussionComment(data.comment);
						$( that.settings.el_mockup_discussion ).removeClass( 'draft' );
						break;
				}

				var textarea = $( that.settings.el_discussion_comment_textarea );
				textarea.val('');
				autosize.update( textarea );
				that._scrollToBottom( $(that.settings.el_mockup_discussion), that.settings.el_discussion_comment_list_wrapper );
			});
		},
		addDiscussionComment: function(discussion_comment) {
			$( this.settings.el_discussion_comment_list_wrapper + ' ul' ).append( discussion_comment );
		},
		saveApproval: function() {
			var that = this;

			if ( ! that.settings.approval_enabled )
				return false;

			if ( $( that.settings.el_approval_signature ).val() == '' )
				return false;

			var request = $.ajax({
				url: ajax_url,
				method: 'POST',
				data: {
					action 		: 'smart_mockups_save_approval',
					post_id		: post_id,
					signature 	: $( that.settings.el_approval_signature ).val()
				},
				dataType: 'json'
			});

			request.done(function( data ) {
				switch ( data.status ) {
					case 'approval_saved':
						location.reload();
						break;
					default:
						break;
				}
			});
		},
		updateFeedbackOrientation: function(dot) {
			var that = this;

			var wrapperW = $(that.settings.el_mockup_wrapper).width();
			var wrapperH = $(that.settings.el_mockup_wrapper).height();
			var feedbackContentW = dot.find( that.settings.el_feedback_content ).width();
			var dotOffsetR = dot.position().left + feedbackContentW + 65;
			var dotOffsetB = dot.position().top + dot.find( that.settings.el_feedback_content ).height() + 65;

			dot.removeClass('invert-horizontal orientation-vertical');
			if ( dotOffsetR > wrapperW && (dot.position().left - feedbackContentW) > 0 ) {
				dot.addClass('invert-horizontal');
			}
			else if ( (wrapperW - dotOffsetR + 35) < 0 ) {
				dot.addClass('orientation-vertical');
			}
		}
	};

	$(document).ready(function() {
		SmartMockups.init( window.mockup_options, window.user_feedbacks );
	});

})( jQuery );
