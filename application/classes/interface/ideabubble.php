<?php
interface Interface_Ideabubble
{
    public function set($data);
    public function get($autoload);
    public function validate();
    public static function create($id);
}
?>