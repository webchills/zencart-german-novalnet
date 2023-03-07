<?php
/**
 * Novalnet payment module
 * This script is used for German language
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_payments.php
 *
 */

define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE', 'Novalnet API-Konfiguration');
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_DESCRIPTION', '<span style="font-weight: bold; color:#878787;">Bevor Sie beginnen, lesen Sie bitte die Installationsanleitung und melden Sie sich mit Ihrem Händlerkonto im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a> an. Um ein Händlerkonto zu erhalten, senden Sie bitte eine E-Mail an <a style="font-weight: bold; color:#0080c9;" href="mailto:sales@novalnet.de">sales@novalnet.de</a> oder rufen Sie uns unter +49 89 923068320 an</span><br/><br/><span style="font-weight: bold; color:#878787;">Die Konfigurationen der Zahlungsplugins sind jetzt im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a> verfügbar. Navigieren Sie zu Konto -> Konfiguration des Shops Ihrer Projekte, um sie zu konfigurieren.</span><br/><br/><span style="font-weight: bold; color:#878787;">Novalnet ermöglicht es Ihnen, das Verhalten der Zahlungsmethode zu überprüfen, bevor Sie in den Produktionsmodus gehen, indem Sie Testzahlungsdaten verwenden. Zugang zu den Novalnet-Testzahlungsdaten finden Sie <a href="https://developer.novalnet.de/testing" target="_blank"> hier </a> </span>');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE', 'Aktivierungsschlüssel des Produkts');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESCRIPTION', 'Ihren Produktaktivierungsschlüssel finden Sie im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a> Projekte > Wählen Sie Ihr Projekt > API-Anmeldeinformationen > API-Signatur (Produktaktivierungsschlüssel)');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE', 'Zahlungs-Zugriffsschlüssel');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESCRIPTION', 'Ihren Paymentzugriffsschlüssel finden Sie im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a> Projekte > Wählen Sie Ihr Projekt > API-Anmeldeinformationen > Paymentzugriffsschlüssel');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE', 'Auswahl der Tarif-ID');
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESCRIPTION', 'Wählen Sie eine Tarif-ID, die dem bevorzugten Tarifplan entspricht, den Sie im Novalnet Admin-Portal für dieses Projekt erstellt haben');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE', '<h2>Benachrichtigungs- / Webhook-URL festlegen</h2><br> Manuelles Testen der Benachrichtigungs / Webhook-URL erlauben');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC', 'Aktivieren Sie diese Option, um die Novalnet-Benachrichtigungs-/Webhook-URL manuell zu testen. Deaktivieren Sie die Option, bevor Sie Ihren Shop liveschalten, um unautorisierte Zugriffe von Dritten zu blockieren');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_TITLE', 'Benachrichtigung / Webhook-URL im Novalnet-Verwaltungsportal');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_URL_DESC', 'Sie müssen die folgende Webhook-URL im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a> hinzufügen. Dadurch können Sie Benachrichtigungen über den Transaktionsstatus erhalten');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE_DUE_DATE', 'Bitte überweisen Sie den Betrag von %1$s spätestens bis zum %2$s auf das folgende Konto');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE', '<script src=../includes/modules/payment/novalnet/novalnet_auto_config.js type=text/javascript></script> <input type="button" id="webhook_url_button" style="font-weight: bold; color:#0080c9;" value="Konfigurieren"> <br> E-Mails senden an');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC', 'E-Mail-Benachrichtigungen werden an diese E-Mail-Adresse gesendet');
define('MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD_TITLE', ' Zahlungsart anzeigen ');
define('MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD_DESCRIPTION', ' Möchten Sie Zahlungen über Novalnet anzeigen? ');

define('MODULE_PAYMENT_NOVALNET_CREDENTIALS_ERROR', 'Bitte geben Sie den gültigen Produktaktivierungsschlüssel und Paymentzugriffsschlüssel ein');
define('MODULE_PAYMENT_NOVALNET_WEBHOOKURL_ERROR', 'Bitte geben Sie eine gültige Webhook-URL ein');
define('MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_SUCCESS_TEXT', 'Callbackskript-/ Webhook-URL wurde erfolgreich im Novalnet Admin Portal konfiguriert');
define('MODULE_PAYMENT_NOVALNET_WEBHOOKURL_CONFIGURE_ALERT_TEXT', 'Sind Sie sicher, dass Sie die Webhook-URL im Novalnet Admin Portal konfigurieren möchten?');


define('MODULE_PAYMENT_NOVALNET_AMOUNT_TRANSFER_NOTE', 'Bitte überweisen Sie den Betrag %s auf das folgende Konto.');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE_DUE_DATE', 'Bitte überweisen Sie den anzahl der raten von %1$s spätestens bis zum %2$s auf das folgende Konto');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TRANSFER_NOTE', 'Bitte überweisen Sie den anzahl der raten %1$s auf das folgende Konto.');
define('MODULE_PAYMENT_NOVALNET_MULTIBANCO_NOTE', 'Bitte verwenden Sie die folgende Zahlungsreferenz, um den Betrag von %s an einem Multibanco-Geldautomaten oder über Ihr Onlinebanking zu bezahlen.');
define('MODULE_PAYMENT_NOVALNET_BANK_NAME', 'Bank: ');
define('MODULE_PAYMENT_NOVALNET_IBAN','IBAN: ');
define('MODULE_PAYMENT_NOVALNET_BIC', ' BIC: ');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER','Kontoinhaber:  ');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', ' Betrag: ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MODE', 'Testbestellung');
define('MODULE_PAYMENT_NOVALNET_BANK_PLACE', 'Ort: ');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet-Transaktions-ID: ');
define('MODULE_PAYMENT_NOVALNET_TRANS_SLIP_EXPIRY_DATE', 'Verfallsdatum des Zahlscheins: ');
define('MODULE_PAYMENT_NOVALNET_NEAREST_STORE_DETAILS', 'Barzahlen-Partnerfilialen in Ihrer Nähe: ');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE_TEXT', 'Bitte verwenden Sie einen der unten angegebenen Verwendungszwecke für die Überweisung. Nur so kann Ihr Geldeingang Ihrer Bestellung zugeordnet werden');
define('MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT','Bestätigen');
define('MODULE_PAYMENT_NOVALNET_REFUND_TEXT','Rückerstattung');
define('MODULE_PAYMENT_NOVALNET_CANCEL_TEXT','Stornieren');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ERROR', 'Die Zahlung war nicht erfolgreich. Ein Fehler ist aufgetreten.');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION', '--Auswählen--');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE','Die Buchung wurde am %1$s Uhr bestätigt');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_SUCCESSFUL_MESSAGE_TEXT', 'Die Buchung wurde am %1$s');
define('MODULE_PAYMENT_NOVALNET_TRANS_DEACTIVATED_MESSAGE', 'Die Transaktion wurde am %1$s Uhr storniert');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCELED_MESSAGE', 'Die Transaktion wurde storniert für die %1$s auf %2$s %3$s');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_EX', '     (in der kleinsten Währungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)');
define('MODULE_PAYMENT_NOVALNET_REFUND_PARENT_TID_MSG', 'Die Rückerstattung für die TID: %1$s mit dem Betrag %2$s wurde veranlasst.');
define('MODULE_PAYMENT_NOVALNET_REFUND_CHILD_TID_MSG', ' Die neue TID: %s für den erstatteten Betrag');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY_BACKEND', 'Zusammenfassung der Ratenzahlung');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ADMIN_TEXT','Raten stornier');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_BACKEND', 'Betrag');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DATE_BACKEND', 'Datum');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_BACKEND', 'Status');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE_BACKEND', 'Novalnet-Transaktions-ID');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_TEXT', 'Stornieren');
define('MODULE_PAYMENT_NOVALNET_MENTION_GUARANTEE_PAYMENT_PENDING_TEXT', 'Ihre Bestellung wird derzeit überprüft. Wir werden Sie in Kürze über den Bestellstatus informieren. Bitte beachten Sie, dass dies bis zu 24 Stunden dauern kann.');
define('MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR', 'Bitte füllen Sie die erforderlichen Felder aus');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFERENCE', 'Verwendungszweck : %1$s');
define('NOVALNET_WEBHOOK_CREDIT_NOTE','Die Gutschrift für die TID ist erfolgreich eingegangen: %1$s mit Betrag %2$s am %3$s. Bitte entnehmen Sie die TID den Einzelheiten der Bestellung bei BEZAHLT in unserem Novalnet Adminportal: %4$s');
define('NOVALNET_WEBHOOK_CHARGEBACK_NOTE','Chargeback erfolgreich importiert für die TID: %1$s Betrag: %2$s am %3$s um %4$s Uhr. TID der Folgebuchung: %5$s');
define('NOVALNET_WEBHOOK_NEW_INSTALMENT_NOTE','Für die Transaktions-ID ist eine neue Rate eingegangen: %1$s mit Betrag %2$s am %3$s. Die Transaktions-ID der neuen Rate lautet: %4$s');
define('NOVALNET_WEBHOOK_INSTALMENT_CANCEL_NOTE','Die Ratenzahlung für die TID wurde gekündigt: %1$s am %2$s');

define('NOVALNET_PAYMENT_STATUS_PENDING_TO_ONHOLD_TEXT','Der Status der Transaktion mit der TID: %1$s wurde am %2$s um von ausstehend auf ausgesetzt geändert ');

define('NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE_DUE_DATE','Transaktion mit TID %1$s und Betrag %2$s wurde am %3$s um erfolgreich aktualisiert ');
define('NOVALNET_WEBHOOK_TRANSACTION_UPDATE_NOTE','Transaktion mit TID %1$s und Betrag %2$s wurde am um erfolgreich.');
define('NOVALNET_PAYMENT_REMINDER_NOTE','Zahlungserinnerung %1$s wurde an den Kunden gesendet.');
define('NOVALNET_COLLECTION_SUBMISSION_NOTE','Die Transaktion wurde an das Inkassobüro übergeben. Inkasso-Referenz: %1$s');

define('MODULE_PAYMENT_NOVALNET_PARTNER_PAYMENT_REFERENCE', 'Partner-Zahlungsreferenz: %s');
define('MODULE_PAYMENT_NOVALNET_ERROR_MSG', 'Prüfung des Hashes fehlgeschlagen');
define('MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE','<b>Transaktion verwalten</b>');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_CAPTURE_CONFIRM','Sind Sie sicher, dass Sie die Zahlung erfassen wollen?');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_VOID_CONFIRM','Sind Sie sicher, dass Sie die Zahlung stornieren möchten?');
define('MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT', 'Bitte Status auswählen');
define('MODULE_PAYMENT_NOVALNET_BACK_TEXT', 'Zurück');
define('MODULE_PAYMENT_NOVALNET_REFUND_TITLE', 'Erstattungsverfahren');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM', 'Sind Sie sicher, dass Sie den Betrag erstatten wollen?');
define('MODULE_PAYMENT_NOVALNET_REFUND_REFERENCE_TEXT', 'Referenz für die Erstattung');
define('MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE', 'Bitte geben Sie den Erstattungsbetrag ein');
define('MODULE_PAYMENT_NOVALNET_REFUND_REASON_TITLE', 'Grund für die Rückerstattung (optional)');
define('MODULE_PAYMENT_NOVALNET_INSTALLMENT_TEXT', 'Wählen Sie Ihren Ratenzahlungsplan <b>(Netto-Kreditbetrag: %s )</b>');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_INSTALMENTS_INFO','Informationen zur Ratenzahlung');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED_INSTALMENTS','Bearbeitete Raten:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE_INSTALMENTS','Fällige Raten:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_AMOUNT','Nächster Ratenbetrag:  ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_INSTALMENT_DATE','Datum der nächsten Ratenzahlung:  ');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PAY_DATE_BACKEND', 'Bezahltes Datum');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_DATE_BACKEND', 'Nächstes Ratenzahlungsdatum');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PAID_DATE_BACKEND','Bezahlt am');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_PAID','Bezahlt');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_PENDING','Ausstehend');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_REFUNDED','Rückerstattet');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_CANCELED','Abgesagt');

define('MODULE_PAYMENT_NOVALNET_BOOK_TITLE','Transaktion durchf&uuml;hren');
define('MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE','Buchungsbetrag der Transaktion');
define('MODULE_PAYMENT_NOVALNET_TRANS_BOOKED_MESSAGE','Ihre Bestellung wurde mit einem Betrag von %s gebucht. Ihre neue TID für den gebuchten Betrag: %s');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZERO_AMOUNT_BOOK_CONFIRM','Sind Sie sich sicher, dass Sie den Bestellbetrag buchen wollen?');
define('MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE','Ungültiger Betrag');
define('MODULE_PAYMENT_NOVALNET_ZEROAMOUNT_BOOKING_MESSAGE','Diese Transaktion wird mit Nullbuchung bearbeitet');
define('MODULE_PAYMENT_NOVALNET_ZEROAMOUNT_BOOKING_TEXT','<br><br>Diese Bestellung wird als Nullbuchung verarbeitet. Ihre Zahlungsdaten werden für zukünftige Online-Einkäufe gespeichert.');

define('MODULE_PAYMENT_NOVALNET_WALLET_PAYMENT_SUCCESS_TEXT','Ihre Bestellung wurde erfolgreich mit Google Pay durchgeführt (Visa **** %1$s)');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES','Alle Raten stornieren');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES_TEXT','Die Ratenzahlung für die TID wurde gekündigt: %1$s am %2$s und die Rückerstattung wurde mit dem Betrag %3$s');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES','Alle verbleibenden Raten stornieren');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES_TEXT','Die Ratenzahlung für die TID wurde gestoppt: %1$s um %2$s');

define('MODULE_PAYMENT_NOVALNET_ALLCYCLES_ERROR_MESSAGE','Sind Sie sicher, dass Sie alle Zyklen abbrechen wollen?');
define('MODULE_PAYMENT_NOVALNET_REMAINING_CYCLES_ERROR_MESSAGE','Sind Sie sicher, dass Sie die verbleibenden Zyklen abbrechen wollen?');

define('MODULE_PAYMENT_NOVALNET_ONHOLD_DE_ORDER_STATUS', 'Zahlung autorisiert (Novalnet)');
define('MODULE_PAYMENT_NOVALNET_CANCELED_DE_ORDER_STATUS', 'Storniert (Novalnet)');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_SUBJECT',' Bestellbestätigung - Ihre Bestellung nummer %1$s bei %2$s wurde bestätigt!');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_MESSAGE',' Bestellbestätigung von %s');
define('MODULE_PAYMENT_NOVALNET_ORDER_MAIL_DATE','Datum der Bestellung: %s');
define('MODULE_PAYMENT_NOVALNET_ORDER_NUMBER','Bestellnummer: %s');
define('MODULE_PAYMENT_NOVALNET_DELIVERY_ADDRESS','Lieferadresse');
define('MODULE_PAYMENT_NOVALNET_BILLING_ADDRESS','Rechnungsadresse');
define('MODULE_PAYMENT_NOVALNET_ORDER_CONFIRMATION','Bestätigung der Zahlung:');

define('MODULE_PAYMENT_NOVALNET_CUSTOMER_SALUTATION','Sehr geehrte Herr/Frau');
