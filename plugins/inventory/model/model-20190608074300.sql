/*
ts:2019-06-08 07:43:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `svg`) VALUES ('inventory', 'Inventory', '1', '0', 'Inventory');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'inventory', 'Inventory', 'Inventory');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'inventory_edit', 'Inventory Edit', 'Inventory Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'inventory_view', 'Inventory View', 'Inventory View', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'inventory_request', 'Inventory Request', 'Inventory Request', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'inventory_approve', 'Inventory Approve', 'Inventory Approve', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'inventory_checkin', 'Inventory Check In', 'Inventory Check In', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'inventory_checkout', 'Inventory Checkout', 'Inventory Checkout', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'inventory'));


INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias LIKE 'inventory%');

CREATE TABLE plugin_inventory_items
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  category_id INT,
  `use` ENUM('Single', 'Multi'),
  product_id  INT,
  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,
  publish TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,
  KEY (product_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_inventory_stocks
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  purchasing_item_id INT,
  supplier_id INT,
  item_id  INT,
  amount_type ENUM('Weight', 'Volume', 'Unit'),
  amount  DECIMAL(10, 2),
  expiry_date DATETIME,
  location_id INT,

  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,
  publish TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (purchasing_item_id),
  KEY (supplier_id),
  KEY (item_id),
  KEY (location_id)
)
ENGINE = INNODB
CHARSET = UTF8;

DROP TABLE IF EXISTS plugin_inventory_stocks_has_checkins;
CREATE TABLE plugin_inventory_stocks_has_checkouts
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  stock_id  INT,
  amount_type ENUM('Weight', 'Volume', 'Unit'),
  amount  DECIMAL(10, 2),
  location_id INT,
  requestee_id  INT,
  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,
  publish TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (stock_id),
  KEY (requestee_id),
  KEY (location_id)
)
ENGINE = INNODB
CHARSET = UTF8;

DROP TABLE IF EXISTS plugin_inventory_stocks_has_checkins_has_checkouts;
CREATE TABLE plugin_inventory_stocks_has_checkouts_has_checkins
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  checkout_id  INT,
  amount_type ENUM('Weight', 'Volume', 'Unit'),
  amount  DECIMAL(10, 2),
  location_id INT,
  requestee_id  INT,
  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,
  publish TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (checkout_id),
  KEY (requestee_id),
  KEY (location_id)
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE plugin_inventory_items ADD COLUMN vat_rate DECIMAL(10, 2);
ALTER TABLE plugin_inventory_items ADD COLUMN amount_type ENUM('Weight', 'Volume', 'Unit');
ALTER TABLE plugin_inventory_stocks_has_checkouts_has_checkins ADD COLUMN lost TINYINT NOT NULL DEFAULT 0;

