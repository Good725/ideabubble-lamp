/*
ts:2015-01-01 00:01:06
*/

-- ----------------------------------------------------
-- MTEE-68 - Product details label incorrect showing back end reference
-- ----------------------------------------------------
UPDATE IGNORE `plugin_products_option` SET `group_label`='size' WHERE `group`='size(baby)';
UPDATE IGNORE `plugin_products_option` SET `group_label`='size' WHERE `group`='size(kids)';
UPDATE IGNORE `plugin_products_option` SET `group_label`='colour' WHERE `group`='color(kma)';
UPDATE IGNORE `plugin_products_matrices` SET `option_1`='color (KMA)' WHERE `option_1`='color(KMA)';
UPDATE IGNORE `plugin_products_matrices` SET `option_2`='color (KMA)' WHERE `option_2`='color(KMA)';

UPDATE IGNORE `settings`
SET    `value_live`='Delivery Method', `value_stage`='Delivery Method', `value_test`='Delivery Method', `value_dev`='Delivery Method'
WHERE  `variable`='postal_destination_string';
