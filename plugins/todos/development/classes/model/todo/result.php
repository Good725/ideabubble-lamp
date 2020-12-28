<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo_Result extends ORM
{
    protected $_table_name = 'plugin_todos_todos2_has_results';

    protected $_belongs_to = [
        'contact'  => ['model' => 'Contacts3_Contact', 'foreign_key' => 'student_id'],
        'level'    => ['model' => 'Course_Level',      'foreign_key' => 'level_id'],
        'schedule' => ['model' => 'Course_Schedule',   'foreign_key' => 'schedule_id'],
        'subject'  => ['model' => 'Course_Subject',    'foreign_key' => 'subject_id'],
        'todo'     => ['model' => 'Todo_Item',         'foreign_key' => 'todo_id']
    ];

    /**
     * @param string $format - 'grade' (object), 'grade_name', 'percent' or 'points', leave blank to get an array with all
     * @return array
     */
    public function get_result($format = null)
    {
        $args = [
            'level_id'   => $this->level_id,
            'subject_id' => $this->subject_id,
            'percent'    => $this->result
        ];

        $return = $this->todo->schema->get_result($args);

        $level_short = $this->level->get_short_name();

        // Update results like H2-O2-F2, etc. to get the grade corresponding to the level
        if (preg_match('/'.$level_short.'(\d+)/', $return['grade']->grade, $matches)) {
            $grade_name = $matches[0];
        } else {
            $grade_name = $return['grade']->grade;
        }

        $return = [
            'grade'      => $return['grade'],
            'grade_name' => $grade_name ? $grade_name : $return['grade']->grade,
            'percent'    => $this->result,
            'points'     => $return['points']
        ];

        return $format ? $return[$format] : $return;
    }
}