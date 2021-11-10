<?php
#########################################################
#                                                       #
#  Paypal payment method class                          #
#  This module is used for real time processing of      #
#  transaction of customers.                            #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_paypal.php                         #
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_TITLE', '<nobr>PayPal <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/images/paypal.gif" alt="PayPal" border="0" /></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor der Aktivierung alle noetigen IDs im Bearbeitungsmodus eingeben!<br /><b><font color=\'red\'>Sie m&uuml; ssen &uuml; ber ein Paypal-H&auml; ndlerkonto verf&uuml; gen, bevor Sie dieses Modul einsetzen k&ouml; nnen.</font></b>');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_PUBLIC_TITLE', '<nobr>PayPal <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/images/paypal.gif"  alt="PayPal"/></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ERROR', 'Kontodaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ORDERNO', 'Best.-Nr.: ');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ORDERDATE', 'Best.-Datum: ');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE', 'Test Mode');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_HASH_ERROR', 'checkHash fehlgeschlagen');
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_TID_MESSAGE',"Novalnet Transaktions ID : ");
	define('MODULE_PAYMENT_NOVALNET_PAYPAL_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
	define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_PAYPAL', 'Sie werden zur Website der Novalnet AG umgeleitet, sobald Sie die Bestellung best&auml;tigen.');
?>
