/*
ts:2015-01-01 00:01:05
*/

UPDATE IGNORE `settings`
SET    `value_live`='Delivery Method', `value_stage`='Delivery Method', `value_test`='Delivery Method', `value_dev`='Delivery Method'
WHERE  `variable`='postal_destination_string';
