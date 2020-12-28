<?php
class Model_Api extends Model
{
    const TABLE = 'engine_api_plugins';

    public static function is_enabled($plugin)
    {
        $enabled = DB::select('enabled')
            ->from(self::TABLE)
            ->where('plugin', '=', $plugin)
            ->execute()
            ->get('enabled');
        return $enabled;
    }
}