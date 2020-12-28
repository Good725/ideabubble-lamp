<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Content extends Controller_Cms
{
    protected $_plugin = 'content';

    protected $_crud_items = [
        'content' => [
            'name' => 'content',
            'model' => 'Content',
            'delete_permission' => 'pages', // Temporary. This should be given its own permission.
            'edit_permission'   => 'pages', // Temporary. This should be given its own permission.
        ]
    ];

    public function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
    }


    public function action_index()
    {
        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home', 'link' => '/admin/'],
            ['name' => 'Content', 'link' => '/admin/content']
        ];

        $this->template->sidebar->tools = '<a href="/admin/content/edit" class="btn btn-primary">Add content</a>';

        $this->template->body = View::factory('iblisting')->set([
            'columns'       => ['ID', 'Name', 'Created', 'Updated', 'Actions'],
            'daterangepicker' => true,
            'default_order' => 'Updated',
            'plugin'        => 'content',
            'type'          => 'content'
        ]);
    }


    public function action_edit()
    {
        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home', 'link' => '/admin/'],
            ['name' => 'Content', 'link' => '/admin/content']
        ];

        $content = new Model_Content($this->request->param('id'));

        $this->template->body = $content->render_editor(['full_form' => true]);
    }

    public function action_save()
    {
        $content = new Model_Content($this->request->param('id'));
        $content->values($this->request->post());
        $content->save();

        IbHelpers::set_message('Content has been saved', 'success popup_box');
        if ($this->request->post('redirect') == 'save_and_exit') {
            $this->request->redirect('/admin/content');
        } else {
            $this->request->redirect('admin/content/edit/'.$content->id);
        }

    }

    public function action_ajax_add_content()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $parent_id = $this->request->query('parent_id');

        try {
            // When inserting the very first Level 1 item, there will be no parent.
            // Create a dummy one, so we have a common parent_id to link all the Level 1 items.
            if (!$parent_id) {
                $content = new Model_Content();
                $content->name = 'Unnamed';
                $content->save_with_moddate();
                $parent_id = $content->id;
            }

            // Save this item
            $content = new Model_Content();
            $content->name = $this->request->query('name');
            $content->parent_id = $parent_id;
            $content->order = $this->request->query('order');
            $content->save_with_moddate();

            $return = [
                'parent_id' => $parent_id,
                'success'   => true,
                'message'   => $content->name.' has been saved.',
                'html'      => View::factory('admin/snippets/content_tree_topic')->set('topic', $content)->set('depth', $this->request->query('depth'))->render()
            ];

        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error adding content.\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return = [
                'success' => false,
                'message' => 'Error adding content. If this problem continues, please ask an administrator to check the error logs.'
            ];
        }

        echo json_encode($return);
    }

    public function action_ajax_save_content()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        try {
            $id   = $this->request->param('id');
            $post = $this->request->post();
            if (isset($post['duration'])) {
                $post['duration'] = IbHelpers::duration_to_seconds($post['duration']);
            }
            if (isset($post['file_id']) && $post['file_id'] == '') {
                $post['file_id'] = null;
            }
            if (isset($post['available_from']) && $post['available_from'] == '') {
                $post['available_from'] = null;
            }
            if (isset($post['available_to']) && $post['available_to'] == '') {
                $post['available_to'] = null;
            }
            //die('<pre>' . print_r($post, 1) . '</pre>');
            $content = new Model_Content($id);
            $content->values($post);
            $content->save_relationships($post);

            $content_array = $content->as_array();
            $content_array['duration_formatted'] = $content->get_duration_formatted('medium');
            $content_array['icon'] = $content->get_icon();

            $return = [
                'name'    => $content->name,
                'content' => $content_array,
                'success' => true,
                'message' => $content->name.' has been saved.',
            ];

        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error saving content.\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return = [
                'success' => false,
                'message' => "Error saving content.\n".$e->getMessage()."\n".$e->getTraceAsString()
            ];
        }

        echo json_encode($return);
    }

    public function action_ajax_get_content()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $content = new Model_Content($this->request->param('id'));
        $return  = $content->as_array();
        $return['learning_outcome_ids'] = array_column($content->learning_outcomes->find_all()->as_array(), 'id');
        $return['type'] = $content->type->name;
        $return['html'] = $content->render();
        $return['duration_formatted'] = $content->get_duration_formatted();
        $return['survey_html'] = View::factory('questionnaire_builder')
            ->set('questionnaire', $content->survey)
            ->set('js_loaded', true)
            ->render();

        if ($return['file_id']) {
            $return['file_url'] = Model_Media::get_path_to_id($return['file_id']);
        }

        echo json_encode($return);
    }

    public function action_ajax_save_content_order()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $orders = $this->request->query('orders');

        foreach ($orders as $id => $order) {
            $content = new Model_Content($id);
            if ($content->order != $order) {
                $content->order = $order;
                $content->save_with_moddate();
            }
        }
    }

    public function action_ajax_delete_content()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        try {
            $content = new Model_Content($this->request->param('id'));
            $content->set_deleted();

            $return = ['success' => true, 'message' => $content->name.' has been deleted.'];

        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error deleting content.\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return = ['success' => false, 'message' => 'Error deleting content. If this problem continues, please ask an administrator to check the error logs.'];
        }

        echo json_encode($return);
    }

    public function action_ajax_save_progress()
    {
        $user = Auth::instance()->get_user();
        $post = $this->request->post();

        // Find existing item or create new item
        $progress = ORM::factory('Content_Progress')
            ->where('user_id',    '=', $user['id'])
            ->where('section_id', '=', $post['section_id'])
            ->where('content_id', '=', $post['content_id'])
            ->find_undeleted()
        ;

        // Set data and save
        $progress->values($post);
        $progress->user_id = $user['id'];
        $progress->save_with_moddate();
    }
}