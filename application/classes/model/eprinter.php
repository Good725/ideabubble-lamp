<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Eprinter extends Model
{
    const EPRINTERS_TABLE = 'engine_eprinters';

    public static function add($location, $tray, $email, $published = 1)
    {
        $inserted = DB::insert(self::EPRINTERS_TABLE)
            ->values(
                array(
                    'location' => $location,
                    'tray' => $tray,
                    'email' => $email,
                    'published' => $published
                )
            )
            ->execute();
        return @$inserted[0];
    }

    public static function remove($id)
    {
        DB::update(self::EPRINTERS_TABLE)
            ->set(array('deleted' => 1))
            ->where('id', '=', $id)
            ->execute();
    }

    public static function update($id, $data)
    {
        DB::update(self::EPRINTERS_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
    }

    public static function search($params = array())
    {
        $select = DB::select('*')
            ->from(array(self::EPRINTERS_TABLE, 'eprinters'))
            ->where('eprinters.deleted', '=', 0);
        if (isset($params['published'])){
            $select->and_where('eprinters.published', '=', $params['published']);
        }

        $eprinters = $select->execute()->as_array();
        return $eprinters;
    }
}
