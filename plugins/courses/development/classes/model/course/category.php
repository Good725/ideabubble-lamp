<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Category extends ORM
{
    protected $_table_name = 'plugin_courses_categories';
    protected $_deleted_column = 'delete';

    protected $_has_many = [
        'courses'      => ['model' => 'Course',      'foreign_key' => 'category_id'],
        'pages'        => ['model' => 'Page',        'foreign_key' => 'course_category_id'],
        'testimonials' => ['model' => 'Testimonial', 'foreign_key' => 'course_category_id']
    ];

    public function get_image_url()
    {
        if (is_numeric($this->file_id)) {
            return Model_Media::get_path_to_id($this->file_id);
        } else {
            return Model_Media::get_image_path($this->file_id, 'courses', ['cachebust' => true]);
        }
    }

    /**
     * Get all testimonials for the course category
     *
     * @param array $args - Set "include_indirect" to "true" to also get testimonials for courses within the category
     * @return array
     */
    public function get_testimonials($args = [])
    {
        $testimonials = $this->testimonials->find_all_published()->as_array('id');

        if (!empty($args['include_indirect'])) {
            $courses = $this->courses->find_all_published();
            foreach ($courses as $course) {
                $testimonials = array_merge($testimonials, $course->testimonials->find_all_published()->as_array('id'));
            }
        }

        return array_values(array_unique($testimonials));
    }
}