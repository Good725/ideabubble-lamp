<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Surveys extends Controller_Cms {

    public function before()
    {
        parent::before();

        // submenu items for cms
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = [
            'Surveys' => [
                [
                    'icon' => 'surveys',
                    'name' => 'Surveys',
                    'link' => 'admin/surveys'
                ]
            ]
        ];

        if (Auth::instance()->has_access('surveys_old')) {

            $this->template->sidebar->menus['Surveys'] = array_merge($this->template->sidebar->menus['Surveys'], [
                [
                    'icon' => 'surveys',
                    'name' => 'Surveys old',
                    'link' => 'admin/surveys/old'
                ], [
                    'name' => 'Questions',
                    'link' => 'admin/surveys/questions'
                ], [
                    'name' => 'Answers',
                    'link' => 'admin/surveys/answers'
                ], [
                    'name' => 'Answers Types',
                    'link' => 'admin/surveys/types'
                ], [
                    'name' => 'Groups',
                    'link' => 'admin/surveys/groups'
                ]
            ]);

        }

        // Set up breadcrumbs
        $this->template->sidebar->breadcrumbs = array(
            array(
                'name' => 'Home',
                'link' => '/admin'
            ),
            array(
                'name' => 'Surveys',
                'link' => '/admin/surveys'
            )
        );

        switch ($this->request->action())
        {
            case 'questions':
            case 'add_edit_question':
                $this->template->sidebar->breadcrumbs[] = array(
                    'name' => 'Questions', 'link' => '/admin/surveys/questions'
                );
                $this->template->sidebar->tools = '<a href="/admin/surveys/add_edit_question"><button type="button" class="btn" id="add_new_question">New Question</button></a>';
                break;

            case 'answers':
            case 'add_edit_answer':
                $this->template->sidebar->breadcrumbs[] = array('name' => 'Answers', 'link' => '/admin/surveys/answers');
                $this->template->sidebar->tools = '<a href="/admin/surveys/add_edit_answer"><button type="button" class="btn" id="add_new_answer">New Answer</button></a>';
                break;

            case 'types':
            case 'add_edit_type':
                $this->template->sidebar->breadcrumbs[] = array('name' => 'Types', 'link' => '/admin/surveys/types');
                break;

            case 'sequences':
            case 'add_edit_sequence':
                $this->template->sidebar->breadcrumbs[] = array(
                    'name' => 'sequences', 'link' => '/admin/surveys/sequences'
                );
                $this->template->sidebar->tools = '<a href="/admin/surveys/add_edit_sequence"><button type="button" class="btn" id="add_new_">New Sequence</button></a>';
                break;

            case 'groups':
            case 'add_edit_group':
                $this->template->sidebar->breadcrumbs[] = array(
                    'name' => 'groups', 'link' => '/admin/surveys/groups'
                );
                $this->template->sidebar->tools = '<a href="/admin/surveys/add_edit_group"><button type="button" class="btn" id="add_new_">New Group</button></a>';

                break ;

            case 'edit_questionnaire':
            case 'index':
                $this->template->sidebar->tools = '<a href="/admin/surveys/edit_questionnaire" class="btn btn-primary">New survey</a>';
                break;

            default:
                $this->template->sidebar->tools = '<a href="/admin/surveys/add_edit_survey"><button type="button" class="btn" id="add_new_survey">New survey</button></a>';
                break;
        }

        // Default Script
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('surveys', 'js/survey.js', ['cachebust' => true]) .'"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validate.min.js"></script>';
        // Default Styles
        $this->template->styles[URL::get_engine_plugin_assets_base('surveys') . 'css/survey.css'] = 'screen';
    }

    public function action_old()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/survey_list.js"></script>';

        //get_icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('surveys');

        //select template to display
        $this->template->body = View::factory('survey_list');
    }

    public function action_index()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/survey_list.js"></script>';

        //get_icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('surveys');

        //select template to display
        $this->template->body = View::factory('survey_list');
    }

    public function action_ajax_get_all_surveys()
    {
        $post = $this->request->post();

        // Hack for now
        $is_surveys2 = strpos($this->request->referrer(), '/old') === false;

        if (isset($post['sEcho']))
        {
            $return['sEcho'] = $post['sEcho'];
        }
        $return['iTotalRecords'] = ORM::factory('Survey')->where('deleted', '=', 0)->count_all();
        $sort = 'date_modified';

        // Use the column id's for the search items
        switch ($post['iSortCol_0'])
        {
            case 0:
                $sort = 'id';
                break;
            case 1:
                $sort = 'title';
                break;
            case 2:
                $sort = 'questions';
                break;
            case 3:
                $sort = 'start_date';
                break;
            case 4:
                $sort = 'end_date';
                break;
            case 5:
                $sort = 'created_on';
                break;
            case 6:
                $sort = 'updated_on';
                break;
            case 7:
                $sort = 'user';
                break;
            case 9:
                $sort = 'publish';
                break;
        }
        $model = new Model_Survey();
        $return['aaData'] = $model->get_all_surveys($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch'], $is_surveys2);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_save_survey()
    {
        try
        {
            $post = $this->request->post();

            $user = Auth::instance()->get_user();

            $id = $post['id'];
            $questions = json_decode($post['questions_ids']);
            $sequences = json_decode($post['sequence_list']);
            $data = ORM::factory('Survey', $id);
            $data->values($post);
            $data->set('start_date',($post['start_date'] !== '' ) ? date("Y-m-d H:i:s",strtotime($post['start_date'])) : NULL);
            $data->set('end_date',($post['end_date'] !== '' ) ? date("Y-m-d H:i:s",strtotime($post['end_date'])) : NULL);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            $data->set('url_title', IbHelpers::slugify($data->title));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->set('start_date',($post['expiry'] == 0 ) ? $data->created_on : $data->start_date );
            $data->set('end_date',($post['expiry'] == 0 ) ? NULL : $data->end_date );
            $data->set('is_backend', $post['is_backend']);
            $data->set('course_id', $post['course_id'] ?? NULL);
            $data->set('contact_id', $post['contact_id'] ?? NULL);
            $data->save();
            $id = $data->id;
            // Insert and update question list
            $survey_questions = array();
            foreach ($questions as $key => $question)
            {
                $survey_questions[] = Model_Survey::save_question_list($id, $question->question_id, $key, $question->group_id);
            }
            // delete questions removed
            $question_list = ORM::factory('SurveyHasQuestion')->where('survey_id', '=', $id)->find_all();
            foreach ($question_list as $q)
            {
                if ( ! in_array($q->id, $survey_questions))
                {
                    Model_Survey::delete_from_question_list($q->id);
                }
            }

            $s_data = ORM::factory('Sequence', $post['sequence_id']);
            $s_data->values($post);
            $s_data->set('updated_by', $user['id']);
            $s_data->set('updated_on', date("Y-m-d H:i:s"));
            $s_data->set('survey_id',$id);
            $s_data->set('title',$post['title']);
            if ( ! is_numeric($post['sequence_id']))
            {
                $s_data->set('created_by', $user['id']);
                $s_data->set('created_on', date("Y-m-d H:i:s"));
            }
            $s_data->save();

            if (empty($sequences))
            {
                $sequence = $s_data->id;
                $question_list = ORM::factory('SurveyHasQuestion')->where('survey_id', '=', $id)->where('publish','=',1)->where('deleted','=',0)->order_by('order_id')->find_all();
                foreach($question_list as $key=>$q)
                {
                    $question = ORM::factory('Question',$q->question_id);
                    $answers = DB::select('id')->from('plugin_survey_answer_options')->where('answer_id', '=', $question->answer_id)->execute()->as_array();
                    $next_question = DB::select('question_id')->from('plugin_survey_has_questions')->where('survey_id','=',$id)->where('publish','=',1)->where('order_id','=',($q->order_id+1))->execute()->get('question_id');
                    $action = $next_question  ? 0 : 1;
                    foreach($answers as $k=>$answer)
                    {
                        $item = ORM::factory('SequenceItems');
                        $item->set('updated_by', $user['id']);
                        $item->set('updated_on', date("Y-m-d H:i:s"));
                        $item->set('created_by', $user['id']);
                        $item->set('created_on', date("Y-m-d H:i:s"));
                        $item->set('sequence_id',$sequence);
                        $item->set('question_id',$q->question_id);
                        $item->set('answer_option_id',$answer['id']);
                        $item->set('target_id',$next_question);
                        $item->set('survey_action',$action);
                        $item->save();
                    }
                }
            }
            else
            {
                foreach ($sequences as $key => $sequence)
                {
                    $a = Model_Sequence::save_sequence_items($sequence, $s_data->id);
                }
            }

            IbHelpers::set_message('The Survey: ' . $post['title'] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/surveys/index');
            }
            else
            {
                $this->request->redirect('/admin/surveys/add_edit_survey/' . $id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving the Survey.', 'error popup_box');
            $this->request->redirect('admin/surveys/index');
        }
    }

    public function action_edit_questionnaire()
    {
        $questionnaire = new Model_Survey($this->request->param('id'));
        $answer_types = ORM::factory('Answertype')->order_by('title')->find_all();
        $types = ['Survey' => 'Feedback'];
        if (Model_Plugin::is_loaded('safety')) {
            $types['Pre-check'] = 'Pre-check';
        }

        $roles = ORM::factory('Roles')->order_by('role')->find_all_undeleted();
        $pages     = ORM::factory('Page')
            ->select([DB::expr("IF(`title` != '', `title`, `name_tag`)"), 'display_name'])
            ->order_by(DB::expr("IF(`title` != '', `title`, `name_tag`)"))
            ->find_all_undeleted()
            ->as_array('id', 'display_name')
        ;
        $documents = (class_exists('Model_Document') && class_exists('Model_Files'));
        $templates = $documents ? Model_Document::get_template() : [];
        $stock_categories = ORM::factory('Product_Category')->order_by('category')->find_all_undeleted()->as_array('id', 'category');

        $crumb = $this->request->param('id') ? 'Edit survey' : 'Add survey';
        $this->template->sidebar->breadcrumbs[] = ['link' => '#', 'name' => $crumb];

        $this->template->styles[URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';

        $this->template->body = View::factory(
            'questionnaire_form',
            compact('questionnaire', 'answer_types', 'pages', 'roles', 'stock_categories', 'templates', 'types')
        );
    }

    public function action_save_questionnaire()
    {
        try {
            $questionnaire = new Model_Survey($this->request->param('id'));
            $questionnaire->save_questionnaire($this->request->post());

            $message = 'Questionnaire #' . $questionnaire->id . ': ' . $questionnaire->title . ' has been saved.';
            IbHelpers::set_message(htmlentities($message), 'success popup_box');
            $this->request->redirect('/admin/surveys/edit_questionnaire/'.$questionnaire->id);
        } catch (Exception $e) {
            throw $e;
            Log::instance()->add(Log::ERROR, "Error saving questionnaire.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Unexpected error saving questionnaire. If this problem continues, please ask an administrator to check the error logs.', 'error popup_box');
            $this->request->redirect('/admin/surveys');
        }
    }

    public function action_delete_questionnaire()
    {
        try {
            $questionnaire = new Model_Survey($this->request->param('id'));
            $questionnaire->delete_and_save();

            $message = 'Questionnaire #' . $questionnaire->id . ' has been deleted.';
            IbHelpers::set_message(htmlentities($message), 'success popup_box');

            // Track activity
            $user = Auth::instance()->get_user();
            $activity = new Model_Activity();
            $activity
                ->set_item_type('survey')
                ->set_action('delete')
                ->set_item_id($questionnaire->id)
                ->set_user_id($user['id'])
                ->save();

        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error deleting questionnaire.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Unexpected error deleting questionnaire. If this problem continues, please ask an administrator to check the error logs.', 'error popup_box');
        }

        $this->request->redirect('/admin/surveys');
    }

    public function action_download_csv()
    {
        $survey   = new Model_Survey($this->request->param('id'));
        $csv_data = $survey->get_csv_data();

        ExportCsv::export_report_data_array($this->response, $csv_data, $survey->url_title);
    }

    public function action_add_edit_survey()
    {
        $id = $this->request->param('id');
        Session::instance()->set('survey_id',$id);
        $data = ORM::factory('Survey', $id);
        $documents = ( class_exists('Model_Document') AND class_exists('Model_Files') );
        $templates = $documents ? Model_Document::get_template() : array();
        $pages     = class_exists('Model_Pages') ? Model_Pages::get_page_list() : array();
        $group_list = DB::select('has.id', 'has.group_id', 'has.order_id', 'g.title')
            ->from(array('plugin_survey_has_groups','has'))
            ->join(array('plugin_survey_groups','g'))
            ->on('has.group_id', '=', 'g.id')
            ->where('has.publish','=',1)
            ->where('has.deleted','=',0)
            ->where('has.group_id','!=',null)
            ->where('has.survey_id','=',$id)
            ->order_by('has.order_id')
            ->execute()
            ->as_array();

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3') . '/js/documents.js"></script>';

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/survey_form.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::site() . 'assets/default/js/documents.js"></script>';
        $this->template->body = VIEW::factory('survey_form', array('templates' => $templates,'documents'=>$documents, 'pages'=>$pages));
        $this->template->body->survey = $data;
        $this->template->body->course_info = (isset($data->course_id)) ? Model_Courses::get_course($data->course_id) : null;
        $this->template->body->groups = ORM::factory('Group')->where('publish', '=', 1)->and_where('deleted', '=', 0)->find_all();
        $this->template->body->questions = ORM::factory('Question')->where('publish', '=', 1)->and_where('deleted', '=', 0)->find_all();
        $this->template->body->question_grp = Model_Survey::get_question_with_group();
        $this->template->body->group_list = $group_list;
        $this->template->body->question_list = ORM::factory('SurveyHasQuestion')->where('survey_id', '=', $id)->where('publish', '=', 1)->and_where('deleted', '=', 0)->order_by('group_id')->order_by('order_id')->find_all();
        $this->template->body->sequence = ORM::factory('Sequence')->where('survey_id', '=', $id)->find();
        $this->template->body->contact_types = Model_Contacts3::get_types();
    }

    public function action_ajax_get_survey_selected_question_preview()
    {
        $post = $this->request->post();
        $questions = json_decode($post['questions']);

        $question_list = Model_Survey::get_questionnaire_preview($questions);

        $results = json_encode($question_list);
        exit($results);
    }

    public function action_ajax_publish_survey()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Survey', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('publish', $item->publish == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Survey', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_delete_survey()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Survey', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('deleted', $item->deleted == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Survey', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_questions()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/question_list.js"></script>';

        //select template to display
        $this->template->body = View::factory('question_list');
    }

    public function action_ajax_get_all_questions()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
        {
            $return['sEcho'] = $post['sEcho'];
        }
        $return['iTotalRecords'] = ORM::factory('Question')->where('deleted','=',0)->count_all();
        $sort = 'date_modified';

        // Use the column id's for the search items
        switch ($post['iSortCol_0'])
        {
            case 0:
                $sort = 'id';
                break;
            case 1:
                $sort = 'title';
                break;
            case 2:
                $sort = 'answer';
                break;
            case 3:
                $sort = 'created_on';
                break;
            case 4:
                $sort = 'updated_on';
                break;
            case 5:
                $sort = 'user';
                break;
            case 7:
                $sort = 'publish';
                break;
        }
        $model = new Model_Question();
        $return['aaData'] = $model->get_all_questions($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_save_question()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $data = ORM::factory('Question', $this->request->post('id'));
            $data->values($post);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->save();
            IbHelpers::set_message('The Question: ' . $post['title'] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/surveys/questions');
            }
            else
            {
                $this->request->redirect('/admin/surveys/add_edit_question/' . $data->id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving Question.', 'error popup_box');
            $this->request->redirect('admin/surveys/questions');
        }
    }

    public function action_add_edit_question()
    {
        $id = $this->request->param('id');
        $data = ORM::factory('Question', $id);

        $options = Model_Question::get_question_answer($id);

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/question_form.js"></script>';
        $this->template->body = VIEW::factory('question_form', array('options' => $options));
        $this->template->body->question = $data;
        $this->template->body->answers = ORM::factory('Answer')->where('publish', '=', 1)->and_where('deleted', '=', 0)->find_all();
    }

    public function action_ajax_get_question_answer()
    {
        $id = $this->request->post('id');
        $options = Model_Question::get_question_answer(NULL,$id);
        exit(json_encode($options));
    }

    public function action_ajax_publish_question()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Question', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('publish', $item->publish == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Question', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_delete_question()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Question', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('deleted', $item->deleted == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Question', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_answers()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/answer_list.js"></script>';

        //select template to display
        $this->template->body = View::factory('answer_list');
    }

    public function action_ajax_get_all_answers()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
        {
            $return['sEcho'] = $post['sEcho'];
        }
        $return['iTotalRecords'] = ORM::factory('Answer')->where('deleted','=',0)->count_all();
        $sort = 'date_modified';

        // Use the column id's for the search items
        switch ($post['iSortCol_0'])
        {
            case 0:
                $sort = 'id';
                break;
            case 1:
                $sort = 'title';
                break;
            case 2:
                $sort = 'type';
                break;
            case 3:
                $sort = 'group_name';
                break;
            case 4:
                $sort = 'created_on';
                break;
            case 5:
                $sort = 'updated_on';
                break;
            case 6:
                $sort = 'user';
                break;
            case 8:
                $sort = 'publish';
                break;
        }
        $model = new Model_Answer();
        $return['aaData'] = $model->get_all_answers($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_save_answer()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $option_list = json_decode($post['option_list']);
            $data = ORM::factory('Answer', $this->request->post('id'));
            $data->values($post);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->save();
            // Insert and Update Options
            $option_id = array();
            if (empty($option_list))
            {
                $option_id[] = Model_Answer::save_option_list('', $data->id, '', 0, 0);
            }
            else
            {
                foreach ($option_list as $key => $option)
                {
                    $option_id[] = Model_Answer::save_option_list($option->id, $data->id, $option->label, $option->value, $key);
                }
            }
            $options = ORM::factory('AnswerOption')->where('answer_id', '=', $data->id)->find_all();
            foreach ($options as $option)
            {
                if ( ! in_array($option->id, $option_id))
                {
                    Model_Answer::delete_from_option_list($option->id);
                }
            }
            IbHelpers::set_message('The Answer: ' . $post['title'] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/surveys/answers');
            }
            else
            {
                $this->request->redirect('/admin/surveys/add_edit_answer/' . $data->id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving the Answer.', 'error popup_box');
            $this->request->redirect('admin/surveys/answers');
        }
    }

    public function action_add_edit_answer()
    {
        $id = $this->request->param('id');
        $data = ORM::factory('Answer', $id);

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/answer_form.js"></script>';
        $this->template->body = VIEW::factory('answer_form');
        $this->template->body->answer = $data;
        $this->template->body->types = ORM::factory('Answertype')->where('publish', '=', 1)->find_all();
        $this->template->body->options = ORM::factory('AnswerOption')->where('answer_id', '=', $id)->where('publish', '=', 1)->and_where('deleted', '=', 0)->order_by('order_id')->find_all();
    }

    public function action_ajax_publish_answer()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Answer', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('publish', $item->publish == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Answer', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_delete_answer()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Answer', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('deleted', $item->deleted == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Answer', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_groups()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/group_list.js"></script>';

        $groups = Model_Group::get_all();
        //select template to display
        $this->template->body = View::factory('group_list');
        $this->template->body->groups = $groups;
    }

    public function action_add_edit_group()
    {
        $id = $this->request->param('id');
        $data = ORM::factory('Group', $id);

        $this->template->body = VIEW::factory('group_form');
        $this->template->body->group = $data;
    }

    public function action_save_group()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $data = ORM::factory('Group',$post['id']);
            $data->values($post);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->save();
            IbHelpers::set_message('The Group: ' . $data->id . ' was ' . (is_numeric($post['id']) ? 'Updated' : 'Created' ). ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/surveys/groups');
            }
            else
            {
                $this->request->redirect('/admin/surveys/add_edit_group/' . $data->id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving the group.', 'error popup_box');
            $this->request->redirect('admin/surveys/groups');
        }
    }

    public function action_ajax_publish_group()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Group', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('publish', $item->publish == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Group', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_get_groups()
    {
        $post = $this->request->post();
        $id = (isset($post['id'])) ? $post['id'] : '';
        $this->auto_render = false;
        exit(json_encode(Model_Group::get_groups($id)));
    }

    public function action_ajax_delete_group()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Group', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('deleted', $item->deleted == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Group', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_sequences()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/sequence_list.js"></script>';

        //select template to display
        $this->template->body = View::factory('sequence_list');
    }

    public function action_ajax_get_all_sequences()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
        {
            $return['sEcho'] = $post['sEcho'];
        }
        $return['iTotalRecords'] = ORM::factory('Sequence')->count_all();
        $sort = 'date_modified';

        // Use the column id's for the search items
        switch ($post['iSortCol_0'])
        {
            case 0:
                $sort = 'id';
                break;
            case 1:
                $sort = 'title';
                break;
            case 2:
                $sort = 'survey_id';
                break;
            case 3:
                $sort = 'survey_title';
                break;
            case 4:
                $sort = 'created_on';
                break;
            case 5:
                $sort = 'updated_on';
                break;
            case 6:
                $sort = 'user';
                break;
            case 8:
                $sort = 'publish';
                break;
        }
        $model = new Model_Sequence();
        $return['aaData'] = $model->get_all_sequences($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_save_sequence()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $sequences = json_decode($post['sequence_list']);
            $data = ORM::factory('Sequence', $this->request->post('id'));
            $data->values($post);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->save();

            foreach ($sequences as $key=>$sequence)
            {
                $a = Model_Sequence::save_sequence_items($sequence,$data->id);
            }

            IbHelpers::set_message('The Sequence: ' . $data->id . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/surveys/sequences');
            }
            else
            {
                $this->request->redirect('/admin/surveys/add_edit_sequence/' . $data->id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving the Sequence.', 'error popup_box');
            $this->request->redirect('admin/surveys/sequences');
        }
    }

    public function action_add_edit_sequence()
    {
        $id = $this->request->param('id');
        $data = ORM::factory('Sequence', $id);

        $survey = View::factory('set_sequence_tab')->set('sequence', $data)->set('survey', ORM::factory('Survey'));

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/sequence_form.js"></script>';
        $this->template->body = VIEW::factory('sequence_form',array('survey'=>$survey));
        $this->template->body->sequence = $data;
        $this->template->body->surveys  = ORM::factory('Survey')->where('publish','=',1)->and_where('deleted','=',0)->find_all()->as_array();
    }

    public function action_ajax_get_survey_for_sequence()
    {
        $this->auto_render = FALSE;
        $sequence          = ORM::factory('Sequence', $this->request->post('id'));
        $survey            = ORM::factory('Survey',   $this->request->post('survey_id'));

        echo View::factory('set_sequence_tab')->set('survey', $survey)->set('sequence', $sequence);
    }

    public function action_ajax_publish_sequence()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Sequence', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('publish', $item->publish == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Sequence', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_publish_sequence_for_survey()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $survey_id = $data['survey_id'];
        $items = ORM::factory('Sequence')->where('survey_id','=',$survey_id)->find_all()->as_array();
        $answer = TRUE;
        foreach($items as $item) {
            $item->set('updated_by', $logged_in_user['id']);
            $item->set('updated_on', date("Y-m-d H:i:s"));
            $item->set('publish', 0);
            $item->save();
            $answer = TRUE;
        }
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_delete_sequence()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Sequence', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('deleted', $item->deleted == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Sequence', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }


    public function action_seed()
    {

        $db = Database::instance();
        try {
            $db->commit();

            switch ($this->request->param('id')) {
                case 'demo':
                    $survey = self::seed_demo();
                    IbHelpers::set_message('Demo survey has been created.', 'success popup_box');
                    $this->request->redirect('admin/surveys/add_edit_survey/'.$survey->id);
                    break;

                case 'investigations':
                    $survey = self::seed_investigations_management();
                    IbHelpers::set_message('Investigations management survey has been created.', 'success popup_box');
                    $this->request->redirect('admin/surveys/add_edit_survey/'.$survey->id);
                    break;
            }
        }
        catch (Exception $e) {
            $db->rollback();

            throw $e;
        }
    }

    function seed_demo()
    {
        // Save survey
        $survey = new Model_Survey();
        $survey->title = 'Demo survey';
        $survey->pagination = 1;
        $survey->save_with_moddate();

        // Save a group
        $group = new Model_Group();
        $group->title = 'Group 1';
        $group->save_with_moddate();

        // Get input types
        $input    = ORM::factory('Answertype')->where('stub', '=', 'input')->find_published();
        $radio    = ORM::factory('Answertype')->where('stub', '=', 'radio')->find_published();
        $textarea = ORM::factory('Answertype')->where('stub', '=', 'textarea')->find_published();

        $answers = [
            ['title' => 'Free text',          'type_id' => $input->id,    'options' => ['']],
            ['title' => 'Number of people',   'type_id' => $radio->id,    'options' => ['Less than 50', 'Less than 200', 'Less than 800', 'Less than 1500', '1500 or more']],
            ['title' => 'Frequency',          'type_id' => $radio->id,    'options' => ['Monthly', 'Quarterly', 'Yearly', 'Never']],
            ['title' => 'Payment options',    'type_id' => $radio->id,    'options' => ['Cash', 'Cheque', 'Credit card', 'Online', 'Bank transfer']],
            ['title' => 'Contact management', 'type_id' => $radio->id,    'options' => ['CRM', 'Excel sheet', 'Note book', 'Multiple platforms', 'Other']],
            ['title' => 'Attendance',         'type_id' => $textarea->id, 'options' => ['']],
            ['title' => 'Booking methods',    'type_id' => $radio->id,    'options' => ['Phone', 'Email', 'Website', 'Third-party software', 'Third-party company (agent etc.)']],
        ];

        $answer_objects = [];

        foreach ($answers as $answer) {
            // Save the answers
            $answer_object = new Model_Answer();
            $answer_object->title = $answer['title'];
            $answer_object->type_id = $answer['type_id'];
            $answer_object->save_with_moddate();

            $answer_objects[$answer['title']] = $answer_object;

            foreach ($answer['options'] as $key => $option) {
                // Save the answer options
                $answer_option = new Model_AnswerOption();
                $answer_option->label = $option;
                $answer_option->answer_id = $answer_object->id;
                $answer_option->order_id = $key;
                $answer_option->value = count($answer['options']) == 1 ? 0 : $key + 1;
                $answer_option->save_with_moddate();
            }
        }

        $questions = [
            ['question' => 'Business name',                    'answer_id' => $answer_objects['Free text']->id],
            ['question' => 'Number of staff',                  'answer_id' => $answer_objects['Free text']->id],
            ['question' => 'How do you manage your contacts?', 'answer_id' => $answer_objects['Contact management']->id],
            ['question' => 'Number of learners this year',     'answer_id' => $answer_objects['Number of people']->id],
            ['question' => 'How do you take bookings?',        'answer_id' => $answer_objects['Booking methods']->id],
            ['question' => 'How do you take payments?',        'answer_id' => $answer_objects['Payment options']->id],
            ['question' => 'How do you track attendance?',     'answer_id' => $answer_objects['Attendance']->id],
            ['question' => 'Do you run events?',               'answer_id' => $answer_objects['Frequency']->id]
        ];

        foreach ($questions as $key => $question) {
            // Save the questions
            $question_object = new Model_Question();
            $question_object->title = $question['question'];
            $question_object->answer_id = $question['answer_id'];
            $question_object->save_with_moddate();

            // Link the questions to the survey and set their order
            $shq = new Model_SurveyHasQuestion();
            $shq->survey_id   = $survey->id;
            $shq->group_id    = $group->id;
            $shq->question_id = $question_object->id;
            $shq->order_id    = $key;
            $shq->save_with_moddate();
        }

        return $survey;
    }


    function seed_investigations_management()
    {
        // Save survey
        $survey = new Model_Survey();
        $survey->title = 'Investigations management';
        $survey->pagination = 1;
        $survey->save_with_moddate();

        // Save a group
        $group = new Model_Group();
        $group->title = 'Group 1';
        $group->save_with_moddate();

        // Get input types
        $radio    = ORM::factory('Answertype')->where('stub', '=', 'radio')->find_published();

        $questions = [
            [
                'question' => 'An investigations manager’s background is typically',
                'options' => [
                    'A member of staff who knows everyone in the company',
                    'A former criminal with knowledge of theft and fraud',
                    'A mature male with a police or military background ',
                    'A young graduate who can be trained up by management'
                ]
            ], [
                'question' => 'Which is not part of a job description, the investigations manager will',
                'options' => [
                    'Report to senior management',
                    'Manage day to day running of the department',
                    'Be qualified and experienced',
                    'Maintain records and reports'
                ]
            ], [
                'question' => 'The main resources in an investigation department are',
                'options' => [
                    'Specialist audio and video equipment',
                    'Staff and equipment',
                    'Police contacts and informants',
                    'Helpful managers and directors'
                ]
            ], [
                'question' => 'The term defamation means',
                'options' => [
                    'To accuse someone of a crime',
                    'Libel in a verbal form',
                    'A mistaken believe that there was reasonable cause',
                    'The wrongful publication of a false statement about a person'
                ]
            ], [
                'question' => 'Which of the following is correct',
                'options' => [
                    'There are eight rules for data protection',
                    'There are no rules for data protection',
                    'The Data Commissioner issues guidelines for data holders',
                    'There are six rules for data protection'
                ]
            ], [
                'question' => 'Which of the following is incorrect, liaison includes',
                'options' => [
                    'Internal and external',
                    'Formal and informal',
                    'Friends and family',
                    'Individual and organisational'
                ]
            ], [
                'question' => 'Insurance Indemnity means',
                'options' => [
                    'The company can profit from all claims',
                    'Someone else pays for everything',
                    'There is no need to worry about claims being made',
                    'The level of exposure to the company is reduced'
                ]
            ], [
                'question' => 'An ideal method of reporting confidential matters to a senior manager would be 	to',
                'options' => [
                    'Leave a written report with a secretary',
                    'Have a face to face meeting in a private office',
                    'Meet after work outside the office in a pub',
                    'Use Facebook or twitter'
                ]
            ], [
                'question' => 'The definition of communications is to',
                'options' => [
                    'Make known',
                    'Provide information',
                    'Listen actively',
                    'Pay attention'
                ]
            ], [
                'question' => 'Which is incorrect',
                'options' => [
                    'The focus of an internal investigation would include for example staff misconduct  ',
                    'The focus of an external investigation would include for example suppliers and customers',
                    'Investigations agencies are not permitted to carry out internal investigations',
                    'Due diligence reports and background searches may only be completed by internal investigation staff'
                ]
            ], [
                'question' => 'Which is incorrect, undercover operations should never be considered in',
                'options' => [
                    'A family setting',
                    'A social setting',
                    'A unionised workplace',
                    'A state body'
                ]
            ], [
                'question' => 'Which is incorrect',
                'options' => [
                    'It is best practice to report all suspected criminal matters to the police',
                    'Mandatory reporting is in place in the case of certain workplace accidents',
                    'All matters remain in house until sufficient evidence is in place',
                    'A state enforcement body may initiate a prosecution against the wishes of the investigations manager'
                ]
            ], [
                'question' => 'The purpose of restricting access to certain information is to',
                'options' => [
                    'Make it seem important',
                    'Maintain its integrity',
                    'Keep claims down',
                    'Comply with data protection'
                ]
            ], [
                'question' => 'In which forms does evidence appear',
                'options' => [
                    'Oral, documentary and physical',
                    'In the newspaper and television',
                    'Oral, documentary, information from witnesses ',
                    'Taking photographs of subjects '
                ]
            ], [
                'question' => 'Evidence is only considered proof',
                'options' => [
                    'When it is collected',
                    'When it is tested in court',
                    'When it is given by the police ',
                    'When it is used '
                ]
            ], [
                'question' => 'Best practice office administration includes the use of',
                'options' => [
                    'Lockable filing cabinets and storage cabinets',
                    'A personal system that only you understand',
                    'Keeping everything in sealed storage containers',
                    'Keeping all records in a secure store away from the office'
                ]
            ], [
                'question' => 'Which is incorrect, the benefits of a computer system include',
                'options' => [
                    'Cost effective way of storing information',
                    'Fast and accurate retrieval of information',
                    'All files are easily accessible by everyone',
                    'Access to the Internet for research purposes'
                ]
            ], [
                'question' => 'An effective filing system must',
                'options' => [
                    'Be cost effective',
                    'Include a clear retrieval process',
                    'Be easily accessible to all staff',
                    'Comply with ISO requirements'
                ]
            ], [
                'question' => 'Loss prevention is best described as',
                'options' => [
                    'Having proper security measures in place to combat crime',
                    'The implementation of effective security systems and procedures in the workplace',
                    'The process of identifying and safeguarding all property at risk',
                    'The implementation of measures to reduce the risk of losing or minimise the amount lost'
                ]

            ], [
                'question' => 'Design of a comprehensive security system involves',
                'options' => [
                    'Electronic, hardware, manpower and procedures',
                    'Twenty-four hour security guard on site',
                    'Alarm system, access control and CCTV',
                    'Comprehensive access control procedures'
                ]
            ]
        ];

        foreach ($questions as $key => $question) {
            // Save the answer (group of responses)
            $answer_object = new Model_Answer();
            $answer_object->title = $question['question'];
            $answer_object->type_id = $radio->id;
            $answer_object->save_with_moddate();

            // Save the options for each answer
            foreach ($question['options'] as $option) {
                $option_object = new Model_AnswerOption();
                $option_object->label = $option;
                $option_object->answer_id = $answer_object->id;
                $option_object->order_id = $key;
                $option_object->value = count($question['options']) == 1 ? 0 : $key + 1;
                $option_object->save_with_moddate();
            }

            // Save the question
            $question_object = new Model_Question();
            $question_object->title = $question['question'];
            $question_object->answer_id = $answer_object->id;
            $question_object->save_with_moddate();

            // Link the questions to the survey and set their order
            $shq = new Model_SurveyHasQUestion();
            $shq->survey_id   = $survey->id;
            $shq->group_id    = $group->id;
            $shq->question_id = $question_object->id;
            $shq->order_id    = $key;
            $shq->save_with_moddate();
        }

        return $survey;
    }

    public function action_unseed()
    {
        switch ($this->request->param('id')) {
            case 'demo':
                $survey = ORM::factory('Survey')->where('title', '=', 'Demo survey')->find();
                $survey->expunge_survey();

                IbHelpers::set_message('Demo survey has been deleted.', 'success popup_box');
                break;

            case 'investigations':
                $survey = ORM::factory('Survey')->where('title', '=', 'Investigations management')->find();
                $survey->expunge_survey();

                IbHelpers::set_message('Investigations management survey has been deleted.', 'success popup_box');
                break;
        }
        $this->request->redirect('admin/surveys/');
    }

    public function action_types()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('surveys') . 'js/type_list.js"></script>';

        //select template to display
        $this->template->body = View::factory('type_list');
    }

    public function action_ajax_get_all_types()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
        {
            $return['sEcho'] = $post['sEcho'];
        }
        $return['iTotalRecords'] = ORM::factory('Answertype')->count_all();
        $sort = 'date_modified';

        // Use the column id's for the search items
        switch ($post['iSortCol_0'])
        {
            case 0:
                $sort = 'id';
                break;
            case 1:
                $sort = 'title';
                break;
            case 2:
                $sort = 'publish';
                break;
        }
        $model = new Model_Answertype();
        $return['aaData'] = $model->get_all_types($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_save_type()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $data = ORM::factory('Answertype', $this->request->post('id'));
            $data->values($post);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->save();
            IbHelpers::set_message('The Type: ' . $post[''] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/surveys/types');
            }
            else
            {
                $this->request->redirect('/admin/surveys/add_edit_type/' . $data->id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving the type.', 'error popup_box');
            $this->request->redirect('admin/survey/types');
        }
    }

    public function action_add_edit_type()
    {
        $id = $this->request->param('id');
        $data = ORM::factory('Answertype', $id);

        $this->template->body = VIEW::factory('type_form');
        $this->template->body->type = $data;
    }

    public function action_ajax_publish_type()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Answertype', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('publish', $item->publish == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Answertype', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }

    public function action_ajax_delete_type()
    {
        $data = $this->request->post();
        $result = array('status' => 'error');
        $logged_in_user = Auth::instance()->get_user();
        $item = ORM::factory('Answertype', $data['id']);
        $item->set('updated_by', $logged_in_user['id']);
        $item->set('updated_on', date("Y-m-d H:i:s"));
        $item->set('delete', $item->deleted == 1 ? 0 : 1);
        $item->save();
        $answer = ORM::factory('Type', $data['id']);
        if ($answer)
        {
            $result['status'] = 'success';
        }
        exit(json_encode($result));
    }
    
    public function action_ajax_display_survey_details()
    {
        $this->auto_render = false;
        $booking_id = $this->request->post('booking_id');
        $booking_data =  DB::select('plugin_ib_educate_booking_has_schedules.schedule_id', 'plugin_courses_schedules.course_id')->join('plugin_courses_schedules',
            'INNER')->on('plugin_courses_schedules.id', '=', 'plugin_ib_educate_booking_has_schedules.schedule_id')
            ->from('plugin_ib_educate_booking_has_schedules')->where('plugin_ib_educate_booking_has_schedules.booking_id', '=', $booking_id)->execute()->current();
        $survey_data = Model_Survey::get_survey_related_to_course($booking_data['course_id']);

        // If starting afresh, clean existing session data for the survey and populate some fields with booking data
        if ($this->request->post('start_new') && !empty($survey_data['id'])) {

            $booking       = new Model_Booking_Booking($booking_id);
            $survey        = new Model_Survey($survey_data['id']);
            $has_questions = $survey->has_questions->find_all_published();
            $responses     = [];

            // See if any of the questions appear to match details from the booking
            // Auto-populate them if so
            foreach ($has_questions as $has_question) {
                $question = $has_question->question;

                // Student last name
                if (preg_match('/(student\s*last\s*name|student\s*surname)/i', $question->title)) {
                    $responses[$question->id] = $booking->applicant->last_name;
                }
                // Student first name
                else if (preg_match('/(student\s*first\s*name|student\s*name)/i', $question->title)) {
                    $responses[$question->id] = $booking->applicant->first_name;
                }
                // School
                else if (strtolower(trim($question->title)) == 'school') {
                    $responses[$question->id] = $booking->applicant->school->name;
                }
                // Academic year
                else if (preg_match('/(school\s*year|academic\s*year)/i', $question->title)) {
                    $responses[$question->id] = $booking->applicant->academic_year->title;
                }
                // Host family last name
                else if (preg_match('/(host\s*family\s*last\s*name|host\s*family\s*surname)/i', $question->title)) {
                    $responses[$question->id] = $booking->get_host_family()->last_name;
                }
                // Host family first name
                else if (preg_match('/(host\s*family\s*first\s*name)/i', $question->title)) {
                    $responses[$question->id] = $booking->get_host_family()->first_name;
                }
            }

            // The survey is starting afresh, so clear existing response session data
            // And overwrite with answers from the booking data
            Session::instance()->set('survey_'.$survey_data['id'], $responses);
        }

        // No survey exists related to course
        if (!isset($survey_data)){
            $survey_view = '<span class="inline-error">No survey exists relating to course.</span>';
        } else {
            $survey_view = Model_Survey::render_survey($survey_data['id']);
        }
        
        $this->response->body($survey_view);
    }
    
    public function action_ajax_display_survey_related_to_booking()
    {
        $this->auto_render = false;
        $booking_id = $this->request->post('booking_id');
        $surveys = Model_Survey::get_surveys_related_to_booking($booking_id);
        // Get Survey Result Meta Data Here
        // Model_Survey::get_answers_from_survey_result('1', '1', $surveys[0]['survey_result_id']);
        $view = array(
           'surveys' => $surveys
        );
        $this->response->body(View::factory('survey_bookings_list', $view));
    }

    public function action_autocomplete()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $select = DB::select(array('id', 'value'), array('title', 'label'))
            ->from('plugin_survey')
            ->and_where('deleted', '=', 0)
            ->order_by('title')
            ->limit(10);
        $term = $this->request->query('term');
        if ($term != '') {
            $select->and_where('title', 'like', '%' . $term . '%');
        }

        $surveys = $select->execute()->as_array();
        echo json_encode($surveys);
    }
}
