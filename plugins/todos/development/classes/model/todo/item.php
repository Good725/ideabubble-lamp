<?php defined('SYSPATH') or die('No direct script access.');

// This is called "Todo_Item", rather than "Todo", because "Todo" was already taken and uses some functions that...
// ... conflict with the standard ORM methods
class Model_Todo_Item extends ORM
{
    protected $_table_name           = 'plugin_todos_todos2';
    protected $_publish_column       = 'published';
    protected $_date_created_column  = 'created';
    protected $_date_modified_column = 'updated';
    protected $_created_by_column    = 'created_by';
    protected $_modified_by_column   = 'updated_by';

    protected $_belongs_to = [
        'schema' => ['model' => 'Todo_GradingSchema', 'foreign_key' => 'grading_schema_id'],
        'todo_category' => ['model' => 'Todo_Category', 'foreign_key' => 'category_id'],
    ];

    protected $_has_many = [
        'academic_years'   => ['model' => 'AcademicYear',      'far_key' => 'academic_year_id', 'foreign_key' => 'todo_id', 'through' => 'plugin_todos_todos2_has_academicyears'],
        // "assignees" directly loads the contact model for the assignees
        'assignees'        => ['model' => 'Contacts3_Contact', 'far_key' => 'contact_id',       'foreign_key' => 'todo_id', 'through' => 'plugin_todos_todos2_has_assigned_contacts'],
        'file_submissions' => ['model' => 'Todo_FileSubmission', 'foreign_key' => 'todo_id'],
        // "has_assignees" loads the relationship table as an object
        'has_assignees'    => ['model' => 'Todo_HasAssignee',  'foreign_key' => 'todo_id'],
        'todo_result'      => ['model' => 'Todo_Result',       'foreign_key' => 'todo_id'],
    ];

    public function get_for_datatable($filters = [], $datatable_args = []) {

        $return['sEcho'] = intval($datatable_args['sEcho']);

        if (!Auth::instance()->has_access('todos_view_results') ||
            ($datatable_args['my'] == 'true' && Auth::instance()->has_access('todos_view_results_limited'))) {
            $user = Auth::instance()->get_user();
            $this->user = Model_Users::get_user($user['id']);
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            if (!isset($contacts[0])) {
                return array();
            }

            $contact = new Model_Contacts3($contacts[0]['id']);
            if ($contact->get_is_primary()) {
                $family_id = $contact->get_family_id();
                $family = new Model_Family($family_id);
                $members = $family->get_members();
                $datatable_args['student_id'] = array();
                foreach ($members as $member) {
                    $datatable_args['student_id'][] = $member->get_id();
                }
            } else {
                $datatable_args['student_id'] = $contacts[0]['id'];
            }
        }
        if (Settings::instance()->get('todos_site_allow_online_exams')) {
            $datatable_args['extended_results'] = 1;
        }
        $return['aaData'] = Model_Todos::results_datatable($datatable_args);
        $return['iTotalRecords'] = DB::select(DB::expr('@found_results as total'))->execute()->get('total');
        $return['iTotalDisplayRecords'] = count($return['aaData']);
        return $return;
    }
}