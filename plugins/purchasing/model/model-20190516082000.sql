/*
ts:2019-05-16 08:20:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `svg`) VALUES ('purchasing', 'Purchasing', '1', '0', 'purchasing');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'purchasing', 'Purchasing', 'Purchasing');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'purchasing_request', 'Purchase Request', 'Purchase Request', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'purchasing'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'purchasing_approve', 'Purchase Approve', 'Purchase Approve', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'purchasing'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'purchasing_complete', 'Purchase complete', 'Purchase Complete', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'purchasing'));


CREATE TABLE plugin_purchasing_purchases
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  business  VARCHAR(100),
  supplier  VARCHAR(100),
  vat DECIMAL(10, 2),
  total DECIMAL(10, 2),
  date_required DATE,
  reviewer_id INT,
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT(1)NOT NULL DEFAULT 0,
  deleted_by INT,
  approved  DATETIME,
  approved_by INT,
  purchased DATETIME,
  purchased_by  INT,
  status  ENUM('Pending', 'Approved', 'Declined', 'Purchased') NOT NULL DEFAULT 'PENDING'

)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_purchasing_purchases_has_items
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  purchase_id INT NOT NULL,
  product VARCHAR(100),
  amount_price DECIMAL(10, 2),
  amount_vat DECIMAL(10, 2),
  amount_total DECIMAL(10, 2),
  amount_type ENUM('Weight', 'Volume', 'Unit'),
  amount  DECIMAL(10, 2),
  total DECIMAL(10, 2),
  deleted TINYINT(1) NOT NULL DEFAULT 0,

   KEY (purchase_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias in ('purchasing', 'purchasing_request', 'purchasing_approve', 'purchasing_complete'));
