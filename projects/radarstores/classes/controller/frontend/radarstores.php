<?php defined('SYSPATH') OR die('No Direct Script Access');

final class Controller_Frontend_RadarStores extends Controller
{
    /**
     *
     */
    public function action_redirect_to_checkout()
    {
        $last_product  = preg_replace('/'.preg_quote(URL::base(), '/').'/', '/', $this->request->query('last_product'));
        $last_category = preg_replace('/(\/[^\/]+)$/', '', $last_product);

        Session::instance()->set('last_category', $last_category);

        $this->request->redirect('checkout.html');
    }
}
