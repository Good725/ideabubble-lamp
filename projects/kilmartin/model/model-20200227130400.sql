/*
ts:2020-02-27 13:04:00
*/

-- Ensure all existing roles that have the courses_schedule_edit permission, has this one too
INSERT IGNORE INTO engine_role_permissions (SELECT *
                                            FROM (SELECT role_id
                                                  FROM engine_role_permissions
                                                  WHERE resource_id = (SELECT id
                                                                       FROM engine_resources
                                                                       WHERE alias = 'courses_schedule_edit')) as `role`,
                                                 (select id
                                                  from engine_resources
                                                  where alias in ('courses_schedule_amendable')) as `alias`);