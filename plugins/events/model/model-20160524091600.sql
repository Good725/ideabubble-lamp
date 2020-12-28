/*
ts:2016-05-24 09:16:00
*/
INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('events', 'events_commission_type', 'Commission Type', 'Fixed', 'Fixed', 'Fixed',  'Fixed',  'Fixed',  'both', '', 'select', 'Events', 0, 'Model_Event,getCommissionTypes');
INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('events', 'events_commission_amount', 'Commission Amount', '0.01', '0.01', '0.01',  '0.01',  '0.01',  'both', '', 'text', 'Events', 0, '');
INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('events', 'events_commission_currency', 'Commission Currency', 'EUR', 'EUR', 'EUR',  'EUR',  'EUR',  'both', '', 'select', 'Events', 0, 'Model_Event,getCurrencies');

INSERT INTO `engine_settings`
(`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('payments', 'stripe_client_id', 'Stripe Client Id', '', '', '',  '',  '',  'both', '', 'text', 'Stripe', 0, '');

-- INSERT INTO `engine_project_role` (`role`, `description`, `publish`, `deleted`) VALUES ('Events','Events', 0, 0);

TRUNCATE TABLE `plugin_events_events`;
TRUNCATE TABLE `plugin_events_venues`;
ALTER IGNORE TABLE `plugin_events_events` DROP COLUMN `date`;
ALTER IGNORE TABLE `plugin_events_events` CHANGE COLUMN `start_time` `start_datetime`  datetime;
ALTER IGNORE TABLE `plugin_events_events` CHANGE COLUMN `end_time` `end_datetime`  datetime;
ALTER IGNORE TABLE `plugin_events_events` CHANGE COLUMN `tickets` `quantity`  INT;
ALTER TABLE `plugin_events_events` ADD COLUMN `is_online` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `plugin_events_events` ADD COLUMN `is_onsale` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `plugin_events_events` ADD COLUMN `is_public` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `plugin_events_events` ADD COLUMN `show_remaining_tickets` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `plugin_events_events` ADD COLUMN `url` VARCHAR(255) UNIQUE;
ALTER TABLE `plugin_events_events` ADD COLUMN `owned_by` INT;
ALTER TABLE `plugin_events_events` ADD KEY (`owned_by`);
ALTER TABLE `plugin_events_events` MODIFY COLUMN `description` MEDIUMTEXT;
ALTER TABLE `plugin_events_events` MODIFY COLUMN `start_datetime` DATETIME;
ALTER TABLE `plugin_events_events` MODIFY COLUMN `end_datetime` DATETIME;
ALTER TABLE `plugin_events_events` MODIFY COLUMN `date_created` DATETIME;
ALTER IGNORE TABLE `plugin_events_events` DROP COLUMN `organizer`;
ALTER IGNORE TABLE `plugin_events_events` DROP KEY `name_UNIQUE`;
ALTER IGNORE TABLE `plugin_events_events` DROP KEY `url`;
ALTER TABLE `plugin_events_events` ADD KEY `url` (category_id, url);

ALTER TABLE `plugin_events_venues` ADD COLUMN map_lat DOUBLE;
ALTER TABLE `plugin_events_venues` ADD COLUMN map_lng DOUBLE;
ALTER TABLE `plugin_events_venues` ADD COLUMN address_1 VARCHAR(100);
ALTER TABLE `plugin_events_venues` ADD COLUMN address_2 VARCHAR(100);
ALTER TABLE `plugin_events_venues` ADD COLUMN address_3 VARCHAR(100);
ALTER TABLE `plugin_events_venues` DROP COLUMN street;
ALTER TABLE `plugin_events_venues` MODIFY COLUMN `date_created` DATETIME;
ALTER TABLE `plugin_events_venues` MODIFY COLUMN `id` INT AUTO_INCREMENT;

CREATE TABLE `plugin_events_events_has_organizers`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  contact_id INT,
  description TEXT,

  KEY (`event_id`),
  KEY (`contact_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_events_sold`
(
  event_id  INT NOT NULL PRIMARY KEY,
  sold  INT NOT NULL DEFAULT 0
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_events_ticket_types_sold`
(
  ticket_type_id  INT NOT NULL PRIMARY KEY,
  sold  INT NOT NULL DEFAULT 0
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_seller_currencies`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  seller_id INT,
  currency  VARCHAR(3),
  published TINYINT NOT NULL DEFAULT 0,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (`seller_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_events_has_ticket_types`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `event_id`  INT NOT NULL,
  `type` ENUM('Free', 'Paid', 'Donation'),
  `name` VARCHAR(100) NOT NULL DEFAULT '',
  `description` TEXT,
  `show_description` TINYINT NOT NULL DEFAULT 0,
  `price` DECIMAL(10, 2),
  `include_commission` TINYINT DEFAULT 0,
  `sale_starts` DATETIME,
  `sale_ends` DATETIME,
  `quantity` INT,
  `max_per_order` INT,
  `min_per_order` INT,
  `visible` TINYINT NOT NULL DEFAULT 1,
  `hide_until` DATETIME,
  `hide_after` DATETIME,

  KEY (`event_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

INSERT INTO `plugin_contacts_mailing_list` (`name`) VALUES ('Event Organizer');

CREATE TABLE `plugin_events_accounts`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  commission_type ENUM('Fixed', 'Percent'),
  commission_amount DECIMAL(10, 2),
  commission_currency VARCHAR(3),
  use_stripe_connect  TINYINT NOT NULL DEFAULT 0,
  stripe_auth TEXT,
  notify_sms_on_buy_ticket  TINYINT NOT NULL DEFAULT 0,
  notify_email_on_buy_ticket  TINYINT NOT NULL DEFAULT 0,
  notify_email_on_event_enquiry  TINYINT NOT NULL DEFAULT 0,
  `status` ENUM('Enabled', 'Disabled', 'Archived', 'Suspended'),
  `status_note` VARCHAR(100),
  iban VARCHAR(100),
  bic VARCHAR(100),
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  deleted TINYINT NOT NULL DEFAULT 0,

  UNIQUE KEY (owner_id)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_orders`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `buyer_id`  INT,
  `account_id` INT,
  `status` ENUM('PROCESSING', 'PAID', 'CANCELLED'),
  `status_reason` VARCHAR(100),
  `total` DECIMAL(10, 2),
  `currency` VARCHAR(3),
  commission_type ENUM('Fixed', 'Percent'),
  commission_amount DECIMAL(10, 2),
  firstname VARCHAR(25),
  lastname VARCHAR(25),
  email VARCHAR(100),
  address_1 VARCHAR(100),
  address_2 VARCHAR(100),
  city VARCHAR(100),
  country_id INT,
  county_id INT,
  eircode VARCHAR(100),
  ip4 INT UNSIGNED,
  created DATETIME,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (`buyer_id`),
  KEY (`account_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_orders_payments`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  currency VARCHAR(3),
  `status` ENUM('PROCESSING', 'PAID', 'VOID', 'CANCELLED', 'ERROR'),
  `status_reason` VARCHAR(100),
  paymentgw VARCHAR(20),
  paymentgw_info VARCHAR(100),
  created DATETIME,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (`order_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_orders_items`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  order_id  INT NOT NULL,
  ticket_type_id  INT NOT NULL,
  quantity  INT DEFAULT 0,
  donation DECIMAL(10, 2) DEFAULT 0,
  price DECIMAL(10, 2) DEFAULT 0,
  currency VARCHAR(3) DEFAULT '',

  KEY (`ticket_type_id`),
  KEY (`order_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_orders_tickets`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  order_item_id INT NOT NULL,
  code  VARCHAR(128) UNIQUE,
  checked DATETIME,
  checked_by  INT,
  checked_note VARCHAR(100),
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (`order_item_id`)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE `plugin_events_categories`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  url VARCHAR(255) UNIQUE,
  category  VARCHAR(255),
  published TINYINT NOT NULL DEFAULT 0,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE `plugin_events_tags`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  tag VARCHAR(100) UNIQUE,
  published TINYINT NOT NULL DEFAULT 0,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE=INNODB
CHARSET=UTF8;


CREATE TABLE `plugin_events_has_tags`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT,
  tag_id  INT,

  KEY (`event_id`),
  KEY (`tag_id`)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('Other', 'Other', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('appearance-or-signing', 'Appearance or Signing', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('attraction', 'Attraction', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('camp, Trip, or Retreat', 'Camp, Trip, or Retreat', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('class-training-workshop', 'Class, Training, or Workshop', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('concert-performance', 'Concert or Performance', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('conference', 'Conference', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('convention', 'Convention', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('dinner-gala', 'Dinner or Gala', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('festival-fair', 'Festival or Fair', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('game-competition', 'Game or Competition', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('meeting-networking-event', 'Meeting or Networking Event', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('party-social-gathering', 'Party or Social Gathering', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('race-endurance-event', 'Race or Endurance Event', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('rally', 'Rally', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('screening', 'Screening', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('seminar-talk', 'Seminar or Talk', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('tour', 'Tour', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('tournament', 'Tournament', 1);
INSERT INTO `plugin_events_categories` (`url`, `category`, `published`) VALUES ('tradeshow-consumer-show-expo', 'Tradeshow, Consumer Show, or Expo', 1);

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'events', 'Events', 'Events');
SELECT id INTO @events_resource_id FROM `engine_resources` o WHERE o.`alias` = 'events';
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_index', 'Events / Index', 'Events List', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_index_limited', 'Events / Index : limited', 'Events List limited access based on permission', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_edit', 'Events / Edit', 'Events Create / Update', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_edit_limited', 'Events / Edit : limited', 'Events Create/Update limited access based on permission', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_delete', 'Events / Delete', 'Events Delete', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_delete_limited', 'Events / Delete : Limited', 'Events Delete limited access based on permission', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_view', 'Events / View', 'Events View', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_view_limited', 'Events / View : limited', 'Events View limited access based on permission', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_orders_index', 'Events / Orders ', 'Events Orders List', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_orders_index_limited', 'Events / Orders : Limited ', 'Events Orders List limited access based on permission', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_orders_view', 'Events / Orders / View', 'Events Orders View', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_orders_view_limited', 'Events / Orders / View : Limited ', 'Events Orders View limited access based on permission', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_orders_edit', 'Events / Orders / Edit', 'Events Orders Edit', @events_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'events_orders_edit_limited', 'Events / Orders / Edit : Limited', 'Events Orders Edit limited access based on permission', @events_resource_id);
