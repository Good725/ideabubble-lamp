<?php
defined('SYSPATH')or die('No direct script access.');

class Model_Page extends ORM
{
    protected $_table_name = 'plugin_pages_pages';
    protected $_date_created_column = 'date_entered';
    protected $_date_modified_column = 'last_modified';

    protected $_belongs_to = [
        'layout'          => ['model' => 'Engine_Layout',   'foreign_key' => 'layout_id'],
        'course'          => ['model' => 'Course',          'foreign_key' => 'course_id'],
        'course_category' => ['model' => 'Course_Category', 'foreign_key' => 'course_category_id'],
        'subject'         => ['model' => 'Course_Subject',  'foreign_key' => 'subject_id']
    ];

    // Wrapper to allow save function from old model file to work with ORM
    public function save_data($data)
    {
        $this->save_with_moddate();
        $old_pages_model = new Model_Pages();
        $data['pages_id'] = $this->id;

        $old_pages_model->set_page_data($data);
    }

    public function where_is_current()
    {
        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';
        $microsite_suffix = $microsite_suffix ? '--'.$microsite_suffix : '';
        $request  = Request::current();
        $name = $request->param('page');

        // Check both '/page-url' and '/page-url.html'
        $tags[] = str_replace('.html', '', $name.$microsite_suffix).'.html';
        $tags[] = str_replace('.html', '', $name.$microsite_suffix);

        return $this->where('name_tag', 'in', $tags);
    }

    public function get_color()
    {
        if ($this->course_id)               { return $this->course->get_color(); }
        else if ($this->subject_id)         { return $this->subject->color; }
        else if ($this->course_category_id) { return $this->course_category->color; }
        else { return false; }
    }

    public function get_testimonials($args = [])
    {
        $args['include_indirect'] = isset($args['include_indirect']) ? $args['include_indirect'] : false;

        // If the page is linked to a course, get the course testimonials
        if ($this->course_id) {
            return $this->course->testimonials->find_all_published();
        }
        // If the page is linked to a subject, get the subject testimonials
        else if ($this->subject_id) {
            return $this->subject->get_testimonials($args);
        }
        // If the page is linked to a course category, get the category testimonials
        else if ($this->course_category_id) {
            return $this->course_category->get_testimonials($args);
        }
        // If not linked to any of the above, get all testimonials or none
        else if ($args['include_indirect']) {
            return ORM::factory('Testimonial')->find_all_published();
        } else {
            return [];
        }
    }

    public function get_courses()
    {
        // Only show courses on the frontend that have a schedule with Book on website is on, unless specifically requested the course
        if ($this->course_id) {
            return ORM::factory('Course')->where('id', '=', $this->course_id)->find_all_published();
        }
        else if ($this->subject_id) {
            return $this->subject->courses->find_all_available();
        }
        else if ($this->course_category_id) {
            return $this->course_category->courses->find_all_available();
        } else {
            return [];
        }
    }

    // todo: We should really remove all instances of ".html" from name tags and just support the one version
    public function where_name($page_name)
    {
        $page_name = str_replace('.html', '', $page_name);
        return $this
            ->or_where_open()
            ->where('name_tag', '=', $page_name)
            ->or_where('name_tag', '=', $page_name.'.html')
            ->or_where_close();
    }

    public function search($term)
    {
        return $this
            ->select([DB::expr("CONCAT('/', `name_tag`)"), 'url'])
            ->select([DB::expr("IF (`title`, `title`, `name_tag`)"), 'search_title'])
            ->select([DB::expr("IF (`title` = " . Database::instance()->quote($term). " OR `name_tag` = " . Database::instance()->quote($term) . ", 1, 0)"), 'is_title_match'])
            ->select([DB::expr("IF (`title` LIKE " . Database::instance()->quote("%" . $term . "%") . " OR `name_tag` LIKE " . Database::instance()->quote("%" . $term . "%") . ", 1, 0)"), 'is_partial_title_match'])
            ->select([DB::expr("IF (`content` LIKE " . Database::instance()->quote("%" . $term . "%") . ", 1, 0)"), 'is_content_match'])
            ->and_where_open()
                ->where('name_tag', 'like', '%'.$term.'%')
                ->or_where('title', 'like', '%'.$term.'%')
                ->or_where('content', 'like', '%'.$term.'%')
            ->and_where_close()
            ->order_by('is_title_match', 'desc')
            ->order_by('is_partial_title_match', 'desc')
            ->order_by('is_content_match', 'desc')
            ->order_by('title');
    }

    /* Return the first sentence containing a searched term, with the searched term bolded */
    public function get_matching_content($term)
    {
        // todo: replace "expand_short_tags" with "parse_page_content" after the appropriate branch has been merged
        $content = preg_replace('/\n\s*\n\s*/', '¶', strip_tags(IbHelpers::expand_short_tags($this->content)));
        $regex = '/[A-Z][^\\.\\?\\!\\¶]*('.$term.')[^\\.\\?\\!\\¶]*/i';

        if ($term && preg_match($regex, $content, $match)) {
            return trim(preg_replace('/('.$term.')/i', '<strong>$1</strong>', $match[0]));
        } else {
            return false;
        }
    }

    // This alias can be removed after confirming that it is no longer used.
    public function has_video()
    {
        self::has_media_player();
    }

    /* Checks if the loaded page contains a video */
    public function has_media_player()
    {
        $has_video = (strpos($this->content, '{video-') || strpos($this->footer, '{video-'));
        $has_audio = (strpos($this->content, '{audio-') || strpos($this->footer, '{audio-'));

        // If a course is being displayed through the page, check the course details
        // Not exactly ideal as this is not relevant to the page model.
        if (!$has_video && strpos($this->layout->layout, 'course_detail') !== false && !empty($_GET['id'])) {
            $course = new Model_Course($_GET['id']);
            $has_video = strpos($course->description, '{video-');
            $has_audio = strpos($course->description, '{audio-');
        }

        // Similarly check for news items
        if (!$has_video && strpos($this->layout->layout, 'news') === 0 && !empty(Request::current()->param('item_identifier'))) {
            $identifier = Request::current()->param('item_identifier');
            $news       = ORM::factory('News_Item')->where_identifier($identifier)->find_frontend();
            $has_video  = strpos($news->content, '{video-');
            $has_audio  = strpos($news->content, '{audio-');
        }

        return $has_video || $has_audio;
    }

    // Publish a draft (replace the original page with its content)
    public function publish_draft()
    {
        $draft = $this;
        if ($this->draft_of) {
            $original_page = new Model_Page($draft->draft_of);

            // Replace the original page with draft content
            $draft_content = $draft->as_array();
            unset($draft_content['id']);
            $original_page->values($draft_content);

            // Ensure the page is published and not a draft.
            $original_page->set('publish', 1);
            $original_page->set('draft_of', 0);
            $original_page->save_with_moddate();

            // Discard the draft
            $draft->delete_and_save();
        }
    }
}