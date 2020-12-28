<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Frontend_Navapi extends Controller
{
    public function action_cron_send_bookings()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->response->headers('Content-type', 'text/plain');
        try {
            $na = new Model_NAVAPI();
            $na->event_sync();
            $na->sync_bookings();
            $na->sync_transactions();
            $na->sync_payments();
            $na->sync_transaction_pdfs();
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}