/*
ts:2015-01-01 00:00:17
*/
INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('formprocessor', 'Form Processor', '0', '0', NULL);

UPDATE `plugins` SET icon = 'formProcessor.png' WHERE friendly_name = 'Form Processor';
UPDATE `plugins` SET `plugins`.`order` = 99 WHERE friendly_name = 'Form Processor';


-- -------------------------------------
-- WPPROD-279 Newsletter Subscription Form Setting and Subscribe Checkbox Form Settings
-- -------------------------------------
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`) VALUES ('newsletter_subscription_form', 'Subscription form', 'TRUE', 'both', 'Check to display a newsletter subscription form', 'checkbox', 'Forms', '0');
INSERT INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`) VALUES ('subscription_checkbox', 'Subscription checkbox', 'TRUE', 'both', 'Check to include a \"subscribe to our Mailing List\" checkbox in contact and quick enquiry forms', 'checkbox', 'Forms', '0');
