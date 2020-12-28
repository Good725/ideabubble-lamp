<?php defined('SYSPATH') OR die('No Direct Script Access');

final class Controller_Frontend_Formprocessor extends Controller_Template
{
    public $template = 'plugin_template';

    public function before()
    {
        $ret = parent::before();
        if ($this->is_external_referer()){
            $error_id = Model_Errorlog::save(null, "SECURITY");
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/');
        }
        return $ret;
    }

    public function action_course_brochure_download()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $course = Model_Courses::get_course($post['course_id']);
        $schedule = Model_Schedules::get_schedule($post['schedule_id']);
        $exists = Model_Contacts3::search(array('email' => $post['email']));
        if (count($exists) == 0) {
            $contact = new Model_Contacts3();
            $type = Model_Contacts3::find_type('student');
            $contact->set_type($type['contact_type_id']);
            $contact->set_subtype_id(0);
            $contact->set_first_name($post['first_name']);
            $contact->set_last_name($post['last_name']);
            $contact->insert_notification(array('value' => $post['email'], 'notification_id' => 1));
            if ($post['telephone'] != '') {
                $contact->insert_notification(array('value' => $post['telephone'], 'notification_id' => 3));
            }
            $contact->trigger_save = false;
            $contact->save(false);
            $contact_id = $contact->get_id();
        } else {
            $contact_id = $exists[0]['id'];
            $contact = new Model_Contacts3($contact_id);
            if ($post['telephone'] != '') {
                $contact->insert_notification(array('value' => $post['telephone'], 'notification_id' => 3));
                $contact->save(false);
            }
        }

        $tags = array(
            array('tag' => 'BROCHURE', 'description' => 'Brochure Downloaded')
        );
        $fields = array();
        /*if (@$schedule['id']) {
            $tags[] = array(
                'tag' => 'Schedule' . $schedule['id'],
                'description' => $schedule['name']
            );
            $tags[] = array(
                'tag' => $schedule['name'],
                'description' => $schedule['name']
            );
        }*/
        if (@$course['code']) {
            $tags[] = array(
                'tag' => $course['code'],
                'description' => $course['title']
            );
        }
        /*if (@$course['id']) {
            $tags[] = array(
                'tag' => 'Course' .$course['id'],
                'description' => $course['title']
            );
            $tags[] = array(
                'tag' => $course['title'],
                'description' => $course['title']
            );
        }*/
        Model_Automations::run_triggers(
            Model_Formprocessor_Brochuredownloadtrigger::NAME,
            array('contact_id' => $contact_id, 'tags' => $tags, $fields)
        );

        if ($course['file_id']) {
            $media = Model_Media::get_by_filename($course['file_id'], 'docs');
            $path = Model_Media::get_localpath_to_id($media['id']);
            header('Content-disposition: attachment; filename="' . $media['filename'] . '"');
            header('Content-type: ' . $media['mime_type']);
            $this->response->headers('Content-type: ' . $media['mime_type']);
            readfile($path);
        }
        else if ($course['use_brochure_template']) {
            $documents = new Controller_Admin_Documents($this->request, $this->response);
            $data = [
                'direct_download' => true,
                'document_name' => 'course_brochure',
                'contact_id' => $contact_id,
                'course_id' => $course['id']
            ];
            $this->response->headers('Content-type', 'application/pdf; charset=utf-8');
            $documents->action_ajax_generate_kes_document($data);
        }

        // Save a record of the download
        $download = new Model_Course_BrochureDownload();
        $download->values([
            'contact_id' => $contact_id,
            'course_id'  => $course['id'],
            'schedule_id' => $post['schedule_id'],
        ]);
        $download->save();
    }

    // Get the POST data received from the form and take action
    public function action_index()
    {
		$valid = TRUE;
        $post = $this->request->post();
        if (empty($post)) {
            exit;
        }
        if (isset($post['email_template'])) {
            //security issue. email_template should not contain characters like . / @
            if (preg_match('#[^a-z0-9\_\-]#i', $post['email_template'])) {
                $this->request->redirect('/');
            }
        }

        $formprocessor_model = new Model_Formprocessor();

        $ignore_captcha = (isset($post['trigger']) && $post['trigger'] == 'add_to_list' && Settings::instance()->get('newsletter_subscription_captcha') == 0);
        $ignore_captcha = (isset($post['ignore_captcha']) AND $post['ignore_captcha'] == 'true') ? true : $ignore_captcha; // This should be removed. It's exploitable

        if ( ! $ignore_captcha && ! $formprocessor_model->captcha_check($post))
        {
            $page = new Model_Pages();
            $this->request->redirect($page->get_page_by_id(Settings::instance()->get('captcha_fail_page')));
        }

        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $this->create_contact($post);
        }
        //Actions
        switch ($post['trigger'])
		{
            case 'add_to_list':
                $validation = $formprocessor_model->add_to_list($post);
                $valid      = $validation['valid'];
                $error      = $validation['error'];
                break;
            case 'contact_us':
                $formprocessor_model->contact_us($post);
                break;
            case 'call_back_request':
                $formprocessor_model->request_callback($post);
                break;
            case 'booking':
                $formprocessor_model->booking($post);
                $data = ($_POST);
                Model_Bookings::save_booking($data);
                break;
            case 'booking2':
                $formprocessor_model->booking2($post);
                break;
            case 'enquiry':
                $formprocessor_model->enquiry($post);
                break;
            case 'get_a_quote':
                $formprocessor_model->get_a_quote($post);
                break;
            case 'consultation':
                $formprocessor_model->consultation($post);
                break;
            case 'enquiry_form':
                $formprocessor_model->enquiry_form($post);
                break;
            case 'custom_form':
                $valid = $formprocessor_model->custom_form($post);
                break;
			case 'register_account':
				$validation = $formprocessor_model->register_account_form($post);
				$valid      = $validation['valid'];
				$error      = $validation['error'];
				$redirect   = $validation['redirect'];
				break;
            case 'subscribe':
                $valid = $formprocessor_model->subscribe($post);
                break;
            case 'mailchimp_add':
                $valid = $formprocessor_model->mailchimp_add($post);
                break;
            case 'concert_form':
                $valid = $formprocessor_model->concert_form($post);
                break;
            case 'new_project_enquiry':
                $formprocessor_model->new_project_enquiry($post);
                break;

			default:
				throw new Exception('unknown form processor trigger:' . $post['trigger']);
        }

		// Redirect after actions
		if ( ! $valid)
		{
			if (isset($error) AND $error != '')
			{
				IbHelpers::set_message($error, 'error');
			}
            if (isset($redirect)) {
                if (URL::is_internal($redirect)) {
                    $this->request->redirect($redirect);
                } else {
                    $this->request->redirect('/');
                }
            }
			$redirect = (isset($redirect) AND $redirect != '') ? $redirect : $this->request->referrer();
			$this->request->redirect($redirect);
		}
        elseif (isset($post['redirect']) AND ! empty($post['redirect']) AND $valid)
		{
            // for tracking add an activity record for successful email sent
            $activity = new Model_Activity();
            $activity
                ->set_item_type('user')
                ->set_action('email')
                ->set_item_id($post['trigger'])
                ->set_user_id(Request::$client_ip)
                ->save();
            if (URL::is_internal($post['redirect'])) {
                $this->request->redirect($post['redirect']);
            } else {
                $this->request->redirect('/');
            }
        }
    }

    /**
     *
     */
    public function action_render_html()
    {
        $this->template->body = View::factory('email/contactform');
    }
    
    private function create_contact($post) {
        // Find a valid email input, if found validate it's a real proper email, if it is check if there's an existing contact
        // loop through all the valid email addresses and see if they match the input
        $name = '';
        $valid_email = '';
        $user = new Model_Users();
        foreach (array_keys($post) as $post_input_name) {
            // post variable must contain email address and name, must be a valid email address, and must not exist in DB already
            if(strpos($post_input_name, 'form_name') || strpos($post_input_name, 'form_first_name')) {
                if (strpos($post_input_name, 'form_name') ) {
                    $name = $post[$post_input_name];
                } elseif(isset($post['contact_form_first_name'])) {
                    $name = $post['contact_form_first_name'] . ' ' .  $post['contact_form_last_name'];
                }  elseif(strpos($post_input_name, 'form_first_name')) {
                    $name = $post[$post_input_name] . ' ' .  $post[str_replace('first_name', 'last_name', $post_input_name)];
                }
            } else if(strpos($post_input_name, 'email_address')
                && filter_var($post[$post_input_name], FILTER_VALIDATE_EMAIL)
                && (Model_Contacts3::get_existing_contact_by_email_and_mobile($post[$post_input_name]) == null && $user->get_user_by_email($post[$post_input_name]) === false)) {
                $valid_email = $post[$post_input_name];
            }
            if(!empty($name) && !empty($valid_email)) {
                // we assume the last word after the space is the last name
                $name_array = explode(" ", $name);
                $last_name = end($name_array);
                
                $family = new Model_Family();
                $family->set_family_name($last_name);
                if (Settings::instance()->get('contacts_create_family') == 1) {
                    $family->save();
                }

                $contact = new Model_Contacts3();
//                $contact->set_family_id($family->get_id());
                // pop last name from the array
                $pos = array_search($last_name, $name_array);
                unset($name_array[$pos]);
                // first name is the rest (or an empty string is nothing's left)
                $first_name = (count($name_array) == 0) ? '' : implode(" ", $name_array);
                $contact->set_first_name($first_name);
                $contact->set_last_name($last_name);
                $contact->set_date_of_birth(null);
                $contact->insert_notification(array(
                    'contact_id' => 0,
                    'notification_id' => 1,
                    'value' => $valid_email
                ));
                if (@$post['contact_form_tel']) {
                    $contact->insert_notification(array(
                        'contact_id' => 0,
                        'notification_id' => 3,
                        'value' => $post['contact_form_tel']
                    ));
                }
                if(@$post['contact_form_mobile']) {
                    $contact->insert_notification(array(
                        'contact_id' => 0,
                        'notification_id' => 2,
                        'country_dial_code' => @$post['contact_form_mobile_country_code'],
                        'dial_code' => trim(@$post['contact_form_mobile_code']),
                        'value' => trim(@$post['contact_form_mobile'])
                    ));
                }
                $contact->set_family_id($family->get_id());

                if (Settings::instance()->get('contacts_create_family') == 1) {
                    $contact->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                    $contact->set_subtype_id(0);
                } else {
                    $contact->set_type(Model_Contacts3::find_type('Student')['contact_type_id']);
                    $contact->set_subtype_id(1);
                }
                if($post['trigger'] == 'add_to_list') {
                    $contact->set_preferences(
                        array(
                            'preference_id' => Model_Preferences::get_stub_id('marketing_updates'),
                            'notification_type' => 'email',
                            'value' => 1
                        )
                    );
                    $contact->append_tags(Model_Contacts3_Tag::get_tag_by_name('newsletter_signup'));
                } else if ($post['trigger'] == 'contact_us' || $post['formbuilder_id'] == 'Contact Us') {
                    $contact->append_tags(Model_Contacts3_Tag::get_tag_by_name('contact_us_enquiry'));
                } else {
                    $contact->append_tags(Model_Contacts3_Tag::get_tag_by_name('other_form_registration'));
                }
                $contact->trigger_save = false;
                $contact->set_is_primary(1);
                $contact->save();
                
                break;
            }
        }
    }
}
