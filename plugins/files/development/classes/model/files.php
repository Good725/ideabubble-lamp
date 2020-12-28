<?php defined('SYSPATH') or die('No Direct Script Access.');

/**
 * Class Model_Files
 */
final class Model_Files extends Model
{
    // Tables
    const TABLE_FILE          = 'plugin_files_file';
    const TABLE_VERSION       = 'plugin_files_version';
    const TABLE_USER          = 'engine_users';

    // Maximum Directories/Files Per Directory
    const FILES_PER_DIRECTORY = 10000; /** Warning! DO NOT CHANGE THIS VALUE! **/

    // Base Directory
    const BASE_DIRECTORY      = 'plugin_files';

    // File Types
    const FILE_TYPE_DIRECTORY = 0;
    const FILE_TYPE_REGULAR   = 1;

    // Status
    const STATUS_S_ERROR      = -1;
    const STATUS_S_OK         =  0;

    // Root Directory Name
    const ROOT_DIRECTORY_NAME = 'HOME';

    /*
     *
     * PUBLIC
     *
     */

    //
    // AJAX
    //

    /**
     * @param $directory_id
     * @return null|string
     */
    public static function ajax_list_directory($directory_id)
    {
        $request = Request::$current;

        try
        {
            if ( ! isset($directory_id) OR ! ( is_numeric($directory_id) OR ctype_digit($directory_id) )) /** _sql_get_files will validate the rest of the arguments given in the QUERY string **/
                throw new Model_Files_INVALID_ARGUMENTS_Exception;

            $like   = $request->query('sSearch'       );
            $limit  = $request->query('iDisplayLength');
            $offset = $request->query('iDisplayStart' );

            for ($i = 0, $m = (int) $request->query('iSortingCols'), $order_by = array(); $i < $m; $i++)
            {
                $order_by[] = array($request->query('iSortCol_'.$i) + 1, $request->query('sSortDir_'.$i));
            }

            $rows = array();

            

            if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $directory_id))
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            list($files, $display) = self::_sql_get_files($directory_id, $like, $limit, $offset, $order_by);
            $n_files               = self::_sql_count_files_for_directory($directory_id);

            

            foreach ($files AS $file)
            {
                //check if a file exists to show user
                if ($file["type"] == 1) //if a file and not a folder
                {
                    //get path for current version of this file
                    $file_path = self::_sql_get_active_path($file['id']);
                    $file_full_path = self::_get_base_path() . DIRECTORY_SEPARATOR . $file_path["path"];

                    // check the file system for the file
                    if (file_exists($file_full_path))
                    {
                        $file_present = "1";
                    }
                    else
                    {
                        $file_present = "0";
                    }
                }
                else {
                    //we assume folder exists ;)
                    $file_present = "1";
                }

                $file["present"] = $file_present;

                $rows[] = array_merge(array_values($file), array(''));

            }

            $response = json_encode(array
            (
                'sEcho'                => intval($request->query('sEcho')),
                'iTotalRecords'        => $n_files,
                'iTotalDisplayRecords' => $like == '' ? $n_files : $display,
                'aaData'               => $rows
            ));
        }
        catch (Exception $exception)
        {
            $response = NULL;
        }

        return $response;
    }

    /**
     * @param string $directory_id
     * @return string
     */
    public static function ajax_get_path_breadcrumbs($directory_id)
    {
        return self::_generate_response_object_from_function('self::get_path_breadcrumbs', array($directory_id));
    }

    /**
     * @param string $parent_id
     * @param string $name
     * @return string
     */
    public static function ajax_create_directory($parent_id, $name)
    {
        return self::_generate_response_object_from_function('self::create_directory', array($parent_id, $name), Model_Files_Messages::DIRECTORY_CREATED);
    }

    /**
     * @param string $directory_id
     * @return string
     */
    public static function ajax_remove_directory($directory_id)
    {
        return self::_generate_response_object_from_function('self::remove_directory', array($directory_id), Model_Files_Messages::DIRECTORY_REMOVED);
    }

    /**
     * @param string $file_id
     * @return string
     */
    public static function ajax_remove_file($file_id)
    {
        return self::_generate_response_object_from_function('self::remove_file', array($file_id), Model_Files_Messages::FILE_REMOVED);
    }

    /**
     * @param string $file_id
     * @return string
     */
    public static function ajax_get_versions($file_id)
    {
        return self::_generate_response_object_from_function('self::get_versions', array($file_id));
    }

    /**
     * @param string $file_id
     * @param string $version_id
     * @return string
     */
    public static function ajax_set_active_version($file_id, $version_id)
    {
        return self::_generate_response_object_from_function('self::set_active_version', array($file_id, $version_id), Model_Files_Messages::VERSION_ACTIVATED);
    }

    /**
     * @param string $version_id
     * @return string
     */
    public static function ajax_remove_version($version_id)
    {
        return self::_generate_response_object_from_function('self::remove_version', array($version_id), Model_Files_Messages::FILE_VERSION_REMOVED);
    }

    //
    // MODEL
    //

    /**
     * @return int
     * @throws Model_Files_DIRECTORY_NOT_FOUND_Exception
     */
    public static function get_root_directory_id()
    {
        if (($id = self::_get_directory_id('/')) === NULL)
            throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

        return (int) $id;
    }

    /**
     * @param string $path
     * @return int|null
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     */
    public static function get_directory_id($path)
    {
        if ( ! isset($path) OR $path == '')
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        return self::_get_directory_id($path);
    }

    /**
     * @param int $directory_id
     * @param string $like
     * @param int $limit
     * @param int $offset
     * @param array $order_by
     * @return array
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function list_directory($directory_id, $like = NULL, $limit = NULL, $offset = NULL, $order_by = NULL)
    {
        if ( ! isset($directory_id) OR ! ( is_numeric($directory_id) OR ctype_digit($directory_id) )) /** _sql_get_files will validate the rest of the arguments **/
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $directory_id))
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            list($result) = self::_sql_get_files($directory_id, $like, $limit, $offset, $order_by);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        

        return (array) $result;
    }

    public static function getDirectoryTree($directory, $showDirs = true)
    {

        $tree = array();
        $directoryId = self::get_directory_id($directory);
        if ($directoryId) {
            $dirs = array(array('path' => $directory, 'id' => $directoryId));
            while ($dir = array_pop($dirs)) {
                $files = self::list_directory($dir['id']);

                foreach ($files as $file) {
                    $file['path'] = $dir['path'] . '/' . $file['name'];
                    if ($file['type'] == 0) {
                        $dirs[] = $file;
                        if ($showDirs) {
                            $tree[] = $file;
                        }
                    } else {
                        $tree[] = $file;
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * @param int $directory_id
     * @return array
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function get_path_breadcrumbs($directory_id)
    {
        if ( ! isset($directory_id) OR ! ( is_numeric($directory_id) OR ctype_digit($directory_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $directory_id))
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            $result = self::_get_path_breadcrumbs($directory_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        

        return (array) $result;
    }

    /**
     * @param int $parent_id
     * @param string $name
     * @param int $pk_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function create_directory($parent_id, $name, $pk_id = NULL)
    {
        $name = $name === NULL ? NULL : trim($name);

        if ( ! isset($parent_id, $name) OR ! ( is_numeric($parent_id) OR ctype_digit($parent_id) ) OR $name == '' OR preg_match('/\//', $name))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $parent_id))
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            if (self::_sql_get_directory_id($name, $parent_id) !== NULL)
                throw new Model_Files_DIRECTORY_ALREADY_EXISTS_Exception;

            if ( ! self::_create_file(self::FILE_TYPE_DIRECTORY, $parent_id, $name, $pk_id))
                throw new Model_Files_UNABLE_TO_CREATE_DIRECTORY_Exception;
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    /**
     * @param int $directory_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function remove_directory($directory_id)
    {
        if ( ! isset($directory_id) OR ! ( is_numeric($directory_id) OR ctype_digit($directory_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $directory_id))
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            if ( ! self::_quick_remove_directory($directory_id))
                throw new Model_Files_UNABLE_TO_REMOVE_DIRECTORY_Exception;
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    public static function get_directory_id_r($path, $create_if_not_exists = true)
    {
        $path = self::_clear_path($path);
        $dirs = explode('/', $path);
        array_unshift($dirs, '/');

        $parent_id = null;
        $dir_id = null;
        foreach ($dirs as $dir) {
            if ($dir == '') {
                $dir = '/';
            }
            $parent_id = $dir_id;
            $id = DB::select('*')
                ->from(self::TABLE_FILE)
                ->where('name', '=', $dir)
                ->and_where('parent_id', ($parent_id === null ? 'is' : '='), $parent_id)
                ->and_where('type', '=', 0)
                ->execute()
                ->get('id');
            if (!$id) {
                if ($create_if_not_exists) {
                    $inserted = DB::insert(self::TABLE_FILE)
                        ->values(
                            array(
                                'parent_id' => $parent_id,
                                'type' => 0,
                                'name' => $dir,
                                'language' => 'en',
                                'deleted' => 0,
                                'template_data' => 0,
                                'deleted' => 0
                            )
                        )
                        ->execute();
                    $id = $inserted[0];
                } else {
                    return false;
                }
            }
            $dir_id = $id;
        }
        return $dir_id;
    }

    /**
     * @param int $file_id
     * @return bool
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     */
    public static function file_exists($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        return self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id);
    }

    /**
     * @param int $parent_id
     * @param string $name
     * @param array $version_file
     * @param int $pk_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function create_file($parent_id, $name, $version_file, $pk_id = NULL)
    {
        $db   = Database::instance();
        $name = $name === NULL ? NULL : trim($name);

        if ( ! isset($parent_id, $name, $version_file) OR ! ( is_numeric($parent_id) OR ctype_digit($parent_id) ) OR $name == '' OR ! ( isset($version_file['name'], $version_file['tmp_name'], $version_file['type'], $version_file['size']) AND trim($version_file['name']) != '' AND is_file($version_file['tmp_name'] )))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        if ($db->begin())
        {
            try
            {
                if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $parent_id))
                    throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

                if ( ($file_id = self::_create_file(self::FILE_TYPE_REGULAR, $parent_id, $name, $pk_id)) === FALSE OR ! self::_add_version($file_id, $version_file) OR ! $db->commit() )
                    throw new Model_Files_UNABLE_TO_CREATE_FILE_Exception;
            }
            catch (Exception $exception)
            {
                $db->rollback(); 

                throw $exception;
            }
        }

        

		return isset($file_id) ? $file_id : FALSE;
    }

    /**
     * @param int $file_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function remove_file($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            if ( ! self::_quick_remove_file($file_id))
                throw new Model_Files_UNABLE_TO_REMOVE_FILE_Exception;
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    /**
     * @param int $file_id
     * @param string $name
     * @param array|null $version_file
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function update_file($file_id, $name, $version_file = NULL)
    {
        $db   = Database::instance();
        $name = trim($name);

        if ( ! isset($file_id, $name) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ) OR ($version_file !== NULL AND ! ( isset($version_file['name'], $version_file['tmp_name'], $version_file['type'], $version_file['size']) AND trim($version_file['name']) !== '' AND is_file($version_file['tmp_name']) )))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        if ($db->begin())
        {
            try
            {
                if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                    throw new Model_Files_FILE_NOT_FOUND_Exception;

                if ( ! self::_sql_update_file_name($file_id, $name) OR ! ($version_file === NULL OR self::_add_version($file_id, $version_file)) OR ! $db->commit() )
                    throw new Model_Files_UNABLE_TO_UPDATE_FILE_Exception;
            }
            catch (Exception $exception)
            {
                $db->rollback(); 

                throw $exception;
            }
        }

        
    }

    /**
     * @param int $file_id
     * @param int $parent_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function move_file($file_id, $parent_id)
    {
        if ( ! isset($file_id, $parent_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ) OR ! ( is_numeric($parent_id) OR ctype_digit($parent_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            if ( ! self::_sql_check_file_id(self::FILE_TYPE_DIRECTORY, $parent_id))
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            self::_sql_update_parent_id($file_id, $parent_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    /**
     * @param int $file_id
     * @param string $local_path
     * @param string $file_name
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function get_file($file_id, $local_path, $file_name = NULL)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ) OR $local_path == '')
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            if (($version_id = self::_sql_get_active_version_id($file_id)) === NULL)
                throw new Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception;

            self::_get_version_file($version_id, $local_path, $file_name);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    /**
     * @param int $file_id
     * @return false | string
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function getFileContent($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            if (($version_id = self::_sql_get_active_version_id($file_id)) === NULL)
                throw new Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception;

            $content = self::getVersionContent($version_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        

        return $content;
    }

    public static function getFileActiveVersionDetails($fileId)
    {
        return DB::select('*')
            ->from(self::TABLE_VERSION)
            ->where('file_id', '=', $fileId)
            ->and_where('active', '=', 1)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->current();
    }

    /**
     * @param int $file_id
     * @return string
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function get_file_name($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            $result = self::_sql_get_file_name(self::FILE_TYPE_REGULAR, $file_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        

        return (string) $result;
    }

    /**
     * @param int $file_id
     * @return string
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function get_active_version_name($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            $result = self::_sql_get_active_version_name($file_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        

        return (string) $result;
    }

    /**
     * @param int $file_id
     * @return array
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function get_versions($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            $result = self::_sql_get_versions($file_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        

        return (array) $result;
    }

    /**
     * @param int $file_id
     * @param int $version_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function set_active_version($file_id, $version_id)
    {
        $db = Database::instance();

        if ( ! isset($file_id, $version_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ) OR ! ( is_numeric($version_id) OR ctype_digit($version_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        if ($db->begin())
        {
            try
            {
                if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                    throw new Model_Files_FILE_NOT_FOUND_Exception;

                if ( ! self::_sql_check_version_id($version_id))
                    throw new Model_Files_VERSION_FILE_NOT_FOUND_Exception;

                if ( ! self::_activate_version($file_id, $version_id) OR ! $db->commit())
                    throw new Model_Files_UNABLE_TO_ACTIVATE_VERSION_Exception;
            }
            catch (Exception $exception)
            {
                $db->rollback(); 

                throw $exception;
            }
        }

        
    }

    /**
     * @param int $version_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function remove_version($version_id)
    {
        if ( ! isset($version_id) OR ! ( is_numeric($version_id) OR ctype_digit($version_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_version_id($version_id))
                throw new Model_Files_VERSION_FILE_NOT_FOUND_Exception;

            if ( self::_sql_check_active_version($version_id))
                throw new Model_Files_VERSION_IS_ACTIVE_Exception;

            if ( ! self::_remove_version($version_id))
                throw new Model_Files_UNABLE_TO_REMOVE_VERSION_FILE_Exception;
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    public static function file_path($file_id)
    {
        $version_id = self::_sql_get_active_version_id($file_id);
        $version = self::_sql_get_version_details($version_id);

        return self::_get_base_path() . '/' . $version['path'];
    }

    /**
     * @param int $file_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function download_file($file_id)
    {
        if ( ! isset($file_id) OR ! ( is_numeric($file_id) OR ctype_digit($file_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_file_id(self::FILE_TYPE_REGULAR, $file_id))
                throw new Model_Files_FILE_NOT_FOUND_Exception;

            if (($version_id = self::_sql_get_active_version_id($file_id)) === NULL)
                throw new Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception;

            self::_download_version($version_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    /**
     * @param int $version_id
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     * @throws Exception
     */
    public static function download_version($version_id)
    {
        if ( ! isset($version_id) OR ! ( is_numeric($version_id) OR ctype_digit($version_id) ))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        

        try
        {
            if ( ! self::_sql_check_version_id($version_id))
                throw new Model_Files_VERSION_FILE_NOT_FOUND_Exception;

            self::_download_version($version_id);
        }
        catch (Exception $exception)
        {
            

            throw $exception;
        }

        
    }

    /*
     *
     * PRIVATE
     *
     */

    //
    // COMMON
    //

    /**
     * @param string $function
     * @param array $arguments
     * @param string|null $success_message
     * @return string
     */
    private static function _generate_response_object_from_function($function, $arguments = array(), $success_message = NULL)
    {
        try
        {
            $status  = self::STATUS_S_OK;

            $data    = call_user_func_array($function, $arguments);
            $message = $success_message;
        }
        catch (Exception $exception)
        {
            $status  = self::STATUS_S_ERROR;

            $data    = NULL;
            $message = $exception->getMessage();
        }

        $response = array
        (
            'status' => $status
        );

        $data    === NULL OR $response['data'   ] = $data;
        $message === NULL OR $response['message'] = $message;

        return (string) json_encode($response);
    }

    /**
     * @return int|null
     */
    private static function _get_logged_user_id()
    {
        static $logged_user = NULL;

        $logged_user = ($logged_user === NULL) ? Auth::instance()->get_user() : $logged_user;

        return isset($logged_user['id']) ? (int) $logged_user['id'] : NULL;
    }

    //
    // FILES
    //

    /**
     * @param string $path
     * @return int|null
     */
    private static function _get_directory_id($path)
    {
        $path  = self::_clear_path($path);

        $id    = self::_sql_get_directory_id('/');
        $names = $path != '' ? explode('/', $path) : array();

        foreach ($names as $name)
        {
            $id = self::_sql_get_directory_id($name, $id);

            if ($id === NULL)
                break;
        }

        return $id === NULL ? NULL : (int) $id;
    }

    /**
     * @param $path
     * @return string
     * @throws Model_Files_UNKNOWN_ERROR_Exception
     */
    private static function _clear_path($path)
    {
        $path !== NULL AND $path = preg_replace('/\/+/'        , '/', $path);
        $path !== NULL AND $path = preg_replace('/(^\/)|(\/$)/', '' , $path);

        if ($path === NULL)
            throw new Model_Files_UNKNOWN_ERROR_Exception;

        return (string) $path;
    }

    /**
     * @param int $directory_id
     * @return array
     * @throws Model_Files_DIRECTORY_NOT_FOUND_Exception
     */
    private static function _get_path_breadcrumbs($directory_id)
    {
        $breadcrumbs = array();

        // Children
        while (($parent_id = self::_sql_get_parent_directory_id(self::FILE_TYPE_DIRECTORY, $directory_id)) !== NULL)
        {
            if (($name = self::_sql_get_file_name(self::FILE_TYPE_DIRECTORY, $directory_id)) === NULL)
                throw new Model_Files_DIRECTORY_NOT_FOUND_Exception;

            $breadcrumbs[] = array
            (
                'id'   => $directory_id,
                'name' => $name
            );

            $directory_id = $parent_id;
        }

        // Root
        $breadcrumbs[] = array
        (
            'id'   => self::_sql_get_directory_id('/'),
            'name' => self::ROOT_DIRECTORY_NAME
        );

        return array_reverse($breadcrumbs);
    }

    /**
     * @param int $type
     * @param int $parent_id
     * @param string $name
     * @param int $pk_id
     * @return bool|int
     */
    private static function _create_file($type, $parent_id, $name, $pk_id = NULL)
    {
        $data = array
        (
            'type'          => $type,
            'name'          => $name,
            'parent_id'     => $parent_id,
            'created_by'    => self::_get_logged_user_id(),
            'modified_by'   => self::_get_logged_user_id(),
            'date_created'  => date('Y-m-d H:i:s', time()),
            'date_modified' => date('Y-m-d H:i:s', time()),
            'deleted'       => 0
        );

        isset($pk_id) AND ($data['id'] = $pk_id);

        return self::_sql_insert(self::TABLE_FILE, $data);
    }

    /**
     * @param int $directory_id
     * @return bool
     */
    private static function _quick_remove_directory($directory_id)
    {
        return self::_sql_remove_file(self::FILE_TYPE_DIRECTORY, $directory_id);
    }

    /**
     * @param int $file_id
     * @return bool
     */
    private static function _quick_remove_file($file_id)
    {
        return self::_sql_remove_file(self::FILE_TYPE_REGULAR, $file_id);
    }

    /**
     * @param int $file_id
     * @param array $version
     * @return bool
     */
    private static function _add_version($file_id, $version)
    {
        $ok   = FALSE;

        $path = self::_get_storage_path();
        $file_ext = strtolower(substr($version['name'], strrpos($version['name'], '.') + 1));
        if (!in_array($file_ext, Model_Media::$allowed_extensions)) {
            Model_Errorlog::save(null, "SECURITY");
            return false;
        }
        $data = array
        (
            'name'      => $version['name'],
            'mime_type' => $version['type'],
            'path'      => $path,
            'size'      => $version['size'],
            'file_id'   => $file_id,
            'active'    => 1,
            'deleted'   => 0
        );

        if (($version_id = self::_sql_insert(self::TABLE_VERSION, $data)) !== FALSE AND self::_activate_version($file_id, $version_id))
        {
            $ok = isset($version['error']) ? move_uploaded_file($version['tmp_name'], self::_get_base_path().DIRECTORY_SEPARATOR.$path) : copy($version['tmp_name'], self::_get_base_path().DIRECTORY_SEPARATOR.$path);
        }

        return $ok;
    }

    /**
     * @param int $version_id
     * @return bool
     */
    private static function _remove_version($version_id)
    {
        return self::_sql_remove_version($version_id);
    }

    /**
     * @return string
     * @throws Model_Files_MAXIMUM_LIMIT_REACHED_Exception
     */
    private static function _get_storage_path()
    {
        $m = ( ! is_dir(self::_get_base_path()) ? 0 : count( glob(self::_get_base_path().DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) )) - 1;

        if ( $m < 0 OR (count( scandir(self::_get_base_path().DIRECTORY_SEPARATOR.($storage_directory = sprintf('%05d', $m))) ) - 2) == self::FILES_PER_DIRECTORY)
        {
            $storage_directory = sprintf('%05d', ++$m);

            if ($m >= self::FILES_PER_DIRECTORY)
                throw new Model_Files_MAXIMUM_LIMIT_REACHED_Exception; /** 10000 * 10000 = 100000000 VERSIONS!!! **/

            self::_create_local_directory(self::_get_base_path().DIRECTORY_SEPARATOR.$storage_directory);
        }

        return (string) $storage_directory.DIRECTORY_SEPARATOR.vsprintf('%d%06d', gettimeofday());
    }

    /**
     * @return string
     */
    private static function _get_base_path()
    {
        static $base_path = NULL;

        if ($base_path === NULL) {
            $project_media_folder = Kohana::$config->load('config')->project_media_folder;
            $base_path = ENGINEPATH.'www'.DIRECTORY_SEPARATOR.'shared_media'.DIRECTORY_SEPARATOR.$project_media_folder.DIRECTORY_SEPARATOR.self::BASE_DIRECTORY;
        }

        return (string) $base_path;
    }

    /**
     * @param string $directory
     * @throws Model_Files_UNABLE_TO_CREATE_LOCAL_DIRECTORY_Exception
     */
    private static function _create_local_directory($directory)
    {
        $mask = umask(0);

        try
        {
            if ( ! mkdir($directory, 0777, TRUE))
                throw new Exception;

            umask($mask);
        }
        catch (Exception $exception)
        {
            umask($mask);

            throw new Model_Files_UNABLE_TO_CREATE_LOCAL_DIRECTORY_Exception;
        }
    }

    /**
     * @param int $file_id
     * @param int $version_id
     * @return bool
     */
    private static function _activate_version($file_id, $version_id)
    {
        return self::_sql_deactivate_all_versions($file_id) AND self::_sql_activate_version($version_id);
    }

    /**
     * @param int $version_id
     * @throws Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception
     */
    private static function _download_version($version_id)
    {
        $version = self::_sql_get_version_details($version_id);

        if ($version === NULL)
            throw new Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception;

        header('Content-disposition: attachment; filename="'.$version['name'].'"');
        header('Content-type: '.$version['mime_type']);

        readfile(self::_get_base_path().DIRECTORY_SEPARATOR.$version['path']);
        exit;
    }

    /**
     * @param int $version_id
     * @param string $local_path
     * @param string $file_name
     * @return bool
     * @throws Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception
     */
    private static function _get_version_file($version_id, $local_path, $file_name = NULL)
    {
        $version = self::_sql_get_version_details($version_id);

        if ($version === NULL)
            throw new Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception;

        return copy(self::_get_base_path().DIRECTORY_SEPARATOR.$version['path'], $local_path.DIRECTORY_SEPARATOR.($file_name === NULL ? $version['name'] : $file_name));
    }

    /**
     * @param int $version_id
     * @return false|string
     * @throws Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception
     */
    private static function getVersionContent($version_id)
    {
        $version = self::_sql_get_version_details($version_id);

        if ($version === NULL)
            throw new Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception;

        $path = self::_get_base_path() . '/' . $version['path'];
        if (!file_exists($path)) {
            return false;
        } else {
            return file_get_contents($path);
        }
    }

    //
    // SQL
    //

    /**
     * @param string $table
     * @param array $data
     * @return bool|int
     */
    private static function _sql_insert($table, $data)
    {
        $result = DB::insert($table, array_keys($data))->values(array_values($data))->execute();

        return ($result[1] == 1) ? (int) $result[0] : FALSE;
    }

    /**
     * @param string $name
     * @param int $parent_id
     * @return int|null
     */
    public static function _sql_get_directory_id($name, $parent_id = NULL)
    {
        $result = DB::select('id')->from(self::TABLE_FILE)
                      ->where('type'     , '=', self::FILE_TYPE_DIRECTORY)
                      ->where('parent_id', '=', $parent_id               )
                      ->where('deleted'  , '=', 0                        )
                      ->where('name'     , '=', $name                    )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? (int) $result[0]['id'] : NULL;
    }

    /**
     * @param string $name
     * @param int $parent_id
     * @return int|null
     */
    public static function get_file_id($name, $parent_id = NULL)
    {
        $result = DB::select('id')->from(self::TABLE_FILE)
                      ->where('type'     , '=', self::FILE_TYPE_REGULAR)
                      ->where('parent_id', '=', $parent_id               )
                      ->where('deleted'  , '=', 0                        )
                      ->where('name'     , '=', $name                    )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? (int) $result[0]['id'] : NULL;
    }

    public static function getFileId($path)
    {
        $dir = dirname($path);
        $name = basename($path);
        $parent_id = self::get_directory_id($dir);
        $file = DB::select('id')->from(self::TABLE_FILE)
            ->where('type'     , '=', self::FILE_TYPE_REGULAR)
            ->where('parent_id', '=', $parent_id               )
            ->where('deleted'  , '=', 0                        )
            ->where('name'     , '=', $name                    )
            ->execute()
            ->current();

        return $file ? (int) $file['id'] : null;
    }

    /**
     * @param int $type
     * @param int $file_id
     * @return bool
     */
    private static function _sql_check_file_id($type, $file_id)
    {
        $result = DB::select('id')->from(self::TABLE_FILE)
                      ->where('type'   , '=', $type   )
                      ->where('id'     , '=', $file_id)
                      ->where('deleted', '=', 0       )
                      ->execute()
                      ->as_array();

        return (count($result) > 0);
    }

    /**
     * @param int $parent_id
     * @param string $like
     * @param int $limit
     * @param int $offset
     * @param array $order_by
     * @return array
     * @throws Model_Files_INVALID_ARGUMENTS_Exception
     */
    private static function _sql_get_files($parent_id, $like = NULL, $limit = NULL, $offset = NULL, $order_by = NULL)
    {
        /** Warning! If you change the order of the fields, you need to update ajax_list_directory() and the corresponding view/javascript **/
        $query_1 = DB::select('t1.id', 'type', 't1.name', 'size', 't1.date_created', 't1.date_modified', array(DB::expr('IFNULL(t3.name, "Unknown")'), 'modified_by'),'t2.path')->from(array(self::TABLE_FILE, 't1'))
                       ->join(array(self::TABLE_VERSION, 't2'), 'LEFT')->on('t2.file_id', '=', 't1.id'         )->on('type', '=', DB::expr(self::FILE_TYPE_REGULAR))
                       ->join(array(self::TABLE_USER   , 't3'), 'LEFT')->on('t3.id'     , '=', 't1.modified_by')
                       ->where('parent_id' , '=', $parent_id)
                       ->where('t1.deleted', '=', 0         )
                       ->where_open()
                           ->or_where('type'  , '=', self::FILE_TYPE_DIRECTORY)
                           ->or_where('active', '=', 1                        )
                       ->where_close();

        $query_2 = DB::select(array('COUNT("t1.id")', 'n'))->from(array(self::TABLE_FILE, 't1'))
                       ->join(array(self::TABLE_VERSION, 't2'), 'LEFT')->on('t2.file_id', '=', 't1.id'         )->on('type', '=', DB::expr(self::FILE_TYPE_REGULAR))
                       ->join(array(self::TABLE_USER   , 't3'), 'LEFT')->on('t3.id'     , '=', 't1.modified_by')
                       ->where('parent_id' , '=', $parent_id)
                       ->where('t1.deleted', '=', 0         )
                       ->where_open()
                           ->or_where('type'  , '=', self::FILE_TYPE_DIRECTORY)
                           ->or_where('active', '=', 1                        )
                       ->where_close();

        // Like
        if ($like !== NULL AND $like != '')
        {
            $query_1
                ->where_open()
                    ->or_where('t1.name', 'LIKE', '%'.$like.'%')
                ->where_close();

            $query_2
                ->where_open()
                    ->or_where('t1.name', 'LIKE', '%'.$like.'%')
                ->where_close();
        }

        // Limit & Offset
        if ( (isset($limit) AND ! ( is_numeric($limit) OR ctype_digit($limit) )) OR (isset($offset) AND ! ( is_numeric($offset) OR ctype_digit($offset) )) )
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        ($limit  !== NULL AND $limit > -1) AND $query_1->limit ($limit );
        ($offset !== NULL AND $limit > -1) AND $query_1->offset($offset);

        // Order By
        if (isset($order_by) AND ! is_array($order_by))
            throw new Model_Files_INVALID_ARGUMENTS_Exception;

        if ($order_by !== NULL AND count($order_by) > 0)
        {
            foreach($order_by AS $order)
            {
                if ( count($order) != 2 OR ! ( is_numeric($order[0]) OR ctype_digit($order[0]) ) OR ! preg_match('/^(ASC|DESC)$/i', $order[1]) )
                    throw new Model_Files_INVALID_ARGUMENTS_Exception;

                $query_1->order_by(DB::expr($order[0]), $order[1]);
            }
        }
        else
        {
            $query_1->order_by('type', 'ASC')->order_by('t1.name', 'ASC');
        }

        // Execute
        $result = $query_1->execute()->as_array();
        $data   = $result;

        $result = $query_2->execute()->as_array(); /** http://www.mysqlperformanceblog.com/2007/08/28/to-sql_calc_found_rows-or-not-to-sql_calc_found_rows/ **/
        $total  = $result[0]['n'];

        return (array) array($data, $total);
    }

    /**
     * @param $directory_id
     * @return int
     */
    private static function _sql_count_files_for_directory($directory_id)
    {
        $items = DB::select(array('COUNT("id")', 'n'))->from(self::TABLE_FILE)
                     ->where('parent_id', '=', $directory_id)
                     ->where('deleted'  , '=', 0            )
                     ->execute()
                     ->as_array();

        return (int) $items[0]['n'];
    }

    /**
     * @param int $type
     * @param int $file_id
     * @return int|null
     */
    private static function _sql_get_parent_directory_id($type, $file_id)
    {
        $result = DB::select('parent_id')->from(self::TABLE_FILE)
                      ->where('type'   , '=', $type   )
                      ->where('id'     , '=', $file_id)
                      ->where('deleted', '=', 0       )
                      ->execute()
                      ->as_array();

        return (count($result) > 0 AND $result[0]['parent_id'] !== NULL) > 0 ? (int) $result[0]['parent_id'] : NULL; /** This is to prevent NULL being returned as INTEGER if parent_id is NULL **/
    }

    /**
     * @param int $type
     * @param int $file_id
     * @return null|string
     */
    private static function _sql_get_file_name($type, $file_id)
    {
        $result = DB::select('name')->from(self::TABLE_FILE)
                      ->where('type'   , '=', $type   )
                      ->where('id'     , '=', $file_id)
                      ->where('deleted', '=', 0       )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? (string) $result[0]['name'] : NULL;
    }

    /**
     * @param int $file_id
     * @return null|string
     */
    private static function _sql_get_active_version_name($file_id)
    {
        $result = DB::select('name')->from(self::TABLE_VERSION)
                      ->where('file_id', '=', $file_id)
                      ->where('active' , '=', 1       )
                      ->where('deleted', '=', 0       )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? (string) $result[0]['name'] : NULL;
    }

    /**
     * @param int $file_id
     * @param string $name
     * @return bool
     */
    private static function _sql_update_file_name($file_id, $name)
    {
        $result = DB::update(self::TABLE_FILE)->set(array('name' => $name, 'modified_by' => self::_get_logged_user_id()))
                      ->where('id', '=', $file_id)
                      ->execute();

        return $result >= 0;
    }

    /**
     * @param int $file_id
     * @param int $parent_id
     * @return bool
     */
    private static function _sql_update_parent_id($file_id, $parent_id)
    {
        $result = DB::update(self::TABLE_FILE)->set(array('parent_id' => $parent_id, 'modified_by' => self::_get_logged_user_id()))
                      ->where('id', '=', $file_id)
                      ->execute();

        return $result >= 0;
    }

    /**
     * @param int $file_id
     * @return bool
     */
    private static function _sql_deactivate_all_versions($file_id)
    {
        $result = DB::update(self::TABLE_VERSION)->set(array('active' => 0))
                      ->where('file_id', '=', $file_id)
                      ->execute();

        return $result >= 0;
    }

    /**
     * @param int $version_id
     * @return bool
     */
    private static function _sql_activate_version($version_id)
    {
        $result = DB::update(self::TABLE_VERSION)->set(array('active' => 1))
                      ->where('id', '=', $version_id)
                      ->execute();

        return $result >= 0;
    }

    /**
     * @param int $file_id
     * @return array
     */
    private static function _sql_get_versions($file_id)
    {
        $result = DB::select('id', 'name', 'size', 'active')->from(self::TABLE_VERSION)
                      ->where('file_id', '=', $file_id)
                      ->where('deleted', '=', 0       )
                      ->order_by('id', 'DESC')
                      ->execute()
                      ->as_array();

        return (array) $result;
    }

    /**
     * @param int $version_id
     * @return bool
     */
    private static function _sql_check_version_id($version_id)
    {
        $result = DB::select('id')->from(self::TABLE_VERSION)
                      ->where('id'     , '=', $version_id)
                      ->where('deleted', '=', 0          )
                      ->execute()
                      ->as_array();

        return count($result) > 0;
    }

    /**
     * @param int $version_id
     * @return bool|null
     */
    private static function _sql_check_active_version($version_id)
    {
        $result = DB::select('active')->from(self::TABLE_VERSION)
                      ->where('id'     , '=', $version_id)
                      ->where('deleted', '=', 0          )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? ($result[0]['active'] == 1) : NULL;
    }

    /**
     * @param int $file_id
     * @return int|null
     */
    private static function _sql_get_active_version_id($file_id)
    {
        $result = DB::select('id')->from(self::TABLE_VERSION)
                      ->where('file_id', '=', $file_id)
                      ->where('active' , '=', 1       )
                      ->where('deleted', '=', 0       )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? (int) $result[0]['id'] : NULL;
    }

    /**
     * @param int $file_id
     * @return string
     */
    private static function _sql_get_active_path($file_id)
    {
        $result = DB::select('path')->from(self::TABLE_VERSION)
            ->where('file_id', '=', $file_id)
            ->where('active' , '=', 1       )
            ->where('deleted', '=', 0       )
            ->execute()
            ->as_array();

        return count($result) > 0 ? $result[0] : NULL;
    }

    /**
     * @param int $version_id
     * @return array|null
     */
    public static function _sql_get_version_details($version_id)
    {
        $result = DB::select('mime_type', 'name', 'path')->from(self::TABLE_VERSION)
                      ->where('id'     , '=', $version_id)
                      ->where('deleted', '=', 0          )
                      ->execute()
                      ->as_array();

        return count($result) > 0 ? (array) $result[0] : NULL;
    }

    /**
     * @param int $type
     * @param int $file_id
     * @return bool
     */
    private static function _sql_remove_file($type, $file_id)
    {
        $result = DB::update(self::TABLE_FILE)->set(array('deleted' => 1))
                      ->where('type', '=', $type   )
                      ->where('id'  , '=', $file_id)
                      ->execute();

        return $result >= 0;
    }

    /**
     * @param int $version_id
     * @return bool
     */
    private static function _sql_remove_version($version_id)
    {
        $result = DB::update(self::TABLE_VERSION)->set(array('deleted' => 1))
                      ->where('id', '=', $version_id)
                      ->execute();

        return $result >= 0;
    }

    public static function unlink_file($file_id)
    {
        $versions = DB::select('*')
            ->from(self::TABLE_VERSION)
            ->where('file_id', '=', $file_id)
            ->execute()
            ->as_array();
        foreach ($versions as $version) {
            @unlink(self::_get_base_path() . '/' . $version['path']);
        }
        DB::update(self::TABLE_VERSION)
            ->set(array('deleted' => 1))
            ->where('file_id', '=', $file_id)
            ->execute();
        DB::update(self::TABLE_FILE)
            ->set(array('deleted' => 1))
            ->where('id', '=', $file_id)
            ->execute();
    }

    public static function copy_from_media($media_id, $directory, $name)
    {
        $media = Model_Media::get_by_id($media_id);
        $media_path = Model_Media::get_localpath_to_id($media_id);
        $directory_id = Model_Files::get_directory_id_r('/tmp/' . time());

        $file_id = Model_Files::create_file($directory_id, $name, array('name' => $name, 'type' => $media['mime_type'], 'tmp_name' => $media_path, 'size' => filesize($media_path)));
        return $file_id;
    }
}

/**
 * Class Model_Files_Exception
 */
class Model_Files_Exception extends Exception
{
    public function __construct()
    {
        $this->message = constant('Model_Files_Messages::'.get_called_class());
    }
}

/**
 * Class Model_Files_UNKNOWN_ERROR_Exception
 */
final class Model_Files_UNKNOWN_ERROR_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_INVALID_ARGUMENTS_Exception
 */
final class Model_Files_INVALID_ARGUMENTS_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_DIRECTORY_ALREADY_EXISTS_Exception
 */
final class Model_Files_DIRECTORY_ALREADY_EXISTS_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_DIRECTORY_NOT_FOUND_Exception
 */
final class Model_Files_DIRECTORY_NOT_FOUND_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_FILE_ALREADY_EXISTS_Exception
 */
final class Model_Files_FILE_ALREADY_EXISTS_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_LOCK_FOR_READ_Exception
 */
final class Model_Files_UNABLE_TO_LOCK_FOR_READ_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_LOCK_FOR_WRITE_Exception
 */
final class Model_Files_UNABLE_TO_LOCK_FOR_WRITE_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_UNLOCK_TABLES_Exception
 */
final class Model_Files_UNABLE_TO_UNLOCK_TABLES_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_FILE_NOT_FOUND_Exception
 */
final class Model_Files_FILE_NOT_FOUND_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_CREATE_DIRECTORY_Exception
 */
final class Model_Files_UNABLE_TO_CREATE_DIRECTORY_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_CREATE_FILE_Exception
 */
final class Model_Files_UNABLE_TO_CREATE_FILE_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_MAXIMUM_LIMIT_REACHED_Exception
 */
final class Model_Files_MAXIMUM_LIMIT_REACHED_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_CREATE_LOCAL_DIRECTORY_Exception
 */
final class Model_Files_UNABLE_TO_CREATE_LOCAL_DIRECTORY_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_UPDATE_FILE_Exception
 */
final class Model_Files_UNABLE_TO_UPDATE_FILE_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_VERSION_FILE_NOT_FOUND_Exception
 */
final class Model_Files_VERSION_FILE_NOT_FOUND_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_ACTIVATE_VERSION_Exception
 */
final class Model_Files_UNABLE_TO_ACTIVATE_VERSION_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception
 */
final class Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception
 */
final class Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_REMOVE_DIRECTORY_Exception
 */
final class Model_Files_UNABLE_TO_REMOVE_DIRECTORY_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_REMOVE_FILE_Exception
 */
final class Model_Files_UNABLE_TO_REMOVE_FILE_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_UNABLE_TO_REMOVE_VERSION_FILE_Exception
 */
final class Model_Files_UNABLE_TO_REMOVE_VERSION_FILE_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_VERSION_IS_ACTIVE_Exception
 */
final class Model_Files_VERSION_IS_ACTIVE_Exception extends Model_Files_Exception {}

/**
 * Class Model_Files_Messages
 */
final class Model_Files_Messages
{
    // General Messages
    const DIRECTORY_CREATED    = 'Directory created.';
    const FILE_REMOVED         = 'File removed.';
    const VERSION_ACTIVATED    = 'Version activated.';
    const DIRECTORY_REMOVED    = 'Directory removed.';
    const FILE_VERSION_REMOVED = 'Version removed.';
    const FILE_CREATED         = 'File created.';
    const FILE_UPDATED         = 'File updated.';

    // Exceptions
    const Model_Files_UNABLE_TO_ACTIVATE_VERSION_Exception       = 'Model_Files_UNABLE_TO_ACTIVATE_VERSION_Exception';
    const Model_Files_UNABLE_TO_CREATE_DIRECTORY_Exception       = 'Model_Files_UNABLE_TO_CREATE_DIRECTORY_Exception';
    const Model_Files_UNABLE_TO_CREATE_FILE_Exception            = 'Model_Files_UNABLE_TO_CREATE_FILE_Exception';
    const Model_Files_UNABLE_TO_CREATE_LOCAL_DIRECTORY_Exception = 'Model_Files_UNABLE_TO_CREATE_LOCAL_DIRECTORY_Exception';
    const Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception    = 'Model_Files_UNABLE_TO_GET_VERSION_DETAILS_Exception';
    const Model_Files_UNABLE_TO_LOCK_FOR_READ_Exception          = 'Model_Files_UNABLE_TO_LOCK_FOR_READ_Exception';
    const Model_Files_UNABLE_TO_LOCK_FOR_WRITE_Exception         = 'Model_Files_UNABLE_TO_LOCK_FOR_WRITE_Exception';
    const Model_Files_UNABLE_TO_REMOVE_DIRECTORY_Exception       = 'Model_Files_UNABLE_TO_REMOVE_DIRECTORY_Exception';
    const Model_Files_UNABLE_TO_REMOVE_FILE_Exception            = 'Model_Files_UNABLE_TO_REMOVE_FILE_Exception';
    const Model_Files_UNABLE_TO_REMOVE_VERSION_FILE_Exception    = 'Model_Files_UNABLE_TO_REMOVE_VERSION_FILE_Exception';
    const Model_Files_UNABLE_TO_UNLOCK_TABLES_Exception          = 'Model_Files_UNABLE_TO_UNLOCK_TABLES_Exception';
    const Model_Files_UNABLE_TO_UPDATE_FILE_Exception            = 'Model_Files_UNABLE_TO_UPDATE_FILE_Exception';
    const Model_Files_UNKNOWN_ERROR_Exception                    = 'Model_Files_UNKNOWN_ERROR_Exception';
    const Model_Files_VERSION_FILE_NOT_FOUND_Exception           = 'Model_Files_VERSION_FILE_NOT_FOUND_Exception';
    const Model_Files_VERSION_IS_ACTIVE_Exception                = 'Model_Files_VERSION_IS_ACTIVE_Exception';
    const Model_Files_INVALID_ARGUMENTS_Exception                = 'Model_Files_INVALID_ARGUMENTS_Exception';
    const Model_Files_DIRECTORY_NOT_FOUND_Exception              = 'Model_Files_DIRECTORY_NOT_FOUND_Exception';
    const Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception         = 'Model_Files_ACTIVE_VERSION_NOT_FOUND_Exception';
    const Model_Files_DIRECTORY_ALREADY_EXISTS_Exception         = 'Model_Files_DIRECTORY_ALREADY_EXISTS_Exception';
    const Model_Files_FILE_ALREADY_EXISTS_Exception              = 'Model_Files_FILE_ALREADY_EXISTS_Exception';
    const Model_Files_FILE_NOT_FOUND_Exception                   = 'Model_Files_FILE_NOT_FOUND_Exception';
    const Model_Files_MAXIMUM_LIMIT_REACHED_Exception            = 'Model_Files_MAXIMUM_LIMIT_REACHED_Exception';
}
