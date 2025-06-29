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
	let flag = true;
	let selectDate = null;
	const doctorsMenu = $( '.doctors-category-menu' );
	const personalSlider = $( '.doctors-profiles' );
	$( '.more-desc' ).click( function( i ) {
		i.preventDefault();
		let moreDes = $( this );
		if ( moreDes.prev().hasClass( 'open' ) ) {
			moreDes.prev().removeClass( 'open' );
			moreDes.text( 'Читати детальніше' ).removeClass( 'arrow-top' );
		} else {
			moreDes.prev().addClass( 'open' );
			moreDes.text( 'Сховати' ).addClass( 'arrow-top' );
		}
	} );
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
	if ( doctorsMenu.length ) {
		if ( $( window ).width() >= 1200 ) {
			if ( doctorsMenu.children().length > 5 ) {
				$( '.doctors-category-menu' ).slick( {
					variableWidth: true,
					infinite: false,
					prevArrow: '<i class="icon-arrow-left"></i>',
					nextArrow: '<i class="icon-arrow-right"></i>',
					dots: true,
					speed: 500
				} );
			}
		} else {
			$( '.doctors-category-menu' ).slick( {
				variableWidth: true,
				infinite: false,
				prevArrow: '<i class="icon-arrow-left"></i>',
				nextArrow: '<i class="icon-arrow-right"></i>',
				dots: true,
				speed: 500
			} );
		}
		doctorsMenu.find( 'li > a' ).click( function( e ) {
			e.preventDefault();
			let url = new URL( window.location.href );
			url.searchParams.set( 'category_id', $( this ).data( 'service_sb_id' ) );
			window.location.href = url.toString();
		} );
	}
	if ( personalSlider.length ) {
		personalSlider.slick( {
			variableWidth: true,
			prevArrow: '<i class="icon-arrow-left"></i>',
			nextArrow: '<i class="icon-arrow-right"></i>'
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
			beforeShowDay: function( date ) {
				return [ true, '' ];
			},
			onChangeMonthYear: function( year, month, inst ) {
				const firstDay = new Date( year, month - 1, 1 );
				const formatted = $.datepicker.formatDate( 'yy-mm-dd', firstDay );
				selectDate = formatted;
				getAvailableDate( formatted );
			}
		} );
		$( '.ui-datepicker-month' ).select2( {
			minimumResultsForSearch: Infinity,
			dropdownParent: '.ui-datepicker-title'
		} );
	}
	const specialistElements = $( '[name=specialist], [name=service_id]' );
	if ( specialistElements.length ) {
		const preloader = $( '.datepicker-block .pswp__preloader__icn' );
		specialistElements.change( function( e ) {
			$( '.service input:not([checked])' ).next().removeClass( 'icon-check' ).addClass( 'icon-plus' );
			$( this ).next( 'label' ).removeClass( 'icon-plus' ).addClass( 'icon-check' );
			$( '.provider' ).html( $( '.service .icon-check h5' ).text() );
			$( '.price span' ).text( $( '.service .icon-check .icon-price' ).text() );
			let providerName = $( '[name=specialist]:checked' ).parent().find( 'h5' ).text() + '<br>';
			$( 'p.provider' ).text( '' );
			let text = $( '.provider' ).text() + '<br>' + providerName;
			$( 'p.provider' ).html( text );
			getAvailableDate( selectDate );
		} );
	}
	$( '.select select' ).select2( {
		minimumResultsForSearch: Infinity,
		dropdownParent: '.select',
		width: '100%'
	} );
	const service = $( '.service [name=service_id]:checked' );
	const provider = $( '[name=specialist]:checked' );
	if ( service.length && provider.length ) {
		getAvailableDate();
	}

	function addSlotTime( date, service, provider ) {
		const data = {
			action: sbipObject.slotAction,
			nonce: sbipObject.slotNonce,
			date: date,
			service: service,
			provider: provider
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
			}
		} );
	}

	function onSelectSlotTime( date ) {
		$( '.clocks-radio .clock-radio' ).click( function( e ) {
			console.log( $( this ).children( 'input' ).val() );
			let time = $( this ).children( 'input' ).val();
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
				booking_id: $( this ).data( 'booking_id' )
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
		responsive: [ {
			breakpoint: 1450,
			settings: {
				slidesToShow: 2.84
			}
		}, {
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
				infinite: true
			}
		} ]
	} );
	if ( $( '.slider-item' ).length ) {
		$( '.slider-item' ).beforeAfter( {
			movable: true,
			clickMove: true,
			position: 50,
			separatorColor: '#4D2A14',
			opacity: 1,
			arrowColor: '#fff',
			bulletColor: '#4D2A14'
		} );
	}
	const backButton = $( '.back-button' );
	if ( backButton.length ) {
		backButton.click( function( e ) {
			e.preventDefault();
			const preloader = $( '.datepicker-block .pswp__preloader__icn' );
			preloader.show();
			window.history.back();
		} );
	}

	function getAvailableDate( date = null ) {
		const preloader = $( '.datepicker-block .pswp__preloader__icn' );
		let data = {
			action: sbipObject.appointmentAction,
			nonce: sbipObject.appointmentNonce,
			location: $( '#sbip-location option:selected' ).val(),
			service: $( '.service [name=service_id]:checked' ).val(),
			provider: $( '[name=specialist]:checked' ).val(),
			date: date
		};
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
				flag = true;
				if ( res.success && res.data.date.length ) {
					const defaultViewDate = selectDate || new Date;
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
						defaultDate: defaultViewDate,
						beforeShowDay: function( date ) {
							const string = $.datepicker.formatDate( 'yy-mm-dd', date );
							return [ res.data.date.indexOf( string ) === -1, 'active-date' ];
						},
						onChangeMonthYear: function( year, month, inst ) {
							const firstDay = new Date( year, month - 1, 1 );
							const formatted = $.datepicker.formatDate( 'yy-mm-dd', firstDay );
							selectDate = formatted;
							getAvailableDate( formatted );
						},
						onSelect: function( dateText, inst ) {
							addSlotTime( dateText, data.service, data.provider );
							setTimeout( function() {
								$( '.ui-datepicker-month' ).select2( {
									minimumResultsForSearch: Infinity,
									dropdownParent: '.ui-datepicker-title'
								} );
							}, 50 );
						}
					} );
					setTimeout( function() {
						$( '.ui-datepicker-month' ).select2( {
							minimumResultsForSearch: Infinity,
							dropdownParent: '.ui-datepicker-title'
						} );
					}, 50 );
				}
			},
			error: function( xhr ) {
				console.log( 'error...', xhr );
			}
		} );
	}
} );
