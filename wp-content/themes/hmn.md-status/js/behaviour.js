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

			round.currentUser  = parseInt( currentUserBar.attr( 'data-userid' ) ); //Current User Id
			round.currentRound = new Array(); // Array of user ids in this round.
			round.graphInfo    = new Array(); // Array of user objects.

			// Store current state in graph info.
			// An array of user objects.
			$.each( users, function() {

				var userInfo = new Object();

				userInfo.userID 	= parseInt( $(this).attr('data-userid') );
				userInfo.userTotal = parseInt( $(this).attr('data-total') );

				round.graphInfo.push( userInfo );

			} );


			// The action.
			users.not( currentUserBar ).click( function() {

				var clickedUser = parseInt( $(this).attr('data-userid') );

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
					if( separators.length != this.userTotal ) {
						separators.remove();
						for (var i = 1; i <= this.userTotal; i++ ) {
							bar.find('.separators').append( $('<span class="separator"></span>') );
						}
					}

					// check if this bar should have the alert class.
					bar.removeClass( 'alert' );
					if( this.userTotal == max || jQuery.inArray( this.userID, round.currentRound ) >= 0 && ( this.userTotal + 1 == max) )
						bar.addClass( 'alert' );

					//Append tbc
					if( jQuery.inArray( this.userID, round.currentRound ) >= 0 && this.userTotal >= 0 ) {
						bar.find('.separators').append( $('<span class="separator tbc"></span>') );
					}

					// Highlight current User deactivated.
					if( bar.is( currentUserBar ) ) {

						separators.removeClass( 'deactivated' );

						for ( var i = -1; i > 0 - round.currentRound.length - 1; i-- ) {
							separators.eq( i ).addClass('deactivated');
						}

					}

					// fix the measurements
					recalculateGraph();

				} );

			};

			// Adjusts inline styles so that graph is displayed correctly.
			var recalculateGraph = function() {

				var max = 0;

				$.each( graph.find('li'), function() {

					var seperators = $(this).find('.separator');

					if( seperators.length > max )
						max = seperators.length;

				} );

				var singleWidth =  ( 100 / max );

				$.each( graph.find('.separator'), function() {

					$(this).width( ( singleWidth - 2 ) + '%' ).css( 'margin-right', '2%' ).attr('data-total', $(this).closest('li').find('.separator').length );

				} );

			}

			var getGraphMax = function() {

				round.graphMax = 0;
				$.each( round.graphInfo, function() {

					if( jQuery.inArray( this.userID, round.currentRound ) >= 0 && ( this.userTotal + 1 > round.graphMax ) )
						round.graphMax = this.userTotal + 1;

					else if ( this.userTotal > round.graphMax )
						round.graphMax = this.userTotal;

				});

				return round.graphMax;

			}

			// If anyone is selected, show a submit button.
			var toggleButton = function() {

				if( round.currentRound.length > 0 )
					button.show();
				else
					button.hide();

			}

			// Todo. display currnent/proposed total.
			var updateTotals = function() {

				$.each( users, function() {

					var separators = $(this).find( '.separator'),
						total      = separators.not('.deactivated, .counter').length;

					//console.log( separators.filter('.counter').length == 0 );

					if( total <= 0 && separators.filter('.counter').length == 0 ) {
						$('<span class="separator counter"></span>').appendTo( $(this).find('.separators' ) );
					} else {

					}


				} );

			}

			updateTotals();

			button.click( function() {
				console.log( round );
				var userString = new String( round.currentRound );
				alert( 'User ' + round.currentUser + ' is making tea for users ' + userString );
			} );



		} );

	} );

})(this.jQuery);


