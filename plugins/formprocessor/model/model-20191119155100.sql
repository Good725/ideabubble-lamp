/*
ts:2019-11-19 15:51:00
*/


INSERT INTO `plugin_messaging_notification_templates`
SET `send_interval`                 = null,
    `name`                          = 'newsletter-signup-frontend-user',
    `description`                   = '',
    `driver`                        = 'EMAIL',
    `type_id`                       = '1',
    `subject`                       = 'Thank you for signing up',
    `sender`                        = '',
    `message`                       = '<p>
 	    <b>Thank you for signing up to our newsletter!</b>
    </p>
    <p>This email was sent from <a href="http://$host">$host</a> and was issued from <a href="$referer">$referer_path</a>.</p>',
    `overwrite_cms_message`         = '0',
    `page_id`                       = '0',
    `header`                        = '',
    `footer`                        = '',
    `schedule`                      = null,
    `date_created`                  = NOW(),
    `date_updated`                  = NOW(),
    `last_sent`                     = null,
    `publish`                       = '1',
    `deleted`                       = '0',
    `create_via_code`               = 'Newsletter Signup Frontend',
    `usable_parameters_in_template` = '',
    `doc_generate`                  = null,
    `doc_helper`                    = null,
    `doc_template_path`             = null,
    `doc_type`                      = null,
    `category_id`                   = '0';

