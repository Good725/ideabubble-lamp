<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Api_Chat extends Controller_Api
{
    public function action_user_list()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $auth = Auth::instance();
        $user = $auth->get_user();
        $keyword = $this->request->query('keyword');

        $query = DB::select('users.id', 'users.email', 'users.use_gravatar', 'users.avatar', 'roles.role', 'contacts.first_name', 'contacts.last_name')
            ->from(array(Model_Users::MAIN_TABLE, 'users'))
            ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'inner')->on('users.role_id', '=', 'roles.id')
            ->join(array(Model_Chat::ROLE_INVITE_TABLE, 'can_invites'), 'inner')
            ->on('users.role_id', '=', 'can_invites.can_invite_role_id')
            ->where('users.deleted', '=', 0)
            ->and_where('can_invites.role_id', '=', $user['role_id']);
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $query->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                ->on('users.id', '=', 'contacts.linked_user_id');
        } else {
            $query->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'left')
                ->on('users.id', '=', 'contacts.linked_user_id');
        }
        if (is_array($this->request->query('roles'))) {
            $query->and_where('roles.role', 'in', $this->request->query('roles'));
        }
        if ($keyword) {
            $query->and_where_open()
                ->or_where('users.email', 'like', '%' . $keyword . '%')
                ->or_where(DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name)"), 'like', '%' . $keyword . '%')
                ->and_where_close();
        }
        $query->order_by('roles.role');
        $query->order_by('contacts.first_name');
        $query->order_by('contacts.last_name');
        $query->order_by('users.email');
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['users'] = $query
            ->execute()
            ->as_array();

        foreach ($this->response_data['users'] as $i => $user) {
            $this->response_data['users'][$i]['avatar'] = Model_Chat::get_avatar($user);
        }
    }

    public function action_room_list()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $keyword = $this->request->query('keyword');

        $user = Auth::instance()->get_user();
        $rooms = Model_Chat::get_room_list($user['id'], array('keyword' => $keyword));

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['rooms'] = $rooms;
    }

    public function action_room_create()
    {
        if (!Auth::instance()->has_access('api_chat_create_room')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $is_public = @$post['is_public'] ? 1 : 0;
        $name = @$post['name'];
        $allow_reply = isset($post['allow_reply']) ? $post['allow_reply'] : 1;
        $user_ids = @$post['invite_user_id'];

        $room_id = Model_Chat::create_room($name, $is_public, $user['id'], $allow_reply);
        if ($user_ids)
        foreach ($user_ids as $user_id) {
            Model_Chat::room_join_user($room_id, $user_id, true, $user['id']);
        }


        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['room_id'] = $room_id;
    }

    public function action_room_join()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $user_id = @$post['user_id'] ?: $user['id'];
        Model_Chat::room_join_user($room_id, $user_id, false, $user['id']);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_room_invite()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $user_ids = $post['user_id'];
        foreach ($user_ids as $user_id) {
            Model_Chat::room_join_user($room_id, $user_id, true, $user['id']);
        }

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_room_leave()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $user_id = @$post['user_id'] ?: $user['id'];
        Model_Chat::room_leave_user($room_id, $user_id);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_room_set_allow_reply()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $user_id = $user['id'];
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $allow_reply = $post['allow_reply'];
        $success = Model_Chat::room_set_allow_reply($room_id, $user_id, $allow_reply) ? 1 : 0;

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_room_mute()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $user_id = @$post['user_id'] ?: $user['id'];
        Model_Chat::room_set_mute($room_id, $user_id, 1);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_room_unmute()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $user_id = @$post['user_id'] ?: $user['id'];
        Model_Chat::room_set_mute($room_id, $user_id, 0);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_message_send()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $room_id = $post['room_id'];
        $message = $post['message'];
        $message_id = Model_Chat::create_message($room_id, $message, $user['id']);

        $this->response_data['success'] = $message_id ? 1 : 0;
        $this->response_data['msg'] = '';
        if ($message_id) {
            $this->response_data['message_id'] = $message_id;
        }
    }

    public function action_message_list()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $get = $this->request->query();
        $room_id = $get['room_id'];
        $unread = $get['unread'];
        $offset = @$get['offset'];
        $limit = @$get['limit'];
        $display_archived = (int)@$get['display_archived'] == 1;
        $data = Model_Chat::get_list_messages($user['id'], $room_id, $unread, $offset, $limit, $display_archived);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['data'] = $data;
    }

    public function action_message_mark_read()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $message_ids = $post['message_id'];
        Model_Chat::set_read_messages($user['id'], $message_ids);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_message_mark_unread()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $message_ids = $post['message_id'];
        Model_Chat::set_unread_messages($user['id'], $message_ids);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_message_archive()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $message_ids = $post['message_id'];
        Model_Chat::archive_messages($user['id'], $message_ids);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_message_unarchive()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $message_ids = $post['message_id'];
        Model_Chat::unarchive_messages($user['id'], $message_ids);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_poll()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        session_commit();
        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['rooms'] = Model_Chat::get_joined_rooms($user['id']);
        $this->response_data['messages'] = $data = Model_Chat::get_list_messages($user['id']);
    }

    public function action_stats()
    {
        if (!Auth::instance()->has_access('api_chat')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = __('Permission Denied');
            return;
        }

        session_commit();
        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['stats'] = Model_Chat::stats($user['id']);
    }
}