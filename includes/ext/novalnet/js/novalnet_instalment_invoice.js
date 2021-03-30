
if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener('load', novalnet_instalment_invoice);
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent('onload', novalnet_instalment_invoice);
}

function novalnet_instalment_invoice() {
		jQuery('#novalnet_instalment_invoice_period').val(0);
		
		//show instalment invoice plan details
		jQuery( document ).on( 'change', '#novalnet_instalment_invoice_period' , function() {
			var novalnet_order_cycle_period = jQuery( "#novalnet_instalment_invoice_period option:selected" ).val();
			var order_amount = jQuery( '#nn_order_amount' ).val();
			var total_amount = 0, instalment_due = 0, last_instalment_due = 0, get_instalment_future_dates = '';			
			total_amount = ( parseFloat( order_amount ) ).toFixed( 2 );
			for ( var i=1;i<=novalnet_order_cycle_period;i++ ) {
				if ( i != novalnet_order_cycle_period ) {
					split_amount = ( parseFloat( total_amount/novalnet_order_cycle_period ) ).toFixed( 2 );
					instalment_due = parseFloat( instalment_due ) + parseFloat( split_amount );
				} else {
					last_instalment_due = ( parseFloat ( total_amount - instalment_due ) ).toFixed( 2 );
				}
			}	
			var lang = jQuery( '#nn_lang' ).val();
			var text = jQuery( '#instalment_text' ).val();
			var instalment_number = jQuery( '#instalment_number' ).val();
			
			var monthly_instalment = jQuery( '#monthly_instalment' ).val();
			get_instalment_future_dates = jQuery( '#instalment_cycles' ).val().split( "/" );
			var number_text = '';
			var final_due = novalnet_order_cycle_period-1;
			if ( novalnet_order_cycle_period == '0' ){
				jQuery( "#novalnet_instalment_table_invoice thead tr" ).remove();
			}else {
				jQuery( "#novalnet_instalment_table_invoice thead tr" ).remove();
				jQuery( "#novalnet_instalment_table_invoice thead" ).append( "<tr><th>" + instalment_number + "</th><th>" + monthly_instalment + "</th></tr>" );
			}
			jQuery( "#novalnet_instalment_table_invoice" ).show(); 
			jQuery( "#novalnet_instalment_table_invoice tbody tr" ).remove();
			for ( var j=0;j<novalnet_order_cycle_period;j++ ) {
				if ( lang == 'en' ) {
					if ( j+1 == 1 || j+1 == 21 ) {
						number_text = j+1+"st "+ text;
					} else if ( j+1 == 2 || j+1 == 22 ) {
						number_text = j+1+"nd "+ text;
					} else if ( j+1 == 3 ) {
						number_text = j+1+"rd "+ text;
					} else {
						number_text = j+1+"th "+ text;
					}
				} else {
					number_text = j+1+'. '+ text;
				}
				
				if ( j != final_due ) {
					jQuery( "#novalnet_instalment_table_invoice tbody" ).append( "<tr><td>" + number_text + "</td><td>" + split_amount + " €</td></tr>" );
				} else {
					jQuery( "#novalnet_instalment_table_invoice tbody" ).append( "<tr><td>" + number_text + "</td><td>" + last_instalment_due + " €</td></tr>" );
				}
			}
		});
}
