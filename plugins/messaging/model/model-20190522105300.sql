/*
ts:2019-05-22 10:53:00
*/

ALTER TABLE plugin_messaging_messages ADD COLUMN reply_to_message_id INT;
ALTER TABLE plugin_messaging_messages ADD COLUMN allow_reply INT;
