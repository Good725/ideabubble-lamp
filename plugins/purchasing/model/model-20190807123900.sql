/*
ts:2019-08-07 12:39:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`,
                                                       `overwrite_cms_message`, `date_created`, `created_by`)
VALUES ('requested_po_updated',
        'Email sent to staff member when their requested PO is approved/declined',
        'EMAIL',
        (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
        'Your purchase order has been $po_status',
        '<p>Hi $name</p>

           <p>Your purchase order has been $po_status</p>

           <p>$po_number</p>

           <p>Regards</p>
           ',
        '1',
        CURRENT_TIMESTAMP,
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0));

UPDATE
    `plugin_messaging_notification_templates`
SET `message` = '<p>Hi $name</p>

           <p>Your purchase order has been $po_status</p>

           <p>$po_number</p>

           <p>Regards</p>'
WHERE `name` = 'requested_po_updated';

