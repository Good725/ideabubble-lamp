/*
ts:2016-03-23 17:07:00
*/

CREATE TABLE plugin_products_auto_featured
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  manufacturer_id INT,
  distributor_id INT,
  max_price DOUBLE,
  min_price DOUBLE,
  numbers  INT
)
ENGINE = INNODB
CHARSET = UTF8;

select id into @products_plugin_id_20160324 from `plugins` where `name`='products';
insert into `engine_cron_tasks` set `title` = 'Set Auto Featured Products', `frequency` = '{\"minute\":[\"0\"],\"hour\":[\"1\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', `plugin_id` = @products_plugin_id_20160324, `publish` = '0', `delete` = '0', `action` = 'cron_autofeature';
