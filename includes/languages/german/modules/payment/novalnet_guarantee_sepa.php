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
* Script : novalnet_guarantee_sepa.php
*/
include_once(dirname(__FILE__).'/novalnet.php');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_TEXT_TITLE', 'SEPA-Lastschrift mit Zahlungsgarantie');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_TEXT_DESCRIPTION', '<br>Der Betrag wird durch Novalnet von Ihrem Konto abgebucht<br/>');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_PUBLIC_TITLE', 'SEPA-Lastschrift mit Zahlungsgarantie ');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_LOGO', (defined('MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY') && MODULE_PAYMENT_NOVALNET_PAYMENT_LOGO_DISPLAY == 'True') ? zen_image(DIR_WS_IMAGES . 'icons/novalnet/novalnet_sepa.png', 'SEPA-Lastschrift mit Zahlungsgarantie') : '');

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_FORM_MANDATE_CONFIRM_TEXT', '<strong><a id="guarntee_sepa_mandate_toggle"  style="color:#0080c9;" onclick="return showmandate(\'guarantee_sepa_mandate_details\');">Ich erteile hiermit das SEPA-Lastschriftmandat (elektronische Übermittlung) und bestätige, dass die Bankverbindung korrekt ist!</a></strong><div id="guarantee_sepa_mandate_details" style="display:none"><p>Ich ermächtige den Zahlungsempfänger, Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die von dem Zahlungsempfänger auf mein Konto gezogenen Lastschriften einzulösen.</p> <p><strong>Gläubiger-Identifikationsnummer: DE53ZZZ00000004253</strong></p> <p><strong>Hinweis:</strong> Ich kann innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem Kreditinstitut vereinbarten Bedingungen.</p></div>');


define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_STATUS_TITLE', MODULE_PAYMENT_GUARNTEE_REQUIREMENT.MODULE_PAYMENT_NOVALNET_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_STATUS_DESC', MODULE_PAYMENT_NOVALNET_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_TEST_MODE_TITLE', MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_TEST_MODE_DESC', MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_PAYMENT_DUE_DATE_TITLE', 'Tage bis zum Einzug des Betrags per SEPA-Lastschrift');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_PAYMENT_DUE_DATE_DESC', 'Geben Sie die Anzahl der Tage ein, nach denen der Zahlungsbetrag eingezogen werden soll (muss zwischen 2 und 14 Tagen liegen).');

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ONHOLD_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ONHOLD_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ONHOLD_LIMIT_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ONHOLD_LIMIT_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_MIN_AMOUNT_LIMIT_TITLE', 'Mindestbestellbetrag für Zahlungsgarantie');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_MIN_AMOUNT_LIMIT_DESC', 'Diese Einstellung wird den Standardwert für den Mindestbestellbetrag überschreiben. Beachten Sie, dass der Betrag im Bereich von 20,00 EUR – 5.000,00 EUR liegen muss.');

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_CUSTOMER_INFO_TITLE', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_CUSTOMER_INFO_DESC', MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_SORT_ORDER_TITLE', MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_SORT_ORDER_DESC', MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_PENDING_ORDER_STATUS_ID_TITLE', 'Status für Bestellungen mit ausstehender Zahlung');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_PENDING_ORDER_STATUS_ID_DESC', 'Wählen Sie, welcher Status für Bestellungen mit ausstehender Zahlung verwendet wird.');

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ORDER_STATUS_ID_TITLE', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ORDER_STATUS_ID_DESC', MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC);

define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ZONE_TITLE', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE);
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_ZONE_DESC', MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC);
?>
