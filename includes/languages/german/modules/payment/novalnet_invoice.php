<?php
#########################################################
#                                                       #
#  Invoice payment method class                         #
#  This module is used for real time processing of      #
#  Invoice data of customers.                       	#
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_invoice.php                        #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE', 'Kauf auf Rechnung');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_INVOICE_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE', 'Kauf auf Rechnung');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CODE', 'Bankleitzahl :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_IBAN', 'IBAN :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BIC', 'SWIFT / BIC :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BANK', 'Bank :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CITY', 'Stadt :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT', 'Betrag :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE', 'Verwendungszweck : TID');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE_INFO', 'Bitte beachten Sie, dass die Ueberweisung nur bearbeitet werden kann, wenn der oben angegebene Verwendungszweck verwendet wird.');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TRANSFER_INFO', 'Bitte überweisen Sie den Betrag mit der folgenden Information an unseren Zahlungsdienstleister Novalnet AG');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_INFO', 'Die Bankverbindung wird Ihnen nach Abschluss Ihrer Bestellung per E-Mail zugeschickt.');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO', 'Zahlungsfrist:');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO_DAYS', 'Tage');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_INFO', 'Bitte überweisen Sie den Betrag mit der folgenden Information an unseren Zahlungsdienstleister Novalnet AG');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_END_INFO', 'Fälligkeitsdatum :');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERNO', 'Best.-Nr. ');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERDATE', 'Best.-Datum ');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ERROR', 'Kontodaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_TEL_REQ', 'Telefonnummer:*');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_REQ', 'Mobiltelefonnummer:*');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN', 'Geben Sie Ihre PIN Nummer:*');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'PIN vergessen? [Neue PIN beantragen]');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Geben Sie bitte die Telefon- / Handynummer ein!');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'Die eingegebene PIN ist falsch oder leer!');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'Sie werden in k&uuml;rze eine PIN per Telefon/SMS erhalten. Bitte geben Sie die PIN in das entsprechende Textfeld ein.');  
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC',"Sie erhalten in K&uuml;rze eine PIN per Telefon/SMS. Geben Sie bitte die PIN in das entsprechende Textfeld ein.");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR',"Ihre PIN Sitzung ist abgelaufen. Bitte versuchen Sie es erneut mit einem neuen Anruf.");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TID_MESSAGE',"Novalnet Transaktions ID : ");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE',"Sie haben die Bestellmenge nach dem Erhalt der PIN-Nummer ge&auml;ndert, versuchen Sie es bitte erneut mit einem neuen Anruf.");  
	define('MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE_EMAIL',"Sie haben die Bestellmenge nach dem Erhalt der Email ge&auml;ndert, versuchen Sie es bitte erneut mit einem neuen Anruf.");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_PHONE_INPUT_REQUEST_DESC',"* Bitte geben Sie Ihre Telefonnummer / E-Mail."); 
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_REQ', 'E-Mail Adresse:*');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_EMAIL_NOTVALID', 'Geben Sie bitte die Emailadresse ein!');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_EMAIL_INPUT_REQUEST_DESC',"Wir haben Ihnen eine Email geschickt, beantworten Sie diese bitte.");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
	define('MODULE_PAYMENT_NOVALNET_INVOICE_MAX_TIME_ERROR', '*Maximale Anzahl von PIN-Eingaben überschritten!');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_NAME', 'NOVALNET AG');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_PAYMNETNAME', 'Novalnet Kauf auf Rechnung');
	define('MODULE_PAYMENT_NOVALNET_INVOICE_TID', 'TID :');
	define('MODULE_PAYMENT_NOVALNET_TEXT_DETAILS_INVOICE_INTERNATIONAL_INFO', 'Nur bei Auslandsüberweisungen:');
?>
