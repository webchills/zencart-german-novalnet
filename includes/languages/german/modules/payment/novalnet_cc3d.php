<?php

#########################################################
#                                                       #
#  CC / Credit card 3dsecure payment text creator script#
#  This script is used for translating the text for     #
#  real time processing of Credit card data of customer #
#  on 3d secure mode                                    #
#                                                       #
#  Copyright (c) 2009 Novalnet AG                       #
#                                                       #
#  Released under the GNU General Public License        #
#  novalnet_cc module Created By Dixon Rajdaniel    	#
#  This free contribution made by request.              #
#  If you have found this script usefull a small        #
#  recommendation as well as a comment on merchant form #
#  would be greatly appreciated.                        #
#														#
#  novalnet_cc_3d.php vxtcCC3Dde1.3.2 2009-03-01		#
#                                                       #
#########################################################

  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_TITLE', '<NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Kreditkarten 3DSecure Bezahlung</NOBR>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_DESCRIPTION', 'Schnell und Sicher bezahlen &uuml;ber Novalnet AG<BR>Bitte vor aktivierung alle noetige IDs in Bearbeitungmodus eingeben!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_LANG', 'DE');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_INFO', '');
  define('MODULE_PAYMENT_NOVALNET_CC3D_STATUS_TITLE', 'Kreditkarten Modul aktivierung');
  define('MODULE_PAYMENT_NOVALNET_CC3D_STATUS_DESC', 'Wollen Sie das Kreditkarten Modul des Novalnet AG aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID_TITLE', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_VENDOR_ID_DESC', 'Novalnet H&auml;ndler ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE_TITLE', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_CC3D_AUTH_CODE_DESC', 'Novalnet H&auml;ndler Authorisierungsschl&uuml;ssel');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID_TITLE', 'Novalnet Angebots-ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID_DESC', 'Ihre Angebots-ID bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID_TITLE', 'Novalnet Tarif-ID');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID_DESC', 'die Tarif-ID des Angebots');
  define('MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT_TITLE', 'Manuelle &Uuml;berpr&uuml;fung der Zahlung ab');
  define('MODULE_PAYMENT_NOVALNET_CC3D_MANUAL_CHECK_LIMIT_DESC', 'Bitte den Betrag in Cent eingeben');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2_TITLE', 'Zweite Angebots-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PRODUCT_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2_TITLE', 'Zweite Tarif-ID Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TARIFF_ID2_DESC', 'zur manuellen &Uuml;berpr&uuml;fung');
  define('MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF_TITLE', 'Ihr hinterlegter Verwendungszweck bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_BOOK_REF_DESC', 'Ihr hinterlegter Verwendungszweck bei Novalnet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID_TITLE', 'Bestellungsstatus');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ORDER_STATUS_ID_DESC', 'Der Bestellstatus des CC Modul');
  define('MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER_TITLE', 'Sortierung nach.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_SORT_ORDER_DESC', 'Sortierungsansicht.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ZONE_TITLE', 'Zahlungsgebiet');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ZONE_DESC', 'Wenn ein Zone ausgew&auml;hlt ist dann wird dieser Modul nur f&uuml;r ausgew&aauml;hlte Zone aktiviert.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED_TITLE', 'erlaubte Zonen');
  define('MODULE_PAYMENT_NOVALNET_CC3D_ALLOWED_DESC', 'Bitte die gew&uuml;nschten Zonen mit komma getrennt eingeben(Zb:AT,DE) oder einfach leer lassen');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_PUBLIC_TITLE', '<DIV><TABLE><TR><TD WIDTH="230" HEIGHT="25" VALIGN="middle"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="http://www.novalnet.de/img/NN_Logo_T.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A>&nbsp;Kreditkarten 3DSecure Bezahlung</NOBR></TD><TD VALIGN="top"><NOBR><A HREF="http://www.novalnet.de" TARGET="_new"><IMG SRC="images/VI_Logo.png" ALT="Bezahlung - Novalnet AG" BORDER="0">&nbsp;<IMG SRC="images/MC_Logo.png" ALT="Bezahlung - Novalnet AG" BORDER="0"></A></NOBR></TD></TR></TABLE></DIV>');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER', 'Kreditkarteninhaber:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CARD_OWNER_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO', 'Kartennummer:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CC_NO_LENGTH', '12');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH', ' Monat:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_MONTH_LENGTH', '2');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR', ' Jahr:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_EXP_YEAR_LENGTH', '2');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_SELECT', 'Bitte waehlen');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC', 'CVC (Pruefziffer): ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC_LENGTH', '3');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_CVC2', '<BR>* Bei Visa-, Master- und Eurocard besteht der CVC-Code<BR>aus den drei letzten Ziffern im Unterschriftenfeld auf der<BR>Rueckseite der Kreditkarte.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_BOOKING_INFO', '<BR><BR>Der Betrag wird von Ihrer Kreditkarte mit dem<BR>Verwendungszweck <B>$BOOKINFO</B> sofort abgebucht.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_NN_MISSING', '* Der zugrundeliegende Parameter fehlt.');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CARD_OWNER', '* Kreditkarteninhaber muss mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CC_NO', '* Kreditkartennummer muss mindestens 12 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_MONTH', '* Monat sollte mindestens 2 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_EXP_YEAR', '* Jahr sollte mindestens 2 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC', '* CVC (Pruefziffer) sollte mindestens 3 stellig sein!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_JS_CVC2', '* ungueltige CVC (Pruefziffer)!');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ERROR', 'Kartendaten Fehler:');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERNO', 'Best.-Nr.: ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEXT_ORDERDATE', 'Best.-Datum: ');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE', 'Testmodus');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE_TITLE', 'Test-Modus-Aktivierung');
  define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_MODE_DESC', 'Wollen Sie den Test-Modus aktivieren?');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PROXY_TITLE', 'Proxy');
  define('MODULE_PAYMENT_NOVALNET_CC3D_PROXY_DESC', 'Wenn Sie ein Proxy einsetzen, tragen Sie hier Ihre Proxy-IP ein (z.B. www.proxy.de:80)');
    define('MODULE_PAYMENT_NOVALNET_CC3D_TEST_ORDER_MESSAGE',"TESTBESTELLUNG <br />");

?>
