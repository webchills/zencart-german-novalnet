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
 *
 */
require_once(DIR_FS_CATALOG . 'includes/modules/payment/novalnet/NovalnetHelper.class.php');
include_once(DIR_FS_CATALOG ."includes/languages/". $_SESSION['language']."/modules/payment/novalnet_payments.php");
	  $outputStartBlock = '';
	  $outputEndBlock = '';
	  $output = '';

// Prepare output based on suitable content components
if (defined('MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD') && MODULE_PAYMENT_NOVALNET_ENABLE_PAYMENT_METHOD == 'True') {
	$output = '<!-- BOF: aim admin transaction processing tools -->';
	$output .= $outputStartBlock;
	global $db, $currencies;
	$request = $_REQUEST;
	$transaction_details = NovalnetHelper::getNovalnetTransDetails($order_id);
	if ($transaction_details->RecordCount()) {
		if(empty($transaction_details)) {
			return false;
		}
		// Refund process
		if (($transaction_details->fields['amount'] > 0)
		&& (($transaction_details->fields['status'] == 'CONFIRMED' && ($transaction_details->fields['amount'] != $transaction_details->fields['refund_amount']))
		|| ($transaction_details->fields['status']=='PENDING'  && ($transaction_details->fields['amount'] > $transaction_details->fields['refund_amount']) && in_array($transaction_details->fields['payment_type'], array('INVOICE','PREPAYMENT','CASHPAYMENT'))))
		&& !in_array($transaction_details->fields['payment_type'],array('MULTIBANCO','INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
			$avail_refund = 0; $refund_value = 0;
			$output .= '<td><table class="noprint">';
			$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;">';
			$output .= '<td><label class="title">'.MODULE_PAYMENT_NOVALNET_REFUND_TITLE . '</label></td><td></td></tr></br>'."\n";
			$output .= zen_draw_form('novalnet_trans_refund', 'novalnet_extension_helper.php', 'oID='.$request['oID'].'&action=refund') ;
			$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;"><td class="dataTableContent">';
			$output .= '</br><label>' .MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $transaction_details->fields['tid'].'</label></td><td></td></tr>';
			$output .= '<tr class="dataTableRow">';
			$output .= '<td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_REFUND_AMT_TITLE . '</td>';
			$output .=  zen_draw_hidden_field('oID', $request['oID']);
			$output .=  zen_draw_hidden_field('nn_refund_amount', MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM);
			$avail_refund = (!empty($transaction_details->fields['callback_amount'])) ? (int)$transaction_details->fields['callback_amount'] : (int)$transaction_details->fields['amount'];
			$refund_value = (!empty($transaction_details->fields['refund_amount'])) ? ((int)$avail_refund - (int)$transaction_details->fields['refund_amount']) : $avail_refund;
			$output .= '<td class="dataTableContent">';
			$output .= zen_draw_input_field('refund_trans_amount',$refund_value,'id="refund_trans_amount"  style="width:100px;margin:0 0 0 2%" autocomplete="off"') . MODULE_PAYMENT_NOVALNET_AMOUNT_EX . '</td></tr>';
			$output .= '<td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_REFUND_REASON_TITLE . '</td>';
			$output .= '<td class="dataTableContent">' . zen_draw_input_field('refund_reason' ,'' ,'id="refund_reason" style="margin:0 0 0 2%;" autocomplete="off"').'</td>';
			$output .= '<tr class="dataTableRow"><td class="dataTableContent">'.zen_draw_input_field('nn_refund_confirm',html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT),'class="btn btn-primary" onclick="return refund_amount_validation();" style="float:left"',false,'submit').'</td><td></td></tr>';
			$output .= '</form>';
			$output .='</table></td>'."\n";
		}
		// Zero amount booking process
		$order_total = $db->Execute("SELECT value FROM " . TABLE_ORDERS_TOTAL . " where class = 'ot_total' AND orders_id = " . zen_db_input($order_id));
		if ($transaction_details->fields['amount'] == 0 && in_array($transaction_details->fields['payment_type'],array('CREDITCARD','DIRECT_DEBIT_SEPA'))
		&& $transaction_details->fields['status'] == 'CONFIRMED') {
			$amount = 0;
			$output .= '<td><table class="noprint">';
			$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;">';
			$output .= '<td><label class="title">'.MODULE_PAYMENT_NOVALNET_BOOK_TITLE . '</label></td><td></td></tr></br>'."\n";
			$output .= zen_draw_form('novalnet_book_amount', 'novalnet_extension_helper.php');
			$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;"><td class="dataTableContent">';
			$output .= '</br><label>' .MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $transaction_details->fields['tid'].'</label></td><td></td></tr>';
			$output .= '<tr class="dataTableRow">';
			$output .= '<td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_BOOK_AMT_TITLE . '</td>';
			$output .=  zen_draw_hidden_field('oID', $request['oID']);
			$output .=  zen_draw_hidden_field('nn_refund_amount', MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM);
			$output .= '<td class="dataTableContent">';
			$amount = round($order_total->fields['value'] ,2)*100;
			$output .= zen_draw_input_field('book_amount',$amount,'id="book_amount" autocomplete="off" style="margin:0% 0% 0% 2%"') . MODULE_PAYMENT_NOVALNET_AMOUNT_EX . '</td></tr>';
			$output .= '<tr class="dataTableRow"><td class="dataTableContent">'.zen_draw_input_field('nn_book_confirm',html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT),'class="btn btn-primary" onclick="return refund_amount_validation();" style="float:left"',false,'submit').'</td><td></td></tr>';
			$output .= '</form>';
			$output .='</table></td>'."\n";
		}
		// On-hold process
		if($transaction_details->fields['status'] == 'ON_HOLD') {
			$options = [];
			$output .= '<td><table class="noprint">';
			$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;">';
			$output .= '<td class="main"><label class="title">'.MODULE_PAYMENT_NOVALNET_TRANS_CONFIRM_TITLE . '</label></td><td></td></tr></br>'."\n";
			$output .= zen_draw_form('novalnet_status_change', 'novalnet_extension_helper.php', 'oID='.$request['oID'].'&action=edit');
			$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;"><td class="dataTableContent">';
			$output .= '</br><label>' .MODULE_PAYMENT_NOVALNET_TRANSACTION_ID . $transaction_details->fields['tid'].'</label></td><td></td></tr></br>';
			$output .= '<tr class="dataTableRow">';
			$output .=  zen_draw_hidden_field('oID', $request['oID']);
			$output .=  zen_draw_hidden_field('nn_capture_update', MODULE_PAYMENT_NOVALNET_PAYMENT_CAPTURE_CONFIRM);
			$output .=  zen_draw_hidden_field('nn_void_update', MODULE_PAYMENT_NOVALNET_PAYMENT_VOID_CONFIRM);
			$output .= '<td class="dataTableContent" style="font-size: 12px;">'.MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT;
			$output .= '<td class="dataTableContent">';
			$options = array (
					array('id'=> '', 		'text' => MODULE_PAYMENT_NOVALNET_SELECT_STATUS_OPTION),
					array('id'=> 'CONFIRM', 'text' => MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT),
					array('id'=> 'CANCEL', 	'text' => MODULE_PAYMENT_NOVALNET_CANCEL_TEXT),
			 );
			$output .= zen_draw_pull_down_menu('trans_status', $options, '', 'onclick="return remove_void_capture_error_message()"').'</td></tr>';
			$output .= '<tr class="dataTableRow"><td class="dataTableContent">'.zen_draw_input_field('nn_manage_confirm',html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT), 'class="btn btn-primary" onclick="return void_capture_status();" style="float:left"',false,'submit').'</td><td></td></tr>';
			$output .= '</form>';
			$output .='</table></td>'."\n";
		}
		// Instalment refund and cancel process
		if (in_array($transaction_details->fields['payment_type'],array('INSTALMENT_INVOICE','INSTALMENT_DIRECT_DEBIT_SEPA'))) {
		$instalment_details = json_decode($transaction_details->fields['instalment_cycle_details'], true) ;
			if(!empty($instalment_details)) {
				$output .= '
					<script>
						function myFunction(cycle) {
						  var refund_id = document.getElementById("instalment_refund_"+cycle);
						  if (refund_id.style.display === "none") {
							refund_id.style.display = "block";
						  } else {
							refund_id.style.display = "none";
						  }
						}
					</script>';
				$output .= '<td><table class="noprint table">';
				$output .= '<tr class="dataTableHeadingRow" style="background-color : #dddddd;">';
				$output .= '<td class="main" colspan="6"><label style="font-size: 12px;">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY_BACKEND . '</label></td><td></td></tr></br>'."\n";
				$output .= '<tr class="dataTableHeadingRow"><td class="dataTableContent" colspan="6">';
				$instalment_status = [];
				foreach($instalment_details as $key => $instalment_details_data){
					array_push($instalment_status,$instalment_details_data['status']);
				}

				$nn_instalment_canceled = false;
				$nn_instacancel_remaining = 'style="display:block"';
				$nn_instacancel_allcycles = 'style="display:block"';
				if (in_array('Canceled', $instalment_status)) {
					$nn_instalment_canceled = true;
				} else if (in_array('Refunded', $instalment_status)) {
					$nn_instacancel_remaining = 'style="display:block"';
					$nn_instacancel_allcycles = 'style="display:none"';
				}

				if ($nn_instalment_canceled == false) {
					$output .= '<button id="nn_instalment_cancel" class="btn btn-primary" style="display: block;">' . MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ADMIN_TEXT . '</button>';
				}
				$output .=  zen_draw_form('nn_instalment_cancel', 'novalnet_extension_helper.php');
				$output .= '<div id= novalnet_instalment_cancel style="display: none;">' ;
				$output .= zen_draw_hidden_field('oID', $request['oID']);
				$output .= zen_draw_input_field('nn_instacancel_remaincycles',html_entity_decode(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_REMAINING_CYCLES), 'id="nn_instacancel_remaincycles" class="btn btn-primary" '.$nn_instacancel_remaining,false,'submit')."&nbsp;";
				$output .= zen_draw_input_field('nn_instacancel_allcycles',html_entity_decode(MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_ALLCYCLES), 'id="nn_instacancel_allcycles" class="btn btn-primary" '.$nn_instacancel_allcycles,false,'submit');
				$output .= '</div></form></td><td></td></tr>';
				$output .= '<tr class="dataTableHeadingRow"><td class="dataTableContent">S.No</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_AMOUNT_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_PAID_DATE_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_NEXT_DATE_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_BACKEND.'</td><td class="dataTableContent">'.MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE_BACKEND.'</td><td></td></tr>';
				$nn_instalment_table = '';
				$instalment_amount = 0;
				$status = [];
				$sno = 1;
				foreach ($instalment_details as $key => $instalment_details_data) {
						$instalment_amount = (strpos((string)$instalment_details_data['instalment_cycle_amount'], '.')) ? $instalment_details_data['instalment_cycle_amount']*100 : $instalment_details_data['instalment_cycle_amount'];
						if (!empty($instalment_details_data['status'])) {
							$status = $instalment_details_data['status'];
						} else {
							$status = (empty($instalment_details_data['reference_tid'])) ? 'Pending' : (($instalment_amount > 0) ? 'Paid' : 'Refunded');
						}
						$status = constant('MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS_' .  strtoupper($status));
						$href = (isset($instalment_details_data['reference_tid']) && $instalment_details_data['reference_tid'] != '' && $instalment_amount != '0' && $instalment_amount > 0) ? "&nbsp;<button id='nn_refund1' class='btn btn-primary' onclick='myFunction($key)'>" . MODULE_PAYMENT_NOVALNET_REFUND_TEXT . "</button>" : '';
						$instalment_amount_formatted = !empty($instalment_amount) ? $currencies->format($instalment_amount/100, 1, $transaction_details->fields['currency']) : '-';
						$nn_instalment_table .= "<tr class='dataTableRow'><td class='dataTableContent'>".$sno++."</td><td class='dataTableContent'>".$instalment_amount_formatted.' '.$href."</td>
						<td class='dataTableContent'>".(isset($instalment_details_data['paid_date']) ? $instalment_details_data['paid_date'] : '')."</td><td class='dataTableContent'>".(isset($instalment_details_data['next_instalment_date']) ? $instalment_details_data['next_instalment_date'] : '')."</td><td class='dataTableContent'>$status</td><td class='dataTableContent'>".(isset($instalment_details_data['reference_tid']) ? $instalment_details_data['reference_tid'] : '')."</td>";
						$nn_instalment_table .= '<td class="dataTableContent">'.zen_draw_form('nn_refund_confirm', 'novalnet_extension_helper.php');
						$nn_instalment_table .= '<div id= instalment_refund_'.$key.' style="display: none;">' ;
						$nn_instalment_table .= zen_draw_hidden_field('oID', $request['oID']);
						$nn_instalment_table .= zen_draw_hidden_field('refund_tid', (isset($instalment_details_data['reference_tid']) ? $instalment_details_data['reference_tid'] : ''));
						$nn_instalment_table .= zen_draw_hidden_field('instalment_cycle', ''.$key.'');
						$nn_instalment_table .= zen_draw_input_field('refund_trans_amount',$instalment_amount,'id="refund_trans_amount"  style="width:100px;margin:0 0 0 2%" autocomplete="off"')."&nbsp;";
						$nn_instalment_table .= zen_draw_input_field('nn_refund_confirm',html_entity_decode(MODULE_PAYMENT_NOVALNET_CONFIRM_TEXT), 'class="btn btn-primary" onclick="return refund_amount_validation();" ',false,'submit')."&nbsp;" ;
						$nn_instalment_table .= "<a class='btn btn-primary' href='" . zen_href_link(FILENAME_ORDERS, 'oID=' . $request['oID'] . '&action=edit') . "'>" . MODULE_PAYMENT_NOVALNET_INSTALMENT_CANCEL_TEXT . "</a>";
						$nn_instalment_table .= '</div></form></td></tr>';
				}
				$nn_instalment_table .='</table></td>'."\n";
				$output .= $nn_instalment_table;
			}
		}
	}
	$output .= $outputEndBlock;
	$output .= '<!-- EOF: aim admin transaction processing tools -->';
}
echo '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'includes/modules/payment/novalnet/novalnet_extension.js"></script>';
echo zen_draw_hidden_field('nn_refund_amount_confirm', MODULE_PAYMENT_NOVALNET_PAYMENT_REFUND_CONFIRM);
echo zen_draw_hidden_field('nn_select_status', MODULE_PAYMENT_NOVALNET_SELECT_STATUS_TEXT);
echo zen_draw_hidden_field('nn_zero_amount_book_confirm', MODULE_PAYMENT_NOVALNET_PAYMENT_ZERO_AMOUNT_BOOK_CONFIRM);
echo zen_draw_hidden_field('nn_amount_error', MODULE_PAYMENT_NOVALNET_AMOUNT_ERROR_MESSAGE);
echo zen_draw_hidden_field('nn_insta_allcycles', MODULE_PAYMENT_NOVALNET_ALLCYCLES_ERROR_MESSAGE);
echo zen_draw_hidden_field('nn_insta_remainingcycles', MODULE_PAYMENT_NOVALNET_REMAINING_CYCLES_ERROR_MESSAGE);
