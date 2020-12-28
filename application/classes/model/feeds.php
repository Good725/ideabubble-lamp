<?php defined('SYSPATH') or die('No direct script access.');

class Model_Feeds extends Model
{
    const MAIN_TABLE = 'engine_feeds';

    public function get_feed_data($id = NULL)
    {
        $feeds = DB::select(
            array(self::MAIN_TABLE.'.id', 'id'),
            array(self::MAIN_TABLE.'.name', 'name'),
            array(self::MAIN_TABLE.'.short_tag', 'short_tag'),
            array(self::MAIN_TABLE.'.function_call', 'function_call'),
            array(self::MAIN_TABLE.'.code_path', 'code_path'),
            array(self::MAIN_TABLE.'.order', 'order'),
            array(self::MAIN_TABLE.'.summary', 'summary'),
            array(self::MAIN_TABLE.'.content', 'content'),
            array(self::MAIN_TABLE.'.publish', 'publish'),
            array('plugin_id', 'plugin_id'),
            array('engine_plugins.name', 'plugin')
        )
            ->from(self::MAIN_TABLE)
            ->join('plugin_feeds', 'left')
            ->on(self::MAIN_TABLE.'.id', '=', 'plugin_feeds.feed_id')
            ->join('engine_plugins', 'left')
            ->on('engine_plugins.id', '=', 'plugin_feeds.plugin_id')
            ->where(self::MAIN_TABLE.'.deleted', '=', '0')
            ->distinct(TRUE);

        if ( ! is_null($id))
        {
            $feeds = $feeds->where(self::MAIN_TABLE.'.id', '=', $id);
        }
        $feeds = $feeds->order_by(self::MAIN_TABLE.'.date_modified', 'desc')->execute();

        return ($id == NULL) ? $feeds : $feeds[0];
    }

    public function change_publish_status($id)
    {
        $query = DB::select()->from(self::MAIN_TABLE)->where('id', '=', $id)->execute()->as_array();
        if (empty($query))
        {
            $str_res = '<strong>Error: </strong> The selected feed does not exist';
        }
        else
        {
            $str_res = $query[0]['publish'];
            if ($str_res == 0)
            {
                $total_rows = DB::update(self::MAIN_TABLE)->set(array('publish' => '1'))->where('id', '=', $id)->limit(1)->execute();
            }
            else
            {
                $total_rows = DB::update(self::MAIN_TABLE)->set(array('publish' => '0'))->where('id', '=', $id)->limit(1)->execute();
            }
            if ($total_rows > 0)
            {
                $str_res = 'success';
            }
            else
            {
                $str_res = '<strong>Error: </strong> Cannot update the database';
            }
        }

        return $str_res;
    }

    public function add_feed($data)
    {
        $plugin_id = $data['plugin'];

        //unset, so this doesn't get inserted into the first table
        unset($data['plugin']);

        $logged_in_user = Auth::instance()->get_user();
        $data['modified_by'] = $logged_in_user['id'];
        $data['created_by'] = $data['modified_by'];
        $data['date_modified'] = date('Y-m-d H:i:s');
        $data['date_created'] = $data['date_modified'];
        $data['deleted'] = 0;

        list($feed_id, $affected_rows) = DB::insert(self::MAIN_TABLE, array_keys($data))
            ->values($data)
            ->execute();

        $insert = DB::insert('plugin_feeds', array('plugin_id', 'feed_id'))
            ->values(array($plugin_id, $feed_id));

        if ($insert->execute())
            return TRUE;
        else
            return FALSE;


    }

    public function edit_feed($data)
    {
        $plugin_id = $data['plugin'];
        unset($data['plugin']);

        $logged_in_user = Auth::instance()->get_user();
        $data['modified_by'] = $logged_in_user['id'];
        $data['date_modified'] = date('Y-m-d H:i:s');

        $update_feeds = DB::update(self::MAIN_TABLE)->set(
            array(
                'name' => $data['name'],
                'short_tag' => $data['short_tag'],
                'function_call' => $data['function_call'],
                'code_path' => $data['code_path'],
                'order' => $data['order'],
                'summary' => $data['summary'],
                'content' => $data['content'],
                'publish' => $data['publish'],
                'date_modified' => $data['date_modified'],
                'modified_by' => $data['modified_by']
            )
        )->where('id', '=', $data['id']);

        if (is_numeric($update_feeds->execute()))
        {
            // This might need to be changed to accommodate a many-to-many relationship
            $update_plugin_feeds = DB::update('plugin_feeds')->set(
                array('plugin_id' => $plugin_id)
            )->where('feed_id', '=', $data['id']);

            if (is_numeric($update_plugin_feeds->execute()))
                return TRUE;
            else
                return FALSE;
        }
        else
            return FALSE;

    }

    public function delete_feed($id)
    {
        $query = DB::select()->from(self::MAIN_TABLE)->where('id', '=', $id)->execute()->as_array();
        if (empty($query))
        {
            $str_res = '<strong>Error: </strong> The selected feed does not exist';
        }
        else
        {
            $str_res = $query[0]['deleted'];
            if ($str_res == 0)
            {
                $total_rows = DB::update(self::MAIN_TABLE)->set(array('deleted' => 1))->where('id', '=', $id)->limit(1)->execute();

                if ($total_rows > 0)
                {
                    $str_res = 'success';
                }
                else
                {
                    $str_res = '<strong>Error: </strong> Cannot update the database';
                }
            }
            else
            {
                $str_res = '<strong>Error: </strong> The selected feed has already been deleted';
            }
        }

        return $str_res;
    }

    public function display_feed($file_path)
    {
        $code_path = str_replace('.php', '', $file_path);

        if (strpos($file_path, '/projects/')) {
            $code_path = substr($code_path, strpos($code_path, '/projects/'));
        }
        if (strpos($file_path, '/plugins/')) {
            $code_path = substr($code_path, strpos($code_path, '/plugins/'));
        }

        $return = TRUE;

        try {
            $result = DB::select()->from(self::MAIN_TABLE)
                ->where('code_path', 'LIKE', '%'.$code_path)
                ->and_where('publish', '=', '0')
                ->execute()->as_array();

            if (count($result) > 0)
                $return = FALSE;
        }
        catch (Exception $e){
            // this is only temporary
        }
        return $return;

    }
}