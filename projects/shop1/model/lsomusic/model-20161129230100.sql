/*
ts:2016-11-29 23:01:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`)
  VALUES
  ('concert-form', 'Concert Form', 'EMAIL', '1', 'Concert Form', 'Date: $date<br />\r\nName: $name<br />\r\nGrade: $grade<br />\r\nInstrument: $instrument<br />\r\nTeacher: $teacher<br />\r\nTitle of Piece: $fulltitleofpiece<br />\r\nComposer Surname: $composer_surname<br />\r\nComposer Name: $composer_firstname', 'concert form', '$date, $name, $grade, $instrument, $teacher, $fulltitleofpiece, $composer_surname, $composer_firstname');

UPDATE `plugin_formbuilder_forms` SET `fields`='<input value=\"concert_form\" name=\"trigger\" type=\"hidden\"><input name=\"event\" value=\"concert-form\" type=\"hidden\"><input name=\"email_template\" value=\"concert-form\" type=\"hidden\"><input value=\"thank-you\" id=\"formbuilder-preview-\" name=\"redirect\" type=\"hidden\"><li><label for=\"doc_input\">Date of Concert</label><input id=\"doc_input\" name=\"date\" class=\"validate[required] datepicker\" type=\"text\"></li><li><label for=\"name_id\">Name</label><input id=\"name_id\" name=\"name\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"grade_id\">Grade</label><input id=\"grade_id\" name=\"grade\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"instrument_id\">Instrument</label><input id=\"instrument_id\" name=\"instrument\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"teacher_id\">Teacher Name</label><input name=\"teacher\" id=\"teacher_id\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"fulltitleofpiece_id\">Full Title of Piece</label><input id=\"fulltitleofpiece_id\" name=\"fulltitleofpiece\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"composer_id\">Composer Surname</label><input id=\"composer_id\" name=\"composer_surname\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"firstname_id\">Composer First Name</label><input name=\"composer_firstname\" id=\"firstname_id\" type=\"text\"></li>                <li><label for=\"submit_btn\"></label><button id=\"submit_btn\" type=\"submit\">Submit</button></li>' WHERE (`form_name`='School Concert');



