<?php defined('SYSPATH') or die('No direct script access.');

class Model_Settings extends Model
{
    const TABLE_SETTINGS = 'engine_settings';
    const TABLE_MICROSITE_OVERWRITES = 'engine_settings_microsite_overwrites';

    // The present server environment
    protected $_environment;
    protected $_microsite_id;

    public function __construct()
    {

        // Set the environment. Tis is used to save and read to the correct row in the DB
        $environment = Kohana::$environment;

        if ($environment == Kohana::PRODUCTION) // Live
        {
            $this->_environment = 'live';
        } elseif ($environment == Kohana::STAGING) // Stage
        {
            $this->_environment = 'stage';
        } elseif ($environment == Kohana::TESTING) // Test
        {
            $this->_environment = 'test';
        } else // dev
        {
            $this->_environment = 'dev';
        }

        $config = Kohana::$config->load('config');
        $this->_microsite_id = (!empty($config->microsite_id)) ? $config->microsite_id : false;
    }

    /**
     * Loads the settings from the database
     */
    private function load($plugin_name = NULL, $group = NULL)
    {

        $query = DB::select()
            ->order_by('group')
            ->order_by('id')
            ->from('engine_settings');

        if ( ! is_null($plugin_name))
        {
            $q = DB::select('settings_group')->from('plugin_settings_relationship')->where('plugin_name','=',$plugin_name)->execute()->as_array();
            if(count($q) > 0)
            {
                $query->where('group','=',$q[0]['settings_group']);
            }
        }

		if ( ! is_null($group))
		{
			if (is_array($group))
			{
				$query->where('group', 'in', $group);
			}
			else
			{
				$query->where('group', '=', $group);
			}
		}

        $result = $query->execute();

        return $result;
    }

    public function get_microsite_overwrites()
    {
        $return = [];
        if ($this->_microsite_id) {
            $settings = DB::select()
                ->from(self::TABLE_MICROSITE_OVERWRITES)
                ->where('microsite_id', '=', $this->_microsite_id)
                ->where('environment', '=', $this->_environment)
                ->execute()
                ->as_array();

            foreach ($settings as $setting) {
                $return[$setting['setting']] = $setting['value'];
            }
        }

        return $return;
    }

    public function insert_microsite_overwrite($setting, $value)
    {
        if ($this->_microsite_id) {
            $value = is_array($value) ? serialize($value) : $value;

            return DB::insert(self::TABLE_MICROSITE_OVERWRITES)
                ->values([
                    'setting'      => $setting,
                    'microsite_id' => $this->_microsite_id,
                    'environment'  => $this->_environment,
                    'value'        => $value
                ])
                ->execute();
        } else {
            return false;
        }
    }

    public function update_microsite_overwrite($setting, $value)
    {
        if ($this->_microsite_id) {
            $value = is_array($value) ? serialize($value) : $value;

            return DB::update(self::TABLE_MICROSITE_OVERWRITES)
                ->set(['value' => $value])
                ->where('setting',      '=', $setting)
                ->where('microsite_id', '=', $this->_microsite_id)
                ->where('environment',  '=', $this->_environment)
                ->execute();
        } else {
            return false;
        }
    }

    public function delete_microsite_overwrite($setting)
    {
        if ($this->_microsite_id) {
            return DB::delete(self::TABLE_MICROSITE_OVERWRITES)
                ->where('setting',      '=', $setting)
                ->where('microsite_id', '=', $this->_microsite_id)
                ->where('environment',  '=', $this->_environment)
                ->execute();

        } else {
            return false;
        }
    }

    /**
     * Save any changed settings back into the database
     */
    public function update($settings)
    {
        $old_settings = $this->load();

		// Get the correct value row (based on the environment)
		$value = 'value_'.$this->_environment;

        // Microsites share settings with the main site.
        // But can also overwrite a select number of these settings at site-level
        // The following code is to save the overwritten values to a separate table
        if ($this->_microsite_id) {
            $overwrite_names = isset($settings['overwrites']) ? $settings['overwrites'] : [];
            $old_overwrites = $this->get_microsite_overwrites();

            foreach ($old_overwrites as $setting_name => $setting_value) {
                if (isset($settings[$setting_name]) && !in_array($setting_name, $overwrite_names)) {
                    // Was overwritten before, is not overwritten now
                    self::delete_microsite_overwrite($setting_name);
                    $alert = true;
                }
            }

            foreach ($overwrite_names as $setting_name) {
                if (!isset($old_overwrites[$setting_name])) {
                    // Was not being overwritten before
                    self::insert_microsite_overwrite($setting_name, $settings[$setting_name]);
                    $alert = true;
                }
                else if ($old_overwrites[$setting_name] !== $settings[$setting_name]) {
                    // Was overwritten before, still overwritten, but value has changed
                    self::update_microsite_overwrite($setting_name, $settings[$setting_name]);
                    $alert = true;
                }

                // Prevent this from being saved to the main "shared" settings table
                unset($settings[$setting_name]);
            }
        }

        foreach ($old_settings as $old_setting)
		{
            // Get the correct value based on the environment
            $old_setting['value'] = $this->env_set($old_setting);

            // If the variable is an array, serialize it (this is used by multiselects)
            $new_value = $settings[$old_setting['variable']];
            $new_value = is_array($new_value) ? serialize($new_value) : $new_value;

            // Check if the variable from the DB is in the post and if they differ. Old Setting is serialized already if multiselect.
            if ((isset($settings[$old_setting['variable']])) && $new_value !== $old_setting['value'])
			{
				// Update the Setting in the DB
                // this could be improved to execute all the updates together at the end in one call
                // rather than executing them one by one
                DB::update('engine_settings')
                    ->set(array($value => $new_value))
                    ->where('variable', '=', $old_setting['variable'])
					->execute();

                $activity = new Model_Activity();
                $activity->set_item_id($old_setting['id']);
                $activity->set_item_type('settings');
                $activity->set_action('update');
                $activity->save();
                $alert = TRUE;
            }
        }

        if (isset($alert)) {
            return IbHelpers::alert('Settings updated.', 'success');
        }

    }

    /**
     * Build the HTML for the settings form
     */
    public function build_form($plugin_name = null, $group = NULL)
    {
        $user     = Auth::instance()->get_user();
        $plugins  = DB::select('p.name')
            ->from(array('engine_plugins','p'))
            ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')->on('p.name', '=', 'resources.alias')
            ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permission'), 'inner')->on('resources.id', '=', 'has_permission.resource_id')
            ->where('has_permission.role_id','=',$user['role_id'])
            ->execute()
            ->as_array();

        $loaded = array();
        foreach ($plugins as $key=>$plugin)
        {
            $loaded[] = $plugin['name'];
        }
        // Be sure to only profile if it's enabled
        if (Kohana::$profiling === TRUE) {
            // Start a new benchmark
            $benchmark = Profiler::start('Settings Builder', __FUNCTION__);
        }

        $settings = $this->load($plugin_name, $group);
        $formatted = array();
        $config = Kohana::$config->load('config');

        $args = [
            'is_microsite' => (!empty($config->microsite_id)),
            'overwrites' => $this->get_microsite_overwrites()
        ];

        // Based on the environment set the value variable
        foreach ($settings as $setting) {

            if ($setting['linked_plugin_name'] == '' OR in_array($setting['linked_plugin_name'],$loaded)) {
                // Get the correct value based on the environment
                $setting['value'] = $this->env_set($setting);

                switch ($setting['type']) {
                    case 'text':
                    case 'password':
                    case 'textarea':
                    case 'checkbox':
                    case 'wysiwyg':
                    case 'date':
                    case 'datetime':
                    case 'html_editor':
                        $formatted[$setting['group']][] = Form::cms($setting['type'], $setting, $args);
                        break;

                    case 'select':
                    case 'multiselect':
                    case 'combobox':
                        if (!is_null(json_decode($setting['options']))) {
                            $setting['options'] = json_decode($setting['options'], true);
                        }
                        else if (is_string($setting['options'])) {
                            $parameter = (Settings::instance()->get($setting['variable']) != "SELECT") ? Settings::instance()->get($setting['variable']) : null;
                            $function = explode(',', $setting['options']);
                            //array functions: element 0 = Model Name, element 1 = Function Name
                            //this function has changed over time, requiring the version check - in spite of PHP being... "portable".
                            //we're checking to see if the function exists as an exception will be thrown by call_user_func if the user cannot access this.
                            if (version_compare(phpversion(), '5.2.3', '<')) {
                                $options = (method_exists($function[0], $function[1]))
                                    ? call_user_func(array($function[0], $function[1], $parameter))
                                    : '<option value="">NOT APPLICABLE</option>';
                            } else {
                                $options = (method_exists($function[0], $function[1]))
                                    ? call_user_func($function[0] . '::' . $function[1], $parameter)
                                    : '<option value="">NOT APPLICABLE</option>';
                            }
                            $setting['options'] = $options;
                        }
                        $formatted[$setting['group']][] = Form::cms($setting['type'], $setting, $args);
                        break;

                    case 'dropdown':
                        // Check if there are options encoded as JSON for the dropdown form the DB
                        if ( ! is_null(json_decode($setting['options']))) {
                            $setting['options'] = json_decode($setting['options'], true);
                        } // If not JSON check if it a valid method call
                        elseif (method_exists($this, $setting['options'])) {
                            //execute the method and use the return as the options array
                            $setting['options'] = $this->$setting['options']();
                        } // If neither of the above are true return nothing
                        else {
                            $setting['options'] = null;
                        }

                        $formatted[$setting['group']][] = Form::cms('dropdown', $setting, $args);
                        break;

                    case 'toggle_button':
                        if (is_string($setting['options'])) {
                            $parameter = (Settings::instance()->get($setting['variable']) != "SELECT") ? Settings::instance()->get($setting['variable']) : null;
                            $function = explode(',', $setting['options']);
                            if (version_compare(phpversion(), '5.2.3', '<')) {
                                $options = (method_exists($function[0], $function[1])) ? call_user_func(array(
                                    $function[0],
                                    $function[1],
                                    $parameter
                                )) : array();
                            } else {
                                $options = (method_exists($function[0],
                                    $function[1])) ? call_user_func($function[0] . '::' . $function[1],
                                    $parameter) : array();
                            }
                            $setting['options'] = $options;
                        }
                        $formatted[$setting['group']][] = Form::cms('toggle_button', $setting, $args);
                        break;

                    case 'color_picker':
                        $formatted[$setting['group']][] = Form::cms('color_picker', $setting, $args);
                        break;
                }
            }
        }

        if (isset($benchmark)) {
            // Stop the benchmark
            Profiler::stop($benchmark);
        }

        return $formatted;
    }

    private function env_set($setting)
    {

        $value = 'value_' . $this->_environment;

        $setting['value'] = $setting[$value];

        return $setting['value'];

    }

    public static function get_currency_list($current)
    {
        $currency = array("AUS", "CAD", "EUR", "STG", "USD");
        $labels   = array("AUS" => "$ AUS", "CAD" => "$ CAD", "EUR" => "&euro; EUR", "STG" => "£ GPD", "USD" => "$ USD");
        $return = "";
        foreach ($currency AS $money) {
            $selected = "";
            if ($current == $money) {
                $selected = ' selected="selected"';
            }
            $return .= '<option value="'.$money.'"'.$selected.'>'.$labels[$money].'</option>';
        }
        return $return;

    }

	public static function get_date_formats($current)
	{
		$formats = array(
			'd/m/Y' => 'DD/MM/YYYY',
			'm/d/Y' => 'MM/DD/YYYY',
			'Y-m-d' => 'YYYY-MM-DD',
            'd-m-Y' => 'DD-MM-YYYY'
		);
		$return = '<option value="">-- Please Select --</option>';
		foreach ($formats as $key => $value)
		{
			$selected = ($key == $current) ? ' selected="selected"' : '';
			$return .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
		return $return;
	}

    public static function left_or_right($current)
    {
        $options = array('left', 'right');
        $return = '';
        foreach ($options AS $option) {
            $selected = '';
            if ($option == $current) {
                $selected = ' selected="selected"';
            }
            $return .= '<option value="'.$option.'"'.$selected.'>'.ucfirst($option).'</option>';
        }
        return $return;
    }

    public static function column_toggle()
    {
        return array('none' => 'None', '2_col' => '2 Columns' , '3_col' => '3 Columns');
    }

    public static function modern_style_column_display($current)
    {
        $options = Model_Settings::column_toggle();
        $return = '';
        foreach ($options as $key=>$option)
        {
            $selected = '';
            if ($key == $current) {
                $selected = ' selected="selected"';
            }
            $return .= '<option value="'.$key.'""'.$selected.'>'.$option.'</option>';
        }
        return $return;
    }

	public static function cms_theme_options($current)
	{
		$options = array('default' => 'Standard', 'modern' => 'Modern');
		$return = '';
		foreach ($options AS $key=>$value) {
			$selected = '';
			if ($key == $current) {
				$selected = ' selected="selected"';
			}
			$return .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
		return $return;
	}

	public static function cms_skin_options($current, $array = false)
	{
        $options = array(
            '01'     => '01',
            '02'     => '02',
            'bcfe'   => 'BCFE',
            'black'  => 'Black (uTicket)',
            'ccdemo' => 'CourseCo Demo',
            'donate' => 'Donate',
            'ibec'   => 'Ibec',
            'icse'   => 'ICSE',
            'itt'    => 'Irish Times Training',
            'kes'    => 'KES',
            'lsm'    => 'LSM',
            'marine' => 'Marine',
            'pac'    => 'PAC',
            'pink'   => 'Pink',
            'sls'    => 'SLS',
            'vw'     => 'Voiceworks',
            'wine'   => 'Wine (Brookfield College)',
        );

        if ($array) {
            return $options;
        }

        $return = '';
		foreach ($options AS $key => $option) {
			$selected = '';
			if ($key == $current) {
				$selected = ' selected="selected"';
			}
			$return .= '<option value="'.$key.'"'.$selected.'>'.$option.'</option>';
		}
		return $return;
	}
    
    public static function on_or_off()
    {
        return array(
            array('value' => '1', 'label' => 'On'),
            array('value' => '0', 'label' => 'Off')
        );
    }

	public static function get_horizontal_positions_options()
	{
		return array(
			array('value' => 'left',   'label' => 'Left'),
			array('value' => 'center', 'label' => 'Centre'),
			array('value' => 'right',  'label' => 'Right')
		);
	}

	public static function get_vertical_positions_options()
	{
		return array(
			array('value' => 'top',    'label' => 'Top'),
			array('value' => 'center', 'label' => 'Centre'),
			array('value' => 'bottom', 'label' => 'Bottom')
		);
	}

	public static function length_units()
	{
		return array(
			array('value' => 'mm', 'label' => 'Millimetres'),
			array('value' => 'cm', 'label' => 'Centimetres'),
			array('value' => 'm',  'label' => 'Metres'),
			array('value' => 'in', 'label' => 'Inches'),
			array('value' => 'ft', 'label' => 'Feet')
		);
	}

    public static function course_display_options($current) {
        $displays = array('All Dates','Next Date Only','Next 7 Days','Next 30 Days','Next 90 Days','Next 365 Days');
        $return = '<option value="">-- Please Select --</option>';
        foreach ($displays as $key=>$value) {
            $selected = ($key == $current) ? ' selected="selected"' : '';
            $return .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
        }
        return $return;
    }
    
    public static function get_filter_contact_types($current)
    {
        $types = Model_Contacts3::get_types();
        $options = '';
        foreach ($types as $id => $type) {
            $selected = false;
            if (is_array($current)) {
                $selected = in_array($id, $current);
            } else {
                $selected = $current == $id;
            }
            $options .= '<option value="' . $type['contact_type_id'] . '" . ' . ($selected ? 'selected="selected"' : '') . '>' . $type['display_name'] . '</option>';
        }
        return $options;
    }
    
    public static function get_currency_formats($current)
    {
        $formats = array(
            'en_IE.utf-8' => 'Euro Ireland',
            'en_GB.utf-8' => 'UK Pounds'
        );
        $return = '<option value="">-- Please Select --</option>';
        foreach ($formats as $key => $value)
        {
            $selected = ($key == $current) ? ' selected="selected"' : '';
            if ($selected)
            {
                setlocale(LC_MONETARY, $key);
            }
            $return .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
        }
        return $return;
    }

	public static function get_site_templates_as_options($current)
	{
	    // find the repo/folder/project name from the script file
        $project_name = rtrim(substr(strstr(PROJECTPATH, 'projects'), 9), '\\');
        $project_name = !empty($project_name) ? "$project_name - " : "";
        $options = DB::select()
			->from('engine_site_templates')
			->where('type', '=', 'website')
			->and_where('deleted', '=', 0)
			// Put "default" first, then order alphabetically
			->order_by(DB::expr("CASE WHEN `stub` = 'default' THEN 0 ELSE 1 END"))
			->order_by('title')
			->execute()
			->as_array();
		$return = '<option value="">-- None selected --</option>';
		foreach ($options as $option)
		{
		    // if the template name already has the folder name on it, don't add it
		    $template_name = (strpos($option['title'], '-')) ? $option['title'] : ucfirst("{$project_name}{$option['title']}");
			$selected = ($option['stub'] == $current) ? ' selected="selected"' : '';
			$return  .= "<option data-id='{$option['id']}' value='{$option['stub']}' $selected >$template_name</option>";
		}
		return $return;
	}

	/**
	 * @param      $current - the currently selected theme. If this is a multiselect, pass in an array of strings. Otherwise pass in a string.
	 * @param bool $filter_by_enabled - only list themes, which have been enabled in the settings
	 * @param bool $filter_by_template - only list themes, corresponding to the
	 * @return string HTML option tags
	 */
	public static function get_site_themes_as_options($current, $filter_by_enabled = FALSE, $filter_by_template = FALSE)
	{
		$multiple = (is_array($current));

		$query = DB::select('theme.id', 'theme.title', 'theme.stub', array('template.stub', 'template_stub'))
			->from(array('engine_site_themes', 'theme'))
			->join(array('engine_site_templates', 'template'))->on('theme.template_id', '=', 'template.id')
			->where('theme.deleted', '=', 0)
			// Put "default" first, then order alphabetically
			->order_by(DB::expr("CASE WHEN `theme`.`stub` = 'default' THEN 0 ELSE 1 END"))
			->order_by('theme.title');

		// Only list enabled themes
		if ($filter_by_enabled && false) // all themes should be enabled by default.
		{
			$available_themes = Settings::instance()->get('available_themes');
			if ($available_themes != FALSE AND count($available_themes) > 0)
			{
				$query->where('theme.title', 'in', Settings::instance()->get('available_themes'));
			}
		}

		// Only list themes for the selected template
		if ($filter_by_template)
		{
			$query->where('template.stub', '=', Settings::instance()->get('template_folder_path'));
		}

		$options = $query->execute()->as_array();
		$return = ($multiple) ? '' : '<option value="">-- None selected --</option>';
		foreach ($options as $option)
		{
			if ($multiple)
			{
				$selected = (in_array($option['stub'], $current)) ? ' selected="selected"' : '';
				$selected = (count($current) == 0) ? ' selected="selected"' : $selected; // select all
			}
			else
			{
				$selected = ($option['stub'] == $current) ? ' selected="selected"' : '';
			}
			$return  .= '<option value="'.$option['stub'].'" data-template="'.$option['template_stub'].'"'.$selected.'>'.$option['title'].'</option>';
		}
		return $return;
	}

    public static function get_browsers()
    {
        return array(
            1 => 'Google Chrome',
            2 => 'Mozilla Firefox',
            3 => 'Safari',
            4 => 'Internet Explorer',
            5 => 'Microsoft Edge',
            6 => 'Opera',
            0 => 'None (Select only this option if you support all browsers)'
        );
    }

    public static function get_recommended_browser($selected_browser)
    {
        $browsers = self::get_browsers();
        $return = '<option value="">-- None Selected -- </option>';
        foreach ($browsers as $browser_id => $browser_name) {
            $selected = ($selected_browser == $browser_id) ? ' selected="selected"' : '';
            $return .= '<option value="' . $browser_id . '"' . $selected . '>' . $browser_name . '</option>';
        }
        return $return;
    }

    public static function get_unsupported_browser_options(
        $current,
        $filter_by_enabled = false,
        $filter_by_template = false
    )
    {
        $browsers = self::get_browsers();
        $options = '';
        foreach ($browsers as $browser_id => $browser_name) {
            $selected = false;
            if (is_array($current)) {
                $selected = in_array($browser_id, $current);
            } else {
                $selected = $current == $browser_name;
            }
            $options .= '<option value="' . $browser_id . '" . ' . ($selected ? 'selected="selected"' : '') . '>' . $browser_name . '</option>';
        }
        return $options;
    }

    public function import($values)
    {
        try {
            Database::instance()->begin();
            foreach ($values as $variable => $value) {
                DB::update(self::TABLE_SETTINGS)
                    ->set(
                        array(
                            'value_' . $this->_environment => is_array($value) ? serialize($value) : $value
                        )
                    )
                    ->where('variable', '=', $variable)
                    ->execute();
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}
