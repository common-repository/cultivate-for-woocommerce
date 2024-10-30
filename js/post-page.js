( function ( $ ) {
	$( '#_cultivate_made_in_usa' )
		.on( 'change', function () {
			$( '.show_if_made_in_usa' ).toggle( $( this ).is( ':checked' ) );
		} )
		.trigger( 'change' );
} )( jQuery );
