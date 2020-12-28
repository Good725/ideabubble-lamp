<?php defined('SYSPATH') or die('No Direct Script Access.');

class Model_Uploader extends Model
{
    // Status Codes
    const STATUS_S_OK                       =  0;
    const STATUS_E_ERROR                    = -1;
    const STATUS_E_FILE_EXISTS              = -2;
    const STATUS_E_FILE_TOO_BIG             = -3;
    const STATUS_E_FILE_TYPE_NOT_ALLOWED    = -4;
    const STATUS_E_DIRECTORY_LIMIT_EXCEEDED = -5;
    const STATUS_E_DIRECTORY_SIZE_EXCEEDED  = -6;

    // Fields
    protected $working_directory;
    protected $mime_types_allowed;
    protected $max_file_size;
    protected $max_files_per_directory;
    protected $max_directory_size;

    //
    // PUBLIC
    //

    /**
     * @param $working_directory
     * @param null $mime_types_allowed
     * @param $max_file_size
     * @param $max_files_per_directory
     * @param $max_directory_size
     * @throws Exception
     */
    public function __construct($working_directory, $mime_types_allowed = NULL, $max_file_size = -1, $max_files_per_directory = -1, $max_directory_size = -1)
    {
        if ( ! is_dir($working_directory))
        {
            $mask = umask(0);
            $ok   = mkdir($working_directory, 0777, TRUE);

            umask($mask);

            if ( ! $ok)
                throw new Exception(get_class().': Unable to initialize the class.');
        }

        $this->working_directory       = $working_directory;
        $this->mime_types_allowed      = $mime_types_allowed;
        $this->max_file_size           = $max_file_size;
        $this->max_files_per_directory = $max_files_per_directory;
        $this->max_directory_size      = $max_directory_size;
    }

    /**
     * @param $file
     * @return int
     */
    public function save_file($file)
    {
        $status = $this->validate_for_save($file);

        if ($status == self::STATUS_S_OK)
        {
            try
            {
                if ( ! move_uploaded_file($file['tmp_name'], $this->working_directory.DIRECTORY_SEPARATOR.$file['name']) )
                {
                    $status = self::STATUS_E_ERROR;
                }
            }
            catch (Exception $e)
            {
                Log::instance()->add(Log::ERROR, $e->getTraceAsString());

                $status = self::STATUS_E_ERROR;
            }
        }

        return $status;
    }

    /**
     * @return bool
     */
    public function remove_folder()
    {
        return $this->_remove_folder($this->working_directory);
    }


    /**
     * @return bool
     */
    public function empty_folder()
    {
        $ok = TRUE;

        try
        {
            $files = $this->list_directory($this->working_directory);

            for ($i = 0; $i < count($files); $i++)
            {
                $file = $this->working_directory.DIRECTORY_SEPARATOR.$files[$i];

                $ok   = ($ok AND is_dir($file) ? $this->_remove_folder($file) : unlink($file));
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $ok = FALSE;
        }

        return $ok;
    }

    //
    // PRIVATE
    //

    /**
     * @param $file
     * @return int
     */
    private function validate_for_save($file)
    {
        $error = self::STATUS_S_OK;
        $ok    = TRUE;

        try
        {
            // General Error
            $ok = ($ok AND ( $file['error'] == 0 ));
            (! $ok AND $error == self::STATUS_S_OK) AND $error = self::STATUS_E_ERROR;

            // File Exists
            $ok = ($ok AND ! file_exists($this->working_directory.DIRECTORY_SEPARATOR.$file['name']));
            (! $ok AND $error == self::STATUS_S_OK) AND $error = self::STATUS_E_FILE_EXISTS;

            // MIME Type
            $ok = ($ok AND ( $this->mime_types_allowed == NULL OR array_search($file['type'], $this->mime_types_allowed) !== FALSE ));
            (! $ok AND $error == self::STATUS_S_OK) AND $error = self::STATUS_E_FILE_TYPE_NOT_ALLOWED;

            // File Size
            $ok = ($ok AND ( $this->max_file_size == -1 OR $file['size'] <= $this->max_file_size ));
            (! $ok AND $error == self::STATUS_S_OK) AND $error = self::STATUS_E_FILE_TOO_BIG;

            // Directory Limit
            $ok = ($ok AND ( $this->max_files_per_directory == -1 OR count($this->list_directory($this->working_directory)) < $this->max_files_per_directory ));
            (! $ok AND $error == self::STATUS_S_OK) AND $error = self::STATUS_E_DIRECTORY_LIMIT_EXCEEDED;

            // Directory Size
            $ok = ($ok AND ( $this->max_directory_size == -1 OR ($this->get_directory_size($this->working_directory) + $file['size']) <= $this->max_directory_size ));
            (! $ok AND $error == self::STATUS_S_OK) AND $error = self::STATUS_E_DIRECTORY_SIZE_EXCEEDED;
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $error = self::STATUS_E_ERROR;
        }

        return $error;
    }

    /**
     * @param $directory
     * @return int
     */
    private function get_directory_size($directory)
    {
        $content = $this->list_directory($directory);
        $size    = 0;

        for ($i = 0; $i < count($content); $i++)
        {
            $size += filesize($directory.DIRECTORY_SEPARATOR.$content[$i]);
        }

        return $size;
    }

    /**
     * @param $directory
     * @return array
     */
    private function list_directory($directory)
    {
        return array_values(array_diff(scandir($directory), array('.', '..')));
    }

    /**
     * @param string $folder
     * @return bool
     */
    private function _remove_folder($folder = NULL)
    {
        $ok = TRUE;

        try
        {
            $base_folder = $folder === NULL ? $this->working_directory : $folder;
            $files       = $this->list_directory($base_folder);

            for ($i = 0; $i < count($files); $i++)
            {
                $file = $base_folder.DIRECTORY_SEPARATOR.$files[$i];

                $ok   = ($ok AND is_dir($file) ? $this->remove_folder($file) : unlink($file));
            }

            rmdir($base_folder);
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $ok = FALSE;
        }

        return $ok;
    }
}
