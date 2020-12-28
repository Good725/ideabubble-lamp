/*
ts:2015-01-01 00:01:00
*/
-- ------------------------------------------
-- TT-12 Create other Content Layouts
-- ------------------------------------------
INSERT IGNORE INTO `ppages_layouts` (`layout`) VALUES ('content-wide'), ('content-news'), ('content-resources');

-- ------------------------------------------
-- TT-18 Contact Form not working
-- ------------------------------------------
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('contact-form', 'Contact Us', 'Website Enquiry');

-- ------------------------------------------
-- TT-15 Schedule a demo form required
-- ------------------------------------------
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('schedule_a_demo', 'Schedule a Demo', 'Schedule a Demo');

-- ------------------------------------------
-- TT-26 Download Enquiry Form and Redirect Required
-- ------------------------------------------
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('download_enquiry', 'Download Enquiry', 'Download Enquiry');

-- ------------------------------------------
-- TT-12 Create other Content Layouts
-- ------------------------------------------
INSERT IGNORE INTO `ppages_layouts` (`layout`) VALUES ('content-d-n-r-c'), ('content-d-r-c'), ('content-r-c');
INSERT IGNORE INTO `ppages_layouts` (`layout`) VALUES ('content-d-r-n-c');

INSERT IGNORE INTO `plugin_news_categories` (`category`, `order`) VALUES ('2012', '-2'), ('2013', '-3'), ('2014', '-4');
