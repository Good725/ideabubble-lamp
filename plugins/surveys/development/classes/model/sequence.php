<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class name in 1 word CalendarEvent will look in the model class folder
 * Class Name with underscore Calendar_Event Need to be in subfolder calendar
 */
class Model_Sequence extends ORM {
    protected $_table_name = 'plugin_survey_sequence';

	protected $_belongs_to = array(
		'survey'   => array('model' => 'Survey')
	);
	protected $_has_many = array(
		'has_questions' => array('model' => 'Surveyhasquestion'),
		'items'         => array('model' => 'Sequenceitems')
	);

    /**
     * @param      $limit
     * @param      $offset
     * @param      $sort
     * @param      $dir
     * @param bool $search
     * @return array
     */
    public function get_all_sequences($limit, $offset, $sort, $dir, $search = FALSE)
    {
        $_limit = ($limit > -1) ? $offset . ',' . $limit : '';
        $items = DB::select('s.*',array(DB::expr("CONCAT(engine_users.name,' ',engine_users.surname)"),'user'),array('survey.title','survey_title'))
            ->from(array($this->_table_name,'s'))
            ->join('engine_users')
            ->on('s.updated_by','=','engine_users.id')
            ->join(array('plugin_survey','survey'))
            ->on('s.survey_id','=','survey.id')
            ->where('s.deleted', '=', 0);
        $_search = '';

        // Enter search Criteria for the table
        if ($search)
        {
            $items->and_where('s.title', 'LIKE', '%' . $search . '%')->or_where('s.id', 'LIKE', '%' . $search . '%');
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
                $survey = ORM::factory('Survey',$item['survey_id']);
                $return[$key]['id'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . $item['id'] . '</a>';
                $return[$key]['title'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . $item['title'] . '</a>';
                $return[$key]['survey_id'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . $item['survey_id'] . '</a>';
                $return[$key]['survey_title'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . $item['survey_title'] . '</a>';
                $return[$key]['created_on'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . IbHelpers::relative_time_with_tooltip($item['created_on']) . '</a>';
                $return[$key]['updated_on'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . IbHelpers::relative_time_with_tooltip($item['updated_on']) . '</a>';
                $return[$key]['user'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">' . $item['user'] . '</a>';

                // Actions
                $return[$key]['actions'] = '<a href="/admin/surveys/add_edit_sequence/' . $item['id'] . '">Edit</a>'
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

    public static function save_sequence_items($sequence,$s_id)
    {
        $user = Auth::instance()->get_user();
        $update = array (
            'target_id'     => $sequence->target_id,
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $user['id'],
            'survey_action' => $sequence->action ?? '0'
        );
        if ($sequence->id != '' AND isset($sequence->id))
        {
//            $s = DB::select()->from('plugin_survey_sequence_items')->where('id', '=', $sequence->id)->execute()->as_array();
            DB::update('plugin_survey_sequence_items')->set($update)->where('id','=',$sequence->id)->execute();
        }
        else
        {
            $insert = array (
                'created_on'    => $update['updated_on'],
                'created_by'    => $update['updated_by'],
                'question_id'   => $sequence->question_id,
                'survey_action' => $sequence->action,
                'sequence_id'   => $s_id,
                'answer_option_id'     => $sequence->answer_id
            );
            $insert = array_merge($insert,$update);
            DB::insert('plugin_survey_sequence_items',array_keys($insert))->values($insert)->execute();
        }
        return TRUE;
    }
}