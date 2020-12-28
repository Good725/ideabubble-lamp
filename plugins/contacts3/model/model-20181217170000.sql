/*
ts:2018-12-17 17:00:00
*/

DELIMITER ;;

-- Insert the "Duplicate Contacts" report, if it does not already exist.
INSERT INTO
  `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`, `action_event`, `date_created`)
SELECT
  'Duplicate Contacts',
  '',
  '1',
  '0',
  'Transfer/Delete Selected Contacts',
  '1',
  'var data = {};
\n$(\".contact-delete\").each(function(){
\n    if (this.checked){
\n    data[\"contact[\"+$(this).data(\"contact-id\")+\"]\"] = {action: \"delete\"};
\n    }
\n});
\n$(\".contact-transfer\").each(function(){
\n    if (this.selectedIndex > 0) {
\n    data[\"contact[\"+$(this).data(\"contact-id\")+\"]\"] = {action: \"transfer\", contact_id: $(this).val()};
\n    }
\n});
\n
\n$.post(
\n    \"/admin/contacts3/bulk_transfer_delete\",
\n    data,
\n    function(response){
\n    alert(\"done\");
\n    }
\n);',
  CURRENT_TIMESTAMP
FROM `plugin_reports_reports`
WHERE NOT EXISTS (SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Duplicate Contacts') LIMIT 1
;;

-- Update report to use the latest version of the SQL.
UPDATE
  `plugin_reports_reports`
SET
  `sql` = 'SELECT
\n    c3_1.family_id     AS `Family Id`,
\n    c3_1.id            AS `Contact Id`,
\n    c3_1.first_name    AS `First Name`,
\n    c3_1.last_name     AS `Last Name`,
\n    c3_1.date_of_birth AS `DOB`,
\n    GROUP_CONCAT(DISTINCT b.booking_id)     AS `Bookings`,
\n    GROUP_CONCAT(DISTINCT t.id)             AS `Transactions`,
\n    GROUP_CONCAT(DISTINCT email_1.`value`)  AS `Email`,
\n    GROUP_CONCAT(DISTINCT mobile_1.`value`) AS `Mobile`,
\n    CONCAT(\'&lt;select class=\"contact-transfer\" data-contact-id=\"\', c3_1.id, \'\" data-family-id=\"\', c3_1.family_id, \'\" style=\"width:100px\"&gt;&lt;option value=\"\"&gt;&lt;/option&gt;\', GROUP_CONCAT(DISTINCT CONCAT(\'&lt;option value=\"\',c3_2.id, \'\"&gt;\', c3_2.id, \' \', c3_2.first_name, \' \', c3_2.last_name, \'&lt;/option&gt;\') SEPARATOR \'\'), \'&lt;/select&gt;\') AS `Transfer To`,
\n    CONCAT(\'&lt;input class=\"contact-delete\" type=\"checkbox\" data-contact-id=\"\', c3_1.id, \'\" data-family-id=\"\', c3_1.family_id, \'\" value=\"delete\" /&gt;\') AS `Delete`
\nFROM plugin_contacts3_contacts c3_1
\nLEFT  JOIN plugin_contacts3_contact_has_notifications email_1  ON c3_1.notifications_group_id = email_1.group_id  AND email_1.notification_id  = 1 AND email_1.deleted = 0
\nLEFT  JOIN plugin_contacts3_contact_has_notifications mobile_1 ON c3_1.notifications_group_id = mobile_1.group_id AND mobile_1.notification_id = 2 AND mobile_1.deleted = 0
\nINNER JOIN plugin_contacts3_contacts c3_2                      ON c3_1.first_name = c3_2.first_name               AND c3_1.id <> c3_2.id           AND c3_1.last_name = c3_2.last_name
\nLEFT  JOIN plugin_contacts3_contact_has_notifications email_2  ON c3_2.notifications_group_id =  email_2.group_id AND email_2.notification_id  = 1 AND email_2.deleted  = 0
\nLEFT  JOIN plugin_contacts3_contact_has_notifications mobile_2 ON c3_2.notifications_group_id = mobile_2.group_id AND mobile_2.notification_id = 2 AND mobile_2.deleted = 0
\nLEFT  JOIN plugin_ib_educate_bookings b                        ON c3_1.id = b.contact_id OR c3_1.id = b.bill_payer
\nLEFT  JOIN plugin_bookings_transactions t                      ON c3_1.id = t.contact_id
\nWHERE c3_1.`delete` = 0
\nAND   c3_2.`delete` = 0
\nAND   (email_1.value = email_2.value OR mobile_1.value = mobile_2.value OR c3_1.date_of_birth = c3_2.date_of_birth)
\nGROUP BY c3_1.id
\nORDER BY c3_1.first_name, c3_1.last_name, c3_1.id
\n',
  `date_modified` = CURRENT_TIMESTAMP
WHERE
  `name` = 'Duplicate Contacts'
AND
  `delete` = 0
;;