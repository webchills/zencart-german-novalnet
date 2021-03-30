<?php
/**
* This script is used for German language content
*
* @author Novalnet AG
* @copyright Copyright (c) Novalnet
* @license https://www.novalnet.de/payment-plugins/kostenlos/lizenz
* @link https://www.novalnet.de
*
* This free contribution made by request.
*
* If you have found this script useful a small
* recommendation as well as a comment on merchant
*
* Script : novalnet_config.php
*/
define('MODULE_PAYMENT_NOVALNET_CONFIG_TEXT_TITLE', 'Allgemeine Novalnet-Einstellungen');
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_TITLE', 'Aktivierungsschlüssel des Produkts');  
define('MODULE_PAYMENT_NOVALNET_PUBLIC_KEY_DESC', 'Der Produktaktivierungsschlüssel verbindet Ihre Zencart Shop-System mit Ihrem Novalnet-Händleraccount. Verwenden Sie den Produktaktivierungsschlüssel von Novalnet, um in den Allgemeinen Einstellungen automatisch Ihre vollständigen Händlerdaten einzutragen. <br>Ihren Produktaktivierungsschlüssel finden Sie im <a href="https://admin.novalnet.de" target="_blank" style="text-decoration: underline; font-weight: bold; color:#0080c9;">Novalnet Admin-Portal</a>: <b>PROJEKT > Wählen Sie Ihr Projekt > Shop-Parameter > API-Signatur (Produktaktivierungsschlüssel)</b>');

define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_TITLE', ' Händler-ID');  
define('MODULE_PAYMENT_NOVALNET_VENDOR_ID_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_AUTH_CODE_TITLE', ' Authentifizierungscode');  
define('MODULE_PAYMENT_NOVALNET_AUTH_CODE_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_TITLE', ' Projekt-ID');  
define('MODULE_PAYMENT_NOVALNET_PRODUCT_ID_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_TITLE', 'Auswahl der Tarif-ID');  
define('MODULE_PAYMENT_NOVALNET_TARIFF_ID_DESC', 'Wählen Sie eine Tarif-ID, die dem bevorzugten Tarifplan entspricht, den Sie im Novalnet Admin-Portal für dieses Projekt erstellt haben.');  

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_TITLE', 'Paymentzugriffsschlüssel');  
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ACCESS_KEY_DESC', '');  

define('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_TITLE', 'Zahlungslogo anzeigen');  
define('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY_DESC', 'Das Logo der Zahlungsart wird auf der Checkout-Seite angezeigt.');  

define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID_TITLE', '<h5><b>Bestellstatus-Management</b></h5>On-hold-Bestellstatus');  
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_COMPLETE_STATUS_ID_DESC', 'Wählen Sie, welcher Status für On-hold-Bestellungen verwendet wird, solange diese nicht bestätigt oder storniert worden sind.');  

define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID_TITLE', 'Status für stornierte Bestellungen');  
define('MODULE_PAYMENT_NOVALNET_ONHOLD_ORDER_CANCELLED_STATUS_ID_DESC', 'Wählen Sie, welcher Status für stornierte oder voll erstattete Bestellungen verwendet wird.'); 

define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_TITLE', '<h5><b>Benachrichtigungs- / Webhook-URL festlegen</b></h5> Manuelles Testen der Benachrichtigungs- / Webhook-URL erlauben');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_TEST_MODE_DESC', 'Aktivieren Sie diese Option, um die Novalnet-Benachrichtigungs-/Webhook-URL manuell zu testen. Deaktivieren Sie die Option, bevor Sie Ihren Shop liveschalten, um unautorisierte Zugriffe von Dritten zu blockieren.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_TITLE', 'E-Mail-Benachrichtigungen einschalten');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_SEND_DESC', 'Aktivieren Sie diese Option, um die angegebene E-Mail-Adresse zu benachrichtigen, wenn die Benachrichtigungs- / Webhook-URL erfolgreich ausgeführt wurde.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_TITLE', 'E-Mails senden an');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_MAIL_TO_DESC', 'E-Mail-Benachrichtigungen werden an diese E-Mail-Adresse gesendet.'); 

define('MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL_TITLE', 'Benachrichtigungs- / Webhook-URL');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_NOTIFY_URL_DESC', 'Händlers mit dem Novalnet-Account synchronisiert zu halten (z.B. Lieferstatus). Weitere Informationen finden Sie in der Installationsanleitung.');


?>


