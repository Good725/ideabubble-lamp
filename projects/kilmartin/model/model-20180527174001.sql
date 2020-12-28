/*
ts:2018-08-27 17:40:01
*/

UPDATE engine_project_role SET allow_frontend_register=1 WHERE role in ('Parent/Guardian', 'Student', 'Mature Student', 'Teacher');

