/**
 * Novalnet payment module
 * This script is used for auto configuring the merchant details
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_auto_config.js
 *
 */

/**
 * Auto configuration process
 */
jQuery(document).ready(function () {
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_PUBLIC_KEY]"]').attr('id', 'novalnet_signature').attr('autocomplete', 'off');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_TARIFF_ID]"]').attr('id', 'novalnet_tariff_id');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_ACCESS_KEY]"]').attr('id', 'novalnet_access_key').attr('autocomplete', 'off');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_CALLBACK_URL]"]').attr('id','novalnet_webhook_url');
    jQuery('input[name="configuration[MODULE_PAYMENT_NOVALNET_CALLBACK_URL]"]').attr('readonly',true);
    jQuery('#novalnet_signature, #novalnet_access_key').change(function () {
        if (jQuery('#novalnet_signature').val() != '' && jQuery('#novalnet_access_key').val() != '') {
            get_merchant_details();
            return true;
        } else if (jQuery('#novalnet_signature').val() == '' && jQuery('#novalnet_access_key').val() == '') {
            clear_basic_params();
        }
    }).change();

    jQuery('#novalnet_signature, #novalnet_access_key').closest('form').submit(function (event) {
        if (jQuery('#novalnet_signature').val() == '') {
            event.preventDefault();
            alert(jQuery('#merchant_credentials_error').val());
        }
    });

    if (jQuery('#novalnet_webhook_url').val() != '' && jQuery('#novalnet_webhook_url').val() != undefined) {
        jQuery('#webhook_url_button').on('click', function () {
            let webhook_url = jQuery.trim(jQuery('#novalnet_webhook_url').val()),
                regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

            if (webhook_url != '' && regex.test(webhook_url)) {
                if (confirm(jQuery('#nn_webhook_alert').val())) {
                    configure_webhook();
                    return true;
                } else {
                    return false;
                }
            } else if (!regex.test(webhook_url) || webhook_url === '' || webhook_url === undefined) {
                alert(jQuery('#nn_webhook_error').val());
                return false;
            }
        });
    }
    /** Get merchant data */
    function get_merchant_details()
    {
        let signature = jQuery.trim(jQuery('#novalnet_signature').val()),
            access_key = jQuery.trim(jQuery('#novalnet_access_key').val()),
            data_to_send = {'action': 'merchant_details', 'signature': signature, 'access_key': access_key, 'lang': jQuery('#nn_language').val()};

        do_ajax_call(data_to_send);
        return true;
    }

    /** Configure webhook URL in Novalnet system */
    function configure_webhook()
    {
        let signature = jQuery.trim(jQuery('#novalnet_signature').val()),
            access_key = jQuery.trim(jQuery('#novalnet_access_key').val()),
            webhook_url = jQuery.trim(jQuery('#novalnet_webhook_url').val());

        if (signature == '' || access_key == '') {
            alert(jQuery('#nn_key_error').val());
            clear_basic_params();
            return false;
        }

        let data_to_send = {'action': 'webhook_configure', 'signature': signature, 'access_key': access_key, 'webhook_url': webhook_url, 'lang': jQuery('#nn_language').val()};
        do_ajax_call(data_to_send);
        return true;
    }

    /** Handle the response */
    function process_result(result)
    {
        let saved_tariff_id = jQuery('#novalnet_tariff_id').val();
        jQuery('#novalnet_tariff_id').replaceWith('<select id="novalnet_tariff_id" name= "configuration[MODULE_PAYMENT_NOVALNET_TARIFF_ID]" ></select>');
        let tariff = result.merchant.tariff;
        if (tariff != undefined) {
            jQuery.each(tariff, function ( index, value ) {
                let tariff_val = index;
                jQuery('#novalnet_tariff_id').append(jQuery('<option>', {
                    value: jQuery.trim(tariff_val),
                    text: jQuery.trim(value.name)
                }));
                if (saved_tariff_id != undefined && saved_tariff_id == tariff_val) {
                     jQuery('#novalnet_tariff_id').val(tariff_val);
                }
            });
        } else {
            clear_basic_params();
            alert(result.status_desc);
        }
    }

    /** Clear basic params */
    function clear_basic_params()
    {
        jQuery('#novalnet_signature').val('');
        jQuery('#novalnet_access_key').val('');
        jQuery('#novalnet_tariff_id').find('option').remove();
        jQuery('#novalnet_tariff_id').append(jQuery('<option>', {
            value: '',
            text : '',
        }));
    }

    /** AJAX call processing */
    function do_ajax_call(data_to_send)
    {
        jQuery.ajax({
            type : 'POST',
            url  : '../novalnet_auto_config.php',
            data : data_to_send,
            success: function (result) {
                let response = (isJson(result)) ? JSON.parse(result) : result;
                if (data_to_send.action == 'merchant_details') {
                    process_result(response);
                } else if (data_to_send.action == 'webhook_configure') {
                    if (response.result.status == 'SUCCESS') {
                        alert(jQuery('#nn_webhook_text').val());
                    } else {
                        alert(jQuery('#nn_webhook_alert').val());
                    }
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                const errMsg = (xhr.responseJSON.message) ? xhr.responseJSON.message : errorThrown;
                alert(errMsg);
            }
        });
    }

    /** validates is JSON or not */
    function isJson(data)
    {
        try {
            JSON.parse(data);
            return true;
        } catch (e) {
            return false;
        }
    }
});
