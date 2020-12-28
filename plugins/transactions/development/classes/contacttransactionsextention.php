<?php defined('SYSPATH') or die('No Direct Script Access.');

class ContactTransactionsExtention extends  ContactsExtention
{
    protected $cache = array();

    public function required_js()
    {
        return array(
            URL::get_engine_plugin_assets_base('transactions') . 'js/transactions.js'
        );
    }

    public function getData($contact, $request = null)
    {
        if (isset($this->cache['data'])) {
            return $this->cache['data'];
        } else {
            $data = array();
            $params = array();

            if (isset($contact['id'])) {
                $params['contact_id'] = $contact['id'];

                if ($request && $request->query('family_id')) {
                    $params['family_id'] = $request->query('family_id');
                }

                $balance = 0;
                $data['transactions'] = Model_Transactions::search($params);
                foreach ($data['transactions'] as $transaction) {
                    $balance += $transaction['outstanding'] * $transaction['income'];
                }
                $data['flags'] = array(
                    array('class' => 'balance', 'text' => 'Balance: &euro;' . number_format($balance, 2), 'value' => $balance)
                );


            } else {
                $data['transactions'] = array();
            }

            $this->cache['data'] = $data;
            return $data;
        }
    }

    public function saveData($contact_id, $post)
    {

    }

    public function getTabs($contact_details)
    {
        return array(
            array(
                'name' => 'accounts',
                'title' => 'Accounts',
                'view' => 'transaction_list_contact_extention',
            )
        );
    }

    public function getFieldsets($contact_details)
    {
        return array();
    }

    public function is_container()
    {
        return false;
    }

    public function get_container()
    {
        return null;
    }

}