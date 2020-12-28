<?php defined('SYSPATH') or die('No direct script access.');
class Model_Payments extends Model{

 /*Function used to log purchase data.
 * @param $data - this is a standard object containing the relevant details of a purchase.
 * Returns true on success, false on error.
 */
    public static function log_payment($data,$cart_id,$invoicepayment = false,$stripe_payment = false,$charge = null)
    {
        if($invoicepayment)
        {
            self::sort_invoice_payment_data($data);
        }
        
        if(!isset($data->paid))
        {
            $data->paid = 1;
        }

        if(!isset($data->realex_status))
        {
            $data->realex_status = 'Realex OK';
        }

        if ($stripe_payment)
        {
            $data->realex_status = 'Stripe OK';
            $data->cc_num = isset($charge->card->last4) ? $charge->card->last4 : (isset($charge->source->last4) ? $charge->source->last4 : '');
            $data->ccType = isset($charge->card->brand) ? $charge->card->brand : (isset($charge->source->brand) ? $charge->source->brand : '');;
        }

		$data->cc_num     = (isset($data->cc_num) AND strlen($data->cc_num) <= 4) ? $data->cc_num : substr($data->ccNum, -4);
		$address          = '';
        if (isset($data->address_1)) $address .= "\n".$data->address_1;
        if (isset($data->address_2)) $address .= "\n".$data->address_2;
        if (isset($data->address_3)) $address .= "\n".$data->address_3;
        if (isset($data->address_4)) $address .= "\n".$data->address_4;
        if (isset($data->address1))  $address .= "\n".$data->address1;
        if (isset($data->address2))  $address .= "\n".$data->address2;
        if (isset($data->address3))  $address .= "\n".$data->address3;
        if (isset($data->address4))  $address .= "\n".$data->address4;
		$checkout         = new Model_Checkout();
		$products         = $checkout->get_cart_details();
        $price            = $invoicepayment ? $data->payment_total : (isset($products->final_price) ? $products->final_price : (isset($data->amount) ? $data->amount : ''));
		$shipping_surname = isset($data->shipping_surname) ? ' '.$data->shipping_surname : '';
		$values = array(
			'cart_details'             => isset($data->products) ? json_encode($data->products) : '',
            'customer_name'            => isset($data->shipping_name)
                ? trim($data->shipping_name).$shipping_surname
                : (isset($data->person_name)
                        ? $data->person_name
                        : (isset($data->ccName) ? $data->ccName : '')),
            'customer_telephone'       => isset($data->phone) ? $data->phone : (isset($data->mobile) ? $data->mobile : ''),
            'customer_address'         => $address,
            'customer_email'           => $data->email,
			'customer_user_id'         => @$data->customer_user_id,
			'delivery_method'          => @$data->delivery_method,
			'store_id'                 => @$data->store_id,
            'paid'                     => $data->paid,
            'cart_id'                  => $cart_id,
            'payment_type'             => $data->ccType,
            'cc_num'                   => $data->cc_num,
            'payment_amount'           => $price,
            'ip_address'               => $_SERVER['REMOTE_ADDR'],
            'user_agent'               => $_SERVER['HTTP_USER_AGENT'],
            'purchase_time'            => date('Y-m-d H:i:s',time()),
            'realex_status'            => $data->realex_status,
			'order_reference'          => isset($data->purchase_order_reference) ? $data->purchase_order_reference : ''
        );
        $payment_log_id = DB::insert('plugin_payments_log',array_keys($values))->values($values)->execute();

        return isset($payment_log_id[0]) ? $payment_log_id[0] : false;
    }

    private static function sort_invoice_payment_data(&$data)
    {
        if(!isset($data->address_1))
        {
            $data->address_1 = '';
        }

        if(!isset($data->address_2))
        {
            $data->address_2 = '';
        }

        if(!isset($data->address_3))
        {
            $data->address_3 = '';
        }

        if(!isset($data->address_4))
        {
            $data->address_4 = '';
        }

        if(!isset($data->shipping_name))
        {
            $data->shipping_name = $data->ccName;
        }

        if(!isset($data->address_3))
        {
            $data->address_3 = '';
        }

        if(!isset($data->address_3))
        {
            $data->address_3 = '';
        }

        if(!isset($data->products))
        {
            if(isset($data->course_name))
            {
                $data->products = $data->course_name;
            }
        }

        if(!isset($data->realex_status))
        {
            $data->realex_status = '';
        }

        if(!isset($data->paid))
        {
            $data->paid = 1;
        }
    }

    public static function get_thank_you_page($args = [])
    {
        $full_url     = isset($args['full_url'])    ? $args['full_url']    : true;
        $is_donation  = isset($args['is_donation']) ? $args['is_donation'] : false;

        // Legacy support for one instance that could not be updated without causing a conflict.
        // This can be removed once tha has been safely updated.
        if ($args === false) {
            $full_url = false;
        }

        $setting_name = $is_donation ? 'donation_thank_you_page' : 'shopping_thank_you_page';
        $page_id      = Settings::instance()->get($setting_name);
        $page         = '';

        if ($page_id && class_exists('Model_Pages') AND $page_id) {
            $page = Model_Pages::get_page_by_id($page_id);
        }

        if (!$page) {
            $page = 'thanks-for-shopping-with-us.html';
        }

        // Include the base URL before the page name, if the $full_url parameter is TRUE
        return $full_url ? URL::site().$page : $page;
    }

    public static function validate_courses_details($post)
    {

        $valid = true;
        try{

            if( !isset($post) OR empty($post) ) return false;

            if( !isset($post->amount) OR empty($post->amount) ) return false;


        }
        catch(Exception $e){
            $valid = false;
        }
        return $valid;
    }

    public static function card_save($contact_id, $order_id, $card_type, $card_number, $expdate, $holder_name)
    {
        $user = Auth::instance()->get_user();
        $now = date::now();

        $contact = new Model_Contacts3($contact_id);
        if ($contact_id == null) {
            $c = Model_Contacts3::get_linked_contact_to_user($user['id']);
            if ($c) {
                $contact = new Model_Contacts3($c['id']);
            }
        }
        if (Settings::instance()->get('enable_realex') == 1) {
            $realvault = new Model_Realvault();
            $payer_ref = DB::select(DB::expr("UUID() as uuid"))->execute()->get('uuid');
            $payer = $realvault->create_payer($payer_ref, $contact->get_first_name(), $contact->get_last_name(), $contact->get_email(), $order_id);
            if ($payer->result == '00') {
                $inserted = DB::insert(Model_Contacts3::PAYMENTGW_TABLE)
                    ->values(
                        [
                            'contact_id' => $contact_id,
                            'paymentgw' => 'realex',
                            'customer_id' => $payer_ref,
                            'created' => $now,
                            'created_by' => $user['id'],
                            'updated' => $now,
                            'updated_by' => $user['id']
                        ]
                    )->execute();
                $gw_id = $inserted[0];
                $card_ref = DB::select(DB::expr("UUID() as uuid"))->execute()->get('uuid');
                $card = $realvault->create_card($card_type, $card_number, $expdate, $holder_name, $payer_ref, $card_ref, $order_id);
                if ($card->result == '00') {
                    $inserted = DB::insert(Model_Contacts3::HAS_CARDS_TABLE)
                        ->values(
                            [
                                'has_paymentgw_id' => $gw_id,
                                'card_id' => $card_ref,
                                'last_4' => substr($card_number, -4),
                                'created' => $now,
                                'created_by' => $user['id'],
                                'updated' => $now,
                                'updated_by' => $user['id'],
                                'exp_year' => '20' . substr($expdate, 2),
                                'exp_month' => substr($expdate, 0, 2),
                            ]
                        )->execute();
                    $card_id = $inserted[0];
                    return $card_id;
                } else {
                    DB::update(Model_Contacts3::PAYMENTGW_TABLE)->set(['deleted' => 1])->where('id', '=', $gw_id)->execute();
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
?>