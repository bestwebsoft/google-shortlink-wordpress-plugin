( function ( $ ) {
	$( document ).ready( function () {
		/* add one more field to direct input */
		$( '#gglshrtlnk_add-field-button' ).click( function() {

			var gglshrtlnk_is_empty = 1;
			$('[name ^= gglshrtlnk_url-input-]').each( function() {
				if ( $( this ).attr( 'value' ) == '' ) { 
					gglshrtlnk_is_empty = 1;
					return false
				} else { 
					gglshrtlnk_is_empty = 0;
				}
			});

			if ( gglshrtlnk_is_empty == 0 ) {
				var oldval = $( '#gglshrtlnk_number_of_input_links' ).val();
				var newval = ++oldval;
				$( '#gglshrtlnk_number_of_input_links' ).val( newval );
				var input = 'gglshrtlnk_url-input-' + $( '#gglshrtlnk_number_of_input_links' ).val();
				var output = 'gglshrtlnk_url-output-' + $( '#gglshrtlnk_number_of_input_links' ).val();
				$( '#gglshrtlnk_direct-input-table' ).find( 'tbody' ).append('<tr valign="top"><td class="gglshrtlnk_long-link-column"><input type="url" name="' + input + '" size="20" value="" /></td><td class="gglshrtlnk_short-link-column"><input type="url" name="' + output + '" size="20" readonly value="" /></td></tr>');	
				$( '#gglshrtlnk_no_more_fields' ).addClass( 'gglshrtlnk_hide' );
			
			} else { 
				$( '#gglshrtlnk_no_more_fields' ).removeClass( 'gglshrtlnk_hide' );
			}
		});
		/* remove one field of direct input */
		$( '#gglshrtlnk_remove-field-button' ).click( function(){
			if ( $( '#gglshrtlnk_direct-input-table' ).find( 'tbody' ).find( 'tr' ).size() > 2 ) {
				var oldval = $( '#gglshrtlnk_number_of_input_links' ).val();
				var newval = --oldval;
				$( '#gglshrtlnk_number_of_input_links' ).val( newval );
				$( '#gglshrtlnk_direct-input-table' ).find( 'tbody' ).find( 'tr:last' ).detach();
			};
		});		
		/* reset direct input form */
		$( '#reset-direct' ).click( function() {
			$( '[name ^= gglshrtlnk_url-input-]' ).each( function(index, element) {
				$( element ).val('');
			});
			$( '[name ^= gglshrtlnk_url-output-]' ).each( function(index, element) {
				$( element ).val('');
			});
		});
		/* confirm delete db */
		$( '#gglshrtlnk_delete-all-radio' ).click( function() {
			if ( $( this ).attr( 'checked' ) == 'checked' ) {
				if ( ! confirm( gglshrtlnk_vars.gglshrtlnk_delete_fromdb_message ) ) {
					$( this ).removeAttr( 'checked' );
					$( this ).blur();
					$( '#gglshrtlnk_scan' ).attr( 'checked', 'checked' );
				}; 
			}
		}); 

		$( '#gglshrtlnk_actions-with-links' ).submit( function() {
			return false;
		});

		$( '#gglshrtlnk_apply_button3' ).click( function() {
			var gglshrtlnk_radio_state = $( 'input[name=gglshrtlnk_actions_with_links_radio]:checked' ).val();
			var gglshrtlnk_data = {
				action: 'additional_opt',
				gglshrtlnk_actions_with_links_radio: gglshrtlnk_radio_state,
				gglshrtlnk_bulk_select1: $( '#gglshrtlnk_bulk_select1' ).val(),
				gglshrtlnk_bulk_select2: $( '#gglshrtlnk_bulk_select2' ).val(),
				'gglshrtlnk_nonce' : gglshrtlnk_vars.gglshrtlnk_ajax_nonce
			};
			$( '#gglshrtlnk_ajax-status' ).removeClass( 'gglshrtlnk_hide' ).removeClass( 'error' ).addClass( 'updated' );
			switch ( gglshrtlnk_radio_state ) {
				case 'replace-all':
					$( '#gglshrtlnk_ajax-status' ).html( '<p>' + gglshrtlnk_vars.gglshrtlnk_replace_all + '</p>' );
				break
				case 'restore-all':
					$( '#gglshrtlnk_ajax-status' ).html( '<p>' + gglshrtlnk_vars.gglshrtlnk_restore_all + '</p>' );
				break
				case 'delete-all-radio':
					$( '#gglshrtlnk_ajax-status' ).html( '<p>' + gglshrtlnk_vars.gglshrtlnk_delete_all_radio + '</p>' );					
				break
				case 'scan':
					$( '#gglshrtlnk_ajax-status' ).html( '<p>' + gglshrtlnk_vars.gglshrtlnk_scan + '</p>' );
				break
			}
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: gglshrtlnk_data,
				success: function( result ) {
					var data = $.parseJSON( result );

					if ( data['error'] != '' ) {
						$( '#gglshrtlnk_ajax-status' ).addClass( 'error' ).removeClass( 'updated' );
						$( '#gglshrtlnk_ajax-status' ).html( '<p>' + data['error'] + '</p>' );
					} else {
						$( '#gglshrtlnk_ajax-status' ).html( '<p>' + data['message'] + '</p>' );
					}

					if ( gglshrtlnk_radio_state == 'delete-all-radio' ) {
					/* disabling radibuttons afted deleting */
						$( '#gglshrtlnk_replace-all, #gglshrtlnk_restore-all' ).attr( 'disabled', 'disabled' );
						$( '#gglshrtlnk_delete-all-radio' ).removeAttr( 'checked' ).attr( 'disabled', 'disabled' );
						$( '#gglshrtlnk_scan' ).attr( 'checked', 'checked' );
					} else if ( gglshrtlnk_radio_state == 'scan' ) {
						$( '#gglshrtlnk_replace-all, #gglshrtlnk_restore-all, #gglshrtlnk_delete-all-radio' ).removeAttr( 'disabled' );
					}
				}
			});
		});

		$( '.total_clicks' ).each( function( current_item ) {
			var gglshrtlnk_data = {
				action: 'total_clicks',
				gglshrtlnk_short_to_count: $( this ).prev().text(),
				'gglshrtlnk_nonce' : gglshrtlnk_vars.gglshrtlnk_ajax_nonce
			};
			$.post( ajaxurl, gglshrtlnk_data, function( result ) {
				$('.total_clicks').eq( current_item ).html( result );
			});
		});	
	});
} )( jQuery );