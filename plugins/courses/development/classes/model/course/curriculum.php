<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Curriculum extends ORM
{
    protected $_table_name = 'plugin_courses_curriculums';
    protected $_publish_column = 'published';

    protected $_belongs_to = [
        'content' => ['model' => 'content', 'foreign_key' => 'content_id']
    ];

    protected $_has_many = [
        'courses' => ['model' => 'course',      'foreign_key' => 'curriculum_id'],
        'specs'   => ['model' => 'course_spec', 'far_key' => 'spec_id', 'foreign_key' => 'curriculum_id', 'through' => 'plugin_courses_curriculums_have_specs']
    ];


    public function save_relationships($data = null)
    {
        $db = Database::instance();
        $db->commit();

        try {
            // Save the curriculum
            $this->values($data);
            $this->save_with_moddate();

            // Save spec relationships
            DB::delete('plugin_courses_curriculums_have_specs')->where('curriculum_id', '=', $this->id)->execute();

            if (!empty($data['specs'])) {
                foreach ($data['specs'] as $spec) {
                    DB::insert('plugin_courses_curriculums_have_specs', ['curriculum_id', 'spec_id'])
                        ->values([$this->id, $spec['id']])->execute();
                }
            }

            // Save learning-objective relationships
            DB::delete('plugin_courses_curriculums_have_learning_outcomes')->where('curriculum_id', '=', $this->id)->execute();

            if (!empty($data['learning_outcomes'])) {
                foreach ($data['learning_outcomes'] as $lo) {
                    if (empty($lo['id']) && isset($lo['title'])) {
                        $new_lo = new Model_Course_LearningOutcome();
                        $new_lo->title = $lo['title'];
                        $new_lo->save_with_moddate();
                        $lo['id'] = $new_lo->id;
                    }

                    DB::insert('plugin_courses_curriculums_have_learning_outcomes', ['curriculum_id', 'learning_outcome_id', 'order'])
                        ->values([$this->id, $lo['id'], $lo['order']])->execute();
                }
            }
        }
        catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'course_curriculum.id',
            'course_curriculum.title',
            'course.title',
            'course_curriculum.published',
            'course_curriculum.date_modified',
            null // actions
        ];

        $this
            ->select(['course.title', 'course_title'])
            ->join(['plugin_courses_courses', 'course'], 'left')
                ->on('course.curriculum_id', '=', 'course_curriculum.id')
                ->on('course.deleted', '=', DB::expr("0"))
            ->group_by('course_curriculum.id');

        $results = $this->apply_datatable_args($datatable_args, $column_definitions)->where_undeleted();
        $q = clone $results;
        $results = $results->find_all();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlspecialchars($result->title);
            $row[] = htmlspecialchars($result->course_title);
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] = View::factory('snippets/iblisting/publish_toggle')->set([
                'published' => $result->published,
                'id_prefix' => 'course-curriculums',
                'id' => $result->id
            ])->render();
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', [
                    ['type' => 'link',   'icon' => 'pencil', 'title' => 'Edit',   'attributes' => ['class' => 'edit-link', 'href' => '/admin/courses/edit_curriculum/'.$result->id]],
                    ['type' => 'button', 'icon' => 'close',  'title' => 'Delete', 'attributes' => ['class' => 'course-curriculums-table-delete', 'data-id' => $result->id, 'data-toggle="modal" data-target="#course-curriculums-table-delete-modal"']]
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

    // Could use a $_has_many with "through", but that wouldn't get the "order" column
    public function get_learning_outcomes()
    {
        $learning_outcomes = [];
        $has_learning_outcomes = DB::select()->from('plugin_courses_curriculums_have_learning_outcomes')->where('curriculum_id', '=', $this->id)->order_by('order')->execute()->as_array();

        foreach ($has_learning_outcomes as $hlo) {
            $lo = ORM::factory('Course_LearningOutcome')->where('id', '=', $hlo['learning_outcome_id'])->find_undeleted();
            if ($lo->id) {
                $learning_outcomes[$hlo['order']] = $lo;
            }
        }

        return $learning_outcomes;
    }
}