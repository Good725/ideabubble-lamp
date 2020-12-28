/*
ts:2020-04-17 07:11:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'api_chat_create_room', 'API Chat Create Room', 'API Chat Create Room', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'api'));

insert into engine_role_permissions
	(resource_id, role_id)
  (
    select
        o.id,rr.role_id
      from engine_resources o
        join
          (
            select
                rp.role_id
              from engine_resources r
                inner join engine_role_permissions rp on r.id = rp.resource_id
              where r.alias='chat_create_room'
          ) rr
      where o.alias='api_chat_create_room'
  );
