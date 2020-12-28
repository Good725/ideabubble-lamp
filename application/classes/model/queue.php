<?php
class Model_Queue
{
    const TABLE = 'engine_queue';

    public static function create($object, $created = null, $status = null, $expires = null)
    {
        if (!$created) {
            $created = date::now();
        }

        if (!$status) {
            $status = 'WAIT';
        }

        if (!$expires) {
            $expires = date('Y-m-d H:i:s', time() + 600);
        }
        $data = array();
        $data['object'] = $object;
        $data['created'] = $created;
        $data['status'] = $status;
        $data['expires'] = $expires;

        $inserted = DB::insert(self::TABLE)->values($data)->execute();
        return $inserted[0];
    }

    public static function set_status($id, $status)
    {
        DB::update(self::TABLE)
            ->set(
                array('status' => $status, 'processed' => date::now())
            )
            ->where('id', '=', $id)
            ->execute();
    }

    public static function set_expired($id = null)
    {
        $q = DB::update(self::TABLE)
            ->set(array('status' => 'EXPIRED'))
            ->where('expires', '<=', date::now())
            ->and_where('status', '=', 'WAIT');
        if ($id) {
            $q->and_where('id', '=', $id);
        }
        $q->execute();
    }

    public static function get_wait_list($object = null)
    {
        $q = DB::select('*')
            ->from(self::TABLE)
            ->where('status', '=', 'WAIT');

        if ($object) {
            $q->and_where('object', '=', $object);
        }

        $list = $q->execute()->as_array();
        return $list;
    }

    public static function get_wait_count($object = null, $id = null)
    {
        self::set_expired();
        $wait = DB::select(DB::expr("count(*) as cnt"))
            ->from(self::TABLE)
            ->where('id', '<', $id)
            ->execute()
            ->current();

        if ($wait == null || @$wait['status'] == 'EXPIRED') {
            return 0;
        }

        $q = DB::select(DB::expr("count(*) as cnt"))
            ->from(self::TABLE)
            ->where('status', '=', 'WAIT');

        if ($object) {
            $q->and_where('object', '=', $object);
        }

        if ($id) {
            $q->and_where('id', '<=', $id);
            DB::update(self::TABLE)
                ->set(array('pinged' => date::now()))
                ->where('id', '=', $id)
                ->execute();
        }

        $cnt = $q->execute()->get('cnt');
        return $cnt;
    }
}
