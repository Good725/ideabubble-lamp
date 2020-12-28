<?php

class Model_Remoteaccounting_ContactSaveAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Remote Accounting Save Contact';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('contact_id');
    }

    public function run($params = array())
    {
        try {
            if (!isset($params['contact_id'])) {
                return;
            }
            if (Settings::instance()->get('remoteaccounting_api') != '') {
                $ac = new Model_Remoteaccounting();
                $c3 = new Model_Contacts3($params['contact_id']);
                $contact = $c3->get_instance();
                $contact['email'] = $c3->get_email();
                $contact['mobile'] = $c3->get_mobile();
                $contact['phone'] = $c3->get_mobile();
                $ac->save_contact($contact);
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}