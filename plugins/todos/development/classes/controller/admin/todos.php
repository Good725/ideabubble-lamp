<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Todos extends Controller_Cms {
    protected $_crud_items = [
        'category' => [
            'name' => 'title',
            'model' => 'Todo_Category',
            'delete_permission' => 'todos_edit',
            'edit_permission'   => 'todos_edit',
        ],
        'schema' => [
            'name' => 'schema',
            'model' => 'Todo_GradingSchema',
            'delete_permission' => 'grades_edit',
            'edit_permission' => 'grades_edit',
        ],
        'todos' => [
            'name' => 'title',
            'model' => 'Todo_Item',
            'delete_permission' => 'grades_edit',
            'edit_permission'   => 'grades_edit',
        ]
    ];

    public function before()
    {
        parent::before();
        $this->template->sidebar = View::factory('sidebar');
        $menus = array();
        if (Auth::instance()->has_access('todos_list')) {
            $menus[] = array(
                'title' => 'Category',
                'link' => '/admin/todos/categories',
                'icon' => 'category'
            );
        }
        if (Auth::instance()->has_access('grades_edit')) {
            $menus[] = array(
                'title' => 'Schemas',
                'link' => '/admin/todos/schemas',
                'icon' => 'settings'
            );
        }
        $this->template->sidebar->menus = array($menus);
        $this->template->sidebar->breadcrumbs = [['name' => 'Home', 'link' => '/admin'],
            ['name' => 'Todos', 'link' => '/admin/todos']];
        Model_Todos::update_exam_status();
    }
    
    public function action_ajax_get_submenu()
    {
        $return = array('items' => array());
        if (Auth::instance()->has_access('todos_list')) {
            $return['items'][] = array(
                'title' => 'All todos',
                'link' => '/admin/todos/list',
                'icon_svg' => 'all-requests'
            );


        }
        
        if (Auth::instance()->has_access('todos_view_my_todos')) {
            $return['items'][] = array(
                'title' => 'My todos',
                'link' => '/admin/todos/list?my=1',
                'icon_svg' => 'all-requests'
            );
        }
        if (Auth::instance()->has_access('todos_view_results')) {
            $return['items'][] = array(
                'title' => 'All results',
                'link' => '/admin/todos/results',
                'icon_svg' => 'template'
            );
        }
        if (Auth::instance()->has_access('todos_view_my_todos')) {
            $return['items'][] = array(
                'title' => 'My results',
                'link' => '/admin/todos/results?my=1',
                'icon_svg' => 'template'
            );
        }
        return $return;
    }
    
    public function action_index()
    {
        if (Auth::instance()->has_access('todos_list')) {
            $this->request->redirect('/admin/todos/list');
        } else if(Auth::instance()->has_access('todos_view_my_todos')) {
            $this->request->redirect('/admin/todos/list?my=1');
        } else {
            IbHelpers::set_message(__('You need access to the "$1" permission to use this feature.',
                array('$1' => 'todos_list')), 'warning popup_box');
            $this->request->redirect('/admin');
        }
    }
    
    public function action_delete()
    {
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $this->auto_render = false;
        
        if (!Auth::instance()->has_access('todos_edit') && !Auth::instance()->has_access('todos_edit_limited')) {
            $result['success'] = 0;
            $result['message'] = __('No Permission');
        } else {
            Model_Todos::save(array('id' => $this->request->post('id'), 'deleted' => 1));
            $result['success'] = 1;
            $result['message'] = __('Exam has been deleted');
        }
        echo json_encode($result);
    }
    
    public function action_toggle_publish()
    {
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $this->auto_render = false;
        
        try {
            $id = $this->request->query('id');
            $publish = $this->request->query('publish');
            
            if (!Auth::instance()->has_access('todos_edit') && !Auth::instance()->has_access('todos_edit_limited')) {
                $result['success'] = 0;
                $result['message'] = __('You need access to the &quot;todos_edit&quot; permission to perform this action.');
            } else {
                Model_Todos::save(array('id' => $id, 'published' => $publish));
                $result['success'] = 1;
                $result['message'] = ($publish == 1) ? __('Todo has been published') : __('Todo has been unpublished');
            }
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, $e->getMessage() . "\n" . $e->getTraceAsString());
            
            $result['success'] = 0;
            $result['message'] = __('Unexpected internal error. Please try again. If this problem continues, please ask an administrator to check the error logs.');
        }
        
        echo json_encode($result);
    }
    
    public function action_edit()
    {
        $post = $this->request->post();
        if (!isset($post['allow_skipping']) || is_null($post['allow_skipping'])) {
            $post['allow_skipping'] = 0;
        }
        if (!isset($post['allow_manual_grading']) || is_null($post['allow_manual_grading'])) {
            $post['allow_manual_grading'] = 0;
        }
        $id = $this->request->param('id');
        $todo = Model_Todos::get($id, true);
        $parent_type = ucfirst(strtolower($this->request->param('toggle', (isset($todo['type'])) ? $todo['type'] : '')));
        // Task, Assignment, Term-Assessment, Class-Test
        $todo_type = !empty($post['type']) ? $post['type'] : $parent_type;

        if (!Auth::instance()->has_access('todos_edit') && !Auth::instance()->has_access('todos_edit_limited')) {
            IbHelpers::set_message("You need access to the &quot;todos_edit&quot or &quot;todos_edit_limited&quot to perform this action.",
                'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            if (Auth::instance()->has_access('todos_edit_limited') && $todo['created_by'] != Auth::instance()->get_user()['id'] && $id !== "new") {
                IbHelpers::set_message("You do not have access to edit other people's todos.",
                    'warning popup_box');
                $this->request->redirect('/admin/todos/list');
            }
        }
        //die('<pre>' . print_r($post, 1) .'</pre>');

        $todo_types = array(
            'Task' => 'Task',
            'Assignment' => 'Assignment',
            'Exam' => 'Exam', // Not actually needed. Exam should be 'Term-Assessemnt', 'Class-Test' or 'State-Exam'.
            'Term-Assessment' => 'Term-Assessment',
            'Class-Test' => 'Class-Test',
            'State-Exam' => 'State-Exam'
        );
        $edit_type_permissions = array();
        $auth = Auth::instance();
        if ($auth->has_access('todos_edit_create_tasks')) {
            $edit_type_permissions[] = 'Task';
        }
        if ($auth->has_access('todos_edit_create_assignments')) {
            $edit_type_permissions[] = 'Assignment';
        }
        if ($auth->has_access('todos_edit_create_assesments')) {
            $edit_type_permissions[] = 'Term-Assessment';
        }
        if ($auth->has_access('todos_edit_create_tests')) {
            $edit_type_permissions[] = 'Class-Test';
        }
        if ($auth->has_access('todos_edit_create_exams')) {
            $edit_type_permissions[] = 'State-Exam';
        }
        if (@$post['action']) {
            if (!in_array(@$todo_types[$todo_type], $edit_type_permissions)) {
                IbHelpers::set_message("You cannot create this type of todo.", 'warning popup_box');
                $this->request->redirect('/admin/todos/list');
            }
            // If the assignment has a content tree from content-plugin...
            // ... update the name of the top-level item to be the todo title
            if (!empty($post['content_id']) && !empty($post['title'])) {
                $content = new Model_Content($post['content_id']);
                if ($content->name != $post['title']) {
                    $content->name = $post['title'];
                    $content->save_with_moddate();
                }
            }
            $id = Model_Todos::save_from_post($post);
            IbHelpers::set_message("Todo has been saved!", 'success popup_box');
            if ($post['action'] == 'save') {
                $this->request->redirect('/admin/todos/edit/' . $id);
            } else {
                $this->request->redirect('/admin/todos/');
            }
        }
        
        if ($id == "new" && !in_array($parent_type, array("Task", "Assignment", "Exam"))) {
            IbHelpers::set_message("Select what type of todo you want to create first.",
                'warning popup_box');
            $this->request->redirect('/admin/todos/list');
        }
        
        $todo['type'] = $todo['type'] ?? $todo_type;
        $todo['type'] = empty($todo['type']) ? "Class-Test" : $todo['type'];
        $todo['assigned_contacts_type'] = $todo['assigned_contacts_type'] ?? 'Group';
        $todo['todo_type_label'] = $todo['todo_type_label'] ?? str_replace('-', ' ', $todo['type']);
        $todo['content'] = ORM::factory('Content')->where('id', '=',
            isset($todo['content_id']) ? $todo['content_id'] : '')->find_undeleted();
        $locations = Model_Locations::get_locations_only();
        $grades = Model_Todos::grades_list();
        $this->template->sidebar->breadcrumbs[] =['name' => (is_numeric($id)) ? "Edit " . strtolower($todo['todo_type_label']) :
            "New " . strtolower($todo_type), 'link' => '/admin/todos/list'];
           
        $this->template->body = View::factory('todos_edit');
        $this->template->body->todo = $todo;
        $this->template->body->todo_object = new Model_Todo_Item($id);
        $this->template->body->academic_years = Model_AcademicYear::get_all();
        $this->template->body->grades = $grades;
        $this->template->body->grading_schema  = ORM::factory('Todo_GradingSchema')->where('id', '=', $todo['grading_schema_id'])->find_undeleted();
        $this->template->body->grading_schemas = ORM::factory('Todo_GradingSchema')->find_all_undeleted();
        $this->template->body->levels = ORM::factory('Course_Level')->order_by('order')->order_by('level')->find_all_undeleted();
        $this->template->body->locations = $locations;
        $this->template->body->related_to_types = Model_Todos::get_related_to();
        $this->template->body->subjects = ORM::factory('Course_Subject')->order_by('name')->find_all_undeleted();
        $this->template->body->edit_limited = Auth::instance()->has_access('todos_edit_limited');
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('media', 'js/multiple_upload.js', ['cachebust' => true]) . '"></script>';
        $stylesheets = array(URL::get_engine_plugin_assets_base('contacts3') . 'css/validation.css' => 'screen');
        $this->template->styles = array_merge($this->template->styles, $stylesheets);
        
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . Settings::instance()->get('google_map_key') . '&libraries=places&sensor=false"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/locations_form.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validate.min.js"></script>';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses') . 'css/forms.css'] = 'screen';
        
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('todos') . 'js/todos_edit.js"></script>';
    }

    public function action_categories()
    {
        if (!Auth::instance()->has_access('todos_list')) {
            IbHelpers::set_message('You need access to the &quot;todos_list&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        } else {
            $this->template->sidebar->tools = '<a href="/admin/todos/edit_category" class="btn btn-primary">' . __('Add Category') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Categories', 'link' => '/admin/todos/categories'];

            $this->template->body = View::factory('iblisting')->set([
                'columns'   => ['ID', 'Name', 'Updated', 'Publish', 'Actions'],
                'id_prefix' => 'todos-categories',
                'plugin'    => 'todos',
                'type'      => 'category'
            ]);
        }
    }

    public function action_edit_category()
    {
        if (!Auth::instance()->has_access('todos_list')) {
            IbHelpers::set_message('You need access to the &quot;todos_list&quot; permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        }
        else {
            $category = new Model_Todo_Category($this->request->param('id'));

            $this->template->sidebar->tools = '<a href="/admin/todos/edit_category/" class="btn btn-primary">' . __('Add Category') . '</a>';
            $this->template->sidebar->breadcrumbs[] = ['name' => 'Categories', 'link' => '/admin/todos/categories'];

            $this->template->body = View::factory('form_categories')->set([
                'category' => $category,
            ]);
        }
    }

    public function action_save_category()
    {
        // todo: replace with spec permission
        if (!Auth::instance()->has_access('todos_list')) {
            IbHelpers::set_message('You need access to the &quot;todos_list&quot; permission to perform this action.', 'error popup_box');
        }
        else {
            try {
                $category = new Model_Todo_Category($this->request->param('id'));
                $category->values($this->request->post());
                $category->save_with_moddate();
                IbHelpers::set_message(htmlspecialchars('Category #'.$category->id.': "'.$category->title.'" successfully saved.'), 'success popup_box');

                if ($this->request->post('action') == 'save_and_exit') {
                    $this->request->redirect('/admin/todos/categories');
                } else {
                    $this->request->redirect('admin/todos/edit_category/'.$category->id);
                }
            }
            catch (Exception $e) {
                Log::instance()->add(Log::ERROR, "Error saving category.\n".$e->getMessage()."\n".$e->getTraceAsString());
                IbHelpers::set_message('Unexpected error saving category. If this problem continues, please ask an administrator to check the error logs.', 'danger popup_box');
                $this->request->redirect('/admin/todos/categories');
            }
        }
    }

    public function action_ajax_get_todo_categories()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $todo_categories = new Model_Todo_Category();
        if((!Auth::instance()->has_access('todos_edit') || $this->request->query('profile'))) {
            $student_id =  Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id'])['id'];
            $todo_categories->where('todo.results_published_datetime', '<', DB::expr('NOW()'));
        } else {
            return json_encode([]);
        }
        $categories = $todo_categories->where_academic_year($this->request->query('academic_year_id'))->where_student_has_todo($student_id)->find_all_undeleted()->as_array('id', 'title');
        echo json_encode($categories);
    }
    public function action_view()
    {
        $post = $this->request->post();
        $id = $this->request->param('id');
        $todo = Model_Todos::get($id, true);
        $todo_object = new Model_Todo_Item($id);

        if (!Auth::instance()->has_access('todos_view_my_todos')) {
            IbHelpers::set_message("You need access to the &quot;todos_view_my_todos&quot to perform this action.",
                'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media').'js/multiple_upload.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media').'js/image_edit.js"></script>';

            if (!isset($todo['id'])) {
                IbHelpers::set_message("Select a valid todo.",
                    'warning popup_box');
                $this->request->redirect("/admin/todos/list?my=1");
            }
        }
    
        if (@$post['action']) {
            Model_Todos::save_limited_from_post($post);
            IbHelpers::set_message("Todo has been saved!", 'success popup_box');
            if ($post['action'] == 'save') {
                $this->request->redirect('/admin/todos/view/' . $todo['id']);
            } else {
                $this->request->redirect('/admin/todos/');
            }
        }

        $user = Auth::instance()->get_user();
        $is_linked_to_todo = $todo['created_by'] == $user['id'];
        foreach ($todo['has_assigned_contacts'] as $assigned_contact_result_details) {
            $logged_in_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
            $logged_in_contact_results = array();
            if ($assigned_contact_result_details['contact_id'] == $logged_in_contact['id']) {
                $is_linked_to_todo = true;
                $logged_in_contact_results[] = $assigned_contact_result_details;
                break;
            }
        }
        $todo['logged_in_contact_results'] = $logged_in_contact_results;
        if (!Auth::instance()->has_access('todos_list') && !$is_linked_to_todo) {
            IbHelpers::set_message("You need access to the &quot;todos_list&quot permission to view todos not associated with you.",
                'warning popup_box');
            $this->request->redirect('/admin/todos/list?my=1');
        }

        $content = new Model_Content($todo['content_id']);

        $contact = Auth::instance()->get_contact();
        if (!Auth::instance()->has_access('todos_edit')){
            $submissions = $todo_object->file_submissions
                ->where('contact_id', '=', $contact->id)
                ->where('file_id', 'is not', null)
                ->find_all_undeleted();
        } else {
            $submissions = $todo_object->file_submissions
                ->where('file_id', 'is not', null)
                ->find_all_undeleted();
        }
        $grades = Model_Todos::grades_list();
        $this->template->sidebar->breadcrumbs[] = ['name' => 'My todos', 'link' => "/admin/todos/list?my=1"];
        $this->template->sidebar->breadcrumbs[] = [
            'name' => (is_numeric($id)) ? "View " . strtolower($todo['todo_type_label']) :
                "New " . strtolower($todo_type),
            'link' => '/admin/todos/list'
        ];
        $this->template->body = View::factory('todos_view');
        $this->template->body->content = $content;
        $this->template->body->grades = $grades;
        $this->template->body->todo = $todo;
        $this->template->body->todo_object = $todo_object;
        $this->template->body->todo_has_assignee = $todo_object->has_assignees->where('contact_id', '=', $contact->id)->find();
        $this->template->body->submissions = $submissions;
        $this->template->body->location = Model_Locations::get_location($todo['location_id']);
        $stylesheets = array(URL::get_engine_plugin_assets_base('contacts3') . 'css/validation.css' => 'screen');
        $this->template->styles = array_merge($this->template->styles, $stylesheets);
        
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . Settings::instance()->get('google_map_key') . '&libraries=places&sensor=false"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/locations_form.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validate.min.js"></script>';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses') . 'css/forms.css'] = 'screen';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('todos') . 'js/todos_edit.js"></script>';

        if ($content->id) {
            $this->template->styles[URL::get_engine_plugin_assets_base('surveys') . 'css/frontend/survey.css'] = 'screen';
            $this->template->styles['https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.css'] = 'screen';
            $this->template->scripts[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.min.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('content') . 'js/my_content.js"></script>';
        }
    }
    
    public function action_clone()
    {
        $this->auto_render = false;
        
        if (!Auth::instance()->has_access('todos_edit') && !Auth::instance()->has_access('todos_edit_limited')) {
            IbHelpers::set_message("You need access to the &quot;todos_edit&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }
        
        $id = Model_Todos::duplicate($this->request->param('id'));
        IbHelpers::set_message("Todo has been cloned", 'info popup_box');
        $this->request->redirect('/admin/todos/edit/' . $id);
    }
    
    public function action_list()
    {
        $my = $this->request->query('my');
        $this->template->sidebar->breadcrumbs[] = ['name' => $my == "1" ? "My todos" : "All todos", 'link' => $my == "1" ? "/admin/todos/list?my=1" : "/admin/todos/list"];
        if (!Auth::instance()->has_access('todos_list') && !Auth::instance()->has_access('todos_view_my_todos')) {
            IbHelpers::set_message("You need access to the &quot;todos_list&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }
        
        if (Auth::instance()->has_access('todos_view_my_todos') && !Auth::instance()->has_access('todos_list')
            && $my != '1') {
            IbHelpers::set_message("You need access to the &quot;todos_list&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin/todos/list?my=1');
        }
        
        if (Auth::instance()->has_access('todos_edit') || Auth::instance()->has_access('todos_edit_limited')) {
            $this->template->sidebar->tools = '<div class="btn-group">' .
                '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">New todo <span class="caret"></span></button>' .
                '<ul class="dropdown-menu">' .
                '<li><a class="add_contact_btn" href="/admin/todos/edit/new/task">Task</a></li>' .
                '<li><a class="add_contact_btn" href="/admin/todos/edit/new/assignment">Assignment</a></li>' .
                '<li><a class="add_contact_btn" href="/admin/todos/edit/new/exam">Exam</a></li>' .
                '</ul></div>';;
        }
        $this->template->body = View::factory('todos_list');
        $this->template->body->my = $my;
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('todos') . 'js/todos_list.js"></script>';
    }
    
    public function action_my_todo()
    {
        $this->template->sidebar->breadcrumbs[] =
            ['name' => "My todos", 'link' => '/admin/todos/list?my=1'];
        $todo = Model_Todos::get($this->request->param('id'), true);
        //die('<pre>' . print_r($todo, 1) . '</pre>');
        $contact = Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id']);
        $is_linked_to_content = false;
        foreach($todo['has_assigned_contacts'] as $assigned_student) {
            if($contact['id'] == $assigned_student['contact_id']) {
                $is_linked_to_content = true;
                break;
            }
        }
        if(!$todo['content_id'] || !$is_linked_to_content) {
            $redirect_message = ($todo['content_id']) ? "You are not linked to this todo" : "There is no content in this todo".
            IbHelpers::set_message($redirect_message,
                'warning popup_box');
            $this->request->redirect('/admin/todos/list?my=1');
        }
        $content = new Model_Content($todo['content_id']);
        $this->template->sidebar->breadcrumbs[] = [['name' => "My todos", 'link' => '/admin/todos/list?my=1'],
            ['name' => "Todo content", 'link' => "/admin/todos/my_todo{$todo['id']}"]];
        
        $this->template->styles[URL::get_engine_plugin_assets_base('surveys') . 'css/frontend/survey.css'] = 'screen';
        $this->template->styles['https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/jquery.validationEngine2.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts3').'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('content') . 'js/my_content.js"></script>';
        $this->template->body = View::factory('admin/my_content')
            ->set('allow_skipping', $content->allow_skipping)
            ->set('content', $content)
            ->set('open_section', 0);
    }
    
    public function action_list_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        if (isset($post['sEcho'])) {
            $return['sEcho'] = $post['sEcho'];
        }
        if ((!Auth::instance()->has_access('todos_list') && Auth::instance()->has_access('todos_view_my_todos'))
            || @$post['my'] == 'true') {
            $auth = Auth::instance()->get_user();
            $contact = current(Model_Contacts3::get_contact_ids_by_user($auth['id']));
            $post['contact_id'] = ($contact) ? $contact['id'] : null;
        }
        $return['aaData'] = Model_Todos::list_datatable($post);
        $return['iTotalRecords'] = DB::select(DB::expr('@found_todos as total'))->execute()->get('total');
        $return['iTotalDisplayRecords'] = count($return['aaData']);
        //display response (json encoded)
        echo json_encode($return);
    }
    
    public function action_results()
    {
        $my = $this->request->query('my') ?? "0";
        $this->template->sidebar->breadcrumbs[] = [
            'name' => $my == "1" ? "My results" : "All results",
            'link' => $my == "1" ? "/admin/todos/results?my=1" : "/admin/todos/results"
        ];
    
        if (!Auth::instance()->has_access('todos_view_results') && !Auth::instance()->has_access('todos_view_results_limited')) {
            IbHelpers::set_message("You need access to the &quot;todos_view_result&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }
        
        if (Auth::instance()->has_access('todos_view_results_limited') && !Auth::instance()->has_access('todos_view_results')
            && $my != '1') {
            IbHelpers::set_message("You need access to the &quot;todos_view_result&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin/todos/results?my=1');
        }
        
        $user = Auth::instance()->get_user();
        $this->user = Model_Users::get_user($user['id']);
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        if (!isset($contacts[0])) {
            IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
            $this->request->redirect('admin');
        }
        if (Settings::instance()->get('todos_site_allow_online_exams')) {
            $statuses = array(
                'upcoming' => 'Upcoming',
                'started' => 'Started',
                'submitted' => 'Submitted',
                'late' => 'Submitted Late');
            $examiners_statuses = array(
                'awaiting' => 'Awaiting',
                'started' => 'Started',
                'graded' => 'Graded'
            );
            $todos = array();
            $all_todos = Model_Todos::search();
            foreach($all_todos as $todo) {
                $todos[$todo['id']] = $todo['title'];
            }
            $metrics = [
                ['amount' => 100, 'text' => 'Total'],
                ['amount' => 20, 'text' => 'Completed'],
                ['amount' => 10, 'text' => 'Started'],
                ['amount' => 70, 'text' => 'Awaiting']
            ];
            $schedules = ORM::factory('Course_Schedule')->order_by('name')->find_all_undeleted()->as_array('id', 'name');
            $staff_role = new Model_Contacts3_Role(['name' => 'teacher']);
            $staff = $staff_role->contacts
                ->order_by('last_name')->order_by('first_name')->find_all_undeleted()->as_array('id', 'full_name');
            $filter_menu_options = [
                ['label' => 'Schedule', 'name' => 'schedule_ids', 'options' => $schedules],
                ['label' => 'Exam',  'name' => 'exam_ids',  'options' => $todos],
                ['label' => 'Examiner',  'name' => 'examiner_ids',  'options' => $staff],
                ['label' => 'Leaner\'s Status',   'name' => 'statuses',     'options' => $statuses],
                ['label' => 'Examiner\'s Status',   'name' => 'examiner_statuses',     'options' => $examiners_statuses],
            ];
            $this->template->body = View::factory('iblisting')->set([
                'below'               => '',
                'columns'             => ['Title', 'Type', 'Due Date', 'Examiner', 'Student', 'Schedule', 'Questions', 'Mark', 'Result', 'Grade', 'Status', 'Comment', 'Actions'],
                'daterangepicker'     => true,
                'filter_menu_options' => $filter_menu_options,
                'id_prefix'           => 'todos',
                'plugin'              => 'todos',
                'reports'             => $metrics,
                'searchbar_on_top'    => false,
                'type'                => 'todos',
            ]);
        } else {
            $this->template->body = View::factory('results_list');
        }

        $this->template->body->my = $my;
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('todos') . 'js/results_list.js"></script>';
    }
    
    public function action_results_datatable()
    {
        if (!Auth::instance()->has_access('todos_view_results') && !Auth::instance()->has_access('todos_view_results_limited')) {
            IbHelpers::set_message("You need access to the &quot;todos_view_results&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }
        
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        if (isset($post['sEcho'])) {
            $return['sEcho'] = $post['sEcho'];
        }
        if (!Auth::instance()->has_access('todos_view_results') ||
            ($post['my'] == 'true' && Auth::instance()->has_access('todos_view_results_limited'))) {
            $user = Auth::instance()->get_user();
            $this->user = Model_Users::get_user($user['id']);
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            if (!isset($contacts[0])) {
                IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
                $this->request->redirect('/admin');
            }
            
            $contact = new Model_Contacts3($contacts[0]['id']);
            if ($contact->get_is_primary()) {
                $family_id = $contact->get_family_id();
                $family = new Model_Family($family_id);
                $members = $family->get_members();
                $post['student_id'] = array();
                foreach ($members as $member) {
                    $post['student_id'][] = $member->get_id();
                }
            } else {
                $post['student_id'] = $contacts[0]['id'];
            }
        }
        $return['aaData'] = Model_Todos::results_datatable($post);
        $return['iTotalRecords'] = DB::select(DB::expr('@found_results as total'))->execute()->get('total');
        $return['iTotalDisplayRecords'] = count($return['aaData']);
        echo json_encode($return);
    }
    
    public function action_email()
    {
        if (!Auth::instance()->has_access('todos_edit') && !Auth::instance()->has_access('todos_edit_limited')) {
            IbHelpers::set_message("You need access to the &quot;todos_edit&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }
        
        $todo_id = $this->request->param('id');
        $results = Model_Todos::email_results($todo_id);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($results);
    }

    public function action_schemas()
    {
        $this->template->sidebar->breadcrumbs[] = ['name' => "Schemas", "link" => "/admin/todos/schemas"];
        $this->template->sidebar->tools = '<a href="/admin/todos/edit_schema" class="btn btn-primary">Add schema</a>';
        $this->template->body = View::factory('iblisting')->set([
            'columns'   => ['ID', 'Title', 'Updated', 'Publish', 'Actions'],
            'id_prefix' => 'todos-schemas',
            'plugin'    => 'todos',
            'type'      => 'schema',
        ]);
    }

    public function action_edit_schema()
    {
        if (!Auth::instance()->has_access('grades_edit')) {
            IbHelpers::set_message("You need access to the &quot;grades_edit&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }

        $schema   = ORM::factory('Todo_GradingSchema')->where('id', '=', $this->request->param('id'))->find_undeleted();
        $subjects = ORM::factory('Course_Subject')->order_by('name')->find_all_undeleted();
        $levels   = ORM::factory('Course_Level')->order_by('order')->order_by('level')->find_all_undeleted();

        $level_columns = [];
        foreach ($levels as $level) {
            $level_columns['levels['.$level->id.']'] = 'Points ('.$level->get_short_name().')';
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => "Grades", "link" => "/admin/todos/grades"];
        $this->template->sidebar->breadcrumbs[] = ['name' => "Schemas", "link" => "/admin/todos/schemas"];
        $this->template->sidebar->tools = '<a href="/admin/todos/edit_schema" class="btn btn-primary">Add schema</a>';
        $this->template->body = View::factory('form_schema')->set([
            'levels'        => $levels,
            'level_columns' => $level_columns,
            'schema'        => $schema,
            'subjects'      => $subjects
        ]);
    }

    public function action_save_schema()
    {
        if (!Auth::instance()->has_access('grades_edit')) {
            IbHelpers::set_message("You need access to the &quot;grades_edit&quot; permission to perform this action", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $schema = ORM::factory('Todo_GradingSchema')->where('id', '=', $this->request->param('id'))->find_undeleted();
        $schema->values($this->request->post());
        $schema->save_relationships($this->request->post());

        IbHelpers::set_message('Schema #'.$schema->id.': '.htmlspecialchars($schema->title).' has been saved.', 'success popup_box');

        if ($this->request->post('redirect') == 'save_and_exit') {
            $this->request->redirect('/admin/todos/schemas');
        } else {
            $this->request->redirect('/admin/todos/edit_schema/'.$schema->id);
        }
    }

    public function action_delete_schema()
    {
        if (!Auth::instance()->has_access('grades_edit')) {
            IbHelpers::set_message("You need access to the &quot;grades_edit&quot; permission to perform this action", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $schema = new Model_Todo_GradingSchema($this->request->param('id'));
        $schema->set_deleted();
        IbHelpers::set_message('Schema #'.$schema->id.': '.htmlspecialchars($schema->title).' has been deleted.', 'success popup_box');
        $this->request->redirect('/admin/todos/schemas');
    }
    
    public function action_grades()
    {
        if (!Auth::instance()->has_access('grades_edit')) {
            IbHelpers::set_message("You need access to the &quot;grades_edit&quot; permission to perform this action",
                'warning popup_box');
            $this->request->redirect('/admin');
        }
        $post = $this->request->post();
        $this->template->sidebar->breadcrumbs[] = ['name' => "Grades", "link" => "/admin/todos/grades"];
        if (isset($post['save'])) {
            Model_Todos::grades_save_post($post);
            IbHelpers::set_message("Grades have been updated", 'info popup_box');
            $this->request->redirect('/admin/todos/grades');
        }
        $grades = Model_Todos::grades_list();
        $this->template->body = View::factory('grades_list');
        $this->template->body->grades = $grades;
    }
    
    public function action_ajax_autocomplete_regarding()
    {
        $result = array();
        $this->auto_render = false;
        $regarding_id = $this->request->query('regarding_id');
        $regarding_term = $this->request->query('term');
        $result = Model_Todos::get_related_to_autocomplete($regarding_id, $regarding_term);
        $this->response->body(json_encode($result));
    }
    
    // Depreciated todos plugin functions below
    public function action_list_todos()
    {
        
        $todos = new Model_Todos();
        $result = $todos->get_all_todos();
        //print_r($result);exit;
        
        $this->template->body = View::factory('list_todos');
        $this->template->body->todos = $result;
        
    }
    
    public function action_ajax_list_todos()
    {
        $contact_id = $this->request->param('id');
        $todos = new Model_Todos();
        $result = $todos->get_todos_related_to_customer($contact_id);
        
        $this->template->body = View::factory('tab_list_todos')->set('contact_id', $contact_id);
        $this->template->body->todos = $result;
        
    }
    
    public function action_ajax_list_claim_todos()
    {
        $claim_id = $this->request->param('id');
        $todos = new Model_Todos();
        $result = $todos->get_todos_related_to_claim($claim_id);
        
        $this->template->body = View::factory('tab_list_claim_todos')->set('claim_id', $claim_id);
        $this->template->body->todos = $result;
        
    }
    
    public function action_ajax_list_policy_todos()
    {
        $policy_id = $this->request->param('id');
        $todos = new Model_Todos();
        $result = $todos->get_todos_related_to_policy($policy_id);
        $this->template->body = View::factory('tab_list_policy_todos')->set('policy_id', $policy_id);
        $this->template->body->todos = $result;
    }
    
    public function action_manage_all()
    {
        if (!Auth::instance()->has_access('todos_manage_all')) {
            ibhelpers::alert('You do not have permission');
            $this->request->redirect('/admin/todos');
        }
        
        $todos = new Model_Todos();
        $result = $todos->get_all_todos_for_all_users();
        
        $this->template->body = View::factory('list_todos');
        $this->template->body->todos = $result;
    }
    
    public function action_autocomplete()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $related_to = $this->request->query('related_to');
        $term = $this->request->query('term');
        $result = Model_Todos::get_related_to_autocomplete($related_to, $term);
        echo json_encode($result);
    }
    
    public function action_edit_todo()
    {
        
        $id = $this->request->param('id');
        
        $related_plugin_name = $this->request->query('related_plugin_name'); // return "plugin_projects" or other related plugin name
        $related_to_id = $this->request->query('related_to_id'); // return in case of "projects" it is project_id
        $return_url = $this->request->query('return_url');
        
        //default return url is ToDos plugin list form
        if (!$return_url) {
            $return_url = URL::adminaction('list_todos');
        }
        
        $todos = new Model_Todos();
        
        if ($this->request->post()) {
            $todo = $this->request->post();
            if ($todos->validate($todo)) {
                if (!Auth::instance()->has_access('todos_edit')) {
                    unset($todo['from_user_id']);
                }
                $todos->update_todo($id, $todo);
                IbHelpers::set_message('To Do updated', 'success popup_box');
                return $this->request->redirect($return_url);
            }
            
        } else {
            // if it is GET request then data is retrived from database
            $todo = $todos->get_todo($id);
            $todo['due_date'] = Date::ymdh_to_dmy($todo['due_date']);
        }
        
        $this->template->body = View::factory('edit_todo');
        $this->template->body->action = 'edit_todo';
        $this->template->body->todo = $todo;
        $this->template->body->from_user_options = $todos->get_users_as_options($todo['from_user_id']);
        $this->template->body->to_user_options = $todos->get_users_as_options($todo['to_user_ids']);
        $this->template->body->related_plugin_name = $related_plugin_name;
        $this->template->body->related_to_id = $related_to_id;
        $this->template->body->return_url = $return_url;
        $this->template->body->user = Auth::instance()->get_user();
        $this->template->body->related_to_list = Model_Todos::get_related_to_list();
        
    }
    
    public function action_add_todo()
    {
        
        $todos = new Model_Todos();
        
        $related_plugin_name = $this->request->query('related_plugin_name'); // return "plugin_projects" or other related plugin name
        $related_to_id = $this->request->query('related_to_id'); // return in case of "projects" it is project_id
        $return_url = $this->request->query('return_url');
        if (!$return_url) {
            $return_url = URL::adminaction('list_todos');
        }
        
        if ($this->request->post()) {
            $todo = $this->request->post();
            
            $related_to = @$todo['related_to'] ? $todo['related_to'] : null;
            $related_to_id = $todo['related_to_id'];
            if (!Auth::instance()->has_access('todos_edit') || !isset($todo['from_user_id'])) {
                $user = Auth::instance()->get_user();
                $todo['from_user_id'] = $user['id'];
            }
            
            if ($todos->validate($todo)) {
                $todos->add_todo($todo, $related_to, $related_to_id);
                IbHelpers::set_message('To Do inserted', 'success popup_box');
                return $this->request->redirect($return_url);
            }
            
        } else {
            // if it is GET request then new todo dataobject is created
            $user = Auth::instance()->get_user();
            
            $todo = array(
                'todo_id' => ''
            ,
                'title' => ''
            ,
                'details' => ''
            ,
                'from_user_id' => $user['id']
            ,
                'to_user_id' => $user['id']
            ,
                'status_id' => 'Open'
            ,
                'priority_id' => 'Normal'
            ,
                'due_date' => ''
            ,
                'get_plugin_id_as_text' => $this->request->query('related_to_text')
            ,
                'related_to_text' => $this->request->query('related_to_text'),
                'related_to' => ''
            );
        }
        
        $this->template->body = View::factory('edit_todo');
        $this->template->body->action = 'add_todo';
        $this->template->body->todo = $todo;
        $this->template->body->from_user_options = $todos->get_users_as_options($todo['from_user_id']);
        $this->template->body->to_user_options = $todos->get_users_as_options($todo['to_user_id']);
        $this->template->body->related_plugin_name = $related_plugin_name;
        $this->template->body->related_to_id = $related_to_id;
        $this->template->body->related_to = Model_Todos::get_related_to($related_plugin_name);
        $this->template->body->return_url = $return_url;
        $this->template->body->user = Auth::instance()->get_user();
        $this->template->body->related_to_list = Model_Todos::get_related_to_list();
    }
    
    
    public function action_list_related_todos()
    {
        
        
        $related_plugin_name = $this->request->query('related_plugin_name'); // return "plugin_projects" or other related plugin name
        $related_to_id = $this->request->query('related_to_id'); // return in case of "projects" it is project_id
        $return_url = $this->request->query('return_url');
        
        $todos = new Model_Todos();
        
        $this->template->body = View::factory('list_related_todos');
        $this->template->body->todos = $todos->get_all_related_todos($related_plugin_name, $related_to_id);
        $this->template->body->related_plugin_name = $related_plugin_name;
        $this->template->body->related_to_id = $related_to_id;
        $this->template->body->return_url = $return_url;
    }
    
    public function action_delete_todo()
    {
        $id = $this->request->param('id');
        $todo = new Model_Todos();
        
        $todo->delete_todo($id);
        
        $this->request->redirect('admin/todos');
    }
    
    public function action_ajax_add_note()
    {
        $id = $this->request->param('id');
        $this->template->body = View::factory('edit_note');
        $this->template->body->id = $id;
    }
    
    /* CONTACT TODOS */
    
    public function action_ajax_new_contact_todo()
    {
        
        $post = $this->request->post();
        if (!empty($post)) {
            $todo_model = new Model_Todos();
            if ($todo_model->validate_contact_todo($post) AND $todo_model->add_contact_todo($post)) {
                $data['msg'] = '<div class="alert alert-success"><a data-dismiss="alert" class="close">×</a><strong>Warning:</strong> To Do added!</div>';
                $data['status'] = 'ok';
                $this->template->body = 'ok';
                return true;
            } else {
                //Set error message
            }
        }
        
        $todos = new Model_Todos();
        $user = Auth::instance()->get_user();
        if (!empty($post)) {
            $user['id'] = $post['to_user_id'];
        }
        $this->template->body = View::factory('add_todo')->set('plugin', 'contact');
        $contact_id = $this->request->query('contact_id');
        $contact_model = new Model_Contact();
        $contact = $contact_model->get_all($contact_id);
        
        $this->template->body->contact_name = $contact['0']['company_or_name'];
        $this->template->body->id = $contact_id;
        $this->template->body->contact_id = $contact_id;
        $this->template->body->to_user_options = $todos->get_users_as_options($user['id']);
        $this->template->body->post = $post;
    }
    
    /* ACCOUNT TODOS */
    
    public function action_ajax_new_account_todo()
    {
        
        $post = $this->request->post();
        if (!empty($post)) {
            $todo_model = new Model_Todos();
            if ($todo_model->validate_contact_todo($post) AND $todo_model->add_account_todo($post)) {
                $data['msg'] = '<div class="alert alert-success"><a data-dismiss="alert" class="close">×</a><strong>Warning:</strong> To Do added!</div>';
                $data['status'] = 'ok';
                $this->template->body = 'ok';
                return true;
            } else {
                //Set error message
            }
        }
        
        $todos = new Model_Todos();
        $user = Auth::instance()->get_user();
        if (!empty($post)) {
            $user['id'] = $post['to_user_id'];
        }
        $this->template->body = View::factory('add_todo')->set('plugin', 'account');
        $transaction_id = $this->request->query('transaction_id');
        $contact_id = $this->request->query('contact_id');
        
        $this->template->body->id = $transaction_id;
        $this->template->body->contact_id = $contact_id;
        $this->template->body->to_user_options = $todos->get_users_as_options($user['id']);
        $this->template->body->post = $post;
    }
    
    /* POLICY TODOS */
    
    public function action_ajax_new_policy_todo()
    {
        
        $post = $this->request->post();
        if (!empty($post)) {
            $todo_model = new Model_Todos();
            if ($todo_model->validate_contact_todo($post) AND $todo_model->add_policy_todo($post)) {
                $data['msg'] = '<div class="alert alert-success"><a data-dismiss="alert" class="close">×</a>To Do added!</div>';
                $data['status'] = 'ok';
                $this->template->body = 'ok';
                return true;
            } else {
                //Set error message
            }
        }
        
        $todos = new Model_Todos();
        $user = Auth::instance()->get_user();
        if (!empty($post)) {
            $user['id'] = $post['to_user_id'];
        }
        $this->template->body = View::factory('add_todo')->set('plugin', 'policy');
        $policy_id = $this->request->query('policy_id');
        $contact_id = $this->request->query('contact_id');
        
        $this->template->body->id = $policy_id;
        $this->template->body->contact_id = $contact_id;
        $this->template->body->to_user_options = $todos->get_users_as_options($user['id']);
        $this->template->body->post = $post;
    }
    
    /* CLAIM TODOS */
    
    public function action_ajax_new_claim_todo()
    {
        
        $post = $this->request->post();
        if (!empty($post)) {
            $todo_model = new Model_Todos();
            if ($todo_model->validate_contact_todo($post) AND $todo_model->add_claim_todo($post)) {
                $data['msg'] = '<div class="alert alert-success"><a data-dismiss="alert" class="close">×</a>To Do added!</div>';
                $data['status'] = 'ok';
                $this->template->body = 'ok';
                return true;
            } else {
                //Set error message
            }
        }
        
        $todos = new Model_Todos();
        $user = Auth::instance()->get_user();
        if (!empty($post)) {
            $user['id'] = $post['to_user_id'];
        }
        $this->template->body = View::factory('add_todo')->set('plugin', 'claim');
        $claim_id = $this->request->query('claim_id');
        $contact_id = $this->request->query('contact_id');
        
        $this->template->body->id = $claim_id;
        $this->template->body->contact_id = $contact_id;
        $this->template->body->to_user_options = $todos->get_users_as_options($user['id']);
        $this->template->body->post = $post;
    }
    
    public function action_ajax_get_last_todos()
    {
        $amount = 3;
        $contact_id = $this->request->query('contact_id');
        
        $todos_m = new Model_Todos();
        $todos = $todos_m->get_last_todos($amount, $contact_id);
        $this->template->body = json_encode($todos);
    }
    
    public function action_ajax_edit_listed_todo()
    {
        $todo_id = $this->request->param('id');
        $this->template->body = View::factory('ajax_edit_todo');
        $todos_m = new Model_Todos();
        $todo = $todos_m->get_todo($todo_id);
        $this->template->body->todo_id = $todo_id;
        $this->template->body->todo = $todo;
        $this->template->body->to_user_options = $todos_m->get_users_as_options($todo['to_user_ids']);
    }
    
    /* UPDATE TO DOS */
    
    public function action_ajax_update_todo()
    {
        
        $post = $this->request->post();
        if (!empty($post)) {
            $todo_model = new Model_Todos();
            if ($todo_model->validate_contact_todo($post) AND $todo_model->update_todo($post['todo_id'], $post)) {
                $data['status'] = 'ok';
                $this->template->body = json_encode($data);
                IbHelpers::set_message('info popup_box', 'To Do Updated!');
                IbHelpers::alert('info popup_box', 'To Do Updated!');
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function action_ajax_list_todos2()
    {
        $type = $this->request->query('type');
        $rid = $this->request->query('rid');
        $todos = new Model_Todos();
        $result = $todos->get_todos_related_to_customer($contact_id);
        
        $this->template->body = View::factory('tab_list_todos')->set('contact_id', $contact_id);
        $this->template->body->todos = $result;
        
    }
    
    public function action_autocomplete_todos()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        
        $params = array();
        $params['keyword'] = $this->request->query('term');
        $params['to_user_id'] = $this->request->query('to_user_id');
        
        if (!Auth::instance()->has_access('todos_manage_all')) {
            $params['to_user_id'] = Auth::instance()->get_user()['id'];
        }
        
        $todos = Model_Todos::search($params);
        foreach ($todos as $i => $todo) {
            $todos[$i]['label'] = $todo['title'];
            $todos[$i]['value'] = $todo['id'];
            
        }
        
        echo json_encode($todos);
    }
    
    public function action_cron() {
        $this->auto_render = false;
        Model_Todos::update_exam_status();
    }

    public function action_ajax_calculate_points()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $q = $this->request->query();

        $schema = ORM::factory('Todo_GradingSchema')->where('id', '=', $q['schema_id'])->find_undeleted();
        $result = $schema->get_result($q);
        if ($result['grade']->id) {
            $return = ['success' => true, 'grade' => $result['grade']->grade, 'points' => $result['points']];
        }
        else {
            $return = ['success' => false, 'grade' => '', 'points' => '', 'error' => 'Could not find grade'];
        }

        echo json_encode($return);
    }
}
