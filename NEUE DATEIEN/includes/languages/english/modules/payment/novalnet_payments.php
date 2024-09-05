<?php
/**
 * Novalnet payment module
 *
 * This script is used for English language
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_payments.php
 *
 */
define('MODULE_PAYMENT_NOVALNET_CREDENTIALS_ERROR', 'Please enter a valid Product Activation Key and Payment Access key');
define('MODULE_PAYMENT_NOVALNET_WEBHOOKURL_ERROR', 'Please enter the valid Webhook URL');
define('MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_SUCCESS_TEXT', 'Notification / Webhook URL is configured successfully in Novalnet Admin Portal');
define('MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_ALERT_TEXT', 'Are you sure you want to configure the Webhook URL in Novalnet Admin Portal?');
define('MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR', 'Please fill in the required fields');

define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE', 'Novalnet API Configuration');
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION', '<span style="font-weight: bold; color:#878787;">Please read the Installation Guide before you start and login to the <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin Portal</a> using your merchant account. To get a merchant account, mail to <a style="font-weight: bold; color:#0080c9;" href="mailto:sales@novalnet.de">sales@novalnet.de</a> or call +49 (089) 923068320</span><br/><br/><span style="font-weight: bold; color:#878787;">Payment plugin configurations are now available in the <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin Portal</a>.Navigate to the Account -> Payment plugin configuration of your projects to configure them.</span><br/><br/><span style="font-weight: bold; color:#878787;">Our platform offers a test mode for all requests; You can control the behaviour of the payment methods by using the <a href="https://developer.novalnet.de/testing" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet test payment data</a></span>');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE_DUE_DATE', 'Please transfer the amount of %1$s to the following account on or before %2$s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE', 'Please transfer the amount of %s to the following account.');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE_DUE_DATE', 'Please transfer the instalment cycle amount of %1$s to the following account on or before %2$s');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE', 'Please transfer the instalment cycle amount of %1$s to the following account.');
define('MODULE_PAYMENT_NOVALNET_MULTIBANCO_NOTE', 'Please use the following payment reference details to pay the amount of %s at a Multibanco ATM or through your internet banking.');
define('MODULE_PAYMENT_NOVALNET_MULTIBANCO_SUPPLIER_NOTE', 'Entity: %s');
define('MODULE_PAYMENT_NOVALNET_BANK_NAME', ' Bank: ');
define('MODULE_PAYMENT_NOVALNET_IBAN', 'IBAN: ');
define('MODULE_PAYMENT_NOVALNET_BIC', ' BIC: ');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER', 'Account holder: ');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', ' Amount: ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MODE', 'Test order');
define('MODULE_PAYMENT_NOVALNET_BANK_PLACE', 'Place: ');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet transaction ID: ');
define('MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE', 'Slip expiry date : ');
define('MODULE_PAYMENT_NOVALNET_NEAREST_STORE_DETAILS', 'Store(s) near to you: ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE_TEXT', 'Please use the following payment references when transferring the amount. This is necessary to match it with your corresponding order');
define('MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT', 'Confirm');
define('MODULE_PAYMENT_NOVALNET_REFUND_TEXT', 'Refund');
define('MODULE_PAYMENT_NOVALNET_CANCEL_TEXT', 'Cancel');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR', 'Payment was not successful. An error occured.');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION', '--Select--');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE', 'The transaction has been confirmed successfully for the TID %1$s and the due date updated as %2$s');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT', 'The transaction has been confirmed on %1$s');
define('MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE', 'The transaction has been cancelled on %1$s');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCELED_MESSAGE', 'The transaction has been cancelled for the %1$s on %2$s %3$s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_EX', '     (in minimum unit of currency. E.g. enter 100 which is equal to 1.00)');
define('MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG', 'Refund has been initiated for the TID: %1$s with the amount of %2$s.');
define('MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG', 'New TID:%s for the refunded amount');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY_BACKEND', 'Instalment summary');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ADMIN_TEXT', 'Instalment cancel');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_BACKEND', 'Amount');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DATE_BACKEND', 'Date');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_BACKEND', 'Status');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE_BACKEND', 'Novalnet Transaction ID');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_TEXT', 'Cancel');
define('MODULE_PAYMENT_NOVALNET_MENTION_GUARANTEE_PAYMENT_PENDING_TEXT', 'Your order is under verification and we will soon update you with the order status. Please note that this may take upto 24 hours.');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE', 'Payment Reference : %1$s');
define('NOVALNET_WEBHOOK_CREDIT_NOTE', 'Credit has been successfully received for the TID: %1$s with amount %2$s on %3$s. Please refer PAID order details in our Novalnet Admin Portal for the TID: %4$s');
define('NOVALNET_WEBHOOK_CHARGEBACK_NOTE', 'Chargeback executed successfully for the TID: %1$s amount: %2$s on %3$s %4$s . The subsequent TID: %5$s');
define('NOVALNET_WEBHOOK_NEW_INSTALMENT_NOTE', 'A new instalment has been received for the Transaction ID: %1$s with amount %2$s on %3$s. The new instalment transaction ID is: %4$s');
define('NOVALNET_WEBHOOK_INSTALMENT_CANCEL_NOTE', 'Instalment has been cancelled for the TID: %1$s on %2$s');
define('NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE_DUE_DATE', 'Transaction updated successfully for the TID: %1$s with amount %2$s and the due date updated as %3$s.');
define('NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE', 'Transaction updated successfully for the TID: %1$s with amount %2$s on %3$s');
define('NOVALNET_PAYMENT_REMINDER_NOTE', 'Payment Reminder %1$s has been sent to the customer.');
define('NOVALNET_COLLECTION_SUBMISSION_NOTE', 'The transaction has been submitted to the collection agency. Collection Reference: %1$s');

define('NOVALNET_PAYMENT_STATUS_PENDING_TO_ONHOLD_TEXT', 'The transaction status has been changed from pending to on-hold for the TID: %1$s on %2$s');

define('MODULE_PAYMENT_NOVALNET_PARTNER_PAYMENT_REFERENCE', 'Partner Payment Reference: %s');
define('MODULE_PAYMENT_NOVALNET_ERROR_MSG', 'Check hash failed');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE', '<b>Manage Transaction</b>');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CAPTURE_CONFIRM', 'Are you sure you want to capture the payment?');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_VOID_CONFIRM', 'Are you sure you want to cancel the payment?');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT', 'Please select status');
define('MODULE_PAYMENT_NOVALNET_BACK_TEXT', 'Back');
define('MODULE_PAYMENT_NOVALNET_REFUND_TITLE', 'Refund process');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM', 'Are you sure you want to refund the amount');
define('MODULE_PAYMENT_NOVALNET_REFUND_REFERENCE_TEXT', 'Refund reference');
define('MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE', 'Please enter the refund amound');
define('MODULE_PAYMENT_NOVALNET_REFUND_REASON_TITLE', 'Refund / Cancellation Reason');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_INSTALMENTS_INFO', 'Instalment information');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED_INSTALMENTS', 'Processed instalments:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE_INSTALMENTS', 'Due instalments:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_AMOUNT', 'Next instalment amount:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_DATE', 'Next instalment date:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PAY_DATE_BACKEND', 'Paid date');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_DATE_BACKEND', 'Next cycle date');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_REFUND_BACKEND', 'Instalment refund');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_PAID', 'Paid');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_PENDING', 'Pending');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_REFUNDED', 'Refunded');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_CANCELED', 'Canceled');

define('MODULE_PAYMENT_NOVALNET_BOOK_TITLE', 'Book transaction');
define('MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE', 'Transaction booking amount');
define('MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE', 'Your order has been booked with the amount of %s. Your new TID for the booked amount: %s');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZERO_AMOUNT_BOOK_CONFIRM', 'Are you sure you want to book the order amount?');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE', 'The amount is invalid');
define('MODULE_PAYMENT_NOVALNET_ZEROAMOUNT_BOOKING_MESSAGE', 'This order processed as a zero amount booking');
define('MODULE_PAYMENT_NOVALNET_ZEROAMOUNT_BOOKING_TEXT', '<br><br>This order will be processed as zero amount booking which store your payment data for further online purchases.');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES', 'Cancel All Instalment');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES_TEXT', 'Instalment has been cancelled for the TID: %1$s on %2$s & Refund has been initiated with the amount %3$s');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES', 'Cancel All Remaining Instalments');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES_TEXT', 'Instalment has been stopped for the TID: %1$s on %2$s');

define('MODULE_PAYMENT_NOVALNET_ALLCYCLES_ERROR_MESSAGE', 'Are you sure you want to cancel all cycles?');
define('MODULE_PAYMENT_NOVALNET_REMAINING_CYCLES_ERROR_MESSAGE', 'Are you sure you want to cancel remaining cycles?');

define('MODULE_PAYMENT_NOVALNET_WALLET_PAYMENT_SUCCESS_TEXT', 'Your order was successfully processed using Google Pay (Visa **** %s)');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_EN_ORDER_STATUS', 'Payment authorized (Novalnet)');
define('MODULE_PAYMENT_NOVALNET_CANCELED_EN_ORDER_STATUS', 'Canceled (Novalnet)');

define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_SUBJECT', 'Order Confirmation - Your Order number %1$s with %2$s has been confirmed!');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_MESSAGE', 'Order Confirmation from %s');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_DATE', 'Date Ordered: %s');
define('MODULE_PAYMENT_NOVALNET_ORDER_NUMBER', 'Order number: %s');
define('MODULE_PAYMENT_NOVALNET_DELIVERY_ADDRESS', 'Delivery address');
define('MODULE_PAYMENT_NOVALNET_BILLING_ADDRESS', 'Billing address');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION', 'Payment confirmation:');
define('MODULE_PAYMENT_NOVALNET_CUSTOMER_SALUTATION', 'Dear Mr/Ms  ');

define('MODULE_PAYMENT_NOVALNET_INCL_TAX_LABEL', 'Incl.Tax');
define('MODULE_PAYMENT_NOVALNET_EXCL_TAX_LABEL', 'Excl.Tax');
define('MODULE_PAYMENT_NOVALNET_DISCOUNT_AND_GIFT_VOUCHER_LABEL', 'Discount');
define('MODULE_PAYMENT_NOVALNET_SHIPPING_LABEL', 'Shipping');

define('MODULE_PAYMENT_NOVALNET_AMOUNT_UPDATE_NOTE', 'Transaction amount %1$s has been updated successfully on %2$s');
define('MODULE_PAYMENT_NOVALNET_DUEDATE_UPDATE_NOTE', 'Transaction due date %1$s has been updated successfully on %2$s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_DUEDATE_UPDATE_NOTE', 'Transaction amount %1$s and due date %2$s has been updated successfully on %3$s');
define('MODULE_PAYMENT_NOVALNET_BARZAHLEN_SUCCESS_BUTTON', 'Pay now with Barzahlen');
define('MODULE_PAYMENT_NOVALNET_WALLET_TOTAL_LABEL', 'Estimated total (No offers applied)');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_ACTION_REQUIRED', 'Action Required - Order Not found for the Transaction ID: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_IN', ' in ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_SALUTATION', 'Dear ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_ATTENTION', 'We would like to bring to your attention the following transaction details:');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_COMMUNICATION_PROBLEM', 'The payment for the above transaction details was successfully processed in Novalnet. However, it appears that our system encountered difficulties in communicating with your shop system.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_DISCREPANCIES', 'We kindly request one of the following actions to prevent any discrepancies for the transaction:');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MANUAL_ORDER_CREATION', 'Manual Order Creation: Please create an order manually in your shop back-office and generate an invoice corresponding to the provided transaction details.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_REFUND_INITIATION', 'Refund Initiation: Alternatively, you may initiate a refund for the transaction through the Novalnet Admin Portal(https://admin.novalnet.de/)');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_PROMPT_REVIEW', 'Your prompt review and action on these transaction details would be greatly appreciated.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_EMAIL', 'Customer E-mail: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_PAYMENT_TYPE', 'Payment Type: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_TID_STATUS', 'Transaction ID Status: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_TID', 'Transaction ID: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_PROJECT_ID', 'Project ID: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_REGARDS', 'Regards,');
