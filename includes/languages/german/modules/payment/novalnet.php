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
* Script : novalnet.php
*/
define('MODULE_PAYMENT_NOVALNET_STATUS_TITLE', 'Zahlungsart aktivieren');
define('MODULE_PAYMENT_NOVALNET_STATUS_DESC', '');

define('MODULE_PAYMENT_NOVALNET_TEST_MODE_TITLE', 'Testmodus aktivieren');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_DESC', 'Aktivieren Sie diese Option, um das Bezahlen auf Ihrer Checkout-Seite zu testen. Im Testmodus werden Zahlungen nicht von Novalnet ausgeführt. Vergessen Sie nicht, den Testmodus nach dem Testen wieder zu deaktivieren, um sicherzustellen, dass die echten Bestellungen ordnungsgemäß abgerechnet werden.');

define('MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_TITLE', 'Benachrichtigung des Käufers');
define('MODULE_PAYMENT_NOVALNET_CUSTOMER_INFO_DESC', 'Der eingegebene Text wird auf der Checkout-Seite angezeigt');

define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_TITLE', 'Geben Sie eine Sortierreihenfolge an');
define('MODULE_PAYMENT_NOVALNET_SORT_ORDER_DESC', 'Die Zahlungsarten werden in Ihrem Checkout anhand der von Ihnen vorgegebenen Sortierreihenfolge angezeigt (in aufsteigender Reihenfolge).');

define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_TITLE', 'Status für erfolgreichen Auftragsabschluss');
define('MODULE_PAYMENT_NOVALNET_ORDER_STATUS_DESC', 'Wählen Sie, welcher Status für erfolgreich abgeschlossene Bestellungen verwendet wird.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_ORDER_STATUS_TITLE', 'Callback-Bestellstatus');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_ORDER_STATUS_DESC', 'Wählen Sie, welcher Status nach der erfolgreichen Ausführung des Novalnet-Callback-Skripts (ausgelöst bei erfolgreicher Zahlung) verwendet wird.');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_TITLE', 'Zahlungsgebiet');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ZONE_DESC', 'Diese Zahlungsart wird für die angegebenen Gebiete angezeigt');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_TITLE', 'Zahlungsbestätigung');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_DESC', 'Wählen Sie, ob die Zahlung sofort belastet werden soll oder nicht. <b>Zahlung einziehen:</b> Betrag sofort belasten. <b>Zahlung autorisieren:</b> Die Zahlung wird überprüft und autorisiert, aber erst zu einem späteren Zeitpunkt belastet. So haben Sie Zeit, über die Bestellung zu entscheiden.
');

define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_TITLE', '<div id="set_limit_title">Mindesttransaktionsbetrag für die Autorisierung (in der kleinsten Währungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)</div>');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_ONHOLD_LIMIT_DESC','<div id="set_limit_desc">Transaktionen über diesem Betrag werden bis zum Capture als "nur autorisiert" gekennzeichnet. Lassen Sie das Feld leer, um alle Transaktionen zu autorisieren.</div>');

define('MODULE_PAYMENT_NOVALNET_TRANSACTION_DETAILS', 'Novalnet-Transaktionsdetails');
define('MODULE_PAYMENT_NOVALNET_TRANSACTION_ID', 'Novalnet Transaktions-ID: ');
define('MODULE_PAYMENT_NOVALNET_TEST_ORDER_MESSAGE', 'Testbestellung');
define('MODULE_PAYMENT_NOVALNET_TEST_MODE_MSG', '<span style="color:red;">Die Zahlung wird im Testmodus durchgeführt, daher wird der Betrag für diese Transaktion nicht eingezogen<br/></span>');
define('MODULE_PAYMENT_NOVALNET_INVOICE_COMMENTS_PARAGRAPH', 'Überweisen Sie bitte den Betrag an die unten aufgeführte Bankverbindung unseres Zahlungsdienstleisters Novalnet');
define('MODULE_PAYMENT_NOVALNET_ACCOUNT_HOLDER', 'Kontoinhaber');
define('MODULE_PAYMENT_NOVALNET_IBAN', 'IBAN');
define('MODULE_PAYMENT_NOVALNET_DUE_DATE', 'Fälligkeitsdatum ');
define('MODULE_PAYMENT_NOVALNET_BANK', 'Bank');
define('MODULE_PAYMENT_NOVALNET_AMOUNT', 'Betrag');
define('MODULE_PAYMENT_NOVALNET_SWIFT_BIC', 'BIC');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF1', 'Verwendungszweck 1');
define('MODULE_PAYMENT_NOVALNET_INVPRE_REF2', 'Verwendungszweck 2');
define('MODULE_PAYMENT_NOVALNET_PAYMENT_MULTI_TEXT', 'Bitte verwenden Sie einen der unten angegebenen Verwendungszwecke für die Überweisung, da nur so Ihr Geldeingang zugeordnet werden kann:');

define('MODULE_PAYMENT_NOVALNET_TRANSACTION_REDIRECT_ERROR', 'Während der Umleitung wurden einige Daten geändert. Die Überprüfung des Hashes schlug fehl');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_CHOOSE_PLAN', 'Wählen Sie Ihren Ratenplan');
define('MODULE_PAYMENT_NOVALNET_VALID_ACCOUNT_CREDENTIALS_ERROR', 'Ihre Kontodaten sind ungültig');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PLAN','Wählen Sie Ihren Ratenplan');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PLAN_TEXT','Wählen Sie die Finanzierungsoption, die Ihren Bedürfnissen am besten entspricht. Die Raten werden Ihnen entsprechend dem gewählten Ratenplan berechnet');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_TEXT','Netto-Kreditbetrag : ');
define('MODULE_PAYMENT_NOVALNET_ENDCUSTOMER_BIRTH_DATE', 'Ihr Geburtsdatum');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_SEPA_DOB_ERROR_MSG', 'Geben Sie ein gültiges Geburtsdatum ein');
define('MODULE_PAYMENT_NOVALNET_AGE_ERROR', 'Sie müssen mindestens 18 Jahre alt sein');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_TEXT', 'Ratenzahlung');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NUMBER', 'Ratenzahl');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_MONTHLY_AMOUNT', 'Monatlich fällige Rate');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_INFO', 'Information zu den Raten: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PROCESSED', 'Bezahlte Raten: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DUE', 'Offene Raten: ');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NXT_AMOUNT', 'Betrag jeder Rate');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DEBIT_TEXT', 'Die nächste Rate in Höhe von %s wird in ein bis drei Werktagen von Ihrem Konto abgebucht.');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_COUNTRY', '<span style="color:red;">Die Zahlung kann nicht ausgeführt werden, weil die Voraussetzungen für die Zahlungsgarantie nicht erfüllt sind (nur Deutschland, Österreich oder die Schweiz sind zulässig).</span>');

define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_CURRENCY', '<span style="color:red;">Die Zahlung kann nicht ausgeführt werden, weil die Voraussetzungen für die Zahlungsgarantie nicht erfüllt sind (Nur EUR als Währung erlaubt).</span>');

define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_ADDRESS','<span style="color:red;">Die Zahlung kann nicht ausgeführt werden, weil die Voraussetzungen für die Zahlungsgarantie nicht erfüllt sind (Die Lieferadresse muss mit der Rechnungsadresse identisch sein)</span>');
define('MODULE_PAYMENT_NOVALNET_FORCE_GUARANTEE_ERROR_MESSAGE_AMOUNT','<span style="color:red;">
Die Zahlung kann nicht ausgeführt werden, weil die Voraussetzungen für die Zahlungsgarantie nicht erfüllt sind (Mindestbestellwert %s).</span>');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_CANCEL', 'Die Transaktion wurde am %s um Uhr storniert');
define('MODULE_PAYMENT_NOVALNET_PRZELEWY_CANCEL', 'Die Transaktion wurde storniert. Grund: %s');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_HOLD_TO_PENDING','Der Status der Transaktion mit der TID: %s am %s. Uhr von ausgesetzt auf ausstehend geändert.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_PENDING_TO_HOLD','Der Status der Transaktion mit der TID: %s wurde am %s. Uhr von ausstehend auf ausgesetzt geändert.');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_CREDIT', 'Novalnet-Callback-Skript erfolgreich ausgeführt für: TID %s mit Betrag %s am %s. Nach bezahlter Transaktion finden Sie diese in unserer Novalnet Admin-Portal unter folgender TID: %s');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_EXECUTE', 'Novalnet-Callback-Skript erfolgreich ausgeführt für die TID: %s mit dem Betrag %s on %s.');
define('MODULE_PAYMENT_GUARANTEE_PAYMENT_MAIL_MESSAGE','Wir freuen uns Ihnen mitteilen zu können, dass Ihre Bestellung bestätigt wurde.');
define('MODULE_PAYMENT_GUARANTEE_PAYMENT_MAIL_SUBJECT','Bestellbestätigung – Ihre Bestellung %s bei %s wurde bestätigt!');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_CONFIRM','Novalnet-Callback-Nachricht erhalten: Die Buchung wurde am %s Uhr bestätigt');

define('MODULE_PAYMENT_NOVALNET_CALLBACK_CHARGEBACK', 'Chargeback erfolgreich importiert für die TID: %s Betrag: %s um %s. Uhr. TID der Folgebuchung: %s.');
define('MODULE_PAYMENT_NOVALNET_CALLBACK_BOOKBACK', 'Rückerstattung / Bookback erfolgreich ausgeführt für die TID: %s Betrag: %s am %s. TID der Folgebuchung: %s.');

define('MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_CYCLE_TEXT', 'Für Ihre Bestellung Nr. %s bei %s ist die nächste Rate fällig. Bitte beachten Sie weitere Details unten');
define('MODULE_PAYMENT_NOVALNET_REFERENCE', 'Verwendungszweck: ');
define('MODULE_PAYMENT_SEPA_DUE_DATE_VALIDATION', 'SEPA Fälligkeitsdatum Ungültiger');
define('MODULE_PAYMENT_NOVALNET_INVOICE_DUE_DATE_ERROR', 'Geben Sie bitte ein gültiges Fälligkeitsdatum ein');
define('MODULE_PAYMENT_GUARNTEE_REQUIREMENT', '<h5><b>Grundanforderungen für die Zahlungsgarantie</b></h5><ul>
    <li>Zugelassene Staaten: AT, DE, CH</li>
    <li>Zugelassene Währung: EUR</li>
    <li>Mindestbetrag der Bestellung: 9,99 EUR</li>
    <li>Mindestalter des Endkunden >= 18 Jahre</li>
    <li>Rechnungsadresse und Lieferadresse müssen übereinstimmen</li>
</ul>');
define('MODULE_PAYMENT_INSTALMENT_REQUIREMENT','<h5><b>Grundanforderungen für die Ratenzahlung</b></h5><ul>
<li>Zugelassene Staaten: AT, DE, CH</li>
<li>Zugelassene Währung: EUR</li>
<li>Mindestbetrag der Bestellung >= 19,98 EUR</li>
<li>Bitte beachten Sie, dass der Betrag einer Rate mindestens 9,99 EUR betragen muss und Raten, die diese Kriterien nicht erfüllen, nicht im Ratenplan angezeigt werden.</li>
<li>Mindestalter des Endkunden >= 18 Jahre</li>
<li>Rechnungsadresse und Lieferadresse müssen übereinstimmen.</li>
</ul><br>');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_MESSAGE', 'Ihre Bestellung ist unter Bearbeitung. Sobald diese bestätigt wurde, erhalten Sie alle notwendigen Informationen zum Ausgleich der Rechnung. Wir bitten Sie zu beachten, dass dieser Vorgang bis zu 24 Stunden andauern kann');
define('MODULE_PAYMENT_NOVALNET_GUARANTEED_SEPA_MESSAGE', 'Ihre Bestellung wird derzeit überprüft. Wir werden Sie in Kürze über den Bestellstatus informieren. Bitte beachten Sie, dass dies bis zu 24 Stunden dauern kann');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT', 'JJJJ-MM-TT');
define('MODULE_PAYMENT_NOVALNET_GUARANTEE_DOB_FORMAT_ERROR', 'Ungültiges Datumsformat');
define('MODULE_PAYMENT_NOVALNET_VALID_MERCHANT_CREDENTIALS_ERROR', 'Füllen Sie bitte alle Pflichtfelder aus.');
define('MODULE_PAYMENT_NOVALNET_VALID_EMAIL_ERROR', 'Ungültige Werte für die Felder email');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY', 'Zusammenfassung');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_PAID_DATE', 'Bezahlt am');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_DATE', 'Nächste Rate fällig am');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE', 'Verwendungszweck');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS', 'Status');
define('MODULE_PAYMENT_NOVALNET_INSTALMENT_SEPA_INFO', 'Die nächste Rate in Höhe von %s %s wird in ein bis drei Werktagen von Ihrem Konto abgebucht.');
define('MODULE_PAYMENT_NOVALNET_CYCLES','%s Raten');
define('MODULE_PAYMENT_NOVALNET_PER_MONTH',' pro %s Monat');
?>
