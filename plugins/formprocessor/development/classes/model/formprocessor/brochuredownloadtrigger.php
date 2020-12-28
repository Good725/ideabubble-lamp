<?php

class Model_Formprocessor_Brochuredownloadtrigger extends Model_Automations_Trigger
{
    const NAME = 'Course Brochure Download';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
