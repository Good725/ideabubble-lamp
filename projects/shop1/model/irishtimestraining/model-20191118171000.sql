/*
ts:2019-11-18 17:10:00
*/

-- Set the email header colour. `email_header_color` needs to be moved to `engine_site_theme_variables`
UPDATE `engine_site_themes` SET `email_header_color` = '#f0f0f0', `email_link_color` = '#19A29E' WHERE `stub` = '49';;