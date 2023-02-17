/**
 * Novalnet payment module
 * This script is used for creating Novalnet custom database table
 *
 * @author     Novalnet AG
 * @copyright  Copyright (c) Novalnet
 * @license    https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 * @link       https://www.novalnet.de
 *
 * Script : db_13_0_0.sql
 *
 */
CREATE TABLE IF NOT EXISTS novalnet_transaction_detail (
  id int(10) unsigned AUTO_INCREMENT COMMENT 'Auto increment',
  order_no int(10) unsigned COMMENT 'Order no from shop',
  tid bigint(20) unsigned COMMENT 'Transaction id',
  amount int(10) unsigned COMMENT 'Amount', 
  currency char(10) COMMENT 'Currency', 
  payment_type varchar(40) COMMENT 'Payment type',
  status varchar(60) COMMENT 'Transaction status',
  payment_details text COMMENT 'Payment details of customer', 
  instalment_cycle_details text NULL COMMENT 'Instalment information used',
  refund_amount int(11) COMMENT 'Refund amount',
  callback_amount int(11) COMMENT 'Callback amount',
  PRIMARY KEY (id),
  KEY tid (tid),
  KEY payment_type (payment_type),
  KEY order_no (order_no)
) COMMENT='Novalnet transaction history';

