<?php

class Controller_Frontend_Automations extends Controller
{
    public function action_cron()
    {
        $params = $this->request->query();
        if (isset($GLOBALS['argv'])) {
            foreach ($GLOBALS['argv'] as $arg) {
                if (preg_match('/(.+)=(.+)/', $arg, $match)) {
                    $params[$match[1]] = $match[2];
                }
            }
        }

        if ($params['@now']) {
            Model_Automations::$now = strtotime($params['@now']);
        }

        $automations = Model_Automations::get_automations();
        $triggers = Model_Automations::get_triggers();
        foreach ($automations as $automation) {
            if (isset($triggers[$automation['trigger']])) {
                $trigger = $triggers[$automation['trigger']];
                $automation_params = array();
                foreach ($automation['sequences'] as $sequence) {
                    if (count($sequence['intervals']) > 0) {
                        foreach ($sequence['intervals'] as $interval) {
                            $run = true;
                            if (isset($params['interval_id'])) {
                                if ($interval['id'] != $params['interval_id']) {
                                    $run = false;
                                }
                            }
                            if ($run) {
                                //echo $automation['name'] . " executed\n";
                                if ($interval['is_periodic'] == 1) {
                                    /*$sequence['conditions'][] = array(
                                        'field' => $interval['interval_field'],
                                        'operator' => $interval['interval_operator'],
                                        'values' => array(array('val' => $interval['interval_amount'] . $interval['interval_type'])),
                                        'execute' => $interval['execute']
                                    );*/
                                }
                                Model_Automations::run_cron_sequence($trigger, $sequence);
                            }
                        }
                    } else {
                        Model_Automations::run_cron_sequence($trigger, $sequence);
                    }
                }
            }
        }
    }
}