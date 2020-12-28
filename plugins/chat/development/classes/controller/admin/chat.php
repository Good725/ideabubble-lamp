<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Chat extends Controller_Cms
{

    public function action_get_data()
    {
        session_commit();
        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $data = array();
        $include_message_id = null;
        if (isset($post['action'])) {
            if ($post['action']['action'] == 'room') {
                $room_id = null;
                if ($post['action']['is_public'] == 0) {
                    $existing_room = Model_Chat::check_private_room($user['id'], $post['action']['join'][0]);
                    $data['existing_room'] = $existing_room;
                    if ($existing_room) {
                        $room_id = $existing_room['id'];
                        Model_Chat::clear_room_left($room_id);
                    }
                }
                if (!$room_id) {
                    $room_id = Model_Chat::create_room($post['action']['room'], $post['action']['is_public'], $user['id']);
                    if (@$post['action']['join']) {
                        foreach ($post['action']['join'] as $user_id) {
                            Model_Chat::room_join_user($room_id, $user_id, true);
                        }
                    }
                }
                $data['room_id'] = $room_id;
            }

            if ($post['action']['action'] == 'message') {
                $include_message_id = Model_Chat::create_message($post['action']['room_id'], $post['action']['text'], $user['id']);
            }

            if ($post['action']['action'] == 'leave') {
                Model_Chat::room_leave_user($post['action']['room_id'], $user['id']);
            }

            if ($post['action']['action'] == 'join') {
                Model_Chat::room_join_user($post['action']['room_id'], $user['id'], false);
            }
        }
        $load_all_messages = !empty($post['load_all_messages']);
        $data['users'] = Model_Chat::get_online_users($user['id']);
        $data['messages'] = Model_Chat::get_unread_messages($user['id'], $load_all_messages, $include_message_id);
        $data['rooms'] = Model_Chat::get_room_list($user['id']);
        $data['post'] = $post;

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);

        ignore_user_abort(true);
        set_time_limit(0);
        $read_messages = array();
        foreach ($data['messages'] as $message) {
            if ($message['read'] == 0 && !empty($post['open_room']) && $message['room_id'] == $post['open_room']) {
                $read_messages[] = $message['id'];
            }
        }

        if (!$load_all_messages) {
            Model_Chat::set_read_messages($user['id'], $read_messages);
        }
    }

    public function action_user_search()
    {
        $user = Auth::instance()->get_user();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $users = Model_Chat::get_online_users($user['id'], $this->request->query('term'));
        echo json_encode($users);

    }
}