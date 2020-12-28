/*
ts:2017-01-09 21:07:00
*/

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_sprints`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`jira_id`	VARCHAR(20),
	`name`	VARCHAR(200),
	`state`	VARCHAR(20),

	KEY		(`jira_id`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_rapidviews_has_sprints`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`rapidview_id`	INT NOT NULL,
	`sprint_id` INT NOT NULL,

	KEY		(`rapidview_id`),
	KEY (`sprint_id`)
) ENGINE = INNODB;

DROP TABLE plugin_extra_projects_rapidviews_sprints;

ALTER TABLE `plugin_extra_projects_rapidviews_sprints_has_issues` RENAME `plugin_extra_projects_sprints_has_issues`;

UPDATE `plugin_reports_parameters` SET `value`='SELECT sprints.id, sprints.name from plugin_extra_projects_sprints sprints ORDER BY name' WHERE (`name`='sprint_id');
UPDATE `plugin_reports_reports` SET `sql`='SELECT \n    __TITLES__ ROUND(SUM(`worklog`.`time_spent`) / 3600, 2) AS `time_spent`\nFROM plugin_extra_projects_worklog worklog INNER JOIN plugin_extra_projects_issues issues ON worklog.issue_id = issues.id INNER JOIN plugin_extra_projects_sprints_has_issues has_issues ON issues.id = has_issues.issue_id INNER JOIN plugin_extra_projects_sprints sprints ON has_issues.sprint_id = sprints.id\n__WHERE__\n__GROUP_BY__' WHERE (`name`='Sprints(JIRA) Spent Time');

