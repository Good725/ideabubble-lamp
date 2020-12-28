<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Events extends Controller_Api
{

    protected $user = null;

    public function before()
    {
        parent::before();

        if (!Model_Api::is_enabled('events')) {
            throw new Exception('Not Enabled');
        }

        $user = Auth::instance()->get_user();
        /*if (!$user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }*/

        $this->user = $user;
    }

    /***
     * @method GET
     *
     */

    public function action_countries()
    {
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['countries'] = Model_Event::getCountryMatrix();
    }

    /***
     * @method GET
     */
    public function action_categories()
    {
        $categories = Model_Lookup::lookupList('Event Category');
        $this->response_data['categories'] = array();
        foreach ($categories as $category) {
            $this->response_data['categories'][] = array(
                'value' => $category['value'],
                'label' => $category['label']
            );
        }
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';

    }

    /***
     * @method GET
     */
    public function action_topics()
    {
        $topics = Model_Lookup::lookupList('Event Topic');
        $this->response_data['topics'] = array();
        foreach ($topics as $topic) {
            $this->response_data['topics'][] = array(
                'value' => $topic['value'],
                'label' => $topic['label']
            );
        }
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';

    }

    /***
     * @method GET
     */
    public function action_venues()
    {
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['venues'] = Model_Event::getVenues();
    }

    /***
     * @method GET
     *
     * @param limit optional
     * @param offset optional
     * @param keyword optional
     * @param before optional
     * @param after optional
     * @param tags optional
     * @param category_id optional
     * @param venue_id optional
     * @param own optional
     *
     * @return Array
     */
    public function action_search()
    {
        if (!$this->user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $get = $this->request->query();
        $limit = is_numeric(@$get['limit']) && @$get['limit'] > 0 && @$get['limit'] < 1000 ? $get['limit'] : 10;
        $offset = is_numeric(@$get['offset']) && @$get['offset'] > 0 ? $get['offset'] : 0;
        $params = array(
            'events_only' => 1,
            'limit' => $limit,
            'offset' => $offset,
        );
        if (@$get['keyword']) {
            $params['keyword'] = $get['keyword'];
        }
        if (@$get['keyword2']) {
            $params['keyword2'] = $get['keyword2'];
        }
        if (@$get['name']) {
            $params['name'] = $get['name'];
        }
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if (@$get['tags']) {
            $params['tags'] = $get['tags'];
        }
        if (@$get['category_id']) {
            $params['category_id'] = $get['category_id'];
        }
        if (@$get['venue_id']) {
            $params['venue_id'] = $get['venue_id'];
        }

        $get['own'] = 1;//temporary solution;
        if ($this->user) {
            if (@$get['own']) {
                $params['owned_by'] = $this->user['id'];
            }
        }

        $events = Model_Event::search($params);
        $found_events = (int)DB::select(DB::expr('@found_events as found_events'))->execute()->get('found_events');

        $this->response_data['success'] = true;
        $this->response_data['msg'] = sprintf(__('Found %s events'), $found_events);
        $this->response_data['events'] = $events;
        $this->response_data['found_events'] = $found_events;
        $this->response_data['limit'] = $limit;
        $this->response_data['offset'] = $offset;
    }

    /***
     * @method GET
     *
     * @param id required
     * @param attendees bool optional
     *
     * @return Array
     */
    public function action_details()
    {
        $get = $this->request->query();
        $id = $get['id'];
        if ($this->user) {

        }

        $event = Model_Event::eventLoad($id);
        $attendees = array();
        if (@$get['attendees'] && $event && $event['owned_by'] == @$this->user['id']) {
            $attendees = Model_Event::ordersList(array(
                'event_id' => $event['id'],
                'status' => 'PAID',
                'display_tickets' => 1
            ));
        }

        $this->response_data['success'] = $event ? true : false;
        $this->response_data['msg'] = $event ? __('Found') : __('No such event');
        $this->response_data['details'] = $event;
        if (@$get['attendees'] && $event) {
            $this->response_data['attendees'] = $attendees;
        }
    }


    /***
     * @method POST
     * @param event_id required
     * @param items required
     * @param discount_code optional
     */
    public function action_order_calculate()
    {
        $post = $this->request->post();
        $items = $post['items'];
        $discount_code = @$post['discount_code'];
        $event = Model_Event::eventLoad($post['event_id']);
        $order = Model_Event::orderCalculate($event, $items, $discount_code);
        $this->response_data['success'] = true;
        $this->response_data['order'] = $order;
        $this->response_data['msg'] = '';
    }

    /***
     * @method POST
     * @param event_id required
     * @param items required
     * @param discount_code optional
     */
    public function action_order_process()
    {
        $locked = (int)DB::select(DB::expr("GET_LOCK('process_order', 30) as locked"))->execute()->get('locked');
        if ($locked != 1) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Server is too busy. Please try again later.');
            return;
        }

        $post = $this->request->post();
        IbHelpers::strip_tags_array($post);
        $result = array();
        $new_user = FALSE;

        $user = Auth::instance()->get_user();

        if (empty($user['id'])) {
            $new_user = TRUE;
            $user_model = new Model_users;
            $new_user_data = $post;
            $new_user_data['can_login'] = 1;
            $registered = $user_model->register_user($new_user_data);
            if (!empty($registered['error'])) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = $registered['error'];
                DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
                return;
            } else if (!Auth::instance()->login($post['email'], $post['password'])) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('Error logging into your new account');
                DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
                return;
            } else {
                $user = Auth::instance()->get_user();
            }
        }

        $event = Model_Event::eventLoad($post['event_id']);
        $account = Model_Event::accountDetailsLoad($event['owned_by']);
        $commission = Model_Event::commissionGet($event, $event['owned_by']);

        // Set up an array of ticket types for the event being booked
        $event_tickets = array();
        foreach ($event['ticket_types'] as $item) $event_tickets[$item['id']] = $item;

        // Validate the ticket types being purchased
        foreach ($post['items'] as $item) {
            if (!isset($event_tickets[$item['ticket_type_id']])) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('The ticket being purchased does not correspond to the selected event.');
                return;
            } else {
                foreach ($item['dates'] as $date) {
                    if ($event_tickets[$item['ticket_type_id']]['dates_quantity_remaining'][$date]['quantity'] < $item['quantity']) {
                        $this->response_data['success'] = false;
                        $this->response_data['msg'] = __('There are not enough tickets left in stock to complete your order.');
                        return;
                    }
                }
            }
        }

        if($post['save_checkout']){
            $checkoutDetails = array(
                'ccName' => $post['ccName'],
                'address' => $post['address'],
                'city' => $post['city'],
                'county' => $post['county'],
                'country_id' => $post['country_id'],
                'postcode' => $post['postcode'],
                'telephone' => $post['telephone'],
                'email' => $post['email'],
                'comments' => $post['comments']
            );
            Model_Event::checkoutDetailsSave($user['id'], $checkoutDetails);
        }

        $order = array();
        $order['buyer_id'] = $user['id'];
        $order['account_id'] = $account['id'];
        $order['status'] = 'Processing';
        $order['status_reason'] = 'New Order';
        $order['total'] = $post['total'];
        $order['currency'] = 'EUR';
        $order['firstname'] = @$post['firstname'] ? @$post['firstname'] : @$post['ccName'];
        $order['lastname'] = @$post['lastname'];
        $order['email'] = $post['email'];
        $order['address_1'] = @$post['address'];
        $order['address_2'] = @$post['address_2'];
        $order['city'] = $post['city'];
        $order['country_id'] = $post['country_id'];
        $order['county'] = isset($post['county']) ? $post['county'] : null;
        $order['county_id'] = @$post['county_id'];
        $order['eircode'] = $post['postcode'];
        $order['telephone'] = $post['telephone'];
        $order['comments'] = isset($post['comments']) ? $post['comments'] : null;
        $order['commission_type'] = $commission['type'];
        $order['commission_amount'] = $commission['amount'];
        $order['commission_fixed_charge_amount'] = $commission['fixed_charge_amount'];
        $order['ip4'] = ip2long($_SERVER['REMOTE_ADDR']);
        $total = Model_Event::orderCalculate($event, $post['items']);
        if ($total['error']) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = $total['error'];
            return;
        } else {
            $order['total'] = $total['total'];
            $order['currency'] = $total['currency'];
            $order['vat_total'] = $total['vat'];
            $order['vat_rate'] = $total['vat_rate'];
            $order['commission_total'] = $total['commission'];
            $order['discount'] = $total['discount'];
        }

        $cc = array();
        $cc['name'] = $post['ccName'];
        $cc['number'] = $post['ccNum'];
        $cc['cvc'] = $post['ccCVC'];
        $cc['year'] = $post['ccYear'];
        $cc['month'] = $post['ccMonth'];
        $cc['type'] = $post['ccType'];
        $result = Model_Event::orderSave($account, $order, $total['items'], $cc);

        if ($result['error']) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = $result['error'];
        } else {
            $this->response_data['success'] = true;
            $this->response_data['msg'] = '';
        }

        if (@$post['signup_newsletter'] && Settings::instance()->get('mailchimp_list_id') != '' && Settings::instance()->get('mailchimp_apikey') != '') {
            $mc = new Mailchimp();
            $result['mailchimp'] = $mc->add_to_list($post['email'], 'subscribed', $_SERVER['REMOTE_ADDR']);
        }

        if ($new_user) {
            // User needs to verify their email before they can conduct further actions with this account
            Auth::instance()->logout();
        }

        DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
        $this->response_data['order'] = $result;
    }

    public function action_orders()
    {
        if (!$this->user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $params = array(
            'owner_id' => $this->user['id'],
            'display_tickets' => 1
        );
        if ($this->request->query('event_id')) {
            $params['event_id'] = $this->request->query('event_id');
        }
        $orders = Model_Event::ordersList($params);

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['orders'] = $orders;
    }

    public function action_tickets()
    {
        if (!$this->user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $get = $this->request->query();

        $params = array();
        $now = date::now();
        if (@$get['past']) {
            $params['past'] = $now;
        }
        if (@$get['upcoming']) {
            $params['upcoming'] = $now;
        }
        if (@$get['sold']) {
            $params['owner_id'] = $this->user['id'];
        }
        if (@$get['bought']) {
            $params['buyer_id'] = $this->user['id'];
        }

        if (!@$get['sold'] && !@$get['bought']) {
            $params['user_id'] = $this->user['id'];
        }
        if (@$get['event_id']) {
            $params['event_id'] = $get['event_id'];
        }
        $tickets = Model_Event::ticketsList($params);

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['tickets'] = $tickets;
    }

    public function action_checkin()
    {
        if (!$this->user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        try {
            Database::instance()->begin();

            $now = date::now();
            $tickets = $this->request->post('ticket');
            $result = array();
            foreach ($tickets as $ticket) {
                $ticket_details = Model_Event::ticketLoad($ticket['id']);
                $allowToEnterStatus = ($this->user['id'] == $ticket_details['seller_id']);
                $post = $this->request->post();
                if ($allowToEnterStatus) {
                    if ($ticket_details['checked']) {
                        $result[] = array(
                            'id' => $ticket['id'],
                            'checked' => 2,
                            'date' => $ticket_details['checked']
                        );
                    } else {
                        $check = array(
                            'checked_by' => $this->user['id'],
                            'checked' => @$ticket['checked'] ? $now : null,
                        );
                        if (@$ticket['note']) {
                            $check['checked_note'] = $ticket['note'];
                        }
                        DB::update(Model_Event::TABLE_TICKETS)->set($check)->where('id', '=', $ticket['id'])->execute();
                        $result[] = array(
                            'id' => $ticket['id'],
                            'checked' => 1,
                            'date' => $now
                        );
                    }
                } else {
                    $result[] = array(
                        'id' => $ticket['id'],
                        'checked' => 0
                    );
                }
            }

            $this->response_data['success'] = true;
            $this->response_data['msg'] = '';
            $this->response_data['result'] = $result;
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            $this->response_data['success'] = false;
            $this->response_data['msg'] = $exc->getMessage();
        }
    }

    public function action_qrcode_scan()
    {
        if (!$this->user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $now = date::now();
        $url = $this->request->post('ticket');
        $ticket = Model_Event::ticketLoadFromUrlParam($url);

        $allowToEnterStatus = ($this->user['id'] == $ticket['seller_id']);
        if ($allowToEnterStatus) {
            if ($ticket['checked']) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = 'Already Checked In';
                $this->response_data['ticket'] = array(
                    'id' => $ticket['id'],
                    'checked' => 2,
                    'date' => $ticket['checked'],
                    'note' => $ticket['checked_note']
                );
            } else {
                $check = array(
                    'checked_by' => $this->user['id'],
                    'checked' => $now,
                );
                if (@$ticket['note']) {
                    $check['checked_note'] = $ticket['note'];
                }
                DB::update(Model_Event::TABLE_TICKETS)->set($check)->where('id', '=', $ticket['id'])->execute();

                $this->response_data['success'] = true;
                $this->response_data['msg'] = '';
                $this->response_data['ticket'] = array(
                    'id' => $ticket['id'],
                    'checked' => 1,
                    'date' => $ticket['checked'],
                    'note' => $ticket['checked_note']
                );
            }
        } else {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Permission Denied');
        }
    }

    /***
     * @method GET
     */
    public function action_dashboard()
    {
        if (!$this->user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['events_on_sale'] = DB::select(
            DB::expr('DISTINCT events.id'),
            'events.name'
        )
            ->from(array(Model_Event::TABLE_EVENTS, 'events'))
            ->join(array(Model_Event::TABLE_DATES, 'dates'), 'inner')->on('events.id', '=', 'dates.event_id')
            ->where('events.deleted', '=', 0)
            ->and_where('dates.deleted', '=', 0)
            ->and_where('events.owned_by', '=', $this->user['id'])
            ->and_where('events.is_onsale', '=', 1)
            ->and_where('events.status', '=', 'Live')
            ->and_where('dates.starts', '>=', date::now())
            ->order_by('dates.starts', 'asc')
            ->execute()
            ->as_array();
        $this->response_data['events_on_sale_count'] = count($this->response_data['events_on_sale']);

        $this->response_data['events_on_sale_next'] = isset($this->response_data['events_on_sale'][0]) ? $this->response_data['events_on_sale'][0] : null;

        $this->response_data['events_bought'] = DB::select(
            DB::expr('DISTINCT events.id'),
            'events.name'
        )
            ->from(array(Model_Event::TABLE_EVENTS, 'events'))
            ->join(array(Model_Event::TABLE_DATES, 'dates'), 'inner')->on('events.id', '=', 'dates.event_id')
            ->join(array(Model_Event::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('dates.id', '=', 'idates.date_id')
            ->join(array(Model_Event::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
            ->join(array(Model_Event::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
            ->where('events.deleted', '=', 0)
            ->and_where('dates.deleted', '=', 0)
            ->and_where('events.is_onsale', '=', 1)
            ->and_where('events.status', '=', 'Live')
            ->and_where('dates.starts', '>=', date::now())
            ->and_where('orders.buyer_id', '=', $this->user['id'])
            ->and_where('orders.status', '=', 'PAID')
            ->order_by('dates.starts', 'asc')
            ->execute()
            ->as_array();
        $this->response_data['events_bought_count'] = count($this->response_data['events_bought']);
    }
}