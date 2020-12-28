/*
ts: 2020-02-21 12:41:00
*/

-- It appears this permission is not in the DALM and not in IBEC project
INSERT IGNORE INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(2, 'todos_content_tab', 'Todos / Content tab', 'Todos content tab',    (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'todos'));


DROP PROCEDURE IF EXISTS `change_new_projects_permissions`;
DELIMITER $$

CREATE PROCEDURE change_new_projects_permissions()
BEGIN
    DECLARE dalm_intialised DATETIME;
    SET dalm_intialised = (select min(registered) from engine_users limit 1);
    IF dalm_intialised > STR_TO_DATE("2020-01-15", " %Y-%m-%d") THEN
        DELETE
        FROM engine_role_permissions
        WHERE role_id = (SELECT id
                         FROM engine_project_role
                         WHERE role = 'Basic');

        INSERT IGNORE INTO engine_role_permissions (SELECT *
                                                    FROM (SELECT id
                                                          FROM engine_project_role
                                                          WHERE role = 'Basic') AS `role`,
                                                         (SELECT id
                                                          FROM engine_resources
                                                          WHERE alias IN
                                                                ('settings',
                                                                 'settings_index',
                                                                 'settings_activities',
                                                                 'dashboards',
                                                                 'edit_all_dashboards',
                                                                 'messaging',
                                                                 'messaging_global_see_all',
                                                                 'messaging_send_system_sms',
                                                                 'messaging_send_system_email',
                                                                 'messaging_view_system_sms',
                                                                 'messaging_view_system_email',
                                                                 'user',
                                                                 'user_profile',
                                                                 'my_activities',
                                                                 'contacts2',
                                                                 'contacts2_index',
                                                                 'contacts2_edit',
                                                                 'contacts2_delete',
                                                                 'todos',
                                                                 'todos_manage_all',
                                                                 'view_website_frontend',
                                                                 'lookups',
                                                                 'media',
                                                                 'courses',
                                                                 'transactions',
                                                                 'contacts2_alert_menu',
                                                                 'global_search',
                                                                 'files',
                                                                 'files_view',
                                                                 'files_edit',
                                                                 'files_delete',
                                                                 'files_edit_directory',
                                                                 'menus',
                                                                 'news',
                                                                 'panels',
                                                                 'pages',
                                                                 'testimonials',
                                                                 'seo',
                                                                 'payments',
                                                                 'reports',
                                                                 'user_tools_messages',
                                                                 'user_tools_help',
                                                                 'login_as',
                                                                 'messaging_send_alerts',
                                                                 'courses_bookings_see_seating_numbers',
                                                                 'messaging_access_own_mail',
                                                                 'messaging_access_system_mail',
                                                                 'messaging_access_others_mail',
                                                                 'messaging_access_drafts',
                                                                 'user_edit',
                                                                 'user_view',
                                                                 'roles',
                                                                 'role_edit',
                                                                 'role_view',
                                                                 'permissions',
                                                                 'cms_action_button_1',
                                                                 'todos_list',
                                                                 'todos_edit',
                                                                 'todos_view_results',
                                                                 'contacts3',
                                                                 'courses_booking_edit',
                                                                 'courses_course_edit',
                                                                 'courses_schedule_edit',
                                                                 'courses_timetable_edit',
                                                                 'courses_category_edit',
                                                                 'courses_subject_edit',
                                                                 'courses_location_edit',
                                                                 'courses_provider_edit',
                                                                 'courses_studymode_edit',
                                                                 'courses_type_edit',
                                                                 'courses_level_edit',
                                                                 'courses_year_edit',
                                                                 'courses_academicyear_edit',
                                                                 'courses_registration_edit',
                                                                 'courses_topic_edit',
                                                                 'courses_zone_edit',
                                                                 'user_profile_education',
                                                                 'user_profile_preferences',
                                                                 'user_profile_email',
                                                                 'todos_view_my_todos',
                                                                 'courses_rollcall',
                                                                 'timesheets',
                                                                 'timesheets_edit',
                                                                 'customscroller',
                                                                 'documents',
                                                                 'notifications',
                                                                 'paybackloyalty',
                                                                 'surveys',
                                                                 'uploader',
                                                                 'linkchecker',
                                                                 'bookings',
                                                                 'todos_content_tab',
                                                                 'grades_edit',
                                                                 'todos_edit_create_tasks',
                                                                 'todos_edit_create_assignments',
                                                                 'todos_edit_create_assesments',
                                                                 'todos_edit_create_tests',
                                                                 'todos_edit_create_exams',
                                                                 'activecampaign'
                                                                    )) AS `alias`);
        DELETE
        FROM plugin_reports_report_sharing
        WHERE group_id = (SELECT id
                          FROM engine_project_role
                          WHERE role = 'Basic');
        INSERT INTO plugin_reports_report_sharing (report_id, group_id)
        VALUES ((SELECT id
                 FROM plugin_reports_reports
                 WHERE name = 'My Roll Call'), (SELECT id
                                                FROM engine_project_role
                                                WHERE role = 'Basic'));

        DELETE
        FROM engine_role_permissions
        WHERE role_id = (SELECT id
                         FROM engine_project_role

                         WHERE role = 'Student');

        INSERT IGNORE INTO engine_role_permissions (SELECT *
                                                    FROM (SELECT id
                                                          FROM engine_project_role
                                                          WHERE role = 'Student') AS `role`,
                                                         (SELECT id
                                                          FROM engine_resources
                                                          WHERE alias IN
                                                                ('contacts2_index_limited', 'contacts2_index_limited',
                                                                 'todos', 'contacts3_frontend_bookings',
                                                                 'contacts3_frontend_accounts',
                                                                 'contacts3_frontend_attendance',
                                                                 'contacts3_frontend_timetables',
                                                                 'contacts3_frontend_wishlist',
                                                                 'todos_view_results',
                                                                 'contacts3_frontend_attendance_edit',
                                                                 'contacts3_frontend_attendance_edit_auth',
                                                                 'todos_view_results_limited')) AS `alias`);

        DELETE
        FROM engine_role_permissions
        WHERE role_id = (SELECT id
                         FROM engine_project_role
                         WHERE role = 'Org rep');

        INSERT IGNORE INTO engine_role_permissions (SELECT *
                                                    FROM (SELECT id
                                                          FROM engine_project_role
                                                          WHERE role = 'Org rep') AS `role`,
                                                         (SELECT id
                                                          FROM engine_resources
                                                          WHERE alias IN
                                                                ('contacts2',
                                                                 'contacts2_index_limited',
                                                                 'contacts2_view_limited',
                                                                 'todos',
                                                                 'contacts3_limited_family_access',
                                                                 'contacts3_limited_view',
                                                                 'contacts3_frontend_bookings',
                                                                 'contacts3_frontend_accounts',
                                                                 'contacts3_frontend_attendance',
                                                                 'contacts3_frontend_timetables',
                                                                 'contacts3_frontend_wishlist',
                                                                 'contacts3_settings',
                                                                 'todos_view_results',
                                                                 'contacts3_frontend_attendance_edit',
                                                                 'contacts3_frontend_attendance_edit_auth',
                                                                 'contacts3_billing')) AS `alias`);
        DELETE
        FROM engine_role_permissions
        WHERE role_id = (SELECT id
                         FROM engine_project_role
                         WHERE role = 'Teacher');

        INSERT IGNORE INTO engine_role_permissions (SELECT *
                                                    FROM (SELECT id
                                                          FROM engine_project_role
                                                          WHERE role = 'Teacher') AS `role`,
                                                         (SELECT id
                                                          FROM engine_resources
                                                          WHERE alias IN
                                                                ('todos',
                                                                    'view_website_frontend',
                                                                    'reports',
                                                                    'user_tools_messages',
                                                                    'user_tools_help',
                                                                    'cms_action_button_2',
                                                                    'todos_list',
                                                                    'todos_view_my_todos',
                                                                    'timetables',
                                                                    'timetables_view_limited',
                                                                    'timesheets',
                                                                    'timesheets_edit_limited',
                                                                    'todos_content_tab',
                                                                    'todos_edit_create_assesments',
                                                                    'courses_rollcall_limited',
                                                                    'courses_schedule_edit_limited'
                                                                    )) AS `alias`);
        DELETE
        FROM plugin_reports_report_sharing
        WHERE group_id = (SELECT id
                          FROM engine_project_role
                          WHERE role = 'Teacher');
        INSERT INTO plugin_reports_report_sharing (report_id, group_id)
        VALUES ((SELECT id
                 FROM plugin_reports_reports
                 WHERE name = 'My Roll Call'), (SELECT id
                                                FROM engine_project_role
                                                WHERE role = 'Teacher'));
    END IF;
END $$
DELIMITER ;

CALL change_new_projects_permissions();
DROP PROCEDURE IF EXISTS `change_new_projects_permissions`;