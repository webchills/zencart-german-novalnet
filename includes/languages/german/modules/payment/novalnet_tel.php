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


  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/novaltel_logo.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Telefonpayment - Pay by call</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_TEL_STATUS_TITLE', 'TEL Modul aktivierung');
  define('MODULE_PAYMENT_NOVALNET_TEL_STATUS_DESC', 'Wollen Sie das Telefonpayment Modul des Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_TEL_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_TEL_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_TEL_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_TEL_INFO_DESC', 'wird im Bezahlformular erscheinen');
  define('MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_TEL_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des TEL Modul');
  define('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_TEL_SORT_ORDER_DESC', 'Sortierungsansicht.'); 
  define('MODULE_PAYMENT_NOVALNET_TEL_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_TEL_ZONE_DESC', 'Wenn ein Zone ausgew&auml;hlt ist dann wird dieser Modul nur f&uuml;r ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_TEL_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen mit komma getrennt eingeben(Zb:AT,DE) oder einfach leer lassen');  
  define('MODULE_PAYMENT_NOVALNET_TEL_TEXT_PUBLIC_TITLE', '<A NAME="novalnet_tel"></A><DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/novaltel_logo.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Telefonpayment - Pay by call</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/novaltel_reciever.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
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
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_TEL_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_TEL_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_TEL_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');

?>
