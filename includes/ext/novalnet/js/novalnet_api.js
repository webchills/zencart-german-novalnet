/*
 * Novalnet API script
 * @author      Novalnet AG <technic@novalnet.de>
 * @copyright   Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener("load", novalnet_api_load)
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent("onload", novalnet_api_load);
}

function novalnet_api_load()
{
    var regex = /^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-\.]+)+([;]([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-\.]+))*$/;
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO]"]').attr('id', 'novalnet_mail_to');    
    jQuery('form[name="modules"').on('submit',function(e){
        let novalnet_mail_to = jQuery.trim( jQuery('#novalnet_mail_to').val() );    
        if(  novalnet_mail_to != '' && ! novalnet_mail_to.match( regex ) ) {                    
            alert(jQuery('#email_validation_error').val());
            e.preventDefault();
        }
    });
    
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_PUBLIC_KEY]"]').attr('id', 'novalnet_public_key');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_VENDOR_ID]"]').attr('id', 'novalnet_vendor_id');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_AUTH_CODE]"]').attr('id', 'novalnet_auth_code');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_PRODUCT_ID]"]').attr('id', 'novalnet_product');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_TARIFF_ID]"]').attr('id', 'novalnet_tariff_id');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY]"]').attr('id', 'novalnet_access_key');
    jQuery('#novalnet_vendor_id,#novalnet_auth_code,#novalnet_product,#novalnet_access_key').attr("readonly", true);
    if ( jQuery('#novalnet_public_key').val() != '' ) {
        get_merchant_details();
    }
    jQuery('#novalnet_public_key').change(function () {
        get_merchant_details();        
    });
    jQuery('#novalnet_public_key').closest('form').submit(function (event) {
        var form = this;
        if (jQuery('#novalnet_public_key').val() == '') {
         event.preventDefault();
         alert(jQuery('#merchant_credentials_error').val());
        }                
        if (jQuery('#novalnet_ajax_complete').attr('value') == '0') {
            event.preventDefault();
            jQuery(document).ajaxComplete(function () {
                jQuery(form).submit();
            });
        }        
    });
}

function null_basic_params()
{
    jQuery('#novalnet_vendor_id, #novalnet_auth_code, #novalnet_product, #novalnet_access_key, #novalnet_public_key').val('');
    jQuery('#novalnet_tariff_id').find('option').remove();
    jQuery('#novalnet_ajax_complete').attr('value', 1);
    jQuery('#novalnet_tariff_id').replaceWith('<input id="novalnet_tariff_id" name="configuration[MODULE_PAYMENT_NOVALNET_TARIFF_ID]" type="text" id="novalnet_tariff_id">');
}

function get_merchant_details()
{
    var public_key = jQuery('#novalnet_public_key').val();
    var language = jQuery('#nn_language').val();    
    if (jQuery.trim(public_key) == '') {
        null_basic_params();
        return false;
    }
    var data_to_send = {
        "hash": public_key,
        'lang': language
    }
    var filePath = '../novalnet_autoconfig.php';

    jQuery('#novalnet_ajax_complete').attr('value', 0);
     
     data_to_send = jQuery.param(data_to_send);

    if ('XDomainRequest' in window && window.XDomainRequest !== null) {
        var xdr = new XDomainRequest();
        xdr.open('POST', filePath);
        xdr.onload = function () {
            process_result(data_to_send);
        };
        xdr.send(data_to_send);
    } else {
         var xmlhttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    process_result(xmlhttp.responseText);
                }
            }
        xmlhttp.open("POST", filePath, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(data_to_send);
    }
    return true;
}

function process_result(hash_string)
{
    var result = JSON.parse(hash_string);
    var saved_tariff_id = jQuery('#novalnet_tariff_id').val();
    jQuery('#novalnet_tariff_id').replaceWith('<select id="novalnet_tariff_id" name= "configuration[MODULE_PAYMENT_NOVALNET_TARIFF_ID]" ></select>');
    var tariff = result.tariff;
    if (tariff != undefined) {
        jQuery('#novalnet_vendor_id').val(result.vendor_id);
        jQuery('#novalnet_auth_code').val(result.auth_code);
        jQuery('#novalnet_product').val(result.product_id);
        jQuery('#novalnet_access_key').val(result.access_key);
        jQuery('#novalnet_ajax_complete').attr('value', 1);
        jQuery.each(tariff, function ( index, value ) {
            var tariff_val = value.type + '-' + index;
            jQuery('#novalnet_tariff_id').append(jQuery('<option>', {
                value: jQuery.trim(tariff_val),
                text: jQuery.trim(value.name)
            }));
            if (saved_tariff_id != undefined && saved_tariff_id == tariff_val) {
                 jQuery('#novalnet_tariff_id').val(tariff_val);
            }
        });

    } else {
        null_basic_params();
        alert(result.status_desc);
    }
}
