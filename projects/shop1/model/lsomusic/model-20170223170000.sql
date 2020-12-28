/*
ts:2017-02-23 17:00:00
*/

UPDATE
  `plugin_formbuilder_forms`
SET
  `fields`        = '<input value=\"concert_form\" name=\"trigger\" type=\"hidden\"><input name=\"event\" value=\"concert-form\" type=\"hidden\"><input name=\"email_template\" value=\"concert-form\" type=\"hidden\"><input value=\"thank-you\" id=\"formbuilder-preview-\" name=\"redirect\" type=\"hidden\"><li><label for=\"doc_input\">Date of Concert</label><input id=\"doc_input\" name=\"date\" class=\"validate[required] datepicker\" type=\"text\"></li><li><label for=\"name_id\">Name</label><input id=\"name_id\" name=\"name\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"grade_id\">Grade</label><input id=\"grade_id\" name=\"grade\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"instrument_id\">Instrument</label><input id=\"instrument_id\" name=\"instrument\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"teacher_id\">Teacher Name</label><input name=\"teacher\" id=\"teacher_id\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"fulltitleofpiece_id\">Full Title of Piece</label><input id=\"fulltitleofpiece_id\" name=\"fulltitleofpiece\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"composer_id\">Composer Name</label><input name=\"composer_name\" id=\"composer_id\" class=\"validate[required]\" type=\"text\"></li><li><label for=\"fulltitleofsecondpiece_id\">Full Title of Second Piece</label><input name=\"fulltitleofsecondpiece\" id=\"fulltitleofsecondpiece_id\" type=\"text\"></li><li><label for=\"composer_id\">Second Composer Name</label><input name=\"second_composer_name\" id=\"second_composer_id\" type=\"text\"></li> <li><label for=\"stool_required\">Stool required</label> <select name=\"stool_required\" class=\"validate[required]\" id=\"stool_required\">   <option value=\"\">Please select</option>   <option value=\"No\">No stool required</option>   <option value=\"Small\">Small stool required</option>   <option value=\"Medium\">Medium stool required</option>   <option value=\"Large\">Large stool required</option>   </select> </li><li><label for=\"concert_form-comments\">Comments</label><textarea name=\"comments\" id=\"concert_form-comments\"></textarea></li><li><label for=\"undefined\"></label><span>[CAPTCHA]</span></li><li><label for=\"submit_btn\"></label><button id=\"submit_btn\" type=\"submit\">Submit</button></li>',
  `date_modified` = CURRENT_TIMESTAMP

WHERE
  `form_name` = 'School Concert';

UPDATE
  `plugin_messaging_notification_templates`
SET
  `message`      = 'Date: $date<br />\nName: $name<br />\nGrade: $grade<br />\nInstrument: $instrument<br />\nTeacher: $teacher<br />\nTitle of Piece: $fulltitleofpiece<br />\nComposer: $composer_name<br />\nTitle of Second Piece: $fulltitleofsecondpiece<br />\nComposer: $second_composer_name<br />\nStool required: $stool_required<br />\nComments: $comments',
  `date_updated` = CURRENT_TIMESTAMP
WHERE `name`='concert-form';
