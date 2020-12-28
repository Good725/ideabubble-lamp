<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo_Category extends ORM
{
    protected $_table_name = 'plugin_todos_todos2_categories';
    protected $_publish_column = 'published';

    protected $_has_many = [
        'todo'     => ['model' => 'Todo_item', 'foreign_key' => 'category_id'],
    ];

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'id',
            'title',
            'published',
            'date_modified',
            null // actions
        ];

        $results = $this->apply_datatable_args($datatable_args, $column_definitions)->find_all_undeleted();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlentities($result->title);
            // List of other columns

            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] = View::factory('snippets/iblisting/publish_toggle')->set([
                'published' => $result->published,
                'id_prefix' => 'todos-categories',
                'id' => $result->id
            ])->render();
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', [
                    ['type' => 'link',   'icon' => 'pencil', 'title' => 'Edit',   'attributes' => ['class' => 'edit-link', 'href' => '/admin/todos/edit_category/'.$result->id]],
                    ['type' => 'button', 'icon' => 'close',  'title' => 'Delete', 'attributes' => ['class' => 'todos-categories-table-delete', 'data-id' => $result->id, 'data-toggle="modal" data-target="#todos-categories-table-delete-modal"']]
                ])->render();

            $rows[] = $row;
        }

        // todo merge Accident reports to get proper total count for categories
        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $results->count(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

   // Gets all todo categories that have a todo item in them with an academic year
    public function where_academic_year($academic_year, $student_id = false)
    {
       return $this->join(array(Model_Todos::TODOS_TABLE, 'todo'), 'inner')
                ->on('todo_category.id', '=', 'todo.category_id')
            ->join(array(Model_Todos::HAS_ACADEMICYEARS_TABLE, 'todos_have_academic_years'), 'inner')
                ->on('todo.id', '=', 'todos_have_academic_years.todo_id')
        ->where('todos_have_academic_years.academicyear_id', '=', $academic_year);
    }

    public function where_student_has_todo($student_id)
    {
        return $this->join(array(Model_Todos::HAS_RESULTS_TABLE, 'todo_result'), 'inner')
            ->on('todo.id', '=', 'todo_result.todo_id')
            ->where('todo_result.student_id', '=', $student_id);
    }

}