/*
ts:2019-03-29 14:20:00
*/

CREATE TABLE `plugin_extra_projects_sprints2` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `jira_sprint_id` INT NOT NULL,
  `customer` VARCHAR(200) NOT NULL,
  `sprint` VARCHAR(45) NOT NULL,
  `summary` VARCHAR(200) NULL,
  `budget` DECIMAL(6,2) NULL,
  `balance` INT NULL,
  `spent` INT NULL,
  `progress` INT NULL,
  `project_status_type_id` INT NULL DEFAULT '1',
  `last_synced` DATE NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `plugin_extra_projects_status_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `plugin_extra_projects_status_types`
VALUES (1,'Status'),(2,'Sales'),(3,'Planning'),(4,'Accounts'),(5,'Admin'),(6,'Design'),(7,'Development'),(8,'QA'),(9,'UAT'),(10,'Sign Off'),(11,'Support'),(12,'Subscribed'),(13,'Completed');


