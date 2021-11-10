<?php

#########################################################
#                                                       #
#  IDEAL / IDEAL payment      							#
#  method class                                         #
#  This module is used for real time processing of      #
#  German Bankdata of customers.                        #
#                                                       #
#  Released under the GNU General Public License.       #
#  This free contribution made by request.              #
#  If you have found this script useful a small         #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script : novalnet_ideal.php     						#
#                                                       #
#########################################################

	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_TITLE', '<nobr>iDEAL <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/ideal_payment_small.png" height="47" alt="iDEAL" border="0" /></a></nobr>');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor der Aktivierung alle noetigen IDs im Bearbeitungsmodus eingeben!');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_LANG', 'DE');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_INFO', '');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_PUBLIC_TITLE', '<nobr>iDEAL <a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/ideal_payment_small.png" height="47" alt="iDEAL"/></a></nobr>'); 
	define('MODULE_PAYMENT_NOVALNET_IDEAL_IN_TEST_MODE', ' (im Testbetrieb)');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_NOT_CONFIGURED', ' (Nicht konfiguriert)');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_GUEST_USER', 'Gast');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_JS_NN_MISSING', '* Grundlegende Parameter fehlt!');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ERROR', 'Kontodaten Fehler:');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ORDERNO', 'Best.-Nr.: ');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_ORDERDATE', 'Best.-Datum: ');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEST_MODE', 'Testmodus');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEXT_HASH_ERROR', 'checkHash fehlgeschlagen');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TEST_ORDER_MESSAGE',"Testbestellung");
	define('MODULE_PAYMENT_NOVALNET_IDEAL_TID_MESSAGE',"Novalnet Transaktions ID : ");
	define('MODULE_PAYMENT_NOVALNET_IDEAL_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');
	define('MODULE_PAYMENT_NOVALNET_IDEAL_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
	define('MODULE_PAYMENT_NOVALNET_INFORMATION_PAYMENT_IDEAL', 'Sie werden zur Website der Novalnet AG umgeleitet, sobald Sie die Bestellung best&auml;tigen.');
?>
