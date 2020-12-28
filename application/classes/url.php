<?php defined('SYSPATH') or die('No direct script access.');
/**
 * URL helper class.
 *
 * @package    Kohana
 * @category   Helpers
 * @license    http://kohanaframework.org/license
 */
class URL extends Kohana_URL
{
    /**
     * @return string The static domain for the project depending on the environment. This value can be found in
     * the config folder of each project.
     */
    public static function get_static_domain()
    {
        static $config = null;
        if ($config == null) {
            $config = Kohana::$config->load('config');
        }
		try{
	        $static_assets = $config->static_assets;
		}catch(Exception $exc){
			$static_assets = false;
		}
		if($static_assets){
			switch (Kohana::$environment)
			{
				case Kohana::PRODUCTION:
					$domain = $static_assets['PRODUCTION'];
					break;
	
				case Kohana::STAGING:
					$domain = $static_assets['STAGING'];
					break;
	
				case Kohana::TESTING:
					$domain = $static_assets['TESTING'];
					break;
	
				case Kohana::DEVELOPMENT:
				default:
					$domain = $static_assets['DEVELOPMENT'];
					break;
			}
			$domain = 'static.' . $domain;
		} else {
			$domain = $_SERVER['HTTP_HOST'];
		}

        return $domain;
    }

    /**
     * Not working with domains like co.uk in this case just return uk
     *
     * @return string domain extension in lower case
     *
     * @example com | ie | uk
     *
     */
    public static function get_domain_extension()
    {
        try{
            $domain = url::get_static_domain();
            $pos = strripos($domain, '.');

            $extension = substr($domain, $pos +1);
            return strtolower($extension);
        }
        catch(Exception $e){
            return '';
        }

    }

    /**
     * @return string An string like static.www.mywebsite.com/
     */
    public static function get_project_assets_base()
    {
        return '//' . URL::get_static_domain() . '/';
    }

    /**
     * @return string An string like static.www.mywebsite.com/plugins/<plugin_name>
     */
    public static function get_project_plugin_assets_base($plugin)
    {
        return '//' . URL::get_static_domain() . '/plugins/' . $plugin . '/';
    }

    /**
     * @return string A string like static.www.mywebsite.com/engine/shared/
     */
    public static function get_engine_assets_base()
    {
        return '//'.URL::get_static_domain().'/engine/shared/';
    }

    /**
     * @return string A string like static.www.mywebsite.com/engine/skin
     * where "skin" is replaced with the skin folder name
     */
    public static function get_engine_theme_assets_base()
    {
        $skin = isset($_GET['usetheme']) ? $_GET['usetheme'] : '';
        if (!$skin) {
            $skin = Settings::instance()->get('cms_skin');
        }

        return '//'.URL::get_static_domain().'/engine/'.$skin.'/';
    }

    /**
     * @return string A string like static.www.mywebsite.com/engine/plugins/<plugin_name>
     */
    public static function get_engine_plugin_assets_base($plugin)
    {
        return '//' . URL::get_static_domain() . '/engine/plugins/' . $plugin . '/';
    }

    /**
     * @return string A string like static.www.mywebsite.com/engine/plugins/<plugin_name>
     */
    public static function get_engine_plugin_asset($plugin = '', $file = '', $args = [])
    {
        $code_path = ENGINEPATH.'plugins/'.$plugin.'/development/assets/'.$file;
        $url_path = '//' . URL::get_static_domain() . '/engine/plugins/' . $plugin . '/'.$file;

        $return = $url_path;

        if (!empty($args['cachebust'])) {
            $return .= '?cb='.filemtime($code_path);
        }

        if (!empty($args['script_tags'])) {
            $return = '<script type="text/javascript" src="'.$return.'"></script>';
        } else if (!empty($args['link_tag'])) {
            $return = '<link rel="stylesheet" href="'.$return.'" />';
        }

        return $return;
    }

    /**
     * Function check if required asset (image, etc) exists in project folder
     * and if yes, then it will use it. It allows to overwrite engine assets with
     * client specific versions.
     */
    public static function overload_asset($filename, $args = [])
    {
        $config      = Kohana::$config->load('config');
        $assets_path = isset($config->assets_folder_path) ? $config->assets_folder_path : '';
        $cms_skin    = Settings::instance()->get('cms_skin');

        // Check a template site's theme folder first
        if ($assets_path AND file_exists(DOCROOT.'assets/'.$assets_path.'/'.$filename)) {
            $filepath = DOCROOT.'assets/'.$assets_path.'/'.$filename;
            $return   = URL::site().'assets/'.$assets_path.'/'.$filename;
        }
        // Check a template site's shared folder next
        elseif (file_exists(DOCROOT.'assets/shared/'.$filename)) {
            $filepath = DOCROOT.'assets/shared/'.$filename;
            $return   = URL::site().'assets/'.$assets_path.'/'.$filename;
        }
        // Check a custom project's assets folder
        elseif (file_exists(DOCROOT.'assets/'.$filename)) {
            $filepath = DOCROOT.'assets/'.$filename;
            $return   = URL::site().'assets/'.$filename;
        }
        // Check engine theme folder
        elseif (file_exists(ENGINEPATH.'application/assets/'.$cms_skin.'/'.$filename)) {
            $filepath = ENGINEPATH.'application/assets/'.$cms_skin.'/'.$filename;
            $return   = '/engine/'.$cms_skin.'/'.$filename;
        }
        // Use the engine shared folder
        else {
            $filepath = APPPATH.'assets';
            $return   = URL::get_engine_assets_base().$filename;
        }

        if (!empty($args['cachebust'])) {
            $return .= '?cb='.filemtime($filepath);
        }

        return $return;
    }

    /**
     * Get a custom media url
     */
    public static function media($folder = 'media', $protocol = NULL)
    {
        // Start with the configured base URL
        $media_url = Settings::instance()->get('media_url');

        // If no media_url was configured fall back to using the base URL
        if (!$media_url)
        {
			// Add the trailing folder slash, if not provided
            return URL::base() . ( (strpos($folder, '/', 0) !== FALSE )? $folder : $folder.'/' );
        }

        if ($protocol === TRUE)
        {
            // Use the initial request to get the protocol
            $protocol = Request::$initial . '://';
        }

        if ($protocol instanceof Request)
        {
            // Use the current protocol
            list($protocol) = explode('/', strtolower($protocol->protocol()));
        }

        if (!$protocol)
        {
            // If no protocol is defined use a protocol independent url
            $protocol = '//';
        }

        if (is_string($protocol))
        {
            if ($port = parse_url($media_url, PHP_URL_PORT))
            {
                // Found a port, make it usable for the URL
                $port = ':' . $port;
            }

            if ($domain = parse_url($media_url, PHP_URL_HOST))
            {
                // Remove everything but the path from the URL
                $media_url = parse_url($media_url, PHP_URL_PATH);
            }
            else
            {
                // Attempt to use HTTP_HOST and fallback to SERVER_NAME
                $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
            }

            // Add the protocol and domain to the base URL
            $media_url = $protocol . $domain . $port . $media_url . '/' . $folder . '/';
        }

        return $media_url;
    }

    /*
     * This function will figure out the app version and build a correct url for the media.
     *
     * At the moment it is just hardcoded. TODO
     */
    /*public static function admin_assets() {

        // Load the correct values baseed on the environment

        //Get domains
        /*$domain = Kohana::$config->load('config')->static_assets;


        if (Kohana::$environment === Kohana::PRODUCTION) // Stage
        {
            return '//development.static.'. $domain['PRODUCTION'] .'/';
        }
        if (Kohana::$environment === Kohana::STAGING) // Stage
        {
            return '//development.static.'. $domain['STAGING'] .'/';
        }
        elseif (Kohana::$environment === Kohana::TESTING) // Test
        {
            return '//development.static.'. $domain['TESTING'] .'/';
        }
        else // dev
        {
            return '//development.static.'. $domain['DEVELOPMENT'] .'/';
        }
    }*/

    /**
     * Plugin specific assets are located into plugin folder. For example: wpp/engine/plugins/insurance/assets
     * This function will generate URL to access those assets. VHOST entry is needed:

    <VirtualHost *:80>
    ServerName plugin.websitecms.dev
    ServerAlias *.plugin.websitecms.dev
    VirtualDocumentRoot /Users/peter/Sites/wpp/engine/plugins/%2/%1/assets/

    <Directory "/Users/peter/Sites/wpp/engine/plugins">
    AllowOverride None
    Order allow,deny
    Allow from all
    </Directory>

    </VirtualHost>
     *
     * @static
     * @param $plugin
     * @param string $version
     * @return string
     */
    /*public static function plugin_assets($plugin, $version='development') {
        // Load the correct values baseed on the environment

        //Get domains
        /*$domain = Kohana::$config->load('config')->static_assets;

        if (Kohana::$environment === Kohana::PRODUCTION) // Production
        {
            return "//$version.$plugin.plugin.". $domain['PRODUCTION'] ."/";
        }
        if (Kohana::$environment === Kohana::STAGING) // Stage
        {
            return "//$version.$plugin.plugin.". $domain['STAGING'] ."/";
        }
        elseif (Kohana::$environment === Kohana::TESTING) // Test
        {
            return "//$version.$plugin.plugin.". $domain['TESTING'] ."/";
        }
        else // dev
        {
            return "//$version.$plugin.plugin.". $domain['DEVELOPMENT'] ."/";
        }
    }*/

    /**
     * Returns full url to action within current controller.
     * Example URL::adminaction() - http://technopath.websitecms.dev/admin/technopathsettings/product
     * Example URL::adminaction('edit_product',123) - http://technopath.websitecms.dev/admin/technopathsettings/edit_product/123
     *
     * @static
     * @param null $action
     * @param null $param
     * @return
     */
    public static function adminaction($action = null, $param = null)
    {
        if (!$action)
        {
            $action = Request::initial()->action();
        }

        return URL::Site('admin/' . Request::initial()->controller() . '/' . $action . '/' . $param);
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 40px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public static function get_gravatar($email, $size = 40, $default = 'mm', $rating = 'g', $img = FALSE, $secure = FALSE, $atts = array())
    {
        $protocol = true;
        if ($protocol === TRUE)
        {
            // Use the initial request to get the protocol
            $protocol = Request::$initial;
        }

        if ($protocol instanceof Request)
        {
            // Use the current protocol
            list($protocol) = explode('/', strtolower($protocol->protocol()));
        }

        if ( ! $protocol)
        {
            // Use the configured default protocol
            $protocol = parse_url(Kohana::$base_url, PHP_URL_SCHEME);
        }

        if ($secure OR $protocol == 'https')
        {
            $url = '//secure.gravatar.com/avatar/';
        }
        else
        {
            $url = '//www.gravatar.com/avatar/';
        }

        $url .= md5( strtolower( trim( $email ) ) );

        // urlencode the default image
        $default = urlencode( $default );

        // Add the url parameters
        $url .= "?s=$size&d=$default&r=$rating";

        if ( $img )
        {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

	public static function get_avatar($user_id = NULL, $cached = false)
	{
        static $cache = array();
        if ($cached) {
            if (isset($cache[$user_id])) {
                return $cache[$user_id];
            }
        }
		if (is_null($user_id))
		{
			$auth = Auth::instance()->get_user();
			$user_id = $auth['id'];
		}
		$user = new Model_User($user_id);
		// Don't show the user a 404 broken image if they don't have one. Instead give give them a Gravatar one
		if (($user->use_gravatar && Settings::instance()->get('gravatar_enabled') === "1") ||
            Model_Media::get_by_filename($user->avatar,'avatars') == null)
		{
			$image = self::get_gravatar($user->email);
		}
		else
		{
			$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $user->avatar, 'avatars');
			$cache_buster = strtotime($user->date_modified);
			$image = $image_path.'?ts='.$cache_buster;
		}
        if ($cached) {
            $cache[$user_id] = $image;
        }
        return $image;
	}

    /**
     * Extracts URL path from referer and add query and fragment to it.
     * Could be used as internal request url after processing form.
     *
     * referer: http://technopath.websitecms.dev/admin/technopathsettings/edit_product/5?parm=abc#fragm
     * result: get_referer_urlpath() -> admin/technopathsettings/edit_product/5?parm=abc#fragm
     *
     * @static
     * @return void
     */
    public static function get_referer_urlpath()
    {
        return self::get_urlpath($_SERVER['HTTP_REFERER']);
    }

    public static function get_urlpath($url)
    {
        $urlarray = parse_url($url);
        return $urlarray['path']. (empty($urlarray['query'])?'':'?'.$urlarray['query']) . (empty($urlarray['fragment'])?'':'#'.$urlarray['fragment']);
    }

    /** Purpose to return the skin folder for the project under settings table
     * @param null $mode : if set to true then returns FULL URL otherwise returns relative
     * @return string : this is the path to the skins folder
     */
    public static function get_skin_urlpath($full_url = null, $engine_fallback = FALSE)
    {
        // todo: get skin folder from db for this mode
        $path = ($full_url) ? URL::site().'assets/' : '/assets/';

		// If the site is a template, get the skin path
		if (isset(Kohana::$config->load('config')->assets_folder_path))
		{
			$path .= Kohana::$config->load('config')->assets_folder_path.'/';
		}

        // return skin path
        return $path;
    }

    public static function is_internal($url)
    {
        $url = strtolower($url);
        $host = strtolower($_SERVER['HTTP_HOST']);
        if (strpos($url, 'http://' . $host) === 0 || strpos($url, 'https://' . $host) || ($url[0] == '/' && $url[1] != '/')) {
            return true;
        }
        return false;
    }

} // End url
