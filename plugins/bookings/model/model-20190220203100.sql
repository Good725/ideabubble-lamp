/*
ts:2019-02-20 20:31:00
*/

update plugin_messaging_notification_templates set usable_parameters_in_template='$date,$interview_date,$location,$student,$staff,$course,$code' where `name` in ('interview-no-offer', 'interview-waiting-list', 'interview-accept-offer', 'course-interview-schedule');
