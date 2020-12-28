/*
ts:2018-08-28 13:26:00
*/

ALTER TABLE engine_project_role ADD COLUMN allow_api_register TINYINT NOT NULL DEFAULT 0;
UPDATE engine_project_role set allow_api_register=allow_frontend_register;
