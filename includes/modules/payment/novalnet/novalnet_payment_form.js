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
	    var order_info = JSON.parse(jQuery("#nn_article_details").val());    
	        
	    Object.values(order_info).forEach((item) => {
			item['label'] = item['label'].replace('#single_quote', "'");;
		})
	    
        const paymentFormRequestObj = {
            iframe: '#v13PaymentForm',
            initForm : {
                orderInformation : {
                        lineItems: order_info,
                        billing: {
                            requiredFields: ["postalAddress", "phone", "email"]
                        },
                },
                uncheckPayments: true,
                showButton : false
            }
        };
        jQuery('#pmt-novalnet_payments').hide();
        jQuery('#pmt-novalnet_payments').prevUntil('input[type="radio"][name="payment"]').not('label').css({"display": "none"});
        var v13PaymentForm = new NovalnetPaymentForm();
    
        // initiate form
        v13PaymentForm.initiate(paymentFormRequestObj);         $(document).on('click', 'input[name="payment"]', function (event) {
            if (this.checked) {
                v13PaymentForm.uncheckPayment();
            }
         });
        // receive wallet payment Response like gpay and applepay
        v13PaymentForm.walletResponse({
            onProcessCompletion: function (response){				
                if (response.result.status == 'SUCCESS') {
                    jQuery('#nn_payment_details').val(JSON.stringify(response));                  
                    var submitEl = jQuery("div #paymentSubmit :submit");
                    jQuery(submitEl).click();  
                    return {status: 'SUCCESS', statusText: 'successfull'};                                               
                } else {
                    return {status: 'FAILURE', statusText: 'failure'};
                }
            }
        });        // receive form validation response
        v13PaymentForm.validationResponse(function (data) {
        });        // receive form selected payment action
        v13PaymentForm.selectedPayment(
            (data)=>{
                jQuery('#nn_payment_details').val(null);
                jQuery('#pmt-novalnet_payments').prop('checked', true);
                jQuery('#pmt-novalnet_payments').hide();
                jQuery('#nn_selected_payment_data').val(JSON.stringify(data));
                if (jQuery("input[id*='pmt-novalnet_payments']:checked") && (data['payment_details']['type'] == 'GOOGLEPAY' || data['payment_details']['type'] == 'APPLEPAY')) {
                    jQuery('.button_continue_checkout').hide();
                    if(jQuery('#conditions').length)
                    jQuery('#conditions').prop('checked', true);
                } else {
                    jQuery('.button_continue_checkout').show();
                    if(jQuery('#conditions').length)
                    jQuery('#conditions').prop('checked', false);
                }
            }
        )

        // Get the postmessage data
        document.querySelector('#paymentSubmit').addEventListener('click',function(event) {
            if (jQuery('#nn_payment_details').val() == '') {
                event.preventDefault();
                event.stopImmediatePropagation();
                v13PaymentForm.getPayment(
                    (data)=>{
                        jQuery('#nn_payment_details').val(JSON.stringify(data));
                        jQuery('form[name="checkout_payment"]').submit();
                        return true;
                    }
                )
            }
        });
});
