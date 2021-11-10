<?php

#########################################################
#                                                       #
#  Prepayment text creator script	                	#
#  This script is used for translating the text for     #
#  real time processing of payment of customers.        #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_invoice module Created By Dixon Rajdaniel   #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
# Ver: novalnet_prepayment.php vxtcPPde1.3.2 2009-03-01 #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Vorkasse</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS_TITLE', 'Prepayment Modul aktivierung');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_STATUS_DESC', 'Wollen Sie das Vorkasse Modul des Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_INFO_DESC', 'wird im Bezahlformular erscheinen');  
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des Prepayment Modul');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_SORT_ORDER_DESC', 'Sortierungsansicht.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ZONE_DESC', 'Wenn ein Zone ausgew&auml;hlt ist dann wird dieser Modul nur f&uuml;r ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen mit komma getrennt eingeben(Zb:AT,DE) oder einfach leer lassen');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Vorkasse</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CODE', 'Bankleitzahl:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_IBAN', 'IBAN:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BIC', 'SWIFT / BIC:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_BANK', 'Bank:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_CITY', 'Stadt:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_AMOUNT', 'Betrag:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE', 'Verwendungszweck:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_REFERENCE_INFO', 'Bitte beachten Sie, dass die Ueberweisung nur bearbeitet werden kann, wenn der oben angegebene Verwendungszweck verwendet wird.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_TRANSFER_INFO', 'Bitte ueberweisen sie den Betrag auf folgendes Konto:');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_BANK_INFO', 'Die Bankdaten werden Ihnen bald nach Beendigung des Checkout-Prozesses per Email zugeschickt werden');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERNO', 'Best.-Nr.: ');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEXT_ORDERDATE', 'Best.-Datum: ');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE', 'Testmodus');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');
  define('MODULE_PAYMENT_NOVALNET_PREPAYMENT_TEST_ORDER_MESSAGE'," TESTBESTELLUNG <br />");
  
 
?>