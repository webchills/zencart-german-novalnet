/**
 * Novalnet payment module
 * This script is used for loading payments from Novalnet
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_payment_form.js
 *
 */
jQuery(document).ready(function () {
    let order_info = JSON.parse(jQuery("#nn_article_details").val());
    const novalnetPaymentIframe = new NovalnetPaymentForm();

    Object.values(order_info).forEach((item) => {
        item['label'] = item['label'].replace('#single_quote', "'");
    });

    const paymentFormRequestObj = {
        iframe: '#novalnet_iframe',
        initForm : {
            orderInformation : {
                lineItems: order_info,
                billing: {},
                labelText: jQuery("#nn_wallet_total_label").val()
            },
            uncheckPayments: true,
            setWalletPending: true,
            showButton : false
        }
    };

    jQuery('#pmt-novalnet_payments').hide();
    jQuery('#pmt-novalnet_payments').prevUntil('input[type="radio"][name="payment"]').not('label').css({"display": "none"});

    /**
     * Initiate the payment form IFRAME
     */
    novalnetPaymentIframe.initiate(paymentFormRequestObj);

    /**
     * Wallet payments response callback
     */
    novalnetPaymentIframe.walletResponse({
        onProcessCompletion: (response) => {
            if (response.result.status == 'SUCCESS') {
                jQuery('#nn_payment_details').val(JSON.stringify(response));
                let submitEl = jQuery("div #paymentSubmit :submit");
                jQuery(submitEl).click();
                return {status: 'SUCCESS', statusText: 'successfull'};
            } else {
                return {status: 'FAILURE', statusText: 'failure'};
            }
        }
    });

    /**
     * Payment form validation result callback
     */
    novalnetPaymentIframe.validationResponse((data) => {});

    /**
     * Gives selected payment method
     */
    novalnetPaymentIframe.selectedPayment((data) => {
            jQuery('#nn_payment_details').val(null);
            jQuery('#pmt-novalnet_payments').prop('checked', true);
            jQuery('#pmt-novalnet_payments').hide();
            jQuery('#nn_selected_payment_data').val(JSON.stringify(data));
        if (jQuery("input[id*='pmt-novalnet_payments']:checked") && (data.payment_details.type == 'GOOGLEPAY' || data.payment_details.type == 'APPLEPAY')) {
            jQuery('.button_continue_checkout').hide();
            if (jQuery('#conditions').length) {
                jQuery('#conditions').prop('checked', true);
            }
        } else {
            jQuery('.button_continue_checkout').show();
            if (jQuery('#conditions').length) {
                jQuery('#conditions').prop('checked', false);
            }
        }
        });

    /**
     * To uncheck novalnet payments when other payments selected
     */
    $(document).on('click', 'input[name="payment"]', function (event) {
        if (this.checked) {
            novalnetPaymentIframe.uncheckPayment();
        }
    });

    /**
     * To get payment response from iframe
     */
    document.querySelector('#paymentSubmit').addEventListener('click', function (event) {
        if (jQuery('#nn_payment_details').val() == '') {
            event.preventDefault();
            event.stopImmediatePropagation();
            novalnetPaymentIframe.getPayment((data) => {
                    jQuery('#nn_payment_details').val(JSON.stringify(data));
                    jQuery('form[name="checkout_payment"]').submit();
                    return true;
            });
        }
    });
});
