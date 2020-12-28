/*
ts:2019-01-24 18:11:00
*/

ALTER TABLE plugin_reports_reports ADD COLUMN generate_documents_link_by_template_variable VARCHAR(100);
ALTER TABLE plugin_reports_reports ADD COLUMN generate_documents_mode ENUM('PARAMETER', 'ROW');
ALTER TABLE plugin_reports_reports ADD COLUMN generate_documents_row_variable VARCHAR(100);
