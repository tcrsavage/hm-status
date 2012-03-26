// remap jQuery to $
(function($){

/*

x = array(
	total => x
	round => array()
	info => array(
		user => [ round=> ]
		)
	);

*/

/*
* Grid Overlay
*
*/
	$( document ).ready( function() {

		/**
		 *	Dev Grid Overlay
		 *
		 *	'Show Grid' button added to the admin bar.
		 *	should be styled in the theme stylesheet.
		 */
		$( '#wp-admin-bar-show-grid a, .show-grid' ).live( 'click', function( e ) {
				e.preventDefault();
				var gridOverlay = '<div id="grid_overlay"><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div><div><span></span></div></div>';
				if( ! $('#grid_overlay').length ) {
					$('body').append( gridOverlay );
				} else {
					$('#grid_overlay').remove();
				}
		});

		$('.masonry').masonry({
 			columnWidth: 144
		});


		$.each( $('.tea-chart'), function() {

			var graph          = $(this);
				users          = $(this).find( 'li' );
				currentUserBar = users.filter( '.current-user' );

			var button = $('<button>Make Round</button>').hide();

			$(this).append( button );

			var round = new Object();

			round.currentUser  = currentUserBar.attr( 'data-userid' ); //Current User Id
			round.currentRound = new Array(); // Array of user ids in this round.
			round.graphInfo    = new Array(); // Array of user objects.

			// Store current state in graph info.
			// An array of user objects.
			$.each( users, function() {

				var userInfo = new Object();

				userInfo.userID 	= $(this).attr('data-userid');
				userInfo.userTotal = $(this).attr('data-total');

				round.graphInfo.push( userInfo );

			} );


			// The action.
			users.click( function() {

				if( currentUserBar.length == 0 ) {
					currentUserBar = $(this);
					currentUserBar.addClass( 'current-user' );
					round.currentUser  = $(this).attr( 'data-userid' ); //Current User Id
					return;
				}

				var clickedUser = $(this).attr('data-userid');

				if( clickedUser == round.currentUser )
					return;

				if( $.inArray( clickedUser, round.currentRound ) >= 0 ) {
					round.currentRound.splice( $.inArray( clickedUser, round.currentRound ), 1 );
					$(this).removeClass( 'selected' );
				} else {
					round.currentRound.push( clickedUser );
					$(this).addClass( 'selected' );
				}

				redrawGraph();
				toggleButton();

			});

			var redrawGraph = function() {

				var max = getGraphMax();

				$.each( round.graphInfo, function() {

					var bar 	   = users.filter( 'li[data-userid="' + this.userID + '"]' ),
						separators = bar.find( '.separator' );

					// Redraw standard graph.
					separators.remove();
					for (var i = 1; i <= this.userTotal; i++ ) {
						bar.find('.separators').append( $('<span class="separator"></span>') );
						separators = bar.find( '.separator' );
					}

					// check if this bar should have the alert class.
					bar.removeClass( 'alert' );
					if( parseInt( this.userTotal ) == parseInt(max) || jQuery.inArray( this.userID, round.currentRound ) >= 0 && ( parseInt( this.userTotal ) + 1 == parseInt( max ) ) )
						bar.addClass( 'alert' );

					//Append tbc
					bar.removeClass( 'selected' );
					if( jQuery.inArray( this.userID, round.currentRound ) >= 0 ) {

						if( parseInt( this.userTotal ) >= 0 ) {
							bar.find('.separators').append( $('<span class="separator tbc"></span>') );
						}

						bar.addClass( 'selected' );

					}

					// Highlight current User deactivated.
					bar.removeClass('current-user' );
					separators.removeClass( 'deactivated' );
					if( this.userID == round.currentUser ) {

						console.log( round.currentRound.length )
						bar.addClass('current-user' );

						for ( var i = -1; i > 0 - round.currentRound.length - 1; i-- ) {
							console.log( separators.eq( i ) );
							separators.eq( i ).addClass('deactivated');
						}

					}

					// fix the measurements
					updateTotals();
					recalculateGraph();


				} );

			};

			// Adjusts inline styles so that graph is displayed correctly.
			var recalculateGraph = function() {

				var singleWidth = ( 100 / getGraphMax() );

				$.each( graph.find('.separator, .counter'), function() {

					$(this).width( ( singleWidth - 2 ) + '%' ).css( 'margin-right', '2%' ).attr('data-total', $(this).closest('li').find('.separator').length );

					if( $(this).hasClass( 'counter' ) )
						$(this).css('margin-left', '-' + singleWidth + '%');

				} );

			}

			var getGraphMax = function() {

				round.graphMax = 0;
				$.each( round.graphInfo, function() {

					if( jQuery.inArray( this.userID, round.currentRound ) >= 0 && ( parseInt( this.userTotal ) + 1 > round.graphMax ) )
						round.graphMax = parseInt( this.userTotal ) + 1;

					else if ( this.userTotal > round.graphMax )
						round.graphMax = parseInt( this.userTotal );

				});
				return round.graphMax;

			}

			// If anyone is selected, show a submit button.
			var toggleButton = function() {

				if( round.currentRound.length > 0 ) {
					button.show();
				} else {
					button.hide();
				}

			}

			// Todo. display currnent/proposed total.
			var updateTotals = function() {

				$.each( round.graphInfo, function() {

					var user    = users.filter( 'li[data-userid="' + this.userID + '"]' ),
						counter = user.find('.counter'),
						counterValue = this.userTotal;

					if( counter.length < 1 ) {
						counter = $('<span class="counter"></span>');
					}

					if( jQuery.inArray( this.userID, round.currentRound ) >= 0 ) {
						counterValue = parseInt( this.userTotal ) + 1;
					}

					if( this.userID == round.currentUser ) {
						counterValue = parseInt( this.userTotal ) - round.currentRound.length;
					}

					user.find('.separators').append( counter );

					counter.text( counterValue );

					user.find('.separator').removeClass('last');
					counter.prev().addClass('last');

				} );

			}

			button.click( function() {

				console.log( 'User ' + round.currentUser + ' is making tea for users ' + new String( round.currentRound ) );

				var data = {
					action: 'tea_tally_ajax',
					round: round
				};

				jQuery.post( ajaxurl, data, function(response) {
					response = jQuery.parseJSON( response );
					console.log( 'response' );
					console.log( response );
					round = response;
					redrawGraph();
					updateTotals();
					toggleButton();
				});



			} );

			updateTotals();
			redrawGraph();

		} );

	} );

})(this.jQuery);


