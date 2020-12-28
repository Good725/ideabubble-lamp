<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class name in 1 word CalendarEvent will look in the model class folder
 * Class Name with underscore Calendar_Event Need to be in subfolder calendar
 */
class Model_OurTemplate extends ORM {
    protected $_table_name = 'TABLE_NAME';

    /**
     * @param      $limit
     * @param      $offset
     * @param      $sort
     * @param      $dir
     * @param bool $search
     * @return array
     */
    public function get_all_($limit, $offset, $sort, $dir, $search = false)
    {
        $_limit =  $offset . ',' . $limit;
        $items = DB::select()->from($this->_table_name)->where('deleted','=',0);
        $_search = '';

        // Enter search Criteria for the table
        if ($search)
        {
            $items->and_where('','LIKE','%'.$search.'%')->or_where('id','LIKE','%'.$search.'%');
        }
        $items = $items->order_by($sort,$dir)->limit($_limit)->execute()->as_array();

        // Build the table data
        $return = array();
        if ($items)
        {
            foreach ($items as $key=>$item)
            {
                $return[$key]['id'] = '<a href="/admin/____/add_edit_/' . $item['id'] . '">' . $item['id'] . '</a>';
                $return[$key]['edit'] = '<a href="/admin/____/add_edit_/' . $item['id'] . '" class="icon-pencil"></a>';

                // Delete and publish
                $return[$key]['delete'] = '<a href="#" class="delete" data-id="' . $item['id'] . '"><i class="icon-remove"></a>';
                if ($item['publish'] == '1')
                {
                    $return[$key]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $item['id'] . '"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$key]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $item['id'] . '"><i class="icon-ban-circle"></i></a>';
                }
            }
        }
        return $return;
    }
}