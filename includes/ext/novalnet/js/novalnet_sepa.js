if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener('load', novalnetSepaLoad);
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent('onload', novalnetSepaLoad);
}
function showmandate(id)
{
  jQuery('#'+id).toggle();
}
function novalnetSepaLoad() { 
		jQuery('#novalnet_instalment_sepa_period').val(0);
        
        //show instalment invoice plan details
        jQuery( document ).on('change', '#novalnet_instalment_sepa_period' , function() {
            var novalnet_order_cycle_period = jQuery( "#novalnet_instalment_sepa_period option:selected").val();
            var order_amount = jQuery('#nn_sepa_order_amount').val();
            var total_amount = 0, instalment_due = 0, last_instalment_due = 0;
            total_amount = (parseFloat(order_amount)).toFixed(2);
            for ( var i=1;i<=novalnet_order_cycle_period;i++ ) {
                if ( i != novalnet_order_cycle_period ) {
                    split_amount = (parseFloat(total_amount/novalnet_order_cycle_period)).toFixed(2);
                    instalment_due = parseFloat(instalment_due) + parseFloat(split_amount);
                } else {
                    last_instalment_due = (parseFloat (total_amount - instalment_due)).toFixed(2);
                }
            }
           
            var lang = jQuery( '#nn_sepa_lang' ).val();
            var text = jQuery( '#instalment_sepa_text' ).val();
            var instalment_number = jQuery( '#instalment_sepa_number' ).val();          
            var monthly_instalment = jQuery( '#monthly_sepa_instalment' ).val();            
            var number_text = '';
            var final_due = novalnet_order_cycle_period-1;
            if (novalnet_order_cycle_period == '0') {
                jQuery( "#novalnet_instalment_table_sepa thead tr" ).remove();
            } else {
                jQuery( "#novalnet_instalment_table_sepa thead tr" ).remove();
                jQuery( "#novalnet_instalment_table_sepa thead" ).append( "<tr><th>" + instalment_number + "</th><th>" + monthly_instalment + "</th></tr>" );
            }
            jQuery( "#novalnet_instalment_table_sepa" ).show(); 
            jQuery( "#novalnet_instalment_table_sepa tbody tr" ).remove();
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
                    jQuery( "#novalnet_instalment_table_sepa tbody" ).append( "<tr><td>" + number_text + "</td><td>" + split_amount + " €</td></tr>" );
                } else {
                    jQuery( "#novalnet_instalment_table_sepa tbody" ).append( "<tr><td>" + number_text + "</td><td>" + last_instalment_due + " €</td></tr>" );
                }
            }
        });
        
jQuery('#novalnet_sepa_bank_iban, #novalnet_guarantee_sepa_bank_iban, #novalnet_instalment_sepa_bank_iban').keyup(function(event) { 
        var iban = jQuery('#'+this.id).val().toUpperCase();
        jQuery('#'+this.id).val(iban);
        this.value = this.value.toUpperCase();
        var field = this.value;
        var value = "";
        for(var i = 0; i < field.length;i++){
            if(i <= 1){
                if(field.charAt(i).match(/^[A-Za-z]/)){
                value += field.charAt(i);
                }
            }
            if(i > 1){
                if(field.charAt(i).match(/^[0-9]/)){
                value += field.charAt(i);
                }
            }
        }
        field = this.value = value;
    });
}

// Validate the IBAN field
allowAlphaNumeric = function(e) {
  var keycode = ( 'which' in e ) ? e.which : e.keyCode,
  reg     = /^(?:[0-9a-z]+$)/gi;
  return ( reg.test( String.fromCharCode( keycode ) ) || 0 === keycode || 8 === keycode );
};
// Validate the Account Holder field
allowName = function (e) {
var keycode = ( 'which' in e ) ? e.which : e.keyCode,
      reg     = /[^0-9\[\]\/\\#,+@!^()$~%'"=:;<>{}\_\|*?`]/g;
  return ( reg.test( String.fromCharCode( keycode ) ) || 0 === keycode || 8 === keycode );
};




