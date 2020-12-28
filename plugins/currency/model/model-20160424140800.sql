/*
ts:2016-04-24 14:08:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`) VALUES ('currency', 'Currency', 1);

CREATE TABLE `plugin_currency_currencies`
(
  `currency` VARCHAR(3) NOT NULL PRIMARY KEY,
  `name` VARCHAR(25),
  `symbol` VARCHAR(10)
)
ENGINE = InnoDB
CHARSET = UTF8;

INSERT INTO plugin_currency_currencies (`currency`, `name`, `symbol`) VALUES ('GBP', 'British Pound', '£');
INSERT INTO plugin_currency_currencies (`currency`, `name`, `symbol`) VALUES ('EUR', 'Euro', '€');
INSERT INTO plugin_currency_currencies (`currency`, `name`, `symbol`) VALUES ('USD', 'U.S. Dollar', '$');

ALTER TABLE plugin_currency_currencies ADD COLUMN `published` TINYINT DEFAULT 1;
ALTER TABLE plugin_currency_currencies ADD COLUMN `deleted` TINYINT DEFAULT 0;

CREATE TABLE `plugin_currency_rates`
(
  `currency` VARCHAR(3) PRIMARY KEY,
  `rate` DOUBLE NOT NULL,
  `updated` DATETIME NOT NULL
)
ENGINE = InnoDB
CHARSET = UTF8;

CREATE TABLE `plugin_currency_rates_archive`
(
  `currency` VARCHAR(3),
  `base` VARCHAR(3),
  `rate` DOUBLE NOT NULL,
  `updated` DATETIME NOT NULL
)
ENGINE = InnoDB
CHARSET = UTF8;

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('currency_base', 'Base Currency', 'currency', 'EUR', 'EUR', 'EUR', 'EUR', 'EUR', 'both', '', 'text', 'Currency', 0, '');

select id into @currency_plugin_id_20160424 from engine_plugins where `name`='currency';
insert into `engine_cron_tasks` set `title` = 'Currency Rates Refresh', `frequency` = '{\"minute\":[\"0\"],\"hour\":[\"*\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', `plugin_id` = @currency_plugin_id_20160424, `publish` = '0', `delete` = '0', `action` = 'cron';
