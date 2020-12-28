/*
ts:2016-04-18 12:03:00
*/

CREATE TABLE `plugin_products_option_groups`
(
  `id`  INT AUTO_INCREMENT PRIMARY KEY,
  `group_label` VARCHAR(100),
  `group` VARCHAR(100),
  `deleted` TINYINT(1) DEFAULT 0
)
ENGINE=InnoDB
CHARSET=UTF8;

INSERT INTO `plugin_products_option_groups`
(`group`, `group_label`)
  (SELECT DISTINCT `group`, IF(`group_label` = '' OR `group_label` IS NULL, `group`, `group_label`) FROM plugin_products_option);

ALTER TABLE `plugin_products_option` ADD COLUMN `group_id` INT;
ALTER TABLE `plugin_products_product_options` ADD COLUMN `option_group_id` INT;
ALTER TABLE `plugin_products_matrices` ADD COLUMN `option_1_id` INT;
ALTER TABLE `plugin_products_matrices` ADD COLUMN `option_2_id` INT;

UPDATE plugin_products_option
  INNER JOIN plugin_products_option_groups
    ON plugin_products_option.`group` = plugin_products_option_groups.`group`
  SET plugin_products_option.group_id = plugin_products_option_groups.id;

UPDATE plugin_products_product_options
  INNER JOIN plugin_products_option_groups
    ON plugin_products_product_options.`option_group` = plugin_products_option_groups.`group`
  SET plugin_products_product_options.option_group_id = plugin_products_option_groups.id;

UPDATE plugin_products_matrices
  INNER JOIN plugin_products_option_groups
    ON plugin_products_matrices.`option_1` = plugin_products_option_groups.`group`
  SET plugin_products_matrices.option_1_id = plugin_products_option_groups.id;

UPDATE plugin_products_matrices
  INNER JOIN plugin_products_option_groups
    ON plugin_products_matrices.`option_2` = plugin_products_option_groups.`group`
  SET plugin_products_matrices.option_2_id = plugin_products_option_groups.id;


ALTER TABLE `plugin_products_product_options` DROP FOREIGN KEY `product_options_fk_2`;
ALTER TABLE `plugin_products_product_options` DROP INDEX `product_options_idx_2`;
ALTER TABLE `plugin_products_product_options` DROP COLUMN `option_group`;

ALTER TABLE `plugin_products_option` DROP COLUMN `group`;
ALTER TABLE `plugin_products_option` DROP COLUMN `group_label`;

