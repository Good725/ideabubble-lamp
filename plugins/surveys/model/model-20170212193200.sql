/*
ts:2017-02-12 19:32:00
*/

ALTER TABLE plugin_survey_result ADD KEY (`survey_id`);
ALTER TABLE plugin_survey_answer_options ADD KEY (`answer_id`);
ALTER TABLE plugin_survey_answer_result ADD KEY (`question_id`);
ALTER TABLE plugin_survey_answer_result ADD KEY (`survey_result_id`);
ALTER TABLE plugin_survey_answer_result ADD KEY (`answer_id`);
ALTER TABLE plugin_survey_answers ADD KEY (`type_id`);
ALTER TABLE plugin_survey_has_groups ADD KEY (`survey_id`);
ALTER TABLE plugin_survey_has_groups ADD KEY (`group_id`);
ALTER TABLE plugin_survey_has_questions ADD KEY (`survey_id`);
ALTER TABLE plugin_survey_has_questions ADD KEY (`group_id`);
ALTER TABLE plugin_survey_has_questions ADD KEY (`question_id`);
ALTER TABLE plugin_survey_questions ADD KEY (`answer_id`);
ALTER TABLE plugin_survey_sequence ADD KEY (`survey_id`);
ALTER TABLE plugin_survey_sequence_items ADD KEY (`sequence_id`);
ALTER TABLE plugin_survey_sequence_items ADD KEY (`question_id`);
ALTER TABLE plugin_survey_sequence_items ADD KEY (`answer_option_id`);
ALTER TABLE plugin_survey_sequence_items ADD KEY (`target_id`);

UPDATE `plugin_reports_reports`
  SET `sql` = 'SELECT \r\n		sq.id, sq.title, GROUP_CONCAT(CONCAT(cstats.label, \' => \', cstats.cnt) SEPARATOR \'<br />\') AS result, SUM(cstats.cnt) AS total\r\n	FROM plugin_survey_questions sq\r\n		INNER JOIN plugin_survey_has_questions hq ON hq.question_id = sq.id\r\n		INNER JOIN plugin_survey s ON hq.survey_id = s.id\r\n		INNER JOIN \r\n			(\r\n				SELECT sq.id, ao.label, count(*) as `cnt`\r\n					FROM plugin_survey_questions sq\r\n						INNER JOIN plugin_survey_answers sa ON sq.answer_id = sa.id AND sa.deleted = 0\r\n						INNER JOIN plugin_survey_answer_options ao ON sa.id = ao.answer_id AND ao.deleted = 0\r\n						INNER JOIN plugin_survey_answer_result ar ON ar.question_id = sq.id AND ar.answer_id = ao.id\r\n					GROUP BY sq.id, sa.id, ao.id\r\n					ORDER BY ao.order_id\r\n			) cstats ON sq.id = cstats.id\r\n	WHERE s.title =  \"{!survey_title!}\"\r\n	GROUP BY sq.id\r\n	ORDER BY sq.title'
  WHERE (`name` = 'Completed Surveys');

UPDATE `plugin_reports_reports`
  SET `sql` = 'SELECT \r\n		sq.title AS `Question`, GROUP_CONCAT(CONCAT(ao.label, \' => \', IFNULL(cstats.cnt, 0)) SEPARATOR \'\\r\\n\') AS `Answers`, SUM(IFNULL(cstats.cnt, 0)) AS `Total`\r\n	FROM plugin_survey_questions sq\r\n		INNER JOIN plugin_survey_has_questions hq ON hq.question_id = sq.id\r\n		INNER JOIN plugin_survey s ON hq.survey_id = s.id\r\n		INNER JOIN plugin_survey_answers sa ON sq.answer_id = sa.id AND sa.deleted = 0\r\n		INNER JOIN plugin_survey_answer_options ao ON sa.id = ao.answer_id AND ao.deleted = 0\r\n		LEFT JOIN \r\n			(\r\n				SELECT sq.id as qid, sa.id as sid, ao.id as aid, count(*) as `cnt`\r\n					FROM plugin_survey_questions sq\r\n						INNER JOIN plugin_survey_answers sa ON sq.answer_id = sa.id AND sa.deleted = 0\r\n						INNER JOIN plugin_survey_answer_options ao ON sa.id = ao.answer_id AND ao.deleted = 0\r\n						INNER JOIN plugin_survey_answer_result ar ON ar.question_id = sq.id AND ar.answer_id = ao.id\r\n					GROUP BY sq.id, sa.id, ao.id\r\n					ORDER BY ao.order_id\r\n			) cstats ON sq.id = cstats.qid AND sa.id = cstats.sid AND ao.id = cstats.aid\r\n	WHERE s.title =  \"{!survey_title!}\"\r\n	GROUP BY sq.id\r\n	ORDER BY sq.title'
  WHERE (`name` = 'Completed Surveys');

