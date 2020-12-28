<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_SITC extends Controller_Template
{
    public function action_cron()
    {
        $this->auto_render = false;
		header('content-type: text/plain; charset=utf-8');
        try {
            Model_SITC::update_from_ftp();
            if (Settings::instance()->get('sitc_local_image') == '1') {
                Model_SITC::download_sitc_pictures();
            }
            Model_Product::setFeaturedProductsFromAutoFeature();
            echo 'sitc import cron completed';
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

    public function action_fix_url_titles()
    {
        $this->auto_render = false;
        if (Model_SITC::fix_url_titles()) {
            echo "successful";
        } else {
            echo "failed";
        }
    }
}