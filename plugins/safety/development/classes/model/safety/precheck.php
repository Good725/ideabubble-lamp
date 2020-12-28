<?php defined('SYSPATH') or die('No direct script access.');

class Model_Safety_Precheck extends ORM
{
    protected $_table_name = 'plugin_safety_prechecks';

    protected $_belongs_to = [
        'assignee'  => ['model' => 'Contacts3_Contact', 'foreign_key' => 'assignee_id'],
        'survey'    => ['model' => 'Survey',            'foreign_key' => 'survey_id'],
    ];

    protected $_has_many = [
        'responses' => ['model' => 'SurveyResult', 'through' => 'plugin_safety_precheck_has_survey_results', 'foreign_key' => 'precheck_id', 'far_key' => 'survey_result_id'],
    ];

    public function get_responses_array()
    {
        $responses = $this->responses->find_all();
        $return = [];

        foreach ($responses as $response) {
            $response_answers = [];

            foreach ($response->choices->find_all()->as_array() as $result_answer) {
                $response_answers[$result_answer->question->id]['value'] = $result_answer->textbox_value
                    ? $result_answer->textbox_value
                    : $result_answer->answer_id;

                $response_answers[$result_answer->question->id]['todo'] = [
                    'id' => $result_answer->todo->id,
                    'summary' => $result_answer->todo->summary,
                    'status' => $result_answer->todo->status,
                    'assignee_id' => $result_answer->todo->assignees->find_undeleted()->id
                ];
            }

            $return[$response->survey_id][$response->stock_id ?? 0] = [
                'result_id'   => $response->id,
                'stock_id'    => $response->stock_id ?? null,
                'course_id'   => $response->course_id,
                'schedule_id' => $response->schedule_id,
                'responses'   => $response_answers,
            ];
        }

        return $return;
    }

    public function save_data($data)
    {
        $db = Database::instance();
        try {
            $db->begin();

            $this->values($data);

            if (!empty($data['surveys'])) {
                $this->survey_id = array_keys($data['surveys'])[0];
            }

            $this->save_with_moddate();
            $user = Auth::instance()->get_user();

            DB::delete('plugin_safety_precheck_has_survey_results')->where('precheck_id', '=', $this->id)->execute();

            foreach ($data['surveys'] as $survey_id => $survey) {

                $stock_ids = array_keys($survey);

                foreach ($stock_ids as $stock_id) {
                    $survey_result = ORM::factory('SurveyResult', $survey['result_id']);
                    $survey_result->survey_id = $survey_id;
                    $survey_result->survey_author = $user['id'];
                    $survey_result->stock_id = $stock_id;

                    if (!empty($survey['course_id'])) {
                        $survey_result->course_id = $survey['course_id'];
                    }

                    if (empty($survey_result->starttime)) {
                        $survey_result->starttime = time();
                    }

                    if (empty($survey_result->endtime)) {
                        $survey_result->endtime = time();
                    }

                    if (!empty($survey['schedule_id'])) {
                        $survey_result->schedule_id = $survey['schedule_id'];
                    }

                    $survey_result->save();

                    $survey_object = new Model_Survey($survey_id);
                    if (!$survey_object->has_children()) {
                        $this->survey_id = $survey_id;
                    }

                    DB::insert('plugin_safety_precheck_has_survey_results', ['precheck_id', 'survey_result_id'])
                        ->values(['precheck_id' => $this->id, 'survey_result_id' => $survey_result->id])
                        ->execute();

                    DB::delete('plugin_survey_answer_result')->where('survey_result_id', '=', $survey['result_id'])->execute();

                    foreach ($survey[$stock_id ? $stock_id : 0]['responses'] as $question_id => $response) {

                        if (!empty($response['todo']) && !empty($response['todo']['summary'])) {
                            $question = new Model_Question($question_id);

                            $todo = new Model_Todo_Item($response['todo']['todo_id']);
                            $todo->set('title', $question->title);
                            $todo->set('summary', $response['todo']['summary']);
                            $todo->set('status', $response['todo']['status']);
                            $todo->save_with_moddate();
                            Model_Todos::save_assigned_students($todo->id, [$response['todo']['assignee_id']]);
                        }

                        DB::insert('plugin_survey_answer_result', ['question_id', 'survey_result_id', 'answer_id', 'textbox_value', 'todo_id'])
                            ->values([
                                'question_id'      => $question_id,
                                'survey_result_id' => $survey_result->id,
                                'answer_id'        => is_numeric($response) ? (int) $response : null,
                                'textbox_value'    => $response['value'],
                                'todo_id'          => isset($todo) ? $todo->id : null,
                            ])
                            ->execute();
                    }
                }
            }

            $db->commit();
        }
        catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function get_reports($filters = [])
    {
        $results = $this->apply_filters($filters);
        return [
            ['text' => 'Total',     'amount' => $results->count_all()],
            ['text' => 'Passed',    'amount' => 0],
            ['text' => 'Failed',    'amount' => 0],
            ['text' => 'Pending',   'amount' => 0],
            ['text' => 'Corrected', 'amount' => 0],
        ];
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'safety_precheck.id',
            ['assignee.id', 'staff_id'],
            [DB::expr("CONCAT(`assignee`.`first_name`, ' ', `assignee`.`last_name`)"), 'Staff'],
            null, // Stock ID
            null, // Stock title
            'survey.title', // Pre-check
            'safety_precheck.date_created',
            'safety_precheck.date_modified',
            null // actions
        ];

        $results = $this
            ->apply_filters($filters)
            ->apply_datatable_args($datatable_args, $column_definitions);

        $results->order_by('safety_precheck.date_modified', 'desc');
        $q = clone $results;
        $results = $results->find_all();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $options = [
                ['type' => 'link', 'title' => 'Edit', 'attributes' => ['class' => 'safety-precheck-edit edit-link', 'data-id' => $result->id, 'data-survey_id' => $result->survey_id]]
            ];

            $row[] = $result->id;
            $row[] = $result->assignee_id;
            $row[] = htmlentities($result->assignee->get_full_name());
            $row[] = null;
            $row[] = null;
            $row[] = htmlentities($result->survey->title);
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_created);
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', $options)->render();

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $q->count_all(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public function apply_filters($filters = [])
    {
        $results = $this
            ->with('assignee')
            ->with('survey')
            ->where_undeleted();

        if (!empty($filters['staff_ids'])) {
            $results->where('safety_precheck.assignee_id', 'in', $filters['staff_ids']);
        }

        if (!empty($filters['precheck_ids'])) {
            $results->where('safety_precheck.survey_id', 'in', $filters['precheck_ids']);
        }

        if (!empty($filters['start_date'])) {
            $results->where('safety_precheck.date_modified', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $results->where('safety_precheck.date_modified', '<=', $filters['end_date'].' 23:59:59');
        }

        return $results;
    }

}