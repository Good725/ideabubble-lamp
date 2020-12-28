<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class name in 1 word CalendarEvent will look in the model class folder
 * Class Name with underscore Calendar_Event Need to be in subfolder calendar
 */
class Model_Group extends ORM {
    protected $_table_name = 'plugin_survey_groups';

    protected $_has_many = array(
        'has_questions' => ['model' => 'SurveyHasQuestion'],
        'questions' => array('model' => 'Question'),
        'surveys'   => array('model' => 'Survey')
    );

    protected $_date_created_column  = 'created_on';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated_on';

    public static function get_all($deleted = TRUE)
    {
        $query = DB::select('a.*',array(DB::expr("CONCAT(engine_users.name,' ',engine_users.surname)"),'user'))
            ->from(array('plugin_survey_groups','a'))
            ->join('engine_users','LEFT')
            ->on('a.updated_by','=','engine_users.id');
        if($deleted) {
            $query ->where('a.deleted','=',0);
        }
        $return = $query->execute()->as_array();
        return $return;
    }

    public static function get_groups($id = NULL)
    {
        $groups = Model_Group::get_all();
        $result = '<option value="">No Group</option>';
        foreach ($groups as $group)
        {
            $selected = ($group['id'] == $id) ? 'selected="selected"' : '' ;
            $result .= '<option '.$selected.' value="'.$group['id'].'">'.$group['title'].'</option>';
        }
        return $result;
    }

    public static function delete_from_option_list($id)
    {
        $user = Auth::instance()->get_user();
        $update = array(
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $user['id'],
            'publish'    => 0,
            'deleted'    => 1
        );
        DB::update('plugin_survey_groups')->set($update)->where('id', '=', $id)->execute();
        return TRUE;
    }

}