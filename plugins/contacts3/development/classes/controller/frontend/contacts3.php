<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Frontend_Contacts3 extends Controller_Cms
{
    private $contact;

    function before()
    {
        if ($this->request->action() == 'reject_invite') {
            return;
        }
        $this->require_login = $require_login = Settings::instance()->get('bookings_checkout_login_require') == 1;
        $bookings_interview_login_require = (int)Settings::instance()->get('bookings_interview_login_require');

        $interview = (int)$this->request->post('is_interview') == 1;

        if ($bookings_interview_login_require == 0 && $this->request->action() == 'ajax_submit_checkout' && $interview) {
            $this->require_login = $require_login = false;
        }
        $post= $this->request->post();
        if (@$post['confirmation'] == 'subscription' && is_numeric(@$post['booking_id'])) {
            $this->require_login = $require_login = false;
        }

        parent::before();

        $user = Auth::instance()->get_user();
        $this->user = Model_Users::get_user($user['id']);
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        if(!isset($contacts[0]) && $require_login){
            IbHelpers::set_message('Your account needs to be linked to a contact profile in order to access this feature.');
            $this->request->redirect('admin');
        }
        if (@$contacts[0]) {
            $this->contact = new Model_Contacts3($contacts[0]['id']);
        } else {
            $this->contact = null;
        }

        $this->template->page_data = array(
            'seo_description' => '',
            'seo_keywords' => '',
            'title' => __('Profile'),
            'content' => '',
            'layout' => 'content',
            'banner_photo' => '',
            'theme_home_page' => '',
            'name_tag' => ''
        );
        $this->template->body  = '';
        $this->template->scripts = array();
        $this->template->title = __('Profile');
        $this->template->sidebar = new stdClass();
        $this->template->header = new stdClass();
        $this->template->sidebar->menus = array();
        $this->template->sidebar->tools = '';

        $this->template->sidebar->frontend_menus = $this->get_frontend_menus();
    }

    public function after()
    {
        if (isset($this->template->body) && $this->template->body) {
            $nmessages = array();
            if ($this->contact->has_preference('bookings')) {
                $wishes = Model_KES_Wishlist::search(array('contact_ids' => Model_Contacts3::get_all_family_members_ids_for_guardian_by_user($this->user['id'])));
                foreach ($wishes as $wish) {
                    $nmessage = array();
                    $nmessage['time'] = $wish['created'];
                    $nmessage['type'] = 'Wishlist';
                    $mcontact = new Model_Contacts3($wish['contact_id']);
                    $nmessage['title'] = $wish['course'] . ' ' . $wish['schedule'] . ' has been added to ' . $mcontact->get_first_name() . ' ' . $mcontact->get_last_name() . '\'s wishlist';
                    $nmessages[] = $nmessage;
                }

                $bookings = Model_KES_Bookings::get_contact_family_bookings($this->contact->get_family_id(), null, null,
                    false);

                foreach ($bookings as $booking) {
                    $nmessage = array();
                    $nmessage['time'] = $booking['date_created'];
                    $nmessage['type'] = 'Booking';
                    $nmessage['title'] = $booking['course_title'] . ' ' . $booking['schedule'] . ' has been booked for ' . $booking['student'] . ' by ' . $booking['modified_by_name'] . ' ' . $booking['modified_by_surname'];
                    $nmessages[] = $nmessage;
                }
            }

            $this->template->body->nmessages = $nmessages;
        }

        parent::after();
    }

    public function action_cron_gdpr_cleanse()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf8');
        $params = $this->request->query();
        if (isset($GLOBALS['argv'])) {
            foreach ($GLOBALS['argv'] as $arg) {
                if (preg_match('/(.+)=(.+)/', $arg, $match)) {
                    $params[$match[1]] = $match[2];
                }
            }
        }
        Model_GDPR::cleanse(@$params['date'], @$params['report']);
    }

    public function get_frontend_menus()
    {
        $frontend_menus = array();

        $frontend_menus[] = array('name' => 'Home', 'icon' => 'fa fa-home', 'link' => 'dashboard.html');

        if(Auth::instance()->has_access('contacts3_frontend_bookings')) {
            $frontend_menus[] = array('name' => 'Bookings', 'icon' => 'flaticon-receipt', 'link' => '/bookings.html');
        }

        if(Auth::instance()->has_access('contacts3_frontend_accounts')) {
            $frontend_menus[] = array('name' => 'Accounts', 'icon' => 'flaticon-cog', 'link' => '/accounts.html');
        }

        if(Auth::instance()->has_access('contacts3_frontend_timesheets')) {
            $frontend_menus[] = array('name' => 'Timesheets', 'icon' => 'flaticon-time', 'link' => '/admin/timesheets');
        }

        if(Auth::instance()->has_access('contacts3_frontend_timetables')) {
            $frontend_menus[] = array('name' => 'Timetables', 'icon' => 'flaticon-calendar-dates', 'link' => '/timetables.html');
        }

        if(Auth::instance()->has_access('courses_schedule_edit_limited')) {
            $frontend_menus[] = array('name' => 'My Schedules', 'icon' => 'flaticon-calendar', 'link' => '/admin/courses/schedules');
        }

        if(Auth::instance()->has_access('contacts3_frontend_attendance')) {
            $frontend_menus[] = array('name' => 'Attendance', 'icon' => 'fa fa-check-square-o', 'link' => '/admin/contacts3/attendance');
        }

        if(Auth::instance()->has_access('contacts3_frontend_wishlist')) {
            $frontend_menus[] = array('name' => 'Wishlist', 'icon' => 'sidebar-wishlist', 'link' => '/wishlist.html');
        }

        $frontend_menus[] = array('name' => 'Profile', 'icon' => 'flaticon-avatar', 'link' => '/admin/profile/edit?section=contact');
        $frontend_menus[] = array('name' => 'Log Out', 'icon' => 'flaticon-logout', 'link' => '/admin/login/logout');
        return $frontend_menus;
    }
    
    public function action_index()
    {
        $this->request->redirect('/admin/contacts3/profile');
    }

    public static function send_application_emails($student_id, $course_ids = [])
    {
        $student  = new Model_Contacts3($student_id);
        $family   = new Model_Family($student->get_family_id());
        $guardian = new Model_Contacts3($family->get_primary_contact_id());
        $params = array(
            'student' => $student->get_first_name() . ' ' . $student->get_last_name(),
            'guardian' => $guardian->get_first_name() . ' ' . $guardian->get_last_name(),
        );

        $course_ids = array_unique($course_ids);
        $course_titles = '';
        foreach ($course_ids as $course_id) {
            $course = Model_Courses::get_course($course_id);
            $course_titles .= ', '.$course['title'];
        }
        $params['course'] = trim($course_titles, ', ');

        $recipients = array();
        $recipients[] = array('target_type' => 'CMS_CONTACT3', 'target' => $student_id);
        if ($guardian->get_id() > 0) {
            $recipients[] = array('target_type' => 'CMS_CONTACT3', 'target' => $guardian->get_id());
        }
        $mm = new Model_Messaging();
        $mm->send_template('fulltime-course-application-customer', null, null, $recipients, $params);
        $mm->send_template('fulltime-course-application-admin', null, null, array(), $params);
    }

    public static function send_interview_emails($data)
    {
        $admin_msg = View::factory('email/course_interview_application_admin', array('data' => $data));

        $mm = new Model_Messaging();
        $mm->send_template(
            'course-interview-admin',
            $admin_msg
        );

        /*
        $mm->send_template(
            'course-interview-student',
            null,
            null,
            array(
                array('target_type' => 'EMAIL', 'target' => $data['student_email'])
            )
        );
        */
    }

    public static function send_org_emails($data, $org_rep_details, $delegate_details) {
        $mm = new Model_Messaging();
        $delegate_org_printer = '';
        $schedule_name = $data['schedule_info']['name'] ?? '';
        $organisation = $data['organisation'] ?? new Model_Contacts3();
        $delegate_name = $org_rep_details->get_contact_name();
        foreach($delegate_details as $delegate_detail) {
            $delegate_org_printer .= "<tr>";
            $delegate_org_printer .= "<th>Delegate name</th><td>{$delegate_detail->get_contact_name()}</td>";
            $delegate_org_printer .= "<th>Delegate email</th><td>{$delegate_detail->get_email()}</td>";
            $delegate_org_printer .= "</tr>";
            // don't send the delegate email to the org rep if they are a delegate but include them in the org rep email as a delegate
            if($delegate_detail->get_id() == $org_rep_details->get_id()) {
                continue;
            }
            $delegate_recipient = array(
                array('target_type' => 'CMS_CONTACT3', 'target' => $delegate_detail->get_id())
            );
            $delegate_vars = array('customer' => $delegate_detail->get_contact_name(), 'hostname' => PROJECTNAME ?? '',
                'delegate_name' => $delegate_name, 'organisation_name' => $organisation->get_first_name(),
                'schedule_name' => $schedule_name);
            $mm->send_template('new_booking_delegate', null, null, $delegate_recipient, $delegate_vars);
           
        }
        $org_recipient = array(
            array('target_type' => 'CMS_CONTACT3', 'target' => $org_rep_details->get_id())
        );
        $org_rep_vars = array('customer' => $org_rep_details->get_contact_name(), 'hostname' => PROJECTNAME ?? '',
            'schedule_name' => $schedule_name);
        $org_rep_vars['delegate_details'] = ['value' => $delegate_org_printer, 'html' => true];
        $mm->send_template('new_booking_org_rep', null, null, $org_recipient, $org_rep_vars);
    }
    
    public function action_ajax_submit_checkout()
    {
        $create_family = Settings::instance()->get('contacts_create_family') == 1;
        $this->auto_render = false;
        if ($this->is_external_referer()){
            $error_id = Model_Errorlog::save(null, "SECURITY");
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/');
        }

        $is_microsite = (isset(Kohana::$config->load('config')->project_suffix) && Kohana::$config->load('config')->project_suffix == 'lang');
        try
        {
            $post              = $this->request->post();
            $data              = $post;
            if (!isset($data['courses'])) {
                $data['courses'] = array();
            }
            $formprocessor     = new Model_Formprocessor();
            $guardian_auth     = null;

            $data['payment_method'] = isset($data['payment_method']) ? $data['payment_method'] : 'cc';
            $bookings_interview_login_require = (int)Settings::instance()->get('bookings_interview_login_require');

            $interview = (int)$this->request->post('is_interview') == 1;
            if ($interview) {
                $data['payment_method'] = null;
            }

            /** Check CAPTCHA **/
            if ( ! $formprocessor->captcha_check($post))
            {
                $return = array(
                    'success' => false,
                    'error_message' => __('Error validating CAPTCHA.'),
                );

                echo json_encode($return);
                return json_encode($return);
            }


            if (@$post['payment_method'] == 'purchase_order') {
                if ($post['has_aiq_account'] == 'no') {
                    $return = array(
                        'success' => false,
                        'error_message' => 'Please contact ' . Settings::instance()->get('project_name') . ' to discuss setting up a new account or complete your booking with credit card.',
                    );

                    echo json_encode($return);
                    return json_encode($return);
                } else {
                  if(Settings::instance()->get('remoteaccounting_api') == Model_Accountsiq::API_NAME && Model_Plugin::is_enabled_for_role('Administrator', 'remoteaccounting')) {
                    /*if (@$post['aiq_customer_code'] == '') {
                        $return = array(
                            'success' => false,
                            'error_message' => __('Account reference code is required. Please review the information below or contact us to complete your booking.')
                        );

                        echo json_encode($return);
                        return json_encode($return);
                    }*/
                    /*if ($post['aiq_billing_email'] == '') {
                        $return = array(
                            'success' => false,
                            'error_message' => __('Billing email is required. Please review the information below or contact us to complete your booking.')
                        );

                        echo json_encode($return);
                        return json_encode($return);
                    }*/
                    if (@$post['aiq_customer_code'] != '') {
                        $aiq = new Model_Accountsiq();
                        $aiq_customer = $aiq->get_contact($post['aiq_customer_code']);

                        if ($aiq_customer == null) {
                            $return = array(
                                'success' => false,
                                'error_message' => __('Unable to verify your account reference code. Please review the information below or contact us to complete your booking.'),
                            );

                            echo json_encode($return);
                            return json_encode($return);
                        }
                        if ($aiq_customer->Email != $post['aiq_billing_email']) {
                            //check primary biller if organization email does not match
                            $biller = Model_Contacts3::search(array('email' => $post['aiq_billing_email']));
                            if (count($biller) == 0) {
                                $return = array(
                                    'success' => false,
                                    'error_message' => __('Unable to verify your billing email address. Please review the information below or contact us to complete your booking.'),
                                );

                                echo json_encode($return);
                                return json_encode($return);
                            }
                        }
                    }

                      if (@$post['aiq_billing_email']) {
                          $payer_contact = Model_Contacts3::search(array('email' => $post['aiq_billing_email']));
                          if ($payer_contact) {
                              if (Settings::instance()->get('bookings_require_primary_biller_organisation_booking') == 1) {
                                  $organisation = Model_Organisation::get_organization_by_primary_biller_id($payer_contact[0]['id']);
                                  if (!$organisation) {
                                      $return = array(
                                          'success' => false,
                                          'error_message' => 'Please contact ' . Settings::instance()->get('project_name') . ' to discuss setting up a new account or complete your booking with credit card.',
                                      );

                                      echo json_encode($return);
                                      return json_encode($return);
                                  }
                              }
                              $data['bill_payer'] = $payer_contact[0]['id'];
                          } else {
                              $return = array(
                                  'success' => false,
                                  'error_message' => 'Please contact ' . Settings::instance()->get('project_name') . ' to discuss setting up a new account or complete your booking with credit card.',
                              );

                              echo json_encode($return);
                              return json_encode($return);
                          }
                      }
                }
              }
            }
            //header('content-type: text/plain');print_r($post);exit;
            if (@$post['mobile'] == '' && @$post['mobile_code'] != '' && @$post['mobile_number'] != '') {
                @$post['mobile'] = $post['mobile_code'] . $post['mobile_number'];
            }

            $cb = new Controller_FrontEnd_Bookings(Request::$current, new Response());
            $cart = $cb->get_session_cart_data();
            $discounts = array();


            // If the booker has not registered yet
            $user = Auth::instance()->get_user();
            if (!$user['id'] && isset($post['email'])) {
                if (isset($post['guardian_id']) && isset($post['student_id'])) {
                    $new_contact = new Model_Contacts3($post['guardian_id']);
                    $this->contact = $new_contact;
                } else {
                    $user_model = new Model_Users();
                    $role_model = new Model_Roles();

                    $new_user = $user_model->register_user([
                        'name' => $post['first_name'],
                        'surname' => $post['last_name'],
                        'email' => $post['email'],
                        'role_id' => $role_model->get_id_for_role('Guardian'),
                        'mpassword' => '!',
                        'password' => '!',
                        'send_verification_email' => true
                    ]);

                    $new_contact = new Model_Contacts3();
                    $new_contact->set_first_name($post['first_name']);
                    $new_contact->set_last_name($post['last_name']);
                    $new_contact->set_subtype_id(0);
                    $new_contact->save();
                    $new_contact->insert_notification([
                        'contact_id' => $new_contact->get_id(),
                        'notification_id' => 1,
                        'value' => $post['email']
                    ]);

                    if (!empty($post['mobile_number'])) {
                        $new_contact->insert_notification([
                            'contact_id' => $new_contact->get_id(),
                            'notification_id' => 2,
                            'country_dial_code' => is_array($post['mobile_international_code']) ? reset(@$post['mobile_international_code']) : @$post['mobile_international_code'],
                            'dial_code' => trim(is_array($post['mobile_code']) ? reset(@$post['mobile_code']): @$post['mobile_code']),
                            'value' => trim( is_array($post['mobile_number']) ? reset(@$post['mobile_number']) : $post['mobile_number'])
                        ]);
                    }

                    $new_contact->set_permissions([$new_user->id]);
                    $new_contact->set_linked_user_id($new_user->id);

                    $this->contact = $new_contact;
                }
            }

            if (!empty($user['id']) && isset($post['mobile_number'])) {
                $linked_contact = Auth::instance()->get_contact();
                $model = new Model_Contacts3;

                $model->insert_notification([
                    'contact_id'      => $linked_contact->id,
                    'notification_id' => 2,
                    'country_dial_code' => trim(is_array($post['mobile_international_code']) ? reset($post['mobile_international_code']) : $post['mobile_international_code']),
                    'dial_code' => trim(is_array($post['mobile_code']) ? reset($post['mobile_code']) :$post['mobile_code']),
                    'value' => trim( is_array($post['mobile_number']) ? reset($post['mobile_number']) : $post['mobile_number'])
                ]);
            }

            foreach ($cart as $booking_item) {
                foreach ($booking_item['discounts'] as $booking_discount) {
                    $data['discounts'][$booking_item['id']][] = $booking_discount;
                }
            }

            $new_student = null;
            $delegate_ids = array();
            // Student ID field is empty => create new student
            if (!is_numeric(@$post['student_id'])) {
                // If there is no "student details" section, use details from the regular "contact details" section
                $student_fields = ['first_name', 'last_name', 'email'];
                foreach ($student_fields as $field) {
                    $post['student_'.$field] = isset($post['student_'.$field]) ? $post['student_'.$field] : (isset($post[$field]) ? $post[$field] : '');
                }

                $student_user = new Model_users();
                $roles = new Model_Roles();
                
                if (@$cart[0]['details']['is_group_booking']) {
                    // save this information so we can send them emails after booking is complete.
                    $org_rep_details = $this->contact;
                    $delegate_details = array();
                    // we assume since the user is booking delegates they are linked to an organisation. Retrieve it
                    foreach ($this->contact->get_contact_relations() as $contact_relation) {
                        if ($contact_relation['position'] == 'organisation') {
                            $organisation = new Model_Contacts3($contact_relation['parent_id']);
                            break;
                        }
                    }
                    $contact_relations = (isset($organisation)) ? array(
                        [
                            'parent_id' => $organisation->get_id(),
                            'position' => 'organisation'
                        ]
                    ) : array();
                    for ($i = 0; $i < count($post['student_first_name']); $i++) {
                        $user_search = Model_Users::search(array('email' => $post['student_email'][$i]));
                        // register delegates in the checkout as users and contacts if their email does not exist
                        if (count($user_search) === 0){
                            $user_inserted = $student_user->register_user(
                                array(
                                    'name' => $post['student_first_name'][$i],
                                    'surname' => $post['student_last_name'][$i],
                                    'email' => $post['student_email'][$i],
                                    'role_id' => $roles->get_id_for_role('Student'),
                                    'mpassword' => '!',
                                    'password' => '!',
                                    'send_verification_email' => true,
                                    'country_dial_code_mobile' => @$post['student_mobile_international_code'][$i],
                                    'dial_code_mobile' => trim(@$post['student_mobile_code'][$i]),
                                    'mobile' => trim(@$post['student_mobile_number'][$i])
                                )
                            );
    
                            $new_student = new Model_Contacts3();
                            $new_student->set_first_name($post['student_first_name'][$i]);
                            $new_student->set_last_name($post['student_last_name'][$i]);
                            $new_student->insert_notification(array(
                                'contact_id' => 0,
                                'notification_id' => 1,
                                'value' => $post['student_email'][$i]
                            ));
                            $new_student->insert_notification(array(
                                'contact_id' => 0,
                                'notification_id' => 2,
                                'country_dial_code' => @$post['student_mobile_international_code'][$i],
                                'dial_code' => trim(@$post['student_mobile_code'][$i]),
                                'value' => trim(@$post['student_mobile_number'][$i])
                            ));
                            $new_family = new Model_Family();
                            $new_family->set_family_name($post['student_last_name'][$i]);
                            if ($create_family) {
                                $new_family->save();
                                $new_student->set_family_id($new_family->get_id());
                            }
                            $student_role = ORM::factory('Contacts3_Role')->where('stub', '=', 'student')->find_undeleted();
                            $new_student->add_role($student_role->id);
                            $new_student->set_subtype_id(0);
                            $new_student->set_type(Model_Contacts3::find_type('Student')['contact_type_id']);
                            if (($bookings_interview_login_require || !$interview) && isset($user_inserted)) {
                                $new_student->set_permissions(array($user_inserted['id']));
                                $new_student->set_linked_user_id($user_inserted['id']);
                            }
                            if (Settings::instance()->get('course_new_student_is_flexi') == 1) {
                                $new_student->set_is_flexi_student(1);
                            }
                            $new_student->set_preferences(array(17));
                            if (isset($organisation)) {
                                $new_student->set_contact_relations($contact_relations);
                            }
                            $new_student->save();
                            $delegate_details[] = $new_student;
                            $delegate_ids[] = $new_student->get_id();
                            $post['student_id'] = $new_student->get_id();

                            if (!$this->contact) {
                                $this->contact = $new_student;
                            }

                            if (isset($new_contact) && $create_family) {
                                $new_contact->set_family_id($new_family->get_id());
                            }
                        } else {
                            $user = current($user_search);
                            $linked_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
                            $user_object = new Model_Users();
                            $user_object->update_user_data($user['id'],[
                                'mobile' => $data['mobile_number'],
                                'country_dial_code_mobile' => $data['mobile_international_code'],
                                'dial_code_mobile' => $data['mobile_code']
                            ]);
                            if ($linked_contact) {
                                $delegate_details[] = new Model_Contacts3($linked_contact['id']);
                                $delegate_ids[] = $linked_contact['id'];
                            }
                        }

                    }
                } else if (empty($post['student_id'])) { //create new student contact
                    if ($bookings_interview_login_require || !$interview) {
                        $user_inserted = $student_user->register_user(
                            array(
                                'name' => $post['student_first_name'],
                                'surname' => $post['student_last_name'],
                                'email' => $post['student_email'],
                                'role_id' => $roles->get_id_for_role('Student'),
                                'mpassword' => '!',
                                'password' => '!',
                                'send_verification_email' => true
                            )
                        );
        
                        $new_student = new Model_Contacts3();
                        $new_student->set_first_name($post['student_first_name']);
                        $new_student->set_last_name($post['student_last_name']);

                        if ($this->contact) {
                            $new_student->set_family_id($this->contact->get_family_id());
                        } else {
                            if ($create_family) {
                                $new_family = new Model_Family();
                                $new_family->set_family_name($post['student_last_name']);
                                $new_family->save();
                                $new_student->set_family_id($new_family->get_id());
                            }
                        }
                        $new_student->add_role(2);
                        $new_student->set_subtype_id(0);
                        $new_student->set_type(Model_Contacts3::find_type('Student')['contact_type_id']);
                        if ($bookings_interview_login_require || !$interview) {
                            $new_student->set_permissions(array($user_inserted['id']));
                            $new_student->set_linked_user_id($user_inserted['id']);
                        }
                        if (Settings::instance()->get('course_new_student_is_flexi') == 1) {
                            $new_student->set_is_flexi_student(1);
                        }
                        $new_student->set_preferences(array(17));
        
                        $new_student->save();
                        if ($post['student_email']) {
                            $new_student->insert_notification(array(
                                'contact_id' => 0,
                                'notification_id' => 1,
                                'value' => $post['student_email']
                            ));
                        }
                        if (@$post['student_mobile_number']) {
                            $new_student->insert_notification(array(
                                'contact_id' => 0,
                                'notification_id' => 2,
                                'country_dial_code' => @$post['student_mobile_international_code'],
                                'dial_code' => trim(@$post['student_mobile_code']),
                                'value' =>  $post['student_mobile_number']
                            ));
                        }

                        $post['student_id'] = $new_student->get_id();

                        if (!$this->contact || !$this->contact->get_id()) {
                            $this->contact = $new_student;
                        }

                        $student = $new_student;
                    }
                }
            } else {
                $student = new Model_Contacts3($post['student_id']);
                if (isset($post['student_year_id'])) {
                    $student->set_year_id($post['student_year_id']);
                }
                if (is_array(@$post['student'])) {
                    $student->set_date_of_birth($post['student']['dob']);
                    $student->set_gender($post['student']['gender']);
                    $student->set_nationality($post['student']['nationality_id']);
                    $student->set_pps_number(isset($post['student']['pps']) ? $post['student']['pps'] : '');

                    if (isset($post['student']['preferences_medical'])) {
                        $student->set_preferences(array_merge($student->get_preferences_ids(),
                            $post['student']['preferences_medical']));
                    }
                    $student->address->load($post['student']);
                }
                $student->insert_notification(array(
                    'contact_id' => 0,
                    'notification_id' => 1,
                    'value' => $post['student_email']
                ));
                if (@$post['student_mobile_number']) {
                    $student->insert_notification(array(
                        'contact_id' => 0,
                        'notification_id' => 2,
                        'country_dial_code' => @$post['student_mobile_international_code'],
                        'dial_code' => trim(@$post['student_mobile_code']),
                        'value' =>  $post['student_mobile_number']
                    ));
                }
                $student->save();

                if (!$this->contact || !$this->contact->get_id()) {
                    $this->contact = $new_student;
                }
            }

                if ($this->contact) {
                    if (!$this->contact->has_role('student')) {
                        if (isset($post['mobile_number'])) {
                            $this->contact->insert_notification(array(
                                'notification_id' => 2,
                                'country_dial_code' => trim(is_array($post['mobile_international_code']) ? reset($post['mobile_international_code']) : $post['mobile_international_code']),
                                'dial_code' => trim(is_array($post['mobile_code']) ? reset($post['mobile_code']) :$post['mobile_code']),
                                'value' => trim( is_array($post['mobile_number']) ? reset($post['mobile_number']) : $post['mobile_number'])
                            ));
                        } else {
                            $this->contact->insert_notification(array(
                                'notification_id' => 2, 'value' => $post['mobile']));
                        }

                    }
                }
    
                if (!is_numeric(@$post['guardian_id']) && ($this->contact == null || $this->contact->has_role('student') ||
                        (isset($this->contact)) && method_exists($this->contact, 'get_family') && count($this->contact->get_family()->get_guardians()) == 0)) {
                    //icse checkout do not have guardian. only student.
                    if (@$post['first_name'] != '' && @$post['last_name'] != '' && @$post['email'] != '' && count(Model_Users::search(array('email' => $post['email']))) === 0) {
                        $guardian_user = new Model_users();
                        $roles = new Model_Roles();
                        if ($bookings_interview_login_require || !$interview) {
                            $guardian_user_array =       array(
                                'name' => $post['first_name'],
                                'surname' => $post['last_name'],
                                'email' => $post['email'],
                                'role_id' => $roles->get_id_for_role('Parent/Guardian'),
                                'mpassword' => '!',
                                'password' => '!',
                                'send_verification_email' => true
                            );
                            if (isset($post['mobile_number'])) {
                                $guardian_user_array['mobile'] = $data['mobile_number'];
                                $guardian_user_array['country_dial_code_mobile'] = $data['mobile_international_code'];
                                $guardian_user_array['dial_code_mobile'] = $data['mobile_code'];
                            } else{
                                $guardian_user_array['mobile'] = $post['mobile'];

                            }
                            $user_inserted = $guardian_user->register_user($guardian_user_array);
                        }


                        $new_guardian = new Model_Contacts3();
                        $new_guardian->set_preferences(
                            array(
                                'preference_id' => Model_Preferences::get_stub_id('bookings'),
                                'notification_type' => 'email',
                                'value' => 1
                            )
                        );
                        $new_guardian->set_first_name($post['first_name']);
                        $new_guardian->set_last_name($post['last_name']);
                        if ($this->contact) {
                            $new_guardian->set_family_id($this->contact->get_family_id());
                        } else {
                            if ($new_student) {
                                $new_guardian->set_family_id($new_student->get_family_id());
                            }
                        }
                        $new_guardian->add_role(1);
                        $new_guardian->set_subtype_id(0);
                        $new_guardian->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                        if ($bookings_interview_login_require || !$interview) {
                            $new_guardian->set_permissions(array($user_inserted['id']));
                            $new_guardian->set_linked_user_id($user_inserted['id']);
                        }
                        $new_guardian->set_is_primary(1);
                        $new_guardian->save();

                        $family = new Model_Family($new_guardian->get_family_id());
                        $family->set_primary_contact_id($new_guardian->get_id());
                        if ($create_family) {
                            $family->save();
                        }

                        $new_guardian->insert_notification(array(
                            'contact_id' => 0,
                            'notification_id' => 1,
                            'value' => $post['email']
                        ));

                        if (isset($post['mobile_number'])) {
                            $new_guardian->insert_notification(array(
                                'notification_id' => 2,
                                'country_dial_code' => trim(is_array($post['mobile_international_code']) ? reset($post['mobile_international_code']) : $post['mobile_international_code']),
                                'dial_code' => trim(is_array($post['mobile_code']) ? reset($post['mobile_code']) :$post['mobile_code']),
                                'value' => trim( is_array($post['mobile_number']) ? reset($post['mobile_number']) : $post['mobile_number'])
                            ));
                        } else {
                            $new_guardian->insert_notification(array(
                                'notification_id' => 2, 'value' => $post['mobile']));
                        }

                        $new_guardian->save();
                        $post['guardian_id'] = $new_guardian->get_id();
                    }
                }

            if (!empty($delegate_ids) && count($delegate_ids) > 1) { // link booking to guardian if multiple delegate booking
                $data['contact_id'] = $post['guardian_id'];
            }

            /** Book and pay **/
            // awkwardly placed to avoid a conflict
            if (!empty($new_guardian)) {
                if ($this->contact == null) {
                    $this->contact = $new_guardian;
                }
            }

            // If no student ID is specified, assume the booking is for the logged-in user
            // If the logged-in user is a student, they can only book for themself
            if (empty($post['student_id']) || ($this->contact != null && ($this->contact->has_role('student') || $cart[0]['details']['is_group_booking']))) {
                $data['contact_id'] = $this->contact->get_id();
            } else {
                $data['contact_id'] = $post['student_id'];
            }

            /*
             * check guardian auth code
             * */
            if ($this->contact)
            if ($this->contact->has_role('student') && Settings::instance()->get('bookings_student_auth_enabled') == 1) {
                $guardian_auth = Model_KES_Bookings::check_student_booking_auth($data['contact_id'], $post['guardian_auth_id'], $post['guardian_auth_code']);
                if (!$guardian_auth) {
                    $return = array(
                        'success' => false,
                        'error_message' => __('Guardian authorization failed.'),
                    );

                    echo json_encode($return);
                    return;
                }
            }

            $data['booking_items'] = isset($post['booking_items']) ? $post['booking_items'] : array();
            $data['schedule_ids']  = isset($data['schedule_ids']) ? $data['schedule_ids'] : array_keys($data['booking_items']);
            if (@$data['confirmation'] == 'subscription') {
                if (@$post['saved_card_id'] && $post['cc_new'] == 0) {
                    $card_id = $post['saved_card_id'];
                } else {
                    $card_id = Model_Payments::card_save(
                        $this->contact ? $this->contact->get_id() : ($data['cc_store_guardian'] == 'YES' && $post['guardian_id'] ? $post['guardian_id'] : $student->get_id()),
                        $data['booking_id'],
                        $data['ccType'],
                        $data['ccNum'],
                        $data['ccExpMM'] . $data['ccExpYY'],
                        $data['ccName']
                    );
                }
                if (!$card_id) {
                    $return = array(
                        'success' => false,
                        'error_message' => __('Unable to save card.'),
                    );

                    echo json_encode($return);
                    return;
                } else {
                    Model_KES_Bookings::save_card($data['booking_id'], $card_id, 1);
                    $return = array(
                        'success'    => true,
                        'booking_id' => $data['booking_id'],
                        'redirect'   => '/thankyou?id=' . $data['booking_id']
                    );

                    $schedule_names = DB::select('name', 'title')
                        ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                                ->on('schedules.course_id', '=', 'courses.id')
                        ->where('schedules.id', 'in', $data['schedule_ids'])->execute()->as_array();
                    $message_params = array(
                        'student' => $data['student_first_name'] . ' ' . $data['student_last_name'],
                        'schedule' => array()
                    );
                    foreach ($schedule_names as $schedule_name) {
                        $message_params['schedule'][] =  $schedule_name['title'] . ' - ' . $schedule_name['name'];
                    }
                    $message_params['schedule'] = implode(', ', $message_params['schedule']);
                    $recipients = array(
                        array('target_type' => 'CMS_CONTACT3', 'target' => $data['student_id'])
                    );
                    $mm = new Model_Messaging();
                    $mm->send_template('course-subscription-confirm-completed', null, null, $recipients, $message_params);
                    echo json_encode($return);
                    return;
                }
            }

            $ac_tags = array();
            foreach ($data['booking_items'] as $schedule_id => $timeslots) {
                $schedule = DB::select('*')->from(Model_Schedules::TABLE_SCHEDULES)->where('id', '=', $schedule_id)->execute()->current();
                if ($schedule['booking_type'] != 'One Timeslot') {
                    if (Model_KES_Bookings::check_duplicate_bookings($data['student_id'], $schedule_id)) {
                        $return = array(
                            'success' => false,
                            'error_message' => __('Duplicate Booking. You already have a booking for ' . $schedule['name']),
                        );

                        echo json_encode($return);
                        return;
                    }
                }

                foreach ($timeslots as $timeslot_id => $timeslot) {
                    if ($schedule['booking_type'] == 'One Timeslot') {
                        if ($duplicate_slot = Model_KES_Bookings::check_duplicate_bookings($data['student_id'], $schedule_id, $timeslot_id)) {
                            $return = array(
                                'success' => false,
                                'error_message' => __('Duplicate Booking. You already have a booking for') . ' ' . $schedule['name'] . ' ' . date('d/m/Y H:i', strtotime($duplicate_slot['datetime_start'])),
                            );

                            echo json_encode($return);
                            return;
                        }
                    }
                    $data['booking_items'][$schedule_id][$timeslot_id]['attending'] = 1;
                    if (@$post['paymentoption'][$schedule_id]) {
                        $data['booking_items'][$schedule_id][$timeslot_id]['paymentoption_id'] = $post['paymentoption'][$schedule_id];
                    }
                }
            }

            if (@$data['courses'])
            foreach ($data['courses'] as $course_id => $course) {
                if (@$post['paymentoption'][$course_id]) {
                    $data['courses'][$course_id]['paymentoption_id'] = $post['paymentoption'][$course_id];
                }
            }

            $data['frontend_booking'] = true;

            if ($data['payment_method'] == 'sms') {
                $data['sms_booking_fee'] = (float)Settings::instance()->get('course_sms_booking_fee');
            }

            if ($data['payment_method'] == 'cc') {
                $data['cc_booking_fee'] = (float)Settings::instance()->get('course_cc_booking_fee');
            }
            $send_interview_schedule_email = true;
            if ($interview) {
                $interview_slots = Model_KES_Bookings::find_interview_slot($data['course_code']);
                if (count($interview_slots) > 0) {
                    $interview_slot = $interview_slots[0];
                    $data['booking_items'][$interview_slot['schedule_id']] = array();
                    $data['booking_items'][$interview_slot['schedule_id']][$interview_slot['id']] = array('schedule_id' => $interview_slot['schedule_id'], 'attending' => 1);
                    $data['courses'] = array(array('course_id' => $interview_slot['course_id']));
                    /*$data['application'] = array(
                        'interview_status' => 'SCHEDULED',
                    );*/
                } else {
                    $send_interview_schedule_email = false;
                    $mm = new Model_Messaging();
                    $mm->send_template('course-timeslots-full', null, null, array(), array('course' => $post['course_code']));
                    $interview_slot = Model_KES_Bookings::find_last_interview_slot($data['course_code']);
                    if ($interview_slot) {
                        $data['booking_items'][$interview_slot['schedule_id']] = array();
                        $data['booking_items'][$interview_slot['schedule_id']][$interview_slot['id']] = array(
                            'schedule_id' => $interview_slot['schedule_id'],
                            'attending' => 1
                        );
                        $data['courses'] = array(array('course_id' => $interview_slot['course_id']));
                    } else {
                        $course_id = DB::select('id')
                            ->from(Model_Courses::TABLE_COURSES)
                            ->where('code', '=', $data['course_code'])
                            ->and_where('deleted', '=', 0)
                            ->execute()
                            ->get('id');
                        $data['courses'] = array(array('course_id' => $course_id));
                    }
                }

            } else {
                if (@$post['course_id']) {
                    $data['courses'] = array(array('course_id' => $post['course_id']));
                }
            }

            $booking = new Model_KES_Bookings();
            if ($interview) {
                if ($send_interview_schedule_email) {
                    $booking->interview_status = 'Scheduled';
                } else {
                    $booking->interview_status = 'Not Scheduled';
                }
            }
            $extra_data = array();

            $custom_checkout = Settings::instance()->get('checkout_customization');
            if (in_array($custom_checkout, ['bc_language', 'sls'])) {
                $extra_data['current_school'] = @$post['current_school'];
                $extra_data['level_of_english'] = @$post['level_of_english'];
                $extra_data['heard_about_via'] = @$post['heard_about_via'];
                $extra_data['heard_about_via_other'] = @$post['heard_about_via_other'];
                $extra_data['has_dietary_requirement'] = @$post['has_dietary_requirement'];
                $extra_data['dietary_requirements'] = @$post['dietary_requirements'];
                $extra_data['has_medical_conditions'] = @$post['has_medical_conditions'];
                $extra_data['medical_conditions'] = @$post['medical_conditions'];
                $extra_data['arrival_flight_number'] = @$post['arrival_flight_number'];
                $extra_data['arrival_flight_date'] = @$post['arrival_flight_date'];
                $extra_data['arrival_flight_time'] = @$post['arrival_flight_time'];
                $extra_data['arrival_airport'] = @$post['arrival_airport'];
                $extra_data['departure_flight_number'] = @$post['departure_flight_number'];
                $extra_data['departure_flight_date'] = @$post['departure_flight_date'];
                $extra_data['departure_flight_time'] = @$post['departure_flight_time'];
                $extra_data['departure_airport'] = @$post['departure_airport'];
                $extra_data['transfer_cork_kerry'] = @$post['transfer_cork_kerry'];
                $extra_data['transfer_dublin_one_way'] = @$post['transfer_dublin_one_way'];
                $extra_data['transfer_dublin_return'] = @$post['transfer_dublin_return'];
                $extra_data['airport_transfer'] = @$post['airport_transfer'];
            }

            if (Settings::instance()->get('cart_special_requirements_enable') == 1
                && isset($post['special_requirements'])) {
                $extra_data['special_requirements'] = @$post['special_requirements'];
            }
            if (!empty($extra_data)) {
                $data['extra_data'] = $extra_data;
            }
            if (!isset($data['contact_id']) && $this->contact) {
                $data['contact_id'] = $this->contact->get_id();
            }

            if (!isset($data['how_did_you_hear'])) {
                $data['how_did_you_hear'] = 0;
            }
            
            if (in_array($custom_checkout, ['sls'])) {
                $data['application'] = $extra_data;
            }

            if (count($delegate_ids) > 0) {
                $data['delegate_ids'] = $delegate_ids;
            }
            if (@$data['payment_method'] == 'purchase_order' && @$data['purchase_order_no']) {
                $data['invoice_details'] = $data['purchase_order_no'];
            }
            if (@$data['payment_method'] == 'purchase_order') {
                $data['payment_method'] = 'invoice';
            }

            if (!empty($post['is_sales_quote'])) {
                $data['is_sales_quote'] = 1;
            }
            if (!empty($post['billing_address'])) {
                $billing_address = $post['billing_address'];
                $billing_address_id = $billing_address['address_id'] ?? '';
                $booking_address = new Model_Residence($billing_address_id);
                $booking_address->load($billing_address);
                $booking_address->save();
                $data['billing_address_id'] = $booking_address->get_address_id();
            }

            $booking->set($data);

            if ($this->contact) {
                $data['card_contact_id'] = $this->contact->get_id();
            } else {
                $data['card_contact_id'] = $data['contact_id'];
            }
            if (@$data['cc_store_guardian'] == 'YES') {
                $data['card_contact_id'] = $post['guardian_id'];
            } else {

            }
            $paid = $booking->book_and_pay($data);

            $ac_tags[] = array('tag' => 'Booking', 'description' => 'Booking');
            //$ac_tags[] = array('tag' => 'Booking' . $booking->get_booking_id(), 'description' => 'Booking Id:' . $booking->get_booking_id());
            $ac_schedules = DB::select('schedules.id', 'schedules.course_id', array('schedules.name', 'schedule'), array('courses.title', 'course'), 'courses.code', 'schedules.start_date', 'schedules.fee_amount')
                ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                    ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                        ->on('schedules.id', '=', 'has_schedules.schedule_id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('schedules.course_id', '=', 'courses.id')
                    ->where('has_schedules.booking_id', '=', $booking->get_booking_id())
                ->execute()
                ->as_array();
            foreach ($ac_schedules as $ac_schedule) {
                if ($ac_schedule['code']) {
                    $ac_tags[] = array(
                        'tag' => $ac_schedule['code'],
                        'description' => $ac_schedule['course']
                    );
                }

                /*
                if ($ac_schedule['start_date']) {
                    $ac_tags[] = array(
                        'tag' => $ac_schedule['start_date'],
                        'description' => 'Schedule Start'
                    );
                }
                if ($ac_schedule['fee_amount']) {
                    $ac_tags[] = array(
                        'tag' => $ac_schedule['fee_amount'],
                        'description' => 'Fee'
                    );
                }
                $ac_tags[] = array(
                    'tag' => 'Schedule' . $ac_schedule['id'],
                    'description' => $ac_schedule['schedule']
                );
                $ac_tags[] = array(
                    'tag' => $ac_schedule['schedule'],
                    'description' => $ac_schedule['schedule']
                );
                $ac_tags[] = array(
                    'tag' => 'Course' . $ac_schedule['course_id'],
                    'description' => $ac_schedule['course']
                );
                $ac_tags[] = array(
                    'tag' => $ac_schedule['course'],
                    'description' => $ac_schedule['course']
                );*/
            }
            if (@$data['is_sales_quote'] == 1) {
                Model_Automations::run_triggers(
                    Model_Bookings_Frontendquotecreatetrigger::NAME,
                    array(
                        'booking_id' => $booking->get_booking_id(),
                        'contact_id' => $data['contact_id'],
                        'tags' => $ac_tags,
                        'payment_method' => $data['payment_method'],
                        'aiq_customer_code' => @$post['aiq_customer_code']
                    )
                );
            } else {
                Model_Automations::run_triggers(
                    Model_Bookings_Frontendbookingcreatetrigger::NAME,
                    array(
                        'booking_id' => $booking->get_booking_id(),
                        'contact_id' => $data['contact_id'],
                        'tags' => $ac_tags,
                        'payment_method' => $data['payment_method'],
                        'aiq_customer_code' => @$post['aiq_customer_code']
                    )
                );
            }
            Model_Automations::run_triggers(
                Model_Bookings_Checkouttrigger::NAME,
                array(
                    'contact_id' => $data['contact_id'],
                    'tags' => $ac_tags,
                    'payment_method' => $data['payment_method'],
                    'aiq_customer_code' => @$post['aiq_customer_code']
                )
            );
            $transactions = Model_Kes_Transaction::get_contact_transactions(null, null, $booking->get_booking_id());
            if (count($transactions)) {
                foreach ($transactions as $transaction) {
                    Model_Automations::run_triggers(
                        Model_Bookings_Checkouttrigger::NAME,
                        array(
                            'transaction_id' => $transaction['id'],
                            'tags' => $ac_tags,
                            'payment_method' => $data['payment_method'],
                            'aiq_customer_code' => @$post['aiq_customer_code']
                        )
                    );
                }
            }

            if ($interview) {
                $application = new Model_Booking_Application();
                $application->booking_id = $booking->get_booking_id();
                $application->save_with_history([
                    'application_status' => 'Enquiry',
                    'interview_status' => 'Pending',
                    'offer_status' => 'Pending',
                    'registration_status' => 'Pending'
                ]);
                if ($booking->interview_status == 'Not Scheduled') {
                    DB::update(Model_KES_Bookings::BOOKING_ITEMS_TABLE)
                        ->set(array('delete' =>  1))
                        ->where('booking_id', '=', $booking->get_booking_id())
                        ->execute();
                }
                self::send_interview_emails($post);
                if ($send_interview_schedule_email) {
                    Model_KES_Bookings::send_interview_schedule_email($booking->get_booking_id());
                }
            } else if ((count($data['courses']) > 0 || !empty($data['booking_items'])) && $schedule->study_mode->study_mode === "Full Time") {
                // Only full time schedules can be sent application emails
                $applicant_id = !empty($data['student_id']) ? $data['student_id'] : (isset($data['contact_id']) ? $data['contact_id'] : null);

                if (count($data['courses']) > 0) {
                    $course_ids = array_column($data['courses'], 'course_id');
                } else {
                    $schedule_ids = array_unique(array_keys($data['booking_items']));
                    $course_ids = [];
                    foreach ($schedule_ids as $schedule_id) {
                        $schedule = ORM::factory('Course_Schedule')->where('id', '=', $schedule_id)->find_published();
                        $course_ids[] = $schedule->course_id;
                    }
                }

                self::send_application_emails($applicant_id, $course_ids);
            } else if($cart[0]['details']['is_group_booking'] && $organisation) {
                $data['schedule_info'] = $schedule ?? null;
                $data['organisation'] = $organisation;
                self::send_org_emails($data, $org_rep_details, $delegate_details);
            }


            /** Update contact preferences **/
            if ( ! empty($data['preferences']))
            {
                $preferences = $this->contact->get_preferences();
                foreach ($data['preferences'] as $preference_id) {
                    $preferences[] = array('preference_id' => $preference_id);
                }
                $this->contact->set_preferences($preferences)->save();
            }


            /** Return data **/
            if (@$paid['payment']['status'] == 'error')
            {
                $message  = isset($paid['payment']['message']) ? $paid['payment']['message'] : '';
                $message .= isset($paid['payment']['error'])   ? $paid['payment']['error']   : '';

                $return = array(
                    'success'       => false,
                    'error_message' => $message,
                    'student_id' => (@$student ? $student->get_id() : null),
                    'guardian_id' => (@$new_guardian ? $new_guardian->get_id() : null)
                );
            }
            else
            {
                $cart = array(
                    'booking' => array(),
                    'booking_id' => null,
                    'client_id' => null,
                    'discounts' => array(),
                    'courses' => array()
                );
                Session::instance()->set('ibcart', $cart);

                if ($guardian_auth) {
                    Model_KES_Bookings::set_student_booking_auth_validated($guardian_auth['id']);
                }

                $return = array(
                    'success'          => true,
                    'booking_id'       => $booking->get_booking_id(),
                    'google_analytics' => $booking->get_google_analytics(),
                    'redirect'         => $interview ? Settings::instance()->get('bookings_interview_redirect_url') : '/thankyou?id='.$paid['booking']->get_booking_id()
                );
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
            throw $e;
            /*
            $return = array(
                'success' => false,
                'error_message' => __(
                    'Unexpected internal error. If this problem persists, please $1. This error has been logged.',
                    array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')
                )
            );*/
        }

        echo json_encode($return);
    }


    /**
     * Used to display contact details under the list of contacts, when a family member is clicked
     */
    public function action_ajax_display_contact_details()
    {
        $this->auto_render = FALSE;
        $contact_id = $this->request->post('contact_id');

        if(!$contact_id){
            return false;
        }

        $user = Auth::instance()->get_user();
        $logged_in_contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $logged_in_contact = new Model_Contacts3($logged_in_contacts[0]['id']);
        $contact = new Model_Contacts3($contact_id);
        $contact_roles = $contact_roles = $contact3->get_roles_stubs(true);
        $family_id = $contact->get_family_id();
        $family = new Model_Family($family_id);

        $can_edit = 0;
        if($logged_in_contact->get_is_primary()){
            $can_edit = 1;
        }

        if($logged_in_contact->get_id() == $contact->get_id() && (in_array('guardian', $logged_in_contact->get_roles_stubs()) || in_array('mature', $logged_in_contact->get_roles_stubs()))){
            $can_edit = 1;
        }

        $view = array(
            'contact'                           => $contact,
            'contact_role'                      => $contact_role,
            'user'                              => $user,
            'account_user'                      => Model_Contacts3::get_user_by_contact_id($contact->get_id()),
            'logged_in_contact'                 => $logged_in_contact,
            'family'                            => $family,
            'preferences'                       => Model_Preferences::get_all_preferences_grouped(),
            'contact_preference_ids'            => $contact->get_preferences_ids(),
            'subject_preferences_ids'           => $contact->get_subject_preferences_ids(),
            'course_types_preferences_ids'      => $contact->get_course_types_preferences_ids(),
            'salutations'                       => Model_Contacts3::temporary_salutation_dropdown(),
            'academic_years'                    => Model_AcademicYear::get_academic_years_options(TRUE),
            'schools'                           => Model_Providers::get_all_schools(),
            'years'                             => Model_Years::get_years_where(array(array('publish', '=', 1))),
            'subjects'                          => Model_Subjects::get_all_subjects(),
            'course_types'                      => Model_Categories::get_categories_without_parent(),
            'counties'                          => Model_Residence::get_all_counties(),
            'roles_stubs'                       => $contact->get_roles_stubs(),
            'assets_folder_path'                => Kohana::$config->load('config')->assets_folder_path,
            'privileges_preferences'            => Model_Preferences::get_family_preferences(),
            'contact_privileges_preferences'    => Model_Contacts3::get_contact_privileges_preferences($contact_id),
            'can_edit'                          => $can_edit
        );
        $this->response->body(View::factory('frontend/snippets/primary_profile', $view));
    }

    private function saveEmail($contact, $email){
        $existingEmails = $contact->get_contact_notifications('email');
        if ($existingEmails) {
            $existingEmails[0]['value'] = trim($email);
            $contact->update_notification($existingEmails[0]);
        } else {
            $contact->insert_notification(
                array(
                    'value' => trim($email),
                    'notification_id' => 1
                )
            );
        }
    }

    public function action_ajax_save_profile()
    {
        $this->auto_render = FALSE;
        $post = $this->request->post();
        $errors = array();

        if (empty($post['contact_id'])) {
            return false;
        }

        $contact = new Model_Contacts3((int)$post['contact_id']);

        if (!$contact) {
            return false;
        }

        if (trim($post['mobile']) != '') {
            $this->saveMobileNumber($contact, $post['mobile']);
        }

        if (!empty($post['email'])) {
            $this->saveEmail($contact, $post['email']);
            $contactUser = Model_Contacts3::get_user_by_contact_id($contact->get_id());
            $logged_in_user = Auth::instance()->get_user();

            $data = array();
            $data['email']   = $post['email'];
            $data['name']    = $post['first_name'];
            $data['surname'] = $post['last_name'];

            // If the user owns the account and has specified a password, they can set their own password
            $setting_password_now = ($logged_in_user['email'] == $post['email'] && ! empty($post['password']) && ! empty($post['mpassword']));

            if ($setting_password_now) {
                $data['password']  = $post['password'];
                $data['mpassword'] = $post['mpassword'];
            }

            $roles = new Model_Roles();
            if (@$post['mature'] == 1) {
                $data['role_id'] = $roles->get_id_for_role('Mature Student');
            } else if (@$post['role'] == 'guardian'){
                $data['role_id'] = $roles->get_id_for_role('Parent/Guardian');
            } else {
                $data['role_id'] = $roles->get_id_for_role('Student');
            }

            try {
                $users = new Model_Users();
                Database::instance()->begin();
                if(!$contactUser){
                    // If the user does not own the email, a random placeholder password wil be created
                    // The email holder will later be emailed instructions on how to change it
                    if (!$setting_password_now) {
                        $data['mpassword'] = $data['password'] = substr(base64_encode(openssl_random_pseudo_bytes(20)), 0, 10);
                        $data['send_verification_email'] = false;
                    }

                    $result = $users->register_user($data);

                    if ($result['success']) {
                        Database::instance()->commit();
                        $contact->set_permissions(array($result['id']));
                        $contact->save();
                        DB::update('engine_users')->set(array('default_home_page' => '/admin'))->where('id', '=', $result['id'])->execute();

                        if (!$setting_password_now) {
                            // Send an email, asking the user to set their own password
                            $validation      = Model_Users::set_user_password_validation($data['email']);
                            $messaging_model = new Model_Messaging();
                            $targets         = array(
                                array('target_type' => 'CMS_USER', 'target' => $result['id'], 'x_details' => 'to')
                            );
                            $parameters      = array(
                                'base_url'        => trim(URL::base(), '/'),
                                'email'           => $data['email'],
                                'first_name'      => $data['name'],
                                'last_name'       => $data['surname'],
                                'validation_code' => $validation['validation'],
                                'primary_name' => $this->user['name'] . ' ' . $this->user['surname']
                            );
                            $messaging_model->send_template('new_user_no_password', '', null, $targets, $parameters);
                        }

                        IbHelpers::set_message('Profile has been created', 'success popup_box');

                    } else if (!empty($result['error'])) {
                        $errors[] = $result['error'];
                        Database::instance()->rollback();
                    }
                } else {
                    $data['password'] = $data['mpassword'] = '';
                    if ($logged_in_user['email'] == $post['email']) {
                        $data['password'] = isset($data['password']) ? $data['password'] : '';
                        $data['mpassword'] = isset($data['mpassword']) ? $data['mpassword'] : '';
                    }

                    $result = $users->update_user_data($contactUser['id'], $data);

                    if($result){
                        // If the user was successfully saved, delete the success message.
                        // It is considered redundant to the "profile updated" message set below.
                        $session = Session::instance();
                        $session->delete('messages');

                        Database::instance()->commit();
                        $currentUser = Auth::instance()->get_user();
                        if ($contactUser['id'] == $currentUser['id']) {
                            //Auth::instance()->logout(TRUE, TRUE);
                        }
                    }else{
                        Database::instance()->rollback();
                    }
                }
            } catch (Exception $exc) {
                Database::instance()->rollback();
                Log::instance()->add(Log::ERROR, $exc->getMessage().$exc->getTraceAsString());
                throw $exc;
            }
        }

        $post['subtype_id'] = $contact->get_subtype_id() ? $contact->get_subtype_id() : 1;
        $post['staff_member'] = 0;

        if($contact->get_is_primary()){
            $family = new Model_Family($contact->get_family_id());
            $family->address->load($post);

            // If a user registered before supplying a last name, "last_name" or their email address...
            // ... could have been used  as a placeholder family name.
            $family_name_is_placeholder = ($contact->get_family_id() == null || $family->get_family_name() == 'last_name' || filter_var($family->get_family_name(), FILTER_VALIDATE_EMAIL));

            // If the family name is still using a placeholder at this point, replace it with the surname of the...
            // ... contact being edited, if they are a primary contact and have provided a surname.
            if ($family_name_is_placeholder && $contact->get_is_primary() && trim($post['last_name'])) {
                $family->set_family_name($post['last_name']);
            }

            $family->save();
        }

        if (@$post['mature'] == 1) {
            $post['roles'][] = 3;
        } else if (@$post['role'] == 'guardian'){
            $post['roles'][] = 1;
        } else {
            $post['roles'][] = 2;
        }


        $contact->load($post);
        $contact->address->load($post);

        if(!empty($post['student']) && !in_array('student', $contact->get_roles_stubs())){
            $contact->add_role_by_stub('student');
        }

        $contact->save();
        IbHelpers::set_message('Profile has been updated', 'success popup_box');

        if(!empty($post['contact_preference']))
        {
            $stubs=array();
            foreach ($post['contact_preference'] as $key => $value)
            {
                array_push($stubs,$key);
            }
            Model_Preferences::save_contact_privilege($stubs,(int)$post['contact_id']);

        }else
        {
            Model_Preferences::save_contact_privilege(null,(int)$post['contact_id']);
        }

        $return = array(
            'contact_id' => $contact->get_id(),
            'errors'     => $errors,
            'messages'   => IbHelpers::get_messages()
        );

        $this->response->body(json_encode($return));
    }

    public function action_save_new_profile(){
        $this->auto_render = FALSE;
        $post = $this->request->post();
        $errors = array();

        if(empty($post['family_id']) || empty($post['role'])){
            return false;
        }

        try {
            Database::instance()->begin();
            $error = false;
            $contact_data = $post;
            $contact_data['family_id'] = $post['family_id'];
            if (@$post['mature'] == 1) {
                $contact_data['roles'][] = 3;
            } else {
                $contact_data['roles'][] = $post['role'] == 'guardian' ? 1 : 2; //Add guardian or child role
            }
            $contact_data['type'] = 1;
            $contact_data['subtype_id'] = 1;

            $contact = new Model_Contacts3();
            $contact->load($contact_data);
            $contact->address->load($contact_data);

            if (trim($post['mobile']) != '') {
                $this->saveMobileNumber($contact, $post['mobile']);
            }
            
            //Add child role if "I am student" checked
            if(!empty($post['mature']) && !in_array('mature', $contact->get_roles_stubs())){
                $contact->add_role_by_stub('mature');
            }
            $contact->save();

            if(!empty($post['email']) && !empty($post['role'])) {
                $this->saveEmail($contact, $post['email']);
                $logged_in_user = Auth::instance()->get_user();

                // If the user owns the account and has specified a password, they can set their own password
                // If not, we will email the account holder steps to set a password
                $setting_password_now = ($logged_in_user['email'] == $post['email'] && ! empty($post['password']) && ! empty($post['mpassword']));

                $data = array();
                $data['email']   = $post['email'];
                $data['name']    = $this->request->post('first_name');
                $data['surname'] = $this->request->post('last_name');

                // If the password is being set now
                if ($setting_password_now) {
                    $data['password'] = $post['password'];
                    $data['mpassword'] = $post['mpassword'];
                }
                // Otherwise, generate a random placeholder password. We'll later email the user a link to set their own password
                else {
                    // Generate a random 10-character password
                    $data['mpassword'] = $data['password'] = substr(base64_encode(openssl_random_pseudo_bytes(20)), 0, 10);
                }

                $roles = new Model_Roles();
                $users = new Model_Users();
                if (@$post['mature'] == 1) {
                    $data['role_id'] = $roles->get_id_for_role('Mature Student');
                } else if ($post['role'] == 'guardian'){
                    $data['role_id'] = $roles->get_id_for_role('Parent/Guardian');
                } else {
                    $data['role_id'] = $roles->get_id_for_role('Student');
                }

                // If we are emailing the user a new password, they don't need both a "set password" and "verify account" email
                // Allowing a password to be set will be understood as a verification.
                $data['send_verification_email'] = $setting_password_now;

                $result = $users->register_user($data);

                if ($result['success']) {
                    $contact->set_permissions(array($result['id']));
                    $contact->save();
                    DB::update('engine_users')->set(array('default_home_page' => '/admin'))->where('id', '=', $result['id'])->execute();

                    if (!$setting_password_now) {
                        // Send an email, asking the user to set their own password
                        $validation      = Model_Users::set_user_password_validation($data['email']);
                        $messaging_model = new Model_Messaging();
                        $targets         = array(
                            array('target_type' => 'CMS_USER', 'target' => $result['id'], 'x_details' => 'to')
                        );
                        $parameters      = array(
                            'base_url'        => trim(URL::base(), '/'),
                            'email'           => $data['email'],
                            'first_name'      => $data['name'],
                            'last_name'       => $data['surname'],
                            'validation_code' => $validation['validation'],
                            'primary_name' => $this->user['name'] . ' ' . $this->user['surname']
                        );
                        $messaging_model->send_template('new_user_no_password', '', null, $targets, $parameters);
                    }
                } else if (!empty($result['error'])) {
                    $error = true;
                    $errors[] = $result['error'];
                }
            }

            if (!$error) {

                if(!empty($post['contact_preference']))
                {
                    $stubs=array();
                    foreach ($post['contact_preference'] as $key => $value)
                    {
                        array_push($stubs,$key);
                    }
                    Model_Preferences::save_contact_privilege($stubs,$contact->get_id());
                }
                Database::instance()->commit();

                IbHelpers::set_message('Profile has been created', 'success popup_box');

                if ($post['redirect']) {
                    $this->request->redirect($post['redirect']);
                } else {
                    return $this->request->redirect('/admin/contacts3/profile?contact_id=' . $contact->get_id());
                }
            } else {
                $error_message = (isset($result) && isset($result['error'])) ? $result['error'] : '';
                IBHelpers::set_message('Error creating profile. '.$error_message, 'danger popup_box');

                Database::instance()->commit();
                return $this->request->redirect('/admin/contacts3/profile?contact_id=' . $contact->get_id() . '&error=duplicate_login_email');
            }
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

        //return $this->request->redirect('/frontend/contacts3');
    }

    public function action_ajax_new_profile()
    {
        $this->auto_render = FALSE;
        $view = array(
            'salutations'               => Model_Contacts3::temporary_salutation_dropdown(),
            'preferences'               => Model_Preferences::get_all_preferences_grouped(),
            'user'                      => Auth::instance()->get_user(),
            'academic_years'            => Model_AcademicYear::get_academic_years_options(TRUE),
            'schools'                   => Model_Providers::get_all_schools(),
            'years'                     => Model_Years::get_years_where(array(array('publish', '=', 1))),
            'subjects'                  => Model_Subjects::get_all_subjects(),
            'counties'                  => Model_Residence::get_all_counties(),
            'course_types'              => Model_Categories::get_categories_without_parent(),
            'assets_folder_path'        => Kohana::$config->load('config')->assets_folder_path,
            'family_id'                 => $this->request->post('family_id'),
            'privileges_preferences'    => Model_Preferences::get_family_preferences(),
            'redirect'                  => $this->request->post('redirect')
        );

        $type = $this->request->post('type');

        if($type == 'guardian'){
            $this->response->body(View::factory('frontend/snippets/primary_profile_add_guardian', $view));
        }elseif($type == 'student') {
            $this->response->body(View::factory('frontend/snippets/primary_profile_add_student', $view));
        }
    }

    public function action_ajax_get_family_address(){
        $this->auto_render = FALSE;
        $family_id = $this->request->post('family_id');
        $family = new Model_Family($family_id);
        $this->response->body(json_encode($family->address->get()));
    }

    public function action_get_timetables_data()
    {
        $post = $this->request->post();
        $result = array();
        $user = Auth::instance()->get_user();
        $search_params = array();
        if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            $search_params['contact_ids'] = array($contacts[0]['id']);
        } else {
            if (@$post['contact_ids']) {
                $search_params['contact_ids'] = $post['contact_ids'];
            }
        }
        if (@$post['before']) {
            $search_params['before'] = $post['before'];
        }
        if (@$post['after']) {
            $search_params['after'] = $post['after'];
        }
        if (@$post['schedule_id']) {
            $search_params['schedule_id'] = $post['schedule_id'];
        }
        if (@$post['booking_id']) {
            $search_params['booking_id'] = $post['booking_id'];
        }
        if (@$post['weekdays']) {
            $search_params['weekdays'] = $post['weekdays'];
        }
        if (isset($post['attending'])) {
            $search_params['attending'] = $post['attending'];
        }
        if (@$post['timeslot_status']) {
            $search_params['timeslot_status'] = $post['timeslot_status'];
        }


        $result['data'] = array_reverse(Model_Contacts3::get_timetable_data($search_params));
        if (@$post['include_mytime'] && isset($search_params['contact_ids'])) {
            $mytimes = Model_Mytime::search(array('contact_ids' => $search_params['contact_ids']));
            $result['data'] = array_merge($result['data'], Model_Mytime::get_all_timeslots($mytimes));
        }
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_timetables_save_note()
    {
        $post = $this->request->post();
        $result = Model_Contacts3::save_timetable_bulk_note($post['booking_item_ids'], $post['note'], $post['attending'], $post['action'] == 'update');
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_timetables_delete_note()
    {
        $post = $this->request->post();
        $result = Model_Contacts3::delete_timetable_bulk_note($post['booking_item_ids']);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_download_homework()
    {
        $this->auto_render = false;
        Model_Files::download_file($this->request->query('file_id'));
    }

    public function action_ajax_load_bulk_update_classes(){
        $this->auto_render = FALSE;

        if(!Auth::instance()->has_access('contacts3_limited_family_access')){
            return false;
        }

        $data = $this->request->query();

        $modelBookings = new Model_KES_Bookings();
        $view = array(
            'booked_classes' => $modelBookings->get_bulk_update_classes($data),
            'attending' => $data['attending']
        );
        $this->response->body(View::factory('frontend/snippets/bulk_update_classes', $view));
    }

    public function action_attendance_bulk_update(){
        $this->auto_render = FALSE;

        if(!Auth::instance()->has_access('contacts3_limited_family_access')){
            return false;
        }

        $post = $this->request->post();
        $modelBookings = new Model_KES_Bookings();
        $modelBookings->attendance_bulk_update($post);
        Model_Contacts3::save_timetable_bulk_note($post['classes_ids'], $post['note'], $post['attending']);

        return $this->request->redirect('/admin/contacts3/attendance');
    }

    public function action_ajax_load_bulk_update_popup(){
        if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
            return false;
        }
        $this->auto_render = FALSE;
        $this->response->body(View::factory('frontend/snippets/bulk_update_popup'));
    }

    public function action_ajax_get_family_members()
    {
        $this->auto_render = FALSE;

        if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
            return false;
        }

        $family = new Model_Family($this->contact->get_family_id());
        $view = array('family' => $family);

        $this->response->body(View::factory('frontend/snippets/family_members', $view));
    }

    public function action_mytime_save()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();

        $user = Auth::instance()->get_user();
        $search_params = array();
        if (Auth::instance()->has_access('contacts3_limited_view')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        } else {
            $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        $id = @$post['id'] ?: 'new';
        $contact_id = @$post['contact_id']?: $contacts[0]['id'];
        $color = @$post['color'];
        $description = @$post['description'];
        $availability = @$post['availability'];
        $subjects = @$post['subjects'];
        $start_date = date::dmy_to_ymd($post['start_date']);
        $start_time = $post['start_time'] . ':0';
        $end_date = date::dmy_to_ymd($post['end_date']);
        $end_time = $post['end_time'] . ':0';
        $days = @$post['days'] ?: array();
        $warn_on_conflict = (int)$post['warn_on_conflict'];

        $conflicts = array();
        if ($warn_on_conflict) {
            $conflicts = Model_Mytime::check_conflicting_entries($contact_id, $start_date, $start_time, $end_date, $end_time, $days);
        }

        if (count($conflicts)) {
            $result = array(
                'warn' => true,
                'conflicts' => $conflicts
            );
        } else {
            $id = Model_Mytime::save($id, $contact_id, $availability, $subjects, $description, $start_date, $start_time, $end_date, $end_time, $color, $days);
            $result = array(
                'success' => true,
                'id' => $id
            );
        }
        echo json_encode($result);
    }

    public function action_delete_mytime()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();

        $user = Auth::instance()->get_user();
        $search_params = array();
        if (Auth::instance()->has_access('contacts3_limited_view')) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        } else {
            $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
        }
        $contact_ids = array();
        foreach ($contacts as $contact) {
            $contact_ids[] = $contact['id'];
        }

        $ids = $post['ids'];

        $result = Model_Mytime::delete($ids);
        echo json_encode($result);
    }

    private function saveMobileNumber($contact, $mobile){
        $existingMobiles = $contact->get_contact_notifications('mobile');
        if ($existingMobiles) {
            $existingMobiles[0]['value'] = trim($mobile);
            $contact->update_notification($existingMobiles[0]);
        } else {
            $contact->insert_notification(
                array(
                    'value' => trim($mobile),
                    'notification_id' => 2
                )
            );
        }
    }

    public static function compare_by_time($a1, $a2)
    {
        $t1 = strtotime($a1['time']);
        $t2 = strtotime($a2['time']);
        if ($t1 == $t2) {
            return 0;
        } else if ($t1 < $t2) {
            return 1;
        } else {
            return -1;
        }
    }

    public static function check_profile_completion()
    {
        if ( ! Model_Contacts3::check_profile_completion('current')) {
            IbHelpers::set_message(
                __(
                    'Thank you for signing up. Please $1 to ensure all your information is accurate.',
                    array('$1' => '<a href="/admin/profile/edit?section=contact">' . __('complete your profile') . '</a>')
                ),
                'warning popup_box'
            );
        }
    }

    public function action_invite_member()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', "application/json; charset=utf-8");

        $response = array();
        try
        {
            $post = $this->request->post();
            $email = $post['email'];
            $name = @$post['name'];

            $result = Model_Contacts3::invite_member($email, $this->contact->get_id(), $name);

            $response = array(
                'success' => true,
                'sent' => $result
            );
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'msg' => $e->getMessage()
            );
        }

        echo json_encode($response);
    }

    public function action_reject_invite()
    {
        $invite_member = $this->request->query('invite_member');
        $invite_hash = $this->request->query('invite_hash');
        $result = Model_Contacts3::invite_reject($invite_member, $invite_hash);
        IbHelpers::set_message(__('You have rejected to join'), 'info');
        $this->request->redirect('/admin/login');
    }

    public function action_ajax_get_dial_codes(){
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $result = self::get_dial_codes($post['country_code'], $post['phone_type']);
        echo json_encode($result);
        exit;
    }

    public static function get_dial_codes($country_code, $phone_type) {
        if (empty($country_code)) {
           return array();
        } else {
            $country_alpha_code = Model_Country::get_country_code_by_country_dial_code($country_code);
            if (empty($country_alpha_code)) {
                return array();
            }
            if (isset($post['phone_type'])) {
                $dial_codes = Model_Country::get_phone_codes_country_code($country_alpha_code, 2, $phone_type);
            } else {
                $dial_codes = Model_Country::get_phone_codes_country_code($country_alpha_code);
            }
            return $dial_codes;
        }
    }
}