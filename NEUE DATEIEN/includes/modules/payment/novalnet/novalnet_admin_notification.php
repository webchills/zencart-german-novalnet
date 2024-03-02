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
 * Script : novalnet_admin_notification.php
 */

include_once(DIR_FS_CATALOG ."includes/languages/". $_SESSION['language']."/modules/payment/novalnet_payments.php");

    $outputVoidCapt = $outputRefund = $output = '';
    $nn_script = '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'includes/modules/payment/novalnet/novalnet_extension.js"></script>';
    $nn_html = '<tr class="dataTableHeadingRow" style="background-color : #dddddd;">';
    $request = $_REQUEST;

if (method_exists($this, '_doRefund')) {
    // Refund process
    if (isRefund($transaction_details->fields)) {
        $avail_refund = $refund_value = 0;
        $avail_refund = (!empty($transaction_details->fields['callback_amount'])) ? $transaction_details->fields['callback_amount'] : $transaction_details->fields['amount'];
        $refund_value = (!empty($transaction_details->fields['refund_amount'])) ? ($avail_refund - $transaction_details->fields['refund_amount']) : $avail_refund;

        $outputRefund .= '<td><table class="noprint">';
        $outputRefund .= $nn_html;
        $outputRefund .= '<td><label class="title">'.MODULE_PAYMENT_NOVALNET_REFUND_TITLE . '</label></td><td></td></tr></br>'."\n";
        $outputRefund .= zen_draw_form('novalnet_trans_refund', FILENAME_ORDERS, zen_get_all_get_params(array('action')) . 'action=doRefund', 'post', '', true) ;
        $outputRefund .= $nn_script ;
        $outputRefund .= $nn_html.'<td class="dataTableContent">';
        $outputRefund .= '</br><label>' .MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $transaction_details->fields['tid'].'</label></td><td></td></tr>';
        $outputRefund .= '<tr class="dataTableRow">';
        $outputRefund .= '<td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE . '</td>';
        $outputRefund .=  zen_draw_hidden_field('oID', $request['oID']);
        $outputRefund .=  zen_draw_hidden_field('nn_amount_error', MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE);
        $outputRefund .=  zen_draw_hidden_field('nn_refund_amount_confirm', MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM);
        $outputRefund .= '<td class="dataTableContent">';
        $outputRefund .= zen_draw_input_field('refund_trans_amount', $refund_value, 'id="refund_trans_amount"  style="width:100px;margin:0 0 0 2%" autocomplete="off"') . MODULE_PAYMENT_NOVALNET_AMOUNT_EX . '</td></tr>';
        $outputRefund .= '<td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_REFUND_REASON_TITLE . '</td>';
        $outputRefund .= '<td class="dataTableContent">' . zen_draw_input_field('refund_reason', '', 'id="refund_reason" style="margin:0 0 0 2%;" autocomplete="off"').'</td>';
        $outputRefund .= '<tr class="dataTableRow"><td class="dataTableContent">'.zen_draw_input_field('nn_refund_confirm', html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT), 'class="btn btn-primary" onclick="return refund_amount_validation();" style="float:left"', false, 'submit').'</td><td></td></tr>';
        $outputRefund .= '</form>';
        $outputRefund .='</table></td>'."\n";
    }
}

// On-hold process
if (method_exists($this, '_doVoid') && method_exists($this, '_doCapt')) {
    if ($transaction_details->fields['status'] == 'ON_HOLD') {
        $options = array (
                array('id'=> '',        'text' => MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION),
                array('id'=> 'CONFIRM', 'text' => MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT),
                array('id'=> 'CANCEL',  'text' => MODULE_PAYMENT_NOVALNET_CANCEL_TEXT),
         );

        $outputVoidCapt .= '<td><table class="noprint">';
        $outputVoidCapt .= $nn_html;
        $outputVoidCapt .= '<td class="main"><label class="title">'.MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE . '</label></td><td></td></tr></br>'."\n";
        $outputVoidCapt .= zen_draw_form('novalnet_status_change', FILENAME_ORDERS, zen_get_all_get_params(array('action')), 'post', 'id="novalnet_status_change"', true) ;
        $outputVoidCapt .= $nn_script ;
        $outputVoidCapt .= $nn_html.'<td class="dataTableContent">';
        $outputVoidCapt .= '</br><label>' .MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $transaction_details->fields['tid'].'</label></td><td></td></tr></br>';
        $outputVoidCapt .= '<tr class="dataTableRow">';
        $outputVoidCapt .=  zen_draw_hidden_field('oID', $request['oID']);
        $outputVoidCapt .=  zen_draw_hidden_field('nn_capture_update', MODULE_PAYMENT_NOVALNET_PAYMENT_CAPTURE_CONFIRM);
        $outputVoidCapt .=  zen_draw_hidden_field('nn_void_update', MODULE_PAYMENT_NOVALNET_PAYMENT_VOID_CONFIRM);
        $outputVoidCapt .=  zen_draw_hidden_field('nn_select_status', MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT);
        $outputVoidCapt .= '<td class="dataTableContent" style="font-size: 12px;">'.MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT;
        $outputVoidCapt .= '<td class="dataTableContent">';
        $outputVoidCapt .= zen_draw_pull_down_menu('trans_status', $options, '', 'id="trans_status"').'</td></tr>';
        $outputVoidCapt .= '<tr class="dataTableRow"><td class="dataTableContent">'.zen_draw_input_field('nn_manage_confirm', html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT), 'class="btn btn-primary" onclick="return void_capture_status();" style="float:left"', false, 'submit').'</td><td></td></tr>';
        $outputVoidCapt .= '</form>';
        $outputVoidCapt .='</table></td>'."\n";
    }
}

// prepare output based on suitable content components
$output = '<!-- BOF: novalnet transaction processing tools -->';

if (defined('MODULE_PAYMENT_NOVALNET_STATUS') && MODULE_PAYMENT_NOVALNET_STATUS == 'True') {

    if (method_exists($this, '_doRefund')) {
        $output .= $outputRefund;
    }

    if (method_exists($this, '_doVoid') && method_exists($this, '_doCapt')) {
        $output .= $outputVoidCapt;
    }
}

$output .= '<!-- EOF: novalnet transaction processing tools -->';


/**
* Check whether refund block shown or not in admin panel
*
* @param array $txn_details
* 
* @return boolean
*/
function isRefund($txn_details) {
    if (($txn_details['amount'] > 0) &&
    (($txn_details['status'] == 'CONFIRMED' && ($txn_details['amount'] != $txn_details['refund_amount'])) ||
        ($txn_details['status']=='PENDING' &&
        ($txn_details['amount'] > $txn_details['refund_amount']) &&
        in_array($txn_details['payment_type'], array('INVOICE','PREPAYMENT','CASHPAYMENT')))
    ) &&
    !in_array($txn_details['payment_type'], array('MULTIBANCO','INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
        return true;

    } else {
        return false;
    }
}