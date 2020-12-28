/*
ts:2018-12-19 13:00:00
*/

UPDATE
  `plugin_reports_reports`
SET
  `sql` = REPLACE(`sql`, "'{!date!}' AS", "DATE_FORMAT('{!date!}', '%d/%m/%Y') AS")
WHERE
  `delete` = 0
AND
  `name` IN ('Master Roll Call', 'Print Roll Call')
;