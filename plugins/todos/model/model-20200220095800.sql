/*
ts:2020-02-20 09:58:00
*/

INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`,
                                                       `usable_parameters_in_template`, `overwrite_cms_message`,
                                                       `date_created`, `date_updated`, `created_by`)
VALUES ('todo_assigned_alert_user',
        'Dashboard notification sent to a user when someone assigns a todo',
        'DASHBOARD',
        (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'system'),
        'New todo assigned',
        'You have been assigned a new todo; <strong>$todo_title</strong>.
         \n<a href="$todo_link">View todo</a>
         \n<strong>Reporter</strong>: $author',
        '$todo_title, $author, $todo_link',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP);