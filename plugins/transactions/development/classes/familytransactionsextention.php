<?php defined('SYSPATH') or die('No Direct Script Access.');

class FamilyTransactionsExtention extends  FamiliesExtention
{
    protected $cache = array();

    public function required_js()
    {
    }

    public function getData($family, $request = null)
    {
        if (isset($this->cache['data'])) {
            return $this->cache['data'];
        } else {
            $data = array();
            $params = array();

            if (isset($family['id'])) {
                $params['family_id'] = $family['id'];
            }

            $balance = 0;
            $data['transactions'] = Model_Transactions::search($params);
            foreach ($data['transactions'] as $transaction) {
                $balance += $transaction['outstanding'] * $transaction['income'];
            }
            $data['flags'] = array(
                array('class' => 'balance', 'text' => 'Balance: &euro;' . number_format($balance, 2), 'value' => $balance)
            );

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