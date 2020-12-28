<?php defined('SYSPATH') or die('No direct script access.');

class Model_Extra extends Model
{
    const SERVICE_TABLE = 'plugin_extra_services';
    const CUSTOMER_TABLE = 'plugin_extra_customers';
    const HOST_TABLE = 'plugin_extra_hosts';
    const CONTROL_PANEL_TABLE = 'plugin_extra_control_panels';
    const DOMAIN_TYPE_TABLE = 'plugin_extra_domain_types';
    const SERVICE_TYPE_TABLE = 'plugin_extra_service_types';
    const STATUS_TABLE = 'plugin_extra_status';
    const BILLING_FREQUENCY_TABLE = 'plugin_extra_billing_frequency';
    const PAYMENT_TYPE_TABLE = 'plugin_extra_payment_type';
    const PAYMENT_TABLE = 'plugin_extra_payments';
    const INVOICE_TABLE = 'plugin_extra_invoices';
    const RVCARDS_TABLE = 'plugin_extra_realvault_cards';
    const RVPAYER_TABLE = 'plugin_extra_realvault_payers';
    const PROJECT_TABLE = 'plugin_extra_projects';
    const ISSUES_TABLE = 'plugin_extra_projects_issues';
    const WORKLOG_TABLE = 'plugin_extra_projects_worklog';
    const RAPIDVIEWS_TABLE = 'plugin_extra_projects_rapidviews';
    const RAPIDVIEWS_SPRINTS_TABLE = 'plugin_extra_projects_rapidviews_has_sprints';
    const SPRINTS_TABLE = 'plugin_extra_projects_sprints';
    const SPRINT_ISSUES_TABLE = 'plugin_extra_projects_sprints_has_issues';
    const SPRINTS_TABLE2 = 'plugin_extra_projects_sprints2';
    const SPRINT2_STATUS_TYPES_TABLE = 'plugin_extra_projects_status_types';

    public static function get_service_data($id = NULL, $customer_id = NULL)
    {
        $services = DB::select(
            array('service.id', 'id'),
            array('customer.company_title', 'company_title'),
            array('customer.email', 'email'),
            array('customer.id', 'company_id'),
            array('service.type_id', 'type_id'),
            array('service_type.friendly_name', 'service_type'),
            array('service.url', 'url'),
            array('service.host_id', 'host_id'),
            array('service.control_panel_id', 'control_panel_id'),
            array('service.ip_address', 'ip_address'),
            array('service.price', 'price'),
            array('service.discount', 'discount'),
            array('service.billing_frequency_id', 'billing_frequency_id'),
            array('billing_frequency.name', 'billing_frequency_name'),
            array('billing_frequency.friendly_name', 'billing_frequency'),
            array('service.status_id', 'status_id'),
            array('sstat.name', 'status'),
            array('sstat.friendly_name', 'fstatus'),
            array('service.years_paid', 'years_paid'),
            array('service.years_confirmed', 'years_confirmed'),
            array('service.date_start', 'date_start'),
            array('service.date_end', 'date_end'),
            array('service.payment_id','payment_id'),
            array('service.referrer', 'referrer'),
            array('service.note', 'note'),
            array('service.date_modified', 'date_modified'),
            array('domain_type.friendly_name', 'domain_type'),
            array('domain_type.id', 'domain_type_id'),
            array('product.id', 'product_id'),
            array('service.auto_renew', 'auto_renew'),
            DB::expr('GROUP_CONCAT(invoice.id) AS paid_invoices'),
            array('customer.contact', 'contact'),
            array('customer.billing_contact', 'billing_contact')
        )
        ->from(array(self::SERVICE_TABLE, 'service'))
            ->join(array(self::CUSTOMER_TABLE, 'customer'),'LEFT OUTER')
                ->on('service.company_id', '=', 'customer.id')
            ->join(array(self::DOMAIN_TYPE_TABLE, 'domain_type'), 'LEFT')
                ->on('service.domain_type_id', '=', 'domain_type.id')
            ->join(array(self::SERVICE_TYPE_TABLE, 'service_type'), 'LEFT')
                ->on('service.type_id', '=', 'service_type.id')
            ->join(array(self::BILLING_FREQUENCY_TABLE, 'billing_frequency'), 'LEFT')
                ->on('service.billing_frequency_id', '=', 'billing_frequency.id')
            ->join(array(Model_Product::MAIN_TABLE, 'product'), 'LEFT')
                ->on('service_type.friendly_name', '=', 'product.title')
            ->join(array(self::STATUS_TABLE, 'sstat'), 'LEFT')
                ->on('service.status_id', '=', 'sstat.id')
            ->join(array(self::INVOICE_TABLE, 'invoice'), 'LEFT')
                ->on('service.id', '=', 'invoice.service_id')
                ->on('invoice.status', '=', DB::expr("'Paid'"))
                ->on('invoice.date_from', '<=', DB::expr("'" . date('Y-m-d') . "'"))
                ->on('invoice.date_to', '>=', DB::expr("'" . date('Y-m-d') . "'"))
        ->where('delete', '=', 0)
        ->group_by('service.id');

        ( ! is_null($id))          ? $services = $services->and_where('service.id', '=', $id)          : NULL;
        ( ! is_null($customer_id)) ? $services = $services->and_where('company_id', '=', $customer_id) : NULL;

        $services = $services->order_by('service.date_modified', 'DESC')->execute()->as_array();
        foreach ($services as $i => $service) {
            $services[$i]['invoices'] = DB::select('*')
                ->from(self::INVOICE_TABLE)
                ->where('service_id', '=', $service['id'])
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array();
        }

        return ($id == NULL) ? $services : $services[0];
    }

    // TODO: Merge this with the above function
    public static function get_sorted_service_data( $from, $to, $id = NULL, $customer_id = NULL)
    {
        $query = DB::select(
            array('service.id', 'id'),
            array('customer.company_title', 'company_title'),
            array('customer.id', 'company_id'),
            array('service.type_id', 'type_id'),
            array('service_type.friendly_name', 'service_type'),
            array('service.url', 'url'),
            array('service.host_id', 'host_id'),
            array('service.control_panel_id', 'control_panel_id'),
            array('service.ip_address', 'ip_address'),
            array('service.price', 'price'),
            array('service.discount', 'discount'),
            array('service.billing_frequency_id', 'billing_frequency_id'),
            array('service.status_id', 'status_id'),
            array('service.years_paid', 'years_paid'),
            array('service.years_confirmed', 'years_confirmed'),
            array('service.date_start', 'date_start'),
            array('service.date_end', 'date_end'),
            array('service.referrer', 'referrer'),
            array('service.note', 'note'),
            array('service.date_modified', 'date_modified'),
            array('domain_type.friendly_name', 'domain_type'),
            array('domain_type.id', 'domain_type_id')
        )
            ->from(array(self::SERVICE_TABLE, 'service'))
            ->join(array(self::CUSTOMER_TABLE, 'customer'),'LEFT OUTER')
            ->on('service.company_id', '=', 'customer.id')
            ->join(array(self::DOMAIN_TYPE_TABLE, 'domain_type'))
            ->on('service.domain_type_id', '=', 'domain_type.id')
            ->join(array(self::SERVICE_TYPE_TABLE, 'service_type'))
            ->on('service.type_id', '=', 'service_type.id');

        if ($id != NULL) {
            $query->where('service.id', '=', $id);
        }

        // AND 'date_end' is between $from and $to
        if ($from)
            $query->and_where('service.date_end', '>', date("Y-m-d", strtotime($from)));

        if ($to)
            $query->and_where('service.date_end', '<', date("Y-m-d", strtotime($to)));

        $services = $query
            ->and_where('delete', '=', '0')
            ->order_by('service.date_end', 'ASC')
            ->execute()
            ->as_array();

        // Would be better if this could be added as an and_where above
        if ($customer_id != NULL) {
            foreach ($services as $key => $service) {
                if ($service['company_id'] != $customer_id) {
                    unset($services[$key]);
                }
            }
        }

        return ($id == NULL) ? $services : $services[0];
    }

    public static function get_payment_data($args)
    {
        $payments = DB::select(
            array('payment.id',                 'id'),
            array('payment.type_id',            'type_id'),
            array('service.url',                'url'),
            array('payment.amount',             'amount'),
            array('payment.service_id',         'service_id'),
            array('service.type_id',            'service_type_id'),
            array('service_type.name',          'service_type'),
            array('service_type.friendly_name', 'service_type_friendly_name'),
            array('payment.date',               'date')

        )
            ->from(array(self::PAYMENT_TABLE,      'payment'))
            ->join(array('plugin_extra_services',      'service'))     ->on('payment.service_id', '=', 'service.id')
            ->join(array('plugin_extra_service_types', 'service_type'))->on('service.type_id',    '=', 'service_type.id')
            ->where('payment.publish',      '=', 1)
            ->where('payment.deleted',      '=', 0)
            ->where('service.publish',      '=', 1)
            ->where('service.delete',       '=', 0)
            ->where('service_type.publish', '=', 1)
            ->where('service_type.deleted', '=', 0);

        if (isset($args['customer_id']) AND ! is_null($args['customer_id']) AND $args['customer_id'] > 0)
        {
            $payments = $payments->where('service.company_id', '=', $args['customer_id']);
        }

        $payments = $payments->execute()->as_array();

        if (isset($args['payment_id']))
        {
            return (isset($payments[0])) ? $payments[0] : NULL;
        }
        else
        {
            return $payments;
        }

    }

    public static function get_sprints2($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = "WHERE  
              (
                plugin_extra_projects_sprints2.id like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.jira_sprint_id like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.customer like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.sprint like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.summary like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.budget like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.spent like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.balance like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.progress like '%" . $search . "%'
              OR
                plugin_extra_projects_status_types.name like '%" . $search . "%'
              OR
                plugin_extra_projects_sprints2.last_synced like '%" . $search . "%'
              ) AND plugin_extra_projects_sprints2.deleted = 0";
        }
        $_limit = ($limit > -1) ? ' LIMIT ' . $offset . ', ' . $limit : '';
        $sprint_status_types = self::get_sprint2_status_types();
        $query = DB::query(
            Database::SELECT, 'SELECT `plugin_extra_projects_sprints2`.`id`, `plugin_extra_projects_sprints2`.`jira_sprint_id`,
            `plugin_extra_projects_sprints2`.`customer`, `plugin_extra_projects_sprints2`.`sprint`, `plugin_extra_projects_sprints2`.`summary`, 
            `plugin_extra_projects_sprints2`.`budget`, `plugin_extra_projects_sprints2`.`spent`, `plugin_extra_projects_sprints2`.`balance`, 
            `plugin_extra_projects_sprints2`.`progress`, `plugin_extra_projects_sprints2`.`last_synced`,
            `plugin_extra_projects_status_types`.`id` AS "status_type_id", `plugin_extra_projects_status_types`.`name` AS "status_type_name"
            FROM `plugin_extra_projects_sprints2` INNER JOIN `plugin_extra_projects_status_types` 
            ON `plugin_extra_projects_sprints2`.`project_status_type_id` = `plugin_extra_projects_status_types`.`id` '
            . $_search . '
            ORDER BY  ' . $sort . ' ' . $dir . $_limit
        )->execute()->as_array();
        for ($i = 0; $i < count($query); $i++) {
            $query[$i]['last_synced'] = "{$query[$i]['last_synced']}&nbsp;&nbsp;<a href='/admin/extra/sync_sprint2?jira_sprint_id={$query[$i]['jira_sprint_id']}'>Sync Now</a>";
            $query[$i]['summary'] = "{$query[$i]['summary']}&nbsp;&nbsp;<a class='edit-summary-button' val='{$query[$i]['id']}'>Edit</a>";
            $query[$i]['budget'] = "{$query[$i]['budget']}&nbsp;&nbsp;<a class='edit-budget-button' val='{$query[$i]['id']}'>Edit</a>";
            $query[$i]['progress'] = "{$query[$i]['progress']}&nbsp;&nbsp;<a class='edit-progress-button' val='{$query[$i]['id']}'>Edit</a>";
            $query[$i]['status'] = "<select class='status-dropdown' val='{$query[$i]['id']}'>";
            foreach ($sprint_status_types as $sprint_type) {
                $selected = ($query[$i]['status_type_id'] == $sprint_type['id']) ? "selected = 'selected'" : "";
                // Select the sprint type id as default select by the user.
                $query[$i]['status'] .= "<option value='{$sprint_type['id']}' {$selected}>{$sprint_type['name']}</option:>";
            }
            $query[$i]['status'] .= "</select>";
        }
        return $query;
    }

    public static function get_sprint2($sprint_id)
    {
        return DB::select()->from(SELF::SPRINTS_TABLE2)
                ->where('id', '=', $sprint_id)->execute()->as_array()[0] ?? false;
    }

    public static function get_sprint2_status_types()
    {
        return DB::select()->from(SELF::SPRINT2_STATUS_TYPES_TABLE)
            ->execute()->as_array();
    }

    public function add_service($data)
    {
        //header('content-type:text/plain; charset=utf-8');print_r($data);
        $id = DB::insert(self::SERVICE_TABLE, array_keys($data))
            ->values($data)
            ->execute();
        if(isset($id[0])){
            $service_id = $id[0];
        } else {
            $service_id = false;
        }
        return $service_id;
    }

    public function edit_service($data)
    {
        $query = DB::update(self::SERVICE_TABLE)
            ->set($data)
            ->where('id', '=', $data['id'])
            ->execute();

        return $query;
    }

    public function delete_service($id)
    {
        $query = DB::update(self::SERVICE_TABLE)
            ->set(array('delete' => 1))
            ->where('id', '=', $id)
            ->execute();

        return $query;
    }

    public function edit_hosts($new_host)
    {
        $date = date('Y-m-d');
        $query = DB::insert(self::HOST_TABLE)
                ->columns(array('name', 'friendly_name', 'date_created', 'publish', 'deleted'))
                ->values(array($new_host, $new_host, $date, 1, 0));
           // ->set($new_host)//
           // ->where('id', '=', $data['id'])
            $query->execute();
    }

    public function get_update_data($data)
    {
        $logged_in_user = Auth::instance()->get_user();

        $years_paid = @$data['years_paid'];
        $years_confirmed = @$data['years_confirmed'];
        $update_data['years_paid'] = '';
        $update_data['years_confirmed'] = '';
        for ($year = 2009; $year <= date('Y') + 10; $year++)
        {
            if (isset($years_paid[$year]) AND $years_paid[$year] != '')
            {
                $update_data['years_paid'].= $year . '|';
            }
            if (isset($years_confirmed[$year]) AND $years_confirmed[$year] != '')
            {
                $update_data['years_confirmed'].= $year . '|';
            }
        }
        $update_data['years_paid']           = substr($update_data['years_paid'], 0, -1);
        $update_data['years_confirmed']      = substr($update_data['years_confirmed'], 0, -1);

        $update_data['company_id']           = $data['company_id'];
        $update_data['url']                  = $data['url'];
        if ($data['date_start'] != '')
        {
            $update_data['date_start']       = date('Y-m-d H:i:s', strtotime($data['date_start']));
        }
        if ($data['date_end'] != '')
        {
            $update_data['date_end']         = date('Y-m-d H:i:s', strtotime($data['date_end']));
        }
        $update_data['type_id']              = $data['type_id'];
        $update_data['domain_type_id']       = $data['domain_type_id'];
        $update_data['host_id']              = $data['host_id'];
        $update_data['control_panel_id']     = $data['control_panel_id'];
        $update_data['ip_address']           = $data['ip_address'];
        $update_data['price']                = $data['price'];
        $update_data['discount']             = $data['discount'];
        $update_data['billing_frequency_id'] = $data['billing_frequency_id'];
        $update_data['payment_id']           = $data['payment_id'];
        $update_data['status_id']            = $data['status_id'];
        $update_data['publish']              = 1;
        $update_data['delete']               = 0;
        $update_data['modified_by']          = $logged_in_user['id'];
        $update_data['date_modified']        = date('Y-m-d H:i:s');
        $update_data['referrer']             = $data['referrer'];
        $update_data['note']                 = (isset($data['note'])) ? $data['note'] : '';
        $update_data['auto_renew']           = (isset($data['auto_renew'])) ? $data['auto_renew'] : 0;

        if (isset($data['id']))
        {
            $update_data['id'] = $data['id'];
        }
        else
        {
            $update_data['created_by']     = $logged_in_user['id'];
            $update_data['date_created']   = date('Y-m-d H:i:s');
        }

        return $update_data;
    }

    public function get_dropdowns_data()
    {
        $data['customers']           = self::get_published_data(self::CUSTOMER_TABLE,'company_title');
        $data['hosts']               = self::get_published_data(self::HOST_TABLE);
        $data['control_panels']      = self::get_published_data(self::CONTROL_PANEL_TABLE);
        $data['domain_types']        = self::get_published_data(self::DOMAIN_TYPE_TABLE);
        $data['service_types']       = self::get_published_data(self::SERVICE_TYPE_TABLE);
        $data['statuses']            = self::get_published_data(self::STATUS_TABLE);
        $data['billing_frequencies'] = self::get_published_data(self::BILLING_FREQUENCY_TABLE);
        $data['payment_type']        = self::get_published_data(self::PAYMENT_TYPE_TABLE);

        return $data;
    }

    public function get_published_data($table,$order_by = null)
    {
        //set to ID if no order by is set
        if($order_by == NULL){
            $order_by = 'ID';

        }
        return DB::select()->from($table)->where('publish', '=', 1)->and_where('deleted', '=', 0)->order_by($order_by)->execute();
    }

    public function validate_service($data)
    {
        $return = TRUE;
        if($data['type_id'] == '' OR $data['type_id'] == '0')
        {
            IbHelpers::set_message('Please select a service.', 'error');
            $return = FALSE;
        }

        if (!$data['price'] AND $return) {
            IbHelpers::set_message('Please set price.', 'error');
            $return = FALSE;
        }

        if ($return)
        {
            IbHelpers::set_message('Service successfully saved.', 'success');
        }

        return $return;
    }

    public function get_notes($id)
    {
        // TODO: create view
        $return = DB::select(
            array('note.id', 'id'),
            array('note.table_link_id', 'table_link_id'),
            array('note.link_id', 'link_id'),
            array('note.note', 'note'),
            array('note.added_by', 'added_by_id'),
            array('adder.name', 'added_by'),
            array('note.edited_by', 'edited_by_id'),
            array('editor.name', 'edited_by'),
            array('note.date_added', 'date_added'),
            array('note.date_edited', 'date_edited'),
            array('note.added_by', 'added_by_id'),
            array('note.deleted', 'deleted')
        )
            ->from(array('plugin_extra_notes', 'note'))
            ->join(array('engine_users', 'adder'))
            ->on('note.added_by', '=', 'adder.id')
            ->join(array('engine_users', 'editor'))
            ->on('note.edited_by', '=', 'editor.id')
            ->where('note.deleted', '!=', 1);

        if ( ! is_null($id))
        {
            $return = $return->and_where('note.id', '=', $id);
        }

        $return = $return
            ->order_by('note.date_edited', 'DESC')
            ->order_by('note.date_added', 'DESC')
            ->order_by('note.id', 'DESC')
            ->execute()
            ->as_array();

        if ( ! is_null($id)) {
            return $return[0];
        }
        else {
            return $return;
        }
    }

    public function get_service_notes($service_id)
    {
        // TODO: create view
        $return = DB::select(
            array('note.id', 'id'),
            array('note.link_id', 'link_id'),
            array('note.note', 'note'),
            array('note.added_by', 'added_by_id'),
            array('adder.name', 'added_by'),
            array('note.edited_by', 'edited_by_id'),
            array('editor.name', 'edited_by'),
            array('note.date_added', 'date_added'),
            array('note.date_edited', 'date_edited'),
            array('note.added_by', 'added_by_id'),
            array('note.deleted', 'deleted')
        )
            ->from(array('plugin_extra_notes', 'note'))
            ->join(array('engine_users', 'adder'))
            ->on('note.added_by', '=', 'adder.id')
            ->join(array('engine_users', 'editor'))
            ->on('note.edited_by', '=', 'editor.id')
            ->where('note.link_id', '=', $service_id)
            ->and_where('note.table_link_id', '=', '1')
            ->and_where('note.deleted', '!=', 1)
            ->order_by('note.date_edited', 'DESC')
            ->order_by('note.date_added', 'DESC')
            ->order_by('note.id', 'DESC')
            ->execute()
            ->as_array();
        return $return;
    }


    public function add_note($data, $user_id = FALSE)
    {
        //Get the user ID, the code could be note clear enough but this is because at some point we are callin this function for an external project, example: yachtdemo
        if (!$user_id)
        {
            $user_data = Auth::instance()->get_user();
            if (!$user_data) {
                $user_id = 1; //System
            }
            else {
                $user_id = $user_data['id'];
            }
        }
        else {
            $user_id = 1; //System
        }

        try
        {
            $query = DB::insert('plugin_extra_notes')
                ->values(
                    array(
                        'link_id'       => $data['link_id'],
                        'table_link_id' => $data['type'],
                        'added_by'      => $user_id,
                        'edited_by'     => $user_id,
                        'note'          => $data['notes'],
                        'date_edited'   => DB::expr('NOW()'),
                        'date_added'    => DB::expr('NOW()')
                    )
                )
                ->execute();

            return 'success';

        }
        catch (exception $e)
        {
            return 'error';
        }
    }

    public function update_note($id, $notes)
    {
        $user_data = Auth::instance()->get_user();
        $user_id = $user_data['id'];

        try
        {
            $query = DB::update('plugin_extra_notes')
                ->set(array('note' => $notes, 'date_edited' => DB::expr('NOW()'), 'edited_by' => $user_id))
                ->where('id', '=', $id)
                ->execute();

            return 'success';

        } catch (exception $e)
        {
            return 'error';
        }
    }

    public static function update_payment_details($services)
    {
        throw new Exception('update_payment_details is not used anymore');
        foreach($services AS $service)
        {
            if(strtotime($service['date_end']) < time()){
                $date_start = date('Y-m-d H:i:s');
            } else {
                $date_start = $service['date_end'];
            }
            $date = self::get_frequency_date($service['billing_frequency_name'], $date_start);
            $data = array('date_end' => $date);
            if($service['status_id'] == 2 || $service['status_id'] == 4){
                $data['status_id'] = 1;
            }
            DB::update(self::SERVICE_TABLE)->set($data)->where('id', '=', $service['id'])->execute();
        }
    }

    /**
     * @param $frequency
     * @param $date
     * @return bool|string
     */
    public static function get_frequency_date($frequency, $date, $quantity = 1)
    {
        static $format = 'Y-m-d H:i:s';
        $result = '';
        $date = strtotime($date);

        switch($frequency)
        {
            case 'monthly':
                $result = date($format, strtotime('+' . $quantity . ' month', $date));
                break;
            case 'quarterly':
                $result = date($format, strtotime('+' . ($quantity * 3) . ' month', $date));
                break;
            case 'yearly':
                $result = date($format, strtotime('+' . $quantity . ' year', $date));
                break;
            case 'once_off':
                $result = '';
        };

        return $result;
    }
    
    public static function get_frequency_interval($frequency, $quantity = 1)
    {
        switch($frequency)
        {
            case 'monthly':
                $result = $quantity . ' month';
                break;
            case 'quarterly':
                $result = (3 * $quantity) . ' month';
                break;
            case 'yearly':
                $result = $quantity . ' year';
                break;
            case 'once_off':
                $result = '';
        };

        return $result;
    }

    public static function bulk_refresh_expiry()
    {
       $q = DB::select('id','url')->from(self::SERVICE_TABLE)->where('publish','=',1)->and_where('delete','=',0)->execute()->as_array();
        $result = '';
        foreach($q AS $key=>$domain)
        {
            $result.=$domain['url'].'  -   ';
            $expiry_date = Kohana_Whois::instance()->lookup($domain['url']);
            if(isset($expiry_date['regrinfo']['domain']['expires']))
            {
                DB::update(self::SERVICE_TABLE)->set(array('date_end' => date('Y-m-d H:i:s',strtotime($expiry_date['regrinfo']['domain']['expires']))))->where('id','=',$domain['id'])->execute();
            }
        }
        return $result;
    }


    /**
     * Purpose : To return an array of services that have expired with the given offset.
     * Usage : send 30 days and offset minus to true to get date that expired from 30 days before today.
     *
     * @param int  $offset_days
     * @param bool $offset_minus
     * @param null $expiry_date
     * @return array of services for view to render
     */
    public static function get_services_expired($offset_days = 0, $offset_minus = FALSE, $expiry_date = NULL)
    {
        static $format = 'Y-m-d H:i:s';
        $result = '';

        $todays_date = date($format);

        if ($offset_minus)
        {
            $offset_minus = '-';
        }
        else
        {
            $offset_minus = '+';
        }

        //set end date offset to search for
        if (!$expiry_date)
        {
            // use todays date if no date specified
            $expiry_date = date($format, strtotime($todays_date . $offset_minus . ' ' . $offset_days . ' days'));
        }
        else
        {
            $expiry_date = date($format, strtotime($offset_minus . ' ' . $offset_days . ' days', $expiry_date));

        }

        // select from database where expiry date is within offset criteria
        $result = DB::select('id', 'url', 'date_end','company_id','price')->from(self::SERVICE_TABLE)->where('publish', '=', 1)->and_where('delete', '=', 0)->and_where('date_end', '<=', $expiry_date)->order_by('date_end')->execute()->as_array();

        return $result;
    }

    public static function add_extra_payment($payment_log_id, $service_id, $amount, $type_id = 0)
    {
        
        $id = DB::insert(self::PAYMENT_TABLE, array('payment_log_id', 'service_id', 'amount', 'date', 'publish', 'deleted', 'type_id'))
                    ->values(array($payment_log_id, $service_id, $amount, date('Y-m-d'), 1, 0, 0))
                    ->execute();
        if($id){
            return $id[0];
        } else {
            return false;
        }
    }
    
    public static function get_service_type($service_type_id)
    {
        $service_type = DB::select('*')->from(self::SERVICE_TYPE_TABLE)->where('id', '=', $service_type_id)->execute()->as_array();
        if($service_type){
            return $service_type[0];
        } else {
            return false;
        }
    }
    
    public static function get_billing_frequency($billing_frequency_id)
    {
        $billing_frequency = DB::select('*')->from(self::BILLING_FREQUENCY_TABLE)->where('id', '=', $billing_frequency_id)->execute()->as_array();
        if($billing_frequency){
            return $billing_frequency[0];
        } else {
            return false;
        }
    }

    public static function create_invoice($service_id, $date_from, $date_to, $due_date, $status, $amount = null, $bullethq_save = true)
    {
        $service = DB::select('*')
            ->from(self::SERVICE_TABLE)
            ->where('id', '=', $service_id)
            ->execute()
            ->current();

        $invoice_id = null;
        if($service){
            if ($amount === null) {
                $amount = (float)$service['price'];
                if ($service['discount']) {
                    $amount -= $service['discount'];
                }
            }
            $bullethq_id = null;
            $bullethq_token = null;

            if ($bullethq_save) {
                $vat_rate = (float)Settings::instance()->get('vat_rate');
                $bullethq_client_id = Model_Customers::get_bullethq_id($service['company_id'], true);
                if ($bullethq_client_id) {
                    $binvoice = array();
                    $binvoice['clientId'] = $bullethq_client_id;
                    $binvoice['invoiceNumber'] =
                        str_pad($service['company_id'], 8, '0', STR_PAD_LEFT) .
                        '-' .
                        str_pad($service_id, 8, '0', STR_PAD_LEFT) . '-' . uniqid();
                    switch (Kohana::$environment) {
                        case    Kohana::DEVELOPMENT:
                            $binvoice['invoiceNumber'] .= '.development';
                            break;
                        case    Kohana::TESTING:
                            $binvoice['invoiceNumber'] .= '.testing';
                            break;
                        case    Kohana::STAGING:
                            $binvoice['invoiceNumber'] .= '.staging';
                            break;
                    }
                    $binvoice['currency'] = 'EUR';
                    $binvoice['issueDate'] = date('Y-m-d H:i:s');
                    $binvoice['dueDate'] = $due_date;
                    $binvoice['currency'] = 'EUR';
                    $binvoice['draft'] = false;
                    $binvoice['invoiceLines'] = array();
                    $price = $amount;
                    if ($vat_rate) {
                        $rate = round($price / (1 + $vat_rate), 2);
                    } else {
                        $rate = $price;
                    }
                    $service_type = self::get_service_type($service['type_id']);
                    $billing_frequency = self::get_billing_frequency($service['billing_frequency_id']);
                    $binvoice['invoiceLines'][] = array(
                        'item' => $service_type['friendly_name'],
                        'description' => $service['url'] . ' ' .
                            $service_type['friendly_name'] . ' ' .
                            $billing_frequency['friendly_name'] . ' ' .
                            $date_from . ', ' . $date_to,
                        'quantity' => 1,
                        'rate' => $rate,
                        'vatRate' => $vat_rate
                    );
                    $bullethq = new Model_BulletHQ();
                    $bullethq_response = $bullethq->create_invoice($binvoice, false);
                    $bullethq_id = $bullethq_response['id'];
                    $bullethq_token = $bullethq_response['token'];
                }
            }
            $invoice_id = self::add_invoice(
                $amount,
                $service_id,
                $bullethq_id,
                $bullethq_token,
                date('Y-m-d H:i:s'),
                $due_date,
                $date_from,
                $date_to,
                $status
            );
        }
        return $invoice_id;
    }

    public static function add_invoice(
        $amount,
        $service_id,
        $bullethq_id,
        $bullethq_token,
        $created,
        $due_date,
        $date_from,
        $date_to,
        $status
    ){
        $result = DB::insert(self::INVOICE_TABLE, 
                            array(
                                'amount',
                                'service_id',
                                'bullethq_id',
                                'bullethq_token',
                                'created',
                                'due_date',
                                'publish',
                                'deleted',
                                'date_from',
                                'date_to',
                                'status'))
                        ->values(array(
                            $amount,
                            $service_id,
                            $bullethq_id,
                            $bullethq_token,
                            $created,
                            $due_date,
                            1,
                            0,
                            $date_from,
                            $date_to,
                            $status))
                        ->execute();
        return isset($result[0]) ? $result[0] : false;
    }
    
    public static function list_invoices($customer_id)
    {
        return DB::select('invoices.*')
                    ->from(array(self::INVOICE_TABLE, 'invoices'))
                        ->join(array(self::SERVICE_TABLE, 'services'), 'inner')->on('invoices.service_id', '=', 'services.id')
                    ->where('invoices.deleted', '=', 0)
                    ->execute()
                    ->as_array();
    }
    
    public static function list_invoices_b($customer_id = null, $date_from = null, $date_to = null)
    {
        $invoicesq = DB::select('invoices.*', 
                                'customers.company_title', 
                                'services.url', 
                                array('services.type_id', 'service_type_id'),
                                array('stypes.friendly_name', 'service_type'))
                        ->from(array(self::INVOICE_TABLE, 'invoices'))
                            ->join(array(self::SERVICE_TABLE, 'services'), 'inner')->on('invoices.service_id', '=', 'services.id')
                            ->join(array(self::CUSTOMER_TABLE, 'customers'), 'inner')->on('services.company_id', '=', 'customers.id')
                            ->join(array(self::SERVICE_TYPE_TABLE, 'stypes'), 'inner')->on('services.type_id', '=', 'stypes.id')
                    ->where('invoices.deleted', '=', 0);
        if($customer_id != null){
            $invoicesq->and_where('services.company_id', '=', $customer_id);
        }
        if($date_from != null){
            $invoicesq->and_where('invoices.created', '>=', $date_from);
        }
        if($date_to != null){
            $invoicesq->and_where('invoices.created', '<=', $date_to);
        }
        $invoices = $invoicesq->order_by('invoices.created', 'desc')->execute()->as_array();
        
        $bullethq = new Model_BulletHQ();
        $bullethqb = new Model_BulletHQB();
        $binvoices = $bullethq->list_invoices();
        foreach($invoices as $i => $invoice){
            $invoices[$i]['bullethq'] = null;
            if ($invoice['bullethq_id'] != '' && $invoice['bullethq_token'] == 0) {
                $bullethq_token = $bullethqb->get_invoice_token($invoice['bullethq_id']);
                if ($bullethq_token) {
                    $invoices[$i]['bullethq_token'] = $bullethq_token;
                    DB::update(self::INVOICE_TABLE)
                        ->set(array('bullethq_token' => $bullethq_token))
                        ->where('bullethq_id', '=', $invoice['bullethq_id'])
                        ->execute();
                }
            }
            foreach($binvoices as $bi => $binvoice){
                if($binvoice['id'] == $invoice['bullethq_id']){
                    $invoices[$i]['bullethq'] = $binvoice;
                    break;
                }
            }

        }
        return $invoices;
    }

    public static function getInvoice($invoiceId)
    {
        $invoice = DB::select(
            'invoices.*',
            'services.url',
            array('services.type_id', 'service_type_id'),
            array('stype.friendly_name', 'service_type'),
            array('sstatus.friendly_name', 'service_status'),
            array('frequency.friendly_name', 'billing_frequency')
        )
            ->from(array(self::INVOICE_TABLE, 'invoices'))
                ->join(array(self::SERVICE_TABLE, 'services'), 'INNER')
                    ->on('invoices.service_id', '=', 'services.id')
                ->join(array(self::SERVICE_TYPE_TABLE, 'stype'), 'INNER')
                    ->on('services.type_id', '=', 'stype.id')
                ->join(array(self::STATUS_TABLE, 'sstatus'), 'INNER')
                    ->on('services.status_id', '=', 'sstatus.id')
                ->join(array(self::BILLING_FREQUENCY_TABLE, 'frequency'), 'INNER')
                    ->on('services.billing_frequency_id', '=', 'frequency.id')
            ->where('invoices.id', '=', $invoiceId)
            ->execute()
            ->current();
        return $invoice;
    }

    public static function auto_invoice_services()
    {
        DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_invoices')->execute();
        DB::query(null, 'CREATE TEMPORARY TABLE tmp_invoices AS (
SELECT MAX(date_to) AS date_to, service_id
    FROM plugin_extra_invoices
    WHERE deleted=0 AND `status` <> \'Cancelled\'
    GROUP BY service_id)')->execute();
        DB::query(null, 'ALTER TABLE tmp_invoices ADD INDEX (`service_id`)')->execute();

        $services = DB::select(
                'services.*',
                DB::expr('COUNT(*) AS has_cards'),
                'tmp_invoices.date_to',
                array('billing_frequencies.name', 'billing_frequency_name')
        )
            ->from(array(self::SERVICE_TABLE, 'services'))
                ->join(array(self::BILLING_FREQUENCY_TABLE, 'billing_frequencies'), 'inner')
                    ->on('services.billing_frequency_id', '=', 'billing_frequencies.id')
                ->join(array(self::RVCARDS_TABLE, 'cards'), 'INNER')
                    ->on('services.company_id', '=', 'cards.customer_id')
                ->join('tmp_invoices', 'LEFT')
                    ->on('services.id', '=', 'tmp_invoices.service_id')
            ->where('services.delete', '=', 0)
            ->and_where('services.publish', '=', 1)
            ->and_where('services.auto_renew', '=', 1)
            ->and_where('cards.expdate', '>', date('Y-m-d'))
            ->and_where_open()
                ->or_where('tmp_invoices.date_to', '<', DB::expr('NOW()'))
                ->or_where('tmp_invoices.service_id', 'is', null)
            ->and_where_close()
            ->group_by('services.company_id')
            ->execute()
            ->as_array();
        foreach ($services as $service) {
            if ($service['has_cards'] > 0) {
                $date_from = date('Y-m-d');
                $date_to = self::get_frequency_date($service['billing_frequency_name'], $date_from, 1);
                $due_date = date('Y-m-d', strtotime('+7 days'));
                self::create_invoice(
                    $service['id'],
                    $date_from,
                    $date_to,
                    $due_date,
                    'Unpaid'
                );
            }
        }
    }

    public static function auto_pay_invoices()
    {
        $invoices = DB::select(
                'invoices.*',
                'services.url',
                'services.company_id',
                'customers.user_id',
                'customers.company_title',
                'customers.phone',
                array('services.type_id', 'service_type_id'),
                array('service_types.friendly_name', 'service_type'),
                array('billing_frequencies.name', 'billing_frequency_name')
        )
            ->from(array(self::INVOICE_TABLE, 'invoices'))
                ->join(array(self::SERVICE_TABLE, 'services'))
                    ->on('services.id', '=', 'invoices.service_id')
                ->join(array(self::CUSTOMER_TABLE, 'customers'))
                    ->on('services.company_id', '=', 'customers.id')
                ->join(array(self::SERVICE_TYPE_TABLE, 'service_types'), 'inner')
                    ->on('services.type_id', '=', 'service_types.id')
                ->join(array(self::BILLING_FREQUENCY_TABLE, 'billing_frequencies'), 'inner')
                    ->on('services.billing_frequency_id', '=', 'billing_frequencies.id')
            ->where('invoices.deleted', '=', 0)
            ->and_where('invoices.status', '=', 'Unpaid')
            ->and_where('services.delete', '=', 0)
            ->execute()
            ->as_array();

        foreach ($invoices as $invoice) {
            $cards = Model_Customers::get_cards($invoice['company_id']);
            foreach ($cards as $card) {
                $charged = self::auto_pay_invoice($invoice, $card);
                if ($charged) {
                    break;
                }
            }
        }
    }

    public static function auto_pay_invoice($invoice, $card)
    {
        $rvPayerId = Model_Customers::get_realvault_id($invoice['company_id']);
        $contact = Model_Customers::get_main_contact($invoice['company_id']);

        if ($rvPayerId) {
            $realvault = new Model_Realvault();
            $realvaultResult = $realvault->charge_card(
                $rvPayerId,
                $card['realvault_id'],
                'ib-extra-invoice-' . $invoice['id'],
                $invoice['amount'],
                'EUR',
                $card['cv']);
            if ((string)$realvaultResult->result == '00') {
                DB::update(self::INVOICE_TABLE)
                    ->set(array('status' => 'Paid'))
                    ->where('id', '=', $invoice['id'])
                    ->execute();

                $cart = array();
                $cart['id'] = 'ib-' . $invoice['company_id'] . '-' . time();
                $cart['user_agent'] = 'cron';
                $cart['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $cart['user_id'] = $invoice['user_id'];
                $cart['cart_data'] = '';
                $cart['form_data'] = '';
                $cart['paid'] = 0;
                $cart['date_created'] = $cart['date_modified'] = date('Y-m-d H:i:s');

                $cartItems = array();
                $cartItem = array(
                    'cart_id' => $cart['id'],
                    'id' => $invoice['service_type_id'],
                    'title' => $invoice['service_type'] . ' ' .
                        $invoice['url'] . ' ' .
                        $invoice['date_from'] . ', ' .
                        $invoice['date_to'],
                    'quantity' => 1,
                    'price' => $invoice['amount'],
                    'delete' => 0
                );
                $cartItems[] = $cartItem;

                $cart['cart_data'] = json_encode($cartItems);

                DB::insert(
                    Model_Cart::CART_TABLE,
                    array_keys($cart)
                )->values($cart)
                    ->execute();
                foreach ($cartItems as $cartItem) {
                    DB::insert(
                        Model_Cart::CART_PRODUCTS_TABLE,
                        array_keys($cartItem)
                    )->values($cartItem)
                        ->execute();
                }

                $payment_log_result = DB::insert(
                    'plugin_payments_log',
                    array(
                        'cart_details',
                        'customer_name',
                        'customer_telephone',
                        'customer_email',
                        'paid',
                        'payment_type',
                        'payment_amount',
                        'customer_user_id',
                        'delivery_method',
                        'cart_id',
                        'ip_address',
                        'user_agent',
                        'purchase_time'
                    )
                )->values(
                    array(
                        json_encode($cart),
                        $invoice['company_title'],
                        $invoice['phone'],
                        '',
                        '1',
                        'realvault',
                        $invoice['amount'],
                        $invoice['user_id'],
                        'online',
                        $cart['id'],
                        $_SERVER['REMOTE_ADDR'],
                        'cron',
                        date('Y-m-d H:i:s')
                    )
                )->execute();

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function auto_renew_services_deprecated()
    {
        $services = DB::select('services.*', 
                                'customers.user_id',
                                'customers.company_title', 
                                'customers.phone', 
                                'customers.contact', 
                                'customers.billing_contact', 
                                'customers.bullethq_id', 
                                'realvault.customer_id', 
                                'realvault.realvault_id', 
                                array('products.id', 'product_id'), 
                                array('service_types.friendly_name', 'service_type'),
                                array('billing_frequencies.name', 'billing_frequency_name'))
                        ->from(array(self::SERVICE_TABLE, 'services'))
                            ->join(array(self::CUSTOMER_TABLE, 'customers'), 'inner')->on('services.company_id', '=', 'customers.id')
                            ->join(array('plugin_extra_realvault_payers', 'realvault'), 'inner')->on('customers.id', '=', 'realvault.customer_id')
                            ->join(array(self::SERVICE_TYPE_TABLE, 'service_types'), 'inner')->on('services.type_id', '=', 'service_types.id')
                            ->join(array(self::BILLING_FREQUENCY_TABLE, 'billing_frequencies'), 'inner')->on('services.billing_frequency_id', '=', 'billing_frequencies.id')
                            ->join(array(Model_Product::MAIN_TABLE, 'products'), 'inner')->on('service_types.friendly_name', '=', 'products.title')
                        ->where('services.auto_renew', '=', 1)
                        ->execute()
                        ->as_array();
        $today = strtotime(date('Y-m-d 00:00:00'));
        foreach($services as $service){
            if($service['billing_frequency_name'] != 'once_off' && strtotime($service['date_end']) <= $today && $service['status_id'] == 1){
                $cards = Model_Customers::get_cards($service['customer_id']);
                if(count($cards) > 0){
                    foreach($cards as $card){
                        if(self::renew_service($service, $card)){
                            break;
                        }
                    }
                }
            }
        }
    }
    
    public static function renew_service_deprecated($service, $card, $quantity = 1)
    {
        $service_id = $service['id'];
        $customer_id = $service['customer_id'];
        $contact = new Model_Contacts($service['contact']);
        $contact = $contact->get_details();
        if($service['billing_contact']){
            $billing_contact = new Model_Contacts($service['billing_contact']);
            $billing_contact = $billing_contact->get_details();
        } else {
            $billing_contact = $contact;
        }
        $price = $service['price'];
        $total = $quantity * $price;
        $vat_rate = (float)Settings::instance()->get('vat_rate');
        $realvault = new Model_Realvault();
        $order_id =  str_pad($service['company_id'], 8, '0', STR_PAD_LEFT) . '-' . str_pad($service['id'], 8, '0', STR_PAD_LEFT) . '-' . uniqid();
        $bullethq_client_id = $service['bullethq_id'];
        
        Model_Checkout::empty_cart();
        $checkout = new Model_Checkout();

        $pitem = new stdclass();
        $pitem->service_id = (int)$service['id'];
        $pitem->product_id = $pitem->id = (int)$service['product_id'];
        $pitem->description = $service['url'];
        $pitem->item = $service['service_type'];
        $pitem->price = ($vat_rate ? round($price / (1 + $vat_rate), 2) : $price);
        $pitem->quantity = 1;
        $pitem->options = new stdclass();
        /////$post->products[] = $pitem;
        $checkout->add_to_cart($pitem, $pitem->price);
            
        $checkout_params = new stdclass();
        $checkout_params->store_id = 1;
        $checkout_params->delivery_method = 'online';
        $checkout->set_store_id($checkout_params);
        $checkout->set_delivery_method($checkout_params);
        $checkout->save_to_session();
        $cart = $checkout->get_cart_details();
        $bullethq = new Model_BulletHQ();
        if(!$bullethq_client_id){
            $bclient = array();
            $bclient['name'] = $service['company_title'];
            $bclient['email'] = $contact['email'];
            $bclient['phoneNumber'] = $service['phone'] ? $service['phone'] : $contact['phone'];
            $bullethq->add_client($bclient, $customer_id);
            $bullethq_client_id = Model_Customers::get_bullethq_id($customer_id);
        }
        try{
            $realvault_result = $realvault->charge_card($service['realvault_id'], 
                                                        $card['realvault_id'], 
                                                        $order_id, 
                                                        $total, 
                                                        'EUR',
                                                        $card['cv']);
            if((string)$realvault_result->result == '00'){
                if(strtotime($service['date_end']) < time()){
                    $date_start = date('Y-m-d H:i:s');
                } else {
                    $date_start = $service['date_end'];
                }
                $date_end = self::get_frequency_date($service['billing_frequency_name'], $date_start, $quantity);
                $date_end = array('date_end' => $date_end);
                if($service['status_id'] == 2 || $service['status_id'] == 4){
                    $date_end['status_id'] = 1;
                }
                DB::update(self::SERVICE_TABLE)->set($date_end)->where('id', '=', $service['id'])->execute();
            
                $binvoice = array();
                $binvoice['clientId'] = $bullethq_client_id;
                $binvoice['invoiceNumber'] = $order_id;
                $binvoice['currency'] = 'EUR';
                $binvoice['issueDate'] = date('Y-m-d');
                $binvoice['dueDate'] = date('Y-m-d');
                $binvoice['currency'] = 'EUR';
                $binvoice['draft'] = false;
                $binvoice['invoiceLines'] = array();
                if($vat_rate){
                    $rate = round($price / (1 + $vat_rate), 2);
                } else {
                    $rate = $price;
                }
                $billing_frequency = self::get_billing_frequency($service['billing_frequency_id']);
                $binvoice['invoiceLines'][] = array('item' => $service['service_type'], 
                                                    'description' => $service['url'] . ' ' . $service['service_type'] . ' ' . $billing_frequency['friendly_name'],
                                                    'quantity' => $quantity,
                                                    'rate' => $rate,
                                                    'vatRate' => $vat_rate);
                $invoice_response = $bullethq->create_invoice($binvoice, false);
                self::add_invoice($total, $service_id, $invoice_response['id'], $invoice_response['token'], date('Y-m-d H:i:s'), date('Y-m-d'));
                $bullethq_payment = array();
                $bullethq_payment['clientId'] = $bullethq_client_id;
                $bullethq_payment['bankAccountId'] = Settings::instance()->get('bullethq_bank_account_id');
                $bullethq_payment['amount'] = $total;
                $bullethq_payment['currency'] = 'EUR';
                $bullethq_payment['dateReceived'] = date('Y-m-d');
                $bullethq_payment['invoiceIds'] = array();
                $bullethq_payment['invoiceIds'][] = $invoice_response['id'];
                $bullethq_payment_result = $bullethq->add_payment($bullethq_payment);
                
                $payment_log_id = false;
                try{
                    $cart        = $checkout->get_cart();
                    $cart_report = new Model_Cart($cart->data->id);
                    $details     = array(
                        'id'            => $checkout->get_cart_id(),
                        'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
                        'ip_address'    => $_SERVER['REMOTE_ADDR'],
                        'user_id'       => $service['user_id'],
                        'cart_data'     => json_encode($cart),
                        'paid'          => 1,
                        'date_created'  => date('d-m-Y H:i:s',time()),
                        'date_modified' => date('d-m-Y H:i:s',time())
                    );
                    $cart_report->set($details);
                    $cart_report_products = new Model_Cartitems($cart_report->get_id());
                    if (isset($cart->data->lines))
                    {
                        $cart_report_products->set($cart->data->lines);
                    }
                    $cart_report->save();
                    $cart_report_products->save();
                    $payment_log_result = DB::insert('plugin_payments_log',array('cart_details','customer_name','customer_telephone','customer_email','paid','payment_type','payment_amount','customer_user_id', 'delivery_method', 'cart_id', 'ip_address', 'user_agent', 'purchase_time'))
                        ->values(array(json_encode($cart), 'realvault', $billing_contact['phone'], $billing_contact['email'], '1', 'realvault', $total, $service['user_id'], 'online', $cart->data->id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], date('Y-m-d H:i:s')))->execute();
                    if($payment_log_result){
                        $payment_log_id = $payment_log_result[0];
                        Model_Extra::add_extra_payment($payment_log_id, $service['id'], $service['price']);
                    }
                    $bullethqb = new Model_BulletHQB();
                    $bullethqb->email_invoice($invoice_response['id']);
                    return true;
                } catch (Exception $e){
                    throw $e;
                }
            } else {
                return false;
            }
        }catch(Exception $exc){
            print_r($exc);
        }
        return false;
    }
    
    protected static function get_services_to_expire($days)
    {
        if(!is_numeric($days)){
            return array();
        }
        $services = DB::select('services.*', 
                                'customers.user_id',
                                'customers.company_title', 
                                'customers.phone', 
                                'customers.contact', 
                                'customers.billing_contact', 
                                'customers.bullethq_id', 
                                'realvault.customer_id', 
                                'realvault.realvault_id', 
                                array('products.id', 'product_id'), 
                                array('service_types.friendly_name', 'service_type'),
                                array('billing_frequencies.name', 'billing_frequency_name'))
                        ->from(array(self::SERVICE_TABLE, 'services'))
                            ->join(array(self::CUSTOMER_TABLE, 'customers'), 'inner')->on('services.company_id', '=', 'customers.id')
                            ->join(array('plugin_extra_realvault_payers', 'realvault'), 'inner')->on('customers.id', '=', 'realvault.customer_id')
                            ->join(array(self::SERVICE_TYPE_TABLE, 'service_types'), 'inner')->on('services.type_id', '=', 'service_types.id')
                            ->join(array(self::BILLING_FREQUENCY_TABLE, 'billing_frequencies'), 'inner')->on('services.billing_frequency_id', '=', 'billing_frequencies.id')
                            ->join(array(Model_Product::MAIN_TABLE, 'products'), 'inner')->on('service_types.friendly_name', '=', 'products.title')
                        ->where('services.status_id', '=', 1)
                        ->and_where('services.date_end', '=', DB::expr('DATE_ADD(CURDATE(), INTERVAL ' . $days . ' DAY)'))
                        ->execute()
                        ->as_array();
        return $services;
    }

    public static function send_renew_reminders()
    {
        $services_30 = self::get_services_to_expire(30);
        foreach($services_30 as $service){
            self::send_reminder($service, 'service_expire_reminder_30_days');
        }
        $services_10 = self::get_services_to_expire(10);
        foreach($services_10 as $service){
            self::send_reminder($service, 'service_expire_reminder_10_days');
        }
        $services_0 = self::get_services_to_expire(0);
        foreach($services_0 as $service){
            self::send_reminder($service, 'service_expire_reminder_0_days');
        }
    }

    public static function send_reminder($service, $email_template)
    {
        $vat_rate = (float)Settings::instance()->get('vat_rate');
        $customer_model = new Model_Customers();
        $customer = $customer_model->get_customer_details($service['company_id']);

        $contact = new Model_Contacts($service['contact']);
        $contact = $contact->get_details();

        if($service['billing_contact']){
            $billing_contact = new Model_Contacts($service['billing_contact']);
            $billing_contact = $billing_contact->get_details();
        } else {
            $billing_contact = $contact;
        }

        $recipients = array();

        if($billing_contact){
            $recipients[] = array('target' => $billing_contact['id'], 'target_type' => 'CMS_CONTACT');
        } else {
            $recipients[] = array('target' => $contact['id'], 'target_type' => 'CMS_CONTACT');
        }

        $data = array();
        $data['contact_name'] = $billing_contact ? $billing_contact['first_name'] . ' ' . $billing_contact['last_name'] : $contact['first_name'] . ' ' . $contact['last_name'];
        $data['company_title'] = $customer['company_title'];
        $data['service_name'] = $service['url'];
        $data['service_type'] = $service['service_type'];
        $data['total'] = number_format($service['price'], 2);
        $data['vat'] = number_format($service['price'] - $service['price'] * (1 / (1 + $vat_rate)), 2);
        $data['price'] = $service['price'];
        $data['subtotal'] = number_format($service['price'] * (1 / (1 + $vat_rate)), 2);
        $data['date_end'] = date('D jS M Y', strtotime($service['date_end']));
        $data['today'] = date('D jS M Y');
        $data['credit'] = 0;

        $messaging = new Model_Messaging();
        $messaging->send_template($email_template, null, null, $recipients, $data);
    }
    
    public static function projects_list()
    {
        return DB::select('*')->from(self::PROJECT_TABLE)->order_by('title')->execute()->as_array();
    }
    
    public static function projects_report_month_list()
    {
        return DB::select(DB::expr("DISTINCT(DATE_FORMAT(started,'%Y-%m-01')) AS `month`"))
                    ->from('plugin_extra_projects_worklog')
                    ->order_by('started', 'desc')
                    ->execute()
                    ->as_array();
    }
    
    public static function projects_report_author_list()
    {
        return DB::select(DB::expr("DISTINCT(`author`) AS `author`"))
                    ->from('plugin_extra_projects_worklog')
                    ->order_by('author', 'asc')
                    ->execute()
                    ->as_array();
    }
    
    public static function projects_report($params = array())
    {
        $query = DB::select(
            array('projects.title', 'project'),
            DB::expr("DATE_FORMAT(worklog.started, '%Y-%m-01') AS `month`"),
            'worklog.author',
            DB::expr("SUM(worklog.time_spent) AS time_spent"),
            DB::expr("SUM(issues.timeoriginalestimate) AS timeoriginalestimate"),
            'issues.status',
            array('sprints.name', 'sprint')
        )
                        ->from(array(self::WORKLOG_TABLE, 'worklog'))
                            ->join(array(self::ISSUES_TABLE, 'issues'), 'inner')->on('worklog.issue_id', '=', 'issues.id')
                            ->join(array(self::PROJECT_TABLE, 'projects'), 'inner')->on('issues.project_id', '=', 'projects.id')
                            ->join(array(self::SPRINT_ISSUES_TABLE, 'sprint_issues'), 'left')->on('issues.id', '=', 'sprint_issues.issue_id')
                            ->join(array(self::SPRINTS_TABLE, 'sprints'), 'left')->on('sprint_issues.sprint_id', '=', 'sprints.id');
        if(@$params['project_id'] != null){
            $query->where('projects.id', '=', $params['project_id']);
        }
        if(@$params['author'] != null){
            $query->where('worklog.author', '=', $params['author']);
        }
        if(@$params['month'] != null){
            $query->where(DB::expr("DATE_FORMAT(worklog.started, '%Y-%m-01')"), '=', $params['month']);
        }
        if(@array_search('Project', $params['group_by']) !== false){
            $query->group_by('projects.id');
        }
        if(@array_search('Month', $params['group_by']) !== false){
            $query->group_by('month');
        }
        if(@array_search('Author', $params['group_by']) !== false){
            $query->group_by('author');
        }
        if(@array_search('Sprint', $params['group_by']) !== false){
            $query->group_by('sprint');
        }
        $worklog = $query->execute()->as_array();

        $fields = array(
            'projects.title',
            'COUNT(*) AS cnt',
            'issues.status'
        );
        if(@array_search('Author', $params['group_by']) !== false){
            $fields[] = 'worklog.author';
        }
        $query = DB::select(DB::expr(implode(',', $fields)))
                        ->from(array(self::ISSUES_TABLE, 'issues'))
                            ->join(array(self::PROJECT_TABLE, 'projects'), 'inner')->on('issues.project_id', '=', 'projects.id');
        if(@array_search('Author', $params['group_by']) !== false){
            $query->join(array(self::WORKLOG_TABLE, 'worklog'), 'inner')->on('issues.id', '=', 'worklog.issue_id');
            $query->group_by('worklog.author');
        }
        $query->group_by('projects.id');
        $query->group_by('issues.status');
        $issue_counts = array();
        foreach($query->execute()->as_array() as $issue_count){
            $issue_counts[$issue_count['title']][$issue_count['author']][] = $issue_count['status'] . '(' . $issue_count['cnt'] . ')';
        }
        return array('worklog' => $worklog, 'issue_counts' => $issue_counts);
    }

    public static function projects_report2($params = array())
    {
        $fields1 = array(
            'issues.id as issue_id'
        );
        if(@array_search('Month', $params['group_by']) !== false){
            $fields1[] = "DATE_FORMAT(worklog.started, '%Y-%m-01') AS `month`";
        }
        if(@array_search('Author', $params['group_by']) !== false){
            $fields1[] = "worklog.author";
        }
        $fields1[] = "sum(worklog.time_spent) as timespent";
        $ticket_time_spent = DB::select(
            DB::expr(implode(', ', $fields1))
        )
            ->from(array(self::ISSUES_TABLE, 'issues'))
                ->join(array(self::WORKLOG_TABLE, 'worklog'), 'inner')->on('issues.id', '=', 'worklog.issue_id')
            ->group_by('issues.id');
        if(@array_search('Month', $params['group_by']) !== false){
            $ticket_time_spent->group_by('month');
        }
        if(@array_search('Author', $params['group_by']) !== false){
            $ticket_time_spent->group_by("author");
        }

        //echo $ticket_time_spent;exit;

        $fields = array();
        if(@array_search('Project', $params['group_by']) !== false){
            $fields[] = 'projects.title as project';
        }
        if(@array_search('Sprint', $params['group_by']) !== false){
            $fields[] = "sprints.name as sprint";
        }
        if(@array_search('Month', $params['group_by']) !== false){
            $fields[] = "worklog.month";
        }
        if(@array_search('Author', $params['group_by']) !== false){
            $fields[] = "worklog.author";
        }
        if(@array_search('Status', $params['group_by']) !== false){
            $fields[] = "issues.status";
        }
        if(@array_search('Resolution', $params['group_by']) !== false){
            $fields[] = "issues.resolution";
        }
        if(@array_search('Ticket', $params['group_by']) !== false){
            $fields[] = "issues.jira_key";
        }
        $fields[] = "count(*) as ticket_count";
        $fields[] = "SUM(issues.timeoriginalestimate) as timeoriginalestimate";
        $fields[] = "worklog.timespent";

        $query = DB::select(
            DB::expr(implode(', ', $fields))
        )
            ->from(array(self::PROJECT_TABLE, 'projects'))
            ->join(array(self::ISSUES_TABLE, 'issues'), 'left')->on('issues.project_id', '=', 'projects.id')
            ->join(array($ticket_time_spent, 'worklog'), 'left')->on('issues.id', '=', 'worklog.issue_id')
            ->join(array(self::SPRINT_ISSUES_TABLE, 'sprint_issues'), 'left')->on('issues.id', '=', 'sprint_issues.issue_id')
            ->join(array(self::SPRINTS_TABLE, 'sprints'), 'left')->on('sprint_issues.sprint_id', '=', 'sprints.id');
        if(@$params['project_id'] != null){
            $query->where('projects.id', '=', $params['project_id']);
        }
        if(@$params['author'] != null){
            $query->where('worklog.author', '=', $params['author']);
        }
        if(@$params['month'] != null){
            $query->where("worklog.month", '=', $params['month']);
        }
        if(@array_search('Project', $params['group_by']) !== false){
            $query->group_by('projects.id');
        }
        if(@array_search('Month', $params['group_by']) !== false){
            $query->group_by('month');
        }
        if(@array_search('Author', $params['group_by']) !== false){
            $query->group_by('author');
        }
        if(@array_search('Sprint', $params['group_by']) !== false){
            $query->group_by('sprint');
        }
        if(@array_search('Status', $params['group_by']) !== false){
            $query->group_by('status');
        }
        if(@array_search('Ticket', $params['group_by']) !== false){
            $query->group_by('issues.id');
        }
        $query->order_by('Project');
        if(@array_search('Sprint', $params['group_by']) !== false){
            $query->order_by('Sprint');
        }

        $worklog = $query->execute()->as_array();

        return $worklog;
    }
    
    public static function projects_sync_jira()
    {
        $existing_projects = self::projects_list();
        $projects = array();
        $projectsf = array();
        foreach($existing_projects as $existing_project){
            $projects[$existing_project['jira_id']] = $existing_project['jira_key'];
            $projectsf[$existing_project['jira_key']] = $existing_project['jira_id'];
        }
        $jira = new Model_Jira();
        $jira_projects = $jira->get_projects();
        //header('content-type: text/plain; charset=utf-8');
        //print_r($jira_projects);die();

        try{
            Database::instance()->begin();
            foreach($jira_projects as $i => $jira_project){
                if (array_key_exists($jira_project['key'], $projectsf)) {
                    if (isset($projects[$jira_project['id']])) {
                        DB::update(self::PROJECT_TABLE)
                            ->set(array(
                                'jira_id' => null
                            ))
                            ->where('jira_id', '=', $jira_project['id'])
                            ->execute();
                    }
                    DB::update(self::PROJECT_TABLE)
                        ->set(array(
                            'jira_id' => $jira_project['id'],
                            'title' => $jira_project['name']
                        ))
                        ->where('jira_key', '=', $jira_project['key'])
                        ->execute();
                } else {
                    if (!isset($projects[$jira_project['id']])) {
                        DB::insert(self::PROJECT_TABLE, array('jira_id', 'jira_key', 'title'))
                            ->values(array($jira_project['id'], $jira_project['key'], $jira_project['name']))
                            ->execute();
                    }
                }
            }
            Database::instance()->commit();
        }catch(Exception $exc){
            Database::instance()->rollback();
            throw $exc;
        }
    }
    
    public static function project_sync_jira($project_id, $sync_sprints = false, $retry_after = 10)
    {
        $jira_id = DB::select('jira_id')->from(self::PROJECT_TABLE)->where('id', '=', $project_id)->execute()->get('jira_id');
        $jira = new Model_Jira();
        $jira_issues = $jira->get_issues($jira_id);
        if ($jira_issues !== false) {
            Database::instance()->begin();
            try {
                DB::delete(self::ISSUES_TABLE)->where('project_id', '=', $project_id)->execute();
                DB::query(null, 'DELETE `plugin_extra_projects_worklog`
    FROM `plugin_extra_projects_worklog`
        INNER JOIN `plugin_extra_projects_issues` ON `plugin_extra_projects_worklog`.issue_id = `plugin_extra_projects_issues`.id
    WHERE `plugin_extra_projects_issues`.project_id = ' . $project_id)->execute();
                foreach ($jira_issues['issues'] as $jira_issue) {
                        $id = DB::insert(self::ISSUES_TABLE, array('project_id', 'jira_id', 'jira_key', 'title', 'description', 'status', 'time_spent', 'updated', 'duedate', 'timeoriginalestimate', 'resolution'))
                            ->values(array($project_id, $jira_issue['id'], $jira_issue['key'], $jira_issue['fields']['summary'], $jira_issue['fields']['description'], $jira_issue['fields']['status']['name'], $jira_issue['fields']['timespent'], date('Y-m-d H:i:s', strtotime($jira_issue['fields']['updated'])), $jira_issue['fields']['duedate'], $jira_issue['fields']['timeoriginalestimate'], $jira_issue['fields']['resolution']['name']))
                            ->execute();
                    foreach ($jira_issue['fields']['worklog']['worklogs'] as $worklog) {
                        DB::insert(self::WORKLOG_TABLE, array('issue_id', 'author', 'started', 'time_spent'))
                            ->values(array($id[0], isset($worklog['author']['name']) ? $worklog['author']['name'] : 'not set', date('Y-m-d H:i:s', strtotime($worklog['started'])), $worklog['timeSpentSeconds']))
                            ->execute();
                    }
                }
                DB::update(self::PROJECT_TABLE)->set(array('synced' => date('Y-m-d H:i:s')))->where('id', '=', $project_id)->execute();
                Database::instance()->commit();
            } catch (Exception $exc) {
                Database::instance()->rollback();
                if ($retry_after) {
                    sleep($retry_after);
                    self::project_sync_jira($project_id, $sync_sprints, false);
                } else {
                    throw $exc;
                }
            }
            if ($sync_sprints) {
                self::projects_sync_sprints();
            }
            return true;
        } else {
            return false;
        }
    }
    
    public static function projects_sync_jira_all()
    {
        self::projects_sync_jira();
        $projects = self::projects_list();
        $results = array();
        foreach($projects as $project){
            $results[$project['id']] = self::project_sync_jira($project['id']);
        }
        self::projects_sync_sprints();
        return $results;
    }

    public static function projects_sync_sprints()
    {

        try{
            Database::instance()->begin();
            $jira = new Model_Jira();
            $rapidviews = $jira->get_rapidviews();
            foreach($rapidviews as $rapidview){
                $rapidview_id = DB::select('id')
                                    ->from(self::RAPIDVIEWS_TABLE)
                                    ->where('jira_id', '=', $rapidview['id'])
                                    ->execute()
                                    ->get('id');
                if(!$rapidview_id){
                    $rapidview_id = DB::insert(self::RAPIDVIEWS_TABLE, array('jira_id', 'name'))
                                        ->values(array($rapidview['id'], $rapidview['name']))
                                        ->execute();
                    $rapidview_id = $rapidview_id[0];
                }
                foreach($rapidview['sprints'] as $sprint){
                    $sprint_id = DB::select('id')
                                        ->from(self::SPRINTS_TABLE)
                                        ->where('jira_id', '=', $sprint['id'])
                                        ->execute()
                                        ->get('id');
                    if(!$sprint_id){
                        $sprint_id = DB::insert(self::SPRINTS_TABLE, array('jira_id', 'name', 'state'))
                                            ->values(array($sprint['id'], $sprint['name'], $sprint['state']))
                                            ->execute();
                        $sprint_id = $sprint_id[0];
                    }

                    $has_sprint_id = DB::select('id')
                        ->from(self::RAPIDVIEWS_SPRINTS_TABLE)
                        ->where('rapidview_id', '=', $rapidview_id)
                        ->and_where('sprint_id', '=', $sprint_id)
                        ->execute()
                        ->get('id');
                    if (!$has_sprint_id) {
                        DB::insert(self::RAPIDVIEWS_SPRINTS_TABLE)
                            ->values(array(
                                'rapidview_id' => $rapidview_id,
                                'sprint_id' => $sprint_id
                            ))
                            ->execute();
                    }

                    DB::delete(self::SPRINT_ISSUES_TABLE)
                        ->where('sprint_id', '=', $sprint_id)
                        ->execute();
                    if (isset($sprint['issues']) && count($sprint['issues']) > 0) {
                        DB::query(null, "INSERT INTO " . self::SPRINT_ISSUES_TABLE . "
                                        (sprint_id, issue_id)
                                        (SELECT " . $sprint_id . ", id FROM " . self::ISSUES_TABLE . " WHERE jira_key IN ('" . implode("', '", $sprint['issues']) . "'))")
                            ->execute();
                    }
                }
            }
            Database::instance()->commit();
        }catch(Exception $exc){
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function sync_sprint($sprint_id)
    {
        try{
            Database::instance()->begin();
            $rapid_sprint = DB::select(array('rapidviews.jira_id', 'rapidview_jira_id'), array('sprints.jira_id', 'sprint_jira_id'))
                ->from(array(self::SPRINTS_TABLE, 'sprints'))
                    ->join(array(self::RAPIDVIEWS_SPRINTS_TABLE, 'has_sprints'), 'inner')->on('sprints.id', '=', 'has_sprints.sprint_id')
                    ->join(array(self::RAPIDVIEWS_TABLE, 'rapidviews'), 'inner')->on('has_sprints.rapidview_id', '=', 'rapidviews.id')
                ->where('sprint_id', '=', $sprint_id)
                ->execute()
                ->current();

            $jira = new Model_Jira();
            $rapidviews = $jira->get_rapidviews($rapid_sprint['rapidview_jira_id'], $rapid_sprint['sprint_jira_id']);
            foreach($rapidviews as $rapidview){
                $rapidview_id = DB::select('id')
                    ->from(self::RAPIDVIEWS_TABLE)
                    ->where('jira_id', '=', $rapidview['id'])
                    ->execute()
                    ->get('id');
                if(!$rapidview_id){
                    $rapidview_id = DB::insert(self::RAPIDVIEWS_TABLE, array('jira_id', 'name'))
                        ->values(array($rapidview['id'], $rapidview['name']))
                        ->execute();
                    $rapidview_id = $rapidview_id[0];
                }

                if (isset($rapidview['sprints']))
                foreach($rapidview['sprints'] as $sprint){
                    $sprint_id = DB::select('id')
                        ->from(self::SPRINTS_TABLE)
                        ->where('jira_id', '=', $sprint['id'])
                        ->execute()
                        ->get('id');
                    if(!$sprint_id){
                        $sprint_id = DB::insert(self::SPRINTS_TABLE, array('jira_id', 'name', 'state'))
                            ->values(array($sprint['id'], $sprint['name'], $sprint['state']))
                            ->execute();
                        $sprint_id = $sprint_id[0];
                    }

                    $has_sprint_id = DB::select('id')
                        ->from(self::RAPIDVIEWS_SPRINTS_TABLE)
                        ->where('rapidview_id', '=', $rapidview_id)
                        ->and_where('sprint_id', '=', $sprint_id)
                        ->execute()
                        ->get('id');
                    if (!$has_sprint_id) {
                        DB::insert(self::RAPIDVIEWS_SPRINTS_TABLE)
                            ->values(array(
                                'rapidview_id' => $rapidview_id,
                                'sprint_id' => $sprint_id
                            ))
                            ->execute();
                    }

                    DB::delete(self::SPRINT_ISSUES_TABLE)
                        ->where('sprint_id', '=', $sprint_id)
                        ->execute();
                    if (isset($sprint['issues']) && count($sprint['issues']) > 0) {
                        DB::query(null, "INSERT INTO " . self::SPRINT_ISSUES_TABLE . "
                                        (sprint_id, issue_id)
                                        (SELECT " . $sprint_id . ", id FROM " . self::ISSUES_TABLE . " WHERE jira_key IN ('" . implode("', '", $sprint['issues']) . "'))")
                            ->execute();
                    }
                }
            }
            Database::instance()->commit();
        }catch(Exception $exc){
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function rapid_views_list()
    {
        return DB::select('rapid_views.*', DB::expr('count(*) as sprints'))
                    ->from(array(self::RAPIDVIEWS_TABLE, 'rapid_views'))
                        ->join(array(self::RAPIDVIEWS_SPRINTS_TABLE, 'has_sprints'), 'inner')
                            ->on('rapid_views.id', '=', 'has_sprints.rapidview_id')
                        ->join(array(self::SPRINTS_TABLE, 'sprints'), 'inner')
                            ->on('has_sprints.sprint_id', '=', 'sprints.id')
                    ->group_by('rapid_views.id')
                    ->order_by('rapid_views.name')
                    ->execute()
                    ->as_array();
    }
    
    public static function rapid_view_details($id)
    {
        $rapid_view = DB::select('*')
                    ->from(array(self::RAPIDVIEWS_TABLE, 'rapid_views'))
                    ->where('id', '=', $id)
                    ->execute()
                    ->current();
        if($rapid_view){
            $rapid_view['sprints'] = DB::select('sprints.*', DB::expr('count(*) as issues'))
                                        ->from(array(self::SPRINTS_TABLE, 'sprints'))
                                            ->join(array(self::RAPIDVIEWS_SPRINTS_TABLE, 'has_sprints'), 'inner')
                                                ->on('sprints.id', '=', 'has_sprints.sprint_id')
                                            ->join(array(self::SPRINT_ISSUES_TABLE, 'has_issues'), 'left')
                                                ->on('sprints.id', '=', 'has_issues.sprint_id')
                                        ->where('has_sprints.rapidview_id', '=', $id)
                                        ->group_by('sprints.id')
                                        ->order_by('sprints.name')
                                        ->execute()
                                        ->as_array();
        }
        return $rapid_view;
    }
    
    public static function sprint_details($id)
    {
        $sprint = DB::select('*')
                    ->from(array(self::SPRINTS_TABLE, 'sprints'))
                    ->where('id', '=', $id)
                    ->execute()
                    ->current();
        if($sprint){
            $sprint['issues'] = DB::select('issues.*')
                                        ->from(array(self::SPRINT_ISSUES_TABLE, 'has_issues'))
                                            ->join(array(self::ISSUES_TABLE, 'issues'), 'inner')->on('has_issues.issue_id', '=', 'issues.id')
                                        ->where('sprint_id', '=', $id)
                                        ->order_by('issues.jira_key', 'asc')
                                        ->execute()
                                        ->as_array();
        }
        return $sprint;
    }
    
    public static function sprints_report($params = array())
    {
        $query = DB::select(array('sprints.name', 'sprint'),
                            DB::expr("DATE_FORMAT(worklog.started, '%Y-%m-01') AS `month`"),
                            'worklog.author',
                            DB::expr("SUM(worklog.time_spent) AS time_spent"),
                            'issues.status')
                        ->from(array(self::WORKLOG_TABLE, 'worklog'))
                            ->join(array(self::ISSUES_TABLE, 'issues'), 'inner')->on('worklog.issue_id', '=', 'issues.id')
                            ->join(array(self::SPRINT_ISSUES_TABLE, 'has_issues'), 'inner')->on('issues.id', '=', 'has_issues.issue_id')
                            ->join(array(self::SPRINTS_TABLE, 'sprints'), 'inner')->on('has_issues.sprint_id', '=', 'sprints.id');
        if(@$params['sprint_id'] != null){
            $query->where('sprints.id', '=', $params['sprint_id']);
        }
        if(@$params['author'] != null){
            $query->where('worklog.author', '=', $params['author']);
        }
        if(@$params['month'] != null){
            $query->where(DB::expr("DATE_FORMAT(worklog.started, '%Y-%m-01')"), '=', $params['month']);
        }
        if(@array_search('Sprint', $params['group_by']) !== false){
            $query->group_by('sprints.id');
        }
        if(@array_search('Month', $params['group_by']) !== false){
            $query->group_by('month');
        }
        if(@array_search('Author', $params['group_by']) !== false){
            $query->group_by('author');
        }
        $worklog = $query->execute()->as_array();
        
        return array('worklog' => $worklog);
    }
    
    public static function sprints_list()
    {
        $sprint_issues_count = DB::select("sprint_id", DB::expr("COUNT(*) as cnt"))
            ->from(self::SPRINT_ISSUES_TABLE)
            ->group_by('sprint_id');
        return DB::select('sprints.*', 'has_sprints.rapidview_id', DB::expr("CONCAT(rapidviews.name, '->', sprints.name) AS `title`"), array('issue_count.cnt', 'issues'))
                    ->from(array(self::SPRINTS_TABLE, 'sprints'))
                        ->join(array(self::RAPIDVIEWS_SPRINTS_TABLE, 'has_sprints'), 'inner')
                            ->on('sprints.id', '=', 'has_sprints.sprint_id')
                        ->join(array(self::RAPIDVIEWS_TABLE, 'rapidviews'), 'inner')
                            ->on('has_sprints.rapidview_id', '=', 'rapidviews.id')
                        ->join(array($sprint_issues_count, 'issue_count'), 'left')->on('sprints.id', '=', 'issue_count.sprint_id')
                    ->order_by('sprints.name')
                    ->group_by("sprints.id")
                    ->execute()
                    ->as_array();
    }

    public static function project_details($project_id)
    {
        $data = array();
        $data['details'] = DB::select('*')->from(self::PROJECT_TABLE)->where('id', '=', $project_id)->execute()->current();
        $data['issues'] = DB::select('*')->from(self::ISSUES_TABLE)->where('project_id', '=', $project_id)->execute()->as_array();
        return $data;
    }

    public static function projects_sync_sprints2()
    {
        Database::instance()->begin();
        try {
            // Function to delete any active sprint that is no longer active or future.
            $jira = new Model_Jira();
            $sprints = $jira->get_sprints();
            foreach ($sprints['values'] as $sprint) {
                self::projects_sync_individual_sprint2($sprint['id'], $sprint);
            }
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
        Database::instance()->commit();
    }

    public static function projects_sync_individual_sprint2($jira_sprint_id, $sprint = false)
    {
        $jira = new Model_Jira();
        if (!$sprint) {
            $sprint = $jira->get_sprint($jira_sprint_id);
        }
        $sprint_issues = ($sprint['id'] === null) ? array() : $jira->get_sprint_issues($sprint['id']);
        $project_names = array();
        foreach ($sprint_issues as $sprint_issue) {
            $project_names[] = $sprint_issue['fields']['project']['name'];
        }
        // we need to get the most common project name per all the tickets in the sprint.
        $project_names = array_count_values($project_names);
        // sort the array, get the first key in the array to get most common value.
        arsort($project_names);
        $most_common_project_name = $sprint['name'] ?? "";
        // foreach, then break. (most efficient way for PHP 7.0)
        foreach ($project_names as $project_name => $value) {
            $most_common_project_name = $project_name;
            break;
        }
        // If returned false, the sprint is no longer active/is a future sprint
        if ($sprint['id'] === null || !$jira->active_sprint_checker($jira_sprint_id)) {
            DB::update(self::SPRINTS_TABLE2)
                ->set(array('deleted' => DB::expr('1')))
                ->where('jira_sprint_id', '=', $jira_sprint_id)
                ->execute();
        } else {
            $jira_sprint = array();
            $jira_sprint['jira_sprint_id'] = $jira_sprint_id;
            $jira_sprint['sprint'] = $sprint['name'] ?? "N/A";
            $jira_sprint['customer'] = $most_common_project_name;
            $jira_sprint['spent'] = $jira->get_time_spent_by_issues($sprint_issues);
            self::projects_sync_sprint2($jira_sprint);
        }
    }

    public static function projects_sync_sprint2($jira_sprint)
    {
        Database::instance()->begin();
        $jira_sprint['last_synced'] = date("Y/m/d");
        $jira_info = DB::select('jira_sprint_id')->from(self::SPRINTS_TABLE2)->where('jira_sprint_id', '=',
            $jira_sprint['jira_sprint_id'])->execute()->count();
        if ($jira_info > 0) {
            try {
                DB::update(self::SPRINTS_TABLE2)
                    ->set($jira_sprint)
                    ->where('jira_sprint_id', '=', $jira_sprint['jira_sprint_id'])
                    ->execute();
            } catch (Exception $exc) {
                Database::instance()->rollback();
                throw $exc;
            }
        } else {
            try {
                DB::insert(self::SPRINTS_TABLE2, array('customer', 'jira_sprint_id', 'sprint', 'spent', 'last_synced'))
                    ->values(array(
                        $jira_sprint['customer'],
                        $jira_sprint['jira_sprint_id'],
                        $jira_sprint['sprint'],
                        $jira_sprint['spent'],
                        $jira_sprint['last_synced']
                    ))
                    ->execute();
            } catch (Exception $exc) {
                Database::instance()->rollback();
                throw $exc;
            }
        }
        Database::instance()->commit();
    }

    public static function projects_sprint2_update_budget($id, $budget_amount)
    {
        $sprint_details = DB::select('*')->from(self::SPRINTS_TABLE2)->execute()->current();
        $sprint_details['budget'] = $budget_amount;
        $sprint_details['balance'] = $sprint_details['budget'] - $sprint_details['spent'];
        $sprint_details['remaining'] = floor(($sprint_details['spent'] / $sprint_details['budget']) * 100);
        Database::instance()->begin();
        try {
            DB::update(self::SPRINTS_TABLE2)
                ->set($sprint_details)
                ->where('id', '=', $id)
                ->execute();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
        Database::instance()->commit();
    }

    public static function projects_save_sprint2($sprint_id, $data)
    {
        $query = DB::update(self::SPRINTS_TABLE2)
            ->set($data)
            ->where('id', '=', $sprint_id)
            ->execute();
        return $query;
    }
}
