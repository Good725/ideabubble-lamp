<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Logistics extends Controller_Cms
{
    public function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home',  'link' => '/admin'],
            ['name' => 'Logistics', 'link' => '/admin/logistics']
        ];

        if (true) { // Add permission check
            $menus[1] = ['name' => 'My applications', 'link' => '/admin/applications/my_applications', 'icon' => 'study-mode'];
            $menus[3] = ['name' => 'My interviews',   'link' => '/admin/applications/my_interviews',   'icon' => 'my-requests'];
            $menus[5] = ['name' => 'My offers',       'link' => '/admin/applications/my_offers',       'icon' => 'settlements'];
        }

        // Order items by array key. e.g. 0, 2, 4, 1, 3, 5 =>  0, 1, 2, 3, 4, 5
        ksort($menus);

        // Remove gaps in array keys. e.g. 1, 3, 5 => 0, 1, 2
        $this->template->sidebar->menus = ['Logistics' => self::get_menu_links()];
    }

    public function action_index()
    {
        self::action_transfers();
    }

    public function action_transfers()
    {
        $this->template->sidebar->tools = '<button type="button" class="btn btn-primary transfer-modal-toggle" id="transfer-add">Add transfer</button>';
        $this->template->sidebar->breadcrumbs[] = ['name' => 'Transfers', 'link' => '#'];

        $this->template->styles[URL::get_engine_assets_base().'css/validation.css'] = 'screen';
        $this->template->styles[URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css'] = 'screen';

        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('logistics').'js/transfers.js"></script>';

        $locations = ORM::factory('Location')->find_all_published();
        $this->template->body = View::factory('list_transfers')
            ->set('locations', $locations);
    }

    public function action_ajax_get_transfer()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $id = $this->request->param('id');
        $transfer = ORM::factory('Logistics_Transfer')->where('id', '=', $id)->find_undeleted();

        $return = $transfer->as_array();
        $return['driver_name'] = $transfer->driver->get_full_name();
        $return['passenger_name'] = $transfer->passenger->get_full_name();
        $return['passenger_bookings'] = $transfer->passenger->bookings;
        $return['note'] = $transfer->get_note();

        echo json_encode($return);
    }

    public function action_ajax_get_passenger_bookings()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $passenger = new Model_Contacts3_Contact($this->request->param('id'));
        $bookings  = $passenger->bookings->find_all()->as_array();

        $return = ['bookings' => []];
        foreach ($bookings as $booking) {
            $booking_data = Model_KES_Bookings::get_details($booking->booking_id);
            $return['bookings'][] = [
                'booking_id' => $booking_data['booking_id'],
                'schedule' => $booking_data['schedule'],
                'label' => 'Booking #'.$booking_data['booking_id'].': '.$booking_data['schedule']
            ];
        }

        echo json_encode($return);
    }

    public function action_ajax_get_transfers_datatable()
    {
        $this->auto_render = false;
        $filters = $this->request->query('filters');
        $results = Model_Logistics_Transfer::get_for_datatable($filters, $this->request->query());
        echo json_encode($results);
    }

    public function action_ajax_save_transfer()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $db = Database::instance();

        try {
            $db->commit();
            $post = $this->request->post();

            // Save the transfer
            $transfer = new Model_Logistics_Transfer($this->request->post('id'));
            $transfer->values($post);
            $transfer->set('scheduled_date', trim($this->request->post('date').' '.$this->request->post('time')));
            $transfer->save_with_moddate();

            $return['success'] = true;
            $return['messages'][] = ['success' => true, 'message' => 'Transfer #'.$transfer->id.' has been saved.'];

            // Save the note
            if ($this->request->post('note') != $transfer->get_note('note')) {
                $note_data = ['type' => 'Logistic transfer', 'reference_id' => $transfer->id, 'note' => trim($post['note'])];
                Model_Notes::save($note_data, true);
            }
            // If this is a new booking, send the email
            if (!$this->request->post('id')) {
                $message    = new Model_Messaging();
                $recipients = [['target_type' => 'CMS_CONTACT3','target' => $transfer->passenger->id]];
                $parameters = [
                    'name'             => $transfer->passenger->get_full_name(),
                    'pickup_location'  => $transfer->pickup->title,
                    'dropoff_location' => $transfer->dropoff->title,
                    'scheduled_date'   => $transfer->scheduled_date
                ];

                $sent = $message->send_template('logistics_transfer_created', null, null, $recipients, $parameters);
                $message = $sent ? (Settings::instance()->get('messaging_keep_outbox')) ? 'An email has been sent to outbox' : 'An email has been sent' : 'Failed to send email';

                $return['messages'][] = ['success' => $sent, 'message' => $message];
            }
        } catch (Exception $e) {
            $db->rollback();
            Log::instance()->add(Log::ERROR, "Error saving transfer.\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return['success'] = false;
            $return['messages'] = [
                ['success' => false, 'message' => 'Error saving transfer. If this problem continues, please ask an administrator to check the error logs.']
            ];
        }

        echo json_encode($return);
    }

    public function action_locations()
    {
        $this->template->sidebar->tools = '<button type="button" class="btn btn-primary location-modal-toggle" id="location-add">Add location</button>';
        $this->template->sidebar->breadcrumbs[] = ['name' => 'Locations', 'link' => '#'];
        $this->template->styles[URL::get_engine_assets_base().'css/validation.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key='.Settings::instance()->get('google_map_key').'"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('logistics').'js/locations.js"></script>';

        $cities   = ORM::factory('Locations_City')->order_by('name')->find_all_published();
        $counties = Model_Residence::get_all_counties();

        $this->template->body = View::factory('list_logistics_locations')
            ->set('cities', $cities)
            ->set('counties', $counties);
    }

    public function action_ajax_get_locations_datatable()
    {
        $this->auto_render = false;
        $filters = $this->request->query('filters');
        $results = Model_Location::get_for_datatable($filters, $this->request->query());
        echo json_encode($results);
    }

    public function action_ajax_get_location()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $id = $this->request->param('id');
        $location = ORM::factory('Location')->where('id', '=', $id)->find_undeleted();

        $return = $location->as_array();
        $return['note'] = $location->get_note();

        echo json_encode($return);
    }

    public function action_ajax_save_location()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $return = [];

        $db = Database::instance();

        try {
            $db->commit();
            $post = $this->request->post();
            $location = ORM::factory('Location')->where('id', '=', $this->request->post('id'))->find_undeleted();
            $location->values($post);

            // Save the city, if a new one is created
            if (!empty($post['new_city']) && trim($post['new_city'])) {
                $city = new Model_Locations_City();
                $city->set('name', $post['new_city']);
                $city->save_with_moddate();

                $location->set('city_id', $city->id);

                // If a new city is created, the dropdown will need to be reubilt to include it
                $return['cities'] = ORM::factory('Locations_City')->order_by('name')->find_all_published()->as_array('name', 'id');
            }

            // Save the location
            $location->save_with_moddate();


            // Save the note
            if ($this->request->post('note') != $location->get_note('note')) {
                $note_data = ['type' => 'Location', 'reference_id' => $location->id, 'note' => trim($post['note'])];
                Model_Notes::save($note_data, true);
            }

            $return['success'] = true;
            $return['message'] = 'Location #'.$location->id.': &quot;'.$location->title.'&quot; has been saved.';


        } catch (Exception $e) {
            $db->rollback();

            Log::instance()->add(Log::ERROR, "Error saving location.\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return['success'] = false;
            $return['message'] = 'Error saving location. If this problem continues, please ask an administrator to check the error logs.';
        }

        echo json_encode($return);
    }


    /*
     * Database seeding
     */

    public function action_seed()
    {
        $fred = new Model_Contacts3_Contact();
        $fred->first_name = 'Fred';
        $fred->last_name = 'Flintstone';
        $fred->save_with_moddate();

        $wilma = new Model_Contacts3_Contact();
        $wilma->first_name = 'Wilma';
        $wilma->last_name = 'Flintstone';
        $wilma->save_with_moddate();

        $pebbles = new Model_Contacts3_Contact();
        $pebbles->first_name = 'Pebbles';
        $pebbles->last_name = 'Flintstone';
        $pebbles->save_with_moddate();

        $michael = new Model_Contacts3_Contact();
        $michael->first_name = 'Michael';
        $michael->last_name = 'Knight';
        $michael->save_with_moddate();

        $dublin = new Model_Location();
        $dublin->title = 'Dublin airport';
        $dublin->save_with_moddate();

        $shannon = new Model_Location();
        $shannon->title = 'Shannon airport';
        $shannon->save_with_moddate();

        $address1 = new Model_Location();
        $address1->title = 'Address 1';
        $address1->save_with_moddate();

        $transfer = new Model_Logistics_Transfer();
        $transfer->title = 'Airport pickup 1';
        $transfer->type = 'Arrival';
        $transfer->pickup_id = $dublin->id;
        $transfer->dropoff_id = $address1->id;
        $transfer->passenger_id = $wilma->id;
        $transfer->driver_id = $michael->id;
        $transfer->scheduled_date = date('Y-m-d', strtotime('-1 weeks')).' 10:30';
        $transfer->save_with_moddate();

        $transfer = new Model_Logistics_Transfer();
        $transfer->title = 'Airport pickup 2';
        $transfer->type = 'Arrival';
        $transfer->pickup_id = $shannon->id;
        $transfer->dropoff_id = $address1->id;
        $transfer->passenger_id = $fred->id;
        $transfer->driver_id = $michael->id;
        $transfer->scheduled_date = date('Y-m-d', strtotime('-1 day')).' 09:00';
        $transfer->save_with_moddate();

        $transfer = new Model_Logistics_Transfer();
        $transfer->title = 'Airport pickup 3';
        $transfer->type = 'Departure';
        $transfer->pickup_id = $address1->id;
        $transfer->dropoff_id = $dublin->id;
        $transfer->passenger_id = $pebbles->id;
        $transfer->driver_id = $michael->id;
        $transfer->scheduled_date = date('Y-m-d', strtotime('+3 weeks')).' 09:00';
        $transfer->save_with_moddate();

        $transfer = new Model_Logistics_Transfer();
        $transfer->title = 'Airport pickup 4';
        $transfer->type = 'Departure';
        $transfer->pickup_id = $address1->id;
        $transfer->dropoff_id = $dublin->id;
        $transfer->passenger_id = $wilma->id;
        $transfer->driver_id = $michael->id;
        $transfer->scheduled_date = date('Y-m-d', strtotime('+3 weeks')).' 09:00';
        $transfer->save_with_moddate();

        $this->request->redirect('/admin/logistics');
    }

    function get_menu_links()
    {
        return  [
            ['name' => 'Transfers', 'link' => '/admin/logistics',           'icon' => 'timeoff'],
            ['name' => 'Locations', 'link' => '/admin/logistics/locations', 'icon' => 'location']
        ];
    }

    public function action_ajax_get_submenu()
    {
        $menu = self::get_menu_links();
        $return = ['items' => []];

        foreach ($menu as $item) {
            $return['items'][] = [
                'title'    => $item['name'],
                'link'     => $item['link'],
                'icon_svg' => $item['icon']
            ];
        }

        return $return;
    }

}