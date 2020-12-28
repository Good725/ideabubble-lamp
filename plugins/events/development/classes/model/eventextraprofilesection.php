<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Eventextraprofilesection
{
    public $name = 'events';

    public function getData($userId)
    {
        return array(
            'account' => Model_Event::accountDetailsLoad($userId),
            'checkoutDetails' => Model_Event::checkoutDetailsLoad($userId)
        );
    }

    public function save($userId, $post)
    {
        $account = Model_Event::accountDetailsLoad($userId);
		if ($account)
		{
			if (empty($account['owner_id'])) $account['owner_id'] = $userId;
			$account['notify_sms_on_buy_ticket'] = @$post['notify_sms_on_buy_ticket'] ? @$post['notify_sms_on_buy_ticket'] : 0;
			$account['notify_email_on_buy_ticket'] = @$post['notify_email_on_buy_ticket'] ? @$post['notify_email_on_buy_ticket'] : 0;
			$account['notify_email_on_event_enquiry'] = @$post['notify_email_on_event_enquiry'] ? @$post['notify_email_on_event_enquiry'] : 0;
            if (array_key_exists('use_stripe_connect', $post)) {
                $account['use_stripe_connect'] = @$post['use_stripe_connect'] ? @$post['use_stripe_connect'] : 0;
            }
            if (array_key_exists('iban', $post)) {
                $account['iban'] = @$post['iban'];
            }
            if (array_key_exists('bic', $post)) {
                $account['bic'] = @$post['bic'];
            }
            if (array_key_exists('qr_scan_mode', $post)) {
                $account['qr_scan_mode'] = @$post['qr_scan_mode'];
            }
			Model_Event::accountDetailsSave($account);
		}

        if(!empty($post['checkout'])) {
            Model_Event::checkoutDetailsSave($account['owner_id'], $post['checkout']);
        }
    }

    public function getView()
    {
        return 'admin/event_account_details';
    }
}