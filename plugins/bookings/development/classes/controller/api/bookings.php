<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_bookings extends Controller_Api
{
    protected $user = null;

    public function before()
    {
        parent::before();

        $user = Auth::instance()->get_user();
        $this->user = Model_Users::get_user($user['id']);
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $this->contact = new Model_Contacts3(@$contacts[0]['id']);
    }

    public function action_search()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $params = array();
        if (@$get['trainer_id']) {
            $params['trainer_id'] = $get['trainer_id'];
        } else {
            if (@$get['contact_id']) {
                $params['contact_id'] = $get['contact_id'];
            } else {
                $params['contact_id'] = $this->contact->get_id();
            }

            if (is_numeric($params['contact_id'])) {
                $c3 = new Model_Contacts3($params['contact_id']);
                if (in_array(1, $c3->get_roles())) { // parent
                    $family_id = $c3->get_family_id();
                    $members = Model_Contacts3::get_family_members($family_id);
                    $params['contact_id'] = array($params['contact_id']);
                    foreach ($members as $member) {
                        $params['contact_id'][] = $member['id'];
                    }
                }
            }
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }
        if (@$get['sort']) {
            $params['sort'] = $get['sort'];
        }

        $bookings = Model_KES_Bookings::search($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['bookings'] = $bookings;
    }

    public function action_search2()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $params = array();
        if (@$get['trainer_id']) {
            $params['trainer_id'] = $get['trainer_id'];
        } else {
            if (@$get['contact_id']) {
                $params['contact_id'] = $get['contact_id'];
            } else {
                $params['contact_id'] = $this->contact->get_id();
            }

            if (is_numeric($params['contact_id'])) {
                $c3 = new Model_Contacts3($params['contact_id']);
                if (in_array(1, $c3->get_roles())) { // parent
                    $family_id = $c3->get_family_id();
                    $members = Model_Contacts3::get_family_members($family_id);
                    $params['contact_id'] = array($params['contact_id']);
                    foreach ($members as $member) {
                        $params['contact_id'][] = $member['id'];
                    }
                }
            }
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }
        if (@$get['keyword']) {
            $params['keyword'] = $get['keyword'];
        }
        if (@$get['sort']) {
            $params['sort'] = $get['sort'];
        }

        $bookings = Model_KES_Bookings::search2($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['bookings'] = $bookings;
    }

    public function action_details()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $booking_id = @$get['id'];

        $details = Model_KES_Bookings::details($booking_id);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['booking'] = $details;
    }

    public function action_offers()
    {
        $discounts = Model_KES_Discount::search(
            array(
                'publish_on_web' => 1,
                'code' => ''
            )
        );

        foreach ($discounts as $i => $discount) {
            $discounts[$i] = array(
                'id' => $discount['id'],
                'title' => $discount['title'],
                'summary' => $discount['summary'],
                'amount' => $discount['amount'],
                'amount_type' => $discount['amount_type']
            );
        }

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['discounts'] = $discounts;
    }

    public function action_stats()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $stats = [];
        $bookings = Model_KES_Bookings::search(['contact_id' => $this->contact->get_id(), 'booking_status' => [1,2,4,5]]);
        $stats['booking_count'] = count($bookings);
        

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['stats'] = $stats;
    }
}