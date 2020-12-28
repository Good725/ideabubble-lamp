/*
ts:2020-02-20 16:02:00
*/

INSERT IGNORE INTO engine_role_permissions
    (SELECT *
     FROM (SELECT id
           FROM engine_project_role
           WHERE role IN ('Student', 'Teacher', 'Org rep', 'Basic')) AS `t1`,
          (SELECT id
           FROM engine_resources
           WHERE alias IN ('user_tools_messages', 'messaging_access_own_mail')) AS `t`);