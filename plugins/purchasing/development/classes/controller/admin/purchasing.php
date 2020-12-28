<?php

class Controller_Admin_Purchasing extends Controller_Cms
{
    function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Purchases', 'link' => '/admin/purchasing'),
        );
    }

    public function action_index()
    {
        $options = ['overview' => __('Overview'),  'details' => __('Details')];
        $attributes = ['class' => 'stay_inline', 'style' => 'display: inline-flex; margin: 0 0 0 1.5rem; width: auto;']; /* need to standardise */
        $this->template->sidebar->tools  = '<button id="new-purchasing-request" class="btn btn-primary">'.__('Request PO').'</button>';
        $this->template->sidebar->tools .= Form::btn_options('purchasing-view', $options, 'overview', false, ['class' => 'purchasing-view_toggle'],  $attributes, ['selected_class' => 'btn-default']);

        $this->template->styles[URL::get_engine_assets_base() . 'css/validation.css'] = 'screen';
        $this->template->styles[URL::get_engine_assets_base() . 'css/bootstrap.daterangepicker.min.css'] = 'screen';

        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('purchasing') . 'js/purchasing.js"></script>';

        $this->template->body = View::factory('admin/purchasing')->set([
            'approvers'  => Model_Purchasing::get_approvers(),
            'departments' => Model_Contacts3::get_all_contacts([['type.label',      '=', 'Department']]),
            'suppliers'  => Model_Contacts3::get_all_contacts([['subtype.subtype', 'in', ['Supplier', 'Suppliers']]])
        ]);
    }

    public function action_request_save()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $data = $this->request->post();

        $id = Model_Purchasing::save($data);
        $message = $id ? __('Purchase has been saved.') : __('Error saving purchase order.');
        echo json_encode(array('id' => $id, 'message' => $message, 'success' => (bool) $id));
    }

    public function action_approve()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $user = Auth::instance()->get_user();
        $data = array(
            'id' => $this->request->post('id'),
            'approved_by' => $user['id'],
            'approved' => date::now(),
            'status' => 'Approved'
        );
        $id = Model_Purchasing::save($data);
        $message = $id ? __('Purchase has been approved.') : __('Error approving purchase.');

        echo json_encode([
            'id'      => $id,
            'message' => $message,
            'success' => (bool) $id
        ]);
    }

    public function action_decline()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $user = Auth::instance()->get_user();
        $data = array(
            'id' => $this->request->post('id'),
            'status' => 'Declined'
        );
        $id = Model_Purchasing::save($data);
        $message = $id ? __('Purchase has been declined.') : __('Error declining purchase.');

        echo json_encode([
            'id'      => $id,
            'message' => $message,
            'success' => (bool) $id
        ]);
    }

    public function action_purchase()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $user = Auth::instance()->get_user();
        $data = array(
            'id' => $this->request->post('id'),
            'purchased_by' => $user['id'],
            'purchased' => date::now(),
            'status' => 'Purchased'
        );
        $id = Model_Purchasing::save($data);
        $message = $id ? __('Purchase has been complete.') : __('Error completing purchase.');

        echo json_encode([
            'id'      => $id,
            'message' => $message,
            'success' => (bool) $id
        ]);
    }

    public function action_requests()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        if (@$post['date']) {
            $post['after'] = $post['date'];
            $post['before'] = date('Y-m-d', strtotime($post['date']) + 86400);
        }
        $result = Model_Purchasing::datatable($post);
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function action_overview()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $params = $post;
        $date_field = 'created';
        $params['sort'] = $date_field;
        $requests = Model_Purchasing::search($params);
        $overview = array('department' => [], 'total' => ['count' => 0, 'value' => 0, 'days' => []], 'days' => []);
        $overview['total_spent_months'] = $this->get_total_spent_months($post['after'], $post['before']);
        foreach ($requests as $request) {
            $date = date('Y-m-d', strtotime($request[$date_field]));
            $request['department_id'] = ($request['department_id'] == null) ? "0" : $request['department_id'];
            if (!isset($overview['department'][$request['department_id']])) {
                $overview['department'][$request['department_id']] = [
                    'department_id' => ($request['department_id'] == null) ? "0" : $request['department_id'],
                    'department' => $request['department'],
                    'count' => 0,
                    'value' => 0,
                    'days' => []
                ];
            }
            if (!isset($overview['department'][$request['department_id']]['days'][$date])) {
                $overview['department'][$request['department_id']]['days'][$date] = 0;
            }
            $overview['department'][$request['department_id']]['count'] += 1;
            $overview['department'][$request['department_id']]['value'] += $request['total'];
            $overview['department'][$request['department_id']]['days'][$date] += $request['total'];
            $overview['department'][$request['department_id']]['months'][date("M", strtotime($date))] += $request['total'];
            
            if (!isset($overview['total']['days'][$date])) {
                $overview['total']['days'][$date] = 0;
                $overview['days'][$date] = $date;
            }
            $overview['total']['count'] += 1;
            $overview['total']['value'] += $request['total'];
            $overview['total']['days'][$date] += $request['total'];
            $overview['total']['months'][date("M", strtotime($date))] += $request['total'];
        }
        $overview['days'] = array_values($overview['days']);
        $overview['days_formatted'] = [];
        $first_day = strtotime($overview['days'][0]);
        $last_day = strtotime($overview['days'][count($overview['days']) - 1]);
        $overview['days'] = [];
        $day = $first_day;
        $days = array();
        $date_format = Settings::instance()->get('date_format') ?: 'd-m-Y';
        while($day <= $last_day) {
            $overview['days'][] = date('Y-m-d', $day);
            $overview['days_formatted'][] = date($date_format, $day);
            $day += 86400;
        }
        echo json_encode($overview, JSON_PRETTY_PRINT);
    }
    
    public function action_ajax_get_purchasing_order_information()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $purchase_order_id = $this->request->query('purchase_order_id');
        $purchase_order_data = Model_Purchasing::details($purchase_order_id);
        $department = new Model_Contacts3($purchase_order_data['department_id']);
        $supplier = new Model_Contacts3($purchase_order_data['supplier_id']);
        $reviewer = new Model_Contacts3($purchase_order_data['reviewer_id']);
        $purchase_order_data['department_name'] = $department->get_first_name() . " " . $department->get_last_name();
        $purchase_order_data['supplier_name'] = $supplier->get_first_name() . " " . $supplier->get_last_name();
        $purchase_order_data['reviewer_name'] = $reviewer->get_first_name() . " " . $reviewer->get_last_name();
        echo json_encode($purchase_order_data);
    }
    
    public function get_total_spent_months($date_start, $date_end) {
        $months = array();
        $start = (new DateTime($date_start))->modify('first day of this month');
        $end = (new DateTime($date_end))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $dt) {
            $months[$dt->format("M")] = Model_Purchasing::get_money_spent_in_month($dt->format("n"));
            $months_in_date[] = $dt->format("n");
        }
        return $months;
    }
}