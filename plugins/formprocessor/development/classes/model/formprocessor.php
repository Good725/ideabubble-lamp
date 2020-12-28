<?php defined('SYSPATH') or die('No Direct Script Access.');

class Model_Formprocessor extends Model
{
	public static function contactus($mailing_list = 'Default')
	{
		//$mails = self::get_contacs_from_list($mailing_list);
		$html_form = View::factory('email/contactform');
		echo $html_form;

		return '';
	}

	public static function consultation_form()
	{
		echo View::factory('email/form-consultation');
		return '';
	}

	/**
	 * @param string $mailing_list
	 *
	 * @return array Array with all the mails in this list
	 */
	public static function get_contacs_from_list($mailing_list = 'Default')
	{
		try
		{
			$sql = DB::select('contacts.*', array('lists.name', 'mailing_list_name'))
                ->from(array('plugin_contacts_contact', 'contacts'))
                    ->join(array('plugin_contacts_mailing_list', 'lists'), 'left')
                        ->on('contacts.mailing_list', '=', 'lists.id')
                ->where('lists.name', '=', $mailing_list)
                ->execute()
                ->as_array();

			return $sql;
		}
		catch (Exception $e)
		{
			return $sql = array();
		}
	}

	/**
	 * Send the email from contact-us page
	 *
	 * @param $post FORM
	 */
	public function contact_us($post)
	{
		$use_messaging   = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');
		$form_identifier = (isset($post['form_identifier']) AND trim($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_';
		$email_body      = View::factory('email/contactformmail', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
		$email_body     .= self::print_all_form_fields($post);

		if ( ! $use_messaging)
		{
			// Use config file and notifications
			$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('contact_notification'));

			if ($event_id !== FALSE)
			{
				$model = new Model_Notifications($event_id);
				//Set subject if passed - otherwise leave the default one
				if ($post['subject'] AND trim($post['subject']) != '')
				{
					$model->set_subject($post['subject']);
				}
				$model->send($email_body);
			} else {
				throw new Exception('config->contact_notification is not set' . Kohana::$config->load('config')->get('contact_notification'));
			}
		}
		else
		{
			// Use messaging plugin
			$messaging_model = new Model_messaging;
			$messaging_model->send_template('contact-form', $email_body, null, array(), array(), null, null, (@$post['contact_form_email_address'] ? $post['contact_form_email_address'] : null));
		}

		//If is set subscribe
		if (isset($post[$form_identifier.'form_add_to_list']))
		{
			$this->add_to_list($post);
		}
	}

	public function consultation($post)
	{
		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('consultation_notification'));

		if ($event_id !== FALSE)
		{
			$model = new Model_Notifications($event_id);
			//Set subject if passed - otherwise leave the default one
			if ($post['subject'] AND trim($post['subject']) != '')
				$model->set_subject($post['subject']);

			$ok = $model->send(View::factory('email/consultationmail', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
		}

		//If is set subscribe
		if (isset($post['subscribe']))
		{
			$this->add_to_list($post);
		}
	}

	public function enquiry_form($post)
	{
		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('enquiry_form'));
		if ($event_id !== FALSE)
		{
			$model = new Model_Notifications($event_id);
			if ($post['subject'] AND trim($post['subject']) != '')
				$model->set_subject($post['subject']);

			$ok = $model->send(View::factory('email/enquiry_form_email', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
		}
	}

	/**
	 * Send the email from course booking page
	 *
	 * @param $post FORM
	 */
	public function booking($post)
	{

		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('booking'));

		if ($event_id !== FALSE)
		{
			$model = new Model_Notifications($event_id);
			$from = (isset($post['guardian_email']) AND !empty($post['guardian_email']) ? $post['guardian_email'] : $model->get_from());
			$model->set_from($from);
			$ok = $model->send(View::factory('email/bookingformmail', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
		}

		//If is set  subscribe
		if (isset($post['contact_form_add_to_list']))
		{
			$this->add_to_list($post);
		}
	}

	/**
	 * Send the email from course booking page
	 *
	 * @param $post FORM
	 */
	public function booking2($post)
	{
		$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');

		// Convert county ID to name
		try
		{
			$courses_model = new Model_Cities();
			$post['guardian_county'] = $courses_model->get_counties($post['guardian_county']);
			$post['student_county'] = $courses_model->get_counties($post['student_county']);
		}
		catch (Exception $e)
		{
			// cities model doesn't exist
		}

		$email_body = View::factory('email/bookingformmail2', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
		$email_body .= self::print_all_form_fields($post);

		if ($use_messaging)
		{
			$messaging_model = new Model_messaging;
			$messaging_model->send_template('contact-form', $email_body);
		}
		else
		{
			$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('booking'));
			if ($event_id !== FALSE)
			{
				$model = new Model_Notifications($event_id);
				$model->send($email_body);
			}
		}

		//If is set  subscribe
		if (isset($post['contact_form_add_to_list']))
		{
			$this->add_to_list($post);
		}
	}

	/**
	 * Send the email from course-enquiry page
	 *
	 * @param $post FORM
	 */
	public function enquiry($post)
	{

		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('booking'));

		if ($event_id !== FALSE)
		{
			$model = new Model_Notifications($event_id);
			$model->set_subject("New Enquiry");

			// Convert county ID to name
			try
			{
				$courses_model = new Model_Cities();
				$post['guardian_county'] = $courses_model->get_counties($post['guardian_county']);
				$post['student_county'] = $courses_model->get_counties($post['student_county']);
			}
			catch (Exception $e)
			{
				// cities model doesn't exist
			}
			$model->update_tags($model, $post);

			$ok = $model->send(View::factory('email/enquiryformmail', array('form' => $post, 'notification' => $model))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
		}

		//If is set  subscribe
		if (isset($post['contact_form_add_to_list']))
		{
			$this->add_to_list($post);
		}
	}

	public function get_a_quote($post)
	{

		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('get_a_quote'));

		if ($event_id !== FALSE)
		{
			$model = new Model_Notifications($event_id);
			$ok = $model->send(View::factory('email/get_a_quote', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
		}

		//If is set  subscribe
		if (isset($post['contact_form_add_to_list']))
		{
			$this->add_to_list($post);
		}
	}


	public function request_callback($post)
	{

		$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('contact_notification_callback'));

		if ($event_id !== FALSE)
		{
			$model = new Model_Notifications($event_id);
			$ok = $model->send(View::factory('email/callbackformmail', array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
		}

		//If is set  subscribe
		if (isset($post['contact_form_add_to_list']))
		{
			$this->add_to_list($post);
		}
	}

	/**
	 * Add contact to default contact list
	 *
	 * @param $post FORM
	 */
	public function add_to_list($post)
	{
		/*
		 * Get form Identifier
		 * @NOTE: Most Form have these Fields HARDCODED, i.e: contact_form_name, contact_form_tel etc.
		 * 		  HOWEVER - THIS Function is used by many Different Forms, which should be updated to HAVE a Proper Form Identifier,
		 * 					that will allow them to use this Function
		 * BY DEFAULT: the $form_identifier will be Kept as: 'contact_' so We don't Mess Up this for the Contact Form
		 */

        $return = array('valid' => TRUE, 'error' => '');

		$form_identifier = (isset($post['form_identifier']) AND trim($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_';

        // IBIS contacts plugin
        if (class_exists('Model_Contact') && Model_Plugin::is_enabled_for_role('Administrator', 'contacts') ) {
            $data = $post;

            if (isset($post[$form_identifier.'form_first_name']) && isset($post[$form_identifier.'form_last_name'])) {
                $data['forename'] = $post[$form_identifier.'form_first_name'];
                $data['surname']  = $post[$form_identifier.'form_last_name'];
            } else {
                // Assume the forename is everything before the first space
                // Assume the surname is everything after the last space
                $separator_position = strrpos($post[$form_identifier.'form_name'], ' ');
                $data['forename']   = substr($post[$form_identifier.'form_name'], 0, $separator_position);
                $data['surname']    = ($separator_position) ? substr($post[$form_identifier.'form_name'], $separator_position + 1) : '';
            }

            $data['salutation'] = $data['forename'];
            $data['c_type']     = Model_Contacttype::lookup_type_id('General Contact');
            $data['notes']      = isset($post[$form_identifier.'form_notes']) ? $post[$form_identifier.'form_notes'] : '';

            // Split the address input into lines
            if (isset($post[$form_identifier.'form_address'])) {
                $address = explode("\n", $post[$form_identifier.'form_address']);
                $data['address1'] = isset($address[0]) ? $address[0] : '';
                $data['address2'] = isset($address[1]) ? $address[1] : '';
                $offset = strlen($data['address1'].' '.$data['address1']);
                $data['address3'] = substr(preg_replace('/\s\s+/', ' ', $post[$form_identifier.'form_address']), $offset);
            }

            // Save the contact
            $contact_saved = Model_Contact::add($data);

            // Save the contact details (email and phone)
            $contact_details = new Model_ContactDetails();
            $contact_details->add($contact_saved, array($post[$form_identifier.'form_email_address']), array('email'), '');
            if (isset($post[$form_identifier.'form_tel'])) {
                $contact_details->add($contact_saved, array($post[$form_identifier.'form_tel']), array('phone'), 1);
            }
        }
        else {
			if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {

                // Email field name could be different depending on the form; prefixed with the form identifier.
				$form_identifier = isset($post['form_identifier']) ? $post['form_identifier'] : 'newsletter_signup';
				$email = $post[$form_identifier.'form_email_address'];

				$exists = Model_Contacts3::search(array('email' => $email));

				if (count($exists) == 0) {
					$contact = new Model_Contacts3();
					$type = Model_Contacts3::find_type('student');
					$contact->set_type($type['contact_type_id']);
					$contact->set_subtype_id(0);

                    if (isset($post[$form_identifier.'form_first_name']) && isset($post[$form_identifier.'form_last_name'])) {
                        $contact->set_first_name($post[$form_identifier.'form_first_name']);
                        $contact->set_last_name($post[$form_identifier.'form_last_name']);
                    } else {
                        $contact->set_first_name($post[$form_identifier.'form_name']);
                        $contact->set_last_name('');
                    }
   					$contact->insert_notification(array('value' => $email, 'notification_id' => 1));
					$contact->trigger_save = false;
					$contact->save(false);
					$contact_id = $contact->get_id();
				} else {
					$contact_id = $exists[0]['id'];
				}

				$tags = array(
					array('tag' => 'SUBSCRIBE', 'description' => 'Newsletter Subscribed'),
				);
				Model_Automations::run_triggers(
						Model_Formprocessor_Newslettersubscribetrigger::NAME,
						array('contact_id' => $contact_id, 'tags' => $tags)
				);

                // Tag the contact, as being created via mailing-list subscription
                $contact_orm = new Model_Contacts3_Contact($contact_id);
                $contact_orm->add_tag('newsletter_signup');

                // Give the contact the "marketing updates" preference, if they subscribed.
                if (!empty($post['contact_form_add_to_list'])) {
                    $contact_orm->add_preference('marketing_updates');
                }

                $contact_saved = $contact_id;
			} else {
				$contact_model = new Model_Contacts();
				if (isset($post['name'])) {
					$contact_model->set_first_name($post['name']);
				}
				if (isset($post[$form_identifier . 'form_name'])) {
					$contact_model->set_first_name($post[$form_identifier . 'form_name']);
				}
				if (isset($post[$form_identifier . 'form_tel'])) {
					$contact_model->set_phone($post[$form_identifier . 'form_tel']);
				}
				if (isset($post['email'])) {
					$contact_model->set_email($post['email']);
				}
				if (isset($post[$form_identifier . 'form_email_address'])) {
					$contact_model->set_email($post[$form_identifier . 'form_email_address']);
				}

				// If the email is invalid, don't continue
				$contact_details = $contact_model->get_details();
				if (empty($contact_details['email']) OR !filter_var($contact_details['email'], FILTER_VALIDATE_EMAIL)) {
					return array(
							'valid' => false,
							'error' => 'Please enter a valid email address.'
					);
				}

				$contact_model->set_mailing_list('Newsletter'); //Set the mailing list in PHP, Make sure you don't get this option from the POST data because this is a risk
				$contact_model->set_publish(1);
				$contact_saved = $contact_model->save();
			}
        }


		// Send Notification to Admin for the Just Saved Contact
		if ($contact_saved !== FALSE)
		{
			$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');
			$email_body    = View::factory(
					'email/new_member_to_mailing_list_admin_mail_notice',
					array('form' => $post)
				)
					->set('skip_comments_in_beginning_of_included_view_file', TRUE)
					->render();


			if ($use_messaging)
			{
				$form_identifier = (isset($post['form_identifier']) AND trim ($post['form_identifier']) != '') ? $post['form_identifier'] : 'contact_';

                $referer_components = parse_url($_SERVER['HTTP_REFERER']);

				$template_params = array(
					'name'         => @$post[$form_identifier . 'form_name'],
					'email'        => @$post[$form_identifier.'form_email_address'],
					'host'         => $_SERVER['HTTP_HOST'],
                    'referer_path' => trim($referer_components['path'], '/'),
                    'referer'      => $_SERVER['HTTP_REFERER']
				);
				
				// Use messaging plugin
				$messaging_model = new Model_messaging;
				$messaging_model->send_template(
					'newsletter-signup',
					null,
					null,
					array(),
					$template_params
				);
                
                $extra_targets = array(
                    array(
                        'id' => null,
                        'template_id' => null,
                        'target_type' => 'EMAIL',
                        'target' => @$post[$form_identifier . 'form_email_address'],
                        'x_details' => 'to',
                        'date_created' => null
                    )
                );
                
				$messaging_model->send_template(
				    'newsletter-signup-frontend-user', null, null, $extra_targets, $template_params
                );
			}
			else
			{
				// Use notifications plugin
				$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('add_to_list_admin_notification'));
				if ($event_id !== FALSE)
				{
					$model = new Model_Notifications($event_id);
					//Set Subject if Passed - otherwise leave the default one
					if (isset($post['subject']) AND trim($post['subject']) != '') $model->set_subject($post['subject']);
					$model->send($email_body);
				}
			}
		}

        return $return;
	}

	// Returns true if the captcha works out or no captcha set on site.
	// False on failure.
	public static function captcha_check($post)
	{
		// For checkouts we need to cast from json to array object
		if (is_object($post))
		{
			$post = (array) $post;
		}

		// Use the site's CAPTCHA-enabled setting as the default.
		$captcha_public_key  = Settings::instance()->get('captcha_public_key');
		$captcha_private_key = Settings::instance()->get('captcha_private_key');
		$captcha_enabled     = Settings::instance()->get('captcha_enabled');
		$captcha_enabled     = ($captcha_enabled && $captcha_public_key != '' && $captcha_private_key != '');

		// If the submitted form has a different CAPTCHA setting, use that instead of the site setting
		if ( ! empty($post['formbuilder_id']))
		{
			$captcha_enabled = Model_Formbuilder::is_captcha_enabled($post['formbuilder_id']);
		}

		if ($captcha_enabled)
		{
            require 'recaptchalib.php';

            // Verify v2 reCAPTCHA, if its corresponding parameter has been used
            if (isset($post['g-recaptcha-response']))
            {
                try
                {
                    $url = 'https://www.google.com/recaptcha/api/siteverify';

                    $data = array(
                        'secret'   => $captcha_private_key,
                        'response' => $post['g-recaptcha-response'],
                        'remoteip' => $_SERVER['REMOTE_ADDR']
                    );
                    $options = array(
                        'http' => array(
                            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method'  => 'POST',
                            'content' => http_build_query($data)
                        )
                    );
                    $context  = stream_context_create($options);
                    $response = file_get_contents($url, false, $context);
                    $resp     = json_decode($response, true);

                    return ($resp['success'] == 'true');

                }
                catch (Exception $e)
                {
                    Log::instance()->add(Log::ERROR, "Error validating CAPTCHA\n".$e->getMessage().$e->getTraceAsString())->write();
                    return false;
                }
            }

            // Validate v1 reCAPTCHA
            else
            {
                if ( ! isset($post['recaptcha_challenge_field']) OR ! isset($post['recaptcha_response_field']))
                {
                    // CAPTCHA is enabled, but the form did not submit CAPTCHA data
                    return false;
                }

                // Check if the user typed the CAPTCHA correctly
                $resp = recaptcha_check_answer($captcha_private_key, $_SERVER["REMOTE_ADDR"], $post["recaptcha_challenge_field"], $post["recaptcha_response_field"]);
                return $resp->is_valid;
            }
		}
		else
		{
			// CAPTCHA is not enabled. Allow the user to proceed without performing a check.
			return true;
		}
	}

	public function custom_form($post)
	{
        $email_parameters = Request::$current->post();
		if (@$post['custom_form_call'] != '' && method_exists('Model_Formprocessor', $post['custom_form_call'])) {
			call_user_func('Model_Formprocessor::' . $post['custom_form_call'], $post);
		}
		$form_id = Session::instance()->get('form_id');
		if (!isset($form_id) OR !is_numeric($form_id))
		{
			// return FALSE;
		}

        if (isset($post['contact_form_add_to_list']) AND $post['contact_form_add_to_list'] == 'on' && $post['event'] != 'contact-form' )
        {
            $this->add_to_list($post);
        }

		$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');

		$event_id = Model_Notifications::get_event_id($post['event']);
		if ($use_messaging OR $event_id !== FALSE)
		{
			$valid = TRUE;
			$validation_function = 'validate_form_'.$post['event'];
			if (method_exists($this, $validation_function))
			{
				$validation = $this->$validation_function($post);
				$valid = $validation['valid'];
				$ok = $valid;
			}

			if ($valid)
			{
				$files = array();
				if (!empty($_FILES))
				{
					for ($i = 0; $i < count($_FILES['formbuilder_files']['tmp_name']); $i++)
					{
						if (is_file($_FILES['formbuilder_files']['tmp_name'][$i]))
						{
							move_uploaded_file($_FILES['formbuilder_files']['tmp_name'][$i], '/var/tmp/'.$_FILES['formbuilder_files']['name'][$i]);
							$files[] = '/var/tmp/'.$_FILES['formbuilder_files']['name'][$i];
						}
					}
				}

                $extra_recipients = [];

                // If the user is interested in a course...
                if (!empty($post['interested_in_course_id']) && class_exists('Model_Courses')) {
                    // ... get course details to include in the email body.
                    $course_id = Kohana::sanitize($post['interested_in_course_id']);
                    $course    = Model_Courses::get_course($course_id);
                    $post['interested_in_course'] = $course;
                }

                // If the user is interested in a schedule...
                if (!empty($post['interested_in_schedule_id']) && class_exists('Model_Courses')) {
                    // ... get schedule details to include in the email body.
                    $schedule_id = Kohana::sanitize($post['interested_in_schedule_id']);
                    $schedule = Model_Schedules::get_schedule($schedule_id);
                    $post['interested_in_schedule'] = $schedule;

                    // ... and if there is a franchisee, add the franchisee as a recipient and use the franchisee template
                    if (!empty($schedule['owned_by'])) {
                        $extra_recipients[] = ['target_type' => 'CMS_USER', 'target' => $schedule['owned_by'], 'x_details' => 'to'];

                        if ($post['event'] == 'contact-form') {
                            $email_parameters['schedule_id']   = $schedule['id'];
                            $email_parameters['schedule_name'] = $schedule['name'];
                            $post['event'] = 'contact-form-franchisee';
                        }
                    }
                }

                if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                    $email = null;
                    if (isset($post['contact_form_email_address'])) {
                        $email = $post['contact_form_email_address'];
                    }
                    if (isset($post['contact_form_email'])) {
                        $email = $post['contact_form_email'];
                    }
                    $exists = Model_Contacts3::search(array('email' => $email));
                    if (count($exists) == 0) {
                        $contact = new Model_Contacts3();
                        $type = Model_Contacts3::find_type('student');
                        $contact->set_type($type['contact_type_id']);
                        $subtype = Model_Contacts3::find_subtype('student');
                        $contact->set_subtype_id(!empty($subtype['id']) ? $subtype['id'] : 0);
                        $first_name = !empty($post['contact_form_first_name']) ? $post['contact_form_first_name'] : $post['contact_form_name'];
                        $last_name  = !empty($post['contact_form_last_name'])  ? $post['contact_form_last_name']  : '';
                        $contact->set_first_name($first_name);
                        $contact->set_last_name($last_name);

                        if ($email) {
                            $contact->insert_notification(array('value' => $email, 'notification_id' => 1));
                        }
                        if (isset($post['contact_form_tel'])) {
                            $contact->insert_notification(array('value' => $post['contact_form_tel'], 'notification_id' => 3));
                        }

                        $contact->trigger_save = false;
                        $contact->save(false);
                        $contact_id = $contact->get_id();
                    } else {
                        $contact_id = $exists[0]['id'];
                    }

                    $tags = array();
					if (@$post['interested_in_course']) {
						$tags[] = array('tag' => 'Enquire', 'description' => 'Enquire');
					} else if (@$post['subject'] == 'Callback Request') {
						$tags[] = array('tag' => 'Callback a Request', 'description' => 'Callback a Request');
					} else {
						$tags[] = array('tag' => 'CONTACTUS', 'description' => 'Contact US');
						$contact_orm = new Model_Contacts3_Contact($contact_id);
						$contact_orm->add_tag('contact_us_enquiry');
					}
					$fields = array();
					if (@$post['interested_in_course']['code']) {
                        $tags[] = array(
                            'tag' => $post['interested_in_course']['code'],
                            'description' => $post['interested_in_course']['title']
                        );
						/*$tags[] = array(
							'tag' => 'Course' . $post['interested_in_course']['id'],
							'description' => $post['interested_in_course']['id']
						);
						$tags[] = array(
								'tag' => $post['interested_in_course']['title'],
								'description' => $post['interested_in_course']['title']
						);*/
					}
					/*if ($post['interested_in_schedule']) {
						$tags[] = array(
								'tag' => 'Schedule' . $post['interested_in_schedule']['id'],
								'description' => $post['interested_in_schedule']['id']
						);
						$tags[] = array(
								'tag' => $post['interested_in_schedule']['name'],
								'description' => $post['interested_in_schedule']['name']
						);
                    }
                    if (@$post['interested_in_schedule']['start_date']) {
                        $tags[] = array(
                            'tag' => $post['interested_in_schedule']['start_date'],
                            'description' => $post['interested_in_schedule']['name']
                        );
                    }
                    if (@$post['interested_in_schedule']['fee_amount']) {
                        $tags[] = array(
                            'tag' => $post['interested_in_schedule']['fee_amount'],
                            'description' => $post['interested_in_schedule']['name']
                        );
                    }
                    */

                    Model_Automations::run_triggers(
                        Model_Formprocessor_Newslettersubscribetrigger::NAME,
                        array('contact_id' => $contact_id, 'tags' => $tags, 'fields' => $fields)
                    );
                }

                $email_body = View::factory('email/'.$post['email_template'], array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
				$email_body .= self::print_all_form_fields($post);

				if ($use_messaging)
				{
					// Use messaging plugin
					$messaging_model = new Model_messaging;
					$sent = $messaging_model->send_template($post['event'], $email_body, $files, $extra_recipients, $email_parameters, null, null, (@$post['contact_form_email_address'] ? $post['contact_form_email_address'] : null));

					// If there is an additional notification
					if ($sent and isset($post['confirmation_event']) AND isset($post['confirmation_email_field']) AND isset($post[$post['confirmation_email_field']]))
					{
						// Get the custom recipient from the form
						$extra_targets = array(
							array('id' => NULL,'template_id' => NULL,'target_type' => 'EMAIL','target' => $post[$post['confirmation_email_field']],'x_details' => 'to','date_created' => NULL)
						);

						$messaging_model->send_template($post['confirmation_event'], 'email/'.$post['confirmation_email_template'], $files, $extra_targets, array(), null, null, (@$post['contact_form_email_address'] ? $post['contact_form_email_address'] : null));
					}

				}
				else
				{
					// Use notifications plugin
					$model = new Model_Notifications($event_id);
					$sent = $model->send($email_body, $files);

					if ($sent AND isset($post['confirmation_email_template']) AND isset($post['confirmation_email_field']) AND isset($post[$post['confirmation_email_field']]))
					{
						$confirmation_sent = $model->send(
							View::factory('email/'.$post['confirmation_email_template'], array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render(),
							$files, // attachments
							$post[$post['confirmation_email_field']] // custom recipient
						);
						if ( ! $confirmation_sent)
						{
							IBHelpers::set_message('Error sending confirmation email', 'error');
						}
					}
				}


				if ($sent AND (isset($post['add_to_list'])) OR (isset($post['form_identifier']) AND isset($post[$post['form_identifier'].'form_add_to_list']) AND $post['event'] != 'contact-form' ))
				{
					$data['contact_form_name'] = isset($post['name']) ? $post['name'] : '';
					$data['contact_form_tel'] = isset($post['phone']) ? $post['name'] : '';
					$data['contact_form_email_address'] = isset($post['email']) ? $post['email'] : '';
					$this->add_to_list($data);
				}
                else if ( ! $sent)
                {
                    IBHelpers::set_message("We could not find an email template to use with your notification.", 'error');
                } else if ($post['event'] == 'contact-form' ) {
                    $thankyou_email_body = View::factory('email/thankyouforcontactingmail',
                        array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file',
                        true)->render();
                    $thankyou_email_body .= self::print_all_form_fields($post);
                    if ($use_messaging) {
                        // Use messaging plugin
                        $messaging_model = new Model_messaging;
                        $extra_recipients = array(
                            array(
                                'target_type' => 'EMAIL',
                                'target' =>  @$post['contact_form_email_address'] ? $post['contact_form_email_address'] : null,
                                'x_details' => 'to',
                                'date_created' => NULL)
                        );
                        if(empty($email_parameters['first_name'])) {
                            $email_parameters['first_name'] = $email_parameters['contact_form_first_name'];
                        }
                        if(empty($email_parameters['email'])) {
                            $email_parameters['email'] = $email_parameters['contact_form_email_address'];
                        }
                        if(empty($email_parameters['email'])) {
                            $email_parameters['email'] = $email_parameters['contact_form_email_address'];
                        }
                        //die('<pre>' . print_r($email_parameters, 1) . '</pre>');
                        $sent = $messaging_model->send_template(
                            'thank_you_for_contacting_us',
                            null,
                            null,
                            $extra_recipients,
                            $email_parameters,
                            'Thank you for contacting us',
                            null,
                            null
                            );
                    } else {
                        // Use notifications plugin
                        $model = new Model_Notifications($event_id);
                        $sent = $model->send($thankyou_email_body, $files);
                    }
                }
				return $sent;
			}
			else
			{
				IBHelpers::set_message($validation['message'], 'error');
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	public function register_account_form($post)
	{
		$return['valid']    = TRUE;
		$return['error']    = '';
		$return['redirect'] = '';
		$user_model = new Model_Users();
		$role_model = new Model_Roles();
		
		$user_data = array();
		$user_data['email'] = $post['email'];
		$user_data['password'] = $post['password'];
		$user_data['name'] = $post['firstname'];
		$user_data['surname'] = $post['surname'];
		$user_data['country'] = $post['country'];
		$user_data['county'] = $post['county'];
		$user_data['address'] = $post['address_line1'];
		$user_data['address_2'] = $post['address_line2'];
		$user_data['eircode'] = isset($post['eircode']) ? $post['eircode'] : '';
		$user_data['phone'] = $post['phone'];
		$user_data['company'] = $post['company'];
		$user_data['role_id'] = $role_model->get_id_for_role($post['account_type']);
		if ( ! $user_data['role_id'])
		{
			$user_data['role_id'] = $role_model->get_id_for_role('Basic');
		}
		$user_data['role_other'] = isset($post['account_type_other']) ? $post['account_type_other'] : '';
		$user_data['heard_from'] = $post['heard_from'];
		$user_data['email_verified'] = 0;
		$user_data['can_login'] = 0;
		$user_data['deleted'] = 0;
		$user_data['registered'] = date('Y-m-d H:i:s');

		// Validate email
		if (filter_var($post['email'], FILTER_VALIDATE_EMAIL) === FALSE)
		{
			$return['valid'] = FALSE;
			$return['error'] = 'Invalid email address';
		}
		elseif ($user_model->get_user_by_email($user_data['email']) !== FALSE)
		{
			$return['valid'] = FALSE;
			$return['error'] = 'This account has already been registered.';
		}

		// Validate password
		elseif ($post['password'] == '')
		{
			$return['valid'] = FALSE;
			$return['error'] = 'You must supply a password.';
		}
		elseif (strlen($post['password']) < 8)
		{
			$return['valid'] = FALSE;
			$return['error'] = 'Your password must be at least 8 characters.';
		}
		elseif ( ! isset($post['repassword']) OR $post['password'] != $post['repassword'])
		{
			$return['valid'] = FALSE;
			$return['error'] = 'Passwords do not match';
		}

		if ($return['valid'])
		{
			try {
				Database::instance()->begin();
				$result = $user_model->add_user_data($user_data);
				$new_user_id = $result ? $result[0] : false;
				if ($new_user_id !== false) {
					// Send verification email to the user
					$hash = md5($post['email'] . Controller_Frontend_Users::SALT);
                    $redirect = isset($post['redirect']) ? '&redirect='.urlencode($post['redirect']) : '';
					$link = URL::site('frontend/users/registration_confirmation/' . $new_user_id . '?hash=' . $hash.$redirect);
					$email_body = View::factory('content/frontend/registration_confirmation_email')->set('link',
							$link)->set('skip_comments_in_beginning_of_included_view_file', true)->render();
					$email_body .= self::print_all_form_fields($post);

					$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');

					if ($use_messaging) {
						$messaging_model = new Model_messaging;
						$messaging_model->send_template('register_account_user', $email_body);
					} else {
						$subject = 'Email confirmation for ' . $post['email'] . ' on ' . URL::base();
						$from = Settings::instance()->get('account_verification_sender');
						$from = ($from == '') ? 'noreply@ideabubble.ie' : $from;
						IbHelpers::send_email($from, $post['email'], null, null, $subject, $email_body);
					}

					// Sent email to the administration
					$email_body = View::factory('email/register_account_form_mail',
							array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file',
							true)->render();
					$email_body .= self::print_all_form_fields($post);
					if ($use_messaging) {
						$messaging_model = new Model_messaging;
						$sent = $messaging_model->send_template('register_account_admin', $email_body);
					} else {
						$event_id = Model_Notifications::get_event_id($post['event']);
						$notifications_model = new Model_Notifications($event_id);
						$sent = $notifications_model->send($email_body);
					}

					$return['valid'] = ($sent !== false);
				} else {
					$return['valid'] = false;
				}
				Database::instance()->commit();
			} catch (Exception $exc) {
				Database::instance()->rollback();
				throw $exc;
			}
		}

		return $return;
	}

	public static function validate_form_express_repeat_prescriptions($data)
	{
		$message = '';
		if (!isset($data['name']) OR trim($data['name']) == '')
		{
			$message = 'You must supply a name';
		}
		elseif (!isset($data['address']) OR trim($data['address']) == '')
		{
			$message = 'You must supply an address';
		}
		elseif (!isset($data['mobile']) OR trim($data['mobile']) == '')
		{
			$message = 'You must supply a mobile or phone number';
		}
		elseif (!isset($data['pharmacy_id']) OR trim($data['pharmacy_id']) == '')
		{
			$message = 'You must select a pharmacy';
		}
		$valid = ($message == '');

		return array('valid' => $valid, 'message' => $message);
	}

    /**
     * Send the email from subscribe form
     *
     * @param $post FORM
     */
    public function subscribe($post)
    {
        // initialise values for the notifications model
        $email_template = "email/subscribeformmail";
        $event_id = Model_Notifications::get_event_id($post['event']);

        if ($event_id !== FALSE)
        {
            $model = new Model_Notifications($event_id);
            //Set subject if passed - otherwise leave the default one
            if ($post['subject'] AND trim($post['subject']) != '')
                $model->set_subject($post['subject']);

            //load the email template file to be used for the notification
            $ok = $model->send(View::factory($email_template, array('form' => $post))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
            if(!$ok)
            {
                IbHelpers::set_message("We could not find a form template for this notification.. Please re check your form template details.", 'error');
                return false;
            }
        }
        else
        {
            IbHelpers::set_message("An event cannot be found for this form. Please re check your form event details.", 'error');
            return false;
        }

        //add them to newsletter list
        $this->add_to_list($post);

        return true;

    }

	public function mailchimp_add($post)
	{
		if (Settings::instance()->get('mailchimp_list_id') != '' && Settings::instance()->get('mailchimp_apikey') != '') {
			$mc = new Mailchimp();
			$result = $mc->add_to_list($post['contact_form_email_address'], 'subscribed', $_SERVER['REMOTE_ADDR']);
			if (@$result['id']) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function print_all_form_fields($post)
	{
		$return = '';
		if ( ! empty($post['formbuilder_id']))
		{
			$form_data = Model_Formbuilder::get_form_data($post['formbuilder_id']);

			if ( ! empty($form_data['email_all_fields']))
			{
				$return .= View::factory('all_form_data')
					->set('form', $post)
					->set('skip_comments_in_beginning_of_included_view_file', TRUE)
					->render();
			}
		}

		return $return;
	}

	public function concert_form($params)
	{
		$messaging_model = new Model_messaging;
		return $messaging_model->send_template('concert-form', null, null, array(), $params);
	}

    public function new_project_enquiry($params)
    {
        try {
            $extra_targets = array();
            if (class_exists('Model_Contacts'))
            {
                // Split the name field into two
                $last_name  = (isset($params['name']) && strpos($params['name'], ' ') !== false) ? preg_replace('#.*\s([\w-]*)$#', '$1', $params['name']) : '';
                $first_name = isset($params['name']) ? trim(preg_replace('#'.$last_name.'#', '', $params['name'])) : ' ';

                // Save the contact
                $contact = new Model_Contacts();
                $contact->set_publish(1);
                $contact->set_first_name($first_name);
                $contact->set_last_name($last_name);
                $contact->set_mailing_list('Default');
                if (!empty($params['email'])) $contact->set_email($params['email']);
                if (!empty($params['phone'])) $contact->set_email($params['phone']);

                $contact->save($params);
                $extra_targets = array(array('target_type' => 'CMS_CONTACT', 'target' => $contact->get_id() ,'x_details' => 'to'));
            }

            // Send the messages
            $messaging = new Model_Messaging;
            $messaging->send_template('new_project_enquiry_admin', null, null, array(), $params, null, null, $params['email']);
            $messaging->send_template('new_project_enquiry_customer', null, null, $extra_targets, $params);
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error submitting campaign form\n"
                .$e->getMessage().$e->getTraceAsString().
                "\n<pre>".json_encode($params)."</pre>"
            )->write();
        }

    }

	public static function save_groody_booking($post)
	{
		$values = array(
				'title' => $post['book_now_form_title'],
				'gender' => $post['book_now_form_gender'],
				'first_name' => $post['book_now_form_first_name'],
				'last_name' => $post['book_now_form_last_name'],
				'dob' => date::dmy_to_ymd($post['book_now_form_dob']),
				'pps_number' => $post['book_now_form_ppsn'],
				'disabled_registered' => @$post['book_now_form_disabled_registered'] == 'yes',
				'disability_details' => $post['book_now_form_disabled_info'],
				'nationality' => $post['book_now_form_nationality'],
				'academic_year' => $post['book_now_form_academic_year'],
				'attending_uni_college' => $post['book_now_form_attending_uni_college'],
				'attending_course' => $post['book_now_form_attending_course'],
				'student_id' => $post['book_now_form_student_id'],
				'student_academic_status' => $post['book_now_form_student_academic_status'],
				'email_address' => $post['book_now_form_email_address'],
				'mobile_phone' => $post['book_now_form_mobile'],
				'home_phone' => $post['book_now_form_pg_phone'],
				'street_address' => $post['book_now_form_address'],
				'town' => $post['book_now_form_town'],
				'county' => $post['book_now_form_county'],
				'post_code' => $post['book_now_form_post_code'],
				'country' => $post['book_now_form_country'],
				'booking_period' => $post['book_now_form_booking_period'],
				'apt_type' => $post['book_now_form_apt_type'],
				'apt_share_1' => $post['book_now_form_apt_share1'],
				'apt_share_2' => $post['book_now_form_apt_share2'],
				'apt_share_3' => $post['book_now_form_apt_share3'],
				'apt_share_4' => $post['book_now_form_apt_share4'],
				'apt_share_5' => $post['book_now_form_apt_share5'],
				'been_before' => $post['book_now_form_been_before'],
				'from_where' => $post['book_now_form_from_where'],
				'notes' => $post['book_now_form_additional_details'],
		);
		$values['title_id'] = substr($values['title'], 0, strpos($values['title'], '_'));
		unset($values['title']);
		$values['academic_year_id'] = substr($values['academic_year'], 0, strpos($values['academic_year'], '_'));
		unset($values['academic_year']);
		$values['attended_uni_id'] = substr($values['attending_uni_college'], 0, strpos($values['attending_uni_college'], '_'));
		unset($values['attending_uni_college']);
		$values['course_id'] = substr($values['attending_course'], 0, strpos($values['attending_course'], '_'));
		unset($values['attending_course']);
		$values['booking_period_id'] = substr($values['booking_period'], 0, strpos($values['booking_period'], '_'));
		unset($values['booking_period']);
		$values['apt_type_id'] = substr($values['apt_type'], 0, strpos($values['apt_type'], '_'));
		unset($values['apt_type']);
		$values['nationality_id'] = substr($values['nationality'], 0, strpos($values['nationality'], '_'));
		unset($values['nationality']);
		$values['country_id'] = substr($values['country'], 0, strpos($values['country'], '_'));
		unset($values['country']);
		$values['academic_status_id'] = substr($values['student_academic_status'], 0, strpos($values['student_academic_status'], '_'));
		unset($values['student_academic_status']);
		$values['heard_about_id'] = substr($values['from_where'], 0, strpos($values['from_where'], '_'));
		unset($values['from_where']);

		$values['date_created'] = date::now();
		DB::insert('booking_form_booking_records')
			->values($values)
			->execute();
		//header('content-type: text/plain');print_r($values);exit;
	}
}