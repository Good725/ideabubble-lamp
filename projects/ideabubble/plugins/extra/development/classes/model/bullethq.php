<?php
class Model_BulletHQ extends Model
{
    /*** Consider an Array Per Version as a future update for easy API switching ***/
    CONST API_BASE_URL              = 'https://accounts-app.bullethq.com/api/';
    CONST API_VERSION_URL           = 'v1/';
    CONST API_CLIENT_URL            = 'clients/';
    CONST API_INVOICE_URL           = 'invoices/';
    CONST API_PAYMENT_URL           = 'clientPayments/';
    CONST IDEABUBBLE_API_USERNAME   = 'accounts@ideabubble.ie';
    CONST IDEABUBBLE_API_PASSWORD   = 'd6daae2cf692299c994ccdcb9d59039e';
    CONST USER_AGENT                = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17';
    CONST HEADER_ACCEPT_TYPE        = 'Accept: application/json';
    CONST HEADER_CONTENT_TYPE       = 'Content-type: application/json';


    public function add_client($data, $customer_id)
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        $ch = curl_init();
        $url = self::API_BASE_URL . self::API_VERSION_URL . self::API_CLIENT_URL;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE,self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        $inf = curl_getinfo($ch);
        Model_ExternalRequests::create(
            self::API_BASE_URL,
            $url,
            json_encode($data),
            $result,
            $inf['http_code']
        );
        curl_close($ch);
        $data = json_decode($result, true);
        if (isset($data['id'])) {
            Model_Customers::update_bullethq_id($customer_id, $data['id']);
        }
        return $data;
    }

    public function delete_client($id)
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        $ch = curl_init();
        $url = self::API_BASE_URL . self::API_VERSION_URL . self::API_CLIENT_URL . $id;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE,self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        $inf = curl_getinfo($ch);
        Model_ExternalRequests::create(
            self::API_BASE_URL,
            $url,
            $id,
            $result,
            $inf['http_code']
        );
        curl_close($ch);
        return json_decode($result, true);
    }

    public function update_client($data, $id)
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        $ch = curl_init();
        $url = self::API_BASE_URL . self::API_VERSION_URL . self::API_CLIENT_URL . $id;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE,self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        $inf = curl_getinfo($ch);
        Model_ExternalRequests::create(
            self::API_BASE_URL,
            $url,
            json_encode($data),
            $result,
            $inf['http_code']
        );
        curl_close($ch);
        return json_decode($result, true);
    }
	
	public function list_clients()
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_BASE_URL . self::API_VERSION_URL . self::API_CLIENT_URL);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE,self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function create_invoice($data = array(),$use_cart = TRUE)
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        if($use_cart)
        {
            $this->update_cart($data);
        }

        $ch = curl_init();
        $url = self::API_BASE_URL . self::API_VERSION_URL . self::API_INVOICE_URL;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
        curl_setopt($ch,CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE, self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        $inf = curl_getinfo($ch);
        Model_ExternalRequests::create(
            self::API_BASE_URL,
            $url,
            json_encode($data),
            $result,
            $inf['http_code']
        );
        curl_close($ch);
        $data = json_decode($result, true);
		if(isset($data['id'])){
			$bullethqb = new Model_BulletHQB();
			$bullethqb->login();
			$data['token'] = $bullethqb->get_invoice_token($data['id']);
		}
        return $data;
    }

	public function list_invoices($client_id = null)
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_BASE_URL.self::API_VERSION_URL.self::API_INVOICE_URL);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE,self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
		if($client_id){
			$invoices = array();
			foreach($data as $invoice){
				if($invoice['clientId'] == $client_id){
					$invoices[] = $invoice;
				}
			}
			$data = $invoices;
		}
		//header('content-type: text/plain');print_r($data);die();        
		return $data;
    }

    public function add_payment($data)
    {
		$company_id = Settings::instance()->get('bullethq_company_id');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_BASE_URL.self::API_VERSION_URL.self::API_PAYMENT_URL);
        curl_setopt($ch, CURLOPT_USERPWD, self::IDEABUBBLE_API_USERNAME . ($company_id ? ':' . $company_id : '') . ':' . self::IDEABUBBLE_API_PASSWORD);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch,CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::HEADER_ACCEPT_TYPE,self::HEADER_CONTENT_TYPE));
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result,true);
        return $result;
    }

    private function update_cart(&$data)
    {
        $vat_rate = Settings::instance()->get('vat_rate');
        $cart = Session::instance()->get('cart');
        foreach($cart->items AS $key=>$item)
        {
            if($item->billable)
            {
                $data['invoiceLines'][] = array("quantity" => 1,
												"rate" => $item->price,
												"vatRate" => $vat_rate,
												"description" => $item->description,
												"item" => $item->item);
            }
        }
    }
}
?>