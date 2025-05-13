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
			minDate: 0,
		} );

		$( '.ui-datepicker-month' ).select2( {
			minimumResultsForSearch: Infinity,
			dropdownParent: '.ui-datepicker-title',
		} );
	}

	const specialistElements = $( '[name=specialist], [name=service_id]' );
	if ( specialistElements.length ) {
		const preloader = $( '.datepicker-block .pswp__preloader__icn' );
		specialistElements.change( function( e ) {

			$('.service input:not([checked])').next().removeClass( 'icon-check' ).addClass( 'icon-plus' );
			$( this ).next( 'label' ).removeClass( 'icon-plus' ).addClass( 'icon-check' );

			let data = {
				action: sbipObject.appointmentAction,
				nonce: sbipObject.appointmentNonce,
				location: $( '#sbip-location option:selected' ).val(),
				service: $( '.service [name=service_id]:checked' ).val(),
				provider: $( '[name=specialist]:checked' ).val()
			};

			$( '.provider' ).html( $( '.service .icon-check h5' ).text() );
			$( '.price span' ).text( $( '.service .icon-check .icon-price' ).text() );

			let providerName = $( '[name=specialist]:checked' ).parent().find( 'h5' ).text() + '<br>';
			$( 'p.provider' ).text( '' );
			let text = $( '.provider' ).text() + '<br>' + providerName;
			$( 'p.provider' ).html( text );

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
							minDate: 0,
							beforeShowDay: function( date ) {
								var string = jQuery.datepicker.formatDate( 'yy-mm-dd', date );
								return [ res.data.date.indexOf( string ) == -1 ];
							},
							onSelect: function( dateText, inst ) {
								addSlotTime( dateText, data.service, data.provider );
								setTimeout( function() {
									$( '.ui-datepicker-month' ).select2( {
										minimumResultsForSearch: Infinity,
										dropdownParent: '.ui-datepicker-title',
									} );
								}, 50 );
							}
						} );
						setTimeout( function() {
							$( '.ui-datepicker-month' ).select2( {
								minimumResultsForSearch: Infinity,
								dropdownParent: '.ui-datepicker-title',
							} );
						}, 50 );
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

	$( '.slider-before-after' ).slick( {
		slidesToShow: 3.6,
		prevArrow: '<i class="icon-arrow-left"></i>',
		nextArrow: '<i class="icon-arrow-right"></i>',
		draggable: false,
		infinite: false,
		responsive: [
			{
				breakpoint: 1450,
				settings: {
					slidesToShow: 2.84
				}
			},
			{
				breakpoint: 769,
				settings: {
					slidesToShow: 1.84
				}
			}, {
				breakpoint: 576,
				settings: {
					slidesToShow: 1,
					centerMode: true,
					centerPadding: '30px',
					infinite: true,
				}
			} ]
	} );
	if($( '.slider-item' ).length){
		$( '.slider-item' ).beforeAfter( {
			movable: true,
			clickMove: true,
			position: 50,
			separatorColor: '#4D2A14',
			opacity: 1,
			arrowColor: '#fff',
			bulletColor: '#4D2A14',
		} );
	}
} );
