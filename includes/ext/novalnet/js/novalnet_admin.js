/*
 * Novalnet API script
 * By Novalnet (https://www.novalnet.de)
 * Copyright (c) Novalnet
 */

if (window.addEventListener) {
    window.addEventListener("load", novalnet_admin);
} else if (window.attachEvent) {
    window.attachEvent("load", novalnet_admin);
}

function novalnet_admin() {
    
    jQuery('input[type="text"]').on('keyup',function(e){
        let selected_name   =   jQuery(this).attr('name');          
        if( ! selected_name.match( /CUSTOMER_INFO/g ) && ! selected_name.match( /STYLE/g )) {       
            if ( this.value != '' && isNaN( this.value ) ) {
                this.value      =   0;
            }
        }
    });
    //capture authorize
        jQuery('#set_limit_title, #set_limit_desc').hide();
        jQuery('[name*="_ONHOLD_LIMIT]"]').hide();
            jQuery('[name*="_ONHOLD]"]').click(function () {
                if (jQuery('[name*="_ONHOLD]"]').prop('checked') == true) {
                        jQuery('#set_limit_title, #set_limit_desc').hide();
                        jQuery('[name*="_ONHOLD_LIMIT]"]').hide().val('');
                }
                if (jQuery('[name*="_ONHOLD]"]').prop('checked') == false) {
                        jQuery('#set_limit_title, #set_limit_desc').show();
                        jQuery('[name*="_ONHOLD_LIMIT]"]').show();
                }
                
            });
        if (jQuery('[name*="_ONHOLD]"]').prop('checked') == false) {
            jQuery('#set_limit_title, #set_limit_desc').show();
            jQuery('[name*="_ONHOLD_LIMIT]"]').show();
        }      

jQuery('button[id=saveButton]').on('click', function(event){    
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_SEPA_PAYMENT_DUE_DATE]"]').attr('id', 'sepa_due_date');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_PAYMENT_DUE_DATE]"]').attr('id', 'guarantee_sepa_due_date');
        performAdminValidations(event);
    });
function performAdminValidations(event) {
       
  if (jQuery('#sepa_due_date').val() != undefined && jQuery.trim(jQuery('#sepa_due_date').val()) != '') {
        if (isNaN(jQuery('#sepa_due_date').val()) || jQuery('#sepa_due_date').val() < 2 || jQuery('#sepa_due_date').val() > 14) {
                event.preventDefault();
                alert(jQuery('#sepa_due_date_error').val());
        }
   } else if(jQuery('#guarantee_sepa_due_date').val() != undefined && jQuery.trim(jQuery('#guarantee_sepa_due_date').val()) != '') {
       if ( isNaN(jQuery('#guarantee_sepa_due_date').val()) || jQuery('#guarantee_sepa_due_date').val() < 2 || jQuery('#guarantee_sepa_due_date').val() > 14) {
            event.preventDefault();
            alert(jQuery('#sepa_due_date_error').val());
       }
   } 
}
    
}


/**
* Creates Installment Select Fields
* @param config_name string
* @param id string
*/
function create_installment_fields( config_name, id ) {

    let config_period_name = "configuration["+ config_name + "_PERIOD]";
    let config_cycle_name = "configuration["+ config_name + "_CYCLE]";
    let period_id = id + '_period';
    let cycle_id = id + '_cycle';

    jQuery('input[name="' + config_period_name + '"]').attr('id', period_id );
    jQuery('input[name="' + config_cycle_name + '"]').attr('id', cycle_id );
    console.log( 'input[name="' + config_cycle_name + '"]' );

    var selected_period = jQuery('#'+ period_id).val();
    var selected_instalment_cycles = jQuery('#'+ cycle_id).val() || '';
    jQuery('#'+ period_id ).replaceWith('<select class="form-control" id="'+ period_id + '" name= "'+config_period_name+'"></select>');
    var lang = jQuery('#nn_lang').val();    
    var instalment_periods = {'1M':'per month', '2M':'per 2 months', '3M':'per 3 months', '4M':'per 4 months', '6M':'per 6 months'};
    if (lang == 'de')
        instalment_periods = {'1M':'pro Monate', '2M':'pro 2 Monate', '3M':'pro 3 Monate', '4M':'pro 4 Monate', '6M':'pro 6 Monate'};
        
    jQuery.each(instalment_periods, function( index, value ) {
        jQuery('#'+period_id).append(jQuery('<option>', {
        value: jQuery.trim(index),
        text: jQuery.trim(value)
        }));
    });
    if(selected_period != '') {
        jQuery('#'+period_id).val(selected_period);
    }
    jQuery('#'+ cycle_id ).replaceWith('<select multiple class="form-control" id="'+cycle_id+'" name= "'+config_cycle_name+'[]"></select>');
    
    var cycle_text = ( lang == 'en') ? ' cycles' : ' raten' ;

    var selected_cycle_array = selected_instalment_cycles.replace(/ /g,'').split(",");
    for( var cycle = 2; cycle < 25; cycle++ )
    {
    if( cycle != 21 && ( cycle % 3 == 0 || ( cycle % 2== 0 && cycle < 11 ) ) ) {
        var attr = {
        value: cycle,
        text: cycle + cycle_text
        };
    if( jQuery.inArray( cycle.toString(), selected_cycle_array ) !== -1 ) {
    attr.selected = 'selected';
    }
    jQuery('#'+cycle_id).append(jQuery('<option>', attr ));
    }
}
}
