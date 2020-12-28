/*
ts:2020-01-30 12:05:00
*/

drop temporary table if exists last_sent_tmp;
create temporary table last_sent_tmp as
    (select max(m.date_created) as last_sent, t.id
     from plugin_messaging_notification_templates t
              left join plugin_messaging_notifications n on t.id = n.template_id
              left join plugin_messaging_messages m on n.message_id = m.id
     group by t.id);

update plugin_messaging_notification_templates inner join last_sent_tmp on plugin_messaging_notification_templates.id = last_sent_tmp.id
set plugin_messaging_notification_templates.last_sent = last_sent_tmp.last_sent;

drop temporary table if exists last_sent_tmp;