/*
ts:2019-04-12 10:26:00
*/

ALTER TABLE plugin_courses_schedules_has_paymentoptions ADD COLUMN interest_type ENUM('Percent', 'Fixed', 'Custom') DEFAULT 'Percent';
ALTER TABLE plugin_courses_schedules_has_paymentoptions ADD COLUMN custom_payments TEXT;

UPDATE plugin_courses_schedules_has_paymentoptions SET `deleted`=1 WHERE interest_type='Percent' AND (interest_rate=0 or months=0 or interest_rate=0);

ALTER TABLE plugin_courses_courses_has_paymentoptions ADD COLUMN interest_type ENUM('Percent', 'Fixed', 'Custom') DEFAULT 'Percent';
ALTER TABLE plugin_courses_courses_has_paymentoptions ADD COLUMN custom_payments TEXT;
