<?php

#########################################################
#                                                       #
#  ELV_AT / DIRECT DEBIT payment text creator script    #
#  This script is used for translating the text for     #
#  real time processing of Austrian Bankdata of customer#
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_elv_at module Created By Dixon Rajdaniel    #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.             			#
#														#
# Version: novalnet_elv_at.php vxtcATde1.3.2 2009-03-01 #
#                                                       #
#########################################################


  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Lastschriftverfahren &Ouml;sterreich</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS_TITLE', 'ELV-AT Modul aktivierung');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_STATUS_DESC', 'Wollen Sie das &Ouml;sterreichische Lastschriftverfahren Modul des Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT_TITLE', 'Manuelle &Uuml;berpr&uuml;fung der Zahlung ab');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_MANUAL_CHECK_LIMIT_DESC', 'Bitte den Betrag in Cent eingeben');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2_TITLE', 'Zweite Angebots-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PRODUCT_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2_TITLE', 'Zweite Tarif-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TARIFF_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_INFO_DESC', 'wird im Bezahlformular erscheinen');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des ELV-AT Modul');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_SORT_ORDER_DESC', 'Sortierungsansicht.'); 
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_ZONE_DESC', 'Wenn ein Zone ausgew&auml;hlt ist dann wird dieser Modul nur f&uuml;r ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen mit komma getrennt eingeben(Zb:AT,DE) oder einfach leer lassen');  
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Lastschriftverfahren &Ouml;sterreich</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/ELV_Logo.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE', 'Bankleitzahl:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_BANK_CODE_LENGTH', '5');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_OWNER', '* Kontoinhaber von Lastschriftverfahren Oesterreich, muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Kontonummer von Lastschriftverfahren Oesterreich, muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_JS_BANK_CODE', '* Bankleitzahl von Lastschriftverfahren Oesterreich, sollte mindestens 5 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ERROR', 'Kontodaten Fehler:');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERNO', 'Best.-Nr. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEXT_ORDERDATE', 'Best.-Datum ');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE', 'Testmodus');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');
  define('MODULE_PAYMENT_NOVALNET_ELV_AT_TEST_ORDER_MESSAGE'," TESTBESTELLUNG <br />");


?>
