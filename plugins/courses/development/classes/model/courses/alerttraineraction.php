<?php

class Model_Courses_AlertTrainerAction extends Model_Automations_Action
{
    const NAME = 'Schedule Alert Trainer';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('schedule_id');
    }

    public function run($params = array())
    {
        $date_format = substr(Settings::instance()->get('date_format'), 0, 5);
        if ($date_format == '') {
            $date_format = 'd-m-Y';
        }

        $mm = new Model_Messaging();
        $recipients = array();
        $recipients[] = array(
            'target_type' => 'CMS_CONTACT3',
            'target' => $params['schedule']['trainer_id']
        );
        $c3 = new Model_Contacts3($params['schedule']['trainer_id']);
        $trainers[$params['schedule']['trainer_id']] = $c3->get_first_name() . ' ' . $c3->get_last_name();
        $message_params = array(
            'schedule_id' => $params['schedule_id'],
            'schedule' => $params['schedule']['name'],
            'timeslots' => array('html' => true, 'value' => '<table border="1"><thead><tr><th>Trainer</th><th>Date</th><th>Start</th><th>End</th></tr></thead>')
        );
        $message_params['timeslots']['value'] .= '<tbody>';
        foreach ($params['alert_trainer_timeslots'] as $alert_trainer_timeslot) {

            if (!isset($trainers[$alert_trainer_timeslot['trainer_id']])) {
                $c3 = new Model_Contacts3($alert_trainer_timeslot['trainer_id']);
                $trainers[$alert_trainer_timeslot['trainer_id']] = $c3->get_first_name() . ' ' . $c3->get_last_name();
            }

            $recipients[] = array(
                'target_type' => 'CMS_CONTACT3',
                'target' => $alert_trainer_timeslot['trainer_id']
            );
            $message_params['timeslots']['value'] .= '<tr>' .
                '<td>' . $trainers[$alert_trainer_timeslot['trainer_id']] . '</td>' .
                '<td>' . date($date_format, strtotime($alert_trainer_timeslot['datetime_start'])) . '</td>' .
                '<td>' . date('H:i', strtotime($alert_trainer_timeslot['datetime_start'])) . '</td>' .
                '<td>' . date('H:i', strtotime($alert_trainer_timeslot['datetime_end'])) . '</td>' .
                '</tr>';
        }
        $message_params['timeslots']['value'] .= '</tbody>';
        $message_params['timeslots']['value'] .= '</table>';
        $mm->send_template('courses-schedule-assigned', null, null, $recipients, $message_params);
    }
}