<?php
#########################################################
#                                                       #
#  PREPAYMENT payment method class                      #
#  This module is used for real time processing of      #
#  PREPAYMENT payment of customers.                     #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_prepayment.php                     #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TITLE', 'Vorauskasse');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_PUBLIC_TITLE', 'Vorauskasse');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CODE', 'Bankleitzahl :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_IBAN', 'IBAN :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BIC', 'SWIFT / BIC :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BANK', 'Bank :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CITY', 'Stadt :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_AMOUNT', 'Betrag :');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE', 'Verwendungszweck : TID');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE_INFO', 'Bitte beachten Sie, dass die Ueberweisung nur bearbeitet werden kann, wenn der oben angegebene Verwendungszweck verwendet wird.');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TRANSFER_INFO', 'Bitte überweisen Sie den Betrag mit der folgenden Information an unseren Zahlungsdienstleister Novalnet AG');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_INFO', 'Die Bankverbindung wird Ihnen nach Abschluss Ihrer Bestellung per E-Mail zugeschickt.');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERNO', 'Best.-Nr.: ');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERDATE', 'Best.-Datum: ');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TID_MESSAGE',"Novalnet Transaktions ID: ");
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ERROR', 'Kontodaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_NAME', 'NOVALNET AG');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PAYMNETNAME', 'Novalnet Vorauskasse');
	define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TID', 'TID :');
	define('MODULE_PAYMENT_NOVALNET_TEXT_DETAILS_PREPAYMENT_INTERNATIONAL_INFO', 'Nur bei Auslandsüberweisungen:');
?>