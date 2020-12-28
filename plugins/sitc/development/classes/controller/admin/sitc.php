<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_SITC extends Controller_Cms
{
    public function action_index()
    {
        $this->notifyPrint('moved to cron!');
    }

    public function action_update_categories()
    {
        $this->notifyPrint('plugin_products_category table is updating...');
        Model_SITC::update_sitc_categories();
        $newNotify = $this->notifyToSend('plugin_products_category table has updated.');
        $this->request->redirect(URL::base().'admin/sitc/update_product'.$newNotify);
    }

    public function action_update_product()
    {
        $this->notifyPrint('plugin_sict_product table is updating...');
        Model_SITC::update_sitc_product();
        $newNotify = $this->notifyToSend('plugin_sict_product table has updated.');
        $this->request->redirect(URL::base().'admin/sitc/update_stock_and_price'.$newNotify);
    }

    public function action_update_stock_and_price()
    {
        $this->notifyPrint('plugin_sict_stock_and_price table is updating...');
        Model_SITC::update_sitc_stock_and_price();
        $newNotify = $this->notifyToSend('plugin_sict_stock_and_price table has updated.');
        $this->request->redirect(URL::base().'admin/sitc/update_sict_distributors'.$newNotify);
    }

    public function action_update_sict_distributors()
    {
        $this->notifyPrint('plugin_sict_distributors table is updating...');
        Model_SITC::update_sitc_distributors();
        $newNotify = $this->notifyToSend('plugin_sict_distributors table has updated.');
        $this->request->redirect(URL::base().'admin/sitc/update_sict_manufacturer'.$newNotify);
    }

    public function action_update_sict_manufacturer()
    {
        $this->notifyPrint('plugin_sict_manufacturer table is updating...');
        Model_SITC::update_sitc_manufacturer();
        $newNotify = $this->notifyToSend('plugin_sict_manufacturer table has updated.');
        $this->request->redirect(URL::base().'admin/sitc/update_products_product'.$newNotify);
    }

    public function action_update_products_product()
    {
        set_time_limit(300);
        $this->notifyPrint('plugin_products_product table is updating...');
        Model_SITC::transfer_to_product_table();
        $newNotify = $this->notifyToSend('plugin_products_product table has updated.');
        $this->request->redirect(URL::base().'admin/sitc/done'.$newNotify);
    }

    public function action_done()
    {
        $this->notifyPrint('All actions have done!');
    }

    private function notifyPrint($current_action)
    {
        foreach($_GET as $doneNotify => $index){
            print_r($doneNotify.'<br>');
        }
        print_r($current_action);
    }

    private function notifyToSend($newNotify)
    {
        $notify = '?';
        foreach($_GET as $doneNotify => $index){
            $notify .= $doneNotify.'&';
        }
        $notify .= $newNotify;
        return $notify;
    }

    public function action_cron()
    {
         $this->notifyPrint('moved to /frontend/sitc/cron');
    }
}