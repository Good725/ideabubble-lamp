/*
ts:2015-01-01 00:01:09
*/

UPDATE IGNORE `settings`
SET    `value_live`='Delivery Information', `value_stage`='Delivery Information', `value_test`='Delivery Information', `value_dev`='Delivery Information'
WHERE  `variable`='shipping_information_string';

UPDATE IGNORE `settings`
SET    `value_live`='Delivery', `value_stage`='Delivery', `value_test`='Delivery', `value_dev`='Delivery'
WHERE  `variable`='postal_destination_string';
