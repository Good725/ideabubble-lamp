<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Frontend_Surveys extends Controller
{
    public function action_render()
    {
        $id = urldecode($this->request->param('item_category'));
        $survey = new Model_Survey($id);
        $content = $survey->render_survey($id);

        $page = new Model_Page();
        $page->title = $survey->title ? $survey->title : $id;
        $page_data = $page->as_array();
        echo  View::factory('front_end/page', compact('content', 'page_data', 'theme'));
        echo '<script src="' . URL::get_engine_plugin_asset('surveys', 'js/survey.js', ['cachebust' => true]) .'"></script>';

    }

    // Get the ID of the next question
    // Done by checking the sequence item corresponding to the current question and answer
    public function action_ajax_get_next_question()
    {
        $this->auto_render = FALSE;
        $return = array();

        $post = $this->request->post();
        $survey_id = $post['survey_id'];
        $return['survey_id'] = $survey_id;
        $answers = isset($post['answers']) ? $post['answers'] : [];

        // Get the Survey
        $survey = ORM::factory('Survey')->where('id', '=', $survey_id)->find_published();
        $return['survey_backend'] = $survey->is_backend;
        // Get the sequence item, corresponding to the question and answer
        $sequence = $survey->sequences->where('publish', '=', 1)->where('deleted', '=', 0)->find();

        // Add the answer to the current question to a session variable, storing the user's answers
        $survey_data = Session::instance()->get('survey_' . $survey_id);
        $survey_data = ($survey_data == '') ? array() : $survey_data;
        $answer_count = count($answers);
        $last_question_id = null;
        foreach ($answers as $answer) {
            $question_id = $answer['question_id'];
            $answer_id = $answer['answer_id'];
            $question_time = round($answer['question_time'] / $answer_count, 1);
            $group_id = $answer['group_id'];

            $question = ORM::factory('Question', $question_id);

            // Get the group
			$group = ORM::factory('SurveyHasQuestion')->where('survey_id', '=', $survey_id)->where('question_id', '=', $question_id)->find();

            $survey_data[$question_id] = $answer_id;
            $last_question_id = $question_id;
        }
        Session::instance()->set('survey_' . $survey_id, $survey_data);

        $survey_result_id = Session::instance()->get('survey_result');

        // Get the next group of questions
        if ($survey->pagination && isset($group) AND ! empty($group->group_id))
        {
            $next_group = $survey->get_next_group($group->group_id);

            $return['group'] = $next_group->id;
            if ($next_group->id == $group_id || $next_group->id == '')
            {
				// If the survey generates a document, the survey doesn't end until after the generation
				if (! $survey->result_template_id)
				{
				    $survey_author_id = ($survey->is_backend) ? Auth::instance()->get_user()['id'] : false;
					Model_SurveyResult::end_survey($survey_result_id, $survey_id, $survey_author_id);
				}
				if($survey->is_backend && isset($survey->course_id)) {
                    Model_SurveyResult::link_survey_to_booking($survey_result_id, $post['booking_id']);
                   // Save Survey as a document to the student's folder
                    $booking = new Model_KES_Bookings($post['booking_id']);
                    $contact_id = $booking->get_contact_details_id();
                    Controller_Frontend_Surveys::save_survey_to_student_folder($survey_id, $survey_data, $contact_id ?? '0');
                }
                $return['question_id'] = 0;
            }
            else
            {
                $questions_group = $survey->has_questions->where('publish', '=', 1)->where('deleted', '=', 0)->where('group_id','=',$return['group'])->order_by('group_id')->order_by('order_id')->find_all();
                $questions = array();
                foreach($questions_group as $k=>$g)
                {
                    $questions[] = $g->question;
                    if ($k == 0)
                    {
                        $return['question_id'] = $g->question->id;
                    }
                }
                $return['questions'] = $questions;
            }
        }
        // Get the next question in sequence
        else {
            $survey = new Model_Survey($survey_id);

            $return['group'] = '';
            $question_id = isset($question_id) ? $question_id : null;
            $answer_id = isset($answer_id) ? $answer_id : null;
            $next_question = $survey->get_next_question($question_id, $answer_id);

            $return['question_id'] = $next_question->id ? $next_question->id : 0;
            $return['questions'] = [$return['question_id']];

            // End the survey, if at the last question, unless the survey needs to generate a document
            if ($return['question_id'] == 0 && ! $survey->result_template_id) {
                Model_SurveyResult::end_survey($survey_result_id, $survey_id);
            }
        }
        if ($survey->store_answer == 1) {
            foreach ($answers as $answer) {
                $question_id = $answer['question_id'];
                $answer_id = $answer['answer_id'];

                $textbox_value = is_numeric($answer_id) ? null : $answer_id;
                Model_AnswerResult::insert_answer($survey_result_id, $question_id, $answer_id, $question_time,
                    $textbox_value);
            }
        }

        $return['result_id'] = $survey_result_id;

        echo json_encode($return);
    }

    // Get the ID of the previous question
    // Done by looking back through the answers the user has supplied
    public function action_ajax_get_prev_question()
    {
        $this->auto_render = FALSE;
        $return = array();

        $survey_id = $this->request->post('survey_id');
        $return['survey_id'] = $survey_id;
        $question_id = $this->request->post('question_id');

        // Get the answers the user has supplied so far
        $survey_data = Session::instance()->get('survey_'.$survey_id);

        // Loop through the answers the user has supplied
        $previous_question_id = NULL;
        $found = FALSE;
        foreach ($survey_data as $q_id => $a_id) // submitted question ID => answer ID
        {
            // If this ID on this loop iteration is the ID of the question the user is navigating back from,
            // the ID of the previous iteration is the ID of the previous question.
            $found = (!$found AND $q_id == $question_id);
            if (!$found)
            {
                $previous_question_id = $q_id;
            }
            else
            {
                // When the user clicks "back", unset all the "future" answers they have given,
                // as the user might take a path that doesn't ask those questions
                unset($survey_data[$q_id]);
            }
        }
        // Store the updated answer data in the session variable
        Session::instance()->set('survey_'.$survey_id, $survey_data);

        // return the ID of the previous question
        $survey = new Model_Survey($survey_id);
        $return['survey_backend'] = $survey->is_backend;
        $return['question_id'] = $previous_question_id;
        echo json_encode($return);
    }

    public function action_get_survey_download()
    {
        $survey = new Model_Survey($this->request->post('survey_id'));
        $result = new Model_SurveyResult($this->request->post('result_id'));

        $return['download']   = $survey->result_pdf_download;
        $return['thanks']     = $survey->display_thank_you;
        $return['show_score'] = $survey->show_score;
        $return['score_html'] = $survey->show_score ? $result->render_score() : '';
        $return['page_name']  = Model_Pages::get_page_by_id($survey->thank_you_page_id);

        echo json_encode($return);
    }

    // Complete the survey
    public function action_finish_survey()
    {
        try
        {
            $this->auto_render = FALSE;
            $survey_id = $this->request->param('id');
            $submitted_answers = Session::instance()->get('survey_'.$survey_id);
            $template_data = array();

            // Loop through the user's answers, get the question and answer text to be displayed on the document
            $i = 1;
            $content = '';
            $comments = '';
            foreach ($submitted_answers as $question_id => $answer)
            {
                $question = ORM::factory('Question', $question_id);
                $type = $question->answer->type->stub;

                // If the user had to select an option, get the text for that option
                if ($type == 'radio')
                {
                    $answer = ORM::factory('AnswerOption', $answer)->label;
                }

                // Variables to be used in the document generator
                /*
                $template_data['question_'.($i + 1)] = $question->title;
                $template_data['answer_'.($i + 1)] = $answer;
                */

                if (strtolower(trim($question->title)) == 'comments')
                {
                    $comments = $answer;
                }
                else
                {
                    // $content .= $question->title."\n    ".$answer."\n";
                    $content .= '<p><strong>'.$question->title.'</strong><br />&nbsp;&nbsp;&nbsp;&nbsp;'.$answer.'</p>';
                }
                $template_data['content'] = array('html' => $content, 'type' => 'block');
                $template_data['comments'] = array('html' => '<p>'.nl2br($comments).'</p>', 'type' => 'block');

                $i++;
            }

            // Generate the document
            $survey = ORM::factory('Survey', $survey_id);
            $template = $survey->template;
            $doc = new Model_Document;
            $timestamp = date('YmdHis');

            // This function takes the document meta data, generates the document and stores it. It also returns a status message.
            $doc->doc_gen_and_storage($template->id, $template_data, $template->name.'_', $timestamp, '', '', TRUE, TRUE);

			// End the survey
			$survey_result_id = Session::instance()->get('survey_result');
			Model_SurveyResult::end_survey($survey_result_id, $survey_id);

            // Delete the session variable, storing the user's answers
            Session::instance()->delete('survey_'.$survey_id);

            header('Content-disposition: attachment; filename="'.$template->name.'_'.$timestamp.'.pdf'.'"');
            header('Content-type: .pdf');
            echo file_get_contents($doc->generated_documents['url_pdf']);
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
        }
    }
    
    public function save_survey_to_student_folder($survey_id, $survey_data, $contact_id)
    {
        $da = new Model_Docarrayhelper();
        $details = $da->fill_survey_data($survey_id, $survey_data);
        $document = new Model_Document();
        $document->doc_gen_and_storage(
            Model_Files::getFileId('/templates/survey_doc'),
            $details,
            '',
            'survey_doc',
            $contact_id,
            '',
            0,
            false
        );
    }
    
}