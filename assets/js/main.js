/* global sbipObject, jQuery */

/**
 * @param sbipObject.ajaxUrl
 * @param sbipObject.appointmentNonce
 * @param sbipObject.appointmentAction
 * @param sbipObject.slotAction
 * @param sbipObject.slotNonce
 * @param sbipObject.deleteBookingAction
 * @param sbipObject.deleteBookingNonce
 * @param sbipObject.mainPageUrl
 */

jQuery( document ).ready( function( $ ) {
	$( '.more' ).click( function( e ) {
		e.preventDefault();
		var more = $( this );
		if ( more.prev().hasClass( 'open' ) ) {
			more.prev().removeClass( 'open' ).slideUp();
			more.text( 'Читати детальніше' ).removeClass( 'arrow-top' );
			more.parent().find( '.specialization' ).show();
		} else {
			more.prev().addClass( 'open' ).slideDown();
			more.text( 'Сховати' ).addClass( 'arrow-top' );
			more.parent().find( '.specialization' ).hide();
		}

	} );

	const doctorsMenu = $( '.doctors-category-menu' );
	if ( doctorsMenu.length ) {
		if ( $( window ).width() >= 1200 ) {
			if ( doctorsMenu.children().length > 5 ) {
				$( '.doctors-category-menu' ).slick( {
					variableWidth: true,
					infinite: false,
					arrows: false,
					dots: true,
				} );
			}
		} else {
			$( '.doctors-category-menu' ).slick( {
				variableWidth: true,
				infinite: false,
				arrows: false,
				dots: true,
			} );
		}

		doctorsMenu.find( 'li > a' ).click( function( e ) {
			e.preventDefault();

			let url = new URL( window.location.href );
			url.searchParams.set( 'category_id', $( this ).data( 'service_sb_id' ) );
			window.location.href = url.toString();
		} );
	}

	const datePicker = $( '.datepicker' );
	if ( datePicker.length ) {
		datePicker.datepicker( {
			showButtonPanel: true,
			changeMonth: true,
			changeYear: false,
			showAnim: 'fold',
			prevText: '',
			nextText: '',
			gotoCurrent: true,
			dateFormat: 'yy-mm-dd',
		} );

		$( '.ui-datepicker-month' ).select2( {
			minimumResultsForSearch: Infinity,
			dropdownParent: '.ui-datepicker-title',
		} );
	}

	const specialistElements = $( '[name=specialist]' );
	if ( specialistElements.length ) {
		$( '.provider' ).html( $( '.service .icon-check h5' ).text() );
		$( '.price span' ).text( $( '.service .icon-check .icon-price' ).text() );
		const preloader = $( '.datepicker-block .pswp__preloader__icn' );
		specialistElements.change( function( e ) {
			let data = {
				action: sbipObject.appointmentAction,
				nonce: sbipObject.appointmentNonce,
				location: $( '#sbip-location option:selected' ).val(),
				service: $( '.service .icon-check' ).data( 'service_id' ),
				provider: $( this ).val()
			};

			let providerName = $( this ).parent().find( 'h5' ).text() + '<br>';
			let text = $( '.provider' ).text() + '<br>' + providerName;
			$( '.provider' ).html( text );

			$.ajax( {
				type: 'POST',
				url: sbipObject.ajaxUrl,
				data: data,
				beforeSend: function() {
					preloader.show();
					$( '.clocks-radio .clock-radio' ).remove();
				},
				success: function( res ) {
					preloader.hide();
					if ( res.success && res.data.date.length ) {
						datePicker.datepicker( 'destroy' ).datepicker( {
							showButtonPanel: true,
							changeMonth: true,
							changeYear: false,
							showAnim: 'fold',
							prevText: '',
							nextText: '',
							gotoCurrent: true,
							dateFormat: 'yy-mm-dd',
							beforeShowDay: function( date ) {
								var string = jQuery.datepicker.formatDate( 'yy-mm-dd', date );
								return [ res.data.date.indexOf( string ) == -1 ];
							},
							onSelect: function( dateText, inst ) {
								addSlotTime( dateText, data.service, data.provider );
							}
						} );
					}
				},
				error: function( xhr ) {
					console.log( 'error...', xhr );
				}
			} );
		} );
	}

	$( '.select select' ).select2( {
		minimumResultsForSearch: Infinity,
		dropdownParent: '.select',
		width: '100%',
	} );

	function addSlotTime( date, service, provider ) {
		const data = {
			action: sbipObject.slotAction,
			nonce: sbipObject.slotNonce,
			date: date,
			service: service,
			provider: provider,
		};
		const preloader = $( '.clocks-radio .pswp__preloader__icn' );

		$.ajax( {
			type: 'POST',
			url: sbipObject.ajaxUrl,
			data: data,
			beforeSend: function( e ) {
				preloader.show();
				$( '.clocks-radio .clock-radio' ).remove();
				$( '.clocks-radio p' ).remove();
			},
			success: function( res ) {
				preloader.hide();
				if ( res.success ) {
					$( '.clocks-radio' ).append( res.data.slots );
					onSelectSlotTime( date );
				}

				if ( ! res.success ) {
					$( '.clocks-radio' ).append( res.data.message );
				}
			},
			error: function( xhr ) {
				console.log( 'error...', xhr );
				//error logging
			}
		} );
	}

	function onSelectSlotTime( date ) {
		$( '.clocks-radio .clock-radio' ).click( function( e ) {
			let time = $( this ).parent().find( 'input' ).val();
			$( '[name=date_and_time]' ).val( date + ' ' + time );
			$( '.appointment input[type=submit]' ).prop( 'disabled', false );
		} );
	}


	const locationEl = $( '#sbip-location' );
	if ( locationEl.length ) {
		locationEl.change( function() {
			const selectedId = $( this ).val();
			const url = new URL( window.location.href );
			url.searchParams.set( 'location', selectedId );
			window.location.href = url.toString();
		} );
	}

	const deleteBookingButton = $( '.delete-booking' );
	if ( deleteBookingButton.length ) {
		deleteBookingButton.click( function( e ) {
			e.preventDefault();
			const preloader = $( '.confirmation .pswp__preloader__icn' );

			const data = {
				action: sbipObject.deleteBookingAction,
				nonce: sbipObject.deleteBookingNonce,
				booking_id: $( this ).data( 'booking_id' ),
			};

			$.ajax( {
				type: 'POST',
				url: sbipObject.ajaxUrl,
				data: data,
				beforeSend: function( e ) {
					preloader.show();
				},
				success: function( res ) {
					preloader.hide();
					if ( res.success ) {
						location.href = sbipObject.mainPageUrl;
					}

					if ( ! res.success ) {
						alert( 'Some Error.' );
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
