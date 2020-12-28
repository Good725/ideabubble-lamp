/*
ts:2019-05-31 09:55:00
*/

SET @group_1_id := (SELECT id from plugin_survey_groups where `title` = 'Group 1' LIMIT 1);

SET @group_2_id := (SELECT id from plugin_survey_groups where `title` = 'Group 2' LIMIT 1);

SET @group_3_id := (SELECT id from plugin_survey_groups where `title` = 'Group 3' LIMIT 1);

SET @group_4_id := (SELECT id from plugin_survey_groups where `title` = 'Group 4' LIMIT 1);

SET @group_5_id := (SELECT id from plugin_survey_groups where `title` = 'Group 5' LIMIT 1);

INSERT INTO plugin_survey (title, start_date, end_date, store_answer, result_pdf_download,
                                                      result_template_id, display_thank_you, thank_you_page_id, publish,
                                                      expiry, deleted, created_by, created_on, updated_by, updated_on,
                                                      pagination, view_all)
VALUES ('Coordinator Student Assessment', null, null, 1, 0, 0, 0, 0, 1, 1, 0, 32, '2019-05-31 08:51:13', 32,
        '2019-05-31 09:46:50', 1, 1);

SET @survey_id := (SELECT id from plugin_survey where `title` = 'Coordinator Student Assessment' LIMIT 1);

INSERT INTO plugin_survey_sequence (title, survey_id, publish, deleted, created_by,
                                                               created_on, updated_by, updated_on)
VALUES ('Coordinator Student Assessment', @survey_id, 1, 0, 32, '2019-05-31 08:51:13', 32, '2019-05-31 09:46:50');

SET @survey_sequence_id := (SELECT id from plugin_survey_sequence where `title` = 'Coordinator Student Assessment' LIMIT 1);

INSERT INTO plugin_survey_answers (title, type_id, group_name, publish, deleted, created_by,
                                   created_on, updated_by, updated_on)
VALUES ('Yes/No', 1, '', 1, 0, 32, '2019-05-31 09:08:47', 32, '2019-05-31 09:08:47');

SET @survey_answer_yes_no_id := (SELECT id
                                 from plugin_survey_answers
                                 where title = 'Yes/No'
                                 LIMIT 1);

INSERT INTO plugin_survey_answers (title, type_id, group_name, publish, deleted, created_by,
                                                              created_on, updated_by, updated_on)
VALUES ('Input', 3, '', 1, 0, 32, '2019-05-31 08:49:33', 32, '2019-05-31 08:49:33');

SET @survey_answer_input_id := (SELECT id from plugin_survey_answers where title = 'Input' LIMIT 1);

INSERT INTO plugin_survey_answers (title, type_id, group_name, publish, deleted, created_by,
                                                              created_on, updated_by, updated_on)
VALUES ('Excellent to Poor', 1, '', 1, 0, 32, '2019-05-31 09:03:32', 32, '2019-05-31 09:05:15');

SET @survey_answer_exc_to_poor_id := (SELECT id from plugin_survey_answers where title = 'Excellent to Poor' LIMIT 1);

INSERT INTO plugin_survey_answers (title, type_id, group_name, publish, deleted, created_by,
                                                              created_on, updated_by, updated_on)
VALUES ('Comment Box', 2, '', 1, 0, 32, '2019-05-31 09:14:19', 32, '2019-05-31 09:14:19');

SET @survey_answer_comment_box_id := (SELECT id from plugin_survey_answers where title = 'Comment Box' LIMIT 1);

INSERT INTO plugin_survey_answer_options (label, value, answer_id, order_id, publish,
                                                                     deleted, created_by, created_on, updated_by,
                                                                     updated_on)
VALUES ('Excellent', 1, @survey_answer_exc_to_poor_id, 0, 1, 0, 32, '2019-05-31 09:03:32', 32, '2019-05-31 09:05:15');

INSERT INTO plugin_survey_answer_options (label, value, answer_id, order_id, publish,
                                                                     deleted, created_by, created_on, updated_by,
                                                                     updated_on)
VALUES ('Good', 2, @survey_answer_exc_to_poor_id, 1, 1, 0, 32, '2019-05-31 09:03:32', 32, '2019-05-31 09:05:15');

INSERT INTO plugin_survey_answer_options (label, value, answer_id, order_id, publish,
                                                                     deleted, created_by, created_on, updated_by,
                                                                     updated_on)
VALUES ('Fair', 3, @survey_answer_exc_to_poor_id, 2, 1, 0, 32, '2019-05-31 09:03:32', 32, '2019-05-31 09:05:15');

INSERT INTO plugin_survey_answer_options (label, value, answer_id, order_id, publish,
                                                                     deleted, created_by, created_on, updated_by,
                                                                     updated_on)
VALUES ('Poor', 4, @survey_answer_exc_to_poor_id, 3, 1, 0, 32, '2019-05-31 09:03:32', 32, '2019-05-31 09:05:16');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Student First Name', @survey_answer_input_id, 1, 0, 32, '2019-05-31 08:50:11', 32, '2019-05-31 08:50:11');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Student Surname', @survey_answer_input_id, 1, 0, 32, '2019-05-31 08:58:16', 32, '2019-05-31 08:59:00');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('School', @survey_answer_input_id, 1, 0, 32, '2019-05-31 08:59:34', 32, '2019-05-31 08:59:34');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('School Year', @survey_answer_input_id, 1, 0, 32, '2019-05-31 09:00:03', 32, '2019-05-31 09:00:03');


INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Host Family First Name', @survey_answer_input_id, 1, 0, 32, '2019-05-31 09:00:40', 32, '2019-05-31 09:00:40');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Host Family Surname', @survey_answer_input_id, 1, 0, 32, '2019-05-31 09:01:08', 32, '2019-05-31 09:01:08');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Integration into Family Life', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:04:01', 32, '2019-05-31 09:04:54');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Interaction with Host Family', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:05:50', 32, '2019-05-31 09:05:50');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she actively participate in daily family life?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:09:15', 32,
        '2019-05-31 09:09:15');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she participate in the activities proposed by the host family?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:09:40', 32,
        '2019-05-31 09:09:40');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('When he/she is not at home, does he/she inform the family of his/her whereabouts and company?',
        @survey_answer_yes_no_id, 1, 0, 32,
        '2019-05-31 09:10:39', 32, '2019-05-31 09:10:39');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she ask for permission to go out / attend events?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:11:07', 32,
        '2019-05-31 09:11:07');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she eat what he/she is offered without complaining?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:11:25', 32,
        '2019-05-31 09:11:25');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she respect the sleeping hours?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:11:41', 32, '2019-05-31 09:11:52');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Cleanliness and tidiness of his/her bedroom', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:12:54', 32, '2019-05-31 09:13:08');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                     created_on, updated_by, updated_on)
VALUES ('Other Comments about Host Family', @survey_answer_input_id, 1, 0, 32, '2019-05-31 09:12:54', 32,
        '2019-05-31 09:13:08');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Attitude and behaviour at home', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:23:14', 32, '2019-05-31 09:23:14');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she respect the times set by the family, e.g. bedtime, meal times etc.?',
        @survey_answer_exc_to_poor_id, 1, 1, 32,
        '2019-05-31 09:35:58', 32, '2019-05-31 09:36:58');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Completion of Homework', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:37:51', 32, '2019-05-31 09:37:51');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Attitude towards fellow classmates ', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:38:11', 32, '2019-05-31 09:38:11');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Attitude towards teachers', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:38:28', 32, '2019-05-31 09:38:28');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Is he/she Punctual?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:38:45', 32, '2019-05-31 09:38:45');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Number of absences', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:39:02', 32, '2019-05-31 09:39:02');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Care of materials (Uniform, Books, Copies, Journal etc.)', @survey_answer_exc_to_poor_id, 1, 0, 32, '2019-05-31 09:39:18', 32,
        '2019-05-31 09:39:18');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she communicate well in English at School?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:39:36', 32,
        '2019-05-31 09:39:36');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Other Comments about School', @survey_answer_comment_box_id, 1, 0, 32, '2019-05-31 09:39:53', 32, '2019-05-31 09:39:53');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Is he/she motivated?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:41:37', 32, '2019-05-31 09:41:37');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Activities he/she is currently involved in', @survey_answer_comment_box_id, 1, 0, 32, '2019-05-31 09:41:53', 32, '2019-05-31 09:41:53');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Weekend routine', @survey_answer_comment_box_id, 1, 0, 32, '2019-05-31 09:42:10', 32, '2019-05-31 09:42:10');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Other Comments about Free Time & Extracurricular', @survey_answer_comment_box_id, 1, 0, 32, '2019-05-31 09:42:26', 32,
        '2019-05-31 09:42:26');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she respect the rules established by the host organization?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:42:45', 32,
        '2019-05-31 09:42:45');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she go to the coordinator when he/she needs something?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:43:03', 32,
        '2019-05-31 09:43:03');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she go to the family when he/she needs something?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:43:20', 32,
        '2019-05-31 09:43:20');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Does he/she communicate well in English with those around him/her?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:43:39', 32,
        '2019-05-31 09:43:39');

INSERT INTO plugin_survey_questions (title, answer_id, publish, deleted, created_by,
                                                                created_on, updated_by, updated_on)
VALUES ('Has he/she had to go to the doctor?', @survey_answer_yes_no_id, 1, 0, 32, '2019-05-31 09:43:55', 32, '2019-05-31 09:43:55');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'Student First Name' LIMIT 1), 0, 1, 0, 32, '2019-05-31 08:51:13', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'Student Surname' LIMIT 1), 1, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'School' LIMIT 1), 2, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'School Year' LIMIT 1), 3, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'Host Family First Name' LIMIT 1), 4, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'Host Family Surname' LIMIT 1), 5, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id from plugin_survey_questions where title = 'Integration into Family Life' LIMIT 1), 6, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id,
        (SELECT id from plugin_survey_questions where title = 'Interaction with Host Family' LIMIT 1), 7, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Attitude and behaviour at home'
                                  LIMIT 1), 8, 1, 0, 32, '2019-05-31 09:41:07', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id,
        (SELECT id from plugin_survey_questions where title = 'Does he/she actively participate in daily family life?' LIMIT 1), 9, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Does he/she participate in the activities proposed by the host family?'
                                  LIMIT 1), 10, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Does he/she respect the times set by the family, e.g. bedtime, meal times etc.?'
                                  LIMIT 1), 11, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'When he/she is not at home, does he/she inform the family of his/her whereabouts and company?'
                                  LIMIT 1), 12, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Does he/she ask for permission to go out / attend events?'
                                  LIMIT 1), 13, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Does he/she eat what he/she is offered without complaining?'
                                  LIMIT 1), 14, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Does he/she respect the sleeping hours?'
                                  LIMIT 1), 15, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Cleanliness and tidiness of his/her bedroom'
                                  LIMIT 1), 16, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_1_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Other Comments about Host Family'
                                  LIMIT 1), 17, 1, 0, 32, '2019-05-31 09:15:39', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Completion of Homework'
                                  LIMIT 1), 18, 1, 0, 32, '2019-05-31 09:41:07', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Attitude towards fellow classmates '
                                  LIMIT 1), 19, 1, 0, 32, '2019-05-31 09:41:07', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Attitude towards teachers'
                                  LIMIT 1), 20, 1, 0, 32, '2019-05-31 09:41:07', 32, '2019-05-31 09:46:50');
INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Is he/she Punctual?'
                                  LIMIT 1), 21, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Number of absences'
                                  LIMIT 1), 22, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Care of materials (Uniform, Books, Copies, Journal etc.)'
                                  LIMIT 1), 23, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Does he/she communicate well in English at School?'
                                  LIMIT 1), 24, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_2_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Other Comments about School'
                                  LIMIT 1), 25, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_3_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Is he/she motivated?'
                                  LIMIT 1), 26, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_3_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Activities he/she is currently involved in'
                                  LIMIT 1), 27, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_3_id, (SELECT id
                                  from plugin_survey_questions
                                  where title =
                                        'Weekend routine'
                                  LIMIT 1), 28, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_3_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Other Comments about Free Time & Extracurricular'
                                  LIMIT 1), 29, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                                                    deleted, created_by, created_on, updated_by,
                                                                    updated_on)
VALUES (@survey_id, @group_4_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Does he/she respect the rules established by the host organization?'
                                  LIMIT 1), 30, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_4_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Does he/she go to the coordinator when he/she needs something?'
                                  LIMIT 1), 31, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_4_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Does he/she go to the family when he/she needs something?'
                                  LIMIT 1), 32, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_4_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Does he/she communicate well in English with those around him/her?'
                                  LIMIT 1), 33, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

INSERT INTO plugin_survey_has_questions (survey_id, group_id, question_id, order_id, publish,
                                         deleted, created_by, created_on, updated_by,
                                         updated_on)
VALUES (@survey_id, @group_4_id, (SELECT id
                                  from plugin_survey_questions
                                  where title = 'Has he/she had to go to the doctor?'
                                  LIMIT 1), 34, 1, 0, 32, '2019-05-31 09:46:12', 32, '2019-05-31 09:46:50');

