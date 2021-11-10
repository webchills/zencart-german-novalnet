<?php

#########################################################
#                                                       #
#  ELV_DE / DIRECT DEBIT payment text creator script    #
#  This script is used for translating the text for     #
#  real time processing of German Bankdata of customer  #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_elv_de module Created By Dixon Rajdaniel    #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#														#
#  Version: novalnet_elv_de.php vxtcDEde1.3.2 2009-03-01#
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Lastschriftverfahren Deutschland</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS_TITLE', 'ELV-DE Modul aktivierung');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_STATUS_DESC', 'Wollen Sie das Deutsche Lastschriftverfahren Modul des Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT_TITLE', 'Manuelle &Uuml;berpr&uuml;fung der Zahlung ab');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_MANUAL_CHECK_LIMIT_DESC', 'Bitte den Betrag in Cent eingeben');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2_TITLE', 'Zweite Angebots-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PRODUCT_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2_TITLE', 'Zweite Tarif-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TARIFF_ID2_DESC', 'zur manuallen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_INFO_DESC', 'wird im Bezahlformular erscheinen');  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_TITLE', 'ACDC Control Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_DESC', 'Wollen Sie ACDC Control aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des ELV-DE Modul');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_SORT_ORDER_DESC', 'Sortierungsansicht.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ZONE_DESC', 'Wenn ein Zone ausgew&auml;hlt ist dann wird dieser Modul nur f&uuml;r ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen mit komma getrennt eingeben(Zb:AT,DE) oder einfach leer lassen');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Lastschriftverfahren Deutschland</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/ELV_Logo.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER', 'Kontoinhaber:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER', 'Kontonummer:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_ACCOUNT_NUMBER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE', 'Bankleitzahl:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_BANK_CODE_LENGTH', '8');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_INFO', "Der <B><A HREF='javascript:show_acdc_info()' ONMOUSEOVER='show_acdc_info()'>acdc-Check</A></B> wird akzeptiert");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_ACDC_DIV', "<SCRIPT>var showbaby;function show_acdc_info(){var url=parent.location.href;url=url.substring(0,url.lastIndexOf('/'))+'/images/acdc_info.png';w='550';h='300';x=screen.availWidth/2-w/2;y=screen.availHeight/2-h/2;showbaby=window.open(url,'showbaby','toolbar=0,location=0,directories=0,status=0,menubar=0,resizable=1,width='+w+',height='+h+',left='+x+',top='+y+',screenX='+x+',screenY='+y);showbaby.focus();}function hide_acdc_info(){showbaby.close();}</SCRIPT>");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_OWNER', '* Deutsche Lastschrift Kontoinhaber muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Deutsche Lastschrift Kontonummer muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_BANK_CODE', '* Deutsche Lastschrift Bankleitzahl sollte mindestens 8 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC', '* Bitte den acdc-Check akzeptieren oder eine andere Zahlungsart auswaehlen!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_JS_ACDC', '* Bitte den acdc-Check akzeptieren oder eine andere Zahlungsart auswaehlen!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ERROR', 'Kontodaten Fehler:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERNO', 'Best.-Nr.: ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEXT_ORDERDATE', 'Best.-Datum: ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE', 'Testmodus');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');

  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TITLE', 'PIN by Callback/SMS');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_DESC', 'Wenn Sie PIN by Callback bzw. PIN by SMS aktiviert haben, wird Ihr Kunde gebeten, seine Telefon-/Mobiltelefonnumer einzugeben. Er erhält dann per Telefon/SMS von der Novalnet AG eine PIN, die er eingeben muss, bevor die Bestellung angenommen wird. ');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL', 'Telefonnummer:*');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN', 'PIN:');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_NEW_PIN', 'PIN vergessen? [Neue PIN anfordern]');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_TEL_NOTVALID', 'Die eingegebene Telefonnummer ist nicht gültig!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_PIN_NOTVALID', 'Die eingegebene PIN ist inkorrekt oder leer!');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SMS_CALL_MESSAGE', 'Sie werden in kürze eine PIN per Telefon/SMS erhalten. Bitte geben Sie die PIN in das entsprechende Textfeld ein.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT_TITLE', 'Mindestbetrag für PIN by Callback.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_MIN_LIMIT_DESC', 'Bitte geben Sie einen Mindestbetrag in Cent an (z.B. 100, 200), um PIN by Callback in Betrieb zu nehmen.');
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_INPUT_REQUEST_DESC',"<b>Bitte geben Sie Ihre PIN ein.</b>");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_PIN_BY_CALLBACK_SESSION_ERROR',"<b>Ihre PIN-Session ist abgelaufen. Bitte versuchen Sie es noch einmal.</b>");
  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TID_MESSAGE',". Novalnet-Transaktions-ID: ");
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_AMOUNT_VARIATION_MESSAGE',"Sie haben den Betrag in Ihrem Warenkorb geändert, nachdem Sie Ihre PIN erhalten haben. Bitte rufen Sie noch einmal an, um eine neue PIN zu erhalten.");  
  define('MODULE_PAYMENT_NOVALNET_ELV_DE_TEST_ORDER_MESSAGE'," TESTBESTELLUNG <br />");
?>
