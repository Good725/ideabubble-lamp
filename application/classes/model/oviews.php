<?php defined('SYSPATH') or die('No direct script access.');

class Model_Oviews extends Model
{
    const VIEWS_TABLE = 'engine_object_views';


    public static function add($type, $object_id, $session_id = null, $user_id = null, $visited = null)
    {
        if ($session_id == null) {
            $session_id = $_COOKIE['session_name'];
        }
        if ($user_id == null) {
            $user = Auth::instance()->get_user();
            $user_id = $user['id'];
        }

        if ($visited == null) {
            $visited = date('Y-m-d H:i:s');
        }

        DB::insert(self::VIEWS_TABLE)
            ->values(
                array(
                    'type' => $type,
                    'object_id' => $object_id,
                    'session_id' => $session_id,
                    'user_id' => $user_id,
                    'visited' => $visited
                )
            )
            ->execute();
    }

    public static function search($params = array())
    {
        if (@$params['distinct']) {
            $select = DB::select('*')
                ->from(self::VIEWS_TABLE);
        } else {
            $select = DB::select('*')
                ->from(self::VIEWS_TABLE);
        }


        if (@$params['type']) {
            $select->and_where('type', '=', $params['type']);
        }
        if (@$params['user_id']) {
            $select->and_where('user_id', '=', $params['user_id']);
        }
        if (@$params['object_id']) {
            $select->and_where('object_id', '=', $params['object_id']);
        }
        if (@$params['session_id']) {
            $select->and_where('session_id', '=', $params['session_id']);
        }

        if (@$params['before']) {
            $select->and_where('visited', '<=', $params['before']);
        }

        if (@$params['after']) {
            $select->and_where('visited', '>=', $params['after']);
        }

        if (@$params['distinct']) {
            $select->group_by('session_id')->group_by('user_id')->group_by('type')->group_by('object_id');
        }

        $select->order_by('visited', 'desc');

        return $select->execute()->as_array();
    }
}
