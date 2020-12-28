<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 15/12/2014
 * Time: 16:53
 */

interface Interface_IBIS_Accounts
{
    public function set($data);
    public function get($autoload);
    public function validate();
    public function get_instance();
    public static function create($id);
    public static function factory($id,$data);

}
