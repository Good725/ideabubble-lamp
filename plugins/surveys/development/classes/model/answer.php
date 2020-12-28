<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class name in 1 word CalendarEvent will look in the model class folder
 * Class Name with underscore Calendar_Event Need to be in subfolder calendar
 */
class Model_Answer extends ORM {
    protected $_table_name = 'plugin_survey_answers';

	protected $_has_many = array(
		'questions' => array('model' => 'Question'),
		'options'   => array('model' => 'AnswerOption')
	);

	protected $_belongs_to = array(
		'type' => array('model' => 'Answertype')
	);

    protected $_date_created_column  = 'created_on';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated_on';

    /*
     * For multiple choice questions, you may flag a right answer by giving it a score of 1 and the other options scores of 0.
     * This function will get the right answer in that scenario.
     */
    public function get_right_answer()
    {
        return $this->options->where('score', '=', 1)->find_undeleted();
    }

    /**
     * @param      $limit
     * @param      $offset
     * @param      $sort
     * @param      $dir
     * @param bool $search
     * @return array
     */
    public function get_all_answers($limit, $offset, $sort, $dir, $search = FALSE)
    {
        $items = DB::select('a.*',array(DB::expr("CONCAT(engine_users.name,' ',engine_users.surname)"),'user'),array('t.title','type'))
            ->from(array($this->_table_name,'a'))
            ->join('engine_users')
            ->on('a.updated_by','=','engine_users.id')
            ->join(array('plugin_survey_answer_types','t'),'LEFT')
            ->on('a.type_id','=','t.id')
            ->where('a.deleted', '=', 0);
        $_search = '';

        // Enter search Criteria for the table
        if ($search)
        {
            $items->and_where('a.title', 'LIKE', '%' . $search . '%')->or_where('a.id', 'LIKE', '%' . $search . '%');
        }
        $items = $items->order_by($sort, $dir);
		if ($limit > -1)
		{
			$items->limit($limit)->offset($offset);
		}
		$items = $items->execute()->as_array();

        // Build the table data
        $return = array();
        if ($items)
        {
            foreach ($items as $key => $item)
            {
                $type = ORM::factory('Answertype',$item['type_id'])->title;

                $return[$key]['id'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . $item['id'] . '</a>';
                $return[$key]['title'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . $item['title'] . '</a>';
                $return[$key]['type'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . $item['type'] . '</a>';
                $return[$key]['group_name'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . $item['group_name'] . '</a>';
                $return[$key]['created'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . IbHelpers::relative_time_with_tooltip($item['created_on']) . '</a>';
                $return[$key]['updated'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . IbHelpers::relative_time_with_tooltip($item['updated_on']) . '</a>';
                $return[$key]['user'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">' . $item['user'] . '</a>';

                // Actions
                $return[$key]['actions'] = '<a href="/admin/surveys/add_edit_answer/' . $item['id'] . '">Edit</a>'
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

    public static function save_option_list($id, $answer_id, $label, $value, $order)
    {
        $user = Auth::instance()->get_user();
        $update = array(
            'label'      => $label,
            'value'      => $value,
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $user['id'],
            'order_id'   => $order,
            'publish'    => 1,
            'deleted'    => 0
        );
        if (isset($id) AND $id != '')
        {
            DB::update('plugin_survey_answer_options')->set($update)->where('id', '=', $id)->execute();
        }
        else
        {
            $insert = array(
                'answer_id'  => $answer_id,
                'created_on' => date("Y-m-d H:i:s"),
                'created_by' => $user['id']
            );
            $insert = array_merge($insert, $update);
            $answer = DB::insert('plugin_survey_answer_options', array_keys($insert))->values($insert)->execute();
            $id = $answer[0];
        }
        return $id;
    }

    public static function delete_from_option_list($id)
    {
        $user = Auth::instance()->get_user();
        $update = array(
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $user['id'],
            'order_id'   => NULL,
            'publish'    => 0,
            'deleted'    => 1
        );
        DB::update('plugin_survey_answer_options')->set($update)->where('id', '=', $id)->execute();
        return TRUE;
    }

}