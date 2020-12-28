/*
ts:2016-02-04 09:45:00
*/

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('schedule_start_time','Course Start Time','','','','','','both','The earliest start time for a course or schedule. Enter the time in format 08:00 for 8am minimum start time','text','Courses',0,''),
('schedule_end_time','Course End Time','','','','','','both','The latest end time for a course or schedule. Enter the time in format 23:00 for 11pm maximum end time','text','Courses',0,''),
('schedule_time_interval','Schedules Intervals','','','','','','both','The interval used for the Schedule display. Example 15 will display the schedule timeslots in 15 minutes intervals','text','Courses',0,'');