<?php
class DonationsInSMSPostProcessor
{
    public function process($message)
    {
        try {
            Database::instance()->begin();
            $donation = array();
            $donation['created'] = date::now();
            $donation['updated'] = date::now();

            $smsfrom = $message['sender'];
            $contacts = Model_Contacts::search(array('mobile' => $smsfrom));
            if (count($contacts) > 0) {
                $contact_id = $contacts[0]['id'];
            } else {
                $contact = new Model_Contacts();
                $contact->set_first_name('');
                $contact->set_last_name('');
                $contact->test_existing_email = false;
                $contact->set_mobile($smsfrom);
                $contact->set_email('');
                $contact->set_mailing_list('Recipient');
                $contact->save();
                $contact_id = $contact->get_id();
            }

            $donation['contact_id'] = $contact_id;
            $donation['message_id'] = $message['id'];

            if (preg_match('/(\d+)/', $message['message'], $match)) {
                $product_id = $match[1];
                $product = Model_Donations::product_details($product_id);

                if ($product && (@$product['status'] == 'Active') && (@$product['deleted'] == '0')) {
                    $donation['product_id'] = $product['id'];
                }
            }

            $unavailability = Model_Messaging::get_unavailable(null);
            $unavailable = false;
            if ($unavailability) {
                $unavailable = true;
                if ($unavailability['from_date'] != '' && strtotime($unavailability['from_date']) > time()) {
                    $unavailable = false;
                }
                if ($unavailability['to_date'] != '' && strtotime($unavailability['to_date']) < time()) {
                    $unavailable = false;
                }
            }

            if (@$unavailability['id'] && $unavailable) {
                $donation['status'] = 'Offline';
            } else {
                if (!$donation['product_id']) {
                    $donation['note'] = 'Invalid code';
                    $donation['status'] = 'Rejected';
                } else {
                    $donation['status'] = 'Processing';
                }
            }

            DB::insert(Model_Donations::TABLE_DONATIONS)
                ->values($donation)
                ->execute();
            Database::instance()->commit();

            $mm = new Model_Messaging();
            $recipients = array(array('target_type' => 'CMS_CONTACT', 'target' => $contact_id));
            if ($donation['status'] == 'Processing') {
                $mm->send_template('donation-sms-received-reply', null, null, $recipients);
            } else if ($donation['status'] == 'Offline' && @$unavailability['auto_reply']) {
                $mm->send_template('donation-sms-received-reply', $unavailability['reply_message'], null, $recipients);
            } else {
                //$mm->send_template('donation-sms-received-invalid-reply', null, date::now(), $recipients, array('code' => $message['message']));
            }
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}