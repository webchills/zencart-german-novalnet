<?php

#########################################################
#                                                       #
#  CC / CREDIT CARD payment method class                #
#  This module is used for real time processing of      #
#  Credit card data of customers.                       #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_cc.php                             #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_TITLE', '<nobr>Kreditkarte <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/creditcard_small.jpg" alt="Visa & Mastercard"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_PUBLIC_TITLE', '<nobr>Kreditkarte <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/creditcard_small.jpg" alt="Visa & Mastercard"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_JS_NN_ID2_MISSING', '* Produkt-ID2 und / oder Tarif-ID2 fehlen!');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_ERROR', 'Kartendaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_ORDERNO', 'Best.-Nr.: ');
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_ORDERDATE', 'Best.-Datum: ');
	define('MODULE_PAYMENT_NOVALNET_CC_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_CC_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_CC_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_CC_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_CC_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_CC_TID_MESSAGE',"Novalnet Transaktions ID: "); 
	define('MODULE_PAYMENT_NOVALNET_CC_TEXT_HASH_ERROR', 'checkHash fehlgeschlagen');
	define('MODULE_PAYMENT_NOVALNET_CC_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");   
	define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_CC', 'Die Belastung Ihrer Kreditkarte erfolgt mit dem Abschluss der Bestellung.');

?>
