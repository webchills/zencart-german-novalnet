<?php

#########################################################
#                                                       #
#  INSTANTBANKTRANSFER / DIRECT DEBIT payment text      #
#  creator script                                       #
#  This script is used for translating the text for     #
#  real time processing of German Bankdata of customer  #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  Novalnet_instantbanktransfer module Created By Dixon #
#  Rajdaniel                                            #
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#                                                       #
#  Version: novalnet_instantbanktransfer.php            #
#  vxtcDEde1.3.2 2009-10-14                             #
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/Sofort_Logo_t.jpg" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Sofortüberweisung</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor der Aktivierung alle noetigen IDs im Bearbeitungsmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS_TITLE', 'Sofortüberweisungsmodul-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_STATUS_DESC', 'Wollen Sie das Deutsche Sofortüberweisungmodul der Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_MANUAL_CHECK_LIMIT_TITLE', 'Manuelle &Uuml;berpr&uuml;fung der Zahlung ab');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_MANUAL_CHECK_LIMIT_DESC', 'Bitte den Betrag in Cent eingeben');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID2_TITLE', 'Zweite Angebots-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PRODUCT_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID2_TITLE', 'Zweite Tarif-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TARIFF_ID2_DESC', 'zur manuallen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO_TITLE', 'Information an Endkunden');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_INFO_DESC', 'wird im Bezahlformular erscheinen');  
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ACDC_TITLE', 'ACDC Control Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ACDC_DESC', 'Wollen Sie ACDC Control aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des Sofortüberweisungsmoduls');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_SORT_ORDER_DESC', 'Sortierungsansicht.');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ZONE_DESC', 'Wenn eine Zone ausgew&auml;hlt ist dann wird dieses Modul nur f&uuml;r die ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen kommagetrennt eingeben(z.B:AT,DE) oder einfach leer lassen');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>Sofortüberweisung</TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/Sofort_Logo_t.jpg" ALT="Bezahlung - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ACDC_INFO', "Der <B><A HREF='javascript:show_acdc_info()' ONMOUSEOVER='show_acdc_info()'>acdc-Check</A></B> wird akzeptiert");
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_ACDC_DIV', "<SCRIPT>var showbaby;function show_acdc_info(){var url=parent.location.href;url=url.substring(0,url.lastIndexOf('/'))+'/images/acdc_info.png';w='550';h='300';x=screen.availWidth/2-w/2;y=screen.availHeight/2-h/2;showbaby=window.open(url,'showbaby','toolbar=0,location=0,directories=0,status=0,menubar=0,resizable=1,width='+w+',height='+h+',left='+x+',top='+y+',screenX='+x+',screenY='+y);showbaby.focus();}function hide_acdc_info(){showbaby.close();}</SCRIPT>");
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_BANK_ACCOUNT_OWNER', '* Deutscher  Kontoinhaber muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_BANK_ACCOUNT_NUMBER', '* Deutsche Kontonummer muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_BANK_CODE', '* Deutsche Bankleitzahl sollte mindestens 8 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_JS_ACDC', '* Bitte den acdc-Check akzeptieren oder eine andere Zahlungsart auswaehlen!');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ERROR', 'Kontodaten Fehler:');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_CUST_INFORM', '"Wir holen zuvor eine Bonit&auml;tsauskunft ein, denn nur bei positiver Auskunft k&ouml;nnen wir die Bestellung durchf&uuml;hren und die Abbuchung erfolgt mit dem Warenversand. Bei Nichteinl&ouml;sung/Widerruf berechnen wir eine Aufwandspauschale von 10,00 Euro und der Vorgang wird sofort dem Inkasso-Verfahren &uuml;bergeben."');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ORDERNO', 'Best.-Nr.: ');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_ORDERDATE', 'Best.-Datum: ');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE', 'Testmodus');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEXT_HASH_ERROR', 'checkHash fehlgeschlagen');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD_TITLE', 'Passwort');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PASSWORD_DESC', 'Passwort eingeben');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TEST_ORDER_MESSAGE'," TESTBESTELLUNG <br> ");
  define('MODULE_PAYMENT_NOVALNET_INSTANTBANKTRANSFER_TID_MESSAGE',". Novalnet-Transaktions-ID : ");   
?>
