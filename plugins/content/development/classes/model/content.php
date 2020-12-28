<?php

class Model_Content extends ORM
{
    protected $_table_name = 'plugin_content_content';

    protected $_belongs_to = [
        'type'   => ['model' => 'Content_Type', 'foreign_key' => 'type_id'],
        'parent' => ['model' => 'Content',      'foreign_key' => 'parent_id'],
        'survey' => ['model' => 'Survey',       'foreign_key' => 'survey_id']
    ];

    protected $_has_many = array(
        'children'          => ['model' => 'Content', 'foreign_key' => 'parent_id'],
        'learning_outcomes' => ['model' => 'Course_LearningOutcome', 'through' => 'plugin_content_has_learning_outcomes', 'foreign_key' => 'content_id', 'far_key' => 'learning_outcome_id']
    );

    public static function get_children_tree($id)
    {
        $media_folder = Kohana::$config->load('config')->project_media_folder;

        $a = array();
        $parent = new Model_Content($id);
        $children = $parent->get_ordered_children()->as_array();
        foreach ($children as $child) {
            $type = $child->type->name;
            $child = $child->as_array();
            $child['text'] = str_replace("href='/", "href='" . URL::site(), $child['text']);
            $child['text'] = str_replace('href="/', 'href="' . URL::site(), $child['text']);
            $child['text'] = str_replace('src="/', 'src="' . URL::site(), $child['text']);
            $child['type'] = $type;
            if ($child['file_id'] && $child['file_url'] == '') {
                $media = DB::select('medias.*')
                    ->from(array(Model_Media::TABLE_MEDIA, 'medias'))
                    ->where('id', '=', $child['file_id'])
                    ->execute()
                    ->current();
                $child['file_path'] = Model_Media::get_path_to_media_item_admin($media_folder, $media['filename'], $media['location']);

            }
            $child['children'] = self::get_children_tree($child['id']);
            $a[] = $child;
        }
        return $a;
    }

    // Filter content to only items linked to schedules that the user has booked and can access.
    public function where_is_booked()
    {
        $available_ids = [];
        $now = strtotime(date('Y-m-d H:i:s'));
        $bookings = ORM::factory('Booking_Booking')->where_auth_booked()->find_all_undeleted();

        foreach ($bookings as $booking) {
            // If the booking has been cancelled or is a sales quote, it is not available.
            if (in_array($booking->status->title, ['Cancelled', 'Sales Quote'])) {
                continue;
            }

            // Booked content is only available for a certain date range
            // X days before start date. Y days after end date.
            $after_start_date = $now >= strtotime($booking->content_available_from_date());
            $before_end_date = $now <= strtotime($booking->content_available_to_date());

            if ($after_start_date && $before_end_date) {
                $available_ids[] = $booking->schedules->find()->content_id;
            }
        }

        // Update ORM query
        if (empty($available_ids)) {
            // Haven't booked anything, return no results
            $this->where('id', '=', '-1');
        } else {
            $this->where('id', 'in', $available_ids);
        }

        return $this;
    }

    // Check if the currently loaded object is booked and is available
    public function is_booked()
    {
        $content = ORM::factory('Content')->where_is_booked()->and_where('id', '=', $this->id)->find();

        return (bool) $content->id;
    }

    /* Published content to only include content in date range */
    public function find_published()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->available_from) {
            $this->where('available_from', '<', $now);
        }
        if ($this->available_to) {
            $this->where('available_to', '>', $now);
        }

        return parent::find_published();
    }

    public function find_all_published()
    {
        $now = date('Y-m-d H:i:s');

        $this->and_where_open();
            $this->where('available_from', '<', $now);
            $this->or_where('available_from', 'is', null);
        $this->and_where_close();
        $this->and_where_open();
            $this->where('available_to', '>', $now);
            $this->or_where('available_to', 'is', null);
        $this->and_where_close();

        return parent::find_all_published();
    }

    public function has_content()
    {
        return ($this->text || $this->file_id || $this->file_url || $this->survey_id);
    }

    public function get_icon()
    {
        // todo: move to database
        switch ($this->type->name) {
            case 'audio': return 'volume-up';   break;
            case 'file':  return 'file-pdf-o';  break;
            case 'pdf':   return 'file-pdf-o';  break;
            case 'image': return 'picture-o';   break;
            case 'video': return 'play-circle'; break;
            default:      return ($this->survey->id) ? 'question-circle-o' : 'font'; break;
        }
    }

    public function save_relationships($data)
    {
        $db = Database::instance();
        $db->commit();
        try {
            $this->values($data);
            $this->save_with_moddate();

            if (isset($data['learning_outcome_ids'])) {
                DB::delete('plugin_content_has_learning_outcomes')->where('content_id', '=', $this->id)->execute();

                if (is_array($data['learning_outcome_ids'])) {
                    foreach ($data['learning_outcome_ids'] as $lo_id) {
                        DB::insert('plugin_content_has_learning_outcomes')->values(['content_id' => $this->id, 'learning_outcome_id' => $lo_id])->execute();
                    }
                }
            }

            $survey_content_type = new Model_Content_Type(['name' => 'questionnaire']);

            // Save the survey, if there is one
            if (!empty($data['type_id']) && $data['type_id'] == $survey_content_type->id) {
                $survey_data = isset($data['survey_data']) ? $data['survey_data'] : [];

                $survey = new Model_Survey(isset($data['survey_id']) ? $data['survey_id'] : $this->survey_id);
                $survey->title = $this->name;
                $survey->expiry = 0;
                $survey->pagination = 1;
                $survey->store_answer = 1;
                $survey->show_score = 1;
                $survey->result_pdf_download = 0;
                $survey->save_questionnaire($survey_data);

                $this->survey_id = $survey->id;
                $this->save();
            }

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public function render()
    {
        switch ($this->type->name) {
            case 'audio':
                return $this->render_audio();
                break;
            case 'video':
                return $this->render_video();
                break;
            case 'pdf':
                $url = $this->file_url ? $this->file_url : Model_Media::get_path_to_id($this->file_id);

                return '<iframe frameborder="0" src="'.$url.'"></iframe>';
                break;
            default:
                return ($this->survey->id) ? ''.$this->survey->render() : IbHelpers::parse_page_content($this->text);
        }
    }

    public function render_audio()
    {
        $filepath = Model_Media::get_path_to_id($this->file_id);
        return '<audio controls src="'.$filepath.'">Your browser does not support the <code>audio</code> element.</audio>';
    }

    public function render_video()
    {
        $return = '';
        if ($this->type->name == 'video') {
            // YouTube video
            if (strpos($this->file_url, 'youtu') !== false) {
                // Get the ID from the URL
                preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $this->file_url, $video_id);
                $video_id = isset($video_id[0]) ? $video_id[0] : '';
                $src = 'https://www.youtube.com/embed/'.$video_id.'?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1';
            }
            // Video video
            else if (strpos($this->file_url, 'vimeo') !== false) {
                preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $this->file_url, $video_id);
                $video_id = isset($video_id[0]) ? $video_id[0] : '';
                $src = 'https://player.vimeo.com/video/'.$video_id.'?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media';
            }

            if (!empty($src)) {
                $return = '<div class="plyr__video-embed" id="content-'.$this->id.'-video">
                    <iframe src="'.$src.'" allowfullscreen allowtransparency allow="autoplay"></iframe>
                </div>';
            }
            // Uploaded video
            else {
                $filepath = Model_Media::get_path_to_id($this->file_id);
                $return = '<div id="content-'.$this->id.'-video"><video controls style="width: 100%;"><source src="'.$filepath.'"></video></div>';
            }
        }

        return $return;
    }

    public function get_ordered_children()
    {
        return $this->children->order_by('order')->find_all_published();
    }

    public function get_duration_formatted($format = null)
    {
        return IbHelpers::seconds_to_time($this->duration, $format);
    }

    public function get_children_duration_formatted($format = null)
    {
        $children = $this->get_ordered_children()->as_array();
        $duration = array_sum(array_column($children, 'duration'));

        return IbHelpers::seconds_to_time($duration, $format);
    }

    public function render_editor($args = [])
    {
        $edit_button_at_depth = isset($args['edit_button_at_depth']) ? $args['edit_button_at_depth'] : 1;

        return View::factory('admin/content_tree')
            ->set('args', $args)
            ->set('content', $this)
            ->set('edit_button_at_depth', $edit_button_at_depth)
            ->set('learning_outcomes', isset($args['learning_outcomes']) ? $args['learning_outcomes'] : [])
            ->set('surveys', ORM::factory('Survey')->find_all_published())
            ->set('types',   ORM::factory('Content_Type')->order_by('order')->find_all_published());
    }

    public static function embed_accordion($id)
    {
        $column = is_numeric($id) ? 'id' : 'name';
        $content = ORM::factory('Content')
            ->where($column, '=', $id)
            ->where('parent_id', 'is', null)
            ->find_published();

        if (!$content->id ) {
            return 'Content <code>'.$id.'</code> not found.';
        } else {
            return View::factory('front_end/subjects_accordion')->set([
                'type' => 'content',
                'items' => $content->id ? $content->children->find_all_published() : []
            ])->render();
        }
    }

    // Check if the user has complete this section
    public function is_complete_by_user($user = null)
    {
        $user = $user ?? Auth::instance()->get_user();

        $progress = ORM::factory('Content_Progress')
            ->where('user_id',     '=', $user['id'])
            ->where('section_id',  '=', $this->id)
            ->where('is_complete', '=', 1)
            ->find_all_undeleted();

        return ($progress->count() > 0);
    }

    public function count_user_complete_children($user = null)
    {
        $children = $this->get_ordered_children();
        $count = 0;

        foreach ($children as $child) {
            $count += $child->is_complete_by_user($user) ? 1 : 0;
        }

        return $count;
    }

    public function count_user_complete_subsections($user = null)
    {
        $children = $this->get_ordered_children();
        $count = 0;

        foreach ($children as $child) {
            $count += $child->count_user_complete_children($user);
        }

        return $count;
    }

    public function count_lessons()
    {
        if (!$this->id) {
            return 0;
        }

        $sections = $this->children->find_all_undeleted();

        $count = 0;
        foreach ($sections as $section) {
            $count += $section->children->find_all_undeleted()->count();
        }

        return $count;
    }

    // Get all learning outcomes for an item, in the order they are listed for a specified curriculum
    public function get_ordered_learning_outcomes($curriculum_id)
    {
        $content_has_los = DB::select(
            ['curriculum_has_lo.order', 'order'],
            ['curriculum_has_lo.learning_outcome_id', 'learning_outcome_id']
        )
            ->from(['plugin_courses_curriculums_have_learning_outcomes', 'curriculum_has_lo'])
            ->join(['plugin_content_has_learning_outcomes', 'content_has_lo'])->on('curriculum_has_lo.learning_outcome_id', '=', 'content_has_lo.learning_outcome_id')
            ->where('curriculum_id', '=', $curriculum_id)
            ->where('content_has_lo.content_id', '=', $this->id)
            ->order_by('curriculum_has_lo.order')
            ->execute()
            ->as_array();

        $content_los = [];
        foreach ($content_has_los as $content_has_lo) {
            $content_los[$content_has_lo['order']] = ORM::factory('Course_LearningOutcome')->where('id', '=', $content_has_lo['learning_outcome_id'])->find_undeleted();
        }

        return $content_los;
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'id',
            'name',
            'date_created',
            'date_modified',
            null, // actions
        ];

        $results = $this
            ->where('parent_id', 'is', null)
            ->apply_filters($filters)
            ->apply_datatable_args($datatable_args, $column_definitions)
            ->find_all_undeleted();

        $datatable_args['unlimited'] = true;
        $all_results = $this
            ->where('parent_id', 'is', null)
            ->apply_filters($filters)
            ->apply_datatable_args($datatable_args, $column_definitions)
            ->find_all_undeleted();

        $rows = [];
        foreach ($results as $result) {
            $rows[] = [
                $result->id,
                htmlspecialchars($result->name),
                IbHelpers::relative_time_with_tooltip($result->date_created),
                IbHelpers::relative_time_with_tooltip($result->date_modified),
                '<a class="edit-link" href="/admin/content/edit/'.$result->id.'">Edit</a>'
            ];
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $all_results->count(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public function apply_filters($filters = [])
    {
        if (!empty($filters['start_date'])) {
            $this->where('date_modified', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $this->where('date_modified', '<=', $filters['end_date'].' 23:59:59');
        }

        return $this;
    }
}