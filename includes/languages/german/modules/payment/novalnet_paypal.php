<?php

#########################################################
#                                                       #
#  Paypal payment text creator script                   #
#  This script is used for translating the text for     #
#  real time processing of transation of customers      #
#                                                       #
#  Copyright (c) 2010 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_Paypal module Created By Dixon              #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Script:  novalnet_paypal.php                         #
#  Version: 1.0.0 2010-10-20                            #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/nn_paypal_small.png" ALT="Payment - Novalnet AG" BORDER="0"></A>&nbsp;Novalnet-Paypal</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor der Aktivierung alle noetigen IDs im Bearbeitungsmodus eingeben!<br /><b><font color=\'red\'>Sie müssen über ein Paypal-Händlerkonto verfügen, bevor Sie dieses Modul einsetzen können.</font></b>');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_STATUS_TITLE', 'Novalnet-Paypal-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_STATUS_DESC', 'Wollen Sie das Paypalmodul der Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID_TITLE', 'Novalnet Produkt-ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID_DESC', 'Ihre Produkt-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_MANUAL_CHECK_LIMIT_TITLE', 'Manuelle &Uuml;berpr&uuml;fung der Zahlung ab');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_MANUAL_CHECK_LIMIT_DESC', 'Bitte den Betrag in Cent eingeben');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID2_TITLE', 'Zweite Angebots-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PRODUCT_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID2_TITLE', 'Zweite Tarif-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TARIFF_ID2_DESC', 'zur manuallen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_INFO_DESC', 'wird im Bezahlformular erscheinen');  
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des Paypalmoduls');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_SORT_ORDER_DESC', 'Sortierungsansicht.');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ZONE_TITLE', 'Payment Zone');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist dann wird dieses Modul nur f&uuml;r die ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen kommagetrennt eingeben(z.B:AT,DE) oder einfach leer lassen');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Payment - Novalnet AG" BORDER="0"></A><b>Novalnet-Paypal</b> (Sie m&uuml;ssen &uuml;ber ein Paypal-H&auml;ndler-Konto verf&uuml;gen)</TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/nn_paypal_klein.png" ALT="Payment - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_NN_MISSING', '* Basisparameter fehlen!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_BANK_ACCOUNT_OWNER', '* Deutscher  Kontoinhaber muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Deutsche Kontonummer muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_BANK_CODE', '* Deutsche Bankleitzahl sollte mindestens 8 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_JS_ACDC', '* Bitte den acdc-Check akzeptieren oder eine andere Zahlungsart auswaehlen!');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ERROR', 'Kontodaten Fehler:');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ORDERNO', 'Best.-Nr.: ');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_ORDERDATE', 'Best.-Datum: ');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE', 'Test Mode');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEXT_HASH_ERROR', 'checkHash fehlgeschlagen');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PASSWORD_TITLE', 'Passwort');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PASSWORD_DESC', 'Passwort eingeben');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');

  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_USER_TITLE', 'PAYPAL Benutzername');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_USER_DESC', 'Geben Sie Ihren PAYPAL API Benutzernamen ein');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_USER', 'PAYPAL API Benutzername');

  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_PASSWORD_TITLE', 'PAYPAL API Passwort');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_PASSWORD_DESC', 'Geben Sie Ihr PAYPAL API Passwort ein');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_PASSWORD', 'PAYPAL API Passwort');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_TEST_ORDER_MESSAGE',"<b>Testbuchung</b> <br>");
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_SIGNATURE_TITLE', 'PAYPAL API Signatur');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_SIGNATURE_DESC', 'Geben Sie Ihre PAYPAL API Signatur ein');
  define('MODULE_PAYMENT_NOVALNET_PAYPAL_API_SIGNATURE', 'PAYPAL API Signatur');
  define('MODULE_PAYMENT_PAYPAL_TEXT_INFO','<img src="https://www.paypal.com/de_DE/DE/i/logo/lockbox_150x47.gif" />');

  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ABORT_ID_TITLE', 'Bestellstatus "Paypal Abbruch"');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_ABORT_ID_DESC', 'Bestellstatus bei Abbruch (z.B. PayPal Abbruch)');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_PENDING_ID_TITLE', 'Bestellstatus "Offen PP wartend"');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_PENDING_ID_DESC', 'Bestellstatus bei anhängiger Transaktion (z.B. Offen PP wartend)');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_REJECTED_ID_TITLE', 'Bestellstatus "Paypal abgelehnt"');
  #define('MODULE_PAYMENT_NOVALNET_PAYPAL_ORDER_STATUS_REJECTED_ID_DESC', 'Bestellstatus bei Ablehnung (z.B. PayPal abgelehnt)');
?>
