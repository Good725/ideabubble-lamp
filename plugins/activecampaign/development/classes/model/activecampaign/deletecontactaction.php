<?php

class Model_Activecampaign_DeleteContactAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Active Campaign Delete Contact';
        $this->purpose = Model_Automations::PURPOSE_DELETE;
        $this->params = array('contact_id');
    }

    public function run($params = array())
    {
        if (Settings::instance()->get('activecampaign_sync_on') == 1) {
            $ac = new Model_Activecampaign();
            $ac->delete_contact($params['contact_id']);
        }
    }
}