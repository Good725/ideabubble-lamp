/*
ts:2016-10-20 17:37:00
*/

UPDATE plugin_events_payment_gateways SET month_fee = NULL, percent_charge = 0.012, fixed_charge = 0.25 WHERE paymentgw = 'stripe' ;
