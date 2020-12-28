<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Activecampaign extends Model
{
    const TABLE_RCONTACTS = 'plugin_remoteaccounting_contacts';
    const TABLE_RTRANSACTIONS = 'plugin_remoteaccounting_transactions';
    const TABLE_RPAYMENTS = 'plugin_remoteaccounting_payments';

    public $key = '';
    public $url = '';
    protected $curl = null;

    protected $fields = array();
    protected $account_fields = array();
    protected static $fields_cache = null;
    protected static $account_fields_cache = null;
    protected static $tags_cache = null;

    public function __construct()
    {
        $settings = Settings::instance();
        $this->url = $settings->get('activecampaign_url');
        $this->key = $settings->get('activecampaign_key');

        $this->curl = curl_init();

        if (self::$fields_cache == null) {
            self::$fields_cache = $this->get_custom_fields();
        }
        $fields = self::$fields_cache;
        /*
         * if ($fields['length'] == 0) {
            $this->create_custom_field('text', 'Booking ID', 'Booking ID', '');
            $this->create_custom_field('text', 'Course ID', 'Course ID', '');
            $this->create_custom_field('text', 'Schedule ID', 'Schedule ID', '');
            $this->create_custom_field('text', 'Course', 'Course', '');
            $this->create_custom_field('text', 'Schedule', 'Schedule', '');
        }*/
        $this->fields = $fields;

        if (self::$account_fields_cache == null) {
            self::$account_fields_cache = $this->get_account_custom_fields();
        }
        $account_fields = self::$account_fields_cache;
        $this->account_fields = $account_fields;
    }

    protected function get_field_id($field)
    {
        foreach ($this->fields as $rfield) {
            if ($rfield['title'] == $field || $rfield['perstag'] == $field) {
                return $rfield['id'];
            }
        }
    }

    protected function get_account_field_id($field)
    {
        foreach ($this->account_fields as $rfield) {
            if ($rfield['fieldLabel'] == $field ||$rfield['personalization'] == $field) {
                return $rfield['id'];
            }
        }
    }

    protected function request($method, $url, $params = null)
    {
        $full_url = $this->url . $url;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, true);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        if ($method != 'GET') {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'Api-Token: ' . $this->key));
            curl_setopt($this->curl, CURLOPT_HTTPGET, false);
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Api-Token: ' . $this->key));

            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, null);
        }

        if ($params) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        }

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $full_url);
        $t1 = microtime(true);
        $response = curl_exec($this->curl);
        $t2 = microtime(true);
        $this->last_error = curl_error($this->curl);
        $this->last_info = curl_getinfo($this->curl);
        $this->last_response = $response;
        Model_ExternalRequests::create($this->url, $url, $params, $response, $this->last_info['status'], date::now(), null, $t2 - $t1);
        try {
            return json_decode($response, true);
        } catch (Exception $exc) {
            return false;
        }
    }

    public function create_tag($tag, $type, $description)
    {
        $response = $this->request('POST', '/api/3/tags', array('tag' => array('tag' => $tag, 'tagType' => $type, 'description' => $description)));
        return $response;
    }

    public function get_tags()
    {
        if (self::$tags_cache == null) {
            $limit = 100;
            $total = 1;
            $result = array();
            for ($offset = 0; $offset < $total; $offset += $limit) {
                $response = $this->request('GET',
                    '/api/3/tags?' . http_build_query(array('limit' => $limit, 'offset' => $offset)));
                if ($response) {
                    $total = (int)$response['meta']['total'];
                    $result = array_merge($result, $response['tags']);
                    if ($offset >= 10000) {
                        break;
                    }
                }
            }
            self::$tags_cache = $result;
        }
        $result = self::$tags_cache;
        return $result;
    }

    public function save_contact($contact_id, $tags = array(), $fields = array())
    {
        $c3 = new Model_Contacts3($contact_id);
        $org = $c3->get_linked_organisation();
        if ($org->get_id()) {
            $this->save_account($org->get_id(), $fields);
        }
        if ($c3->get_type() == Model_Contacts3::find_type('Organisation')['contact_type_id']) {
            return $this->save_account($contact_id, $fields);
        }

        $tag_list = $this->get_tags();
        if ($tags)
        foreach ($tags as $tag) {
            $found = false;
            foreach ($tag_list as $search_tag) {
                if (strcasecmp($search_tag['tag'],  $tag['tag']) == 0) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $new_tag = $this->create_tag($tag['tag'], 'contact', $tag['description']);
                if ($new_tag['tag']) {
                    $tag_list[] = $new_tag['tag'];
                }
            }
        }
        $rs = new Model_Remotesync();

        $contact = array();
        $contact['firstName'] = $c3->get_first_name();
        $contact['lastName'] = $c3->get_last_name();
        $contact['email'] = $c3->get_email();
        $contact['phone'] = $c3->get_mobile();
        if ($contact['phone'] == '') {
            $contact['phone'] = $c3->get_phone();
        }
        if($c3->get_job_title() != '') {
            $fields['JOB_TITLE'] = $c3->get_job_title();
        }
        $job_function_id = $c3->get_job_function_id();
        if ($job_function_id) {
            $fields['JOB_FUNCTION'] = Model_Contacts3::get_job_function($job_function_id);
            if ($fields['JOB_FUNCTION']) {
                $fields['JOB_FUNCTION'] = $fields['JOB_FUNCTION']['label'];
            }
        }

        $related_organisations = $c3->get_contact_relations_details(array('contact_type' => 'organisation'));
        if ($related_organisations) {
            $organization = Model_Organisation::get_org_by_contact_id($related_organisations[0]['parent_id']);
            if ($organization) {
                if ($organization->get_organisation_industry_id()) {
                    $fields['ACCT_INDUSTRY_VERTICAL'] = Model_Organisation::get_organization_industry($organization->get_organisation_industry_id());
                    if ($fields['ACCT_INDUSTRY_VERTICAL']) {
                        $fields['ACCT_INDUSTRY_VERTICAL'] = $fields['ACCT_INDUSTRY_VERTICAL']['label'];
                    }
                }
                if ($organization->get_organisation_size_id()) {
                    $fields['ACCT_NUMBER_OF_EMPLOYEES'] = Model_Organisation::get_organization_size($organization->get_organisation_size_id());
                    if ($fields['ACCT_NUMBER_OF_EMPLOYEES']) {
                        $fields['ACCT_NUMBER_OF_EMPLOYEES'] = $fields['ACCT_NUMBER_OF_EMPLOYEES']['label'];
                    }
                }
            }
        }

        $organization = Model_Organisation::get_org_by_contact_id($contact_id);
        if ($organization->get_id()) {
            if ($organization->get_organisation_industry_id()) {
                $fields['ACCT_INDUSTRY_VERTICAL'] = Model_Organisation::get_organization_industry($organization->get_organisation_industry_id());
                if ($fields['ACCT_INDUSTRY_VERTICAL']) {
                    $fields['ACCT_INDUSTRY_VERTICAL'] = $fields['ACCT_INDUSTRY_VERTICAL']['label'];
                }
            }
            if ($organization->get_organisation_size_id()) {
                $fields['ACCT_NUMBER_OF_EMPLOYEES'] = Model_Organisation::get_organization_size($organization->get_organisation_size_id());
                if ($fields['ACCT_NUMBER_OF_EMPLOYEES']) {
                    $fields['ACCT_NUMBER_OF_EMPLOYEES'] = $fields['ACCT_NUMBER_OF_EMPLOYEES']['label'];
                }
            }
        }

        if ($contact['email']) {
            $synced = $rs->get_object_synced('ActiveCampaign-Contact', $contact_id);
            if ($synced) {
                $response = $this->request('PUT', '/api/3/contacts/' . $synced['remote_id'], array('contact' => $contact));
            } else {
                $response = $this->request('POST', '/api/3/contact/sync', array('contact' => $contact));
            }
        }

        if (isset($response['contact']['id'])) {
            if ($org->get_id() > 0) {
                $org_synced = $rs->get_object_synced('ActiveCampaign-Contact', $org->get_id());
                if ($org_synced) {
                    $this->link_contact_account($response['contact']['id'], $org_synced['remote_id'], $c3->get_job_title());
                }
            }
            if ($tags)
            foreach ($tags as $tag) {
                foreach ($tag_list as $search_tag) {
                    if (strcasecmp($search_tag['tag'], $tag['tag']) == 0) {
                        $this->create_contact_tag($response['contact']['id'], $search_tag['id']);
                    }
                }
                if ($tag['tag'] == 'Booking') {
                    $this->move_first_deal($contact_id);
                }
            }
            if ($fields)
            foreach ($fields as $field => $value) {
                $field_id = $this->get_field_id($field);
                $this->create_contact_field($response['contact']['id'], $field_id, $value);
            }
            $rs->save_object_synced('ActiveCampaign-Contact', $response['contact']['id'], $contact_id);
        }
        return true;
    }

    public function create_contact_tag($contact, $tag)
    {
        $response = $this->request('POST', '/api/3/contactTags', array('contactTag' => array('contact' => $contact, 'tag' => $tag)));
        return $response;
    }

    public function create_contact_field($contact, $field_id, $value)
    {
        $response = $this->request('POST', '/api/3/fieldValues', array('fieldValue' => array('contact' => $contact, 'field' => $field_id, 'value' => $value)));
        return $response;
    }

    public function save_account($contact_id, $fields = array())
    {
        $rs = new Model_Remotesync();
        $c3 = new Model_Contacts3($contact_id);
        $contact = array();
        $contact['name'] = trim($c3->get_first_name() . ' ' . $c3->get_last_name());

        $organization = Model_Organisation::get_org_by_contact_id($contact_id);
        if ($organization) {
            if ($c3->get_phone() != '') {
                $fields['ACCT_PHONE_NUMBER'] = $c3->get_phone();
            }
            if ($organization->get_organisation_industry_id()) {
                $fields['ACCT_INDUSTRY_VERTICAL'] = Model_Organisation::get_organization_industry($organization->get_organisation_industry_id());
                if ($fields['ACCT_INDUSTRY_VERTICAL']) {
                    $fields['ACCT_INDUSTRY_VERTICAL'] = $fields['ACCT_INDUSTRY_VERTICAL']['label'];
                }
            }
            if ($organization->get_organisation_size_id()) {
                $fields['ACCT_NUMBER_OF_EMPLOYEES'] = Model_Organisation::get_organization_size($organization->get_organisation_size_id());
                if ($fields['ACCT_NUMBER_OF_EMPLOYEES']) {
                    $fields['ACCT_NUMBER_OF_EMPLOYEES'] = $fields['ACCT_NUMBER_OF_EMPLOYEES']['label'];
                }
            }
        }

        $synced = $rs->get_object_synced('ActiveCampaign-Contact', $contact_id);
        if ($synced) {
            $response = $this->request('PUT', '/api/3/accounts/' . $synced['remote_id'], array('contact' => $contact));
        } else {
            $response = $this->request('POST', '/api/3/accounts', array('account' => $contact));
        }

        if (isset($response['account']['id'])) {
            if ($fields)
                foreach ($fields as $field => $value) {
                    $field_id = $this->get_account_field_id($field);
                    $this->create_account_field($response['account']['id'], $field_id, $value);
                }
            $rs->save_object_synced('ActiveCampaign-Contact', $response['account']['id'], $contact_id);
        }
        return true;
    }

    public function create_account_field($contact, $field_id, $value)
    {
        $response = $this->request('POST', '/api/3/accountCustomFieldData', array('accountCustomFieldDatum' => array('accountId' => $contact, 'customFieldId' => $field_id, 'fieldValue' => $value)));
        return $response;
    }

    public function create_custom_field($type, $title, $descript, $perstag, $defval = '', $isrequired = 0, $visible = 1, $ordernum = 1)
    {
        $response = $this->request(
            'POST',
            '/api/3/fields',
            array(
                'field' => array(
                    'type' => $type,
                    'title' => $title,
                    'descript' => $descript,
                    'isrequired' => $isrequired,
                    'perstag' => $perstag,
                    'defval' => $defval,
                    'visible' => $visible,
                    'ordernum' => $ordernum
                )
            )
        );
        return $response;
    }

    public function get_custom_fields()
    {
        $response = $this->request('GET', '/api/3/fields');
        return $response['fields'];
    }

    public function get_account_custom_fields()
    {
        $response = $this->request('GET', '/api/3/accountCustomFieldMeta');
        return $response['accountCustomFieldMeta'];
    }

    public function link_contact_account($contact_id, $account_id, $jobtitle = '')
    {
        $response = $this->request('POST', '/api/3/accountContacts', array('accountContact' => array('contact' => $contact_id, 'account' => $account_id, 'jobTitle' => $jobtitle)));
        return $response;
    }

    public function delete_contact($contact_id)
    {
        $rs = new Model_Remotesync();
        $c3 = new Model_Contacts3($contact_id);
        $synced = $rs->get_object_synced('ActiveCampaign-Contact', $contact_id);
        if ($synced) {
            $response = $this->request('DELETE', '/api/3/contacts/' . $synced['remote_id']);
        }
        return true;
    }

    public static function create_from_remote($contact)
    {
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced('ActiveCampaign-Contact', $contact['id'], 'remote');
        if ($synced) {
            $c3 = new Model_Contacts3($synced['cms_id']);
        } else {
            $c3 = new Model_Contacts3();
        }

        $c3->set_first_name($contact['first_name']);
        $c3->set_last_name($contact['last_name']);
        $c3->set_type(1);
        $c3->set_subtype_id(0);
        $notifications = array();
        $notification = [
            'id' => 'new',
            'notification_id' => 1,
            'value' => $contact['email']
        ];
        $c3->insert_notification($notification);
        $c3->save();

        $rs->save_object_synced('ActiveCampaign-Contact', $contact['id'], $c3->get_id());
    }

    public static function get_conversion_script($booking_id)
    {
        $ac_cs = Settings::instance()->get('activecampaign_conversion_script');
        if ($ac_cs) {
            $booking = new Model_KES_Bookings($booking_id);
            $contact_id = $booking->get_contact_details_id();
            $contact = new Model_Contacts3($contact_id);
            $email = $contact->get_email();
            $value = $booking->get_booking_amount();
            $ac_cs = str_replace("trackcmp_email = ''", "trackcmp_email = '" . $email ."'", $ac_cs);
            $ac_cs = str_replace("trackcmp_conversion_value = ''", "trackcmp_conversion_value = '" . $value ."'", $ac_cs);
        }
        return $ac_cs;
    }

    public function move_first_deal($contact_id)
    {
        $contact = new Model_Contacts3($contact_id);
        $contact_name = trim($contact->get_first_name() . ' ' . $contact->get_last_name());
        $res = $this->request('GET', '/api/3/deals?' . http_build_query(array('orders[contact_name]' => $contact_name)));
        $result = false;
        if (isset($res['deals'])) {
            foreach ($res['deals'] as $deal) {
                if ($deal['status'] == 0) { // 0 => open; 1 => won; 2 => lost
                    $params = $deal;
                    foreach ($params as $key => $value) {
                        if (!in_array($key, array(	"contact",
                            "account",
                            "description",
                            "currency",
                            "group",
                            "owner",
                            "percent",
                            "stage",
                            "status",
                            "title",
                            "value"))) {
                            unset($params[$key]);
                        }
                    }
                    $params['status'] = 1;
                    $params = array('deal' => $params);
                    $result = $this->request('PUT', '/api/3/deals/' . $deal['id'], $params);
                    break;
                }
            }
        }
        return $result;
    }
}

