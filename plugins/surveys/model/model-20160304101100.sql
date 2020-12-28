/*
ts:2016-03-04 10:11:00
*/

-- Don't display on dashboard by default
UPDATE IGNORE `plugin_reports_reports` SET `dashboard` = '0' WHERE `name` IN ('Completed Surveys', 'Speed of Survey', 'Abondoned Surveys');

-- Correct misspelling
UPDATE IGNORE `plugin_reports_reports`
SET
  `name`    = 'Abandoned Surveys',
  `summary` = 'Number of Abandoned surveys'
WHERE `name` = 'Abondoned Surveys';

UPDATE IGNORE `plugin_reports_widgets`
SET
  `name` = 'Abandoned Surveys',
  `x_axis` = 'Abandoned',
  `y_axis` = 'Abandoned Surveys'
WHERE `name` = 'Abondoned Surveys';

UPDATE IGNORE `plugin_reports_reports`
SET `sql`    = 'SELECT s.title AS \'Abandoned Surveys\', COUNT(*) AS \'Abandoned\' FROM plugin_survey s LEFT JOIN plugin_survey_result r on s.id = r.survey_id WHERE ISNULL(endtime) AND s.deleted = 0 GROUP BY s.title'
WHERE `name` = 'Abandoned Surveys';
