/*
ts:2020-04-20 23:33:00
*/
INSERT IGNORE INTO
    `plugin_pages_pages`
(
    `name_tag`,
    `title`,
    `content`,
    `date_entered`,
    `last_modified`,
    `created_by`,
    `modified_by`,
    `publish`,
    `deleted`,
    `include_sitemap`,
    `layout_id`,
    `category_id`)
SELECT
    'thank-you-waitlist',
    'Thank you',
    '<h2><em><strong>Thank you!</strong></em></h2>

  <h2>Thank you for adding to waitlist.</h2>

  <p>We will notify you once the free space on this course appears.&nbsp;</p>

  <p>If you have any questions please feel free to email us <a href=http://info@kes.ie">info@kes.ie</a> or call our offices on 061-444989.</p>',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    '1',
    '0',
    '0',
    (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'thankyou' LIMIT 1),
    (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (
        SELECT `id`
        FROM `plugin_pages_pages`
        WHERE `name_tag` IN (
                             'thank-you-waitlist', 'thank-you-waitlist.html') AND `deleted` = 0)
LIMIT 1;