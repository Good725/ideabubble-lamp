<?php

class Model_Remoteaccounting_ContactDeleteAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Remote Accounting Delete Contact';
        $this->purpose = Model_Automations::PURPOSE_DELETE;
        $this->params = array('contact_id');
    }

    public function run($params = array())
    {
        if (Settings::instance()->get('remoteaccounting_api') != '') {
            $ac = new Model_Remoteaccounting();
            $ac->delete_contact($params);
        }
    }
}