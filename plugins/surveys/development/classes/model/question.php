<?php defined('SYSPATH') or die('No direct script access.');

/**
 */
class Model_Question extends ORM {
    protected $_table_name = 'plugin_survey_questions';

    protected $_has_many = array(
		'has_questions' => array('model' => 'Surveyhasquestion')
    );
	protected $_belongs_to = array(
		'answer' => array('model' => 'Answer')
	);

    protected $_date_created_column  = 'created_on';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated_on';

    /**
     * @param      $limit
     * @param      $offset
     * @param      $sort
     * @param      $dir
     * @param bool $search
     * @return array
     */
    public function get_all_questions($limit, $offset, $sort, $dir, $search = FALSE)
    {
        $_limit = ($limit > -1) ? $offset . ',' . $limit : '';
        $items = DB::select('q.*',array('a.title','answer'),array(DB::expr("CONCAT(engine_users.name,' ',engine_users.surname)"),'user'))->from(array($this->_table_name,'q'))
            ->join(array('plugin_survey_answers','a'))
            ->on('q.answer_id','=','a.id')
            ->join('engine_users')
            ->on('q.updated_by','=','engine_users.id')
            ->where('q.deleted', '=', 0);
        $_search = '';

        // Enter search Criteria for the table
        if ($search)
        {
            $items->and_where('q.title', 'LIKE', '%' . $search . '%')->or_where('q.id', 'LIKE', '%' . $search . '%');
        }
        $items = $items->order_by($sort, $dir);
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
                $return[$key]['id'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">' . $item['id'] . '</a>';
                $return[$key]['title'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">' . $item['title'] . '</a>';
                $return[$key]['answer'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">' . $item['answer'] . '</a>';
                $return[$key]['created_on'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">' . IbHelpers::relative_time_with_tooltip($item['created_on']) . '</a>';
                $return[$key]['updated_on'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">' . IbHelpers::relative_time_with_tooltip($item['updated_on']) . '</a>';
                $return[$key]['user'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">' . $item['user'] . '</a>';

                // Actions
                $return[$key]['actions'] = '<a href="/admin/surveys/add_edit_question/' . $item['id'] . '">Edit</a>'
                                        .'<a href="#" class="delete" data-id="' . $item['id'] . '">Delete</a>';
                // publish
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

    public static function get_question_answer($id = NULL , $answer_id = NULL)
    {
        $result = NULL;
        if ( !is_null($id))
        {
            $question = ORM::factory('Question', $id);
            $answer_id = $question->answer_id;
        }
        if ( ! is_null($answer_id))
        {
            $answer = ORM::factory('Answer', $answer_id);
            $options = ORM::factory('AnswerOption')->where('answer_id', '=', $answer_id)->where('publish','=',1)->where('deleted','=',0)->find_all()->as_array();
            if ($options)
            {
                $result = array();
                foreach ($options as $key => $option)
                {
                    $result[] = array(
                        'order' => $key,
                        'id'    => $option->id,
                        'label' => $option->label,
                        'value' => $option->value,
                        'type'  => ORM::factory('Answertype', $answer->type_id)->title
                    );
                }
            }
            else
            {
                $result[] = array(
                    'order' => NULL,
                    'id'    => NULL,
                    'label' => NULL,
                    'value' => NULL,
                    'type'  => ORM::factory('Answertype', $answer->type_id)->title
                );
            }
        }
        return $result;
    }

    public function render($args = [])
    {
        return View::factory('frontend/input/'.$this->answer->type->stub)->set([
            'question' => $this,
            'name'     => isset($args['name'])  ? $args['name']  : null,
            'response' => isset($args['value']) ? $args['value'] : null
        ]);
    }
}