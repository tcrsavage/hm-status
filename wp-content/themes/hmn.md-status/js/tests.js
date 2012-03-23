// remap jQuery to $
(function($){

	$.each( $( '#menu-main > li' ), function() {
		
		var menuItem = $(this),
			subMenuItem = $(this).children( '.sub-menu' );
	
		if( 0 < subMenuItem.size() ) {
			
			console.log( menuItem.children('a') );
			
			menuItem.children('a').live( 'click', function( e ) { 

				alert( 'hola' );

				e.preventDefault() 

				if( subMenuItem.is( ':visible' ) )
					subMenuItem.hide( 100 ); 
				else
					subMenuItem.show( 100 ); 

			} );
			
		}
			
		
	});

	function menuFlex( menu ){
	
		var flexMenuButton = $('<li class="flexMenu"><a href="#">More +</a></li>'),
			flexMenuList = $('<ul class="sub-menu"></ul>');
	
		flexMenuButton.append( flexMenuList ).appendTo( menu );
	
		var showHide = function(){
		
			var width = 0;
			
			$.each( menu.children().not( '.flexMenu' ), function(){				
		
				width = width + $(this).width();
	
				if( width > menu.width() - flexMenuButton.outerWidth() )
					$(this).hide();
					
				else
					$(this).show();
		
			} );		
			
			flexMenuList.children().remove();
			
			
			flexNavItems = menu.children().not(':visible,.flexMenu').clone();
			
			//console.log( flexNavItems.size() );
			
			if( flexNavItems.size() > 0 ) {
				flexMenuButton.show();
				flexMenuList.append( flexNavItems.show() );
			} else {
				flexMenuButton.hide();
			}
				

			
		};
				
		showHide();
		$(window).resize( function() {
			showHide();
		} );

		
	}
	
	menuFlex( $( '#menu-main' ) );
	
	

})(this.jQuery);