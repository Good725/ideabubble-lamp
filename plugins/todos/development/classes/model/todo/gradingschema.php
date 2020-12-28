<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo_GradingSchema extends ORM
{
    protected $_table_name = 'plugin_todos_grading_schemas';
    protected $_publish_column = 'published';

    protected $_has_many = [
        'grades' => ['model' => 'Todo_Grade', 'through' => 'plugin_todos_schemas_have_grades', 'foreign_key' => 'schema_id', 'far_key' => 'grade_id']
    ];

    public function save_relationships($data)
    {
        $db = Database::instance();
        $db->commit();

        try {
            // Save the schema
            $this->values($data);
            $this->save_with_moddate();

            // Save schema-grade relationship
            DB::delete('plugin_todos_grading_schema_points')->where('schema_id', '=', $this->id)->execute();

            if (!empty($data['grades'])) {
                foreach ($data['grades'] as $grade_data) {
                    // Update data for the grade
                    $grade = new Model_Todo_Grade($grade_data['id']);
                    $grade->values($grade_data);
                    $grade->save();

                    // $points
                    foreach ($grade_data['levels'] as $level_id => $points) {
                        $gsp = new Model_Todo_GradingSchemaPoints();
                        $gsp->grade_id   = $grade->id;
                        $gsp->level_id   = $level_id;
                        $gsp->order      = $grade_data['order'];
                        $gsp->points     = $points;
                        $gsp->schema_id  = $this->id;
                        $gsp->subject_id = $grade_data['subject_id'];
                        $gsp->save();
                    }
                }
            }
        }
        catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function get_grades()
    {
        $return = [];
        $has_grades = ORM::factory('Todo_GradingSchemaPoints')->where('schema_id', '=', $this->id)->order_by('order')->find_all();

        foreach ($has_grades as $has_grade) {
            $grade = $has_grade->grade;

            if ($grade->id) {
                if (!isset($return[$has_grade->order])) {
                    $return[$has_grade->order] = $grade->as_array();
                    $return[$has_grade->order]['percent_min'] = (float) $grade->percent_min;
                    $return[$has_grade->order]['percent_max'] = (float) $grade->percent_max;
                    $return[$has_grade->order]['subject_id'] = $has_grade->subject_id;
                    $return[$has_grade->order]['levels'] = [];
                }
                $return[$has_grade->order]['levels'][$has_grade->level_id] = $has_grade->points;
            }
        }

        return $return;
    }
    public function get_grades_string()
    {
        $return = $this->get_grades();
        $string = '';

        foreach ($return as $grade) {
            $string .= $grade->grade. ' '.((int) $grade->percent_min).' - '.((int) $grade->percent_max).'%, ';
        }

        return trim(trim($string), ',');
    }

    public function get_result($args)
    {
        $level_id   = isset($args['level_id'])   ? $args['level_id']        : null;
        $subject_id = isset($args['subject_id']) ? $args['subject_id']      : null;
        $percent    = isset($args['percent'])    ? (float) $args['percent'] : null;
        $percent    = (is_null($percent) && isset($args['result'])) ? (float) $args['result'] : $percent;
        $result = DB::select('points.*')
            ->from(['plugin_todos_grading_schema_points', 'points'])
            ->join(['plugin_todos_grades', 'grade'])->on('points.grade_id', '=', 'grade.id')
            ->where('points.schema_id',  '=',  $this->id)
            ->where('grade.percent_min', '<=', $percent)
            ->where('grade.percent_max', '>=', $percent)
            ->where('points.level_id',   '=',  $level_id)
            ->where('points.subject_id', 'in', [$subject_id, ''])
            ->order_by('subject_id', 'desc') // If there are points directly linked to the subject, give them precedence
            ->execute()
            ->as_array();
        if ($percent == 0 && $args['percent'] == '') {
            $grade_id =  null;
            $points   =  null;
        } else {
            $grade_id = isset($result[0]) ? $result[0]['grade_id'] : null;
            $points   = isset($result[0]) ? $result[0]['points']   : null;
        }
        return [
            'grade'  => new Model_Todo_Grade($grade_id),
            'points' => $points
        ];
    }


    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'id',
            'title',
            'published',
            'date_modified',
            null // actions
        ];

        $results = $this->apply_datatable_args($datatable_args, $column_definitions)->where_undeleted();
        $q = clone $results;
        $results = $q->find_all();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlentities($result->title);

            // List of other columns

            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] = '<label class="checkbox-icon">
                    <input class="todos-schemas-table-publish" type="checkbox" '.($result->published ? 'checked' : '').' data-id="'.$result->id.'" />
                    <span class="checkbox-icon-unchecked icon-ban-circle"></span><span class="checkbox-icon-checked icon-check"></span>
                </label>';
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', [
                    ['type' => 'link',   'icon' => 'pencil', 'title' => 'Edit',   'attributes' => ['class' => 'edit-link', 'href' => '/admin/todos/edit_schema/'.$result->id]],
                    ['type' => 'button', 'icon' => 'close',  'title' => 'Delete', 'attributes' => ['class' => 'list-schema-delete', 'data-id' => $result->id, 'data-toggle="modal" data-target="#todos-schemas-table-delete-modal"']]
                ])->render();

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $q->count_all(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }
}