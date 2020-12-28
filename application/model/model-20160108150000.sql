/*
ts:2016-01-08 15:00:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`,`value_stage`,`value_test`,`value_dev`,`default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES
('column_toggle', 'Column Toggle Default', '3_col','3_col','3_col','3_col','3_col', 'both', 'Toggle the number of column for the modern style, None => No side column, 2 Column => Left menu Column and main area, 3 colum => Left menu, main area and right activity column', 'select', 'Engine', '0', 'Model_Settings,modern_style_column_display');
