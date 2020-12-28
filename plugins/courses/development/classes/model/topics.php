<?php defined('SYSPATH') or die('No direct script access.');

class Model_Topics extends Model
{
    const TABLE_TOPICS = 'plugin_courses_topics';

    private static $model_topic_table = 'plugin_courses_topics';

    public static function get_all_topics()
    {
        $query =  DB::select()
            ->from(self::$model_topic_table)
            ->where('deleted', '=', 0)
            ->order_by('name', 'ASC')
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_topic_by_id($topic_id = NULL)
    {
        $query =  DB::select()
            ->from(self::$model_topic_table)
            ->where('id', '=', $topic_id)
            ->execute()
            ->as_array();
        return $query[0];
    }

    public static function count_topics($args = array())
    {
        $search      = isset($args['search'])      ? $args['search']      : false;
        $course_id   = isset($args['course_id'])   ? $args['course_id']   : null;
        $schedule_id = isset($args['schedule_id']) ? $args['schedule_id'] : null;

        $q = DB::select(array(DB::expr("COUNT(*)"), 'count'))
            ->from(array('plugin_courses_topics', 'topic'))
            ->where('topic.deleted', '=', 0);

        if ( ! is_null($course_id)) {
            $q
                ->join(array('plugin_courses_courses_has_topics', 'cht'))->on('cht.topic_id',  '=', 'topic.id')
                ->join(array('plugin_courses_courses',         'course'))->on('cht.course_id', '=', 'course.id')
                ->where('course.id',   '=', $course_id)
                ->where('cht.deleted', '=', 0);
        }
        if ( ! is_null($schedule_id)) {
            $q
                ->join(array('plugin_courses_schedules_have_topics', 'sht'))->on('sht.topic_id' ,   '=', 'topic.id')
                ->join(array('plugin_courses_schedules',        'schedule'))->on('sht.schedule_id', '=', 'schedule.id')
                ->where('schedule.id', '=', $schedule_id)
                ->where('sht.deleted', '=', 0);
        }

        if ( ! empty($search)) {
            $q
                ->and_where_open()
                    ->where('topic.description', 'like', '%'.$search.'%')
                    ->or_where('topic.name',     'like', '%'.$search.'%')
                ->and_where_close();
        }

        return $q->execute()->get('count');
    }

    public static function get_topics($args = array())
    {
        $limit       = isset($args['limit'])       ? $args['limit']       : '-1';
        $offset      = isset($args['offset'])      ? $args['offset']      : 0;
        $sort        = isset($args['sort'])        ? $args['sort']        : null;
        $dir         = isset($args['dir'])         ? $args['dir']         : 'asc';
        $search      = isset($args['search'])      ? $args['search']      : null;
        $course_id   = isset($args['course_id'])   ? $args['course_id']   : null;
        $schedule_id = isset($args['schedule_id']) ? $args['schedule_id'] : null;
        $editable    = isset($args['editable'])    ? $args['editable']    : true;
        $format      = isset($args['format'])      ? $args['format']      : 'data';

        $q = DB::select('topic.id', 'topic.name', 'topic.description')
            ->distinct(true)
            ->from(array('plugin_courses_topics', 'topic'))
            ->where('topic.deleted', '=', 0);

        if ( ! is_null($course_id)) {
            $q
                ->join(array('plugin_courses_courses_has_topics', 'cht'))->on('cht.topic_id',  '=', 'topic.id')
                ->join(array('plugin_courses_courses',         'course'))->on('cht.course_id', '=', 'course.id')
                ->where('course.id',   '=', $course_id)
                ->where('cht.deleted', '=', 0);
        }
        if ( ! is_null($schedule_id)) {
            $q
                ->join(array('plugin_courses_schedules_have_topics', 'sht'))->on('sht.topic_id',    '=', 'topic.id')
                ->join(array('plugin_courses_schedules',        'schedule'))->on('sht.schedule_id', '=', 'schedule.id')
                ->where('schedule.id', '=', $schedule_id)
                ->where('sht.deleted', '=', 0);
        }

        if ( ! empty($search)) {
            $q
                ->and_where_open()
                    ->where('topic.description', 'like', '%'.$search.'%')
                    ->or_where('topic.name',     'like', '%'.$search.'%')
                ->and_where_close();
        }

        if ($sort) {
            $q->order_by($sort, $dir);
        }
        if ($limit && $limit != -1) {
            $q->limit($limit, $offset);
        }

        $topics = $q->execute()->as_array();

        $return = array();
        if ($format == 'datatable')
        {
            if ($editable)
            {
                foreach ($topics as $i => $sub)
                {
                    $return[$i]['name'] = '<label class="topic_title" data-id="'.$sub['id'].'" style="width:100%">'.$sub['name'].'</label>';
                    $return[$i]['description'] = '<label class="tooltip-txt">'.$sub['description'].'</label>';
                    $return[$i]['action'] = '
                                <div class="action-btn">
                                    <a href="#" class=""><span class="icon icon-ellipsis-h" aria-hidden="true"></span></a>
                                    <ul>
                                        <li><a class="action_edit_topic" data-toggle="modal" data-id="'.$sub['id'] .'"  href="#">Edit</a></li>
                                        <li><a class="action_duplicate_topic" data-toggle="modal" data-id="'.$sub['id'] .'"  href="#">Duplicate</a></li>
                                        <li><a data-toggle="modal" data-id="'.$sub['id'] .'"  href="#">Delete</a></li>
                                    </ul>
                                </div> ';
                }
            }
            else
            {
                foreach ($topics as $i => $topic)
                {
                    $return[$i]['name'] = $topic['name'];
                    $return[$i]['description'] = $topic['description'];
                    $return[$i]['action'] = '<a href="#" class="delete_course_topic delete" data-id="'.$topic['id'].'">Remove</a>';
                }
            }
        }
        else
        {
            $return = $topics;
        }

        return $return;
    }

    public static function update_topic($id,$name,$description)
    {
//        $logged_in_user = Auth::instance()->get_user();

        $ret = DB::update(self::$model_topic_table)
            ->set(array(
                'name' => ':name',
                'description' => ':description'
            ))
            ->where('id', '=',':id')
            ->parameters(array(
                ':id' => $id,
                ':name' => $name,
                ':description' => $description
            ))
            ->execute();

        if ($ret >= 0)
        {
            $response['message'] = 'success';
            $response['redirect'] = '/admin/courses/topics';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }

        return $response;
    }

    public static function save_topic($data)
    {
        // add / update
        $save_action = 'add';
        $item_id = 0;
        unset($data['redirect']);
        //Add the necessary values to the $data array for update
//        $logged_in_user = Auth::instance()->get_user();

//            $data['created_by'] = $logged_in_user['id'];
//            $data['date_created'] = date('Y-m-d H:i:s');
            $data['deleted'] = 0;
            $query = DB::insert('plugin_courses_topics', array_keys($data))
                ->values($data)
                ->execute();

            $save_action = 'add';
            $item_id = (isset($query[0]) AND $query[0] > 0)? $query[0] : 0;


        // Set Successful / Not Successful Insert / Update Message
        if(
            ($save_action == 'add' AND $query[0] > 0)
        )
        {
            IbHelpers::set_message (
                'Topic ID #'.$item_id.':  "'.$data['name'].'" has been CREATED.',
                'success popup_box'
            );
        }
        else
        {
            IbHelpers::set_message (
                'Sorry! There was a problem with '.(($save_action == 'add')? 'CREATION' : 'UPDATE' )
                .' of '.( ($item_id > 0)? 'Topic ID #'.$item_id : 'Topic' ).': "'.$data['name'].'".<br />'
                .'Please make sure, that form is filled properly and Try Again!',
                'error popup_box'
            );
        }

        return $item_id;
    }

    public static function remove_topic($id)
    {
        DB::update('plugin_courses_courses_has_topics')
            ->set(array('plugin_courses_courses_has_topics.deleted' => 1))
            ->where('plugin_courses_courses_has_topics.topic_id', '=', $id)
            ->execute();

        $ret = DB::update('plugin_courses_topics')
            ->set(array('deleted' => 1))
            ->where('id', '=', $id)
            ->execute();
        if ($ret > 0)
        {
            $response['message'] = 'success';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }
        return $response;
    }

    public static function autocomplete_topics($term = null)
    {
        $select = DB::select(
            DB::expr("DISTINCT topics.id as value"),
            array('topics.name', 'label')
        )
            ->from(array(self::TABLE_TOPICS, 'topics'));
        if ($term != '') {
            $select->where('topics.name', 'like', '%' . $term . '%');
        }

        return $select->execute()->as_array();
    }
}
