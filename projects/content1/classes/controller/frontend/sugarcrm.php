<?php defined('SYSPATH') OR die('No Direct Script Access');

final class Controller_Frontend_Sugarcrm extends Controller
{
    public function action_parserestcall()
    {
        $this->auto_render = false;

        $html = View::factory('templates/wide_banner/sugarcrm/ParseRestCall')->render();
        $this->response->body($html);

    }

}
