<?php defined('SYSPATH') or die('No direct script access.');

class Model_Bookings extends Model
{
    const STATUS_S_OK = 0;
    const STATUS_E_ERROR = -1;
    const STATUS_E_MISSING_OPTIONS = -2;
    const STATUS_E_WRONG_COUPON_CODE = -3;
    const FIELD_DELIMITER = ",";


    private static function generate_response($status, $response_data)
    {
        $response = new stdClass();

        $response->status = $status;
        $response->data = $response_data;

        return $response;
    }

    public static function count_all($search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (`plugin_courses_courses`.`title` like '%" . $search . "%' OR `plugin_courses_categories`.`category` like '%" . $search . "%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT `plugin_courses_schedules`.`id` FROM `plugin_courses_bookings_migrate`
            LEFT JOIN
            `plugin_courses_schedules`
            ON
            `plugin_courses_schedules`.`id` = `plugin_courses_bookings_migrate`.`schedule_id`
            LEFT JOIN
            `plugin_courses_courses`
            ON
            `plugin_courses_courses`.`id` = `plugin_courses_schedules`.`course_id`
            LEFT JOIN
            `plugin_courses_categories`
            ON
            `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
			WHERE
			`plugin_courses_bookings_migrate`.`paid` = 1 " . $_search . "
			GROUP BY `plugin_courses_schedules`.`id`;")
            ->execute()
            ->as_array();
        return count($query);
    }

    public static function get_bookings_csv()
    {
        $query = DB::query(Database::SELECT,
            "SELECT first_name,last_name,address,email,gender,mobile,phone,teaching_co_reg,teaching_co_number,comments,school,school_address,roll_no,school_phone,school_county,county,date_modified FROM plugin_courses_bookings_migrate")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_bookings($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (`plugin_courses_courses`.`title` like '%" . $search . "%' OR `plugin_courses_categories`.`category` like '%" . $search . "%')";
        }
		$_limit = ($limit != -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT,
            "SELECT `plugin_courses_schedules`.`id`, `plugin_courses_courses`.`title` as `course`, `plugin_courses_categories`.`category`, `plugin_courses_schedules`.`start_date` as `schedule`, count(*) as `total`
                FROM
                `plugin_courses_bookings_migrate`
                LEFT JOIN
                `plugin_courses_schedules`
                ON
                `plugin_courses_schedules`.`id` = `plugin_courses_bookings_migrate`.`schedule_id`
                LEFT JOIN
                `plugin_courses_courses`
                ON
                `plugin_courses_courses`.`id` = `plugin_courses_schedules`.`course_id`
                LEFT JOIN
                `plugin_courses_categories`
                ON
                `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
                WHERE
                `plugin_courses_bookings_migrate`.`paid` = 1 " . $_search . "
			GROUP BY
			`plugin_courses_schedules`.`id`
			ORDER BY " . $sort . " " . $dir . " " . $_limit)
            ->execute()
            ->as_array();
        $return = array();
        if (count($query) > 0) {
            $i = 0;
            foreach ($query as $elem => $sub) {
                $return[$i]['course'] = '<a href="/admin/courses/bookings_people/?id=' . $sub['id'] . '">' . $sub['course'] . '</a>';
                $return[$i]['category'] = '<a href="/admin/courses/bookings_people/?id=' . $sub['id'] . '">' . $sub['category'] . '</a>';
                $return[$i]['schedule'] = '<a href="/admin/courses/bookings_people/?id=' . $sub['id'] . '">' . $sub['schedule'] . '</a>';
                $query = DB::query(Database::SELECT, "SELECT `date_created` FROM `plugin_courses_bookings_migrate` WHERE `schedule_id` = " . $sub['id'] . " ORDER BY `date_created` DESC LIMIT 0,1")
                    ->execute()
                    ->as_array();
                $return[$i]['last_booking'] = '<a href="/admin/courses/bookings_people/?id=' . $sub['id'] . '">' . $query['0']['date_created'] . '</a>';
                $return[$i]['total'] = '<a href="/admin/courses/bookings_people/?id=' . $sub['id'] . '">' . $sub['total'] . '</a>';
                $i++;
            }
        }

        return $return;
    }


    public static function count_all_for_schedule($id, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (`plugin_courses_bookings_migrate`.`first_name` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`last_name` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`email` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`phone` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`comments` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`school` like '%" . $search . "%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as count
            FROM
            `plugin_courses_bookings_migrate`
            WHERE
			`id` IS NOT NULL " . $_search . "
			AND
            `schedule_id` = " . $id . $_search . ";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }


    public static function get_all_for_schedule($id, $limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (`plugin_courses_bookings_migrate`.`first_name` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`last_name` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`email` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`phone` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`comments` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`school` like '%" . $search . "%' OR `school_address` LIKE '%" . $search . "%' OR `roll_no` LIKE '%" . $search . "%' OR `school_phone` LIKE '%" . $search . "%' OR `plugin_courses_counties`.`name` LIKE '%" . $search . "%')";
        }
        $_limit = ' LIMIT ' . $offset . ',' . $limit;
        $query = DB::query(Database::SELECT,
            "SELECT `first_name`, `last_name`, `email`, `phone`, `comments`, `school`, `school_address`, `roll_no`, `school_phone`, `plugin_courses_counties`.`name` as `county`, `plugin_courses_bookings_migrate`.`paid`
                FROM
                `plugin_courses_bookings_migrate`
                LEFT JOIN
                `plugin_courses_counties`
                ON
                `plugin_courses_counties`.`id` = `plugin_courses_bookings_migrate`.`county_id`
                WHERE
                `plugin_courses_bookings_migrate`.`id` IS NOT NULL " . $_search . "
                AND
                `plugin_courses_bookings_migrate`.`schedule_id` =" . $id . $_search . " ORDER BY " . $sort . " " . $dir . " " . $_limit)
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_all_applicants($schedule)
    {
        if (empty($schedule)) {
            $query = array("Result" => "Invalid Schedule ID");
        } else {
            $query = DB::query(Database::SELECT, "SELECT first_name,last_name,address,email,gender,mobile,phone,teaching_co_reg,teaching_co_number,comments,school,school_address,roll_no,school_phone,school_county,county,date_modified FROM plugin_courses_bookings_migrate WHERE plugin_courses_bookings_migrate.schedule_id = " . $schedule)->execute()->as_array();
        }
        return $query;
    }

    public static function save_booking($data)
    {
        if (self::validate_no_empty($data['first_name']) === false)
            return false;
        if (self::validate_no_empty($data['last_name']) === false)
            return false;
        if (self::validate_no_empty($data['email']) === false)
            return false;
        $insert = array(
            'first_name' => ($data['first_name']),
            'last_name' => ($data['last_name']),
            'email' => ($data['email']),
            'schedule_id' => (int)$data['schedule_id'],
            'paid' => 1,
            'payment_details' => 'Free course',
            'county' => ($data['county'])
        );
        if (isset($data['address']) AND strlen($data['address']) > 0) {
            $insert['address'] = ($data['address']);
        }
        if (isset($data['gender']) AND strlen($data['gender']) > 0) {
            $insert['gender'] = ($data['gender']);
        }
        if (isset($data['mobile']) AND strlen($data['mobile']) > 0) {
            $insert['mobile'] = ($data['mobile']);
        }
        if (isset($data['teaching_co_reg']) AND strlen($data['teaching_co_reg']) > 0) {
            $insert['teaching_co_reg'] = (int)$data['teaching_co_reg'];
        }
        if (isset($data['teaching_co_number']) AND strlen($data['teaching_co_number']) > 0) {
            $insert['teaching_co_number'] = ($data['teaching_co_number']);
        }
        if (isset($data['comments']) AND strlen($data['comments']) > 0) {
            $insert['comments'] = ($data['comments']);
        }
        if (isset($data['school']) AND strlen($data['school']) > 0) {
            $insert['school'] = ($data['school']);
        }
        if (isset($data['school_address']) AND strlen($data['school_address']) > 0) {
            $insert['school_address'] = ($data['school_address']);
        }
        if (isset($data['roll_no']) AND strlen($data['roll_no']) > 0) {
            $insert['roll_no'] = ($data['roll_no']);
        }
        if (isset($data['school_phone']) AND strlen($data['school_phone']) > 0) {
            $insert['school_phone'] = ($data['school_phone']);
        }


        $query = DB::insert('plugin_courses_bookings_migrate', array_keys($insert))
            ->values($insert)
            ->execute();
        return $query;
    }

    public static function save_ajax_booking($data)
    {
        if (self::validate_no_empty($data['schedule']) === false)
            return false;
        if (self::validate_no_empty($data['first_name']) === false)
            return false;
        if (self::validate_no_empty($data['last_name']) === false)
            return false;
        if (self::validate_no_empty($data['email']) === false)
            return false;
        if (self::validate_no_empty($data['address_1']) === false)
            return false;
        if (self::validate_no_empty($data['address_2']) === false)
            return false;
        $insert = array(
            'schedule_id' => (int)$data['schedule'],
            'first_name' => ($data['first_name']),
            'last_name' => ($data['last_name']),
            'email' => ($data['email']),
            'county' => (@$data['county']),
            'address' => ($data['address_1'] . ' ' . $data['address_2']),
            'key' => ($data['code'])
        );
        if (isset($data['mobile']) AND strlen($data['mobile']) > 0) {
            $insert['mobile'] = ($data['mobile']);
        }

        $query = DB::insert('plugin_courses_bookings_migrate', array_keys($insert))
            ->values($insert)
            ->execute();
        if ($query !== false) {
            $return['success'] = 1;
            $return['booking'] = $query['0'];
        }
        return $return;
    }

    public static function save_ajax_booking_with_cart($data)
    {
		if (self::validate_no_empty($data['schedule_id']) === false)
            return false;
        if (self::validate_no_empty($data['student_first_name']) === false)
            return false;
        if (self::validate_no_empty($data['student_last_name']) === false)
            return false;
        if (self::validate_no_empty($data['student_email']) === false)
            return false;
        $insert = array(
            'schedule_id' => (int)$data['schedule_id'],
            'first_name' => ($data['student_first_name']),
            'last_name' => ($data['student_last_name']),
            'email' => ($data['student_email']),
			'address' => (@$data['student_address1']." \n".@$data['student_address2']." \n".(isset($data['country']) ? $data['country'] : '')),
			'county_id' => (isset($data['county_id']) ? $data['county_id'] : ''),
			'phone' => (isset($data['phone']) ? $data['phone'] : ''),
			'comments' => (isset($data['comments']) ? $data['comments'] : '')
        );
        if (isset($data['student_mobile']) AND strlen($data['student_mobile']) > 0) {
            $insert['mobile'] = ($data['student_mobile']);
        }
        $insert['data'] = serialize($data);

        if ($insert['county_id'] === '') {
            $insert['county_id'] = null;
        }
        $query = DB::insert('plugin_courses_bookings_migrate', array_keys($insert))
            ->values($insert)
            ->execute();
        if ($query !== false) {
            $admin_fee = (Settings::instance()->get('admin_fee_toggle') === 'TRUE') ? Settings::instance()->get('admin_fee_price') : 0;
            $return['success'] = 1;
            $return['booking'] = $query['0'];
            $session = Session::instance();
            $session->delete('bookings');
            $_bookings = $session->get('bookings');
            $_bookings['cart'][$query['0']]['title'] = $data['training'];
            $_bookings['cart'][$query['0']]['schedule'] = $data['schedule_id'];
            $_bookings['cart'][$query['0']]['schedule_d'] = $data['schedule'];
            $_bookings['cart'][$query['0']]['price'] = $data['price'];
            $_bookings['cart'][$query['0']]['bid'] = $query['0'];
            if (isset($_bookings['cart']['amount'])) {
                $_bookings['cart']['amount'] = $_bookings['cart']['amount'] + $data['price'];
            } else {
                $_bookings['cart']['amount'] = $data['price'] + $admin_fee;
            }

            $session->set('bookings', $_bookings);
        }
        return $return;
    }

    private static function validate_no_empty($data, $minlength = 1, $maxlength = false)
    {
        $minlength--;
        if ($maxlength === false) {
            if (isset($data) AND strlen($data) > $minlength) {
                return true;
            } else {
                return false;
            }
        } else {
            $maxlength++;
            if (isset($data) AND strlen($data) > $minlength AND strlen($data) < $maxlength) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function checkout_booking($id)
    {
        $id = "";
        $details = array();

    }

    public function get_paypal_data($data)
    {
        try
		{
            $settings = Settings::instance();
            $business = FALSE;

            $ok = (isset($data->amount) AND ($business = $settings->get('paypal_email')) !== FALSE);

            if ($ok)
			{
                $form_data = new stdClass();

                // General
                $form_data->cmd = '_xclick';
                $form_data->upload = 1;
                $form_data->business = $business;
                $form_data->currency_code = 'EUR';
                $form_data->no_shipping = 1;
                $form_data->return = isset($data->return_url) ? $data->return_url : $_SERVER['HTTP_HOST'];
                $form_data->cancel_return = isset($data->cancel_return_url) ? $data->cancel_return_url : $_SERVER['HTTP_HOST'];
				$form_data->notify_url = Kohana_URL::base().'/frontend/payments/paypal_callback/booking';

                // Schedule
                $form_data->item_name = $data->title;
                $form_data->amount = $data->amount;
                $form_data->quantity = 1;
				$form_data->custom = $data->custom;

                $status = self::STATUS_S_OK;
                $response_data = $form_data;
            }
			else
			{
                $status = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
		catch (Exception $e)
		{
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return self::generate_response($status, $response_data);
    }

    public static function transaction_paid($code, $schedule, $amount, $channel)
    {
        $query = DB::query(Database::SELECT, "SELECT * FROM `plugin_courses_bookings_migrate` WHERE
            `key` = '" . ($code) . "' AND `schedule_id` = " . (int)$schedule)
            ->execute()
            ->as_array();
        if (is_array($query) AND count($query) > 1)
		{
            throw new Exception("Error while transaction is being processed. Please contact the site administrator.");
        }
		elseif (is_array($query) AND count($query) == 1)
		{
            $data = array(
                'paid' => 1,
                'payment_details' => serialize(
                    array(
                        'payment_date' => date("Y-m-d H:i:s"),
                        'amount' => ($amount),
                        'channel' => ($channel)
                    )
                )
            );
            DB::update('plugin_courses_bookings_migrate')
                ->set($data)
                ->where('id', '=', $query['0']['id'])
                ->execute();
            return;
        }
		else
		{
            throw new Exception("Error while transaction is being processed. Please contact the site administrator.");
        }
    }

	public static function update_booking($id, $data)
	{
		return DB::update('plugin_courses_bookings_migrate')->set($data)->where('id', '=', $id)->execute();
	}

	public static function get_booking_data($id)
	{
		$q =  DB::select()->from('plugin_courses_bookings_migrate')->where('id', '=', $id)->execute();
		return (count($q) > 0 ? $q[0] : array());
	}

    public static function cart_paid($code, $amount, $channel, $address1 = NULL, $address2 = NULL)
    {
        if (isset($code) AND strlen($code) > 0) {
            $code = substr($code, 0, -1);
            $in = " WHERE `id` IN (" . str_ireplace("|", ", ", $code) . ")";
            $query = DB::query(Database::SELECT, "SELECT * FROM `plugin_courses_bookings_migrate`" . $in)
                ->execute()
                ->as_array();
            if (is_array($query) AND count($query) > 0) {
                $data = serialize(array(
                    'payment_date' => date("Y-m-d H:i:s"),
                    'amount' => ($amount),
                    'channel' => ($channel),
                    'address1' => ($address1),
                    'address2' => ($address2)
                ));
                DB::query(Database::UPDATE, "UPDATE `plugin_courses_bookings_migrate` SET
                `paid` = 1,
                `payment_details` = '" . $data . "'" . $in)
                    ->execute();
                return;
            } else {
                throw new Exception("Error while transaction is being processed. Please contact with site administrator.");
            }
        } else {

        }
    }

    public static function get_one_by_code($code)
    {
        $query = DB::query(Database::SELECT, "SELECT `plugin_courses_bookings_migrate`.`id`, `plugin_courses_courses`.`summary`, `plugin_courses_schedules`.`start_date`
        FROM `plugin_courses_bookings_migrate`
        LEFT JOIN
        `plugin_courses_schedules`
        ON
        `plugin_courses_bookings_migrate`.`schedule_id` = `plugin_courses_schedules`.`id`
        LEFT JOIN
        `plugin_courses_courses`
        ON
        `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
        WHERE
        `plugin_courses_bookings_migrate`.`key` = '" . ($code) . "'")
            ->execute()
            ->as_array();
        if (is_array($query) && count($query) == 1) {
            return $query['0'];
        } else {
            return false;
        }
    }

    public static function get_csv_report($id, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (`plugin_courses_bookings_migrate`.`first_name` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`last_name` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`email` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`phone` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`comments` like '%" . $search . "%' OR `plugin_courses_bookings_migrate`.`school` like '%" . $search . "%')";
        }
        $query = DB::query(Database::SELECT, "SELECT
        `plugin_courses_bookings_migrate`.*,
        `plugin_courses_counties`.`name` as `county`
                FROM
                `plugin_courses_bookings_migrate`
                LEFT JOIN
                `plugin_courses_counties`
                ON
                `plugin_courses_counties`.`id` = `plugin_courses_bookings_migrate`.`county_id`
                WHERE
                `paid` = 1 " . $_search . "
                AND
                `plugin_courses_bookings_migrate`.`schedule_id` =" . $id . $_search)
            ->execute()
            ->as_array();
        $return = array();
        $i = 0;
        if (is_array($query) AND count($query) > 0) {
            foreach ($query as $elem => $val) {
                foreach ($val as $_skey => $_sval) {
                    if ($_skey == 'data') {
                        $r = unserialize($_sval);
                        if (is_array($r) AND count($r) > 0) {
                            foreach ($r as $rt => $rk) {
                                $return[$i]['data_' . $rt] = $rk;
                            }
                        }
                        unset($r);
                    } else {
                        $return[$i][$_skey] = $_sval;
                    }
                }
                $i++;
            }
            $filename = "bookings_export_" . date("%Y_%m_%d_%H_%i_%s");
            ExportCsv::export_report_data_array();
        }

    }

    public static function clear_cart()
    {
        $session = Session::instance();
        $session->delete('bookings');
        return true;
    }

    public static function get_calendar_feed()
    {
        //we're getting the schedule start dates, news [condition] date_to_publish (1st priority) OR date_created
        //for now we limit this from today onwards.
        $query = DB::query(Database::SELECT,"SELECT date_publish AS `date`,
                                            `id` AS news_id,
                                            title AS news_title,
                                            '' AS `name`,
                                            '' AS course_name FROM plugin_news
                                            WHERE date_publish <= NOW()
                                            UNION
                                            SELECT start_date AS `date`,
                                            '' AS news_id,
                                            '' AS news_title,
                                            `id` AS course_id,
                                            `name` AS course_name
                                            FROM plugin_courses_schedules
                                            WHERE start_date >= NOW()
                                            ORDER BY `date`")->execute()->as_array();
        return $query;
    }
}