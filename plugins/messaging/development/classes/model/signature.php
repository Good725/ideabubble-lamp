<?php defined('SYSPATH') or die('No direct script access.');

class Model_Signature extends Model
{
    const SIGN_TABLE = 'plugin_messaging_signatures';

    public static function save($params)
    {
        if (!isset($params['id'])) {
            $params['created'] = date::now();
            if (!isset($params['created_by'])) {
                $user = Auth::instance()->get_user();
                $params['created_by'] = $user['id'];
            }
        }
        $params['updated'] = date::now();
        if (!isset($params['updated_by'])) {
            $user = Auth::instance()->get_user();
            $params['updated_by'] = $user['id'];
        }

        if (!isset($params['id'])) {
            $inserted = DB::insert(self::SIGN_TABLE)->values($params)->execute();
            $id = $inserted[0];
        } else {
            DB::update(self::SIGN_TABLE)->set($params)->where('id', '=', $params['id'])->execute();
            $id = $params['id'];
        }

        return $id;
    }

    public static function delete($params)
    {
        if (!isset($params['id'])) {
            $params['created'] = date::now();
            if (!isset($params['created_by'])) {
                $user = Auth::instance()->get_user();
                $params['created_by'] = $user['id'];
            }
        }
        $params['updated'] = date::now();
        if (!isset($params['updated_by'])) {
            $user = Auth::instance()->get_user();
            $params['updated_by'] = $user['id'];
        }
        $params['deleted'] = 1;

        DB::update(self::SIGN_TABLE)->set($params)->where('id', '=', $params['id'])->execute();
    }

    public static function get($sign)
    {
        $selectq = DB::select('*')
            ->from(self::SIGN_TABLE)
            ->where('deleted', '=', 0);

        if (is_numeric($sign)) {
            $selectq->and_where('id', '=', $sign);
        } else {
            $selectq->and_where('title', '=', $sign);
        }
        $signature = $selectq->execute()->current();
        return $signature;
    }

    public static function search($params = array())
    {
        $selectq = DB::select('*')
            ->from(self::SIGN_TABLE)
            ->where('deleted', '=', 0);


        $signatures = $selectq->execute()->as_array();
        return $signatures;
    }
}