<?php defined('SYSPATH') or die('No direct script access.');

class Model_News_Item extends ORM
{

    protected $_table_name = 'plugin_news';
    protected $_belongs_to = [
        'category'        => ['model' => 'News_Category',   'foreign_key' => 'category_id'],
        'course'          => ['model' => 'Course',          'foreign_key' => 'course_id'],
        'course_category' => ['model' => 'Course_Category', 'foreign_key' => 'course_category_id'],
        'course_subject'  => ['model' => 'Course_Subject',  'foreign_key' => 'course_subject_id'],
        'creator'         => ['model' => 'User',            'foreign_key' => 'created_by'],
    ];

    public function get_url_name()
    {
        $pages_model = new Model_Pages();
        return $pages_model->filter_name_tag($this->title);
    }

    public function get_url()
    {
        $category_url_name = Model_Pages::filter_name_tag($this->category->category);
        $item_url_name = Model_Pages::filter_name_tag($this->title);

        return '/news/'.$category_url_name.'/'.$item_url_name;
    }

    public function get_image_url()
    {
        if ($this->image) {
            return Model_Media::get_image_path($this->image, 'news', ['cachebust' => true]);
        } else {
            return null;
        }
    }

    public function get_date($format = null)
    {
        $date = ($this->event_date) ? $this->event_date : $this->date_modified;

        if ($date && $format) {
            return date($format, strtotime($date));
        } else {
            return $date;
        }
    }

    // Filter to items matching a URL identifier
    public function where_identifier($identifier)
    {
        return $this->where('id', '=', Model_News::get_news_id($identifier));
    }
    
    public function apply_filters($filters = [])
    {
        $this->with('course');

        if (!empty($filters['term'])) {
            $this->where('news_item.title', 'like', '%'.$filters['term'].'%');
        }

        if (!empty($filters['category_id'])) {
            $this->where('news_item.category_id', '=', $filters['category_id']);
        }

        if (!empty($filters['course_category_ids'])) {
            $this
                ->and_where_open()
                    ->where('course_category_id',    'in', $filters['course_category_ids'])
                    ->or_where('course.category_id', 'in', $filters['course_category_ids'])
                ->and_where_close();
        }

        if (!empty($filters['course_type_ids'])) {
            $this->where('course.type_id', 'in', $filters['course_type_ids']);
        }

        if (!empty($filters['media_type'])) {
            $this->where('news_item.media_type', '=', $filters['media_type']);
        }

        if (!empty($filters['media_types'])) {
            $this->where('news_item.media_type', 'in', $filters['media_types']);
        }

        if (isset($filters['page'])) {

            // Use the "items per page" setting for articles. Use 3 for everything else.
            if (empty($filters['media_type']) || $filters['media_type'] == 'Article') {
                $limit = Settings::instance()->get('news_feed_item_count') ?: 3;
            } else {
                $limit = 3;
            }

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

    public function find_frontend($all = false)
    {
        $query = $this
            ->and_where_open()
                ->where('date_publish', 'IS', null)
                ->or_where('date_publish', '<', DB::expr('NOW()'))
            ->and_where_close()
            ->and_where_open()
                ->where('date_remove', 'IS', null)
                ->or_where('date_remove', '>', DB::expr('NOW()'))
            ->and_where_close()
            // Put numbers 1, 2, 3, ... first. Then list 0 and NULL.
            ->order_by(DB::expr("CASE WHEN `order`='0' THEN NULL ELSE -`order` END"), 'DESC')
            ->order_by('event_date', 'DESC')
            ;

        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

        if (!empty($microsite_suffix)) {
            // Only get news items that end in the microsite suffix or do not use any suffix
            $query
                ->and_where_open()
                    ->where('news_item.title', 'like', '%--'.$microsite_suffix)
                    ->or_where('news_item.title', 'not like', '%--%')
                ->and_where_close();
        }

        if ($all) {
            return $query->find_all_published();
        } else {
            return $query->find_published();
        }
    }

    public function find_all_frontend()
    {
        return $this->find_frontend('all');
    }

    /**
     * Render a news item as HTML
     */
    public function parse_html()
    {
        // Replace short tags, apply localisation, parse block editor, etc.
        $content = IbHelpers::parse_page_content($this->content);

        // If the setting to auto-prefix news items with an AddThis is toolbox is enabled...
        // ... add the toolbox, if it has not already been added to the news item.
        if (strpos($this->content, '{addthis_toolbox') === false && Settings::instance()->get('auto_addthis_on_news')) {
            $content = IbHelpers::addthis_toolbox() . $content;
        }

        return $content;
    }
}