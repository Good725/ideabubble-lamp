/*
ts:2019-05-24 14:30:00
*/

/* Create new settings */
INSERT INTO `engine_settings`
  (`variable`, `name`, `note`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `group`, `type`)
VALUES
(
  'email_wrapper_html',
  'Email wrapper',
  'HTML to wrap around all emails',
  '',
  '',
  '',
  '',
  '',
  'Messaging',
  'html_editor'
);

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('email_logo', 'Email logo', 'The logo to be used in emails sent by the site.', 'select', 'Messaging', 'Model_Media,get_logos_as_options');

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('email_help_text', 'Need help text', 'Help text to be displayed near the bottom of emails.', 'wysiwyg', 'Messaging');

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('email_footer_text', 'Footer text', 'Text displayed at the very bottom of emails.', 'wysiwyg', 'Messaging');

DELIMITER ;;
UPDATE
  `engine_settings`
SET
  `default` = '<style>
\na:link {color: $email_link_color;}
\n</style>
\n<table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" width="100%" style="background: #f0f0f0; overflow; hidden;" width="100%">
\n    <tbody>
\n        <tr align="center" style="background: $theme_color;">
\n            <td>&nbsp;</td>
\n            <td style="padding: 16px"><img alt="$company_name" src="$logo_src" height="48" /></td>
\n            <td>&nbsp;</td>
\n        </tr>
\n        <tr style="background: $theme_color; box-shadow: 0 5px 5px #ccc;">
\n            <td height="50">&nbsp;</td>
\n            <td rowspan="2" width="512">
\n                <div style="background: #ffffff; border-radius: 6px 6px 0 0; min-height: 50px; padding: $content_padding $content_padding 10px;">$message_body</div>
\n            </td>
\n            <td height="50">&nbsp;</td>
\n        </tr>
\n        <tr>
\n            <td>&nbsp;</td>
\n            <td>&nbsp;</td>
\n        </tr>
\n        <tr>
\n            <td></td>
\n            <td><div style="background: #fff;padding-top: 6px;border-radius: 0 0 6px 6px;"></div></td>
\n            <td></td>
\n       </tr>
\n        <tr>
\n            <td>&nbsp;</td>
\n            <td>&nbsp;</td>
\n            <td>&nbsp;</td>
\n        </tr>
\n        <tr>
\n            <td>&nbsp;</td>
\n            <td>
\n                <div style="background: #ffffff; border-radius: 6px; padding: 16px; text-align: center;">$need_help_text</div>
\n            </td>
\n            <td>&nbsp;</td>
\n        </tr>
\n        <tr>
\n            <td>&nbsp;</td>
\n            <td>&nbsp;</td>
\n            <td>&nbsp;</td>
\n        </tr>
\n        <tr>
\n            <td>&nbsp;</td>
\n            <td>
\n                <div>&nbsp;</div>
\n
\n                <div style="text-align: center;" style="padding: 16px;">$footer_text</div>
\n            </td>
\n            <td>&nbsp;</td>
\n        </tr>
\n    </tbody>
\n</table>'
WHERE
  `variable` = 'email_wrapper_html'
;;

UPDATE
  `engine_settings`
SET
  `value_live`  = '<p style="margin: .5em 0;">Need more help?<br /><a href="$base_urlcontact-us.html" style="color: $email_link_color;">We&#39;re here, ready to talk</a></p>',
  `value_stage` = '<p style="margin: .5em 0;">Need more help?<br /><a href="$base_urlcontact-us.html" style="color: $email_link_color;">We&#39;re here, ready to talk</a></p>',
  `value_test`  = '<p style="margin: .5em 0;">Need more help?<br /><a href="$base_urlcontact-us.html" style="color: $email_link_color;">We&#39;re here, ready to talk</a></p>',
  `value_dev`   = '<p style="margin: .5em 0;">Need more help?<br /><a href="$base_urlcontact-us.html" style="color: $email_link_color;">We&#39;re here, ready to talk</a></p>',
  `default`     = '<p style="margin: .5em 0;">Need more help?<br /><a href="$base_urlcontact-us.html" style="color: $email_link_color;">We&#39;re here, ready to talk</a></p>'
WHERE
  `variable` = 'email_help_text'
;;

/* Add column to the theme editor */
ALTER TABLE `engine_site_themes` ADD COLUMN `email_header_color` VARCHAR(25) NOT NULL DEFAULT '#37478f' AFTER `template_id`;;
ALTER TABLE `engine_site_themes` ADD COLUMN `email_link_color`   VARCHAR(25) NOT NULL DEFAULT '#0000ee' AFTER `email_header_color`;;

UPDATE `engine_site_themes` SET `email_header_color` = '#37478f', `email_link_color` = '#37478f' WHERE `stub` = 'default';;
UPDATE `engine_site_themes` SET `email_header_color` = '#00c6ee', `email_link_color` = '#00c6ee' WHERE `stub` = 'kes1';;
UPDATE `engine_site_themes` SET `email_header_color` = '#2d7b31', `email_link_color` = '#2d7b31' WHERE `stub` = '30';; -- Cullen Insurance
UPDATE `engine_site_themes` SET `email_header_color` = '#00c6ee', `email_link_color` = '#00c6ee' WHERE `stub` = '31';; -- Kilmartin
UPDATE `engine_site_themes` SET `email_header_color` = '#5d0024', `email_link_color` = '#5d0024' WHERE `stub` = '32';; -- Brookfield College (.ie site)
UPDATE `engine_site_themes` SET `email_header_color` = '#d02a27', `email_link_color` = '#d02a27' WHERE `stub` = '33';; -- STAC
UPDATE `engine_site_themes` SET `email_header_color` = '#d02a27', `email_link_color` = '#d02a27' WHERE `stub` = '34';; -- STAC
UPDATE `engine_site_themes` SET `email_header_color` = '#425ba9', `email_link_color` = '#425ba9' WHERE `stub` = '35';; -- Pallaskenry
UPDATE `engine_site_themes` SET `email_header_color` = '#425ba9', `email_link_color` = '#425ba9' WHERE `stub` = '36';; -- Pallaskenry
UPDATE `engine_site_themes` SET `email_header_color` = '#ffffff', `email_link_color` = '#212a5e' WHERE `stub` = '37';; -- Saoirse
UPDATE `engine_site_themes` SET `email_header_color` = '#f07523', `email_link_color` = '#f07523' WHERE `stub` = '38';; -- Smart Marketing
UPDATE `engine_site_themes` SET `email_header_color` = '#000000', `email_link_color` = '#31cdb5' WHERE `stub` = '39';; -- uTicket
UPDATE `engine_site_themes` SET `email_header_color` = '#ffffff', `email_link_color` = '#ee1c25' WHERE `stub` = '40';; -- Irish Haemochromatosis Association
UPDATE `engine_site_themes` SET `email_header_color` = '#00385d', `email_link_color` = '#00385d' WHERE `stub` = '41';; -- Idea Bubble
UPDATE `engine_site_themes` SET `email_header_color` = '#5ac9e8', `email_link_color` = '#5ac9e8' WHERE `stub` = '42';; -- CourseCo
UPDATE `engine_site_themes` SET `email_header_color` = '#007ad3', `email_link_color` = '#e13c27' WHERE `stub` = '43';; -- Brookfield College (.com and .it microsites)
UPDATE `engine_site_themes` SET `email_header_color` = '#5ac9e8', `email_link_color` = '#5ac9e8' WHERE `stub` = '44';; -- CourseCo Demo

-- Same value for each environment
UPDATE
  `engine_settings`
SET
  `value_dev`   = `default`,
  `value_test`  = `default`,
  `value_stage` = `default`,
  `value_live`  = `default`
WHERE
  `variable` = 'email_wrapper_html'
AND 1 = 1
;;
