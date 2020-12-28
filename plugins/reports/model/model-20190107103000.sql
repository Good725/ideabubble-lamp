/*
ts:2018-01-07 10:30:00
*/

ALTER TABLE `plugin_reports_reports`
  ADD COLUMN `show_results_counter` INT(1)       NOT NULL DEFAULT 0 AFTER `totals_group`,
  ADD COLUMN `results_counter_text` VARCHAR(255) NULL               AFTER `show_results_counter`
;
