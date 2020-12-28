<?php
/**
 * Created by Provident CRM.
 * User: itabarino
 * Date: 29/01/15
 * Time: 09:58
 */

class RestAPICacheHelper
{
    private $_cache_path = 'cache/SugarRest/API/';
    private $_extension = '.cache';
    private $_session_id = null;
    private $_rest_url = null;
    private $_rest_user = null;
    private $_rest_password = null;
    private $_default_cache_time = 4; // mins
    private $_default_ignore_list = array('session');

    /**
     * Default Constructor
     *
     * @param String $rest_url          -- Rest API URL
     * @param String $rest_user         -- Rest API Username
     * @param String $rest_md5_password -- Rest API MD5 Password
     */
    public function __construct($rest_url , $rest_user, $rest_md5_password)
    {
        $this->_session_id = @file_get_contents('cache/SugarRest/.sugarcrmsession');
        $this->_rest_url = $rest_url;
        $this->_rest_user = $rest_user;
        $this->_rest_password = $rest_md5_password;

        //create cache path if not exists
        if (!is_dir($this->_cache_path)) {
            mkdir($this->_cache_path, 0755, true);
        }
    }

    /**
     * Public function to call the API method or get data from Cache
     *
     * @param String $method_name -- API method name
     * @param Array  $parameters  -- array with all parameters
     * @param Bool   $cache       -- array with all parameters
     * @param Int    $cache_time  -- array with all parameters
     * @param Array  $cache_parameters_ignore_list  -- array with all parameters to be ignored
     *
     * @return Array return data
     * @throws String Print error message
     */
    public function callAPIMethod($method_name, $parameters, $cache = false, $cache_time = 0 , $cache_parameters_ignore_list = null )
    {
        try{
            //if cache = false return data from API
            if (!$cache) {
                $return_data = $this->_getAPIData($method_name, $parameters);
            } else {

                //define cache time
                if (empty($cache_time) || $cache_time == 0) {
                    $cache_time = $this->_default_cache_time;
                }

                //create cache name
                $cache_name = $this->_createCacheName($method_name, $parameters, $cache_parameters_ignore_list);

                //Try to get cache data
                $return_data = $this->_getCacheData($cache_name);

                //if $return_data is false, cache file doesn't exists or is expired
                if (!$return_data) {
                    //get data from API
                    $return_data = $this->_getAPIData($method_name, $parameters);
                    //create new cache file
                    $this->_createCacheFile($cache_name, $return_data, $cache_time);
                }
            }

            return $return_data;

        } catch (Exception $e) {
            $return_data = array('error' => $e->getMessage());
            return $return_data;
        }
    }

    /**
     * Exec API method using CURL
     *
     * @param String $method_name -- API method name
     * @param Array  $parameters  -- array with all parameters
     *
     * @return Array return data
     * @throws Exception error
     */
    private function _getAPIData($method_name, $parameters, $exec_login = false)
    {
        try {
            //if the call does not have a valid session
            //set the session, and add it to the params array at the start
            if ($method_name != 'login' && $this->_session_id == '') {
                if (! $this->_auth()) {
                    throw new Exception($method_name . ": Login failed");
                }
            }

            if (!empty($this->_session_id)) {
                $session_params = array('session' => $this->_session_id);
                $parameters = array_merge($session_params, $parameters);
            }

            //initialise and configure curl session
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->_rest_url);
            curl_setopt($curl, CURLOPT_POST, true);
            // Tell curl not to return headers, but do return the response
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $post = array(
                "method" => $method_name,
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => json_encode($parameters)
            );

            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

            // set cert options
            if (Settings::instance()->get("sugar_api_cert_validation") == 1) {
                //cert to be used in the call
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            } else {
                //cert not to be used in the call
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            }

            if (curl_error($curl)) {
                throw new Exception(curl_error($curl));
            }

            $response = curl_exec($curl);

            // Close the connection
            curl_close($curl);
            // Convert the result from JSON format to a PHP array
            $result = json_decode($response);

            //exec login again in case of dead session
            if (isset($result->number) && $result->number == '11') {
                if (!$exec_login) {
                    $this->_session_id = '';
                    $result = $this->_getAPIData($method_name, $parameters, true);
                }
            }

            return $result;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Authentication method
     *
     * @return Bool
     * @throws Exception error
     */
    private function _auth()
    {
        try {
            $params = array(
                'user_auth' => array(
                    'user_name' => $this->_rest_user,
                    'password' => $this->_rest_password,
                    "version"=>"1"
                ),
                "application_name"=>"Provident_Sugar_REST",
                "name_value_list"=>array(),
            );

            $res = $this->_getAPIData('login', $params);

            if ($res !== false && isset($res->id)) {
                $this->_session_id = $res->id;

                // check if debug is set so we can gather more info after the call
                if (Settings::instance()->get("debug_mode")==1)
                {
                    Log::instance()->add(Log::DEBUG,"Debugging output of result var from call : res = ".json_encode($res));
                }

                //save cache file
                file_put_contents('cache/SugarRest/.sugarcrmsession', $res->id);
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Create the cache name
     *
     * @param String $method_name -- API method name
     * @param Array  $parameters  -- array with all parameters
     * @param Array  $cache_parameters_ignore_list  -- array with all parameters
     *
     * @return Array return data
     */
    private function _createCacheName($method_name, $parameters, $cache_parameters_ignore_list = null )
    {
        //parsing parameters name
        //remove session id
        unset($parameters['session']);
        //remove ignored parameters
        $ignore_list = array_merge($this->_default_ignore_list, (array)$cache_parameters_ignore_list);
        if (!empty($ignore_list)) {
            foreach ($ignore_list as $ignored) {
                unset($parameters[$ignored]);
            }
        }

        //convert to md5
        $parameters_name = md5(json_encode($parameters));

        //parsing cache name | eg.: get_document_entry_list_fed1d2aa4ab9cd28dd771492a3bc2f97.cache
        $cache_name = $method_name.'_'.$parameters_name.$this->_extension;

        //return cache name
        return strtolower($cache_name);
    }

    /**
     * Verify if the file already exists and if is expired
     *
     * @param String $cache_name -- Cache name
     *
     * @return mixed -- Cache data if valid | false if not valid
     */
    private function _getCacheData($cache_name)
    {
        $cache_dir = $this->_cache_path.$cache_name;

        //verify if file exists
        if (true === file_exists($cache_dir)) {
            $cache_data = file_get_contents($cache_dir);

            $data = json_decode($cache_data);

            //verify if expired
            if ($data->timestamp < time()) {
                return false;
            } else {
                //return cache data
                return $data->data;
            }
        } else {
            return false;
        }
    }

    /**
     * Create the cache name
     *
     * @param String $cache_name
     * @param Array  $data    -- array with all parameters
     * @param Integer $cache_time   -- time in minutes
     *
     * @return Bool
     */
    private function _createCacheFile($cache_name, $data, $cache_time)
    {
        $cache_dir = $this->_cache_path.$cache_name;

        $cache_data = new StdClass();
        $cache_data->timestamp = time()+($cache_time*60);
        $cache_data->data = $data;

        file_put_contents($cache_dir, json_encode($cache_data));

        return true;
    }

}