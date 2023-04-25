<?php
/**
 * Novalnet payment module
 * admin component by webchills (www.webchills.at)
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : novalnet_transactions.php
 */

  require('includes/application_top.php');
  
  $novalnet_sort_order_array = [
    ['id' => '0', 'text' => TEXT_SORT_NOVALNET_ID_DESC],
    ['id' => '1', 'text' => TEXT_SORT_NOVALNET_ID],
    ['id' => '2', 'text' => TEXT_SORT_ZEN_ORDER_ID_DESC],
    ['id' => '3', 'text' => TEXT_SORT_ZEN_ORDER_ID],
    ['id' => '4', 'text' => TEXT_SORT_NOVALNET_PAYMENT_TYPE_DESC],
    ['id' => '5', 'text' => TEXT_SORT_NOVALNET_PAYMENT_TYPE],
    ['id' => '6', 'text' => TEXT_SORT_NOVALNET_STATUS_DESC],
    ['id' => '7', 'text' => TEXT_SORT_NOVALNET_STATUS]
  ];

  $novalnet_sort_order = 0;
  if (isset($_GET['novalnet_sort_order'])) {
    $novalnet_sort_order = (int) $_GET['novalnet_sort_order'];
  }

  switch ($novalnet_sort_order) {
    case (0):
      $order_by = " order by n.id DESC";
      break;
    case (1):
      $order_by = " order by n.order_no";
      break;
    case (2):
      $order_by = " order by n.order_no DESC, n.id";
      break;
    case (3):
      $order_by = " order by n.order_no, n.id";
      break;
    case (4):
      $order_by = " order by n.payment_type DESC";
      break;
    case (5):
      $order_by = " order by n.payment_type";
      break;
      case (6):
      $order_by = " order by n.status";
      break;
      case (7):
      $order_by = " order by n.status DESC";
      break;
      
    default:
      $order_by = " order by n.id DESC";
      break;
    }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $selected_status = (isset($_GET['novalnet_status']) ? $_GET['novalnet_status'] : '');    
  $novalnet_statuses = [];
  $novalnet_statuses[0] = array('id' => 'ON_HOLD', 'text' => 'ON_HOLD'); 
  $novalnet_statuses[1] = array('id' => 'CONFIRMED', 'text' => 'CONFIRMED');
  $novalnet_statuses[2]= array('id' => 'PENDING', 'text' => 'PENDING');  
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
<head>
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
    <style>
.supportinfo {
font-size: 13px;
margin: 0 0 20px 0;
padding:0
} 
.supportinfo a {
font-size: 13px;

}  
#btlogo{
width:172px;
height:40px;
float:right;
margin:5px 5px 5px 40px;
} 
#btsorter{
width:500px;
height:50px;
float:right;
margin:-50px 5px 0px 10px;
} 
</style>

</head>
<body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <div class="container-fluid">
    <h1><?php echo HEADING_ADMIN_TITLE; ?></h1>
    <!-- only show if the Novalnet module is installed //-->
<?php  if (defined('MODULE_PAYMENT_NOVALNET_STATUS')) { ?>
<span id="btsorter"><?php
  $hidden_field = (isset($_GET['novalnet_sort_order'])) ? zen_draw_hidden_field('novalnet_sort_order', $_GET['novalnet_sort_order']) : '';
  echo zen_draw_form('novalnet_status', FILENAME_NOVALNET_TRANSACTIONS, '', 'get') . HEADING_NOVALNET_STATUS . ' ' . zen_draw_pull_down_menu('novalnet_status', array_merge([['id' => '', 'text' => TEXT_ALL_IPNS]], $novalnet_statuses), $selected_status, 'onchange="this.form.submit();"') . zen_hide_session_id() . $hidden_field . '</form>';
  $hidden_field = (isset($_GET['novalnet_status'])) ? zen_draw_hidden_field('novalnet_status', $_GET['novalnet_status']) : '';
  echo '&nbsp;&nbsp;&nbsp;' . TEXT_NOVALNET_SORT_ORDER_INFO . zen_draw_form('novalnet_sort_order', FILENAME_NOVALNET_TRANSACTIONS, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu('novalnet_sort_order', $novalnet_sort_order_array, $novalnet_sort_order, 'onChange="this.form.submit();"') . zen_hide_session_id() . $hidden_field . '</form>';
?></span>
    <span class="supportinfo"><?php echo AMOUNT_INFO; ?> | <a href="https://admin.novalnet.de" target="_blank">Novalnet Admin Portal</a></span>

       <div class="row">
           <div class="col-sm-12 col-md-9 configurationColumnLeft">
              <table class="table">
              <tr class="dataTableHeadingRow">
              	<td class="dataTableHeadingContent">ID</td>
              	<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDER_NUMBER; ?></td>              	
                <td class="dataTableHeadingContent"><?php echo NOVALNET_TRANSACTION_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_AMOUNT; ?></td> 
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY; ?></td>                 
                <td class="dataTableHeadingContent"><?php echo NOVALNET_PAYMENT_TYPE; ?></td>     
                <td class="dataTableHeadingContent" text-right><?php echo NOVALNET_STATUS; ?></td>
                <td class="dataTableHeadingContent" text-right><?php echo NOVALNET_REFUND_AMOUNT; ?></td>
                <td class="dataTableHeadingContent" text-right><?php echo NOVALNET_CALLBACK_AMOUNT; ?></td>
                       
                       
                              
              </tr>
<?php
  if (zen_not_null($selected_status)) {
    $novalnet_search = "AND n.status  = :selectedStatus: ";
    $novalnet_search = $db->bindVars($novalnet_search, ':selectedStatus:', $selected_status, 'string');
    switch ($selected_status) {
      case 'ON_HOLD':
      case 'CONFIRMED':        
      case 'PENDING': 
      default:
        $novalnet_query_raw = "SELECT * from `".TABLE_NOVALNET_TRANSACTION_DETAIL."` as n, " .TABLE_ORDERS . " as o  where o.orders_id = n.order_no " . $novalnet_search . $order_by;
        break;
   } 
  } else {
        $novalnet_query_raw = "SELECT * from `".TABLE_NOVALNET_TRANSACTION_DETAIL."` as n left join " .TABLE_ORDERS . " as o on o.orders_id = n.order_no " . $order_by;

  }

  $novalnet_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_NOVALNET_IPN, $novalnet_query_raw, $novalnet_query_numrows);
  $novalnet_response = $db->Execute($novalnet_query_raw);
  foreach ($novalnet_response as $novalnet_tran) {
    if ((!isset($_GET['novalnetId']) || (isset($_GET['novalnetId']) && ($_GET['novalnetId'] == $novalnet_response->fields['id']))) && !isset($novalnetInfo) ) {
      $novalnetInfo = new objectInfo($novalnet_tran); 
    }   
    
      echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_NOVALNET_TRANSACTIONS, 'page=' . $_GET['page'] . '&novalnetId=' . $novalnet_tran['id'] . (zen_not_null($selected_status) ? '&status=' . $selected_status : '') . (zen_not_null($novalnet_sort_order) ? '&novalnet_sort_order=' . $novalnet_sort_order : '') ) . '\'">' . "\n";
   
?>
                <td class="dataTableContent"> <?php echo $novalnet_tran['id']; ?> </td>
                <td class="dataTableContent"> <?php echo $novalnet_tran['order_no']; ?> </td>
                <td class="dataTableContent"> <?php echo $novalnet_tran['tid']; ?> </td>
		            <td class="dataTableContent"> <?php echo $novalnet_tran['amount']; ?> </td>
                <td class="dataTableContent"> <?php echo $novalnet_tran['currency']; ?> </td>
                <td class="dataTableContent"> <?php echo $novalnet_tran['payment_type']; ?> </td>
                <td class="dataTableContent"> <?php echo $novalnet_tran['status']; ?>
                <td class="dataTableContent"> <?php echo $novalnet_tran['refund_amount']; ?>
                <td class="dataTableContent"> <?php echo $novalnet_tran['callback_amount']; ?>  
                	
              <?php echo '</tr>';
  }
?>
              <tr>
                    <td colspan="3" class="smallText"><?php echo $novalnet_split->display_count($novalnet_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_NOVALNET_IPN, $_GET['page'], "Zeige <strong>%d</strong> bis <strong>%d</strong> (von <strong>%d</strong> Transaktionen)"); ?></td>
                    <td colspan="3" class="smallText"><?php echo $novalnet_split->display_links($novalnet_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_NOVALNET_IPN, MAX_DISPLAY_PAGE_LINKS, isset($_GET['page']) ? (int)$_GET['page'] : 1, zen_get_all_get_params(['page'])); ?></td>
                  </tr>
                </table>
           </div>
<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      break;
    case 'edit':
      break;
    case 'delete':
      break;
    default:
      
      if (isset($novalnetInfo) && is_object($novalnetInfo)) {
        $heading[] = ['text' => '<strong>' . 'Novalnet'.' #' . $novalnetInfo->id . '</strong>'];
        $novalnet = $db->Execute("SELECT * FROM " . TABLE_NOVALNET_TRANSACTION_DETAIL . " WHERE id = '" . $novalnetInfo->id . "'");
        $novalnet_count = $novalnet->RecordCount();  
	  

      switch ($novalnet->fields['status']){
      	case 'ON_HOLD':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($novalnetInfo->order_no);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_NOVALNET_RESPONSE_BEGIN.'#'.$novalnetInfo->id.' '.TEXT_INFO_NOVALNET_RESPONSE_END.'#'. $novalnetInfo->order_no . '</strong>');
        $contents[] = array('text' =>  '' . NOVALNET_REFERENCE_ID .'' . ': '.$novalnetInfo->tid);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$novalnetInfo->order_no);
        $contents[] = array('text' =>  '' . NOVALNET_TRANSACTION_ID .'' . ': '.$novalnetInfo->tid);
        $contents[] = array('text' =>  '' . TABLE_HEADING_AMOUNT .'' . ': '.$novalnetInfo->amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CURRENCY .'' . ': '.$novalnetInfo->currency);
        $contents[] = array('text' =>  '' . NOVALNET_PAYMENT_TYPE .'' . ': '.$novalnetInfo->payment_type);
        $contents[] = array('text' =>  '' . NOVALNET_REFUND_AMOUNT .'' . ': '.$novalnetInfo->refund_amount);   
        $contents[] = array('text' =>  '' . NOVALNET_CALLBACK_AMOUNT .'' . ': '.$novalnetInfo->callback_amount);              
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('novalnetId', 'action')) . 'oID=' . $novalnetInfo->order_no .'&' . 'novalnetID=' . $novalnetInfo->id .'&action=edit' . '&referer=novalnet') . '">' . NOVALNET_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		      	case 'CONFIRMED':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($novalnetInfo->order_no);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_NOVALNET_RESPONSE_BEGIN.'#'.$novalnetInfo->id.' '.TEXT_INFO_NOVALNET_RESPONSE_END.'#'. $novalnetInfo->order_no . '</strong>');
        $contents[] = array('text' =>  '' . NOVALNET_REFERENCE_ID .'' . ': '.$novalnetInfo->tid);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$novalnetInfo->order_no);
        $contents[] = array('text' =>  '' . NOVALNET_TRANSACTION_ID .'' . ': '.$novalnetInfo->tid);
        $contents[] = array('text' =>  '' . TABLE_HEADING_AMOUNT .'' . ': '.$novalnetInfo->amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CURRENCY .'' . ': '.$novalnetInfo->currency);
        $contents[] = array('text' =>  '' . NOVALNET_PAYMENT_TYPE .'' . ': '.$novalnetInfo->payment_type);  
        $contents[] = array('text' =>  '' . NOVALNET_REFUND_AMOUNT .'' . ': '.$novalnetInfo->refund_amount);   
        $contents[] = array('text' =>  '' . NOVALNET_CALLBACK_AMOUNT .'' . ': '.$novalnetInfo->callback_amount);  
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('novalnetId', 'action')) . 'oID=' . $novalnetInfo->order_no .'&' . 'novalnetID=' . $novalnetInfo->id .'&action=edit' . '&referer=novalnet') . '">' . NOVALNET_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		
		case 'PENDING':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($novalnetInfo->order_no);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_NOVALNET_RESPONSE_BEGIN.'#'.$novalnetInfo->id.' '.TEXT_INFO_NOVALNET_RESPONSE_END.'#'. $novalnetInfo->order_no . '</strong>');
        $contents[] = array('text' =>  '' . NOVALNET_REFERENCE_ID .'' . ': '.$novalnetInfo->tid);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$novalnetInfo->order_no);
        $contents[] = array('text' =>  '' . NOVALNET_TRANSACTION_ID .'' . ': '.$novalnetInfo->tid);
        $contents[] = array('text' =>  '' . TABLE_HEADING_AMOUNT .'' . ': '.$novalnetInfo->amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CURRENCY .'' . ': '.$novalnetInfo->currency);
        $contents[] = array('text' =>  '' . NOVALNET_PAYMENT_TYPE .'' . ': '.$novalnetInfo->payment_type);  
        $contents[] = array('text' =>  '' . NOVALNET_REFUND_AMOUNT .'' . ': '.$novalnetInfo->refund_amount);   
        $contents[] = array('text' =>  '' . NOVALNET_CALLBACK_AMOUNT .'' . ': '.$novalnetInfo->callback_amount); 
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('novalnetId', 'action')) . 'oID=' . $novalnetInfo->order_no .'&' . 'novalnetID=' . $novalnetInfo->id .'&action=edit' . '&referer=novalnet') . '">' . NOVALNET_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		

		default:
        $heading[] = array('text' => '');
        $contents[] = array('text'=> '' );
        }
      }
      break;
  }
  if (!empty($heading) && !empty($contents)) {
    $box = new box();
      echo '<div class="col-sm-12 col-md-3 configurationColumnRight">';
    echo $box->infoBox($heading, $contents);
      echo '</div>';
  }
?>
       </div>
<?php } ?>
</div>
<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
</body>
</html>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>