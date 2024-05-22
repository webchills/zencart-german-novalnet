/**
 * Novalnet payment module
 * This script is used for handling validation of post process of
 * Novalnet payment orders
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_extension.js
 *
 */

jQuery(document).ready(function () {
    let elements = jQuery(".refundBtns"),
        nn_instalment_refund_function = function () {
            let cycle = this.getAttribute("data-instalment"),
                refund_id = jQuery('#instalment_refund_'+ cycle);

            if (refund_id.style.display == "none") {
                refund_id.style.display = "block";
            } else {
                refund_id.style.display = "none";
            }
        };

    for (let i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', nn_instalment_refund_function, false);
    }
    $(document).on('click', '#nn_instacancel_allcycles, #nn_instacancel_remaincycles, #nn_instalment_cancel', function (event) {
        if ($("#novalnet_instalment_cancel").css({"display": "none"})) {
            $("#novalnet_instalment_cancel").css({"display": "inline-flex"});
            $("#nn_instalment_cancel").css({"display": "none"});
        } else {
            $("#novalnet_instalment_cancel").css({"display": "none"});
            $("#nn_instalment_cancel").css({"display": "block"});
        }
        if (this.id == 'nn_instacancel_allcycles') {
            if (!confirm(jQuery("[name=nn_insta_allcycles]").val())) {
                return false;
            }
        } else if (this.id == 'nn_instacancel_remaincycles') {
            if (!confirm(jQuery("[name=nn_insta_remainingcycles]").val())) {
                return false;
            }
        }
    });
});

function void_capture_status()
{
    if (jQuery('#trans_status').val() == '') {
        jQuery('#nn_void_capture_error').html(jQuery("[name=nn_select_status]").val());
        return false;
    }

    let display_status = jQuery("#trans_status").val() == 'CONFIRM' ? jQuery("[name=nn_capture_update]").val() : jQuery("[name=nn_void_update]").val();
    if (!confirm(display_status)) {
        return false;
    }

    let url = jQuery('#novalnet_status_change').attr('action');
    if (jQuery("#trans_status").val() == 'CONFIRM') {
        jQuery('#novalnet_status_change').attr('action', url + '&action=doCapture');
    } else {
        jQuery('#novalnet_status_change').attr('action', url + '&action=doVoid');
    }
    return true;
}

function refund_amount_validation()
{
	if (jQuery('#refund_trans_amount').val() != undefined) {
        let amount = jQuery('#refund_trans_amount').val();
        if (amount.trim() == '' || amount == 0 || isNaN(amount)) {
            jQuery('#nn_refund_error').html(jQuery("[name=nn_amount_error]").val());
            return false;
        }
    }
    if (jQuery('#refund_trans_amount').val() != null) {
        if (!confirm(jQuery("[name=nn_refund_amount_confirm]").val())) {
            return false;
        }
    }
    if (jQuery('#book_amount').val() != null) {
        if (!confirm(jQuery("[name=nn_zero_amount_book_confirm]").val())) {
            return false;
        }
    }
}
