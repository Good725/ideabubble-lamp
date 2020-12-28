<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Todos extends Controller_Api
{
    protected $auth;
    protected $user;

    public function before()
    {
        parent::before();

        $this->auth = Auth::instance();
        $this->user = $this->auth->get_user();
    }

    public function action_list()
    {
        if (!$this->auth->has_access('todos')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $params = array();
        if (@$get['keyword']) {
            //$params['keyword'] = $get['keyword'];
            $params['sSearch'] = $get['keyword'];
        }
        if (@$get['schedule_id']) {
            $params['schedule_id'] = $get['schedule_id'];
        }
        if (@$get['course_id']) {
            $params['course_id'] = $get['course_id'];
        }
        if (@$get['trainer_id']) {
            $params['trainer_id'] = $get['trainer_id'];
        }
        if (@$get['student_id']) {
            $params['student_id'] = $get['student_id'];
        }
        if (@$get['contact_id']) {
            $params['contact_id'] = $get['contact_id'];
        }
        if (@$get['location_id']) {
            $params['location_id'] = $get['location_id'];
        }
        if (@$get['building_id']) {
            $params['building_id'] = $get['building_id'];
        }
        if (@$get['room_id']) {
            $params['room_id'] = $get['room_id'];
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }
        if (@$get['offset']) {
            $params['offset'] = $get['offset'];
        }
        if (@$get['limit']) {
            $params['limit'] = $get['limit'];
        } else {
            $params['limit'] = 100;
        }

        $types = array();
        $settings = Settings::instance();
        if ($settings->get('todos_api_display_tasks') == 1) {
            $types[] = 'Task';
        }
        if ($settings->get('todos_api_display_assignments') == 1) {
            $types[] = 'Assignment';
        }
        if ($settings->get('todos_api_display_assesments') == 1) {
            $types[] = 'Term-Assessment';
        }
        if ($settings->get('todos_api_display_tests') == 1) {
            $types[] = 'Class-Test';
        }
        if ($settings->get('todos_api_display_exams') == 1) {
            $types[] = 'State-Exam';
        }

        if (!$this->auth->has_access('todos_manage_all')) {
            $user = $this->auth->get_user();
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            if (count($contacts) > 0) {
                $contact = new Model_Contacts3($contacts[0]['id']);
                if ($contact->get_is_primary()) {
                    $family_id = $contact->get_family_id();
                    $family = new Model_Family($family_id);
                    $members = $family->get_members();
                    $params['student_id'] = array();
                    foreach ($members as $member) {
                        $params['student_id'][] = $member->get_id();
                    }
                    $params['contact_id'] = $contact->get_id();
                } else {
                    $params['student_id'] = array($contacts[0]['id']);
                }
            }
        }

        $todos = Model_Todos::search($params);
        foreach ($todos as $i => $todo) {
            $todos[$i]['user_avatars'] = array();
            $todos[$i]['created_by_avatar'] = URL::get_avatar($todo['created_by'], true);
            if ($todo['assignee_user_ids'] != '') {
                $user_ids = explode(',', $todo['assignee_user_ids']);
                foreach ($user_ids as $j => $auser_id) {
                    $todos[$i]['user_avatars'][$j] = URL::get_avatar($auser_id, true);
                }
            }
        }
        $this->response_data['total'] = (int)DB::select(DB::expr('@found_todos as total'))->execute()->get('total');
        $this->response_data['limit'] = $params['limit'];
        $this->response_data['todos'] = $todos;

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_details()
    {
        if (!$this->auth->has_access('todos')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $id = $this->request->query('id');
        $todos = new Model_Todos();
        $todo = Model_Todos::get($id, true);
        $todo['created_by_avatar'] = URL::get_avatar($todo['created_by'], true);
        if (count($todo['has_assigned_contacts']) > 0) {
            foreach ($todo['has_assigned_contacts'] as $i => $contact) {
                $todo['has_assigned_contacts'][$i]['avatar'] = URL::get_avatar($contact['user_id'], true);
            }
        }
        $todo['content'] = null;
        if ($todo['content_id']) {
            $todo['content'] = new Model_Content($todo['content_id']);

            $type = $todo['content']->type->name;
            $todo['content'] = $todo['content']->as_array();
            $todo['content']['text'] = str_replace("href='/", "href='" . URL::site(), $todo['content']['text']);
            $todo['content']['text'] = str_replace('href="/', 'href="' . URL::site(), $todo['content']['text']);
            $todo['content']['text'] = str_replace('src="/', 'src="' . URL::site(), $todo['content']['text']);
            $todo['content']['type'] = $type;
            $todo['content']['children'] = Model_Content::get_children_tree($todo['content_id']);
        }
        $this->response_data['todo'] = $todo;

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }

    public function action_edit()
    {

        if (!$this->auth->has_access('todos')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->post();
        $id = @$post['id'];

        $m_todo = new Model_Todos();
        if (is_numeric($id) && !$this->auth->has_access('todos_manage_all')) {

            $data = $m_todo->get_todo($id, $this->user['id']);
            if (!$data) {
                $this->response_data['success'] = 0;
                $this->response_data['msg'] = 'Access Denied';
                return;
            }
        }

        $todo = array();
        $todo['title'] = $post['title'];
        $todo['details'] = $post['details'];
        if (!is_numeric($id)) {
            $todo['from_user_id'] = $this->user['id'];
        }
        $todo['status_id'] = $post['status_id'];
        $todo['priority_id'] = $post['priority_id'];
        $todo['type_id'] = 'Task';
        $todo['due_date'] = date::ymd_to_dmy($post['due_date']);
        $todo['to_user_id'] = $post['to_user_id'];
        $todo['related_to_text'] = '';

        try {
            if (is_numeric($id)) {
                $m_todo->update_todo($id, $todo);
            } else {
                $m_todo->add_todo($todo, 7, '');
            }

            $this->response_data['success'] = 1;
            $this->response_data['msg'] = 'Todo Saved';
        } catch (Exception $exc) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Unexpected Error' . $exc->getMessage();
        }
    }

    public function action_users()
    {
        Model_Users::get_visible_users();
        if (!$this->auth->has_access('todos_manage_all')) {
            $users = Model_Users::search(array('role_id' => $this->user['role_id']));
        } else {
            $users = Model_Users::search();
        }
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['users'] = $users;
    }
}
