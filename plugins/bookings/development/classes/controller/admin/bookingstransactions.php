<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Bookingstransactions extends Controller_Cms {

    public function action_ajax_get_bookings() {
        $family_id = $this->request->query('family_id');
        $contact_id = $this->request->query('contact_id');
        $bookings = Model_KES_Bookings::get_contact_family_bookings($family_id,$contact_id,NULL,FALSE);
        exit(json_encode($bookings));
    }

    /**
     * Save the transaction if it doesn't exist or update the transaction and copy the old transactio to the history table
     */
    public function action_ajax_save_transaction()
    {
        $this->auto_render = FALSE;
        $post = $this->request->post();
        // Update the transaction
		$answer = false;
        if($post['id'] !== '')
        {
            $answer = ORM::factory('Kes_Transaction')->save_history($post['id'],$post);
            if($answer != '')
            {
                $status = array('status'=>'success' , 'message'=>'Transaction updated successfully');
            }
            else
            {
                $status = array('status'=>'error', 'message'=>'The transaction was not updated successfully');
            }
        }
        // Try to save a transaction if the total or type are set
        else if($post['total'] !== 0 && $post['type'] !== '')
        {
            // save the transaction if the type is journal credit or a booking ID is set
            if ($post['type'] == 2 OR $post['booking_id'] !== '')
            {
                $answer = ORM::factory('Kes_Transaction')->save_transaction($post);
            }
            if($answer)
            {
                $status = array('status'=>'success', 'message' => 'Transaction created successfully');
            } else {
                $status = array('status'=>'error', 'message'=>'The transaction for this booking already exists');
            }
        }
        else
        {
            $status = array('status'=>'error', 'message'=>'Please fill in the form fields');
        }
        echo json_encode($status);
    }

    /**
     * Get the transactions for the family or the contact
     */
    public function action_ajax_get_transactions(){
        $this->auto_render = FALSE;
        $family_id = $this->request->query('family_id');
        $contact_id = $this->request->query('contact_id');
        if($contact_id || $family_id){
            if ($contact_id)
            {
                $transactions = ORM::factory('Kes_Transaction')->get_contact_transactions($contact_id,NULL);
            }
            else
            {
                $transactions = ORM::factory('Kes_Transaction')->get_contact_transactions(NULL,$family_id);
            }
            $view = View::factory('admin/list_accounts_transactions')
                ->set('transactions', $transactions)
                ->set('transaction_types', ORM::factory('Kes_Transaction')->get_transaction_types())
                ->set('payment_statuses', ORM::factory('Kes_Payment')->get_payment_status());
        }
        else
        {
            $view = View::factory('admin/list_accounts_payments')->set('alert', 'The transactions weren\'t found');
        }
        $this->response->body($view);
    }

    public function action_ajax_get_transaction(){
        $id = $this->request->query('id');
        $transaction = ORM::factory('Kes_Transaction', $id)->as_array();
        exit(json_encode($transaction));
    }

    public function action_ajax_get_payments(){
        $this->auto_render = FALSE;
        $transaction_id = $this->request->query('transaction_id');
        if($transaction_id)
        {
            $user = Auth::instance()->get_user();
            $contact_id = DB::select('contact_id')
                ->from('plugin_bookings_transactions')
                ->where('id','=',$transaction_id)->execute()->get('contact_id');
            $activity = new Model_Activity();
            $activity
                ->set_item_type('payment')
                ->set_action('list')
                ->set_item_id($transaction_id)
                ->set_user_id($user['id'])
                ->set_scope_id($contact_id ?? '0')
                ->save();
            $payments = ORM::factory('Kes_Payment')->get_transaction_payment($transaction_id);
            $history = ORM::factory('Kes_Transaction')->get_transaction_history($transaction_id);
            $view = View::factory('admin/list_accounts_payments')
                ->set('payments', $payments)
                ->set('transactions',$history)
                ->set('payment_statuses', ORM::factory('Kes_Payment')->get_payment_status());
        }
        else
        {
            $view = View::factory('admin/list_accounts_payments')->set('alert', 'The payments for the transaction weren\'t found');
        }

        $this->response->body($view);
    }

    public function action_ajax_delete(){
        $id = $this->request->query('id');
        $answer = ORM::factory('Kes_Transaction')->delete_transaction($id);
        exit(json_encode($answer ? array('status'=>'success') : array('status'=>'error', 'message'=>'The transaction doesen\'t exist.')));
    }

    public function action_ajax_show_cancel_booking()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();
        if ( isset($data['transaction_id']))
        {
            $transaction    = ORM::factory('Kes_Transaction')->get_transaction($data['transaction_id']);
        }
        else
        {
            $transaction    = ORM::factory('Kes_Transaction')->get_transaction(NULL,$data['booking_id']);
        }
        $view = View::factory('admin/snippets/cancel_booking_modal_form')
            ->set('transaction',$transaction);

        echo $view;
    }

    public function action_ajax_cancel_transaction()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();
        $results = Model_Kes_Transaction::cancel_transaction($data);
        exit(json_encode($results));
    }

    public function action_ajax_show_contact_balance()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();

        $results = NULL ;

        if (isset($data['contact_id']))
        {
            $contact = new Model_Contacts3($data['contact_id']);
            $family_balance = ORM::factory('Kes_Transaction')->get_contact_balance_label(NULL,$contact->get_family_id());
            $contact_balance = ORM::factory('Kes_Transaction')->get_contact_balance_label($data['contact_id'],NULL);
            $results = array(
                'status'    => 'success',
                'contact_balance'   => $contact_balance,
                'family_balance'    => $family_balance
            );
        }
        else if (isset($data['family_id']))
        {
            $family_balance = ORM::factory('Kes_Transaction')->get_contact_balance_label(NULL,$data['family_id']);
            $results = array(
                'status'    => 'success',
                'contact_balance'   => NULL,
                'family_balance'    => $family_balance
            );
        }

        exit(json_encode($results));
    }

    public function action_ajax_check_outstanding_transactions()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();

        $results=array('status'=>'success','outstanding'=>FALSE,'bookings'=>FALSE,'message'=>'');

        $bookings = DB::select('b1.booking_id',
            'b1.contact_id',
            'b1.booking_status',
            'b2.title')
            ->from(array('plugin_ib_educate_bookings','b1'))
            ->join(array('plugin_ib_educate_bookings_status','b2'))
                ->on('b1.booking_status','=','b2.status_id')
            ->where('b2.title','=','Confirmed')
            ->where('b1.contact_id','=',$data['contact_id'])
            ->execute()
            ->as_array();

        $outstanding_transactions = ORM::factory('Kes_Transaction')->get_contact_outstanding_transactions($data['contact_id']);

        if ($bookings OR $outstanding_transactions)
        {
            $results['message'].='<ul>';
        }

        if ($bookings)
        {
            $results['bookings']=TRUE;
            $results['message'].='<li>There are confirmed bookings for this contact.<br>Please cancel the bookings before proceeding.</li>';
        }
        if ($outstanding_transactions)
        {
            $results['outstanding']=TRUE;
            $results['message'].='<li>There are outstanding transactions for this contact.<br>Please pay or cancel the transactions.</li>';
        }

        if ($bookings OR $outstanding_transactions)
        {
            $results['message'].='</ul>';
        }

        exit(json_encode($results));
    }
}