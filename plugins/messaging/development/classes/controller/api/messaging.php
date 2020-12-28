<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Api_Messaging extends Controller_Api
{
    public function action_send()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();

        $post = $this->request->post();
        $recipients = @$post['recipients'];
        $driver = @$post['driver'] ?: 'dashboard';
        $provider = @$post['provider'] ?: 'system';
        $message = @$post['message'] ?: '';
        $subject = @$post['subject'] ?: '';
        $reply_to_message_id = @$post['reply_to_message_id'];
        $from = $user['id'];
        $targets = array();
        foreach ($recipients as $recipient) {
            if (@$recipient['user_id']) {
                $targets[] = array(
                    'target_type' => 'CMS_USER',
                    'target' => $recipient['user_id']
                );
            } else {
                $targets[] = $recipient;
            }
        }

        if (@$post['schedule_id']) {
            $students = Model_KES_Bookings::rollcall_list(array('schedule_id' => $post['schedule_id']));
            $added = array();
            foreach ($students as $student) {
                if (!@$added[$student['user_id']] && $student['user_id']) {
                    $targets[] = array(
                        'target_type' => 'CMS_USER',
                        'target' => $student['user_id']
                    );
                    $added[$student['user_id']] = $student['user_id'];
                }
            }
        }

        if (@$post['timeslot_id']) {
            $students = Model_KES_Bookings::rollcall_list(array('timeslot_id' => $post['timeslot_id']));
            $added = array();
            foreach ($students as $student) {
                if (!@$added[$student['user_id']] && $student['user_id']) {
                    $targets[] = array(
                        'target_type' => 'CMS_USER',
                        'target' => $student['user_id']
                    );
                    $added[$student['user_id']] = $student['user_id'];
                }
            }
        }

        $mm = new Model_Messaging();
        $id = $mm->send(
            $driver,
            $provider,
            $from,
            $targets,
            $message,
            $subject,
            null, 0, 'new', array(), null,
            $reply_to_message_id
        );

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['message_id'] = $id;
    }

    public function action_read()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        $message_id = (int)$this->request->query('id');
        $mm = new Model_Messaging();
        $message = $mm->get_message_details($message_id);
        DB::query(null,
            "UPDATE
                plugin_messaging_messages
                  INNER JOIN plugin_messaging_message_targets ON plugin_messaging_message_targets.message_id = plugin_messaging_messages.id
                  INNER JOIN plugin_messaging_message_final_targets ON plugin_messaging_message_final_targets.target_id = plugin_messaging_message_targets.id
                SET plugin_messaging_message_final_targets.delivery_status='READ'
                WHERE plugin_messaging_messages.id = $message_id")
            ->execute();

        $message['replies'] = array();
        $reply_to_message_ids = array($message_id);
        while (count($reply_to_message_ids) > 0) {
            $reply_to_message_id = array_pop($reply_to_message_ids);
            $replies = DB::select('m.*', DB::expr('IFNULL(u.name, m.sender) as sender_d'))
                    ->from(array('plugin_messaging_messages', 'm'))
                        ->join(array('engine_users', 'u'), 'left')->on('m.sender', '=', 'u.id')
                ->where('reply_to_message_id', '=', $reply_to_message_id)
                ->execute()
                ->as_array();
            foreach ($replies as $reply) {
                $message['replies'][] = array(
                    'id' => $reply['id'],
                    'subject' => $reply['subject'],
                    'message' => $reply['message'],
                    'date_created' => $reply['date_created'],
                    'sender' => $reply['sender_d'] ?: $reply['sender'],
                    'allow_reply' => $reply['allow_reply']
                );
                $reply_to_message_ids[] = $reply['id'];
            }
        }
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['message'] = array(
            'id' => $message['id'],
            'subject' => $message['subject'],
            'message' => $message['message'],
            'from' => $message['sender'],
            'allow_reply' => $message['allow_reply'],
            'replies' => $message['replies']
        );
    }

    public function action_listsent()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        $keyword = $this->request->query('keyword');
        $driver = $this->request->query('driver') ?: 'dashboard';

        $mm = new Model_Messaging();
        $messages = $mm->search_messages(
            array(
                'driver' => $driver,
                'sender' => $user['id'],
                'sent' => 1,
                'search' => $keyword,
                'iDisplayLength' => $this->request->query('limit'),
                'iDisplayLength' => $this->request->query('offset'),
                'driver' => $driver
            )
        );

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['messages'] = array();
        foreach ($messages as $message) {
            $this->response_data['messages'][] = array(
                'id' => $message['id'],
                'subject' => $message['subject'],
                'message' => $message['message'],
                'from' => $message['from']
            );
        }
    }

    public function action_listreceived()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        $keyword = $this->request->query('keyword');
        $driver = $this->request->query('driver') ?: 'dashboard';

        $mm = new Model_Messaging();
        $messages = $mm->search_messages(
            array(
                'driver' => $driver,
                'target' => $user['id'],
                'target_type' => 'CMS_USER',
                'received' => 1,
                'search' => $keyword,
                'iDisplayLength' => $this->request->query('limit'),
                'iDisplayLength' => $this->request->query('offset'),
                'driver' => $driver
            )
        );

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['messages'] = array();
        foreach ($messages as $message) {
            $this->response_data['messages'][] = array(
                'id' => $message['id'],
                'subject' => $message['subject'],
                'message' => $message['message'],
                'from' => $message['from'],
                'read' => $message['delivery_status'] == 'READ' ? 1 : 0
            );
        }
    }

    public function action_userlist()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        $keyword = $this->request->query('keyword');

        $query = DB::select('users.id', 'users.email', 'roles.role', 'contacts.first_name', 'contacts.last_name')
            ->from(array(Model_Users::MAIN_TABLE, 'users'))
            ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'inner')->on('users.role_id', '=', 'roles.id')
            ->join(array(Model_Messaging::SEND_ROLE_TABLE, 'can_roles'), 'inner')
                ->on('users.role_id', '=', 'can_roles.can_send_to_role_id')
            ->where('users.deleted', '=', 0)
            ->and_where('can_roles.role_id', '=', $user['role_id']);
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
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['users'] = $query
            ->execute()
            ->as_array();
    }
}