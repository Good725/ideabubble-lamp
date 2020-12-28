<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Settings loader and updater
 *
 * @category   Settings
 * @author     Diarmuid
 */
class Settings
{

    /**
     * @var  array  list of settings
     */
    protected $_settings = array();

    /**
     * @var  Settings  Singleton instance container
     */
    protected static $_instance;

    /**
     * Get the singleton instance of this class
     *
     *     $settings = Settings::instance();
     *
     * @return  Settings
     */
    public static function instance($location = 'both')
    {
        if (Settings::$_instance === NULL) {
            // Create a new instance
            Settings::$_instance = new Settings;

            // Load the settings into a variable
            Settings::$_instance->load_with_microsite_overwrites($location);
        }

        return Settings::$_instance;
    }

    /**
     * Loads the settings from the database
     */
    private function load($location)
    {

        $query = DB::select('variable', 'value_dev', 'value_test', 'value_stage', 'value_live', 'config_overwrite')
            ->from('engine_settings');

        // For the site only load the necessary settings
        if ($location === 'site') {
            $query->where('location', '=', 'site')->or_where('location', '=', 'both');
        }

        $results = $query->execute();

        $environment = Kohana::$environment;

		// Load the correct values based on the environment
		switch ($environment)
		{
			case Kohana::PRODUCTION: $value_label = 'value_live';  break;
			case Kohana::STAGING:    $value_label = 'value_stage'; break;
			case Kohana::TESTING:    $value_label = 'value_test';  break;
			default:                 $value_label = 'value_dev';   break;
		}

        $config_overwrite = array();
        foreach ($results as $result)
		{
            if ($result['config_overwrite'] == 1) {
                $config_overwrite[$result['variable']] = true;
            }
			if (@unserialize($result[$value_label]) !== FALSE)
			{
				$this->_settings[$result['variable']] = unserialize($result[$value_label]);
			}
			else
			{
				$this->_settings[$result['variable']] = $result[$value_label];
			}
        }

        if (isset($this->_settings['use_config_file']) && $this->_settings['use_config_file'] == 1) {
            $config = Kohana::$config->load('config')->as_array();

            foreach ($config as $variable => $value) {
                if (isset($this->_settings[$variable]) && !@$config_overwrite[$variable]) {
                    $this->_settings[$variable] = $value;
                }
            }
        }

        return $this;
    }

    private function load_with_microsite_overwrites($location)
    {
        $this->load($location);
        $model_settings = new Model_Settings();
        $overwrites = $model_settings->get_microsite_overwrites();

        foreach ($overwrites as $variable => $value) {
            $this->_settings[$variable] = $value;
        }

        return $this;
    }

    /**
     * Get the settings array.
     *
     *     $settings->get();
     *
     * @return  array
     */
    public function get($setting = NULL)
    {
        static $config = null;
        if ($config == null) {
            $config = Kohana::$config->load('config');
        }
        if ($setting)
		{

			$theme = isset($config->assets_folder_path) ? $config->assets_folder_path : '';

			// Check if the setting exists at theme level
			if (isset($this->_settings['theme_'.$theme.':'.$setting]))
			{
				return $this->_settings['theme_'.$theme.':'.$setting];
			}
			// Use the regular setting
            elseif (isset($this->_settings[$setting]))
			{
                return $this->_settings[$setting];
            }
            return FALSE;
        }

        // Write each message into the DB along with some user and session info
        return $this->_settings;
    }

    public function set($setting, $value)
    {
        $environment = Kohana::$environment;

        // Load the correct values based on the environment
        switch ($environment)
        {
            case Kohana::PRODUCTION: $value_label = 'value_live';  break;
            case Kohana::STAGING:    $value_label = 'value_stage'; break;
            case Kohana::TESTING:    $value_label = 'value_test';  break;
            default:                 $value_label = 'value_dev';   break;
        }

        DB::update('engine_settings')
            ->set(
                array($value_label => $value)
            )
        ->where('variable', '=', $setting)
        ->execute();

        $this->_settings[$setting] = $value;
    }


	/**
	 * Get settings that can be used as overwrites for config-file settings
	 */
	public static function get_config_overwrite_settings()
	{
		// Use a different value column in the query, depending on the environment
		switch (Kohana::$environment)
		{
			case Kohana::PRODUCTION : $column = 'value_live';  break;
			case Kohana::STAGING    : $column = 'value_stage'; break;
			case Kohana::TESTING    : $column = 'value_test';  break;
			default                 : $column = 'value_dev';   break;
		}

		$settings = DB::select('variable', array($column, 'value'))
			->from('engine_settings')
			->where('config_overwrite', '=', 1)
			->execute();

		// Format the results
		$return = array();
		foreach ($settings as $setting)
		{
			$return[$setting['variable']] = $setting['value'];
		}

		return $return;
	}

    public static function get_column_toggle_setting()
    {
        $user     = Auth::instance()->get_user();
        $query = DB::select('user_column_profile')->from('engine_users')->where('id','=',$user['id'])->execute()->current();
        $column = $query['user_column_profile'];
        if (is_null($column) OR $column == '')
        {
            $column = Settings::instance()->get('column_toggle');
        }
        // Default to 2 columns if no setting is saved for user or system
        if (is_null($column) OR $column == '')
        {
            $column = '3_col';
        }
        return $column;
    }

    public static function get_google_analytics_script()
    {
        // Removed the misspelled version, after all instances have been updated
        return self::get_google_analitycs_script();
    }

    public static function get_google_analitycs_script()
    {
        $settings = Settings::instance();

        $google_analytics_code = $settings->get('google_analitycs_code');
        $user = Auth::instance()->get_user();

        if (!empty($google_analytics_code)) {
            //This is a google analytics script, I added this directly in a function because I think it wouldn't need to be changed
            //Feel free to move into a view if we need different versions
            $script = "
                <script type='text/javascript'>
                  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

                  ";

            if ($user['id']) {
                $script .= "ga('create', '".$google_analytics_code."', 'auto', {userId: ".$user['id']."});";
            } else {
                $script .= "ga('create', '".$google_analytics_code."', 'auto');";
            }

            $script .= "
                  ga('send', 'pageview');
                </script>";
            return $script;
        } else {
            return '';
        }
    }

} // End Log_Database