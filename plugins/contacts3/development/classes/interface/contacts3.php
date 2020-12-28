<?php
interface Interface_Contacts3
{
    public function load($data);
    public function get($autoload = FALSE);
    public function save();
    public function get_instance();
    public function delete();
    public function validate();
}
?>