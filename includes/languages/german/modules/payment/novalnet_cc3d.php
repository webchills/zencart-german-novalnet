<?php

#########################################################
#                                                       #
#  CC3D / CREDIT CARD 3d secure payment method class    #
#  This module is used for real time processing of      #
#  Credit card data of customers.                       #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_cc3d.php                           #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE', '<nobr>Kreditkarte 3D Secure <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/creditcard_small.jpg" alt="Visa & Mastercard"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE', '<nobr>Kreditkarte 3D Secure <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/creditcard_small.jpg" alt="Visa & Mastercard"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER', 'Kreditkarteninhaber:');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER_LENGTH', '3');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO', 'Kartennummer:');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO_LENGTH', '12');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH', 'Monat :');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH_LENGTH', '2');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR', 'Jahr :');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR_LENGTH', '2');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT', 'Bitte waehlen');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC', 'CVC (Pruefziffer): ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTHS_YEARS', 'G&uuml;ltigkeit (Monat/Jahr) :');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC_LENGTH', '3');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC2', '<BR>* Bei Visa-, Master- und Eurocard besteht der CVC-Code<BR>aus den drei letzten Ziffern im Unterschriftenfeld auf der<BR>Rueckseite der Kreditkarte.');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO', '<BR>Die Belastung Ihrer Kreditkarte erfolgt mit dem Abschluss der Bestellung.');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_ID2_MISSING', '* Produkt-ID2 und / oder Tarif-ID2 fehlen!');
	define('MODULE_PAYMENT_NOVALNET_CC3D_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_CC3D_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_CC3D_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CARD_OWNER', '* Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO', '* Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_MONTH', '*Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_YEAR', '* Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC', '* Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2', '* Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR_EXP_MONTH', '*Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR_EXP_YEAR', '*Geben Sie bitte g&uuml;ltige Kreditkartendaten ein! ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR', 'Kartendaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERNO', 'Best.-Nr.: ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERDATE', 'Best.-Datum: ');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_CC3D_TID_MESSAGE',"Novalnet Transaktions ID: "); 
	define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO_ERR', '*Geben Sie bitte gültige Kreditkartendaten ein!');
	define('MODULE_PAYMENT_NOVALNET_CC3D_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
?>
