/*
ts:2016-03-14 14:05:00
*/

-- Remove hardcoded IDs
UPDATE `plugin_reports_reports`
SET `widget_id` = NULL
WHERE `name` IN ('Quick Stats', 'Attendee VS Absentee', 'Outstanding PAYG', 'RAB', 'Yearly Prepay', 'Top 10 Vacancies', 'Free Rooms');