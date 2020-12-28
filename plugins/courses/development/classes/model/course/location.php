<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Location extends ORM
{
    protected $_table_name = 'plugin_courses_locations';
    protected $_deleted_column = 'delete';

    protected $_belongs_to = [
        'parent' => ['model' => 'Course_Location', 'foreign_key' => 'parent_id'],
        'county' => ['model' => 'Course_County',   'foreign_key' => 'county_id'],
        'course_schedule' => ['model' => 'Course_Schedule', 'foreign_key' => 'course_id'],
    ];

    // Get the location's county. If it doesn't have one, get the parent location's county
    public function get_county($iteration = 0)
    {
        // Arbitrary amount of times this can recurse to avoid infinite loops
        if ($iteration > 10) {
            return new Model_Course_County();
        }
        // If this has a county, return it
        elseif ($this->county->id) {
            return $this->county;
        }
        // If this doesn't have a county, run this function again on the parent location
        else {
            return $this->parent->get_county($iteration + 1);
        }
    }
}