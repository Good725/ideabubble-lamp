insert into plugin_timeoff_departments
  (id, name)
values
  (1, 'Accounting'),
  (2, 'Human Resources'),
  (3, 'Security'),
  (4, 'Cleaning');

INSERT INTO `users`
(`id`, `role_id`, `group_id`, `discount_format_id`, `email`, `password`, `logins`, `last_login`, `logins_fail`, `last_fail`, `name`, `surname`, `country`, `timezone`, `county`, `address`, `eircode`, `address_2`, `address_3`, `phone`, `mobile`, `company`, `registered`, `email_verified`, `trial_start_date`, `can_login`, `deleted`, `validation_code`, `status`, `role_other`, `heard_from`, `credit_account`, `default_home_page`, `default_dashboard_id`, `datatable_length_preference`, `auto_logout_minutes`)
VALUES
(47, 3, NULL, NULL, 'timeoff-staff@ideabubble.com', 'c31c224ca045584b1a57abc0adc9cc8fd5019eaf3db2b591c973dd3f10273a63', 0, NULL, 0, NULL, 'John', NULL, NULL, 'Europe/Dublin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, NULL, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL);

INSERT INTO `users`
(`id`, `role_id`, `group_id`, `discount_format_id`, `email`, `password`, `logins`, `last_login`, `logins_fail`, `last_fail`, `name`, `surname`, `country`, `timezone`, `county`, `address`, `eircode`, `address_2`, `address_3`, `phone`, `mobile`, `company`, `registered`, `email_verified`, `trial_start_date`, `can_login`, `deleted`, `validation_code`, `status`, `role_other`, `heard_from`, `credit_account`, `default_home_page`, `default_dashboard_id`, `datatable_length_preference`, `auto_logout_minutes`)
VALUES
(51, 3, NULL, NULL, 'timeoff-manager@ideabubble.com', 'c31c224ca045584b1a57abc0adc9cc8fd5019eaf3db2b591c973dd3f10273a63', 0, NULL, 0, NULL, 'Alice', NULL, NULL, 'Europe/Dublin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, NULL, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL);

insert into plugin_timeoff_departments_staff
  (department_id, staff_id, role)
values
  (1, 47, 'staff'),
  (1, 51, 'manager');
