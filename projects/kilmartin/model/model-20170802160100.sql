/*
ts:2017-08-02 16:01:00
*/

UPDATE
  `plugin_messaging_notification_templates`
SET
  `sender` = 'noreply@kes.ie',
  `date_updated` = CURRENT_TIMESTAMP
WHERE
  `name` = 'new_user_no_password'
;