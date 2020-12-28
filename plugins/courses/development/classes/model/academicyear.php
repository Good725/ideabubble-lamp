<?php defined('SYSPATH') or die('No direct script access.');

class Model_AcademicYear extends ORM {
    const TABLE_ACADEMICYEARS = 'plugin_courses_academic_year';

    protected $_table_name = 'plugin_courses_academic_year';

    public static function get_all()
    {
        $academics = DB::select('id', 'title', 'start_date', 'end_date', 'status', 'publish')
            ->from('plugin_courses_academic_year')
            ->where('deleted', '=', 0)
            ->order_by('start_date', 'asc')
            ->execute()
            ->as_array();
        return $academics;
    }

    public static function get_all_academic_years($limit, $offset, $sort, $dir, $search = false)
    {
        $_limit = ($limit > -1) ? $offset . ',' . $limit : '';
        $academics = DB::select()->from('plugin_courses_academic_year')->where('deleted','=',0);
        $_search = '';
        if ($search)
        {
            $academics->and_where('title','LIKE','%'.$search.'%')->or_where('id','LIKE','%'.$search.'%');
        }
        $academics = $academics->order_by($sort,$dir);
		if ($limit > -1) $academics->limit($_limit);
		$academics = $academics->execute()->as_array();
        $return = array();
        if ($academics)
        {
            foreach ($academics as $key=>$academic)
            {
                $return[$key]['id'] = '<a href="/admin/courses/add_edit_academic_year/' . $academic['id'] . '">' . $academic['id'] . '</a>';
                $return[$key]['title'] = '<a href="/admin/courses/add_edit_academic_year/' . $academic['id'] . '">' . $academic['title'] . '</a>';
                $return[$key]['start_date'] = '<a href="/admin/courses/add_edit_academic_year/' . $academic['id'] . '">' . date('d M Y',strtotime($academic['start_date'])) . '</a>';
                $return[$key]['end_date'] = '<a href="/admin/courses/add_edit_academic_year/' . $academic['id'] . '">' . date('d M Y',strtotime($academic['end_date'])) . '</a>';
                $return[$key]['edit'] = '<a href="/admin/courses/add_edit_academic_year/' . $academic['id'] . '">Edit</a>';
                $return[$key]['updated_on'] = '<a href="/admin/courses/add_edit_academic_year/' . $academic['id'] . '">' . $academic['updated_on'] . '</a>';
                $return[$key]['delete'] = '<a href="#" class="delete" data-id="' . $academic['id'] . '">Delete</a>';

                if($academic['status'] == 1)
                {
                    $return[$key]['status'] = '<a href="#" class="status" data-status="1" data-id="' . $academic['id'] . '">Active</a>';
                }
                else
                {
                    $return[$key]['status'] = '<a href="#" class="status" data-status="0" data-id="' . $academic['id'] . '">Pending</a>';
                }

                if ($academic['publish'] == '1')
                {
                    $return[$key]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $academic['id'] . '"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$key]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $academic['id'] . '"><i class="icon-ban-circle"></i></a>';
                }
            }
        }
        return $return;
    }

    public static function get_academic_years_options($publish = FALSE )
    {
        $result = '';
        $academic_years = DB::select()->from('plugin_courses_academic_year')->where('deleted','=',0);
        if ($publish)
        {
            $academic_years->where('publish','=',1);
        }
        $academic_years = $academic_years->execute()->as_array();
        if ($academic_years)
        {
            foreach($academic_years as $key=>$year)
            {
                $academic_years[$key]['value'] =  $year['title'] .' - ' . date('d M y',strtotime($year['start_date'])) .' - ' . date('d M y',strtotime($year['end_date'])) .' - ' ;
                $academic_years[$key]['value'] .= $year['status'] == 0 ? 'Pending - ' : 'Active - ';
                $academic_years[$key]['value'] .= $year['publish'] == 1 ? 'Published' : 'Archived';
            }
        }
        return $academic_years;
    }

    public static function autocomplete_search_academicyears($term = null)
    {
        $select = DB::select(
            DB::expr("DISTINCT academicyears.id as value"),
            array('academicyears.title', 'label')
        )
            ->from(array('TABLE_ACADEMICYEARS', 'academicyears'));
        if ($term != '') {
            $select->where('title', 'like', '%' . $term . '%');
        }

        return $select->execute()->as_array();
    }
}