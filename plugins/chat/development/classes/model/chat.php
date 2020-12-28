<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Chat extends Model
{
    const ROOMS_TABLE = 'plugin_chat_rooms';
    const MESSAGES_TABLE = 'plugin_chat_rooms_has_messages';
    const JOIN_TABLE = 'plugin_chat_rooms_has_users';
    const READ_TABLE = 'plugin_chat_rooms_has_messages_has_read_by';
    const ARCHIVE_TABLE = 'plugin_chat_rooms_has_messages_archived';
    const ONLINE_TABLE = 'plugin_chat_online_users';
    const ROLE_INVITE_TABLE = 'plugin_chat_roles_can_invite';

    public static function get_avatar($user)
    {
        static $avatar_cache = array();

        if (isset($avatar_cache[$user['id']])) {
            return $avatar_cache[$user['id']];
        }
        if ($user['use_gravatar']) {
            $avatar_cache[$user['id']] = URL::get_gravatar($user['email']);
        } else {
            $image_path = Model_Media::get_path_to_media_item_admin(
                Kohana::$config->load('config')->project_media_folder,
                $user['avatar'],
                'avatars'
            );
            $avatar_cache[$user['id']] = $image_path;
        }
        return $avatar_cache[$user['id']];
    }

    public static function get_joined_rooms($user_id, $args = array())
    {
        $q = DB::select(
            'rooms.*',
            'join_users.joined',
            array('invited_by_users.name', 'invited_by'),
            array('join_users.invited_by', 'join_users.invited_by_id'),
            'join_users.invited',
            array('messages.id', 'last_message_id'),
            array('messages.message', 'last_message'),
            array('messages.created', 'last_message_date'),
            DB::expr("IF(read.message_id is null, 0, 1) as last_message_read"),
            array('sender_users.name', 'last_message_sender')
        )
            ->from(array(self::ROOMS_TABLE, 'rooms'))
                ->join(array(self::JOIN_TABLE, 'join_users'), 'inner')->on('rooms.id', '=', 'join_users.room_id')
                ->join(array(Model_Users::MAIN_TABLE, 'invited_by_users'), 'left')->on('join_users.invited_by', '=', 'invited_by_users.id')
                ->join(array(self::MESSAGES_TABLE, 'messages'), 'left')->on('messages.room_id', '=', 'rooms.id')
                ->join(array(self::READ_TABLE, 'read'), 'left')
                    ->on('read.message_id', '=', 'messages.id')
                    ->on('read.user_id', '=', DB::expr($user_id))
                ->join(array(Model_Users::MAIN_TABLE, 'sender_users'), 'inner')->on('messages.user_id', '=', 'sender_users.id')
            ->where('rooms.closed', 'is', null)
            ->and_where('join_users.user_id', '=', $user_id)
            ->and_where('join_users.left', 'is', null);
        
        $q->group_by('rooms.id');
        $q->order_by('rooms.name', 'asc');
        $q->order_by('messages.id', 'desc');

        if (!empty($args['keyword'])) {
            $q->and_where('rooms.name', 'like', '%' . $args['keyword']);
        }

        if (!empty($args['limit'])) {
            $q->limit($args['limit']);
        }

        $rooms = $q->execute()->as_array();

        foreach ($rooms as $i => $room) {
            $rooms[$i]['users'] = DB::select('users.id', 'users.email', 'users.name', 'users.surname', 'users.avatar', 'users.use_gravatar', 'joined_users.joined')
                ->from(array(Model_Users::MAIN_TABLE, 'users'))
                    ->join(array(self::JOIN_TABLE, 'joined_users'), 'inner')
                        ->on('users.id', '=', 'joined_users.user_id')
                ->where('joined_users.room_id', '=', $room['id'])
                ->and_where('joined_users.left', 'is', null)
                ->execute()
                ->as_array();
            if ($room['name'] == '') {
                $joined_users = array();
                foreach ($rooms[$i]['users'] as $j => $user) {
                    if ($user['id'] != $user_id) {
                        $joined_users[] = $user['name'];
                    }
                }
                $rooms[$i]['name'] = implode(', ', $joined_users);
            }
            foreach ($rooms[$i]['users'] as $j => $user) {
                $rooms[$i]['users'][$j]['avatar'] = self::get_avatar($user);
            }
        }

        return $rooms;
    }

    public static function get_room_list($user_id, $args = array())
    {
        $lastq = DB::select(DB::expr('max(id) as last_message_id'), 'room_id')
            ->from(self::MESSAGES_TABLE)
            ->group_by('room_id');
        $unreadq = DB::select(DB::expr("count(*) as unread_count"), 'joined_rooms.room_id')
            ->from([self::JOIN_TABLE, 'joined_rooms'])
                ->join([self::MESSAGES_TABLE, 'messages'], 'inner')->on('joined_rooms.room_id', '=', 'messages.room_id')
            ->and_where('joined_rooms.user_id', '=', $user_id)
            ->and_where('joined_rooms.left', 'is', null)
            ->join([self::READ_TABLE, 'read_messages'], 'left')
            ->on('messages.id', '=', 'read_messages.message_id')
            ->on('read_messages.user_id', '=', 'joined_rooms.user_id')
            ->and_where('read_messages.user_id', 'is', null)
            ->group_by('joined_rooms.room_id');

        $q = DB::select(
            'rooms.*',
            DB::expr("IF(join_users.left is null, join_users.joined, null) as joined"),
            DB::expr("IF(join_users.left is null, invited_by_users.name, null) as invited_by"),
            DB::expr("IF(join_users.left is null, join_users.invited_by, null) as invited_by_id"),
            DB::expr("IF(join_users.left is null, join_users.invited, null) as invited"),
            "join_users.mute",
            array('messages.id', 'last_message_id'),
            array('messages.message', 'last_message'),
            array('messages.created', 'last_message_date'),
            array('last_sender.name', 'last_message_sender'),
            array('last_sender.id', 'last_message_sender_id'),
            array('last_sender.name', 'last_message_sender'),
            array('last_sender.avatar', 'last_message_sender_avatar'),
            array('last_sender.use_gravatar', 'last_message_sender_use_gravatar'),
            DB::expr("IF(read.message_id is null, 0, 1) as last_message_read"),
            DB::expr("IFNULL(um.unread_count, 0) as unread_count")
        )
            ->from(array(self::ROOMS_TABLE, 'rooms'))
            ->join(array(self::JOIN_TABLE, 'join_users'), 'left')->on('rooms.id', '=', 'join_users.room_id')
            ->join(array(Model_Users::MAIN_TABLE, 'invited_by_users'), 'left')->on('join_users.invited_by', '=', 'invited_by_users.id')
            ->join(array($lastq, 'lm'), 'left')->on('rooms.id', '=', 'lm.room_id')
            ->join(array(self::MESSAGES_TABLE, 'messages'), 'left')->on('lm.last_message_id', '=', 'messages.id')
            ->join(array(Model_Users::MAIN_TABLE, 'last_sender'), 'left')->on('messages.user_id', '=', 'last_sender.id')
            ->join(array(self::READ_TABLE, 'read'), 'left')
                ->on('read.message_id', '=', 'messages.id')
                ->on('read.user_id', '=', DB::expr($user_id))
            ->join(array($unreadq, 'um'), 'left')->on('rooms.id', '=', 'um.room_id')
            ->where('rooms.closed', 'is', null)
            ->and_where_open()
                ->or_where_open()
                    ->and_where('join_users.user_id', '=', $user_id)
                    ->and_where('join_users.left', 'is', null)
                ->or_where_close()
                ->or_where('rooms.is_public', '=', 1)
            ->and_where_close();

        $q->group_by('rooms.id');
        $q->order_by('last_message_id', 'desc');

        if (!empty($args['keyword'])) {
            $q->and_where('rooms.name', 'like', '%' . $args['keyword']);
        }

        if (!empty($args['limit'])) {
            $q->limit($args['limit']);
        }

        $rooms = $q->execute()->as_array();

        foreach ($rooms as $i => $room) {
            $rooms[$i]['last_message_sender_avatar'] = self::get_avatar(array('avatar' => $room['last_message_sender_avatar'], 'use_gravatar' => $room['last_message_sender_use_gravatar']));
            unset($rooms[$i]['last_message_sender_use_gravatar']);
            $rooms[$i]['users'] = DB::select('users.id', 'users.email', 'users.name', 'users.surname', 'users.avatar', 'users.use_gravatar', 'joined_users.joined')
                ->from(array(Model_Users::MAIN_TABLE, 'users'))
                ->join(array(self::JOIN_TABLE, 'joined_users'), 'inner')
                ->on('users.id', '=', 'joined_users.user_id')
                ->where('joined_users.room_id', '=', $room['id'])
                ->and_where('joined_users.left', 'is', null)
                ->execute()
                ->as_array();
            if ($room['name'] == '') {
                $joined_users = array();
                foreach ($rooms[$i]['users'] as $j => $user) {
                    if ($user['id'] != $user_id) {
                        $joined_users[] = $user['name'];
                    }
                }
                $rooms[$i]['name'] = implode(', ', $joined_users);
            }
            foreach ($rooms[$i]['users'] as $j => $user) {
                $rooms[$i]['users'][$j]['avatar'] = self::get_avatar($user);
            }
        }

        return $rooms;
    }

    public static function room_join_user($room_id, $user_id, $ask, $invited_by = null)
    {
        $exists = DB::select('*')
            ->from(array(self::JOIN_TABLE, 'rooms'))
            ->where('room_id', '=', $room_id)
            ->and_where('user_id', '=', $user_id)
            ->and_where('left', 'is', null)
            ->execute()
            ->current();

        if (!$exists) {
            DB::insert(self::JOIN_TABLE)
                ->values(
                    array(
                        'user_id' => $user_id,
                        'room_id' => $room_id,
                        'joined' => $ask ? null : date::now(),
                        'invited_by' => $invited_by,
                        'invited' => date::now()
                    )
                )
                ->execute();
        } else {
            DB::update(self::JOIN_TABLE)
                ->set(array('joined' => $ask ? null : date::now()))
                ->where('room_id', '=', $room_id)
                ->and_where('user_id', '=', $invited_by)
                ->and_where('left', 'is', null)
                ->execute();
        }
    }

    public static function room_leave_user($room_id, $user_id)
    {
        DB::update(self::JOIN_TABLE)
            ->set(array('left' => date::now()))
            ->where('room_id', '=', $room_id)
            ->and_where('user_id', '=', $user_id)
            ->and_where('left', 'is', null)
            ->execute();
    }

    public static function room_set_mute($room_id, $user_id, $mute)
    {
        DB::update(self::JOIN_TABLE)
            ->set(array('mute' => $mute))
            ->where('room_id', '=', $room_id)
            ->and_where('user_id', '=', $user_id)
            ->execute();
    }

    public static function room_set_allow_reply($room_id, $user_id, $allow)
    {
        $creator = DB::select('created_by')
            ->from(self::ROOMS_TABLE)
            ->where('id', '=', $room_id)
            ->execute()
            ->get('created_by');
        if ($creator != $user_id) {
            return false;
        } else {
            DB::update(self::ROOMS_TABLE)
                ->set(array('allow_reply' => $allow))
                ->where('id', '=', $room_id)
                ->execute();
            return true;
        }
    }

    public static function create_room($name, $is_public, $user_id, $allow_reply = 1)
    {
        if ($is_public) {
            $exists = DB::select('*')
                ->from(array(self::ROOMS_TABLE, 'rooms'))
                ->where('name', '=', $name)
                ->execute()
                ->current();
        } else {
            $exists = false;
        }
        if ($exists) {
            DB::update(self::ROOMS_TABLE)->set(array('closed' => null, 'allow_reply' => $allow_reply))->where('id', '=', $exists['id'])->execute();
            return $exists['id'];
        } else {
            $inserted = DB::insert(self::ROOMS_TABLE)
                ->values(array(
                    'name' => $name,
                    'is_public' => $is_public,
                    'created_by' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'allow_reply' => $allow_reply
                ))
                ->execute();
            self::room_join_user($inserted[0], $user_id, false);
            return $inserted[0];
        }
    }

    public static function check_private_room($user_id_1, $user_id_2)
    {
        $room = DB::select('rooms.*')
            ->from(array(self::ROOMS_TABLE, 'rooms'))
                ->join(array(self::JOIN_TABLE, 'joined_users'), 'inner')->on('rooms.id', '=', 'joined_users.room_id')
            ->where('rooms.is_public', '=', 0)
            ->and_where('rooms.deleted', '=', 0)
            ->and_where_open()
                ->or_where_open()
                    ->and_where('rooms.created_by', '=', $user_id_1)
                    ->and_where('joined_users.user_id', '=', $user_id_2)
                ->or_where_close()
                ->or_where_open()
                    ->and_where('rooms.created_by', '=', $user_id_2)
                    ->and_where('joined_users.user_id', '=', $user_id_1)
                ->or_where_close()
            ->and_where_close()
            ->execute()
            ->current();
        return $room;
    }

    public static function clear_room_left($room_id)
    {
        DB::update(self::ROOMS_TABLE)->set(array('closed' => null))->where('id', '=', $room_id)->execute();
        DB::delete(self::LEAVE_TABLE)
            ->where('room_id', '=', $room_id)
            ->execute();
    }

    public static function create_message($room_id, $text, $user_id)
    {
        $room = DB::select('*')
            ->from(self::ROOMS_TABLE)
            ->where('id', '=', $room_id)
            ->execute()
            ->current();
        if ($room['created_by'] != $user_id && $room['allow_reply'] == 0) {
            return false;
        }

        $inserted = DB::insert(self::MESSAGES_TABLE)
            ->values(
                array(
                    'room_id' => $room_id,
                    'message' => $text,
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s')
                )
            )
            ->execute();
        DB::insert(self::READ_TABLE)
            ->values(
                [
                    'user_id' => $user_id,
                    'message_id' => $inserted[0]
                ]
            )
            ->execute();
        return $inserted[0];
    }

    public static function get_list_messages($user_id, $room_id = null, $unread = true, $offset = 0, $limit = 100, $display_archived = false)
    {
        $messagesq = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS messages.*'),
            'users.email',
            array('users.name', 'username'),
            'users.avatar',
            'users.use_gravatar',
            DB::expr("IF(read.user_id IS NULL, 0, 1) as `read`")
        )
            ->from(array(self::MESSAGES_TABLE, 'messages'))
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'inner')->on('messages.user_id', '=', 'users.id')
                ->join(array(self::ROOMS_TABLE, 'rooms'), 'inner')->on('messages.room_id', '=', 'rooms.id')
                ->join(array(self::JOIN_TABLE, 'joined_users'), 'inner')->on('rooms.id', '=', 'joined_users.room_id')
                    ->on('rooms.id', '=', 'joined_users.room_id')
                ->join(array(self::READ_TABLE, 'read'), 'left')
                    ->on('messages.id', '=', 'read.message_id')
                    ->on('read.user_id', '=', DB::expr($user_id))
                ->join(array(self::ARCHIVE_TABLE, 'archive'), 'left')
                    ->on('messages.id', '=', 'archive.message_id')
                    ->on('archive.user_id', '=', DB::expr($user_id))
            ->where('joined_users.user_id', '=', $user_id);

        if ($room_id) {
            $messagesq->and_where('messages.room_id', '=', $room_id);
        }

        if ($unread) {
            $messagesq->and_where('read.user_id', 'is', null);
        }

        if (!$display_archived) {
            $messagesq->and_where('archive.message_id', 'is', null);
        }

        if ($offset) {
            $messagesq->offset($offset);
        }
        if ($limit > 0) {
            $messagesq->limit($limit);
        }

        $messages = $messagesq->order_by('messages.id', 'asc')
            ->execute()
            ->as_array();

        $total = DB::select(DB::expr("FOUND_ROWS() as total"))->execute()->get('total');

        $avatar_cache = array();

        foreach ($messages as $i => $message) {
            if (!isset($avatar_cache[$message['user_id']])) {
                $avatar_cache[$message['user_id']] = self::get_avatar(array('id' => $message['user_id'], 'email' => $message['email'], 'avatar' => $message['avatar'], 'use_gravatar' => $message['use_gravatar']));
            }
            $messages[$i]['avatar'] = $avatar_cache[$message['user_id']];

        }

        return array('total' => $total, 'messages' => $messages);
    }

    public static function get_unread_messages($user_id, $all = false, $include_message_id = null)
    {
        $messagesq = DB::select(
            'messages.*',
            'users.email',
            array('users.name', 'username'),
            'users.avatar',
            'users.use_gravatar',
            DB::expr("IF(read.user_id IS NULL, 0, 1) as `read`")
        )
            ->distinct('messages.id')
            ->from(array(self::MESSAGES_TABLE, 'messages'))
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'inner')->on('messages.user_id', '=', 'users.id')
                ->join(array(self::ROOMS_TABLE, 'rooms'), 'inner')->on('messages.room_id', '=', 'rooms.id')
                ->join(array(self::JOIN_TABLE, 'joined_users'), 'inner')->on('rooms.id', '=', 'joined_users.room_id')
                ->join(array(self::READ_TABLE, 'read'), 'left')
                    ->on('messages.id', '=', 'read.message_id')
                    ->on('read.user_id', '=', DB::expr($user_id));
        if ($include_message_id) {
            $messagesq->or_where('messages.id', '=', $include_message_id);
        } else {
            $messagesq->where('joined_users.user_id', '=', $user_id)
                ->and_where('joined_users.left', 'is', null);
            if (!$all) {
                $messagesq->and_where('read.user_id', 'is', null);
            }
        }
        $messages = $messagesq->order_by('messages.id', 'asc')
            ->execute()
            ->as_array();

        foreach ($messages as $i => $message) {
            $messages[$i]['avatar'] = self::get_avatar(array('id' => $message['user_id'], 'email' => $message['email'], $message['avatar'], 'use_gravatar' => $message['use_gravatar']));
        }
        return $messages;
    }

    public static function set_read_messages($user_id, $message_ids)
    {
        foreach ($message_ids as $message_id) {
            $read = DB::select(DB::expr("count(*) as cnt"))
                ->from(self::READ_TABLE)
                ->where('user_id', '=', $user_id)
                ->and_where('message_id', '=', $message_id)
                ->execute()
                ->get('cnt');
            if ($read == 0) {
                DB::insert(self::READ_TABLE)
                    ->values(array('user_id' => $user_id, 'message_id' => $message_id))
                    ->execute();
            }
        }
    }

    public static function set_unread_messages($user_id, $message_ids)
    {
        DB::delete(self::READ_TABLE)
            ->where('user_id', '=', $user_id)
            ->and_where('message_id', 'in', $message_ids)
            ->execute();
    }

    public static function archive_messages($user_id, $message_ids)
    {
        foreach ($message_ids as $message_id) {
            $read = DB::select(DB::expr("count(*) as cnt"))
                ->from(self::ARCHIVE_TABLE)
                ->where('user_id', '=', $user_id)
                ->and_where('message_id', '=', $message_id)
                ->execute()
                ->get('cnt');
            if ($read == 0) {
                DB::insert(self::ARCHIVE_TABLE)
                    ->values(array('user_id' => $user_id, 'message_id' => $message_id))
                    ->execute();
            }
        }
    }

    public static function unarchive_messages($user_id, $message_ids)
    {
        DB::delete(self::ARCHIVE_TABLE)
            ->where('user_id', '=', $user_id)
            ->and_where('message_id', 'in', $message_ids)
            ->execute();
    }

    public static function get_online_users($user_id, $term = null)
    {
        $logged_in_user = Auth::instance()->get_user();

        $searchq = DB::select(
            'users.id',
            'users.email',
            'users.name',
            'users.surname',
            'users.avatar',
            'users.use_gravatar',
            array('users.id', 'value'),
            array('users.email', 'label'),
            DB::expr("IF(onlines.user_id IS NOT NULL, 1, 0) AS online")
        )
            ->from(array(Model_Users::MAIN_TABLE, 'users'))
                ->join(array('engine_role_permissions', 'has_permissions'), 'inner')
                    ->on('users.role_id', '=', 'has_permissions.role_id')
                ->join(array('engine_resources', 'resources'), 'inner')
                    ->on('has_permissions.resource_id', '=', 'resources.id')
                ->join(array(self::ONLINE_TABLE, 'onlines'), 'left')->on('users.id', '=', 'onlines.user_id')
            ->where('users.deleted', '=', 0)
            ->and_where('resources.alias', '=', 'chat')
            ->order_by('email');

        if ($term) {
            $searchq->and_where_open()
                ->or_where('users.email', 'like', '%' . $term . '%')
                ->or_where('users.name', 'like', '%' . $term . '%')
                ->or_where('users.surname', 'like', '%' . $term . '%')
                ->and_where_close();
        }

        $users = $searchq
            ->execute()
            ->as_array();

        foreach ($users as $i => $user) {
            $room = self::check_private_room($logged_in_user['id'], $user['id']);
            $users[$i]['avatar']  = self::get_avatar(array('id' => $user['id'], 'email' => $user['email'], 'avatar' => $user['avatar'], 'use_gravatar' => $user['use_gravatar']));
            $users[$i]['online']  = ($users[$i]['online'] == 1);
            $users[$i]['room_id'] = isset($room['id']) ? $room['id'] : '';
        }

        return $users;
    }

    public static function update_online_list()
    {
        $user = Auth::instance()->get_user();
        if ($user['id']) {
            if (Request::$initial->action() == 'logout') {
                DB::delete(self::ONLINE_TABLE)->where('user_id', '=', $user['id'])->execute();
            } else {
                $chat_offline_after_minutes = Settings::instance()->get('chat_offline_after_minutes');
                $now = date::now();
                $remove = date('Y-m-d H:i:s', strtotime('-' . $chat_offline_after_minutes . ' minute'));
                DB::delete(self::ONLINE_TABLE)->where('last_actioned', '<', $remove)->execute();
                DB::query(null,
                    'REPLACE INTO ' . self::ONLINE_TABLE . ' SET user_id=' . $user['id'] . ', last_actioned="' . $now . '"')->execute();
            }
        }
    }

    public static function stats($user_id)
    {
        $stats = [];
        $select = DB::select(DB::expr("count(*) as unread_count"))
            ->from([self::JOIN_TABLE, 'joined_rooms'])
                ->join([self::MESSAGES_TABLE, 'messages'], 'inner')->on('joined_rooms.room_id', '=', 'messages.room_id');
            $select->and_where('joined_rooms.user_id', '=', $user_id)
                ->and_where('joined_rooms.left', 'is', null)
                ->join([self::READ_TABLE, 'read_messages'], 'left')
                    ->on('messages.id', '=', 'read_messages.message_id')
                    ->on('read_messages.user_id', '=', 'joined_rooms.user_id')
                ->and_where('read_messages.user_id', 'is', null);
        $stats['unread_message_count'] = $select->execute()->get('unread_count');
        return $stats;
    }
}