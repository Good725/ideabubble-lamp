/*
ts:2019-07-08 10:03:00
*/

insert into plugin_inventory_items
	(title, amount_type)
	(select distinct product, amount_type from plugin_purchasing_purchases_has_items);

ALTER TABLE `plugin_purchasing_purchases_has_items` ADD COLUMN `inventory_item_id`  int NULL AFTER `purchase_id`;

UPDATE plugin_purchasing_purchases_has_items
	INNER JOIN plugin_inventory_items ON plugin_purchasing_purchases_has_items.product = plugin_inventory_items.title
	SET plugin_purchasing_purchases_has_items.inventory_item_id = plugin_inventory_items.id;

ALTER TABLE `plugin_purchasing_purchases_has_items` DROP COLUMN `product`;

