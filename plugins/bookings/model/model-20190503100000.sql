/*
ts:2019-05-03 10:00:00
*/
UPDATE
  `plugin_reports_reports`
SET
  `sql`  = REPLACE(`sql`, "CONCAT_WS(' ', st.first_name, st.last_name)", "CONCAT('<a href=\"/admin/contacts3?contact=', st.id, '\" target=\"_blank\">', `st`.`first_name`, ' ', `st`.`last_name`, '</a>')")
WHERE
  `name` = 'Master Roll Call'
;