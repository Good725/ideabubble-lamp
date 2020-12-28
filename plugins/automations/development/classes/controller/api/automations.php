<?php

class Controller_Api_Automations extends Controller_Api
{
    public function action_delete()
    {
        if (!Auth::instance()->has_access('automations_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->post('id');
        Model_Automations::delete_automation($id);
        $this->response_data['msg'] = 'deleted';
    }

    public function action_save()
    {
        if (!Auth::instance()->has_access('automations_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        $this->response_data['post'] = $post;
        $id = Model_Automations::save_automation($post);
        $this->response_data['msg'] = 'saved';
        $this->response_data['id'] = $id;

    }

    public function action_get()
    {
        if (!Auth::instance()->has_access('automations_edit')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $id = $this->request->query('id');
        $automation = Model_Automations::get_data($id);
        $this->response_data['msg'] = '';
        $this->response_data['automation'] = $automation;
    }

    public function action_log_list()
    {
        if (!Auth::instance()->has_access('automations_view')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $sequence_id = $this->request->query('sequence_id');
        $automation_id = $this->request->query('automation_id');
        $filter = array();
        if ($automation_id) {
            $filter['automation_id'] = $automation_id;
        }
        if ($automation_id) {
            $filter['sequence_id'] = $sequence_id;
        }
        $log = Model_Automations::log_list($filter);
        $this->response_data['msg'] = '';
        $this->response_data['log'] = $log;
    }

    public function action_settings()
    {
        if (!Auth::instance()->has_access('automations_settings')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            Model_Automations_Settings::save_enabled_triggers($post['trigger']);
            IbHelpers::set_message("Settings have been updated!", 'info popup_box');
            $this->request->redirect('/admin/automations/settings');
        }

        $triggers = Model_Automations::get_triggers();
        $enabled_triggers = Model_Automations_Settings::get_enabled_triggers();
        $this->template->body = View::factory('admin/automation_settings')
            ->set('triggers', $triggers)
            ->set('enabled_triggers', $enabled_triggers);
    }

    public function action_test()
    {
        $post = $this->request->post();
        $automation_id = $post['automation_id'];
        $now = $post['now'];
        $automation = Model_Automations::get_data($automation_id);

        if ($now) {
            Model_Automations::$now = strtotime($now);
        }

        $this->response_data['success'] = false;

        $automations = Model_Automations::get_automations();
        $triggers = Model_Automations::get_triggers();
        $result = array();
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
                            /*$sequence['conditions'][] = array(
                                'field' => $interval['interval_field'],
                                'operator' => $interval['interval_operator'],
                                'values' => array(array('val' => $interval['interval_amount'] . $interval['interval_type'])),
                                'execute' => $interval['execute']
                            );*/
                            $records = Model_Automations::run_cron_sequence($trigger, $sequence, true);
                            if (count($records)) {
                                $result[] = $records;
                            }
                        }
                    }
                } else {
                    $records = Model_Automations::run_cron_sequence($trigger, $sequence, true);
                    if (count($records)) {
                        $result[] = $records;
                    }
                }
            }

            $this->response_data['success'] = true;
            $this->response_data['msg'] = '';
        }
        $this->response_data['result'] = $result;
        $this->response_data['automation'] = $automation;
        $this->response_data['trigger'] = $triggers[$automation['trigger']];
    }
}