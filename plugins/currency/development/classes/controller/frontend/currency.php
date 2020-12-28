<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Currency extends Controller_Template
{
    public function action_cron()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'text/plain; charset=utf-8');
        Model_Currency::updateRatesFromXE();
        echo "Currency rates updated:\n";
        foreach(Model_Currency::getRates() as $currency => $rate){
            echo "\t" . $currency . ":" . $rate . "\n";
        }
    }


    public function action_set_preferred_currency()
    {
        $this->auto_render = false;
        Model_Currency::setPreferredCurrency($this->request->post('currency'));
    }
}