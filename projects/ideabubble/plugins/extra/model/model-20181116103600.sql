/*
ts:2018-11-16 10:36:00
*/

ALTER TABLE plugin_extra_projects_issues ADD COLUMN resolution VARCHAR(20);
ALTER TABLE plugin_extra_projects_issues ADD COLUMN timeoriginalestimate VARCHAR(20);
ALTER TABLE plugin_extra_projects_issues ADD COLUMN duedate VARCHAR(20);
ALTER TABLE plugin_extra_projects_issues MODIFY COLUMN status VARCHAR(25);


