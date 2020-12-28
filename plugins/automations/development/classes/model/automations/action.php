<?php
class Model_Automations_Action
{
    public $name = null;
    public $params = array();
    public $purpose = null;
    public $message = null;

    public function __construct()
    {

    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_params()
    {
        return $this->params;
    }

    public function get_purpose()
    {
        return $this->purpose;
    }

    public function run($params = array())
    {
        throw new Exception("Not implemented");
    }
}