/*
ts:2016-02-19 13:30:00
*/

-- Completed Surveys
INSERT INTO  plugin_reports_widgets (`name`, type, `x_axis`, `y_axis`, `html`, `created_by`, `modified_by`, `date_created`, `date_modified`,`delete`,`publish`)
VALUES('Surveys',2,'Completed Surveys','Completed','',1,1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1);
INSERT INTO `plugin_reports_reports` (`name`, `summary`, `widget_sql`,`sql`,
`category`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`,`report_type`, `autoload`)
SELECT 'Completed Surveys','Number of Completed Surveys','SELECT s.title AS \'Completed Surveys\', COALESCE(r.total, 0) AS \'Completed\' FROM plugin_survey s LEFT JOIN( SELECT COUNT(*)AS total, survey_id FROM plugin_survey_result WHERE NOT ISNULL(endtime) GROUP BY survey_id)r ON s.id = r.survey_id WHERE s.deleted = 0 GROUP BY s.title;','SELECT q.title AS \'Question Title\', (SELECT GROUP_CONCAT(ao.label , \' - \', (SELECT COUNT(*) FROM plugin_survey_answer_result ar WHERE answer_id = ao.id ) SEPARATOR \' ; \') FROM plugin_survey_answers a LEFT JOIN plugin_survey_answer_options ao ON a.id = ao.answer_id LEFT JOIN plugin_survey_answer_result ar ON ao.id = ar.answer_id WHERE a.id = q.answer_id) AS \'Answers\',(SELECT COUNT(*) FROM plugin_survey_answer_result WHERE id IN ( SELECT ar1.id FROM plugin_survey_answers a1 LEFT JOIN plugin_survey_answer_options ao1 ON a1.id = ao1.answer_id LEFT JOIN plugin_survey_answer_result ar1 ON ao1.id = ar1.answer_id WHERE a1.id = q.answer_id)) AS \'Total\'	FROM plugin_survey_has_questions sq LEFT JOIN plugin_survey_questions q ON sq.question_id = q.id LEFT JOIN plugin_survey_result sr ON sq.survey_id = sr.survey_id LEFT JOIN plugin_survey s	ON s.id = sq.survey_id WHERE s.title =  \'{!survey_title!}\'',0,1,1,1, CURRENT_TIMESTAMP,CURRENT_TIMESTAMP, 1,0, plugin_reports_widgets.`id`,'sql', 1
FROM plugin_reports_widgets WHERE plugin_reports_widgets.`name`='Surveys' ORDER BY plugin_reports_widgets.`id` DESC LIMIT 1;

-- Insert Parameter to select survey
INSERT INTO plugin_reports_parameters (report_id, `type`,`name`,`value`,`delete`, is_multiselect)
SELECT `plugin_reports_reports`.`id`,'custom','survey_title','(SELECT title FROM plugin_survey WHERE deleted = 0 AND store_answer = 1;)',0,0 FROM `plugin_reports_reports` WHERE `name` = 'Completed Surveys';

-- Speed of Survey
INSERT INTO  plugin_reports_widgets (`name`, type, `x_axis`, `y_axis`, `html`, `created_by`, `modified_by`, `date_created`, `date_modified`,`delete`,`publish`)
VALUES ('Speed of Survey',2,'Survey Name','Time','',NULL,NULL,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1);
INSERT INTO `plugin_reports_reports`  (`name`, `summary`, `sql`,`category`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`,`report_type`, `autoload`)
SELECT 'Speed of Survey','Completiotion time of surveys','SELECT s.title AS \'Survey Name\' , CAST(SUM(r.endtime - r.starttime)/ COUNT(*) AS UNSIGNED) / 60 AS \'Time\' FROM plugin_survey_result r JOIN plugin_survey s ON s.id = r.survey_id WHERE NOT ISNULL(endtime) GROUP BY survey_id;',0,1,1,1, CURRENT_TIMESTAMP,CURRENT_TIMESTAMP, 1,0, plugin_reports_widgets.`id`,'sql', 1
FROM plugin_reports_widgets WHERE plugin_reports_widgets.`name`='Speed of Survey' ORDER BY plugin_reports_widgets.`id` DESC LIMIT 1;

-- Abondoned Surveys
INSERT INTO  plugin_reports_widgets (`name`, type, `x_axis`, `y_axis`, `html`, `created_by`, `modified_by`, `date_created`, `date_modified`,`delete`,`publish`)
VALUES ('Abondoned Surveys',2,'Abondoned Surveys','Abondoned','',NULL,NULL,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1);
INSERT INTO `plugin_reports_reports`  (`name`, `summary`, `sql`,`category`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`,`report_type`, `autoload`)
SELECT 'Abondoned Surveys', 'Number of Abandonned surveys','SELECT s.title AS \'Abondoned Surveys\', COUNT(*) AS \'Abondoned\' FROM plugin_survey s LEFT JOIN plugin_survey_result r on s.id = r.survey_id WHERE ISNULL(endtime) AND s.deleted = 0 GROUP BY s.title',0,1,1,1, CURRENT_TIMESTAMP,CURRENT_TIMESTAMP, 1,0, plugin_reports_widgets.`id`,'sql', 1
FROM plugin_reports_widgets WHERE plugin_reports_widgets.`name`='Abondoned Surveys' ORDER BY plugin_reports_widgets.`id` DESC LIMIT 1;