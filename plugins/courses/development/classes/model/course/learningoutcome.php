<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_LearningOutcome extends ORM
{
    protected $_table_name = 'plugin_courses_learning_outcomes';
    protected $_publish_column = 'published';


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
        $results = $results->find_all();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlentities($result->title);
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] = View::factory('snippets/iblisting/publish_toggle')->set([
                'published' => $result->published,
                'id_prefix' => 'course-learning_outcomes',
                'id' => $result->id
            ])->render();
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', [
                    ['type' => 'link',   'icon' => 'pencil', 'title' => 'Edit',   'attributes' => ['class' => 'edit_link edit-link', 'href' => '/admin/courses/edit_learning_outcome/'.$result->id]],
                    ['type' => 'button', 'icon' => 'close',  'title' => 'Delete', 'attributes' => ['class' => 'course-learning_outcomes-table-delete', 'data-id' => $result->id, 'data-toggle="modal" data-target="#course-learning_outcomes-table-delete-modal"']]
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