<?php

#########################################################
#                                                       #
#  Telephone payment text creator script 		        #
#  This script is used for translating the text for     #
#  real time processing of Telephone Payment of customer#
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_tel module Created By Dixon Rajdaniel    	#
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.             			#
#														#
#  Version : novalnet_tel.php vxtcTELde1.3.2 2009-03-01	#
#                                                       #
#########################################################
https://www.novalnet.de/img/novaltel_logo.png

  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE', '<nobr>Novalnet Telefonpayment - Pay by call<a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/novaltel_logo.png" alt="Telefon Payment" border="0"></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_INFO', '');  
  
  define('MODULE_PAYMENT_NOVALNET_TEL_IN_TEST_MODE', ' (im Testbetrieb)');
  define('MODULE_PAYMENT_NOVALNET_TEL_NOT_CONFIGURED', ' (Nicht konfiguriert)');
  define('MODULE_PAYMENT_NOVALNET_TEL_GUEST_USER', 'Gast');
  
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE', '<nobr>Telefonpayment<a href="https://www.novalnet.de" target="_new"><img src="https://www.novalnet.de/img/novaltel_reciever.png" alt="Telefon Payment" border="0"></a></nobr>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP_INFO', '<B>Folgende Schritte sind noetig um Ihre Zahlung abzuschliessen:');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1', '<B>Schritt 1:</B>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2', '<B>Schritt 2:</B>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP1_DESC', 'Bitte rufen Sie die angezeigte Telefonnummer an:');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_STEP2_DESC', 'Bitte warten Sie auf den Signalton und legen Sie dann den Hoerer auf.<BR>Nach Ihrem erfolgreichen Anruf klicken Sie bitte unten auf Weiter.');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_COST_INFO', '* Dieser Anruf kostet einmalig <B>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TAX_INFO', '&euro;</B> (inkl. MwSt) und ist nur vom Deutschen Festnetzanschluss moeglich! *');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR1', 'Betraege unter 0,90 Euro und ueber 10,00 Euro koennen nicht verarbeitet werden bzw. werden nicht akzeptiert!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_AMOUNT_ERROR2', 'Betraege unter 0,90 Euro koennen nicht verarbeitet werden bzw. werden nicht akzeptiert!'); 
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_ERROR', 'Zahlung nicht moeglich!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_ORDERNO', 'Best.-Nr.: ');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_ORDERDATE', 'Best.-Datum: ');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE', 'Testmodus');
   define('MODULE_PAYMENT_NOVALNET_TEL_CURL_MESSAGE',"* Sie müssen die CURL-Funktion auf Server aktivieren, überprüfen Sie bitte mit Ihrem Hosting-Provider darüber!");
?>
