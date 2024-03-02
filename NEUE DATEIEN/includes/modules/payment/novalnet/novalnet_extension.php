<?php
/**
 * Novalnet payment module
 * This script is used for post process of Novalnet payment orders
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_extension.php
 */

require_once(DIR_FS_CATALOG . 'includes/modules/payment/novalnet/NovalnetHelper.php');
include_once(DIR_FS_CATALOG ."includes/languages/". $_SESSION['language']."/modules/payment/novalnet_payments.php");

    $output = '';
    $nn_html = '<tr class="dataTableHeadingRow" style="background-color : #dddddd;">';

// Prepare output based on suitable content components
if (defined('MODULE_PAYMENT_NOVALNET_STATUS') && MODULE_PAYMENT_NOVALNET_STATUS == 'True') {
    $request = $_REQUEST;
    $output = '<!-- BOF: aim admin transaction processing tools -->';
    $output .= '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'includes/modules/payment/novalnet/novalnet_extension.js"></script>';
    // Zero amount booking process
    $order_total = $db->Execute("SELECT value FROM " . TABLE_ORDERS_TOTAL . " where class = 'ot_total' AND orders_id = " . zen_db_input($request['oID']));
    if ($transaction_details->fields['amount'] == 0 &&
         isset($payment_details['zero_amount_booking']) &&
        $transaction_details->fields['status'] == 'CONFIRMED'
    ) {
        $amount = round($order_total->fields['value'], 2) * 100;

        $output .= '<td><table class="noprint">';
        $output .= $nn_html;
        $output .= '<td><label class="title">'.MODULE_PAYMENT_NOVALNET_BOOK_TITLE . '</label></td><td></td></tr></br>'."\n";
        $output .= zen_draw_form('novalnet_book_amount', 'novalnet_extension_helper.php');
        $output .= $nn_html. '<td class="dataTableContent">';
        $output .= '</br><label>' .MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $transaction_details->fields['tid'].'</label></td><td></td></tr>';
        $output .= '<tr class="dataTableRow">';
        $output .= '<td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE . '</td>';
        $output .=  zen_draw_hidden_field('oID', $request['oID']);
        $output .=  zen_draw_hidden_field('nn_zero_amount_book_confirm', MODULE_PAYMENT_NOVALNET_PAYMENT_ZERO_AMOUNT_BOOK_CONFIRM);
        $output .=  zen_draw_hidden_field('nn_amount_error', MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE);
        $output .= '<td class="dataTableContent">';
        $output .= zen_draw_input_field('book_amount', $amount, 'id="book_amount" autocomplete="off" style="margin:0% 0% 0% 2%"') . MODULE_PAYMENT_NOVALNET_AMOUNT_EX . '</td></tr>';
        $output .= '<tr class="dataTableRow"><td class="dataTableContent">'.zen_draw_input_field('nn_book_confirm', html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT), 'class="btn btn-primary" onclick="return refund_amount_validation();" style="float:left"', false, 'submit').'</td><td></td></tr>';
        $output .= '</form>';
        $output .='</table></td>'."\n";
    }

    // Instalment refund and cancel process
    if (!empty($transaction_details->fields['instalment_cycle_details'])) {
        $instalment_details = json_decode($transaction_details->fields['instalment_cycle_details'], true);
        if (!empty($instalment_details)) {
            $output .= '
            <script>
                function novalnetRefundbuttonsHandler(cycle) {
                  var refund_id = document.getElementById("instalment_refund_"+cycle);
                  if (refund_id.style.display === "none") {
                    refund_id.style.display = "block";
                  } else {
                    refund_id.style.display = "none";
                  }
                }
            </script>';
            $output .= '<td><table class="noprint table">';
            $output .= $nn_html;
            $output .= '<td class="main" colspan="6"><label style="font-size: 12px;">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY_BACKEND . '</label></td><td></td></tr></br>'."\n";
            $output .= '<tr class="dataTableHeadingRow"><td class="dataTableContent" colspan="6">';
            $instalment_status = [];

            foreach ($instalment_details as $key => $instalment_details_data) {
                array_push($instalment_status, $instalment_details_data['status']);
            }

            $nn_instalment_canceled = false;
            $nn_instacancel_remaining = 'style="display:block"';
            $nn_instacancel_allcycles = 'style="display:block"';

            if (in_array('Canceled', $instalment_status)) {
                $nn_instalment_canceled = true;
            } elseif (in_array('Refunded', $instalment_status)) {
                $nn_instacancel_remaining = 'style="display:block"';
                $nn_instacancel_allcycles = 'style="display:none"';
            } elseif (in_array('Paid', $instalment_status) && !empty($instalment_details_data['reference_tid'])) {
                $nn_instacancel_remaining = 'style="display:none"';
                $nn_instacancel_allcycles = 'style="display:block"';
            }
            if (in_array('Refunded', $instalment_status) && !empty($instalment_details_data['reference_tid'])) {
                 $nn_instalment_canceled = true;
            }

            if ($nn_instalment_canceled == false) {
                $output .= '<button id="nn_instalment_cancel" class="btn btn-primary" style="display: block;">' . MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ADMIN_TEXT . '</button>';
            }

            $output .=  zen_draw_form('nn_instalment_cancel', 'novalnet_extension_helper.php');
            $output .= '<div id= novalnet_instalment_cancel style="display: none;">' ;
            $output .= zen_draw_hidden_field('nn_insta_allcycles', MODULE_PAYMENT_NOVALNET_ALLCYCLES_ERROR_MESSAGE);
            $output .= zen_draw_hidden_field('nn_insta_remainingcycles', MODULE_PAYMENT_NOVALNET_REMAINING_CYCLES_ERROR_MESSAGE);
            $output .= zen_draw_hidden_field('oID', $request['oID']);
            $output .= zen_draw_input_field('nn_instacancel_remaincycles', html_entity_decode(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES), 'id="nn_instacancel_remaincycles" class="btn btn-primary" '.$nn_instacancel_remaining, false, 'submit')."&nbsp;";
            $output .= zen_draw_input_field('nn_instacancel_allcycles', html_entity_decode(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES), 'id="nn_instacancel_allcycles" class="btn btn-primary" '.$nn_instacancel_allcycles, false, 'submit');
            $output .= '</div></form></td><td></td></tr>';
            $output .= '<tr class="dataTableHeadingRow"><td class="dataTableContent">S.No</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_DATE_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_PAID_DATE_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE_BACKEND.'</td><td></td></tr>';
            $nn_instalment_table = '';
            $instalment_amount = 0;
            $status = [];
            $sno = 1;

            foreach ($instalment_details as $key => $instalment_details_data) {
                $instalment_amount = (strpos((string) $instalment_details_data['instalment_cycle_amount'], '.')) ? $instalment_details_data['instalment_cycle_amount'] * 100 : $instalment_details_data['instalment_cycle_amount'];

                if (!empty($instalment_details_data['status'])) {
                    $status = $instalment_details_data['status'];
                } else {
                    $status = (empty($instalment_details_data['reference_tid'])) ? 'Pending' : (($instalment_amount > 0) ? 'Paid' : 'Refunded');
                }

                $status = constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_' .  strtoupper($status));
                $href = (isset($instalment_details_data['reference_tid']) && !empty($instalment_details_data['reference_tid']) != '' && $instalment_amount != '0' && $instalment_amount > 0 && $status != constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_REFUNDED')) ? "&nbsp;<button id='nn_refund1' class='btn btn-primary' onclick='novalnetRefundbuttonsHandler($key)'>" . MODULE_PAYMENT_NOVALNET_REFUND_TEXT . "</button>" : '';
                $instalment_amount_formatted = !empty($instalment_amount) ? $currencies->format($instalment_amount/100, 1, $order->info['currency']) : '-';
                $nn_instalment_table .= "<tr class='dataTableRow'><td class='dataTableContent'>".$sno++."</td><td class='dataTableContent'>".$instalment_amount_formatted.' '.$href."</td>
                <td class='dataTableContent'>".(isset($instalment_details_data['next_instalment_date']) ? $instalment_details_data['next_instalment_date'] : '')."</td><td class='dataTableContent'>".(isset($instalment_details_data['paid_date']) ? $instalment_details_data['paid_date'] : '')."</td><td class='dataTableContent'>$status</td><td class='dataTableContent'>".(isset($instalment_details_data['reference_tid']) ? $instalment_details_data['reference_tid'] : '')."</td>";
                $nn_instalment_table .= '<td class="dataTableContent">'.zen_draw_form('nn_refund_confirm', 'novalnet_extension_helper.php');
                $nn_instalment_table .= '<div id= instalment_refund_'.$key.' style="display: none;">' ;
                $nn_instalment_table .= zen_draw_hidden_field('oID', $request['oID']);
                $nn_instalment_table .= zen_draw_hidden_field('refund_tid', (isset($instalment_details_data['reference_tid']) ? $instalment_details_data['reference_tid'] : ''));
                $nn_instalment_table .= zen_draw_hidden_field('instalment_cycle', ''.$key.'');
                $nn_instalment_table .= zen_draw_hidden_field('nn_amount_error', MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE);
                $nn_instalment_table .= zen_draw_hidden_field('nn_refund_amount_confirm', MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM);
                $nn_instalment_table .= zen_draw_input_field('refund_trans_amount', $instalment_amount, 'id="refund_trans_amount"  style="width:100px;margin:0 0 0 2%" autocomplete="off"')."&nbsp;";
                $nn_instalment_table .= zen_draw_input_field('nn_refund_confirm', html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT), 'class="btn btn-primary" onclick="return refund_amount_validation();" ', false, 'submit')."&nbsp;" ;
                $nn_instalment_table .= "<a class='btn btn-primary' href='" . zen_href_link(FILENAME_ORDERS, 'oID=' . $request['oID'] . '&action=edit') . "'>" . MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_TEXT . "</a>";
                $nn_instalment_table .= '</div></form></td></tr>';
            }

            $nn_instalment_table .='</table></td>'."\n";
            $output .= $nn_instalment_table;
        }
    }

    $output .= '<!-- EOF: aim admin transaction processing tools -->';
}
