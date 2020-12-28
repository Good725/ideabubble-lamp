/*
ts:2020-01-14 09:14:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`,
                                      `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('invoice_enable_lead_booker_for_primary_biller', 'Allow invoice type bookings to be billed to lead booker', 'courses', '0', '0', '0', '0', '0',
        'If enabled, allow the lead booker to be the primary biller for the organisation in the checkout',
        'toggle_button', 'Courses', 'Model_Settings,on_or_off');