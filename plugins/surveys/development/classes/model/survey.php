<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class name in 1 word CalendarEvent will look in the model class folder
 * Class Name with underscore Calendar_Event Need to be in subfolder calendar
 */
class Model_Survey extends ORM {
    protected $_table_name = 'plugin_survey';

    protected $_has_many = [
        'has_questions' => ['model' => 'SurveyHasQuestion'],
        'has_groups'    => ['model' => 'SurveyHasGroup'],
        'sequences'     => ['model' => 'Sequence'],
        'responses'     => ['model' => 'SurveyResult', 'foreign_key' => 'survey_id'],
    ];
    protected $_belongs_to = array(
        'stock_category' => ['model' => 'Product_Category', 'foreign_key' => 'stock_category_id'],
        'template'       => ['model' => 'Survey_File',      'foreign_key' => 'result_template_id'],
    );

    protected $_date_created_column = 'created_on';
    protected $_modified_by_column = 'updated_by';
    protected $_date_modified_column = 'updated_on';

    public function save_with_moddate()
    {
        $this->url_title = IbHelpers::slugify($this->title);
        parent::save_with_moddate();
    }

    public function get_url()
    {
        $name_tag = $this->url_title ? $this->url_title : urlencode($this->title);
        return URL::base().'survey/'.$name_tag;
    }

    public function get_groups()
    {
        $groups = [];
        $group_ids = [];

        // Get groups directly linked to the survey.
        $has_groups = $this->has_groups->order_by('order_id')->find_all_undeleted();
        foreach ($has_groups as $has_group) {
            if (!in_array($has_group->group_id, $group_ids)) {
                $group_ids[] = $has_group->group_id;

                $groups[$has_group->order_id] = $has_group->group;
            }
        }

        // Legacy support. Get groups, which are only linked to the survey via the questions.
        $has_questions = $this->has_questions->order_by('order_id')->find_all_undeleted();
        foreach ($has_questions as $has_question) {
            if ($has_question->group->id && !in_array($has_question->group->id, $group_ids)) {
                $group_ids[] = $has_question->group_id;

                $groups[$has_question->order_id] = $has_question->group;
            }
        }

        return $groups;
    }

    // Get all questions, in order
    public function has_questions()
    {
        $has_groups = $this->has_groups->order_by('order_id', 'asc')->find_all_undeleted();
        $has_questions = [];

        // Loop through groups, then loop through questions within the group
        // to get the questions in their overall order
        foreach ($has_groups as $has_group) {
            $has_questions = array_merge($has_questions, $this
                ->has_questions
                ->where('group_id', '=', $has_group->group_id)
                ->order_by('order_id', 'asc')
                ->find_all_undeleted()->as_array());
        }

        return $has_questions;
    }

    /**
     * Get the group that comes before a specified group within this survey
     *
     * @param $current_group_id - The ID of the current group
     * @return Model_Group
     */
    public function get_previous_group($current_group_id)
    {
        $groups = $this->get_groups();

        $previous_group = new Model_Group;
        foreach ($groups as $group) {
            if ($group->id == $current_group_id) {
                return $previous_group;
            }

            $previous_group = $group;
        }

        return $previous_group;
    }

    /**
     * Get the group that comes after a specified group within this survey
     *
     * @param $current_group_id - The ID of the current group
     * @return Model_Group
     */
    public function get_next_group($current_group_id)
    {
        $groups = $this->get_groups();

        $found_current = false;
        foreach ($groups as $group) {
            if ($found_current) {
                return $group;
            }

            $found_current = ($group->id == $current_group_id);
        }

        return new Model_Group();
    }

    /**
     * Get the next question in the survey
     *
     * @param $current_question_id - The ID of the current question
     * @param $option_id - The IF of the answer that was submitted, if applicable
     * @return Model_Question
     */
    public function get_next_question($current_question_id, $option_id = null)
    {
        $sequence = $this->sequences->find_undeleted();

        $current_question = new Model_Question($current_question_id);

        // If a sequence is defined, get the question linked to the current question/selected answer
        if ($sequence->id) {
            // Next question depends on the answer to the current question
            if ($current_question->type == 'radio' || $current_question->type == 'dropdown' && $option_id) {
                $sequence_item = $sequence->items
                    ->where('question_id', '=', $current_question_id)
                    ->where('answer_option_id', '=', $option_id)
                    ->find();
            }
            // Next question does not depend on the answer to the current question
            else {
                $sequence_item = $sequence->items
                    ->where('question_id', '=', $current_question_id)
                    ->find();
            }


            if ($sequence_item->target_id) {
                return new Model_Question($sequence_item->target_id);
            }
        }

        // Otherwise get the next ordered question
        $has_questions = $this->has_questions
            ->order_by('group_id')
            ->order_by('order_id')
            ->find_all_undeleted();
        $found_current = false;

        $i = 0;

        foreach ($has_questions as $has_question) {
            if ($found_current && $has_question->question_id != $current_question_id) {
                return $has_question->question;
            }
            $found_current = ($has_question->question_id == $current_question_id);

            $i++;
        }

        // No question found, return a blank
        return new Model_Question();
    }

    public function has_children()
    {
        return $this
            ->has_questions
            ->with('question')
            ->where('question.child_survey_id', 'is not', null)
            ->and_where('question.child_survey_id', '!=', 0)
            ->where_undeleted()
            ->count_all() > 0;
    }

    public function has_parents()
    {
        return ORM::factory('Question')->where('child_survey_id', '=', $this->id)->where_undeleted()->count_all() > 0;
    }

    public function where_top_level()
    {
        $subquery = DB::select('child_survey_id')
            ->distinct(true)
            ->from('plugin_survey_questions')
            ->where('child_survey_id', 'is not', null)
            ->where('deleted', '=', 0);
        return $this->where('id', 'not in', $subquery);
    }


    public function ungrouped_questions()
    {
        return $this
            ->has_questions
            ->and_where_open()
                ->where('group_id', '=', 0)
                ->or_where('group_id', '=', null)
                ->or_where('group_id', '=', '')
            ->and_where_close()
            ->order_by('order_id');
    }


    public function save_questionnaire($data)
    {
        $db = Database::instance();
        try {
            $db->commit();

            $this->values($data);
            if (!$this->expiry) {
                $this->start_date = '';
                $this->end_date = '';
            }

            $this->save_with_moddate();

            // Keep track of what groups were previously saved. (Necessary when checking if any got removed.)
            $previous_groups = ORM::factory('SurveyHasGroup')
                ->where('survey_id', '=', $this->id)
                ->find_all_undeleted();
            $current_group_ids = [];


            // Should go above the `->save_with_moddate()` above. This extra `->save()` isn't needed.
            // Placed here to avoid a conflict.
            $this->url_title = IbHelpers::slugify($this->title);
            $this->save();
            // Save groups
            $groups = [];
            if (!empty($data['groups'])) {
                foreach ($data['groups'] as $key => $group_data) {
                    // Load group or create a new one
                    $group = new Model_Group($group_data['id']);
                    $group->set('title', $group_data['title']);
                    $group->set('type', $group_data['type']);
                    $group->save_with_moddate();
                    // Load survey-group relationship or create a new one
                    $has_group = ORM::factory('SurveyHasGroup')
                        ->where('survey_id', '=', $this->id)
                        ->where('group_id', '=', $group->id)
                        ->find_undeleted();
                    $has_group->set('survey_id', $this->id);
                    $has_group->set('group_id',  $group->id);
                    $has_group->set('order_id',  $group_data['order_id']);
                    $has_group->save_with_moddate();
                    // Need this for reference within the questions
                    if (!empty($group_data['id'])) {
                        $groups[$group_data['order_id']] = $group;
                    } else {
                        $groups[$key] = $group;
                    }

                    $current_group_ids[] = $group->id;
                }
            } else {
                // Load group linked to this survey or create a new one
                $has_group = ORM::factory('SurveyHasGroup')->where('survey_id', '=', $this->id)->find_undeleted();
                $default_group = new Model_Group($has_group->group_id);
                $default_group->save_with_moddate();

                $has_group->set('survey_id', $this->id);
                $has_group->set('group_id', $default_group->id);
                $has_group->save_with_moddate();
            }
            // Delete groups that were removed
            foreach ($previous_groups as $previous_group) {
                if (!in_array($previous_group->group_id, $current_group_ids)) {
                    $previous_group->delete_and_save();
                }
            }

            // Keep track of previous questions
            $previous_questions = ORM::factory('SurveyHasQuestion')
                ->where('survey_id', '=', $this->id)
                ->find_all_undeleted();
            $current_question_ids = [];
            if (!empty($data['questions'])) {
                foreach ($data['questions'] as $question_data) {
                    if (empty($question_data['title'])) {
                        continue;
                    }
                    // Load existing question or create a new one
                    $question = new Model_Question($question_data['id']);
                    $question->set('title', trim($question_data['title']));
                    $question->set('child_survey_id', $question_data['child_survey_id']);
                    $question->set('required', $question_data['required']);
                    $question->set('max_score', $question_data['max_score']);
                    $question->save_with_moddate();
                    $group_id = @$groups[$question_data['group_number']]
                        ? $groups[$question_data['group_number']]->id
                        : (isset($default_group) ? $default_group->id : null);
                   // Load existing survey-question pivot table relationship or create a new one.
                    $shq = ORM::factory('SurveyHasQuestion')
                        ->where('survey_id', '=', $this->id)
                        ->where('question_id', '=', $question->id)
                        ->find_undeleted();
                    $shq->set('question_id', $question->id);
                    $shq->set('group_id', $group_id);
                    $shq->set('survey_id', $this->id);
                    $shq->set('order_id', $question_data['order_id']);
                    $shq->save_with_moddate();
                    $answer = new Model_Answer($question->answer_id);
                    $answer->set('title', $question->title);
                    $answer->set('type_id', $question_data['type_id']);
                    $answer->save_with_moddate();

                    $question->set('answer_id', $answer->id);
                    $question->save();

                    $current_question_ids[] = $question->id;

                    // Answers that this question had before being saved
                    $previous_answer_options = $answer->options->find_all_undeleted();
                    $current_answer_option_ids = [];
                    if (!empty($question_data['answer_options'])) {
                        foreach ($question_data['answer_options'] as $answer_option_data) {
                            if (!empty($answer_option_data['label'])) {
                                if (($answer_option_data['label'] == 'Yes' || $answer_option_data['label'] == 'No') && $question_data['type_id'] != 6) {
                                    continue;
                                }
                                $score = $answer_option_data['score'] ? $answer_option_data['score'] : 0;
                                $answer_option = new Model_Answeroption($answer_option_data['id']);
                                $answer_option->set('answer_id', $answer->id);
                                $answer_option->set('order_id', $answer_option_data['order_id']);
                                $answer_option->set('label', $answer_option_data['label']);
                                // $answer_option->set('value', $answer_option_data['label']);
                                $answer_option->set('score', $score);
                                $answer_option->save_with_moddate();

                                $current_answer_option_ids[] = $answer_option->id;
                            }
                        }
                    }

                    // Delete answer_options that were removed
                    foreach ($previous_answer_options as $previous_answer_option) {
                        if (!in_array($previous_answer_option->id, $current_answer_option_ids)) {
                            $previous_answer_option->delete_and_save();
                        }
                    }

                }
            }

            if (!empty($data['prompts'])) {
                foreach($data['prompts'] as $prompt_data) {
                    if (empty($prompt_data['title'])) {
                        continue;
                    }
                    $prompt = new Model_Question($prompt_data['id']);
                    $prompt->set('title', trim($prompt_data['title']));
                    $prompt->set('child_survey_id', null);
                    $prompt->set('required', false);
                    $prompt->set('max_score', 0);
                    $prompt->set('type', 'prompt');
                    $prompt->save_with_moddate();
                    $group_id = @$groups[$prompt_data['group_number']]
                        ? $groups[$prompt_data['group_number']]->id
                        : (isset($default_group) ? $default_group->id : null);
                    $shq = ORM::factory('SurveyHasQuestion')
                        ->where('survey_id', '=', $this->id)
                        ->where('question_id', '=', $prompt->id)
                        ->find_undeleted();
                    $shq->set('question_id', $prompt->id);
                    $shq->set('group_id', $group_id);
                    $shq->set('survey_id', $this->id);
                    $shq->set('order_id', $prompt_data['order_id']);
                    $shq->save_with_moddate();
                    $current_question_ids[] = $prompt->id;
                }
            }

            // Delete questions that were removed
            foreach ($previous_questions as $previous_question) {
                if (!in_array($previous_question->id, $current_question_ids)) {
                    $previous_question->delete_and_save();
                }
            }

            $user = Auth::instance()->get_user();
            $activity = new Model_Activity();
            $activity
                ->set_item_type('survey')
                ->set_action(empty($data['id']) ? 'create' : 'update')
                ->set_item_id($this->id)
                ->set_user_id($user['id'])
                ->save();

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        return $this;
    }

    public function get_csv_data()
    {
        $responses = $this->responses->find_all();
        $has_questions = $this->has_questions();

        $data = [];

        // Column for each question
        //Each row is number of answers for current respondent -
        // columns headers are questions, rows are answers and response date, respondent name
        foreach ($responses as $response) {
            $name = $response->get_respondent_name();
            $date = $response->endtime
                ? date('Y-m-d', $response->endtime)
                : ($response->starttime ? date('Y-m-d', $response->starttime) : '');
            $questions = array();
            foreach ($has_questions as $has_question) {
                $choice   = $response->choices->where('question_id', '=', $has_question->question_id)->find();

                $question = $has_question->question->title;
                $answer   = $choice->display_answer();

                $questions[$question] = $answer;
            }
            $data[] = array_merge(array('Name' => $name), $questions, array('Date' => $date));
        }

        return $data;
    }

    /**
     * @param      $limit
     * @param      $offset
     * @param      $sort
     * @param      $dir
     * @param bool $search
     * @return array
     */
    public function get_all_surveys($limit, $offset, $sort, $dir, $search = false, $is_surveys2 = false)
    {
        $_limit = ($limit > -1) ? $offset . ',' . $limit : '';
        $items = DB::select('s.*',array(DB::expr("CONCAT(engine_users.name,' ',engine_users.surname)"),'user'))->from(array($this->_table_name,'s'))
            ->join('engine_users')
            ->on('s.updated_by','=','engine_users.id')
            ->where('s.deleted', '=', 0);
        $_search = '';

        // Enter search Criteria for the table
        if ($search)
        {
            $items->and_where('s.title', 'LIKE', '%' . $search . '%')->or_where('s.id', 'LIKE', '%' . $search . '%');
        }
        $dir = strtolower($dir);
        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'asc';
        }
        $items = $items->order_by('s.updated_on', 'desc')->order_by($sort, $dir);
        if ($limit > -1)
        {
            $items->limit($_limit);
        }
        $items = $items->execute()->as_array();

        // Build the table data
        $return = array();
        if ($items)
        {
            foreach ($items as $key => $item)
            {
                $link = $is_surveys2 ? '/admin/surveys/edit_questionnaire/'.$item['id'] : '/admin/surveys/add_edit_survey/' . $item['id'];

                $questions = ORM::factory('SurveyHasQuestion')->where('survey_id','=',$item['id'])->where('publish','=',1)->and_where('deleted' , '=', 0)->count_all();
                $completed = ORM::factory('SurveyResult')->where('survey_id','=',$item['id'])->count_all();

                $return[$key]['id'] = $item['id'];
                $return[$key]['title'] = htmlspecialchars($item['title']);
                $return[$key]['questions'] = $questions;
                $return[$key]['completed'] =  $completed;
                $return[$key]['start_date'] = $item['expiry'] == 1 && $item['start_date']
                    ? IbHelpers::formatted_time($item['start_date'], ['time' => false])
                    : 'n/a';
                $return[$key]['end_date'] = $item['expiry'] == 1 && $item['end_date']
                    ? IbHelpers::formatted_time($item['end_date'], ['time' => false])
                    : 'n/a';
                $return[$key]['created_on'] = IbHelpers::relative_time_with_tooltip($item['created_on']) . '</a>';
                $return[$key]['updated_on'] = IbHelpers::relative_time_with_tooltip($item['updated_on']) . '</a>';
                $return[$key]['user'] = $item['user'] . '</a>';

                // Actions
                $return[$key]['actions'] = '<a class="edit-link" href="' . $link .'">Edit</a>'
                    . '<a href="#" class="delete" data-id="' . $item['id'] . '">Delete</a>';
                // Publish
                if ($item['publish'] == '1')
                {
                    $return[$key]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $item['id'] . '"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$key]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $item['id'] . '"><i class="icon-ban-circle"></i></a>';
                }
            }
        }
        return $return;
    }

    public static function save_question_list($survey, $question, $order, $group_id)
    {
        $ql = DB::select()->from('plugin_survey_has_questions')->where('survey_id', '=', $survey)->and_where('question_id', '=', $question)->execute()->as_array();
        $user = Auth::instance()->get_user();
        if ($ql)
        {
            $update = array(
                'updated_on' => date("Y-m-d H:i:s"),
                'updated_by' => $user['id'],
                'order_id'   => $order,
                'group_id'   => $group_id,
                'publish'    => 1,
                'deleted'    => 0
            );
            foreach ($ql as $q)
            {
                DB::update('plugin_survey_has_questions')->set($update)->where('id', '=', $q['id'])->execute();
                $id = $q['id'];
            }
        }
        else
        {
            $insert = array(
                'updated_on'  => date("Y-m-d H:i:s"),
                'updated_by'  => $user['id'],
                'order_id'    => $order,
                'group_id'    => $group_id,
                'publish'     => 1,
                'deleted'     => 0,
                'survey_id'   => $survey,
                'question_id' => $question,
                'created_on'  => date("Y-m-d H:i:s"),
                'created_by'  => $user['id']
            );
            $answer = DB::insert('plugin_survey_has_questions', array_keys($insert))->values($insert)->execute();
            $id = $answer[0];
        }
        return $id;
    }

    public static function  save_survey_group_list($survey, $group, $order)
    {
        $ql = DB::select()->from('plugin_survey_has_groups')->where('survey_id', '=', $survey)->and_where('group_id', '=', $group)->execute()->as_array();
        $user = Auth::instance()->get_user();
        if ($ql)
        {
            $update = array(
                'updated_on' => date("Y-m-d H:i:s"),
                'updated_by' => $user['id'],
                'order_id'   => $order,
                'publish'    => 1,
                'deleted'    => 0
            );
            foreach ($ql as $q)
            {
                DB::update('plugin_survey_has_groups')->set($update)->where('id', '=', $q['id'])->execute();
                $id = $q['id'];
            }
        }
        else
        {
            $insert = array(
                'updated_on'  => date("Y-m-d H:i:s"),
                'updated_by'  => $user['id'],
                'order_id'    => $order,
                'group_id'    => $group,
                'publish'     => 1,
                'deleted'     => 0,
                'survey_id'   => $survey,
                'created_on'  => date("Y-m-d H:i:s"),
                'created_by'  => $user['id']
            );
            $answer = DB::insert('plugin_survey_has_groups', array_keys($insert))->values($insert)->execute();
            $id = $answer[0];
        }
        return $id;
    }

    /**
     * Delete a survey, its groups, questions and answers
     *
     * @return ORM
     * @throws Exception
     * @throws Kohana_Exception
     */
    public function expunge_survey()
    {
        $db = Database::instance();
        $db->commit();

        try {
            $has_questions = ORM::factory('SurveyHasQuestion')->where('survey_id', '=', $this->id)->find_all();

            foreach ($has_questions as $has_q) {
                $has_q->group->set_deleted();
                $has_q->question->set_deleted();
                $has_q->question->answer->set_deleted();
                $has_q->set_deleted();
            }

            $has_groups = ORM::factory('SurveyHasGroup')->where('survey_id', '=', $this->id)->find_all();
            foreach ($has_groups as $has_g) {
                $has_g->set_deleted();
            }

            return $this->set_deleted();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function delete_from_question_list($id)
    {
        $user = Auth::instance()->get_user();
        $update = array(
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $user['id'],
            'order_id'   => NULL,
            'publish'    => 0,
            'deleted'    => 1
        );
        DB::update('plugin_survey_has_questions')->set($update)->where('id', '=', $id)->execute();
        return TRUE;
    }

    public static function delete_from_group_list($id)
    {
        $check = DB::select()->from('plugin_survey_has_groups')->where('survey_id', '=', $id)->execute()->as_array();
        if ($check) {
            $user = Auth::instance()->get_user();
            $update = array(
                'updated_on' => date("Y-m-d H:i:s"),
                'updated_by' => $user['id'],
                'order_id'   => null,
                'publish'    => 0,
                'deleted'    => 1
            );
            DB::update('plugin_survey_has_groups')->set($update)->where('survey_id', '=', $id)->execute();
        }
        return TRUE;
    }
    public static function get_question_with_group()
    {
		$res= DB::select('question.*',array('grp.id','grpid'),array('answer.title','anstitle'))
                ->from(array('plugin_survey_questions','question'))
                ->join(array('plugin_survey_answers','answer'),'left')
                ->on('question.answer_id','=','answer.id')
                ->join(array('plugin_survey_groups','grp'),'left')
                ->on('answer.group_name','=','grp.title')
                ->where('question.publish','=',1)
                ->where('question.deleted','=',0)
                ->execute()
                ->as_array();
        return $res;
                
    }	

    public static function get_questionnaire_preview($questions)
    {
        $result = array();
        foreach ($questions as $key => $question)
        {
            $q = ORM::factory('Question', $question->question_id);
            $answer = ORM::factory('Answer', $q->answer_id);
            $group = ORM::factory('Group', $question->group_id);
            $answer_option = ORM::factory('AnswerOption')->where('answer_id', '=', $answer->id)->where('publish', '=', 1)->and_where('deleted', '=', 0)->find_all();
            $result[$key] = array(
                'question' => $q->title,
                'question_id' => $question->question_id,
                'group_id'  => $question->group_id,
                'group_title' => $group->title,
                'stub'     => ORM::factory('Answertype', $answer->type_id)->stub,
                'answers'  => array()
            );
            if (count($answer_option)>0)
            {
                foreach ($answer_option as $option)
                {
                    $result[$key]['answers'][] = array('label' => $option->label, 'value' => $option->value);
                }
            }
            else
            {
                $result[$key]['answers'][] = array('label' => '', 'value' => '');
            }
        }

        return $result;
    }

    public static function get_survey_next_question($survey,$sequence=NULL,$question=NULL,$option_id=NULL,$order_id=NULL)
    {
        $result = array(
            'status'        => 'success',
            'message'       => '',
            'question'      => '',
            'answers'       => array(),
            'sequence_id'   => $sequence,
            'order_id'      => ''
        );
        // No Survey selected
        if (is_null($survey))
        {
            $result['message']  = 'No Survey Selected';
            $result['status']   = 'error';
        }
        // Get the first Question
        else if (is_null($question))
        {
            $questions = DB::select('has.question_id','has.order_id',array('seq.id','sequence_id'),'q.title')
                ->from(array('plugin_survey_has_questions','has'))
                ->join(array('plugin_survey_questions','q'))
                ->on('has.question_id','=','q.id')
                ->join(array('plugin_survey_sequence','seq'),'LEFT')
                ->on('has.survey_id','=','seq.survey_id')
                ->where('has.publish','=',1)
                ->where('has.deleted','=',0)
                ->order_by('has.order_id')
                ->execute()
                ->as_array();
            $test = array_filter($questions);
            if ( ! empty($test))
            {
                $result['question']     = array(
                    'question_id'   => $questions[0]['question_id'],
                    'question'      => $questions[0]['title']
                );
                $result['order_id']     = $questions[0]['order_id'];
                $result['sequence_id']  = $questions[0]['sequence_id'];
                $answers = ORM::factory('AnswerOption')->where('answer_id','=',$questions[0]['question_id'])->find_all();
                foreach ( $answers as $ans )
                {
                    $result['answer'][]=array(
                        'label'     => $ans->label,
                        'value'     => $ans->value,
                        'option_in' => $ans->id,
                        'target_id' => ORM::factory('plugin_survey_sequence_items')
                            ->where('sequence_id','=',$result['sequence_id'])
                            ->where('answer_option_id','=',$ans->id)
                            ->target_id
                    );
                }
            }
            else
            {
                $result['message']  = 'No Survey Selected';
                $result['status']   = 'error';
            }
        }
        // get the next question
        else
        {
            // Get next Question in Sequence
            if (! is_null($sequence) AND ! is_null($option_id))
            {
                $question_id = ORM::factory('SequenceItems')
                    ->where('survey_id','=',$survey)
                    ->where('sequence_id','=',$sequence)
                    ->where('answer_option_id','=',$option_id)
                    ->target_id;
                // @todo Get Question
            }
            // Get next Question in order
            else
            {
                $question_id = ORM::factory('SurveyHasQuestion')
                    ->where('survey_id','=',$survey)
                    ->where('order_id','=',($order_id+1))
                    ->where('publish','=',1)
                    ->question_id;
                // @todo get question
            }

            // End of Survey
            if (is_null($question_id) OR $question_id == 0)
            {
                $result['message']  = 'End of Survey';
                $result['status']   = 'end';
            }

        }
        return $result;
    }

    /**
     * Render a survey that has been loaded as an object
     */
    public function render()
    {
        return self::render_survey($this->id);
    }

    // For the short tag
    public static function render_survey($input)
    {
        if (!is_numeric($input)) {
            $survey = ORM::factory('Survey')->where('url_title', '=', $input)->find_published();
            if (!$survey->id) {
                $survey = ORM::factory('Survey')->where('title', '=', $input)->find_published();
            }
        }
        else {
            $survey = ORM::factory('Survey')->where('id', '=', $input)->find_published();
        }
        $id = $survey->id;

        $responses = Session::instance()->get('survey_' . $id);
        if ($id && !$responses){
            $res_id = Model_SurveyResult::start($id);
            Session::instance()->set('survey_' . $id, array());
            Session::instance()->set('survey_result', $res_id);
        } else if(empty(Session::instance()->get('survey_result'))) {
            $res_id = Model_SurveyResult::start($id);
            Session::instance()->set('survey_result', $res_id);
        }
        


        if (!$survey->id) {
            return '<span class="inline-error">Survey <code>'.$input.'</code> does not exist.</span>';
        }

        // Survey use Expiry date
        if ($survey->expiry == 1 )
        {
            // The survey is finished
            if ($survey->end_date != '' AND date("Y-m-d H:i:s") > $survey->end_date)
            {
                return '<span class="inline-error">Survey <code>'.$id.'</code> is not accessible at this present time.<br/>It finished on '.(date('l dS F',strtotime($survey->end_date))).'</span>';
            }
            // The survey hasn't started yet
            if (date("Y-m-d H:i:s") < $survey->start_date)
            {
                return '<span class="inline-error">Survey <code>'.$id.'</code> is not accessible at this present time.<br/>It will start on '.(date('l dS F',strtotime($survey->start_date))).'</span>';
            }
        }

		// Get question data. If no question ID is specified, get the first one
        $question_id = isset($_POST['question_id']) ? $_POST['question_id'] : '';
        $previous_question_id = isset($_POST['previous_question_id']) ? $_POST['previous_question_id'] : '';
        $previous_group_id = isset($_POST['previous_group_id']) ? $_POST['previous_group_id'] : '';
        $has_question = $survey->has_questions->order_by('group_id')->order_by('order_id');
        if ($question_id) {
            $has_question->where('question_id', '=', $question_id);
        }
        $has_question = $has_question->find_published();
        $question = $has_question->question;
        $supplied_answers = Session::instance()->get('survey_'.$id);

        // Find the number of the first question on the page to display
        $has_questions = $survey->has_questions->order_by('group_id')->order_by('order_id')->find_all_published()->as_array();
        $question_found = false;
        for ($question_number = 0; $question_number < count($has_questions) && !$question_found; $question_number++) {
            $question_found = ($has_questions[$question_number]->question_id == $question_id) || $has_questions[$question_number]->group_id == $has_question->group_id;
        }

        $question_number = $question_found ? $question_number : 1;

        $group = null;
        if ($survey->pagination == 1) {
            $group = $has_question->group_id;
            $questions_group = $survey->has_questions->where('publish', '=', 1)->where('deleted', '=', 0)->where('group_id','=',$group)->order_by('group_id')->order_by('order_id')->find_all();
            $questions = array();
            foreach($questions_group as $g)
            {
                $questions[] = $g->question;
            }
        }
        else {
            $questions = array($question);
        }

        return View::factory('frontend/survey')
            ->set('survey', $survey)
            ->set('group', $group)
            ->set('has_question', $has_question)
            ->set('questions', $questions)
            ->set('question_number', $question_number)
            ->set('previous_question_id', $previous_question_id)
            ->set('previous_group_id', $previous_group_id)
            ->set('responses', $responses);
    }

    public static function get_survey_related_to_course($course_id) {
        $survey = DB::select('id',
            'title')->from('plugin_survey')->where('plugin_survey.course_id',
                '=', $course_id)->execute()->current();
        return $survey;
    }
    
    public static function get_surveys_related_to_booking($booking_id) {
        $surveys = DB::select('survey_info.endtime', 'survey_info.survey_author')->from(array('plugin_ib_educate_bookings_has_surveys', 'bookings_has_survey'))
            ->join(array( 'plugin_survey_result', 'survey_info'),'inner')
            ->on('bookings_has_survey.survey_result_id', '=', 'survey_info.id')->where('bookings_has_survey.booking_id', '=', $booking_id)
            ->execute()->as_array();
        return $surveys;
    }
    
    public static function get_answers_from_survey_result($survey_result_id) {
//        $surveys = DB::select('survey_result_id')->from('plugin_survey_answer_result')->where('booking_id',
//            '=', $booking_id)
//            ->execute()->as_array();
    }
    public static function get_answer_responses($survey_id, $question_id, $survey_result_id = FALSE)
    {
        $return      = array();
        $question    = new Model_Question($question_id);
        $answer_type = $question->answer->type->stub;

        if (in_array($answer_type, array('checkbox', 'radio', 'select')))
        {
            // Get all the possible answers for the question
            $answer_options = DB::select(
                'answer_option.id',
                array('question.id',         'question_id'),
                array('question.title',      'question'),
                array('answer.id',           'answer_id'),
                array('answer_option.label', 'answer')
            )
                ->from(array('plugin_survey_answer_options', 'answer_option'))
                ->join(array('plugin_survey_answers',        'answer'  ))->on('answer_option.answer_id', '=', 'answer.id')
                ->join(array('plugin_survey_questions',      'question'))->on('question.answer_id',      '=', 'answer.id')
                ->where('question.id', '=', $question_id)
                ->where('answer_option.deleted', '=', 0)
                ->execute()
                ->as_array();

            // Get all responses
            $results = DB::select(
                'answer_result.question_id',
                array('question.title', 'question'),
                'answer_result.answer_id',
                array('answer.label', 'answer'),
                array('answer.id', 'answer_option_id'),
                array(DB::expr("count(`answer_result`.`answer_id`)"), 'count')
            )
                ->from(array('plugin_survey_answer_result',  'answer_result'))
                ->join(array('plugin_survey_result',         'survey_result'))->on('answer_result.survey_result_id', '=', 'survey_result.id')
                ->join(array('plugin_survey_questions',      'question'     ))->on('answer_result.question_id',      '=', 'question.id')
                ->join(array('plugin_survey_answer_options', 'answer'       ))->on('answer_result.answer_id',        '=', 'answer.id')
                ->where('survey_result.survey_id',   '=', $survey_id)
                ->where('answer_result.question_id', '=', $question_id);
                if($survey_result_id !== FALSE) {
                    $results = $results->where('survey_result.survey_result_id', '=', $survey_result_id);
                }
                $results = $results->group_by('answer_result.answer_id')
                    ->order_by('answer_result.question_id')
                    ->execute()
                    ->as_array();
            // Not all possible answer options will necessarily have been picked at least once
            // This is needed to set a count of "0" to answer options that were not picked
            foreach ($answer_options as $key => $answer_option) {
                $answer_options[$key]['count'] = 0;
                foreach ($results as $result) {
                   if ($result['answer_option_id'] == $answer_option['id']) {
                       $answer_options[$key]['count']  = $result['count'];
                   }
                }
            }

            $return = $answer_options;
        }
        else if (in_array($answer_type, array('input', 'textarea')))
        {
            // Get all responses
            $return = DB::select(
                'answer_result.question_id',
                array('question.title', 'question'),
                array('answer_result.textbox_value', 'answer'),
                array(DB::expr("1"), 'count')
            )
                ->from(array('plugin_survey_answer_result',  'answer_result'))
                ->join(array('plugin_survey_result',         'survey_result'))->on('answer_result.survey_result_id', '=', 'survey_result.id')
                ->join(array('plugin_survey_questions',      'question'     ))->on('answer_result.question_id',      '=', 'question.id')
                ->where('survey_result.survey_id',   '=', $survey_id)
                ->where('answer_result.question_id', '=', $question_id);
                if ($survey_result_id !== false) {
                    $return = $return->where('answer_result.survey_result_id', '=', $survey_result_id);
                }
                $return = $return->order_by('answer_result.question_id')
                ->execute()
                ->as_array();

        }

        return $return;
    }
    
    public static function get_questions_from_survey($survey_id)
    {
        $surveys = DB::select('sq.id', 'sq.title', array('sa.title', 'answer_title'), 'st.stub', 'sq.answer_id')->from(array('plugin_survey_has_questions', 'shq'))
            ->join(array('plugin_survey_questions', 'sq'),'inner')->on('shq.question_id', '=', 'sq.id')
            ->join(array('plugin_survey_answers', 'sa'), 'inner')->on('sq.answer_id', '=', 'sa.id')
            ->join(array('plugin_survey_answer_types', 'st'), 'inner')->on('sa.type_id', '=', 'st.id')
            ->where('shq.survey_id', '=', $survey_id)->order_by('sq.id', 'asc')
            ->execute()->as_array();
        return $surveys;

    }
}
