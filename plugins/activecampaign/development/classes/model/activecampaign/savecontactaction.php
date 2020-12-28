<?php

class Model_Activecampaign_SaveContactAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Active Campaign Save Contact';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('contact_id');
    }

    public function run($params = array())
    {
        if (Settings::instance()->get('activecampaign_sync_on') == 1) {
            $ac = new Model_Activecampaign();
            $ac->save_contact($params['contact_id'], @$params['tags'], @$params['fields']);
        }
    }
}