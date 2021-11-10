<?php

#########################################################
#                                                       #
#  ELVAT / DIRECT DEBIT payment method class            #
#  This module is used for real time processing of      #
#  Austrian Bankdata of customers.                      #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_elv_at.php                         #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE', '<nobr>Lastschrift &Ouml;sterreich <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/ELV_Logo.png" alt="&Ouml;sterreichische Lastschriftverfahren"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_INFO', '');  
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_PUBLIC_TITLE', '<nobr>Lastschrift &Ouml;sterreich <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/ELV_Logo.png" alt="&Ouml;sterreichische Lastschriftverfahren"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber:');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer:');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '5');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE', 'Bankleitzahl:');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE_LENGTH', '3');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_ID2_MISSING', '* Produkt-ID2 und / oder Tarif-ID2 fehlen!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_OWNER', '* Geben Sie bitte g&uuml;ltige Kontodaten ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Geben Sie bitte g&uuml;ltige Kontodaten ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE', '* Geben Sie bitte g&uuml;ltige Kontodaten ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ERROR', 'Kontodaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERNO', 'Best.-Nr. ');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERDATE', 'Best.-Datum ');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_ORDER_MESSAGE',"Testbestellung");
	//Start : Pin by call back 
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_TEL_REQ', 'Telefonnummer:*');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_REQ', 'Mobiltelefonnummer:*');  
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_EMAIL_REQ', 'E-Mail Adresse:*');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_PIN', 'Geben Sie Ihre PIN Nummer:*');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_NEW_PIN', 'PIN vergessen? [Neue PIN beantragen]');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Geben Sie bitte die Telefon- / Handynummer ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'Die eingegebene PIN ist falsch oder leer!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_EMAIL_NOTVALID', 'Geben Sie bitte die Emailadresse ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL_INPUT_REQUEST_DESC',"Wir haben Ihnen eine Email geschickt, beantworten Sie diese bitte.");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'Sie werden in k&uuml;rze eine PIN per Telefon/SMS erhalten. Bitte geben Sie die PIN in das entsprechende Textfeld ein.');

	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_INPUT_REQUEST_DESC',"Sie erhalten in K&uuml;rze eine PIN per Telefon/SMS. Geben Sie bitte die PIN in das entsprechende Textfeld ein.");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_PIN_BY_CALLBACK_SESSION_ERROR',"Ihre PIN Sitzung ist abgelaufen. Bitte versuchen Sie es erneut mit einem neuen Anruf.");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_AMOUNT_VARIATION_MESSAGE',"Sie haben die Bestellmenge nach dem Erhalt der PIN-Nummer ge&auml;ndert, versuchen Sie es bitte erneut mit einem neuen Anruf.");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_AMOUNT_VARIATION_MESSAGE_EMAIL',"Sie haben die Bestellmenge nach dem Erhalt der Email ge&auml;ndert, versuchen Sie es bitte erneut mit einem neuen Anruf.");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_EMAIL_PHONE_INPUT_REQUEST_DESC',"* Bitte geben Sie Ihre Telefonnummer / E-Mail.");
	//End : Pin by call back
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TID_MESSAGE'," Novalnet Transaktions ID : ");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ERROR_ACCOUNT_NUMBER', '* Geben Sie bitte g&uuml;ltige Kontodaten ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE_ERROR', '* Geben Sie bitte g&uuml;ltige Kontodaten ein!');
	define('MODULE_PAYMENT_NOVALNET_ELV_AT_MAX_TIME_ERROR', '*Maximale Anzahl von PIN-Eingaben überschritten!');
	define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_AT', 'Die Belastung Ihres Kontos erfolgt mit dem Versand der Ware.');
?>
