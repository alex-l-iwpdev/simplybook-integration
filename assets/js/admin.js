/* global sbipObject, jQuery */
/**
 * @param sbipObject.ajaxUrl
 * @param sbipObject.syncNonce
 * @param sbipObject.syncAction
 */

jQuery( document ).ready( function( $ ) {
	const syncButton = $( '.synchronization' );
	if ( syncButton.length ) {
		const preloader = $( '.pswp__preloader__icn' );
		syncButton.click( function( e ) {
			e.preventDefault();

			const data = {
				action: sbipObject.syncAction,
				nonce: sbipObject.syncNonce,
			};

			$.ajax( {
				type: 'POST',
				url: sbipObject.ajaxUrl,
				data: data,
				beforeSend: function( e ) {
					preloader.show();
					syncButton.prop( 'disabled', true );
				},
				success: function( res ) {
					preloader.hide();
					syncButton.prop( 'disabled', false );
					if ( res.success ) {
						alert( res.data.message );
					}

					if ( ! res.success ) {
						alert( res.data.message );
					}
				},
				error: function( xhr ) {
					console.log( 'error...', xhr );
					//error logging
				}
			} );
		} );
	}
} );
