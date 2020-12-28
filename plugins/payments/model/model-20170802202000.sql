/*
ts:2017-08-02 20:20:00
*/

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `autoload`) VALUES ('Allpoints Payments', 'SELECT * FROM plugin_payments_allpoints_transactions WHERE deleted = 0 ORDER BY id DESC', '1', '0', 'sql', 1);

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where `name` = 'Allpoints Payments'), 'date', 'From', '01-08-2017');

INSERT INTO `plugin_reports_parameters`
  (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where `name` = 'Allpoints Payments'), 'date', 'To', '');

UPDATE `plugin_reports_reports`
  SET
    `sql`='SELECT \nt.id, t.amount, t.verification_code, t.mobilenumber, t.description, t.reference, t.operator, t.`status`, t.created, t.updated, t.remote_tx_id, t.verification_fails,\n	IF (t.status = \'COMPLETED\', CONCAT(\'<input type=\"checkbox\" name=\"invoiced[\', t.id, \']\" value=\"1\" \', IF(invoiced, \' checked=\"checked\" \', \'\'), \'/>\', IF(invoiced, invoiced, \'\')), \'\') AS `invoice`\nFROM plugin_payments_allpoints_transactions t WHERE deleted = 0 and created >= \'{!From!}\' and created < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY) ORDER BY id DESC\r\n',
    `action_button_label`='Mark Invoice Paid',
    `action_button`='1',
    `action_event`='var $tr = $(\"#report_table tbody tr\");\r\nvar data = {};\r\n$tr.each (function(){\r\n var input = $(this).find(\"input\")[0];\r\n if (input) {\r\n   if (input.checked) {\r\n     data[input.name] = 1;\r\n   } else {\r\n    data[input.name] = 0;\r\n   }\r\n }\r\n});\r\n\r\n$.post(\r\n \'/admin/allpoints/mark_invoiced\',\r\n data,\r\n function (response) {\r\n  alert(\'done\');\r\n }\r\n);\r\n'
    WHERE (`name`='Allpoints Payments');

ALTER TABLE plugin_payments_allpoints_transactions ADD COLUMN invoiced DATETIME;
