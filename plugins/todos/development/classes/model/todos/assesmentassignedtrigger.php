<?php

class Model_Todos_AssesmentAssignedTrigger extends Model_Todos_TaskAssignedTrigger
{
    const NAME = 'an Exam is assigned';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;

        $this->generated_message_params = array(
            '@id@',
            '@title@',
            '@type@',
            '@mode@',
            '@status@',
            '@priority@',
            '@summary@',
            '@gradingtype@',
            '@examstartdate@',
            '@examenddate@',
            '@examsstarttime@',
            '@examendtime@',
            '@duedate@',
            '@location@',
            '@locationcounty@',
            '@assigneeid@',
            '@assigneename@',
            '@assigneemobile@',
            '@assigneeemail@',
            '@scheduleid@',
            '@schedule@',
            '@courseid@',
            '@course@',
            '@category@',
            '@subject@',
            '@trainerid@',
            '@trainername@',
            '@trainerfirstname@',
            '@trainerlastname@',
            '@traineremail@',
            '@trainermobile@'
        );
    }
}