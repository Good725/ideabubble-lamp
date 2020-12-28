<?php
/**
 * Created by JetBrains PhpStorm.
 * Author: dale@ideabubble.ie
 * Date: 03/02/2014
 * Time: 16:18
 */
final class Model_Customers extends Model
{
    const customers_table = 'plugin_extra_customers';
    private $id;
    private $user_id;
    private $company_title;
    private $industry;
    private $phone;
    private $contact = NULL;
    private $billing_contact = NULL;
    private $notes;
    private $summary;
    private $address1;
    private $address2;
    private $address3;
    private $county;
    private $date_modified;
	private $bullethq_id;
    private $_post;

    function __construct($customer_id = NULL,$post = NULL)
    {
        if(isset($customer_id) AND is_numeric($customer_id))
        {
            $this->set($this->get_customer_details($customer_id));
        }
        $this->_post = $post;
    }

    public function get_customer_details($customer_id)
    {
        $q =  DB::select('company_title','user_id','industry','contact','billing_contact','phone','notes','summary','address1','address2','address3','county','date_modified', 'bullethq_id')
            ->from(self::customers_table)->where('deleted','=',0)
            ->and_where('publish','=',1)
            ->and_where('id','=',$customer_id)
            ->order_by('company_title')
            ->execute()
            ->as_array();
        $this->id = (int) $customer_id;
        return $q[0];
    }

	public function action_get_customers($offset = 0, $limit = 10000)
	{
		$results = DB::select(
			'plugin_extra_customers.id','plugin_extra_customers.date_modified','plugin_extra_customers.county',
			'plugin_extra_customers.date_modified','plugin_extra_customers.address1', 'plugin_extra_customers.company_title',
			array(DB::expr("CONCAT(plugin_contacts_contact.first_name,' ',plugin_contacts_contact.last_name)"), 'contact'),
			'plugin_extra_customers.phone','plugin_contacts_contact.email',	'users.logins','users.last_login','users.logins_fail','users.last_fail')
			->from('plugin_extra_customers')
			->join('plugin_contacts_contact', 'left')->on('plugin_contacts_contact.id', '=', 'plugin_extra_customers.contact')
			->join(array('engine_users', 'users'), 'left')->on('plugin_extra_customers.user_id', '=', 'users.id')
			->where('plugin_extra_customers.publish', '=', 1)
			->where('plugin_extra_customers.deleted', '=', 0)
			->order_by('plugin_extra_customers.date_modified', 'desc')
			->limit($limit)
			->offset($offset)
			->execute()
			->as_array();

		return $results;
	}

    public function set($data)
    {
        $this->id = isset($data['id']) ? $data['id'] : $this->id;
        $this->contact = isset($data['contact']) ? $data['contact'] : $this->contact;
        $this->billing_contact = isset($data['billing_contact']) ? $data['billing_contact'] : $this->billing_contact;
        $this->company_title = isset($data['company_title']) ? $data['company_title']: $this->company_title;
        $this->industry = isset($data['industry']) ? $data['industry']: $this->industry;
        $this->phone = isset($data['phone']) ? $data['phone']: $this->phone;
        $this->notes = isset($data['notes']) ? $data['notes']: $this->notes;
        $this->summary = isset($data['summary']) ? $data['summary']: $this->summary;
        $this->address1 = isset($data['address1']) ? $data['address1']: $this->address1;
        $this->address2 = isset($data['address2']) ? $data['address2']: $this->address2;
        $this->address3 = isset($data['address3']) ? $data['address3']: $this->address3;
        $this->county = isset($data['county']) ? $data['county']: $this->county;
		$this->bullethq_id = isset($data['bullethq_id']) ? $data['bullethq_id']: $this->bullethq_id;
        $this->date_modified = date('Y-m-d H:i:s');
    }

    public function save($frontend = FALSE)
    {
        $old_email = Model_Contacts::get_contact_email($this->user_id);
        if(is_array($this->_post) AND !empty($this->_post))
        {
            $main_contact    = (isset($this->_post['contact'])         AND is_numeric($this->_post['contact']))         ? $this->_post['contact']         : NULL;
            $billing_contact = (isset($this->_post['billing_contact']) AND is_numeric($this->_post['billing_contact'])) ? $this->_post['billing_contact'] : NULL;

            $edit_contact = (
                ($this->_post['contact_first_name'] != '') OR ($this->_post['contact_last_name'] != '') OR
                ($this->_post['contact_phone']      != '') OR ($this->_post['contact_email']     != ''));

            $edit_billing_contact = (
                ($this->_post['billing_first_name'] != '') OR ($this->_post['billing_last_name'] != '') OR
                ($this->_post['billing_phone']      != '') OR ($this->_post['billing_email']     != ''));

            if ($billing_contact != $main_contact OR $edit_contact)
            {
                $contact = new Model_Contacts($main_contact);
                $contact->set_first_name($this->_post['contact_first_name']);
                $contact->set_last_name($this->_post['contact_last_name']);
                $contact->set_email($this->_post['contact_email']);
                $contact->set_phone($this->_post['contact_phone']);
                $contact->set_mailing_list('customer');
                $contact->set_publish(1);
                $contact->save();

                if( ! is_numeric($this->_post['contact']))
                {
                    $this->contact = Model_Contacts::get_last_inserted_contact_id();
                }
            }

            if ($edit_billing_contact)
            {
                $contact = new Model_Contacts($billing_contact);
                $contact->set_first_name($this->_post['billing_first_name']);
                $contact->set_last_name($this->_post['billing_last_name']);
                $contact->set_email($this->_post['billing_email']);
                $contact->set_phone($this->_post['billing_phone']);
                $contact->set_mailing_list('billing');
                $contact->set_publish(1);
                $contact->save();

                if( ! is_numeric($this->_post['billing_contact']))
                {
                    $this->billing_contact = Model_Contacts::get_last_inserted_contact_id();
                }
            }
        }

        $post = $this->_post;
        $users_model = new Model_Users();
        $user_data['can_login'] = 1;
        $user_data['email'] = $post['email'] = isset($post['contact_email']) ? $post['contact_email'] : '';
        $user_id = isset($post['user_id']) ? $post['user_id'] : '';

        if (isset($post['contact_password']) AND $post['contact_password'] != '' AND ! $frontend)
        {
            $user_data['password'] = $post['contact_password'];
        }
        else
        {
            $user_data['password'] = '';
        }


        try
        {
            Database::instance()->begin();
			$cdetails = $contact->get_details();
            if ($this->id == 'new')
            {
                $user_data['role_id'] = DB::select('id')->from('engine_project_role')->where('role', '=','Extra User')->and_where('publish', '=', 1)->and_where('deleted', '=', 0)->execute()->get('id', 0);
                $existing_user = $users_model->get_user_by_email($post['email']);
                if ($existing_user == FALSE)
                {
                    $user_data['email_verified'] = 0;
                    (isset($post['password'])) ? $user_data['password'] = $post['password'] : NULL;
                    $insert = ($post['email'] != '') ? $users_model->add_user_data($user_data) : '';
                    if (isset($insert[0]))
                    {
                        $user_id = $insert[0];
                    }
                }
                else
                {
                    $user_id = $existing_user['id'];
                }
                $query = DB::insert(self::customers_table)->columns(array('user_id', 'mailing_list','company_title','industry','notes','summary','address1','address2','address3','contact','billing_contact','phone','county'))
                    ->values(array($user_id, 1,$this->company_title,$this->industry,$this->notes,$this->summary,$this->address1,$this->address2,$this->address3,$this->contact,$this->billing_contact,$this->phone,$this->county))->execute();
                $customer_id = $query[0];
                $bclient = array();
                $bclient['id'] = $this->bullethq_id;
                $bclient['name'] = $this->company_title;
                $bclient['email'] = $user_data['email'];
                $bclient['phoneNumber'] = $this->phone;
                $bullethqResponse = self::saveToBullethq($customer_id, $bclient);
				$this->save_to_realvault($customer_id, $cdetails);
				$return = $customer_id;
            }
            elseif (is_numeric($this->id))
            {
                $old_details = $this->get_customer_details($this->id);

                if ($old_details['user_id'] == '' OR $old_details['user_id'] == 0)
                {
                    if ($post['email'] != '')
                    {
                        $user_data['role_id'] = DB::select('id')->from('engine_project_role')->where('role', '=','Extra User')->and_where('publish', '=', 1)->and_where('deleted', '=', 0)->execute()->get('id', 0);
                        $insert  = ($post['email'] != '') ? $users_model->add_user_data($user_data) : '';
                        $user_id = $insert[0];
                    }
                }
                else
                {

                    if ($post['email'] != $old_email)
                    {
                        if ($post['email'] == '')
                        {
                            $user_id = '';
                            $user_data['deleted'] = 1;
                        }
                        else
                        {
                            $user_id = $old_details['user_id'];
                        }

                        $main_contact = is_numeric($this->_post['contact']) ? $this->_post['contact']:NULL;
                        $contact = new Model_Contacts($main_contact);
                        $contact->set_first_name($this->_post['contact_first_name']);
                        $contact->set_last_name($this->_post['contact_last_name']);
                        $contact->set_email($this->_post['contact_email']);
                        $contact->set_phone($this->_post['contact_phone']);
                        $contact->set_mailing_list('customer');
                        $contact->set_publish(1);
                        $contact->save();
                    }
                }
                $this->user_id = $user_id;

                DB::update(self::customers_table)->set($this->load())->where('id', '=', $this->id)->execute();
				$bclient = array();
				$bclient['id'] = $this->bullethq_id;
				$bclient['name'] = $this->company_title;
				$bclient['email'] = $user_data['email'];
				$bclient['phoneNumber'] = $this->phone;
				$bclient['addressLine1'] = $this->address1;
				$bclient['addressLine2'] = $this->address2;
				$bullethq = new Model_BulletHQ();
				$bullethqResponse = self::saveToBullethq($this->id, $bclient);

				$this->save_to_realvault($this->id, $cdetails);
				$return = $this->id;
            }
            else
            {
                $return = '';
            }
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
        return $return;
    }

    public function load()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'contact' => $this->contact,
            'billing_contact' => (empty($this->billing_contact)) ? NULL : $this->billing_contact,
            'phone' => $this->phone,
            'company_title' => $this->company_title,
            'industry' => $this->industry,
            'notes' => $this->notes,
            'summary' => $this->summary,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'county' => $this->county,
            'date_modified' => $this->date_modified,
			'bullethq_id' => $this->bullethq_id
        );
    }

    public function delete()
    {

        try {
            Database::instance()->begin();
            DB::update(self::customers_table)->set(array('deleted' => 1))->where('id','=',$this->id)->execute();
			if($this->bullethq_id){
				$bullethq = new Model_BulletHQ();
				$bullethq->delete_client($this->bullethq_id);
			}
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw new Exception("Failed to delete customer.".$e->getMessage());
        }
    }

    public function counties_of_ireland()
    {
        $counties = array(
            'Antrim','Armagh','Carlow','Cavan','Clare','Cork','Derry','Donegal','Down','Dublin',
            'Fermanagh','Galway','Kerry','Kildare','Kilkenny','Laois','Leitrim','Limerick','Longford',
            'Louth','Mayo','Meath','Monaghan','Offaly','Roscommon','Sligo','Tipperary','Tyrone','Waterford',
            'Westmeath','Wexford','Wicklow', 'Navan');

        $result = "";
        foreach($counties AS $county)
        {
            if($county == $this->county)
            {
                $result = '<option value="'.$county.'">'.$county.'</option>'.$result;
            }
            else
            {
                $result.='<option value="'.$county.'">'.$county.'</option>';
            }
        }

        if($this->county == NULL OR $this->county == "")
        {
            $result = '<option value="">---Choose a county---</option>'.$result;
        }

        return $result;
    }

    public static function check_email($email)
    {
        $q = DB::select('id')->from('engine_users')->where('email','=',trim($email))->execute()->as_array();
        return count($q) > 0 ? true : false;
    }

    public static function get_main_contact($id)
    {
        $q = DB::select('plugin_contacts_contact.first_name','plugin_contacts_contact.last_name')
            ->from('plugin_contacts_contact')
            ->join(self::customers_table,"LEFT")
            ->on('plugin_contacts_contact.id','=',self::customers_table.'.contact')
            ->limit(1)
            ->execute()
            ->as_array();
        return $q[0]['first_name']." ".$q[0]['last_name'];
    }

    public static function render_customer_login_form()
    {
		if(Auth::instance()->logged_in()){
			IbHelpers::set_message("You've already logged in", 'info');
			Request::$current->redirect('/customer-payment.html');
		} else {
	        return View::factory('frontend/account_service_login');
		}
    }

    public static function render_licenses_table()
    {
        return View::factory('frontend/licenses_table');
    }

    public static function render_customer_registration_form()
    {
        return View::factory('frontend/account_service_registration');
    }

    public static function render_registration_successful()
    {
        return View::factory('frontend/registration_successful');
    }

    public static function render_password_reset_form()
    {
        return View::factory('frontend/forgot_password_form');
    }

    public static function render_new_password_form()
    {
        return View::factory('frontend/reset_password');
    }

    public static function render_customer_payment_form()
    {
		$logged_in_user  = Auth::instance()->get_user();
		$user_model      = new Model_Users();
		$user            = $user_model->get_user($logged_in_user['id']);
		
        $contact_model   = new Model_Contacts();
        if(Session::instance()->get('cart') == NULL)
        {
            $cart            = new stdClass();
            $cart->id        = microtime(true);
            $cart->items     = array();
            Session::instance()->set('cart',$cart);
        }

		$bullethq = new Model_BulletHQ();
		$bullethq_invoices = array();
        $customers       = DB::select()->from(self::customers_table)->where('deleted','=',0)->and_where('publish','=',1)->and_where('user_id','=',$logged_in_user['id'])->execute()->as_array();
        if (count($customers) == 1)
        {
            $customer        = $customers[0];
            $customer_model  = new Model_Customers($customer['id']);
            $extra_model     = new Model_Extra();
            $contact         = (array) json_decode($contact_model->get_contact(array('id' => $customer['contact'])));
            $billing_contact = (array) json_decode($contact_model->get_contact(array('id' => $customer['billing_contact'])));
            $services        = $extra_model->get_service_data(NULL, $customer['id']);
            $payments        = $extra_model->get_payment_data(array('customer_id' => $customer['id']));
			if($customer['bullethq_id']){
				$bullethq_invoices = Model_Extra::list_invoices_b($customer['id']);
			}
			$cards           = Model_Customers::get_cards($customer['id']);
        }
        else
        {
            $customer        = NULL;
            $customer_model  = new Model_Customers();
            $contact         = NULL;
            $billing_contact = NULL;
            $services        = array();
            $payments        = array();
			$cards           = array();
        }

        //self::prepare_cart($services);

        $counties = $customer_model->counties_of_ireland();
        $view = View::factory('frontend/account_service_payment')
            ->set('customer',        $customer)
            ->set('counties',        $counties)
            ->set('contact',         $contact)
            ->set('billing_contact', $billing_contact)
            ->set('services',        $services)
            ->set('payments',        $payments)
			->set('bullethq_invoices', $bullethq_invoices)
			->set('cards',           $cards)
			->set('user',            $user);

        return $view;
    }

    public static function check_bullethq_for_customer($id)
    {
        $q = DB::select('bullethq_id')->from(self::customers_table)->where('id','=',$id)->execute()->as_array();
        return (count($q) > 0 AND isset($q[0]) AND $q[0]['bullethq_id'] != NULL) ? $q[0]['bullethq_id'] : false;
    }

    public static function get_bullethq_id($id, $create = false)
    {
        $bullethq_id = DB::select('bullethq_id')
            ->from(self::customers_table)
            ->where('id', '=', $id)
            ->execute()->get('bullethq_id');
        if (!$bullethq_id) {
            $customer = DB::select('*')
                ->from(Model_Extra::CUSTOMER_TABLE)
                ->where('id', '=', $id)
                ->execute()
                ->current();
            if ($customer) {
                $main_contact = DB::select('*')
                    ->from('plugin_contacts_contact')
                    ->where('id', '=', $customer['contact'])
                    ->execute()
                    ->current();
                if ($main_contact) {
                    $bclient = array();
                    $bclient['name'] = $customer['company_title'];
                    $bclient['email'] = $main_contact['email'];
                    $bclient['phone'] = $customer['email'] ? $customer['email'] : $main_contact['email'];
                    $response = self::saveToBullethq($id, $bclient);
                    $bullethq_id = isset($response['id']) ? $response['id'] : false;
                }
            }
        }
        return $bullethq_id;
    }

    public static function get_data_for_bullethq($id)
    {
        $q = DB::select('company_title','address1','address2','email','phone','user_id', 'bullethq_id')->from(self::customers_table)->where('id','=',$id)->execute()->as_array();
        $a = DB::select('email')->from('engine_users')->where('id','=',$q[0]['user_id'])->execute()->as_array();
        return count($q) > 0 ? array('name' => $q[0]['company_title'],'email' => $a[0]['email'],'addressLine1' => $q[0]['address1'],'addressLine2' => $q[0]['address2'],'phoneNumber' => $q[0]['phone'],'vatNumber' => '','countryCode' => 'IE', 'bullethq_id' => $q[0]['bullethq_id']) : false;
    }

    public static function update_bullethq_id($id,$bullethq_id)
    {
        $result = DB::update(self::customers_table)->set(array('bullethq_id' => $bullethq_id))->where('id','=',$id)->execute();
    }

    public static function prepare_cart($items)
    {
        $cart = Session::instance()->get('cart');
        Session::instance()->delete('cart');
        foreach($items AS $key=>$item)
        {
			$billable = false;
			if($item['status_id'] == 2){//pending
				$billable = true;
			} else if(strtotime($item['date_end']) < strtotime("+90 days") && $item['date_end'] != '0000-00-00 00:00:00' && $item['status_id'] == 4) {
				$billable = true;
			}
			if($billable){
				$object = new stdClass();
				$object->id = $item['product_id'];
				$object->service_id = $item['id'];
				$object->description = isset($item['url']) ? $item['url'] : '';
				$object->item = $item['service_type'];
				$object->price = $item['price'] - $item['discount'];
				$object->billable = $billable;
				$cart->items[$item['product_id']] = $object;
			}
        }
        Session::instance()->set('cart',$cart);
    }

    public static function toggle_cart_item($item_id,$toggle)
    {
        $cart = Session::instance()->get('cart');
        $result = array();
        $total = 0;
        foreach($cart->items AS $key => $item){
			if($item->service_id == $item_id){
				$item->billable = $toggle;
			}
			if($item->billable){
				$total += $item->price;
			}
        }

        $result['total'] = $total;
        return json_encode($result);
    }

    public static function get_payment_total()
    {
        $cart = Session::instance()->get('cart');
        $items = $cart->items;
        $total = 0;
        foreach($items AS $key=>$item)
        {
            if($item->billable)
            {
                $total += $item->price;
            }
        }
        return $total;
    }
	
	protected function save_to_realvault($customer_id, $contact)
	{
		$rv_payer_id = 'ideabubble-' . str_pad($customer_id, 5, '0', STR_PAD_LEFT);
        switch (Kohana::$environment) {
            case    Kohana::DEVELOPMENT:
                $rv_payer_id .= ' - development';
                break;
            case    Kohana::TESTING:
                $rv_payer_id .= ' - testing';
                break;
            case    Kohana::STAGING:
                $rv_payer_id .= ' - staging';
                break;
        }
		$realvault = new Model_Realvault();
		$exists = DB::select('realvault_id')->from('plugin_extra_realvault_payers')->where('customer_id', '=', $customer_id)->execute()->get('realvault_id');
		if(!$exists){
			$realvault_result = $realvault->create_payer($rv_payer_id, $contact['first_name'], $contact['last_name'], $contact['email']);
		} else {
			$realvault_result = $realvault->update_payer($rv_payer_id, $contact['first_name'], $contact['last_name'], $contact['email']);
		}
		if((string)$realvault_result->result == '501'){
			$realvault_result = $realvault->update_payer($rv_payer_id, $contact['first_name'], $contact['last_name'], $contact['email']);
		}
		if((string)$realvault_result->result == '00'){
			DB::query(null, 'REPLACE INTO plugin_extra_realvault_payers SET customer_id=' . $customer_id . ', realvault_id="' . $rv_payer_id . '"')->execute();
		}
		
		return (string)$realvault_result->result == '00' ? $rv_payer_id : false;
	}
	
	public function save_card_to_realvault($customer_id, $card_type, $card_number, $expdate, $holder_name, $cv)
	{
		$customer_details = $this->get_customer_details($customer_id);
		$contact = json_decode(Model_Contacts::get_contact(array('id' => $customer_details['contact'])), true);
		$rv_payer_id = DB::select('realvault_id')->from('plugin_extra_realvault_payers')->where('customer_id', '=', $customer_id)->execute()->get('realvault_id');
		if(!$rv_payer_id){
			$rv_payer_id = $this->save_to_realvault($customer_id, $contact);
		}
		if($rv_payer_id){
			$rv_card_id = 'ideabubble-' . gmdate('YmdHis');
			$realvault = new Model_Realvault();
			$realvault_result = $realvault->create_card($card_type, $card_number, $expdate, $holder_name, $rv_payer_id, $rv_card_id);
			if((string)$realvault_result->result == '00'){
				$card_number_d = substr($card_number, 0, 4) . ' **** **** **' . substr($card_number, -2);
				$expdate_d = date('Y-m-t', strtotime('20' . substr($expdate, 2) . '-' . substr($expdate, 0, 2) . '-01'));
				$result = DB::insert('plugin_extra_realvault_cards', array('customer_id', 'card_number', 'expdate', 'realvault_id', 'cv'))
						->values(array($customer_id, $card_number_d, $expdate_d, $rv_card_id, $cv))
						->execute();
				return $result[0];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public static function get_realvault_id($customerId, $create = true)
	{
		$rvPayerId = DB::select('realvault_id')
            ->from('plugin_extra_realvault_payers')
            ->where('customer_id', '=', $customerId)
            ->execute()
            ->get('realvault_id');
        if (!$rvPayerId && $create) {
            $customer = DB::select('*')
                ->from(Model_Extra::CUSTOMER_TABLE)
                ->where('id', '=', $customerId)
                ->execute()
                ->current();
            if ($customer) {
                $mainContact = DB::select('*')
                    ->from('plugin_contacts_contact')
                    ->where('id', '=', $customer['contact'])
                    ->execute()
                    ->current();
                if ($mainContact) {
                    $rvPayerId = self::save_to_realvault($customerId, $mainContact);
                }
            }
        }
		return $rvPayerId;
	}

	public static function get_cards($customer_id)
	{
		return DB::select('*')
					->from('plugin_extra_realvault_cards')
					->where('customer_id', '=', $customer_id)
					->and_where('expdate', '>=', date('Y-m-d'))
					->execute()
					->as_array();
	}
	
	public static function get_card($customer_id, $card_id)
	{
		$result = DB::select('*')
					->from('plugin_extra_realvault_cards')
					->where('customer_id', '=', $customer_id)
					->and_where('id', '=', $card_id)
					->and_where('expdate', '>=', date('Y-m-d'))
					->execute()
					->as_array();
		if(isset($result[0])){
			return $result[0];
		} else {
			return null;
		}
	}
	
	public static function delete_cards($customer_id, $card_ids)
	{
		$rv_payer_id = DB::select('realvault_id')->from('plugin_extra_realvault_payers')->where('customer_id', '=', $customer_id)->execute()->get('realvault_id');
		$rv_cards = DB::select('*')
							->from('plugin_extra_realvault_cards')
							->where('customer_id', '=', $customer_id)
							->and_where('id', 'in', $card_ids)
							->execute()
							->as_array();
		DB::delete('plugin_extra_realvault_cards')
			->where('customer_id', '=', $customer_id)
			->and_where('id', 'in', $card_ids)
			->execute();
		$realvault = new Model_Realvault();
		foreach($rv_cards as $rv_card){
			$realvault->cancel_card($rv_payer_id, $rv_card['realvault_id']);
		}
	}

    public static function saveToBullethq($customerId, $client)
    {
        switch (Kohana::$environment) {
            case    Kohana::DEVELOPMENT:
                $client['name'] .= '.development';
                break;
            case    Kohana::TESTING:
                $client['name'] .= '.testing';
                break;
            case    Kohana::STAGING:
                $client['name'] .= '.staging';
                break;
        }
        $bullethq = new Model_BulletHQ();
        if (isset($client['id']) && $client['id'] > 0) {
            $response = $bullethq->update_client($client, $client['id']);
        } else {
            unset($client['id']);
            $response = $bullethq->add_client($client, $customerId);
        }
        return $response;
    }
}
?>