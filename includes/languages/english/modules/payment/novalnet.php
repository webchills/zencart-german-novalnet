<?php
/**
* This script is used for English language content
*
* @author Novalnet AG
* @copyright Copyright (c) Novalnet
* @license https://www.novalnet.de/payment-plugins/kostenlos/lizenz
* @link https://www.novalnet.de
*
* This free contribution made by request.
*
* If you have found this script useful a small
* recommendation as well as a comment on merchant
*
* Script : novalnet.php
*/
define('MODULE_PAYMENT_NOVALNET_STATUS_TITLE', 'Enable payment method');
define('MODULE_PAYMENT_NOVALNET_STATUS_DESC', '');

define('MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE', 'Enable test mode');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC', 'Enable this option to test payments at your checkout page. In the test mode the amount will not actually be charged by Novalnet. Remember to disable the test mode again after testing to ensure that actual purchased are properly charged.');

define('MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_TITLE', 'Notification for the buyer');
define('MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_DESC', 'The entered text will be displayed at the checkout page');

define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE', 'Define a sorting order');
define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC', 'The payment methods will be listed in your checkout (in ascending order) based on your given sorting order.');

define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE', 'Completed order status');
define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC', 'Status to be used for successful orders.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_ORDER_STATUS_TITLE', 'Callback order status');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_ORDER_STATUS_DESC', 'Status to be used when callback script is executed for payment received by Novalnet.');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE', 'Payment zone');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC', 'This payment method will be displayed for the mentioned zone(-s)');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_TITLE', 'Payment confirmation');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_DESC', 'Choose whether or not the payment should be charged immediately. <b>Capture:</b> completes the transaction by transferring the funds from buyer account to merchant account. <b>Authorize</b> verifies payment details and reserves funds to capture it later, giving time for the merchant to decide on the order.');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_TITLE', '<div id="set_limit_title">Minimum transaction amount for authorization (in minimum unit of currency. E.g. enter 100 which is equal to 1.00)</div>');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_DESC','<div id="set_limit_desc">Transactions above this amount will be "authorized only" until you capture. Leave the field blank to authorize all transactions.</div>');

define('MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS', 'Novalnet transaction details');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet transaction ID: ');
define('MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE', 'Test order');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG', '<span style="color:red;">The payment will be processed in the test mode therefore amount for this transaction will not be charged<br/></span>');
define('MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH', 'Please transfer the amount to the below mentioned account details of our payment processor Novalnet');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER', 'Account holder');
define('MODULE_PAYMENT_NOVALNET_IBAN', 'IBAN');
define('MODULE_PAYMENT_NOVALNET_DUE_DATE', 'Due date');
define('MODULE_PAYMENT_NOVALNET_BANK', 'Bank');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', 'Amount');
define('MODULE_PAYMENT_NOVALNET_SWIFT_BIC', 'BIC');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF1', 'Payment Reference 1');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF2', 'Payment Reference 2');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MULTI_TEXT', 'Please use any one of the following references as the payment reference, as only through this way your payment is matched and assigned to the order:');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR', 'While redirecting some data has been changed. The hash check failed');

define('MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR', 'Your account details are invalid');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PLAN','Choose your instalment plan');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PLAN_TEXT','Choose the financing option that best fits your needs and you will be charged based on that chosen plan');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TEXT','Net loan amount : ');

define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE', 'Your date of birth');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_DOB_ERROR_MSG', 'Please enter valid birthdate');
define('MODULE_PAYMENT_NOVALNET_AGE_ERROR', 'You need to be at least 18 years old');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_TEXT', 'Instalment');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NUMBER', 'Instalment number');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_MONTHLY_AMOUNT', 'Monthly instalment amount');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_INFO', 'Instalment Information: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED', 'Processed instalments: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE', 'Due instalments: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NXT_AMOUNT', 'Instalment Cycle Amount: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CHOOSE_PLAN', 'Choose any one of the available instalment plan');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DEBIT_TEXT', 'The instalment amount for this cycle %s will be debited from your account in one - three business days.');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_COUNTRY', '<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee are not met (Only Germany, Austria or Switzerland are allowed)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_CURRENCY', '<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee are not met (Only EUR currency allowed)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_ADDRESS','<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee are not met (The shipping address must be the same as the billing address)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_AMOUNT','<span style="color:red;">The payment cannot be processed, because the basic requirements for the payment guarantee are not met (Minimum order amount must be %s)</span>');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_CANCEL', 'The transaction has been canceled on %s');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY_CANCEL', 'The transaction has been canceled due to: %s');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_HOLD_TO_PENDING','The transaction status has been changed from on hold to pending for the TID: %s on %s.');


define('MODULE_PAYMENT_NOVALNET_CALLBACK_PENDING_TO_HOLD','The transaction status has been changed from pending to on-hold for the TID: %s on %s.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_CREDIT', 'Novalnet callback script executed successfully for TID: %s with the amount %s on %s. Please refer to paid transactions in Novalnet Admin Portal with the TID: %s');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_EXECUTE', 'Novalnet Callback Script executed successfully for the TID: %s with amount %s on %s.');
define('MODULE_PAYMENT_GUARANTEE_PAYMENT_MAIL_MESSAGE','We are pleased to inform you that your order has been confirmed.');
define('MODULE_PAYMENT_GUARANTEE_PAYMENT_MAIL_SUBJECT','Order Confirmation - Your Order %s with %s has been confirmed!');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_CONFIRM','The transaction has been confirmed on %s');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_CHARGEBACK', 'Chargeback executed successfully for the TID: %s amount: %s on %s. The subsequent TID: %s.');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_BOOKBACK', 'Refund/Bookback executed successfully for the TID: %s amount: %s on %s. The subsequent TID: %s.');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_CYCLE_TEXT', 'The next instalment cycle have arrived for the instalment order %s placed at the store %s, kindly refer further details below.');
define('MODULE_PAYMENT_NOVALNET_REFERENCE', 'Payment Reference: ');

define('MODULE_PAYMENT_SEPA_DUE_DATE_VALIDATION', 'SEPA Due date is not valid');
define('MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_ERROR', 'Please enter valid due date');
define('MODULE_PAYMENT_GUARNTEE_REQUIREMENT', '<h5>Basic requirements for payment guarantee</h5><ul>
    <li>Allowed countries: AT, DE, CH</li>
    <li>Allowed currency: EUR</li>
    <li>Minimum order amount: 9,99 EUR or more</li>
    <li>Age limit: 18 years or more</li>
    <li>The billing address must be the same as the shipping address</li>   
</ul><br/>');

define('MODULE_PAYMENT_INSTALMENT_REQUIREMENT','<h5>Basic requirements for instalment payment</h5><ul>
<li>Allowed countries: AT, DE, CH</li>
<li>Allowed currency: EUR</li>
<li>Minimum order amount: 19,98 EUR or more</li>
<li>Please note that the instalment cycle amount has to be a minimum of 9.99 EUR and the installment cycles which do not meet this criteria will not be displayed in the installment plan.</li>
<li>Age limit: 18 years or more</li>
<li>The billing address must be the same as the shipping address</li>
</ul><br>');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MESSAGE', 'Your order is being verified. Once confirmed, we will send you our bank details to which the order amount should be transferred. Please note that this may take up to 24 hours.');
define('MODULE_PAYMENT_NOVALNET_GUARANTEED_SEPA_MESSAGE', 'Your order is under verification and we will soon update you with the order status. Please note that this may take upto 24 hours.');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT', 'YYYY-MM-DD');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT_ERROR', 'The date format is invalid');
define('MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR', 'Please fill in all the mandatory fields');
define('MODULE_PAYMENT_NOVALNET_VALID_EMAIL_ERROR', 'Email fields are not valid ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY', 'Instalment Summary');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PAID_DATE', 'Paid date');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DATE', 'Next Instalment Date');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE', 'Reference');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS', 'Status');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_INFO', 'The instalment amount for this cycle %s %s will be debited from your account in one - three business days.');
define('MODULE_PAYMENT_NOVALNET_CYCLES','%s Cycles');
define('MODULE_PAYMENT_NOVALNET_PER_MONTH',' per %s month');
?>
