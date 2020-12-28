<?php
defined('SYSPATH')or die('No direct script access.');


//add new form
//edit existing form
//delete existing form
//show existing forms

class Model_Formbuilder extends Model
{
    const form_table = 'plugin_formbuilder_forms';
    private $form;
    private $id;
    private $form_name;
    private $action;
    private $method;
    private $fields;
    private $options;
    private $summary;
	private $email_all_fields;
    private $captcha_enabled;
    private $captcha_version;
	private $use_stripe;
    private $form_id;
    private $publish = 1;

    public function get_id()
    {
        return $this->id;
    }

    function __construct($form = NULL)
    {
        if($form !== NULL AND is_object($form) OR is_numeric($form))
        {
            if (is_null($_POST) or empty($_POST))
            {
                $this->set($this->get_form_data($form));
            }
            else
            {
                $data = $_POST;
                $data['id'] = $form;
                $this->set($data);
            }
        }
        else if(is_string($form) AND $form == "new")
        {
            $_POST['id'] = $this->get_next_id();
            $this->set($_POST);
        }
    }

    public function show_all_forms($column = 'date_modified',$direction = 'desc')
    {
        $query = DB::select('id','form_name','action','method','date_modified','publish')
            ->from('plugin_formbuilder_forms')
            ->where('deleted', '=', 0)
            ->or_where('deleted', '=', NULL)
            ->order_by($column,$direction)->execute()->as_array();
        return $query;
    }

    public function save($post,$type = 'new')
    {
        if(!isset($post))
        {
            throw new Exception("No post data.");
        }

        $this->set($post);

        $this->set_action($this->generate_notification_link($post['action']));
        $this->options = "redirect:".$post['success_page']."|failpage:".$post['failure_page'];

        try {
            Database::instance()->begin();
            if(is_numeric($type) AND !empty($type))
            {
                DB::update(self::form_table)->set($this->load())->where('id','=',$type)->execute();
            }
            else
            {
                DB::insert(self::form_table,array_keys($this->load()))->values($this->load())->execute();
            }
            Database::instance()->commit();
        }
        catch(Database_Exception $e)
        {
            Log::instance()->add(Log::ERROR, 'Failed to create form.');
            Database::instance()->rollback();
            throw $e;
        }

        if(!is_numeric($type))
        {
            $query = DB::select('id')->from('plugin_formbuilder_forms')->order_by('date_created','ASC')->limit(1)->execute()->as_array();
            $return = $query[0]['id'];
        }
        else
        {
            $return = $type;
        }

        return TRUE;
    }

    public function generate_notification_link($post)
    {
        //until there is time to do this properly just return the link hardcoded...
        $actions = array('frontend/formprocessor/' => 'formprocessor');
        /*if(in_array($post,$actions))
        {
            $location = array_keys($actions,$post);
            return $location[0];
        }
        else
        {
            return 0;
        }*/

        if($k = array_search($post,$actions))
        {
            return $k;
        }
        else if ($post == '')
        {
            return 'frontend/formprocessor/';
        }
        else
        {
            return $post;
        }
    }

    public function present_form($id,$list_order = "ul", $add_form_tag = true)
    {
        try
		{
            if ( ! isset($id) AND ( ! isset($this->form) OR ! is_numeric($this->form)))
            {
                throw new Exception("ID for form is not set.");
            }

            if (is_numeric($this->form))
            {
                $id = $this->form;
            }

            if (is_numeric($id) == $id)
            {
                $search_criteria = 'id';
            }
            else
            {
                $search_criteria = 'form_name';
            }

            $form_query = DB::select('id','action','class','form_id','method','email_all_fields','captcha_enabled','captcha_version','use_stripe','fields','options')
                ->from('plugin_formbuilder_forms')
                ->where_open()
                ->where('deleted','=',0)
                ->or_where('deleted', '=', NULL)
                ->where_close()
                ->and_where($search_criteria,'=',$id)
                ->limit(1)->execute()->as_array();
            $form = $form_query[0];

            if(count($form_query) == 0)
            {
                throw new Exception("Invalid Form ID");
            }

            Session::instance()->set('form_id',$form['id']);

            $form_header = '';
            if ($add_form_tag) {
                $form_class = !empty($form['class']) ? ' class="' . $form['class'] . '" ': '';
                $form_header = '<form action="' . $form['action'] . '" method="' . $form['method'] . '" id="' . IbForm::string_to_id($form['form_id']) . '" ' . $form_class . 'enctype="multipart/form-data">';
            }
            $hidden_fields = '<input type="hidden" name="formbuilder_id" value="'.$id.'" />';
            if ( ! empty($form['options']))
            {
                $options = explode('|',$form['options']);
                foreach($options AS $option)
                {
                    $parameters = explode(":",$option);
                    $hidden_fields.='<input type="hidden" name="'.$parameters[0].'" value="'.$parameters[1].'"/>';
                }
            }

            $form_body = $form['fields'];

			// Translate content in tags
			preg_match_all('#<(button|label|legend|option)(.*?)>(.*?)</\1>#s', $form_body, $matches);
			if (isset($matches[3])) // 3 = the (.*?) in the above regex
			{
				$find = array();
				$replace = array();
				foreach ($matches[3] as $match)
				{
					$find[] = '>'.$match.'<';
					$replace[] = '>'.html_entity_decode((htmlentities(__($match), NULL, 'UTF-8'))).'<';
				}
				$form_body = str_replace($find, $replace, $form_body);
			}

			// Translate placeholder text
			preg_match_all('#placeholder="(.*?)"#', $form_body, $matches);
			if (isset($matches[1])) // 1 = the (.*?) in the above regex
			{
				$find = array();
				$replace = array();
				foreach ($matches[1] as $match)
				{
					$find[] = 'placeholder="'.$match.'"';
					$replace[] = 'placeholder="'.htmlentities(__($match), NULL, 'UTF-8').'"';
				}
				$form_body = str_replace($find, $replace, $form_body);
			}

            $split_index    = strpos($form_body, '<li');
            $hidden_fields .= substr($form_body, 0, $split_index);
            $form_body      = '<'.$list_order.'>'.substr($form_body, $split_index).'</'.$list_order.'>';

            if($form['captcha_enabled'] == 1)
            {
                require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                $captcha_public_key = Settings::instance()->get('captcha_public_key');
                $catpcha_html = recaptcha_get_html($captcha_public_key, null, $form['captcha_version']);
                if (strpos($form_body, '<span>[CAPTCHA]</span>') !== false) {
                    $form_body = str_replace('<span>[CAPTCHA]</span>', $catpcha_html, $form_body);
                } else {
                    $form_body = $catpcha_html . $form_body;
                }

                $form_body = str_replace('<li></li>', '', $form_body);
            }

			$stripe_enabled = ($form['use_stripe'] AND Settings::instance()->get('stripe_enabled') == 'TRUE');

			if ($stripe_enabled)
			{
				// If Stripe has been enabled, add a stripe button
				$stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
				$stripe_publishable_key = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
				$stripe_publishable_key = trim($stripe_publishable_key);

				require_once APPPATH.'/vendor/stripe/lib/Stripe.php';
				$stripe_secret_key = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
				Stripe::setApiKey(trim($stripe_secret_key));

				$form_body .= '<div id="stripeButton">
							<button type="button" class="stripe-button" id="stripe-button" data-key="'.$stripe_publishable_key.'">
								<span>'.htmlentities(__('Pay Now'), NULL, 'UTF-8').'</span>
							</button>
						</div>';
                $form_body .= View::factory('form_stripe_inputs')->render();

				// This needs to go before payments.js
				// $form_body .= '<script src="//checkout.stripe.com/checkout.js"></script>';
			}
			else
			{
				// If stripe has not been enabled remove other stripe elements, such as payment type selector and its label
				$dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->strictErrorChecking = false;
				$dom->loadHTML($form_body);
                $xPath = new DOMXPath($dom);
				$nodes = $xPath->query('//*[contains(@id, "_method_stripe") or contains(@for, "_method_stripe")]');

				foreach ($nodes as $node)
				{
					$node->parentNode->removeChild($node);
				}
				$form_body = $dom->saveHTML();
			}

            $form_footer = '';
            if ($form_header) {
                $form_footer = '</form>';
            }
            $datepicker_script = '
            <script>$(document).ready(function(){
                if(jQuery().datepicker)
                {
                $(".datepicker").datepicker();
                }
                });
                </script>';
            $finished_form = $form_header.$hidden_fields.$form_body.$form_footer.$datepicker_script;
            return $finished_form;
        }
        catch(Exception $e)
        {
            return "Couldn't create form. ".$e->getMessage();
        }
    }

    public function compare_forms($post)
    {
        if(!isset($this->form))
        {
            return false;
        }

        try{
            $html = new DOMDocument();
            libxml_use_internal_errors(true);
            $html->strictErrorChecking = false;
            $html->loadHTML($this->get_fields($this->form));

            unset($post['failpage']);
            unset($post['redirect']);
            unset($post['recaptcha_challenge_field']);
            unset($post['recaptcha_response_field']);

            $field_array = array();
            $input = $html->getelementsbytagname('input');
            $textarea = $html->getelementsbytagname('textarea');
            $select = $html->getelementsbytagname('select');

            foreach($input as $name)
            {
                if($name->getAttribute('name') != "")
               {
                   $field_array[$name->getAttribute('name')] = $post[$name->getAttribute('name')];
                }
            }

            foreach($textarea as $name)
            {
                if($name->getAttribute('name') != "")
                {
                    $field_array[$name->getAttribute('name')] = $post[$name->getAttribute('name')];
                }
            }

            foreach($select as $name)
            {
                if($name->getAttribute('name') != "")
                {
                    $field_array[$name->getAttribute('name')] = $post[$name->getAttribute('name')];
                }
            }

            asort($field_array);
            asort($post);

            if($field_array == $post)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    private function get_fields($form)
    {
        $query = DB::select('fields')
			->from('plugin_formbuilder_forms')
			->and_where_open()
				->where('deleted','=',0)
				->or_where('deleted', '=', NULL) // todo: update table to set 0 as the default
			->and_where_close()
			->and_where('id','=',$form)
			->limit(1)->execute()->as_array();
        return $query[0]['fields'];
    }

    public static function is_captcha_enabled($form)
    {
        $check_column = is_numeric($form) ? 'id' : 'form_name';

        $query = DB::select('captcha_enabled')
			->from('plugin_formbuilder_forms')
			->and_where_open()
				->where('deleted','=',0)
				->or_where('deleted', '=', NULL) // todo: update table to set 0 as the default
			->and_where_close()
			->and_where($check_column, '=', $form)
			->limit(1)->execute()->as_array();
        return ($query[0]['captcha_enabled'] == 1) ? TRUE : FALSE;
    }

    public function get_current_form()
    {
        return isset($this->form) ? $this->form : 0;
    }

    public function load()
    {
        return array(
            'id'               => $this->id,
            'form_name'        => $this->form_name,
            'form_id'          => $this->form_id,
            'action'           => $this->action,
            'method'           => $this->method,
            'fields'           => trim($this->fields),
            'options'          => $this->options,
            'summary'          => $this->summary,
            'email_all_fields' => $this->email_all_fields,
            'captcha_enabled'  => $this->captcha_enabled,
            'captcha_version'  => $this->captcha_version,
            'use_stripe'       => $this->use_stripe,
            'publish'          => intval($this->publish)
        );
    }

    public function set($data)
    {
        foreach($data AS $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }

        $this->publish = ($this->publish == 1) ? 1 : 0;
    }

    public static function get_form_data($form_id)
    {
        if(is_numeric($form_id))
        {
            $where = 'id';
        }
        else
        {
            $where = 'form_name';
        }
        $q = DB::select('id','form_name','form_id','action','method','fields','options','summary','email_all_fields','captcha_enabled','captcha_version','use_stripe','publish')->from(self::form_table)->where($where,'=',$form_id)->execute()->as_array();
        return $q[0];
    }

    public function get_options_as_array()
    {
        if($this->options == NULL)
        {
            return NULL;
        }

        $options = explode('|',$this->options);
        $result = array();

        foreach($options AS $option)
        {
            $parts = explode(':',$option);
            $result[$parts[0]] = $parts[1];
        }

        return $result;
    }

    public function get_next_id()
    {
        $q = DB::query(Database::SELECT,"SELECT MAX(id) AS `id` FROM ".self::form_table." LIMIT 1")->execute()->as_array();
        return ++$q[0]['id'];
    }

    public function set_action($action)
    {
        $this->action = $action;
    }

    public static function render($id)
    {
        $form = new Model_Formbuilder($id);
        return $form->present_form($id);
    }

	// Get content to be translated
	public static function get_localisation_messages()
	{
		$forms = DB::select('fields')->from(self::form_table)->where('deleted', '=', 0)->or_where('deleted', '=', NULL)->execute()->as_array();
		$messages = array();
		foreach ($forms as $form)
		{
			// Get content inside button, label, legend and option tags
			$tag_regex = '#<(button|label|legend|option)(.*?)>(.+?)</\1>#s';
			preg_match_all($tag_regex, $form['fields'], $matches);
			if (isset($matches[3])) // 3 = the (.+?) in the above regex
			{
				$messages = array_merge($messages, $matches[3]);
			}

			// Get placeholder text
			$placeholder_regex = '#placeholder="(.+?)"#';
			preg_match_all($placeholder_regex, $form['fields'], $matches);
			if (isset($matches[1])) // 1 = the (.+?) in the above regex
			{
				$messages = array_merge($messages, $matches[1]);
			}
		}

		return $messages;
	}

}