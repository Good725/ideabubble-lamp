/*
ts:2019-10-11 14:30:00
*/

DELIMITER ;;
UPDATE `plugin_panels` SET `publish` = '0' WHERE `position` = 'footer';;

INSERT INTO `plugin_panels`
(`title`,        `position`, `text`, `order_no`, `type_id`, `date_modified`,   `created_by`, `modified_by`, `publish`, `deleted`) VALUES
('Logo',         'footer',   '',     '1',        '2',       CURRENT_TIMESTAMP, '1',          '1',           '1',       '0'),
('Contact us',   'footer',   '',     '1',        '2',       CURRENT_TIMESTAMP, '1',          '1',           '1',       '0'),
('Connect',      'footer',   '',     '1',        '2',       CURRENT_TIMESTAMP, '1',          '1',           '1',       '0');;

UPDATE `plugin_panels`
SET   `text`  = '<p style="margin-bottom: 1rem;"><a href="/"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/itt_logo_footer.svg" class="m-auto" style="display: block; width: 300px; max-width: none;" /></a></p>\n'
WHERE `title` = 'Logo';;

UPDATE `plugin_panels`
SET   `text`  = '<p class="nowrap">
\n    <a class="button mr-1 mr-md-5" href="/contact-us">Contact us</a>
\n    <a href="#" style="vertical-align: middle;"><img src="/shared_media/irishtimestraining/media/photos/content/app-store-badge.svg" alt="Download on the App Store" style="border: 1px solid #fff;margin-bottom: -5px;" /></a>
\n</p>'
WHERE `title` = 'Contact us';;

UPDATE `plugin_panels`
SET `text` = '<div class="footer-social-icons">
\n    <h5>CONNECT WITH US</h5>
\n
\n    <p>
\n        <a href="https://www.facebook.com/IrishTimesTraininig"          title="Facebook"><img src="/shared_media/irishtimestraining/media/photos/content/itt-facebook.svg" alt="Facebook" /></a>
\n        <a href="https://twitter.com/itimestraining"                    title="Twitter" ><img src="/shared_media/irishtimestraining/media/photos/content/itt-twitter.svg"  alt="Twitter"  /></a>
\n        <a href="https://www.linkedin.com/company/irish-times-training" title="LinkedIn"><img src="/shared_media/irishtimestraining/media/photos/content/itt-linkedin.svg" alt="LinkedIn" /></a>
\n    </p>
\n</div>'
WHERE `title` = 'Connect';;

