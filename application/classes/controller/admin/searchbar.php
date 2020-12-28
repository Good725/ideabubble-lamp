<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Searchbar extends Controller_Cms
{
    protected static $registeredSearchPlugins = array();

    public static function register_globalsearch($object)
    {
        self::$registeredSearchPlugins[] = $object;
    }

    public function before()
    {
        parent::before();
    }

    public function action_index()
    {
    }

    public function action_ajax_getresults()
    {
        $this->auto_render = FALSE;
        $post     = $this->request->post();
        $get      = $this->request->query();
        $data     = (@$get['term'] != '') ? $get['term'] : @$post['searchparams'];
        $results  = array();
        $count    = 0;
		$user     = Auth::instance()->get_user();

        if ($data != '')
        {
            /* Get pages */
            if (Model_Plugin::get_isplugin_enabled_foruser($user['id'], 'pages'))
            {
                $json_items = Model_Pages::get_pages_json($data);
                $items      = (array) json_decode($json_items);

                if ($items['count'] > 0)
                {
                    $count += $items['count'];
                    foreach ($items['results'] as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Pages ('.$items['count'].')';
                        $ac_item['label']    = ($item['title'] != '') ?  $item['title'] : substr($item['name_tag'], 0, strpos($item['name_tag'], '.html'));
                        $ac_item['link']     = '/admin/pages/edit_pag/'.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get products */
            if (Model_Plugin::get_isplugin_enabled_foruser($user['id'], 'products'))
            {
                $json_items = Model_Product::get_products_json($data);
                $items      = (array) json_decode($json_items);

                if ($items['count'] > 0)
                {
                    $count += $items['count'];
                    foreach ($items['results'] as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Products ('.$items['count'].')';
                        $ac_item['label']    = trim($item['product_code'].' '.$item['title']);
                        $ac_item['link']     = '/admin/products/edit_product/?id='.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get courses */
            if (Model_Plugin::get_isplugin_enabled_foruser($user['id'], 'courses'))
            {
                $json_items = Model_Courses::get_courses_json($data);
                $items      = (array) json_decode($json_items);

                if ($items['count'] > 0)
                {
                    $count += $items['count'];
                    foreach ($items['results'] as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Courses ('.$items['count'].')';
                        $ac_item['label']    = $item['title'];
                        $ac_item['link']     = '/admin/courses/edit_course/?id='.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get schedules */
            if (Model_Plugin::get_isplugin_enabled_foruser($user['id'], 'courses'))
            {
                $json_items = Model_Schedules::get_schedules_json($data);
                $items      = (array) json_decode($json_items);

                if ($items['count'] > 0)
                {
                    $count += $items['count'];
                    foreach ($items['results'] as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Schedules ('.$items['count'].')';
                        $ac_item['label']    = $item['title'];
                        $ac_item['link']     = '/admin/courses/edit_schedule/?id='.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get contacts */
            if (class_exists('Model_Contacts') && Model_Plugin::is_enabled_for_role('Administrator', 'contacts2'))
            {
                if (!Auth::instance()->has_access('contacts2_edit')){
                    $user = Auth::instance()->get_user();
                    $user_id = $user['id'];
                } else {
                    $user_id = null;
                }
                $json_items = Model_Contacts::get_contacts_json($data, $user_id);
                $items      = (array) json_decode($json_items);

                if ($items['count'] > 0)
                {
                    $count += $items['count'];
                    foreach ($items['results'] as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Contacts ('.$items['count'].')';
                        $ac_item['label']    = $item['title'];
                        $ac_item['link']     = '/admin/contacts2/edit/'.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get IB Educate contacts (contacts3) */
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
            {
                $where = array(array(DB::expr('trim(concat_ws(\' \', `contact`.`first_name`, `contact`.`last_name`, `family`.`family_name`, `c_notif`.`value`))'), 'LIKE', '%'.$data.'%'));

                $items = Model_Contacts3::get_all_contacts($where);

                if (count($items) > 0)
                {
                    $count += count($items);
                    array_splice($items, 5);
                    foreach ($items as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Educate Contacts ('.count($items).')';

                        $ac_item['label']    = (isset($item['first_name']) ? $item['first_name']  : '').' ';
                        $ac_item['label']   .= (isset($item['last_name'])  ? $item['last_name']   : '').' - ';
                        $ac_item['label']   .= (isset($item['family'])     ? $item['family'] : '').' - ';
                        $ac_item['label']   .= (isset($item['mobile'])     ? $item['mobile']      : '');
                        $ac_item['label']    = trim(trim(trim(str_replace('-  -', '-', $ac_item['label'])), '-'));

                        $ac_item['link']     = '/admin/contacts3/?contact='.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get IB Educate bookings */
            if (class_exists('Model_KES_Bookings'))
            {
                $where   = array();
                $where[] = array();
                $items   = Model_KES_Bookings::get_contact_family_bookings(NULL, NULL, $data);

                if (count($items) > 0)
                {
                    $count += count($items);
                    array_splice($items, 5);
                    foreach ($items as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['booking_id'];
                        $ac_item['category'] = 'Education Bookings ('.count($items).')';
                        $ac_item['label']    = $item['booking_id'];
                        $ac_item['label']   .= ($item['student']        != '') ? ' - '.$item['student']        : '';
                        $ac_item['label']   .= ($item['schedule_title'] != '') ? ' - '.$item['schedule_title'] : '';
                        $ac_item['label']   .= ($item['course_title']   != '') ? ' - '.$item['course_title']   : '';
                        $ac_item['label']   .= ($item['year']           != '') ? ' - '.$item['year']           : '';
                        $ac_item['link']     = '/admin/bookings/?booking='.$item['booking_id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            /* Get reports */
            if (Model_Plugin::get_isplugin_enabled_foruser($user['id'], 'reports'))
            {
                $where = array();
                $where[] = array(DB::expr('CONCAT_WS(\' \', `report`.`name`, `category`.`name`)'), 'like', '%'.$data.'%');
                $items   = Model_Reports::get_reports($where);

                if (count($items) > 0)
                {
                    $count += count($items);
					$result_count = count($items);
                    array_splice($items, 5);
                    foreach ($items as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['id'];
                        $ac_item['category'] = 'Reports ('.$result_count.')';
                        $ac_item['label']    = trim(trim(trim($item['name'].' - '.$item['category_name']), '-'));
                        $ac_item['link']     = '/admin/reports/add_edit_report/'.$item['id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            if (class_exists('Model_Family') && class_exists('Model_Contacts3'))
            {
                $items = Model_Family::search_families($data);

                if (count($items) > 0)
                {
                    $count += count($items);
                    array_splice($items, 5);
                    foreach ($items as $item)
                    {
                        $item                = (array) $item;
                        $ac_item['id']       = $item['family_id'];
                        $ac_item['category'] = 'Families ('.count($items).')';
                        $ac_item['label']    = trim(trim(trim($item['family_name']), '-'));
                        $ac_item['link']     = '/admin/contacts3/add_edit_family/'.$item['family_id'];
                        $results[]           = $ac_item;
                    }
                }
            }

            foreach (self::$registeredSearchPlugins as $searchPlugin) {
                $searchPlugin->search($results, $count, $data);
            }

            /* */
        }
        $results[] = array('label' => 'count', 'value' => $count);
        $response  = ($count == 0) ? '[{"label":"No Results."}]' : json_encode($results);
        $this->response->body($response);
    }

}