
CREATE TABLE IF NOT EXISTS novalnet_transaction_detail (
  id int(11) AUTO_INCREMENT COMMENT 'Auto Increment ID',
  tid bigint(20) unsigned COMMENT 'Novalnet Transaction Reference ID',
  order_no int(11) COMMENT 'Order number from shop',  
  payment_id int(11) unsigned COMMENT 'Payment ID',
  payment_type varchar(50) COMMENT 'Executed Payment type of this order',
  amount int(11) unsigned COMMENT 'Transaction amount',
  callback_amount int(11) unsigned COMMENT 'Callback amount',  
  gateway_status int(11) unsigned COMMENT 'Novalnet transaction status',
  instalment_details text COMMENT 'Stored instalment details',    
  `date` datetime COMMENT 'Transaction Date for reference',
  `language` varchar(10) COMMENT 'Shop language',  
  PRIMARY KEY (id),
  KEY tid (tid),
  KEY payment_type (payment_type),
  KEY order_no (order_no)
) COMMENT='Novalnet Transaction History';

ALTER TABLE orders_status_history MODIFY comments text;
