<?php
class Model_AnswerResult extends ORM{
    protected $_table_name = 'plugin_survey_answer_result';

    protected $_has_many = [
        'survey_result'  => ['model' => 'SurveyResult'],
    ];

    protected $_belongs_to = [
        'question'       => ['model' => 'Question',     'foreign_key' => 'question_id'],
        'answer_option'  => ['model' => 'AnswerOption', 'foreign_key' => 'answer_id'],
        'todo'           => ['model' => 'Todo_Item',    'foreign_key' => 'todo_id'],
    ];

    public function display_answer()
    {
        return $this->textbox_value ? $this->textbox_value : $this->answer_option->label;
    }

    public static function insert_answer($survey_result_id, $question_id, $answer_id, $question_time, $textbox_value = null){
        $insert = array(
            'survey_result_id' => $survey_result_id,
            'question_id' => $question_id,
            'question_time' => $question_time,
        );
        if ($textbox_value != null && is_string($textbox_value)) {
            $insert['textbox_value'] = $textbox_value;
        }

        if (is_numeric($answer_id)) {
            $insert['answer_id'] = $answer_id;
        }

        if (is_array($answer_id)) {
            foreach ($answer_id as $answer_id_checkbox) {
                $insert['answer_id'] = $answer_id_checkbox;
                $inserted = DB::insert('plugin_survey_answer_result', array_keys($insert))->values($insert)->execute();
            }
        } else {
            $inserted = DB::insert('plugin_survey_answer_result', array_keys($insert))->values($insert)->execute();
        }
        return $inserted[0];
    }
}