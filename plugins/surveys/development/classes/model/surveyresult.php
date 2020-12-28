<?php
class Model_SurveyResult extends ORM{
    const RESULT_TABLE = 'plugin_survey_result';
    protected $_table_name = 'plugin_survey_result';

    protected $_has_many = [
        'choices' => ['model' => 'AnswerResult', 'foreign_key' => 'survey_result_id'],
    ];

    protected $_belongs_to = [
        'survey' => ['model' => 'Survey', 'foreign_key' => 'survey_id'],
        'author' => ['model' => 'User',   'foreign_key' => 'survey_author'],
    ];

    /**
     * Count the number of answered questions.
     *
     * Returns `0`, if there are no questions
     * Returns `answered / total`, e.g. "3 / 5", if there are questions.
     */
    public function get_completion()
    {
        $questions = $this->survey->has_questions->find_all_undeleted()->as_array('question_id');
        if (empty ($questions)) {
            // If there are no questions, display 0. No need to lookup answers to the questions.
            return 0;
        } else {
            // If there are questions count the answers.
            $responses = $this->choices->where('question_id', 'IN', array_keys($questions))->find_all();
            $answered = 0;
            foreach ($responses as $response) {
                // Treat questions with blank answers as unanswered..
               if ($response->display_answer() !== '' && $response->display_answer() !== null) {
                   $answered++;
               }
            }

            return $answered . ' / ' . count($questions);
        }
    }

    public function get_score()
    {
        $score = 0;
        foreach ($this->choices->find_all() as $choice) {
            $score += $choice->answer_option->score;
        }
        return $score;
    }

    // Render a table, companing the submitted answers to the correct answers.
    public function render_score()
    {
        $has_questions = $this->survey->has_questions();

        $results = [];
        foreach ($has_questions as $has_question) {
            $submitted_answer = $this->choices->where('question_id', '=', $has_question->question_id)->find();
            $right_answer = $has_question->question->answer->get_right_answer();

            $results[] = [
                'question'         => $has_question->question->title,
                'right_answer'     => $right_answer->label,
                'submitted_answer' => $submitted_answer->display_answer(),
                'correct'          => ($right_answer->id && $submitted_answer->answer_id == $right_answer->id),
            ];
        }

        return View::factory('frontend/survey_score')->set(compact('results'))->render();
    }

    /* Get the name of the respondent.
     * If the respondent was not logged in, say "Anonymous", followed by the response number
     */
    public function get_respondent_name()
    {
        $name = trim($this->author->get_full_name());

        return $name ? $name : 'Anomymous '.$this->id;
    }

    public static function start($survey_id)
    {
        $user = Auth::instance()->get_user();

        $insert = [
            'survey_id'     => $survey_id,
            'starttime'     => time() ,
            'user_ip'       => $_SERVER['REMOTE_ADDR'],
            'survey_author' => isset($user['id']) ? $user['id'] : null,
        ];
        $inserted = DB::insert('plugin_survey_result', array_keys($insert))->values($insert)->execute();
        return $inserted[0];
    }

    public static function end_survey($survey_result_id, $survey_id, $survey_author = FALSE){
        if($survey_author === false || !is_numeric($survey_author)) {
            $survey_author = '0';
        }
        DB::update('plugin_survey_result')
            ->set(array(
                'endtime' => time(), 'survey_author' => $survey_author
                )
            )
            ->where('id', '=', $survey_result_id)
            ->execute();
        Session::instance()->delete('survey_result');
        Session::instance()->delete('survey_'.$survey_id);
    }
    
    public static function link_survey_to_booking($survey_result_id, $booking_id) {
        $insert = array(
            'booking_id' => $booking_id,
            'survey_result_id' => $survey_result_id
        );
        $inserted = DB::insert('plugin_ib_educate_bookings_has_surveys', array_keys($insert))->values($insert)->execute();
        return $inserted[0];
    }
}