<?php defined('SYSPATH') or die('No direct script access.');

class Debug extends Kohana_Debug
{

    //ugly workaround for phpdocx pro debug
    public static function getInstance()
    {
        return new Debug();
    }
}
