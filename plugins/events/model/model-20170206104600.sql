/*
ts:2017-02-06 10:46:00
*/

ALTER TABLE plugin_events_orders_payments ADD COLUMN paymentgw_info_2 TEXT;
update plugin_events_orders_payments p inner join plugin_events_orders o on p.order_id = o.id inner join plugin_events_accounts a on o.account_id = a.id
	set p.paymentgw_info_2 = a.stripe_auth
	where a.use_stripe_connect = 1;
