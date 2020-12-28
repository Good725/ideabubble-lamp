/*
ts:2020-09-28 08:31:00
*/

create table plugin_ccsaas_databases
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  db_id VARCHAR(100)
)
ENGINE=INNODB CHARSET=UTF8 COLLATE=utf8_general_ci;

alter table plugin_ccsaas_websites add column database_id int;

