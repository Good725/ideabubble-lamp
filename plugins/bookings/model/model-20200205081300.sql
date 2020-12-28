/*
ts:2020-02-05 08:13:00
*/

update plugin_reports_reports set php_modifier = REPLACE(php_modifier,"/*and i'. $i . '.booking_status <> 3 */'","and i'. $i . '.booking_status <> 3 '") where name like '%roll call%';
