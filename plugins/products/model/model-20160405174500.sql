/*
ts:2016-04-05 17:45:00
*/

CREATE TABLE IF NOT EXISTS `plugin_products_reviews` (
  `id`            INT          NOT NULL AUTO_INCREMENT ,
  `title`         VARCHAR(127) NULL ,
  `rating`        INT(1)       NULL ,
  `review`        TEXT         NULL ,
  `author`        VARCHAR(127) NOT NULL ,
  `email`         VARCHAR(127) NULL ,
  `ip_address`    VARCHAR(15)  NULL ,
  `product_id`    INT(11)      NOT NULL ,
  `created_by`    INT(11)      NULL ,
  `modified_by`   INT(11)      NULL ,
  `date_created`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP    NULL ,
  `publish`       INT(1)       NOT NULL DEFAULT 0 ,
  `deleted`       INT(1)       NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
);

INSERT IGNORE INTO `settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES
('enable_customer_reviews', 'Customer Reviews', 'products', '0', '0', '0', '0', '1', 'Allow customers to post reviews on products', 'toggle_button', 'Products', 'Model_Settings,on_or_off');
