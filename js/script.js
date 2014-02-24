( function ( $ ) {
	$( document ).ready( function () {
		//add one more field to direct input
		$( '#gglshrtlnk_add-field-button' ).click( function() {
			// alert( $.isEmptyObject($('[name ^= gglshrtlnk_url-input-]')) );
			var gglshrtlnk_is_empty = 1;
			$('[name ^= gglshrtlnk_url-input-]').each( function() {
				if ( $( this ).attr( 'value' ) == '' ) { 
					gglshrtlnk_is_empty = 1;
					return false
				} else { 
					gglshrtlnk_is_empty = 0;
				}
			});
			//alert('gglshrtlnk_is_empty = ' + gglshrtlnk_is_empty );
			if ( gglshrtlnk_is_empty == 0 ){
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
		//remove one field of direct input
		$( '#gglshrtlnk_remove-field-button' ).click( function(){
			if ( $( '#gglshrtlnk_direct-input-table' ).find( 'tbody' ).find( 'tr' ).size() > 2 ) {
				var oldval = $( '#gglshrtlnk_number_of_input_links' ).val();
				var newval = --oldval;
				$( '#gglshrtlnk_number_of_input_links' ).val( newval );
				$( '#gglshrtlnk_direct-input-table' ).find( 'tbody' ).find( 'tr:last' ).detach();
			};
		});		
		//reset direct input form
		$( '#reset-direct' ).click( function() {
			$( '[name ^= gglshrtlnk_url-input-]' ).each( function(index, element) {
				$( element ).val('');
			});
			$( '[name ^= gglshrtlnk_url-output-]' ).each( function(index, element) {
				$( element ).val('');
			});
		});
		//confirm delete db
		$( '#gglshrtlnk_delete-all-radio' ).click( function() {
			if ( $( this ).attr( 'checked' ) == 'checked' ) {
				if ( ! confirm( gglshrtlnk_delete_fromdb_message ) ) {
					$( this ).removeAttr( 'checked' );
					$( this ).blur();
					$( '#gglshrtlnk_scan' ).attr( 'checked', 'checked' );
				}; 
			}
		}); 	
	});
} )( jQuery );