<?php

class Model_Navapi_Datechangetrigger extends Model_Automations_Trigger
{
    const NAME = 'a Navision event date changed';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('schedule_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;

        $this->generated_message_params = array(
            '@scheduleid@',
            '@oldeventdate@',
            '@neweventdate@',
            '@schedule@',
            '@course@'
        );
    }

    public function filter($data, $sequence)
    {
        $schedule = DB::select(array('schedules.name', 'schedule'), array('courses.title', 'course'))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('schedules.course_id', '=', 'courses.id')
            ->where('schedules.id', '=', $data['scheduleid'])
            ->execute()
            ->current();
        return array(
            array_merge($data, $schedule)
        );
    }
}
