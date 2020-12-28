/*
ts:2016-01-25 12:20:00
*/

ALTER TABLE plugin_reports_reports  ADD COLUMN generate_documents TINYINT(1);
ALTER TABLE plugin_reports_reports  ADD COLUMN generate_documents_template_file_id INT;
ALTER TABLE plugin_reports_reports  ADD COLUMN generate_documents_pdf TINYINT(1);
ALTER TABLE plugin_reports_reports  ADD COLUMN generate_documents_office_print TINYINT(1);
ALTER TABLE plugin_reports_reports  ADD COLUMN generate_documents_office_print_bulk TINYINT(1);
ALTER TABLE plugin_reports_reports  ADD COLUMN generate_documents_tray TINYINT;

-- small bug fix
ALTER TABLE `plugin_reports_parameters` MODIFY COLUMN `is_multiselect`  TINYINT(1);


