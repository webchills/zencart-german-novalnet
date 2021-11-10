<?php
/**
* This script is used for Creating Novalnet instalment summary table
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
* Script : novalnet_admin.php
*/

$novalnet_details = $db->Execute("select id, tid, language, instalment_details from novalnet_transaction_detail where order_no = '".zen_db_input($oID)."'");
     $instalment_details = unserialize($novalnet_details->fields['instalment_details']);
        if (!empty($instalment_details)) {
          include_once( DIR_FS_CATALOG . DIR_WS_INCLUDES .'languages/' . $instalment_details->fields['language'] . '/modules/payment/novalnet.php');
      ?>
      
      <tr>
        <td class="main noprint"><br /><strong><?php echo MODULE_PAYMENT_NOVALNET_INSTALMENT_SUMMARY; ?></strong></td>
      </tr>
      <tr>
        <td class="main"><table border="1" cellspacing="0" cellpadding="5">
          <tr>
            <td class="smallText" align="center"><strong><?php echo 'S.no'; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo MODULE_PAYMENT_NOVALNET_AMOUNT; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo MODULE_PAYMENT_NOVALNET_INSTALMENT_PAID_DATE; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo MODULE_PAYMENT_NOVALNET_INSTALMENT_DATE; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo MODULE_PAYMENT_NOVALNET_INSTALMENT_STATUS; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo MODULE_PAYMENT_NOVALNET_INSTALMENT_REFERENCE; ?></strong></td>
          </tr>
      <?php
        
        foreach($instalment_details as $key => $value) {
        echo '<tr><td class="smallText">' . $key . '</td>' . "\n";
        echo '<td class="smallText">' . $value['amount'] .' ' . $order->info['currency']. '</td>' . "\n";
        echo '<td class="smallText">' . zen_date_long($value['paidDate']) . '&nbsp;</td>' . "\n";
        echo '<td class="smallText">' . zen_date_long($value['nextCycle'])  . '&nbsp;</td>' . "\n";
        echo '<td class="smallText">' . $value['status'] . '</td>' . "\n";
        echo '<td class="smallText">' . $value['reference'] .'</td></tr>' . "\n";
        }        
    }  
?>
 </table></td></tr>
