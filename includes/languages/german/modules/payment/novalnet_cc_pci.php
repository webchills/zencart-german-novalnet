<?php

#########################################################
#                                                       #
#  CCPCI / CREDIT CARD PCI payment method class     	#
#  This module is used for real time processing of      #
#  Credit card data of customers.     					#
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_cc_pci.php                         #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_TITLE', '<nobr>Kreditkarte PCI <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/creditcard_small.jpg" alt="Visa & Mastercard"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_PUBLIC_TITLE', '<nobr>Kreditkarte PCI <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/creditcard_small.jpg" alt="Visa & Mastercard"/></a></nobr>'); 
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_NOT_CONFIGURED', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_JS_NN_ID2_MISSING', '* Produkt-ID2 und / oder Tarif-ID2 fehlen!');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_ERROR', 'Kartendaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_ORDERNO', 'Best.-Nr.: ');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_ORDERDATE', 'Best.-Datum: ');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TID_MESSAGE',"Novalnet Transaktions ID: "); 
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_TEXT_HASH_ERROR', 'checkHash fehlgeschlagen');
	define('MODULE_PAYMENT_NOVALNET_CC_PCI_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
	define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_CC_PCI', 'Sie werden zur Website der Novalnet AG umgeleitet, sobald Sie die Bestellung best&auml;tigen.');

?>
