/*
ts:2016-10-20 12:00:00
*/

ALTER TABLE plugin_events_payment_gateways ADD COLUMN month_fee DECIMAL(10, 3);
ALTER TABLE plugin_events_payment_gateways MODIFY COLUMN percent_charge DECIMAL(10, 3);
ALTER TABLE plugin_events_payment_gateways MODIFY COLUMN fixed_charge DECIMAL(10, 3);
ALTER TABLE plugin_events_orders_payments ADD COLUMN paymentgw_fee DECIMAL(10, 3);

/*refer to https://stripe.com/us/pricing for stripe fees, %2.9 + 0.30 per charge*/
UPDATE plugin_events_payment_gateways SET month_fee = NULL, percent_charge = 0.029, fixed_charge = 0.3 WHERE paymentgw = 'stripe' ;

/*refer to https://www.realexpayments.com/online-payments/ for realex fees; 29 per month, 350 tx; 0.12 for extra tx*/
UPDATE plugin_events_payment_gateways SET month_fee = 29, percent_charge = 0.0, fixed_charge = 0.12 WHERE paymentgw = 'realex';
