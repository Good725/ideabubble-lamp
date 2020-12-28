<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Transactions extends Controller_Cms
{
    function before()
    {
        if (!Auth::instance()->has_access('transactions_limited_access') && !Auth::instance()->has_access('transactions')){
            if ($this->request->is_ajax()) {
                $this->response->status(403);
                exit();
            } else {
                IbHelpers::set_message(__('You have no permission for this page.'), 'info');
                $this->request->redirect('/admin');
            }
        }
        parent::before();

    }

    public function action_index()
    {
        $user = Auth::instance()->get_user();
        $params = array();
        if (Auth::instance()->has_access('transactions_limited_access')) {
            $params['user_id'] = $user['id'];
        }
        $transactions = Model_Transactions::search($params);

        $assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
        $this->template->scripts[] = '<script type="text/javascript" src="/assets/' . $assets_folder_path . '/js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="/assets/' . $assets_folder_path . '/js/jquery.validationEngine2-en.js"></script>';
        //$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts2') . 'js/contacts.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('transactions') . 'js/transactions.js"></script>';


        $this->template->body = View::factory('transactions_list_self');
        $this->template->body->data = array('transactions' => $transactions);
    }

    public function action_get_transaction_data()
    {
        $transaction_id = $this->request->post('transaction_id');
        $transaction = Model_Transactions::load($transaction_id);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(array('transaction' => $transaction));
    }

    public function action_transaction_has_changed()
    {
        $transaction_id = $this->request->post('transaction_id');
        $updated = $this->request->post('updated');
        $changed = Model_Transactions::test_has_changed($transaction_id, $updated);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(array('changed' => $changed));
    }

    public function action_get_payments()
    {
        $transaction_id = $this->request->query('transaction_id');
        $params = array('transaction_id' => $transaction_id);
        $payments = Model_TransactionPayments::search($params);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(array('payments' => $payments));
    }

    public function action_make_payment()
    {
        $post = $this->request->post();

        $gateway = $post['gateway'];
        $payment_handler = Model_TransactionPayments::get_gateway_handler($gateway);
        $payment = $payment_handler->process(
            Model_TransactionPayments::save(
                array(
                    'to_transaction_id' => $post['transaction_id'],
                    'type' => 'Payment',
                    'currency' => $post['currency'],
                    'amount' => $post['amount'],
                    'gateway' => $gateway,
                    'status' => 'Processing'
                )
            ),
            $post
        );
        if (@$payment['id'] && @$post['note']) {
            Model_Notes::create('Payment', $payment['id'], $post['note']);
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(array('payment' => $payment));
    }
}
