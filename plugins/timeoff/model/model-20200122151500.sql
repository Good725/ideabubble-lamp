/*
ts:2020-01-22 15:15:00
*/

-- Soft delete duplicate notes
UPDATE
    `plugin_contacts3_notes` `n1`
JOIN
    `plugin_contacts3_notes` `n2`
    ON  `n1`.`link_id`       = `n2`.`link_id`
    AND `n1`.`table_link_id` = `n2`.`table_link_id`
    AND `n1`.`id`            < `n2`.`id`
    AND TRIM(REPLACE(`n1`.`note`, '\n','')) = TRIM(REPLACE(`n2`.`note`, '\n',''))
    AND `n1`.`deleted` = 0
    AND `n2`.`deleted` = 0
JOIN
    `plugin_contacts3_notes_tables` `table_link`
    ON  `n1`.`table_link_id` = `table_link`.`id`
    AND `table_link`.`table` = 'plugin_timeoff_requests'
SET
  `n1`.`deleted` = 1
;