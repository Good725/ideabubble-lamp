<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Admin_Currency extends Controller_Head
{
    public function action_index()
    {
        $currencies = Model_Currency::getCurrencies();
        $rates = Model_Currency::getRates();

        $this->template->body = View::factory('list_currencies');
        $this->template->body->rates = $rates;
        $this->template->body->currencies = $currencies;
    }


}