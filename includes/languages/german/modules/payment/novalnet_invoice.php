<?php

#########################################################
#                                                       #
#  INVOICE payment text creator script                  #
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
# Version: novalnet_invoice.php vxtcINde1.3.2 2009-03-01#
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;per Rechnung</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS_TITLE', 'INVOICE Modul aktivierung');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_STATUS_DESC', 'Wollen Sie das Rechnungseingang Modul des Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_DURATION_TITLE', 'Zahlungsfrist in tagen');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_DURATION_DESC', 'wird im Bezahlformular erscheinen');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_INFO_DESC', 'wird im Bezahlformular erscheinen');  
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des INVOICE Modul');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_SORT_ORDER_DESC', 'Sortierungsansicht.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ZONE_DESC', 'Wenn ein Zone ausgew&auml;hlt ist dann wird dieser Modul nur f&uuml;r ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen mit komma getrennt eingeben(Zb:AT,DE) oder einfach leer lassen');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;per Rechnung</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CODE', 'Bankleitzahl:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_IBAN', 'IBAN:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BIC', 'SWIFT / BIC:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_BANK', 'Bank:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_CITY', 'Stadt:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_AMOUNT', 'Betrag:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE', 'Verwendungszweck:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_REFERENCE_INFO', 'Bitte beachten Sie, dass die Ueberweisung nur bearbeitet werden kann, wenn der oben angegebene Verwendungszweck verwendet wird.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_TRANSFER_INFO', 'Bitte ueberweisen Sie den Betrag auf folgendes Konto:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_BANK_INFO', 'Die Bankverbindung wird Ihnen nach Abschluss Ihrer Bestellung per E-Mail zugeschickt!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO', 'Zahlungsfrist:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_INFO_DAYS', 'Tage');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_INFO', 'Bitte ueberweisen Sie den Betrag spaetestens bis zum');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_DURATION_LIMIT_END_INFO', 'auf folgendes Konto:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERNO', 'Best.-Nr. ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEXT_ORDERDATE', 'Best.-Datum ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE', 'Testmodus');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');
  
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TITLE', 'PIN by Callback/SMS');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_DESC', 'Wenn Sie PIN by Callback bzw. PIN by SMS aktiviert haben, wird Ihr Kunde gebeten, seine Telefon-/Mobiltelefonnumer einzugeben. Er erhält dann per Telefon/SMS von der Novalnet AG eine PIN, die er eingeben muss, bevor die Bestellung angenommen wird. ');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL', 'Telefonnummer:*');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN', 'PIN:');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'PIN vergessen? [Neue PIN anfordern]');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Die eingegebene Telefonnummer ist nicht gültig!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'Die eingegebene PIN ist inkorrekt oder leer!');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'Sie werden in kürze eine PIN per Telefon/SMS erhalten. Bitte geben Sie die PIN in das entsprechende Textfeld ein.');  
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT_TITLE', 'Mindestbetrag für PIN by Callback.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_MIN_LIMIT_DESC', 'Bitte geben Sie einen Mindestbetrag in Cent an (z.B. 100, 200), um PIN by Callback in Betrieb zu nehmen.');
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_INPUT_REQUEST_DESC',"<b>Bitte geben Sie Ihre PIN ein.</b>");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_PIN_BY_CALLBACK_SESSION_ERROR',"<b>Ihre PIN-Session ist abgelaufen. Bitte versuchen Sie es noch einmal.</b>");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TEST_ORDER_MESSAGE',"TESTBESTELLUNG <br />");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_TID_MESSAGE',". Novalnet-Transaktions-ID: ");
  define('MODULE_PAYMENT_NOVALNET_INVOICE_AMOUNT_VARIATION_MESSAGE',"Sie haben den Betrag in Ihrem Warenkorb geändert, nachdem Sie Ihre PIN erhalten haben. Bitte rufen Sie noch einmal an, um eine neue PIN zu erhalten.");  
?>
