<?php defined('SYSPATH') or die('No direct script access.');

class Model_Testimonial extends ORM
{
    protected $_table_name = 'plugin_testimonials';

    protected $_belongs_to = [
        'course' => ['model' => 'Course', 'foreign_key' => 'course_id'],
        'course_category' => ['model' => 'Course_Category', 'foreign_key' => 'course_id'],
    ];

    public function get_banner_image_url()
    {
        return $this->banner_image ? Model_Media::get_image_path($this->banner_image, 'testimonial_banners') : '';
    }

    public function get_image_url()
    {
        return Model_Media::get_image_path($this->image, 'testimonials');
    }

    /**
     * Render the results of a `find_all()` as a string for document generation
     */
    public function as_doc_string($limit = null)
    {
        if (!is_null($limit) && is_numeric($limit) && $limit > 0) {
            $testimonials = $this->apply_filters(array('limit' => $limit))->order_by(DB::expr('RAND()'))->find_all_published();
        } else {
            $testimonials = $this->apply_filters()->order_by(DB::expr('RAND()'))->find_all_published();
        }
        $string = '';
        foreach ($testimonials as $testimonial) {
            $string .= '"' . $testimonial->summary .'"<br/><br/>' .
                $testimonial->item_signature . ',<br/>' . $testimonial->item_position . ', '. $testimonial->item_company.'<br/>';
        }
        return trim($string);
    }

    /**
     * ORM query builder method
     * e.g. $testimonials = ORM::factory('Testimonial')->apply_filters($filters)->find_all_published();
     */
    public function apply_filters($filters = [])
    {
        $this->with('course');

        if (!empty($filters['term'])) {
            $this
                ->and_where_open()
                    ->where(   'testimonial.title',          'like', '%'.$filters['term'].'%')
                    ->or_where('testimonial.content',        'like', '%'.$filters['term'].'%')
                    ->or_where('testimonial.summary',        'like', '%'.$filters['term'].'%')
                    ->or_where('testimonial.item_signature', 'like', '%'.$filters['term'].'%')
                    ->or_where('testimonial.item_position',  'like', '%'.$filters['term'].'%')
                    ->or_where('testimonial.item_website',   'like', '%'.$filters['term'].'%')
                ->and_where_close();
        }

        if (!empty($filters['category_id'])) {
            $this->where('testimonial.category_id', '=', $filters['category_id']);
        }

        if (!empty($filters['course_category_ids'])) {
            $this
                ->and_where_open()
                    ->where('testimonial.course_category_id',    'in', $filters['course_category_ids'])
                    ->or_where('course.category_id', 'in', $filters['course_category_ids'])
                ->and_where_close();
        }

        if (!empty($filters['course_type_ids'])) {
            $this->where('course.type_id', 'in', $filters['course_type_ids']);
        }

        if (isset($filters['page'])) {
            $limit = Settings::instance()->get('testimonials_feed_item_count') ?: 3;
            $offset = ((int) $filters['page'] - 1) * $limit;
            $offset = $offset >= 0 ? $offset : 0;

            $this->limit($limit)->offset($offset);
        }

        if (!empty($filters['limit'])) {
            $this->limit($filters['limit']);
        }

        if (!empty($filters['offset'])) {
            $this->offset($filters['offset']);
        }

        return $this;
    }

}