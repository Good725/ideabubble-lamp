/*
ts:2018-03-21 11:04:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('contact-invite-family-member', 'EMAIL', '1', 'Invitation', 'Hello $name $email,\r\n\r\n<a href=\"$url_join\">click</a> to join or\r\n<a href=\"$url_reject\">click</a> to reject', '1', 'Contacts', '$url_join,$url_reject,$email,$name', 'contacts3');

CREATE TABLE plugin_contacts3_invitations
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  invited_by_contact_id INT NOT NULL,
  invited_email VARCHAR(100),
  invited_contact_id  INT,
  status  ENUM('Wait', 'Accepted', 'Rejected')
)
ENGINE = INNODB
CHARSET = UTF8;
