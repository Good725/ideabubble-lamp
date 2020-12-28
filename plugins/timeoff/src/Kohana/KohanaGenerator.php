<?php


namespace Ideabubble\Timeoff\Kohana;

use DB;
use Ideabubble\Timeoff\Generator;

class KohanaGenerator implements Generator
{
    public function nextId()
    {
        list($insertId, $affectedRows) = DB::insert('plugin_timeoff_generator', ['id'])
            ->values([null])->execute();
        return $insertId;
    }
    
}