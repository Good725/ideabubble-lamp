/*
ts:2016-04-10 18:15:00
*/

UPDATE `plugin_reports_reports`
  SET `sql` = 'SELECT \npurchase_time as `Date`,\ncustomer_name AS `Name`,\ncustomer_telephone as `Phone`,\ncustomer_email as `Checkout Email`,\nusers.id as `User Id`,\nusers.email as `User Email`,\ngroup_concat(case when t2.cart_id = plugin_payments_log.cart_id then t2.title end SEPARATOR \',\') AS `Products`,\n	(\n		CASE\n		WHEN paid = 1 THEN\n			\'Yes\'\n		ELSE\n			IF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'Credit\', \'No\')\n		END\n	) AS `Paid`,\n	payment_amount as `Total`,\nIF(users.credit_account = 1 and plugin_payments_log.payment_type = \'\', \'credit\', plugin_payments_log.payment_type) as `Payment Method`,\n`plugin_payments_log`.`delivery_method` as `Delivery Method`,\nCONCAT_WS(\' \', store.title, store.county) as `Store`\nFROM\n	plugin_payments_log\nLEFT JOIN engine_users users ON plugin_payments_log.customer_user_id = users.id\nLEFT JOIN plugin_products_cart_items AS t2 ON t2.cart_id = plugin_payments_log.cart_id\nLEFT JOIN plugin_locations_location AS store ON `plugin_payments_log`.`store_id` = `store`.`id` \nGROUP BY plugin_payments_log.cart_id\nORDER BY `purchase_time` DESC'
  WHERE `name` = 'Orders';
