<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Subject extends ORM
{
    protected $_table_name = 'plugin_courses_subjects';

    protected $_has_many = [
        'courses'      => ['model' => 'Course',      'foreign_key' => 'subject_id'],
        'pages'        => ['model' => 'Page',        'foreign_key' => 'subject_id'],
        'testimonials' => ['model' => 'Testimonial', 'foreign_key' => 'subject_id'],
    ];

    public function get_image_url()
    {
        return Model_Media::get_image_path($this->image, 'courses');
    }

    /**
     * Get all testimonials for the subject
     *
     * @param array $args - Set "include_indirect" to "true" to also get testimonials for courses within the subject
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