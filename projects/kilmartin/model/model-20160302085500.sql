/*
ts:2016-03-02 08:55:00
*/

UPDATE plugin_reports_reports SET dashboard = 0 WHERE plugin_reports_reports.`sql` LIKE '%b.schedule_id%';