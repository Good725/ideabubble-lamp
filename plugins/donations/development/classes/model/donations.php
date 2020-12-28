<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Donations extends ORM
{
    const TABLE_PRODUCTS = 'plugin_donations_products';
    const TABLE_DONATIONS = 'plugin_donations_donations';

    public static function product_details($product_id)
    {
        $product = DB::select(
            'products.*',
            DB::expr("count(*) as `requests`"),
            array('donations.product_id', 'requested')
        )
            ->from(array(self::TABLE_PRODUCTS, 'products'))
            ->join(array(self::TABLE_DONATIONS, 'donations'), 'left')->on('products.id', '=', 'donations.product_id')
            ->where('products.id', '=', $product_id)
            ->group_by('products.id')
            ->execute()
            ->current();
        return $product;
    }

    public static function product_save($product)
    {
        $exists = self::product_details(@$product['id']);
        if ($exists){
            DB::update(self::TABLE_PRODUCTS)->set($product)->where('id', '=', $product['id'])->execute();
            $product = self::product_details($product['id']);
        } else {
            $result = DB::insert(self::TABLE_PRODUCTS)->values($product)->execute();
            $product = self::product_details($result[0]);
        }

        return $product;
    }

    public static function products()
    {
        $products = DB::select(
            'products.*',
            DB::expr("count(*) as `requests`"),
            array('donations.product_id', 'requested')
        )
            ->from(array(self::TABLE_PRODUCTS, 'products'))
                ->join(array(self::TABLE_DONATIONS, 'donations'), 'left')->on('products.id', '=', 'donations.product_id')
            ->where('products.deleted', '=', 0)
            ->group_by('products.id')
            ->execute()
            ->as_array();
        return $products;
    }

    public static function search($params = array())
    {
        if (@$params['count_only']) {
            $searchq = DB::select(
                DB::expr("count(*) as `qty`")
            );
        } else {
            $searchq = DB::select(
                'donations.*',
                array('products.name', 'product'),
                array('products.value', 'cost'),
                'contacts.mobile',
                'messages.message',
                'qty.qty',
                'qty.total_paid'
            );
        }

        $searchq
            ->from(array(self::TABLE_DONATIONS, 'donations'))
                ->join(array(self::TABLE_PRODUCTS, 'products'), 'left')
                    ->on('donations.product_id', '=', 'products.id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'left')
                    ->on('donations.contact_id', '=', 'contacts.id')
                ->join(array('plugin_messaging_messages', 'messages'), 'left')
                    ->on('donations.message_id', '=', 'messages.id')
            ->where('donations.deleted', '=', 0);

        if (!@$params['count_only']) {
            $request_counts = DB::select(
                DB::expr("count(*) as qty"),
                DB::expr("sum(if(donations.status = 'completed', products.value, 0)) as `total_paid`"),
                "donations.contact_id"
            )
                ->from(array(self::TABLE_DONATIONS, 'donations'))
                    ->join(array(self::TABLE_PRODUCTS, 'products'), 'left')
                        ->on('donations.product_id', '=', 'products.id')
                ->group_by('donations.contact_id');

            $searchq->join(array($request_counts, 'qty'), 'left')->on('donations.contact_id', '=', 'qty.contact_id');
        }

        if (@$params['mobile']) {
            $searchq->and_where('contacts.mobile', '=', $params['mobile']);
        }
        if (@$params['status']) {
            $searchq->and_where('donations.status', '=', $params['status']);
        }
        if (array_key_exists('product_id', $params)) {
            if ($params['product_id'] === null) {
                $searchq->and_where('donations.product_id', 'is', null);
            } else {
                $searchq->and_where('donations.product_id', '=', $params['product_id']);
            }
        }

        if (@$params['count_only']) {
            $qty = $searchq->execute()->get('qty');
            return $qty;
        } else {
            $searchq->order_by('id', 'desc');

            $donations = $searchq->execute()->as_array();
            foreach ($donations as $i => $donation) {
                $donations[$i]['message'] = html::entities($donation['message']);
            }
            return $donations;
        }
    }

    public static function get($id)
    {
        $searchq = DB::select(
            'donations.*',
            array('products.name', 'product'),
            array('products.value', 'cost'),
            'contacts.mobile',
            'messages.message'
        )
            ->from(array(self::TABLE_DONATIONS, 'donations'))
            ->join(array(self::TABLE_PRODUCTS, 'products'), 'left')
            ->on('donations.product_id', '=', 'products.id')
            ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'left')
            ->on('donations.contact_id', '=', 'contacts.id')
            ->join(array('plugin_messaging_messages', 'messages'), 'left')
            ->on('donations.message_id', '=', 'messages.id')
            ->where('donations.deleted', '=', 0);

        $searchq->and_where('donations.id', '=', $id);

        $donation = $searchq->execute()->current();
        return $donation;
    }

    public static function status_set($id, $status = null, $reply = null, $note = null, $paid = null, $custom_mobile = null, $mute = null)
    {
        $user = Auth::instance()->get_user();

        $data = array(
            'updated' => date::now(),
            'updated_by' => $user['id']
        );
        if ($status != '') {
            $data['status'] = $status;
        }

        $donation = DB::select('*')->from(self::TABLE_DONATIONS)->where('id', '=', $id)->execute()->current();
        if ($note || $reply) {
            $data['note'] = date::now() . '<br />' . $note . ($reply != '' ? '<br /><b>' . $reply . '</b>': '');
            if ($donation['note']) {
                $data['note'] .= '<br />' . $donation['note'];
            }

        }
        if ($paid) {
            $data['paid'] = $paid;
        }
        DB::update(self::TABLE_DONATIONS)->set($data)->where('id', '=', $id)->execute();
        $donation = self::get($id);

        if ($mute == "1") {
            $contact = new Model_Contacts($donation['contact_id']);
            $contact_details = $contact->get_details();
            if ($contact_details['mobile']) {
                Model_Messaging::mute($contact_details['mobile']);
            }
        }

        if ($reply && $mute != "1") {
            try {
                $mm = new Model_Messaging();

                    $mm->send(
                        'sms',
                        null,
                        null,
                        array(
                            array(
                                'target_type' => ($custom_mobile != '' ? 'PHONE' : 'CMS_CONTACT'),
                                'target' => ($custom_mobile != '' ? $custom_mobile : $donation['contact_id'])
                            )
                        ),
                        $reply,
                        ''
                    );
            } catch (Exception $exc) {
                Log::instance()->add(Log::ERROR, "Could not send reply message (" . $exc->getMessage() . ')');
            }
        }
    }

    public static function get_messages($mobile)
    {
        $contact_id = null;
        $contacts = Model_Contacts::search(array('mobile' => $mobile));
        if ($contacts) {
            $contact_id = $contacts[0]['id'];
        }
        $searchq = DB::select('m.id', 'm.sender', 'm.message', array('m.date_created', 'created'))
            ->from(array('plugin_messaging_messages', 'm'))
            ->join(array('plugin_messaging_message_targets', 't'), 'inner')->on('m.id', '=', 't.message_id')
            ->join(array('plugin_messaging_message_final_targets', 'f'), 'inner')->on('t.id', '=', 'f.target_id')
            ->where('m.deleted', '=', 0);
        $searchq->and_where_open();
        if ($contact_id) {
            $searchq->or_where_open();
            $searchq->and_where('t.target_type', '=', 'CMS_CONTACT')
                ->and_where('t.target', '=', $contact_id);
            $searchq->or_where_close();

            $searchq->or_where_open();
            $searchq->and_where('t.target_type', '=', 'PHONE')
                ->and_where('t.target', '=', $mobile);
            $searchq->or_where_close();

            $searchq->or_where_open();
            $searchq->and_where('f.target_type', '=', 'PHONE')
                ->and_where('t.target', '=', $mobile);
            $searchq->or_where_close();
        }
        $searchq->and_where_close();

        $searchq->order_by('m.id', 'desc');
        $messages = $searchq->execute()->as_array();

        return $messages;
    }
}
