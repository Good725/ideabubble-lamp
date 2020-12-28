<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Courses extends Controller_Api
{
    protected $user = null;

    public function before()
    {
        parent::before();
    }

    public function action_details()
    {
        $get = $this->request->query();

        $course = Model_Courses::get_course($get['id']);


        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['course'] = $course;
    }

    public function action_categories()
    {
        $categories = Model_Categories::get_all_published_categories();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['categories'] = $categories;
    }

    public function action_academic_years()
    {
        $academic_years = Model_AcademicYear::get_all();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['academic_years'] = $academic_years;
    }

    public function action_years()
    {
        $years = Model_Years::get_all_years();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['years'] = $years;
    }

    public function action_locations()
    {
        $locations = Model_Locations::get_locations_where(array());
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['locations'] = $locations;
    }

    public function action_topics()
    {
        $topics = Model_Topics::get_all_topics();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['topics'] = $topics;
    }

    public function action_subjects()
    {
        $subjects = Model_Subjects::get_all_subjects();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['subjects'] = $subjects;
    }

    public function action_types()
    {
        $types = Model_Types::get_all_types();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['types'] = $types;
    }

    public function action_levels()
    {
        $levels = Model_Levels::get_all_levels();
        foreach ($levels as $i => $level) {
            $levels[$i] = array(
                'id' => $level['id'],
                'level' => $level['level']
            );
        }
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['levels'] = $levels;
    }
}