/*
 * Novalnet Credit Card script
 * @author      Novalnet AG <technic@novalnet.de>
 * @copyright   Novalnet
 * @license     https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
if (window.addEventListener) { // For all major browsers, except IE 8 and earlier
    window.addEventListener("load", novalnetCCLoad);
} else if (window.attachEvent) { // For IE 8 and earlier versions
    window.attachEvent("onload", novalnetCCLoad);
}
var targetOrigin = 'https://secure.novalnet.de';
function getFormValue()
{   
    var styleObj = {
        labelStyle : jQuery('#nn_cc_default_label').val(),
        inputStyle : jQuery('#nn_cc_default_input').val(),
        styleText  : jQuery('#nn_cc_default_css').val(),
        };
    var textObj   = {
        cvcHintText: jQuery('#nn_cvc_hint').val(),
        errorText  : jQuery('#validate_account_details').val(),
        card_holder : {
            labelText : jQuery('#nn_holder_label').val(),
            inputText : jQuery('#nn_holder_input').val(),
        },
        card_number : {
            labelText : jQuery('#nn_number_label').val(),
            inputText : jQuery('#nn_number_input').val(),
        },
        expiry_date : {
            labelText : jQuery('#nn_expiry_label').val(),
            inputText : jQuery('#nn_expiry_input').val(),
        },
        cvc  : {
            labelText : jQuery('#nn_cvc_label').val(),
            inputText : jQuery('#nn_cvc_input').val(),
        }
    };
    var requestObj = {
        callBack: 'createElements',
        customText: textObj,
        customStyle: styleObj
    };
    ccloadIframe(JSON.stringify(requestObj))
}

function novalnetCCLoad()
{
    jQuery('form[name=checkout_payment]').submit(
        function (event) {
            
            var selected_payment = (jQuery("input[name='payment']").attr('type') == 'hidden') ? jQuery("input[name='payment']").val() : jQuery("input[name='payment']:checked").val();   
            
            if ( selected_payment == 'novalnet_cc') {
                var pan_hash = jQuery("#nn_cc_pan_hash").val();
               
                if ((pan_hash == '') || (pan_hash == undefined)) {                    
                     event.preventDefault();
                     getHashFromserver();
                }
            }
        }
    );
    if (window.addEventListener) {
        // addEventListener works for all major browsers
        window.addEventListener('message', function (e) {
            addEvent(e);
        }, false);
    } else {
        // attachEvent works for IE8
        window.attachEvent('onmessage', function (e) {
            addEvent(e);
        });
    }
    // Function to handle Event Listener
    function addEvent(e)
    {
        if (e.origin === targetOrigin) {
            if (typeof e.data === 'string') {
                // Convert message string to object
                var data = eval('(' + e.data.replace(/(<([^>]+)>)/gi, "") + ')');
            } else {
                var data = e.data;
            }
            
            if (data['callBack'] == 'getHash') {
                if (data['error_message'] != undefined) {
                    alert(jQuery('<textarea />').html(data['error_message']).text());
                    return false;
                } else {
                    jQuery('#nn_cc_pan_hash').val(data['hash']);
                    jQuery('#nn_cc_uniqueid').val(data['unique_id']);
                    jQuery('form[name=checkout_payment]').submit();
                }
            }
            if (data['callBack'] == 'getHeight') {
                jQuery('#nnIframe').attr('height',data['contentHeight']);
            }
        }
    }

    function getHashFromserver()
    {
        ccloadIframe(JSON.stringify({callBack: 'getHash'})); // Call the postMessage event for getting the iframe content height dynamically
    }

}

function ccloadIframe(request)
{
    var iframe = jQuery('#nnIframe')[0];
    iframe = iframe.contentWindow ? iframe.contentWindow : iframe.contentDocument.defaultView;
    iframe.postMessage(request, targetOrigin);
}
