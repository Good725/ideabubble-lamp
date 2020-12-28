/*
ts:2019-11-25 15:29:00
*/

truncate TABLE `plugin_contacts3_job_functions`;
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Accounting & Finance', 'accounting_and_finance');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Administrative', 'administrative');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Business Development & Sales', 'business_development_and_sales');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Consulting', 'consulting');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Engineering', 'engineering');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Human Resources', 'human_resources');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Information Technology', 'information_technology');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Learning & Development', 'learning_and_development');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Legal', 'legal');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Marketing', 'marketing');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Operations', 'operations');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Program & Product Management', 'program_and_product_management');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Purchasing', 'purchasing');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Research', 'research');
INSERT INTO `plugin_contacts3_job_functions` (`label`, `name`)
VALUES ('Support', 'support');

truncate TABLE `plugin_contacts3_organisation_industries`;
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Agriculture', 'agriculture');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Arts', 'arts');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Construction', 'construction');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Corporate Services', 'corporate_services');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Design', 'design');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Education', 'education');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Energy & Mining', 'energy_and_mining');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Entertainment', 'entertainment');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Finance', 'finance');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Hardware & Networking', 'hardware_and_networking');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Healthcare', 'healthcare');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Hospitality', 'hospitality');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Legal', 'legal');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Manufacturing', 'manufacturing');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Media & Communications', 'media_and_communication');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Non-profit', 'non_profit');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Public administration', 'public_administration');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Public safety', 'public_safety');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Real estate', 'real_estate');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Retail', 'retail');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Software & IT services', 'software_and_it_services');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Transportation & logistics', 'transportation_and_logistics');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Travel', 'travel');
INSERT INTO `plugin_contacts3_organisation_industries` (`label`, `name`)
VALUES ('Wellness & Fitness', 'wellness_and_fitness');

truncate TABLE `plugin_contacts3_organisation_sizes`;
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`, `order`)
VALUES ('1 - 25', '1_to_25', 1);
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`, `order`)
VALUES ('26 - 50', '26_to_50', 2);
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`, `order`)
VALUES ('51 - 100', '51_to_100', 3);
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`, `order`)
VALUES ('101 - 500', '101_to_500', 4);
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`, `order`)
VALUES ('501 - 1000', '501_to_1000', 5);
INSERT INTO `plugin_contacts3_organisation_sizes` (`label`, `name`, `order`)
VALUES ('More than 1000', 'more_than_1000', 6);