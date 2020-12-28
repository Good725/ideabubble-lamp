<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Styleguide extends Controller_Head
{
    public function action_index()
    {
        $this->template->body = View::factory('prototypes/styleguide');
    }
}