<?php
/**
 * Novalnet payment module
 *
 * This script is used for Spanish language
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
define('MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE_DUE_DATE', 'Por favor, transfiera la cantidad de %1$s a la siguiente cuenta en o antes de %2$s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE', 'Por favor, transfiera la cantidad de %s a la siguiente cuenta.');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE_DUE_DATE', 'Por favor, transfiera el importe del ciclo de pago de %1$s a la siguiente cuenta antes de %2$s');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE', 'Por favor, transfiera el importe del ciclo de cuotas de %1$s a la siguiente cuenta.');
define('MODULE_PAYMENT_NOVALNET_MULTIBANCO_NOTE', 'Utilice los siguientes datos de referencia de pago para abonar el importe de %s en un cajero automático Multibanco o a través de su banca por Internet.');
define('MODULE_PAYMENT_NOVALNET_BANK_NAME', ' Banco: ');
define('MODULE_PAYMENT_NOVALNET_IBAN', 'IBAN: ');
define('MODULE_PAYMENT_NOVALNET_BIC', ' BIC: ');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER', 'Titular de la cuenta: ');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', ' Importe: ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MODE', 'Test order');
define('MODULE_PAYMENT_NOVALNET_BANK_PLACE', 'Ciudad: ');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet ID de transacción: ');
define('MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE', 'Fecha de caducidad: ');
define('MODULE_PAYMENT_NOVALNET_NEAREST_STORE_DETAILS', 'Tienda(s) cercana(s): ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE_TEXT', 'Por favor, utilice las siguientes referencias de pago al transferir el importe. Esto es necesario para que coincida con su pedido correspondiente');
define('MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT', 'Confirm');
define('MODULE_PAYMENT_NOVALNET_REFUND_TEXT', 'Refund');
define('MODULE_PAYMENT_NOVALNET_CANCEL_TEXT', 'Cancel');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR', 'El pago no se ha realizado correctamente. Se ha producido un error.');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION', '--Select--');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE', 'La transacción ha sido confirmada con éxito para el TID %1$s y la fecha de vencimiento actualizada como %2$s');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT', 'La operación se ha confirmado en %1$s');
define('MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE', 'La transacción se ha cancelado el %1$s');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCELED_MESSAGE', 'La transacción ha sido cancelada para el %1$s en %2$s %3$s');
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
define('MODULE_PAYMENT_NOVALNET_MENTION_GUARANTEE_PAYMENT_PENDING_TEXT', 'Su pedido está siendo verificado y pronto le informaremos del estado del mismo. Tenga en cuenta que esto puede tardar hasta 24 horas.');

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
define('MODULE_PAYMENT_NOVALNET_REFUND_REASON_TITLE', 'Reason for refund (optional)');

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
define('MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE', 'Su pedido ha sido reservado con la cantidad de %s. Su nuevo TID para el importe reservado: %s');
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

define('MODULE_PAYMENT_NOVALNET_WALLET_PAYMENT_SUCCESS_TEXT', 'Su pedido se ha procesado correctamente mediante Google Pay (Visa **** %s)');
define('MODULE_PAYMENT_NOVALNET_ONHOLD_EN_ORDER_STATUS', 'Pago autorizado (Novalnet)');
define('MODULE_PAYMENT_NOVALNET_CANCELED_EN_ORDER_STATUS', 'Cancelado (Novalnet)');

define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_SUBJECT', 'Confirmación de pedido - ¡Su pedido número %1$s con %2$s ha sido confirmado!');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_MESSAGE', 'Confirmación de pedido de %s');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_DATE', 'Fecha del pedido: %s');
define('MODULE_PAYMENT_NOVALNET_ORDER_NUMBER', 'Número de pedido: %s');
define('MODULE_PAYMENT_NOVALNET_DELIVERY_ADDRESS', 'Dirección de entrega');
define('MODULE_PAYMENT_NOVALNET_BILLING_ADDRESS', 'Dirección de facturación');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION', 'Confirmación de pago:');
define('MODULE_PAYMENT_NOVALNET_MAIL_TEMPLATE_DEAR_TEXT', 'Estimado Sr./Sra. %s,');
define('MODULE_PAYMENT_NOVALNET_MAIL_TEMPLATE_BEST_REAGRDS', 'Atentamente,');
define('MODULE_PAYMENT_NOVALNET_MAIL_TEMPLATE_NOVALNET', 'El equipo de Novalnet');
define('MODULE_PAYMENT_NOVALNET_CUSTOMER_SALUTATION', 'Estimado Sr./Sra. ');

define('MODULE_PAYMENT_NOVALNET_INCL_TAX_LABEL', 'IVA incluido');
define('MODULE_PAYMENT_NOVALNET_EXCL_TAX_LABEL', 'IVA excluido');
define('MODULE_PAYMENT_NOVALNET_DISCOUNT_AND_GIFT_VOUCHER_LABEL', 'Descuento');
define('MODULE_PAYMENT_NOVALNET_SHIPPING_LABEL', 'Envío');

define('MODULE_PAYMENT_NOVALNET_AMOUNT_UPDATE_NOTE', 'Transaction amount %1$s has been updated successfully on %2$s');
define('MODULE_PAYMENT_NOVALNET_DUEDATE_UPDATE_NOTE', 'Transaction due date %1$s has been updated successfully on %2$s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_DUEDATE_UPDATE_NOTE', 'Transaction amount %1$s and due date %2$s has been updated successfully on %3$s');
define('MODULE_PAYMENT_NOVALNET_BARZAHLEN_SUCCESS_BUTTON', 'Pague ahora con Barzahlen');
define('MODULE_PAYMENT_NOVALNET_WALLET_TOTAL_LABEL', 'Total estimado (Sin aplicar ofertas)');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_ACTION_REQUIRED', 'Aktion erforderlich - Bestellung nicht gefunden für die Transaktions-ID: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_IN', ' im ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_SALUTATION', 'Sehr geehrter ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_ATTENTION', 'Wir möchten Sie auf die folgenden Transaktionsdetails aufmerksam machen:');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_COMMUNICATION_PROBLEM', 'Die Zahlung für die oben genannten Transaktionsdetails wurde in Novalnet erfolgreich verarbeitet. Es scheint jedoch, dass unser System Schwierigkeiten bei der Kommunikation mit Ihrem Shopsystem hatte.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_DISCREPANCIES', 'Wir bitten Sie, eine der folgenden Maßnahmen zu ergreifen, um Unstimmigkeiten bei der Transaktion zu vermeiden:');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MANUAL_ORDER_CREATION', 'Manuelle Auftragserstellung: Bitte legen Sie eine Bestellung manuell im Back-Office Ihres Shops an und erstellen Sie eine Rechnung entsprechend den angegebenen Transaktionsdetails.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_REFUND_INITIATION', 'Einleitung der Rückerstattung: Alternativ können Sie über das Novalnet Admin Portal(https://admin.novalnet.de/) eine Rückerstattung der Transaktion beantragen.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_PROMPT_REVIEW', 'Wir würden es sehr begrüßen, wenn Sie diese Vorgänge umgehend überprüfen und entsprechende Maßnahmen ergreifen würden.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_EMAIL', 'Kunden-E-Mail: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_PAYMENT_TYPE', 'Zahlungsart: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_TID_STATUS', 'Transaktions-ID Status: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_TID', 'Transaktions-ID: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_PROJECT_ID', 'Projekt-ID: ');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_REGARDS', 'Herzliche Grüße,');


define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CAPTURE_SUBJECT', 'Transaktion via Novalnet bestätigt');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CANCEL_SUBJECT', 'Transaktion Storniert via Novalnet');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_REFUND_SUBJECT', 'Transaktion Rückerstattung via Novalnet');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CREDIT_SUBJECT', 'Guthaben erhalten via Novalnet');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_CHARGEBACK_SUBJECT', 'Rückbuchung via Novalnet erhalten');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_INSTALMENT_SUBJECT', 'Neue Rate via Novalnet erhalten');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_INSTALMENT_CANCEL_SUBJECT', 'Ratenzahlung via Novalnet gekündigt');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_TRANS_UPDATE_SUBJECT', 'Transaktion aktualisiert via Novalnet');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_REMINDER_SUBJECT', 'Zahlungserinnerung via Novalnet');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_COLLECTION_SUBMISSION_SUBJECT', 'Inkassoübergabe via Novalnet');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_SUPPORT_TEXT', 'Wenn Sie weitere Unterstützung benötigen, wenden Sie sich bitte an unser Support-Team unter support@novalnet.de. Oder besuchen Sie Ihr Novalnet-Administrationsportal für weitere Informationen.');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_TRANS_DETAILS_TEXT', 'Transaktionsdetails:');
define('MODULE_PAYMENT_NOVALNET_WEBHOOK_MAIL_COLLECTION_STATUS', 'Inkasso Status: ');
