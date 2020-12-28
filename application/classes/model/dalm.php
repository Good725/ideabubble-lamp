<?php defined('SYSPATH') or die('No Direct Script Access.');

/**
 * Class Model_DALM
 */
final class Model_DALM extends Model
{
    const MODEL_DIRECTORY = 'model'; // The name of model directory
    const MODEL_FILE = 'model.sql'; // The name of the model file

    const DEFAULT_DB_CHARSET = 'utf8'; // The default database character set
    const DEFAULT_DB_COLLATION = 'utf8_general_ci'; // The default database collation

    const MODEL_TABLE = 'engine_dalm_model'; // The name of the table that will be used to store the models processed
    const STATEMENTS_TABLE = 'engine_dalm_statement'; // The name of the table that will be used to store the statements executed

    const MODEL_TYPE_ENGINE = 1;
    const MODEL_TYPE_ENGINE_PLUGIN = 2;
    const MODEL_TYPE_PROJECT = 3;
    const MODEL_TYPE_PROJECT_PLUGIN = 4;
    const MODEL_TYPE_TEMPLATE = 5;

    const STATUS_PROCESSING_MODEL = 2;
    const STATUS_MODEL_NOT_PROCESSED = 1;
    const STATUS_MODEL_PROCESSED_OK = 0;
    const STATUS_MODEL_PROCESSED_WITH_ERRORS = -1;

    /**
     * Contains the configuration (host, user, password...).
     * @var array $configuration
     */
    private static $configuration;

    /**
     * Contains the mysqli object for the primary connection.
     * @var mysqli $primary_connection
     */
    private static $primary_connection;

    /**
     * Contains the statements that caused the model processing to be aborted.
     * @var array $failed_statements
     */
    private static $failed_statements;

    private static $model_errors = array();

    protected static $lock_fd = null;
    //
    // PUBLIC
    //
    /**
     * retrieves the the dalm model entries for display in app.
     * @return array
     */
    public static function get_dalm_model()
    {
        $r = DB::select('*')
            ->from(self::MODEL_TABLE)
            ->order_by('status')
            ->execute()
            ->as_array();

        return $r;
    }

    /**
     * resets the DALM_MODEL status to 0 for a given ID
     * @return array
     */
    public static function clear_error($id)
    {
        DB::update(self::MODEL_TABLE)
            ->set(array('status' => 0, 'md5' => date('Y-m-d H:i:s') . '-cleared'))
            ->where('id', '=', $id)
            ->execute();
    }

    /**
     * retrieves the the dalm statement entries for display in app.
     * @return array
     */
    public static function get_dalm_statements()
    {
        $r = DB::select('*')
            ->from(self::STATEMENTS_TABLE)
            ->order_by('id')
            ->execute()
            ->as_array();

        return $r;
    }

    public static function get_automation_cache_filename()
    {
        return $cache_file = Kohana::$cache_dir . '/' . $_SERVER['HTTP_HOST'] . '/automations.json';
    }

    /**
     * Updates the database used by this project.
     * @param string $profile
     * @return bool
     * @throws Model_DALM_UNABLE_TO_CONNECT_TO_MYSQL_Exception
     * @throws Model_DALM_UNABLE_TO_CREATE_REQUIRED_DATABASE_Exception
     * @throws Model_DALM_UNABLE_TO_SELECT_REQUIRED_DATABASE_Exception
     */
    public static function update_db($profile = 'default')
    {
        $run_once = false;
        try {
            if (Settings::instance()->get('dalm_run_once') == 1) {
                $run_once = true;
            }
        } catch (Exception $exc) {

        }
        $ok = FALSE;
        $engineversion = @file_get_contents(ENGINEPATH . '/../.revision');
        $cache_dir = Kohana::$cache_dir . '/' . $_SERVER['HTTP_HOST'] . '/dalm';
        if(!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }
        $last_dalm_run_engine_version = @file_get_contents($cache_dir . '/engine');

        $force_dalm = @$_GET['dalm'] == 'force';
        if ($force_dalm == false && $run_once == true && $engineversion != '' && $last_dalm_run_engine_version == $engineversion) {
            return true;
        }
        @unlink(self::get_automation_cache_filename());

        if (preg_match('/^5\.(5|6)\./', phpversion())) {
            $er = error_reporting();
            error_reporting($er  ^ E_DEPRECATED);
        }
        self::_set_configuration($profile);
        if (isset(self::$configuration['nodalm']) && self::$configuration['nodalm'] == true) {
            return true;
        }
        self::_establish_primary_connection();

       try {
           self::_acquire_lock();

           self::_create_db();

           if (!self::$primary_connection->select_db(self::$configuration['connection']['database'])) {
               throw new Model_DALM_UNABLE_TO_SELECT_REQUIRED_DATABASE_Exception;
           }


           self::_create_dalm_tables();

           if (!($ok = self::_update_db())) {
               self::_report();
           } else {
               if ($engineversion != '') {
                   file_put_contents($cache_dir . '/engine', $engineversion);
               }
           }
           self::_release_lock();
       } catch (Model_DALM_UNABLE_TO_ACQUIRE_LOCK_Exception $exc) {
           //header('content-type: text/plain');print_r($exc);exit;
           self::_report($exc);
           throw new Exception("Please wait a minute and then try again");
       } catch (Exception $e) {
           //header('content-type: text/plain');print_r($e);exit;
            self::_report($e);
			self::_release_lock();
        }

        self::_close_all_connections();

        return $ok;
    }

    //
    // PRIVATE
    //

    /**
     * Set the configuration from the specified profile.
     * @param string $profile
     */
    private static function _set_configuration($profile)
    {
        self::$configuration = Kohana::$config->load('database')->$profile;
    }

    /**
     * Establishes the primary connection to MySQL.
     * @throws Model_DALM_UNABLE_TO_CONNECT_TO_MYSQL_Exception
     */
    private static function _establish_primary_connection()
    {
        self::$primary_connection = new mysqli(
            self::$configuration['connection']['hostname'],
            self::$configuration['connection']['username'],
            self::$configuration['connection']['password']
        );

        if (self::$primary_connection->connect_errno !== 0) {
            throw new Model_DALM_UNABLE_TO_CONNECT_TO_MYSQL_Exception;
        } else {
            self::$primary_connection->set_charset(
                isset(self::$configuration['charset']) ? self::$configuration['charset'] : 'utf8'
            );
        }
    }

    /**
     *
     */
    private static function _close_all_connections()
    {
        if (self::$primary_connection instanceof mysqli) {
            self::$primary_connection->close();
        }
    }

    /**
     * Creates the database if it does not exist.
     * @throws Model_DALM_UNABLE_TO_CREATE_REQUIRED_DATABASE_Exception
     */
    private static function _create_db()
    {
        $query = "CREATE DATABASE IF NOT EXISTS `" . self::$configuration['connection']['database'] . "` CHARACTER SET " . self::DEFAULT_DB_CHARSET . " COLLATE " . self::DEFAULT_DB_COLLATION;

        if (!self::$primary_connection->query($query))
            throw new Model_DALM_UNABLE_TO_CREATE_REQUIRED_DATABASE_Exception;
    }

    /**
     * Creates the DALM tables if they does not exist.
     * @throws Model_DALM_UNABLE_TO_CREATE_REQUIRED_TABLES_Exception
     */
    private static function _create_dalm_tables()
    {
        $dalm_model = self::MODEL_TABLE;
        $dalm_statement = self::STATEMENTS_TABLE;

        $new_dalm_ = self::$primary_connection->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='" . self::MODEL_TABLE . "'");
        $new_dalm = $new_dalm_->fetch_assoc();
        $new_dalm_->free();
        if (preg_match('/DALM VERSION:\s*([\d\.]+)/', $new_dalm['TABLE_COMMENT'], $dalm_version)) {
            if ($dalm_version[1] == '1.0') {
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_statement` ADD COLUMN executed DATETIME"
                );
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.1'"
                );

                $dalm_version[1] = '1.1';
            }

            if ($dalm_version[1] == '1.1') {
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` ADD COLUMN last_query TEXT"
                );
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.2'"
                );
                $dalm_version[1] = '1.2';
            }

            if ($dalm_version[1] == '1.2') {
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_statement` ADD COLUMN ignored DATETIME"
                );
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.3'"
                );
                $dalm_version[1] = '1.3';
            }

            if ($dalm_version[1] == '1.3') {
                try {
                    Model_Dalm::replaceViewSqlSecurityDefiners();
                    Model_Dalm::replaceRoutineSqlSecurityDefiners();
                } catch (Exception $exc) {
                    //mysql 5.1 does not have some information
                }
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.4'"
                );
                $dalm_version[1] = '1.4';
            }

            if ($dalm_version[1] == '1.4') {
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` ADD COLUMN `updated` DATETIME"
                );
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.5'"
                );
                $dalm_version[1] = '1.5';
            }

            if ($dalm_version[1] == '1.5') {
                try {
                    Model_Dalm::replaceEventSqlSecurityDefiners();
                } catch (Exception $exc) {
                    //mysql 5.1 does not have some information
                }
                self::$primary_connection->query(
                    "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.6'"
                );
                $dalm_version[1] = '1.6';
            }
        } else {
            if (!$new_dalm) {
                $old_dalm_ = self::$primary_connection->query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='dalm_model'");
                $old_dalm = $old_dalm_->fetch_assoc();
                $old_dalm_->free();
                if ($old_dalm) {
                    self::$primary_connection->query('ALTER TABLE `dalm_model` RENAME TO `engine_dalm_model`');
                    self::$primary_connection->query('ALTER TABLE `dalm_statement` RENAME TO `engine_dalm_statement`');
                    self::$primary_connection->query('ALTER TABLE `engine_dalm_statement` MODIFY COLUMN `statement` TEXT');
                } else {
                    $query_1 = "CREATE TABLE IF NOT EXISTS `" . self::MODEL_TABLE . "` (`id` INT(11) NOT NULL AUTO_INCREMENT, `type` INT(11) NOT NULL, `name` VARCHAR(256) NOT NULL, `status` TINYINT(1) NOT NULL, `md5` VARCHAR(32) NOT NULL,`last_error` TEXT, PRIMARY KEY (`id`), UNIQUE INDEX `" . self::MODEL_TABLE . "_idx_1` (`md5` ASC)) CHARSET = utf8 ENGINE = InnoDB";
                    $query_2 = "CREATE TABLE IF NOT EXISTS `" . self::STATEMENTS_TABLE . "` (`id` INT(11) NOT NULL AUTO_INCREMENT, `model_id` INT(11) NOT NULL, `md5` VARCHAR(32) NOT NULL, `statement` TEXT NOT NULL, PRIMARY KEY (`id`), INDEX `" . self::STATEMENTS_TABLE . "_idx_1` (`model_id` ASC), CONSTRAINT `" . self::STATEMENTS_TABLE . "_fk_1` FOREIGN KEY (`model_id`) REFERENCES `" . self::MODEL_TABLE . "` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION) CHARSET = utf8 ENGINE = InnoDB";

                    if (!(self::$primary_connection->query($query_1) and self::$primary_connection->query($query_2))) {
                        throw new Model_DALM_UNABLE_TO_CREATE_REQUIRED_TABLES_Exception;
                    }
                }
            }
            $has_versioned_models_ = self::$primary_connection->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='" . self::MODEL_TABLE . "' AND COLUMN_NAME='version'");
            $has_versioned_models = $has_versioned_models_->fetch_assoc();
            $has_versioned_models_->free();
            if (!$has_versioned_models) {
                self::$primary_connection->query('ALTER TABLE `engine_dalm_model` ADD COLUMN `version` VARCHAR(20)');
                self::$primary_connection->query('ALTER TABLE `engine_dalm_model` ADD COLUMN `depends` TEXT');
            } else {
                if ($has_versioned_models['CHARACTER_MAXIMUM_LENGTH'] == 10) {
                    self::$primary_connection->query('ALTER TABLE `engine_dalm_model` MODIFY COLUMN `version` VARCHAR(20)');
                }
            }

            $charset_ = self::$primary_connection->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='" . $dalm_statement . "' AND COLUMN_NAME='statement'");
            $charset = $charset_->fetch_assoc();
            $charset_->free();
            if ($charset['CHARACTER_SET_NAME'] != 'utf8') {
                /*
                 * convert to utf8 in two steps(latin1->binary->utf8) instead one(latin1->utf8)
                 * due to already existing special characters which was wrongly handled
                 */
                self::$primary_connection->query('ALTER TABLE `' . $dalm_statement . '` DEFAULT CHARACTER SET=binary');
                self::$primary_connection->query(
                    'ALTER TABLE `' . $dalm_statement . '` MODIFY COLUMN `statement` TEXT CHARACTER SET binary'
                );
                self::$primary_connection->query('ALTER TABLE `' . $dalm_statement . '` DEFAULT CHARACTER SET=utf8');
                self::$primary_connection->query(
                    'ALTER TABLE `' . $dalm_statement . '` MODIFY COLUMN `statement` TEXT CHARACTER SET utf8'
                );

                /*
                 * model table does not need two step conversion like above
                 * */
                self::$primary_connection->query(
                    'ALTER TABLE `' . $dalm_model . '` CONVERT TO CHARACTER SET utf8'
                );
            }

            self::$primary_connection->query(
                "ALTER TABLE `engine_dalm_model` COMMENT='DALM VERSION: 1.0'"
            );

            // to continue updating dalm tables based on dalm version comment
            self::_create_dalm_tables();
        }
    }

    /**
     * Locks the MODEL_TABLE table.
     * @throws Model_DALM_UNABLE_TO_ACQUIRE_LOCK_Exception
     */
    public static function _acquire_lock()
    {
        $GLOBALS['dalm_time_limit'] = ini_get('max_execution_time');
        set_time_limit(0);
        ignore_user_abort(true);
        if (!isset(self::$configuration['connection']['database'])) {
            self::_set_configuration('default');
        }
        self::$lock_fd = fopen(Kohana::$cache_dir . '/' . self::$configuration['connection']['database'] . '.lock', 'c+');
        if (!self::$lock_fd) {
            header('Content-Type: text/plain; charset=utf-8');
            echo "dalm lock failed. system error.";
            exit;
        }

        if (!flock(self::$lock_fd, LOCK_EX|LOCK_NB)) {
            header('Content-Type: text/plain; charset=utf-8');
            echo "dalm lock failed. please wait a few minutes and try again";
            exit;
            //throw new Model_DALM_UNABLE_TO_ACQUIRE_LOCK_Exception;
        }
    }

    /**
     * Unlocks the MODEL_TABLE table.
     * @throws Model_DALM_UNABLE_TO_RELEASE_LOCK_Exception
     */
    public static function _release_lock()
    {
        flock(self::$lock_fd, LOCK_UN);
        fclose(self::$lock_fd);
        unlink(Kohana::$cache_dir . '/' . self::$configuration['connection']['database'] . '.lock');

        set_time_limit($GLOBALS['dalm_time_limit']);
        unset($GLOBALS['dalm_time_limit']);
    }

    /*
     *
     * UPDATE PROCESS
     *
     */

    /**
     * Updates the model. If the $stop_on_first_error parameter is set to TRUE, the function will stop on first error and will return FALSE. Otherwise, it will
     * continue. If all the models are processed OK, the function will return TRUE. It is important to note that different threads can be involved in the update
     * process, but the same model will be processed only by one thread.
     * @param bool $stop_on_first_error
     * @return bool
     * @throws Model_DALM_UNABLE_TO_ACQUIRE_LOCK_Exception
     * @throws Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception
     * @throws Model_DALM_UNABLE_TO_GET_PROCESSED_MODELS_Exception
     * @throws Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception
     * @throws Model_DALM_UNABLE_TO_REGISTER_MODELS_Exception
     * @throws Model_DALM_UNABLE_TO_CONNECT_TO_MYSQL_Exception
     * @throws Model_DALM_UNABLE_TO_SET_MODEL_STATUS_Exception
     * @throws Model_DALM_UNABLE_TO_GET_PREVIOUS_STATEMENTS_Exception
     * @throws Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception
     * @throws Model_DALM_UNABLE_TO_RELEASE_LOCK_Exception
     */
    private static function _update_db()
    {
        $ok = TRUE;

        $stop_on_first_error = true;
        $models_found = self::_find_models();
        if (count($models_found)) {
            header('content-type: text/plain; charset=utf-8');
            echo "Convert old models to new timestamped models:\n";
            foreach ($models_found as $m) {
                echo "\t" . $m['file'] . "\n";
            }
            exit();
        }
		/*
		 * non versioned model wont be processed
		 * $models_to_process = self::_register_models_to_process($models_found);

        if (count($models_to_process) > 0) {
            foreach ($models_to_process as $model) {
                $model_processed_ok = self::_process_model($model);

                self::_set_model_status($model, $model_processed_ok ? self::STATUS_MODEL_PROCESSED_OK : self::STATUS_MODEL_PROCESSED_WITH_ERRORS);

                if (!($ok = ($ok AND $model_processed_ok)) AND $stop_on_first_error) {
                    break;
                }
            }
        }
		*/

        if ($ok) {
            $versioned_models = self::_get_versioned_models();
            $versioned_models_to_process = self::_register_models_to_process($versioned_models);
            /*//header('content-type: text/plain; charset-utf-8');
            foreach($versioned_models as $vm) {
                echo $vm["name"] . ":" . date('YmdHis', $vm["timestamp"]) . "<br>";
            }
            //exit();
            echo "<br><br>=====><br><br>";
            foreach($versioned_models_to_process as $vm) {
                echo $vm["name"] . ":" . date('YmdHis', $vm["timestamp"]) . "<br>";
            }
            exit();*/
            if (count($versioned_models_to_process) > 0) {
                foreach ($versioned_models_to_process as $model) {
                    $model_processed_ok = self::_process_model($model);
                    self::_set_model_status($model,
                        $model_processed_ok ? self::STATUS_MODEL_PROCESSED_OK : self::STATUS_MODEL_PROCESSED_WITH_ERRORS);

                    if (!$model_processed_ok) {
                        $ok = false;
                        break;
                    }
                }
            }
        }
        
        self::update_model_errors();

        return $ok;
    }

    private static function update_model_errors()
    {
        DB::update(self::MODEL_TABLE)->set(array('last_error' => ''))->where('status','=',0)->execute();
        foreach(self::$model_errors AS $key=>$error)
        {
            try{
                DB::update(self::MODEL_TABLE)
                    ->set(array(
                        'last_error' => $error['error'],
                        'last_query' => $error['query'],
                        'updated' => date('Y-m-d H:i:s')
                    ))->where('id','=',$error['model_id'])->execute();
            }
            catch(Exception $e)
            {
                //go to hell. won't throw exception...
            }
        }
    }

	public static function delete_models($ids)
	{
		foreach($ids as $id){
			DB::delete(self::STATEMENTS_TABLE)->where('model_id', '=', $id)->execute();
			DB::delete(self::MODEL_TABLE)->where('id', '=', $id)->execute();
		}
	}

	public static function get_missing_models()
	{
		$found_models = array_merge(self::_find_models(), self::_find_versioned_models());
		$models = DB::select('*')->from(self::MODEL_TABLE)->execute()->as_array();
		$missing_models = array();
		foreach($models as $model){
			$found = false;
			foreach($found_models as $found_model){
				if(
                    $model['name'] == $found_model['name']
                    &&
                    (isset($found_model['type']) ? $model['type'] == $found_model['type'] : true)
                ){
					$found = true;
					break;
				}
			}
			if(!$found){
				$missing_models[] = $model;
			}
		}
		
		return $missing_models;
	}

    /**
     * Returns an array with the models found.
     * @return array
     * @throws Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception
     */
    private static function _find_models()
    {
        $models = array();

        $template_dir = Kohana::$config->load('config')->get('template_folder_path');

        $engine_file = ENGINEPATH . 'application' . DIRECTORY_SEPARATOR . self::MODEL_DIRECTORY . DIRECTORY_SEPARATOR . self::MODEL_FILE;
        $project_file = PROJECTPATH . self::MODEL_DIRECTORY . DIRECTORY_SEPARATOR . self::MODEL_FILE;
        $template_file = PROJECTPATH . 'views/templates/'.$template_dir. DIRECTORY_SEPARATOR . self::MODEL_DIRECTORY . DIRECTORY_SEPARATOR . self::MODEL_FILE;

        if (is_file($engine_file)) {
            $models[] = self::_new_model(self::MODEL_TYPE_ENGINE, 'engine', $engine_file);
        }

        if (is_file($project_file)) {
            $models[] = self::_new_model(self::MODEL_TYPE_PROJECT, mb_strtolower(PROJECTNAME), $project_file);
        }

        if (is_file($template_file)) {
            $models[] = self::_new_model(self::MODEL_TYPE_TEMPLATE, mb_strtolower($template_dir), $template_file);
        }

        if (Kohana::$config->load('config')->get('db_id'))
        {
            $template_project_file = PROJECTPATH.self::MODEL_DIRECTORY.DIRECTORY_SEPARATOR.Kohana::$config->load('config')->get('db_id').DIRECTORY_SEPARATOR.self::MODEL_FILE;
            if (is_file($template_project_file))
            {
                $models[] = self::_new_model(self::MODEL_TYPE_PROJECT, mb_strtolower(PROJECTNAME), $template_project_file);
            }
        }


        return array_merge($models, self::_find_plugin_models());
    }
	
	private static function _find_versioned_models()
	{
        $exclude_plugins = array();
        if (isset(self::$configuration['dalm_exclude_plugins'])) {
            $exclude_plugins = self::$configuration['dalm_exclude_plugins'];
        }

		$template_dir = Kohana::$config->load('config')->get('template_folder_path');
		$models = array();
        $directories = array();
		$directories[] = array('type_name' => 'engine', 'name' => 'application', 'file' => ENGINEPATH . 'application/' . self::MODEL_DIRECTORY);
		if(is_dir(ENGINEPATH . 'plugins')){
			foreach(self::_get_required_engine_plugins() as $eplugin){
                if (in_array($eplugin, $exclude_plugins)) {
                    continue;
                }
				if(file_exists(ENGINEPATH . 'plugins/' . $eplugin . '/' . self::MODEL_DIRECTORY)){
					$directories[] = array('type_name' => 'engine.plugin', 'name' => $eplugin, 'file' => ENGINEPATH . 'plugins/' . $eplugin . '/' . self::MODEL_DIRECTORY);
				}
			}
		}
		$directories[] = array('type_name' => 'project', 'name' => 'project', 'file' => PROJECTPATH . self::MODEL_DIRECTORY);
		if(is_dir(PROJECTPATH . 'plugins')){
			foreach(array_values(array_diff(scandir(PROJECTPATH . 'plugins'), array('.', '..'))) as $pplugin){
                if (in_array($pplugin, $exclude_plugins)) {
                    continue;
                }
                if(file_exists(PROJECTPATH . 'plugins/' . $pplugin . '/' . self::MODEL_DIRECTORY)){
					$directories[] = array('type_name' => 'project.plugin', 'name' => $pplugin, 'file' => PROJECTPATH . 'plugins/' . $pplugin . '/' . self::MODEL_DIRECTORY);
				}
			}
		}
		$directories[] = array('type_name' => 'template', 'name' => $template_dir, 'file' => PROJECTPATH . 'views/templates/' . $template_dir . '/' . self::MODEL_DIRECTORY);
		
		$db_id = Kohana::$config->load('config')->get('db_id');
		if($db_id){
			$directories[] = array('type_name' => 'project', 'name' => $db_id, 'file' => PROJECTPATH . self::MODEL_DIRECTORY . '/' . $db_id);
		}

        if (@$GLOBALS['project']) {
            if ($GLOBALS['project'] != $db_id)
            if (file_exists(PROJECTPATH . self::MODEL_DIRECTORY . '/' . $GLOBALS['project'])) {
                $directories[] = array('type_name' => 'project', 'name' => $GLOBALS['project'], 'file' => PROJECTPATH . self::MODEL_DIRECTORY . '/' . $GLOBALS['project']);
            }
        }
		return $directories;
	}
	
	private static function _resolve_versioned_models_dependencies(&$model, &$models)
	{
		$version = (int)ltrim($model['version'], '0');
		if(!$version)$version = 0;
		do{
			$modified = false;
			foreach($models as $ti => $test){
				if(!isset($model['depends'][$ti]) && $model['type_name'] == $test['type_name'] && $model['name'] == $test['name']){
					$tversion = (int)ltrim($test['version'], '0');
					if(!$tversion)$tversion = 0;
					if($tversion < $version){
						$model['depends'][$ti] = array('name' => $test['type_name'] . '.' . $test['name'], 'version' => $test['version']);
						$modified = true;
					}
				}
			}
			foreach($model['depends'] as $di => $depend){
				if(isset($models[$di])){
					foreach($models[$di]['depends'] as $pdi => $pdepend){
						if(!isset($model['depends'][$pdi])){
							$model['depends'][$pdi] = $pdepend;
							$modified = true;
						}
					}
				}
			}
		}while($modified == true);
	}

	private static function _scan_versioned_models($directories)
	{
		$models = array();
		foreach($directories as $dir){
			if(file_exists($dir['file'])){
				$files = scandir($dir['file']);
				foreach($files as $file){
					if(preg_match('#model-(\d+)\.(sql|php)#i', $file, $match)){
						$model = array('type_name' => $dir['type_name'],
										'name' => $dir['name'],
										'version' => $match[1],
                                        'timestamp' => 0,
										'file' => $dir['file'] . '/' . $file,
										'statements' => file_get_contents($dir['file'] . '/' . $file),
										'depends' => array());
                        $model['php'] = $match[2] == 'php';
						if($model['type_name'] == 'engine'){
							$model['type'] = self::MODEL_TYPE_ENGINE;
						} else if($model['type_name'] == 'engine.plugin'){
							$model['type'] = self::MODEL_TYPE_ENGINE_PLUGIN;
						} else if($model['type_name'] == 'project'){
							$model['type'] = self::MODEL_TYPE_PROJECT;
						} else if($model['type_name'] == 'project.plugin'){
							$model['type'] = self::MODEL_TYPE_PROJECT_PLUGIN;
						} else if($model['type_name'] == 'template'){
							$model['type'] = self::MODEL_TYPE_TEMPLATE;
						}
						$model['md5'] = md5($model['statements']);

						if(preg_match('#^(\<\?php)?\s*/\*(.*?)\*/#s', $model['statements'], $info)){
							$model['statements'] = substr($model['statements'], strlen($info[0]));
                            if(preg_match('#ts:\s*(\d\d\d\d-\d\d-\d\d\s+\d\d:\d\d:\d\d)#i', $info[0], $timestamp)){
                                $model['timestamp'] = strtotime($timestamp[1]);
                            }
							if(preg_match_all('#depends:\s*(.+):(\d+)#', $info[0], $depends)){
								foreach($depends[0] as $di => $depend){
									$model['depends'][$depends[1][$di] . ':' . $depends[2][$di]] = array('name' => $depends[1][$di], 'version' => $depends[2][$di]);
								}
							}
						}
						$models[$model['type_name'] . '.' . $model['name'] . ':' . $model['version']] = $model;
					}
				}
			}
		}
		foreach($models as $mi => $model){
			self::_resolve_versioned_models_dependencies($models[$mi], $models);
		}
		
		usort($models, function($m1, $m2){
            /*
            $v1 = (int)ltrim($m1['version'], '0');
			$v2 = (int)ltrim($m2['version'], '0');
			if(!$v1)$v1 = 1;
			if(!$v2)$v2 = 1;
				
			if($m1['type_name'] == $m2['type_name'] && $m1['name'] == $m2['name']){
				return $v1 - $v2;
			}

			foreach($m1['depends'] as $depend){
				$vd = (int)ltrim($depend['version']);
				if(!$vd)$vd = 1;
				if($depend['name'] == $m2['type_name'] . '.' . $m2['name'] && $vd <= $v2){
					return 1;
				}
			}
			foreach($m2['depends'] as $depend){
				$vd = (int)ltrim($depend['version']);
				if(!$vd)$vd = 1;
				if($depend['name'] == $m1['type_name'] . '.' . $m1['name'] && $vd <= $v1){
					return -1;
				}
			}*/

            // use timestamps instead of version/dependencies

            if ($m1['timestamp'] != $m2['timestamp']) {
                return $m1['timestamp'] - $m2['timestamp'];
            } else {
                return $m1['type'] - $m2['type'];
            }
		});

        return $models;
	}

	private static function _get_versioned_models()
	{
		$directories = self::_find_versioned_models();
		$models = self::_scan_versioned_models($directories);
		return $models;
	}

    /**
     * Returns an array with the plugin models found. If there are two plugins with the same name, one in the engine and one in the project, the
     * engine plugin model will be discarded, as it won't be loaded on the project.
     * @return array
     * @throws Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception
     */
    private static function _find_plugin_models()
    {
        $models = array();

        $project_plugins = is_dir(PROJECTPATH . 'plugins') ? array_values(array_diff(scandir(PROJECTPATH . 'plugins'), array('.', '..'))) : array();
        $engine_plugins = is_dir(ENGINEPATH . 'plugins') ? array_values(array_diff(self::_get_required_engine_plugins(), $project_plugins)) : array();

        foreach ($engine_plugins as $plugin) {
            $file = ENGINEPATH . 'plugins' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . self::MODEL_DIRECTORY . DIRECTORY_SEPARATOR . self::MODEL_FILE;

            if (is_file($file)) {
                $models[] = self::_new_model(self::MODEL_TYPE_ENGINE_PLUGIN, mb_strtolower($plugin), $file);
            }
        }

        foreach ($project_plugins as $plugin) {
            $file = PROJECTPATH . 'plugins' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . self::MODEL_DIRECTORY . DIRECTORY_SEPARATOR . self::MODEL_FILE;

            if (is_file($file)) {
                $models[] = self::_new_model(self::MODEL_TYPE_PROJECT_PLUGIN, mb_strtolower($plugin), $file);
            }
        }

        return $models;
    }

    /**
     * Returns the required engine plugins. Teh required plugins are set in the project configuration file. It the variable 'engine_plugins' does not exist, this
     * function will return an empty array. Otherwise, it will return an array with the name of each plugin contained in that variable.
     * @return array
     */
    private static function _get_required_engine_plugins()
    {
        $required_plugins = array();
        $engine_plugins = Kohana::$config->load('config')->get('engine_plugins');

        unset(Kohana::$config->_groups['config']);

        if ($engine_plugins != NULL AND is_dir(ENGINEPATH . 'plugins')) {
            $required_plugins = $engine_plugins === '*' ? array_values(array_diff(scandir(ENGINEPATH . 'plugins'), array('.', '..'))) : explode(',', $engine_plugins);
        }
		
		///workaround for MC-7
		if(array_search('todos', $required_plugins) === false && file_exists(ENGINEPATH . 'plugins/todos')){
			$required_plugins[] = 'todos';
		}

        return $required_plugins;
    }

    /**
     * Returns a new model object.
     * @param int $type
     * @param string $name
     * @param string $file
     * @return array
     * @throws Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception
     */
    private static function _new_model($type, $name, $file)
    {
        $statements = file_get_contents($file);

        if ($statements === FALSE)
            throw new Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception;

        return array
        (
            'id' => NULL,
            'type' => $type,
            'name' => $name,
            'file' => $file,
            'statements' => $statements,
            'md5' => md5($statements)
        );
    }

    /**
     * Registers new models (not registered previously) or updates old ones (already registered but a new version has been found) in the MODEL_TABLE table. This function will
     * return an array with the models to process.
     * @param array $models_found
     * @return array
     * @throws Model_DALM_UNABLE_TO_GET_PROCESSED_MODELS_Exception
     * @throws Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception
     * @throws Model_DALM_UNABLE_TO_REGISTER_MODELS_Exception
     */
    private static function _register_models_to_process($models_found)
    {
        $processed_models = self::_get_processed_models();
        $models_to_process = self::_get_models_to_process($models_found, $processed_models);

        self::_register_models($models_to_process);

        return $models_to_process;
    }

    /**
     * Returns an array of all MD5 values contained in the MODEL_TABLE table.
     * @return array
     * @throws Model_DALM_UNABLE_TO_GET_PROCESSED_MODELS_Exception
     */
    private static function _get_processed_models()
    {
        $list = array();
        $query = "SELECT `md5` FROM `" . self::MODEL_TABLE . "` WHERE `status` = '" . self::STATUS_MODEL_PROCESSED_OK . "'";

        if (!($result = self::$primary_connection->query($query)))
            throw new Model_DALM_UNABLE_TO_GET_PROCESSED_MODELS_Exception;

        while ($r = $result->fetch_array(MYSQLI_NUM)) {
            $list[] = $r[0];
        }

        return $list;
    }

    /**
     * Gets the model to be processed according to the information contained in the $processed_models array. This function will return NULL if one of
     * the arguments or both are NULL.
     * @param array $models_found
     * @param array $processed_models
     * @return array|null
     */
    private static function _get_models_to_process($models_found, $processed_models)
    {
        $models_to_process = NULL;

        if (isset($models_found) AND isset($processed_models)) {
            $models_to_process = $models_found;

            foreach ($models_to_process as $id => $model) {
                if (array_search($model['md5'], $processed_models) !== FALSE) {
                    unset($models_to_process[$id]);
                }
            }
        }

        return $models_to_process;
    }

    /**
     * Registers (inserts) new models into the MODEL_TABLE table it they do not exist. Otherwise, updates the record with the new MD5 of the model. The model status is
     * set to STATUS_MODEL_NOT_PROCESSED.
     * @param array $models
     * @throws Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception
     * @throws Model_DALM_UNABLE_TO_REGISTER_MODELS_Exception
     */
    private static function _register_models(&$models)
    {
        if (isset($models)) {
            foreach ($models as &$model) {
                if (($id = self::_get_model_id($model))) {
                    $query = "UPDATE `" . self::MODEL_TABLE . "` 
								SET `md5` = '" . $model['md5'] . "', 
									`status` = '" . self::STATUS_MODEL_NOT_PROCESSED . "',
									`updated` = NOW()
									" . (isset($model['version']) ? ",`depends` = '" . implode(',', array_keys($model['depends'])) . "'" : "") . "
								WHERE `id` = '" . $id . "' AND `status` != '" . self::STATUS_PROCESSING_MODEL . "'" . (isset($model['version']) ? " AND `version` = '" . $model['version'] . "'" : "");
                } else {
                    $query = "INSERT INTO `" . self::MODEL_TABLE . "` 
								(`type`, `name`, `updated`, `md5`, `status`" . (isset($model['version']) ? ", `version`, `depends`" : "") . ")
								VALUES
								('" . $model['type'] . "','" . $model['name'] . "',NOW(),'" . $model['md5'] . "','" . self::STATUS_MODEL_NOT_PROCESSED . "'" . (isset($model['version']) ? ", '" . $model['version'] . "', '" . implode(',', array_keys($model['depends'])) . "'" : "") . ")";
                }

                if (!self::$primary_connection->query($query))
                    throw new Model_DALM_UNABLE_TO_REGISTER_MODELS_Exception;

                $model['id'] = $id ? $id : self::$primary_connection->insert_id;
            }
        }
    }

    /**
     * Returns the ID of the requested model or FALSE if it is not found in the MODEL_TABLE table.
     * @param array $model
     * @return bool
     * @throws Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception
     */
    private static function _get_model_id($model)
    {
        $id = FALSE;
        $query = "SELECT `id` FROM `" . self::MODEL_TABLE . "` WHERE `type` = '" . $model['type'] . "' AND `name` = '" . $model['name'] . "'" . (isset($model['version']) ? " AND `version`='" . $model['version'] . "'" : " AND `version` IS NULL");

        if (!($result = self::$primary_connection->query($query)))
            throw new Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception;

        if ($result->num_rows === 1) {
            $row = $result->fetch_array(MYSQLI_NUM);
            $id = $row[0];
        }

        return $id;
    }

    /**
     * Set the status of a model.
     * @param array $model
     * @param int $status
     * @throws Model_DALM_UNABLE_TO_SET_MODEL_STATUS_Exception
     */
    private static function _set_model_status($model, $status)
    {
        $query = "UPDATE `" . self::MODEL_TABLE . "` SET `status` = '" . $status . "' WHERE `id` = '" . $model['id'] . "'";

        if (!self::$primary_connection->query($query))
            throw new Model_DALM_UNABLE_TO_SET_MODEL_STATUS_Exception;
    }

    /*
     *
     * MODEL PROCESSING
     *
     */

    /**
     * Processes a model. Executes all the statements contained in the model and not executed previously (unless $process_previous_statements
     * is set to TRUE). Will stop on first error. If the model is successfully processed, the statements executed are registered in the STATEMENTS_TABLE table.
     * @param array $model
     * @param bool $process_previous_statements
     * @return bool
     * @throws Model_DALM_UNABLE_TO_GET_PREVIOUS_STATEMENTS_Exception
     * @throws Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception
     */
    private static function _process_model($model, $process_previous_statements = FALSE)
    {
        if ($model['php']) {
            try {
                //eval($model['statements']);
                //header('content-type: text/plain');print_R($model);exit;
                @include_once $model['file'];
                $ok = true;
            } catch (Exception $exc) {
                $ok = false;
                self::_add_failed_statement($model['file'], $exc->getMessage());
            }
        } else {
            $statements = self::_get_statements($model);
            $previous_statements = $process_previous_statements ? array() : self::_get_previous_statements();
            //header('content-type: text/plain');print_r($previous_statements);exit();


            $statements_to_be_executed = self::_get_statements_to_be_executed($statements, $previous_statements);
            $successful_statements = self::_execute_statements($statements_to_be_executed);

            //self::_register_statements($successful_statements);
            if (($ok = (count($successful_statements) === count($statements_to_be_executed)))) {
            } else {
                self::_add_failed_statement($model['file'],
                    $statements_to_be_executed[count($successful_statements)]['query']);
            }
        }
        return $ok;
    }

    /**
     * Gets all the statements of a model.
     * @param array $model
     * @return array
     */
    private static function _get_statements($model)
    {
        $statements = array();
        $lines = explode("\n", $model['statements']);

        for ($i = 0, $m = count($lines), $delimiter = ';', $query = ''; $i < $m; $i++) {
            $line = preg_replace('/^(-- ?.*)|(-- ?.*)$/', '', trim($lines[$i]));

            if (preg_match('/^(delimiter) +(.+)/i', $line, $matches)) {
                $delimiter = $matches[2];
            } else {
                $query .= $line . ' ';

                if (preg_match('/(' . preg_quote($delimiter) . ' *)$/i', $line)) {
                    $statements[] = self::_new_statement($model['id'], self::_clean_query($query, $delimiter));
                    $query = '';
                }
            }
        }

        return $statements;
    }

    /**
     * Returns a clean SQL query, removing duplicated spaces and other characters.
     * @param string $query
     * @param string $delimiter
     * @return string
     */
    private static function _clean_query($query, $delimiter)
    {
        return preg_replace('/(;;)$/', ';', trim(preg_replace('/(' . preg_quote($delimiter) . ' *)$/i', '', $query)) . ';'); /* TODO: Need improvement */
    }

    /**
     * Returns a new statement object.
     * @param int $model_id
     * @param string $query
     * @return array
     */
    private static function _new_statement($model_id, $query)
    {
        return array
        (
            'model_id' => $model_id,
            'query' => $query,
            'md5' => md5($query)
        );
    }

    /**
     * Returns an array of all MD5 values contained in the STATEMENTS_TABLE table for a give model identifier.
     * @param int $model_id
     * @return array
     * @throws Model_DALM_UNABLE_TO_GET_PREVIOUS_STATEMENTS_Exception
     */
    private static function _get_previous_statements()
    {
        $list = array();
        $query = "SELECT `md5` FROM `" . self::STATEMENTS_TABLE . "`";

        if (!($result = self::$primary_connection->query($query)))
            throw new Model_DALM_UNABLE_TO_GET_PREVIOUS_STATEMENTS_Exception;

        while ($r = $result->fetch_array(MYSQLI_NUM)) {
            $list[] = $r[0];
        }

        return $list;
    }

    /**
     * Gets the statements to be executed according to the statements contained in the array of previous statements.
     * @param array $statements_found
     * @param array $previous_statements
     * @return array
     */
    private static function _get_statements_to_be_executed($statements_found, $previous_statements)
    {
        $statements_to_be_executed = array();

        foreach ($statements_found as $statement) {
            if (($i = array_search($statement['md5'], $previous_statements)) !== FALSE) {
                unset($previous_statements[$i]);
            } else {
                $statements_to_be_executed[] = $statement;
            }
        }

        return $statements_to_be_executed;
    }

    /**
     * Executes a set of statements and returns an array with the statements successfully executed. Will stop on first error.
     * @param array $statements
     * @return array
     */
    private static function _execute_statements($statements)
    {
        $successful_statements = array();

        if (count($statements) > 0)
        {
            self::$primary_connection->query("SET @KOHANA_ENV='" . getenv("KOHANA_ENV") . "'");
            foreach ($statements as $statement)
            {
                if (trim($statement['query']) == ';') {
                    continue;
                    //print_r($statements);die();
                }
				if(preg_match('/^\s*create\s+(procedure|function|trigger|event)\s+(`?[^\s\(\)]+`?)/i', $statement['query'], $proc)){
					self::$primary_connection->query('drop ' . $proc[1] . ' if exists ' . $proc[2]);
				}

                $error = true;
                if (!self::$primary_connection->query($statement['query']))
                {
                    $query = preg_replace('/\s+/', ' ', $statement['query']);
                    if (stripos(self::$primary_connection->error, 'duplicate') !== false && @$_GET['skip_duplicate'] == 1) {
                        $error = false;
                    } else if (stripos(self::$primary_connection->error, 'already exists') !== false && @$_GET['skip_duplicate'] == 1) {
                        $error = false;
                    } else

                    if(strpos(strtolower($query), "/*nodalmerror*/")) {
                        $error = false;
                    } else if(strpos(strtolower($query),"alter ignore table") !== FALSE OR strpos(strtolower($query),"alter table"))
                    {
                        self::$model_errors[] = array(
                            'query' => $statement['query'],
                            'error' => self::$primary_connection->error,
                            'model_id' => $statement['model_id']
                        );
                        //DB::update('dalm_model')->set(array('last_error' => $query))->execute();
                        Kohana::$log->add(
                            Log::ERROR,
                            "Failed ALTER statement: " . $query . " => " . self::$primary_connection->error
                        );
                        $error = false;
                    }
                    else
                    {
                        self::$model_errors[] = array(
                            'query' => $statement['query'],
                            'error' => self::$primary_connection->error,
                            'model_id' => $statement['model_id']
                        );
                        //DB::update('dalm_model')->set(array('last_error' => $query))->execute();
                        Kohana::$log->add(
                            Log::ERROR,
                            "Failed ALTER statement: " . $query . " => " . self::$primary_connection->error
                        );
                        break;
                    }
                } else {
                    $error = false;
                }
                if (!$error) {
                    self::_register_statement($statement);
                }

                $successful_statements[] = $statement;
            }
        }

        return $successful_statements;
    }

    /**
     * Registers a set of statements in the STATEMENTS_TABLE table.
     * @param array $statements
     * @throws Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception
     */
    private static function _register_statements($statements)
    {
        if (count($statements) > 0) {
            $query = "INSERT INTO `" . self::STATEMENTS_TABLE . "` (`model_id`, `md5`, `statement`, `executed`) VALUES";

            foreach ($statements as $statement) {
                $query .= "(" . $statement['model_id'] . ",'" . $statement['md5'] . "','" . self::$primary_connection->escape_string($statement['query']) . "', NOW()),";
            }

            if (!self::$primary_connection->query(substr_replace($query, '', -1)))
                throw new Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception;
        }
    }

    private static function _register_statement($statement)
    {
        $query = "INSERT INTO `" . self::STATEMENTS_TABLE . "` (`model_id`, `md5`, `statement`, `executed`) VALUES";
        $query .= "(" . $statement['model_id'] . ",'" . $statement['md5'] . "','" . self::$primary_connection->escape_string($statement['query']) . "', NOW()),";
        if (!self::$primary_connection->query(substr_replace($query, '', -1)))
            throw new Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception;
    }

    /*
     *
     * LOG FUNCTIONS
     *
     */

    /**
     * Adds a failed statement to the array $failed_statements.
     * @param string $file
     * @param string $statement
     */
    private static function _add_failed_statement($file, $statement)
    {
        self::$failed_statements[] = array
        (
            'file' => $file,
            'statement' => $statement
        );
    }

    /**
     * Writes the report to the LOG and sends an email if we are in the PRODUCTION environment.
     * @param exception $exception
     */
    private static function _report($exception = NULL)
    {
        self::_write_report($exception);

        if ((Kohana::$environment == Kohana::PRODUCTION) OR
            (Kohana::$environment == Kohana::STAGING) OR
            (Kohana::$environment == Kohana::TESTING)) {
            self::_send_report($exception);
        }
    }

    /**
     * Writes a briefly report to the log system.
     * @param exception $exception
     */
    private static function _write_report($exception = NULL)
    {
        Log::instance()->add(Log::INFO, '================================= [DALM][BEGIN] =================================');

        if ($exception !== NULL) {
            Log::instance()->add(Log::ERROR, '[DALM][PRIMARY CONNECTION][' . $_SERVER['HTTP_HOST'] . '][' . date('Y-m-d H:i:s') . ']: ' . $exception->getMessage() . ' ~ ' . $exception->getFile() . ' [ ' . $exception->getLine() . ' ]');
        }

        if (self::$primary_connection instanceof mysqli) {
            Log::instance()->add(Log::INFO, '[DALM][PRIMARY CONNECTION][' . $_SERVER['HTTP_HOST'] . '][' . date('Y-m-d H:i:s') . ']: Error (' . self::$primary_connection->errno . '): ' . self::$primary_connection->error);
        }

        if (isset(self::$failed_statements) AND is_array(self::$failed_statements)) {
            foreach (self::$failed_statements as $failed_statement) {
                Log::instance()->add(Log::INFO, '[DALM][FAILED STATEMENT][' . $_SERVER['HTTP_HOST'] . '][' . date('Y-m-d H:i:s') . ']: File: \'' . $failed_statement['file'] . '\' Statement: \'' . $failed_statement['statement'] . '\'');
            }
        }

        Log::instance()->add(Log::INFO, '================================= [DALM][ END ] =================================');
    }

    /**
     * Sends an email with a briefly report.
     * @param exception $exception
     */
    private static function _send_report($exception = NULL)
    {
        $dalm_configuration = Kohana::$config->load('config')->get('DALM');
        unset(Kohana::$config->_groups['config']);

        if (isset($dalm_configuration['notifications_email']) AND ($mail_to = $dalm_configuration['notifications_email']) !== '')
        {
            $jira_project_code = Settings::instance()->get('jira_project_code');
            $jira_project_code = ($jira_project_code == '') ? '' : '['.$jira_project_code.']';
            $subject = $jira_project_code.'[DALM][' . mb_strtoupper(PROJECTNAME) . ']['.(isset($_SERVER['KOHANA_ENV']) ? $_SERVER['KOHANA_ENV'] : 'Development').']';
            $message = '';

            if ($exception !== NULL) {
                $message .= '<b>EXCEPTION</b>' . PHP_EOL . PHP_EOL;
                $message .= '[DALM][PRIMARY CONNECTION][' . $_SERVER['HTTP_HOST'] . '][' . date('Y-m-d H:i:s') . ']: ' . $exception->getMessage() . ' ~ ' . $exception->getFile() . ' [ ' . $exception->getLine() . ' ]' . PHP_EOL . PHP_EOL;
                $message .= $exception->getTraceAsString() . PHP_EOL . PHP_EOL;
            }

            if (self::$primary_connection instanceof mysqli) {
                Log::instance()->add(Log::INFO, '[DALM][PRIMARY CONNECTION][' . $_SERVER['HTTP_HOST'] . '][' . date('Y-m-d H:i:s') . ']: Error (' . self::$primary_connection->errno . '): ' . self::$primary_connection->error . PHP_EOL);
            }

            if (isset(self::$failed_statements) AND is_array(self::$failed_statements)) {
                foreach (self::$failed_statements as $failed_statement) {
                    $message .= '[' . $_SERVER['HTTP_HOST'] . '][' . get_class() . '][FAILED STATEMENT][' . date('Y-m-d H:i:s') . ']: File: \'' . $failed_statement['file'] . '\' Statement: \'' . $failed_statement['statement'] . '\'' . PHP_EOL;
                }

                $message .= PHP_EOL . PHP_EOL;
            }

            try {
                //auto load is not active at this stage of kohana; load manually
                include_once  ENGINEPATH . 'plugins/messaging/development/classes/model/messaging.php';
                include_once  ENGINEPATH . 'plugins/messaging/development/classes/model/messagingrecipientprovider.php';

                $messaging = new Model_Messaging();
                $messaging->send_template(
                    'db-update-error',
                    null,
                    null,
                    array(array('target_type' => 'EMAIL', 'target' => $mail_to)),
                    array('error' => $message)
                );
            } catch (Exception $exc) {
                IbHelpers::send_email('support', $mail_to, NULL, NULL, $subject, preg_replace('/\n/', '<br/>', $message));
            }
        }
    }

    public static function db_audit($refresh)
    {
        set_time_limit(0);
        $fromCache = file_exists(APPPATH . '/cache/db_report.txt') && $refresh;
        if ($fromCache){
            $report = unserialize(file_get_contents(APPPATH . '/cache/db_report.txt'));
        } else {
            session_commit();//unlock session so enable browsing pages.
            proc_nice(19);//lowest process priority. it will take long time so do not slow the other processes.

            $models = self::_find_models();

            $installFiles = ENGINEPATH . 'application/install';
            foreach (scandir(ENGINEPATH . 'application/install') as $installFile) {
                $models[] = array(
                    'statements' => file_get_contents($installFiles . '/' . $installFile),
                    'file' => $installFiles . '/' . $installFile,
                    'type' => 'install'
                );
            }

            $tablesFromModels = array();
            $tablesInDb = array();
            foreach ($models as $i => $model) {
                preg_match_all('#create\s+table\s+(if\s+not\s+exists\s+)?([a-z0-9_\`]+)#i', $model['statements'],
                    $creates);
                //print_r($creates);
                foreach ($creates[1] as $n => $ifnot) {
                    $table = trim($creates[2][$n], '`');
                    if (!isset($tablesFromModels[$table])) {
                        $tablesFromModels[$table] = array('name' => $table, 'created' => array(), 'renamed' => array());
                    }
                    $tablesFromModels[$table]['created'][] = $model['file'];
                    $tablesFromModels[$table]['ifnot'] = $ifnot ? 'yes' : 'no';
                }
                preg_match_all('#alter\s+table\s+([a-z0-9_\`]+)\s+rename\s+to\s+([a-z0-9_\`]+)#i', $model['statements'],
                    $renames);
                //print_r($renames);
                foreach ($renames[1] as $n => $rename) {
                    $from = trim($rename, '`');
                    $to = trim($renames[2][$n], '`');
                    if (!isset($tablesFromModels[$from])) {
                        $tablesFromModels[$from] = array('name' => $from, 'created' => array(), 'renamed' => array());
                    }
                    if (!isset($tablesFromModels[$to])) {
                        $tablesFromModels[$to] = array('name' => $to);
                    }
                    $tablesFromModels[$from]['renamed'][] = $model['file'];
                    $tablesFromModels[$from]['renamed_to'] = $to;
                    $tablesFromModels[$to]['renamed'][] = $model['file'];
                    $tablesFromModels[$to]['renamed_from'] = $from;
                }
                //unset($models[$i]['statements']);
            }

            $tables = DB::query(Database::SELECT,
                "select * from information_schema.tables where table_schema = database() and TABLE_TYPE='BASE TABLE' order by TABLE_NAME ASC")->execute()->as_array();
            foreach ($tables as $table) {
                $createSql = DB::query(Database::SELECT,
                    "SHOW CREATE TABLE `" . $table['TABLE_NAME'] . "`")->execute()->current();
                $tablesInDb[$table['TABLE_NAME']] = array(
                    'name' => $table['TABLE_NAME'],
                    'engine' => $table['ENGINE'],
                    'data_size' => $table['DATA_LENGTH'],
                    'index_size' => $table['INDEX_LENGTH'],
                    'row' => $table['ROW_FORMAT'],
                    'sql' => $createSql['Create Table']
                );
            }
            //print_r($models);
            //print_r($tablesInDb);
            //print_r($tablesFromModels);
            $phpFiles = self::getAccessiblePhpFileList();
            $report = array();
            foreach ($tablesInDb as $table => $details) {
                $report[$table] = $details;
                $report[$table]['type'] = 'Table';
                $report[$table]['model'] = array_merge(@$tablesFromModels[$table]['created'] ? $tablesFromModels[$table]['created'] : array(),
                    @$tablesFromModels[$table]['renamed'] ? @$tablesFromModels[$table]['renamed'] : array());
                $report[$table]['usedByPhp'] = array();
                $report[$table]['usedBySql'] = array();
            }

            self::scanUsagesInSql($report, $models);
            //print_r($report);exit();
            self::scanUsagesInPhp($report, $phpFiles);
            //print_r($phpFiles);exit();
            file_put_contents(APPPATH . '/cache/db_report.txt', serialize($report));
        }
        foreach($report as $key => $object){
            if (strpos($object['name'], 'engine_dalm_') !== false) {
                continue;
            }

            $suggest = '';
            if (count($object['usedByPhp']) == 0) {
                $suggest .= "-- Unused by code\n";
                if ($object['type'] == 'Table') {
                    $suggest .= "DROP TABLE `" . $object['name'] . "`;\n";
                }
            } else {
                if (count($object['model']) == 0) {
                    $suggest .= "-- Missing Create\n";
                    if ($object['type'] == 'Table') {
                        $suggest .= $object['sql'] . "\n";
                    }
                }
                $firstModel = @$object['model'][0];
                if ($firstModel) {
                    if (strpos($firstModel, 'engine/application/model')) {
                        if (strpos($object['name'], 'engine_') !== 0) {
                            $suggest .= "-- Rename\n";
                            $suggest .= "-- engine/application/models/model.sql\n";
                            if ($object['type'] == 'Table') {
                                $suggest .= "ALTER TABLE `" . $object['name'] . "` RENAME TO `engine_" . $object['name'] . "`;\n";
                            }
                        }
                    }
                    if (strpos($firstModel, '/plugins/') !== false) {
                        $plugin = '';
                        if (preg_match('#/plugins/(.*?)/model#', $firstModel, $plugin)) {
                            $plugin = $plugin[1];
                            if (strpos($object['name'], 'plugin_' . $plugin) === false) {
                                $suggest .= "-- Rename\n";
                                if (strpos($firstModel, 'engine/plugins/') !== false) {
                                    $suggest .= "-- engine/plugins/" . $plugin . "/models/model.sql\n";
                                } else {
                                    $suggest .= "-- plugins/" . $plugin . "/models/model.sql\n";
                                }
                                if ($object['type'] == 'Table') {
                                    $rename = preg_replace('#^plugin(s)?_(.+?)_#', '', $object['name']);
                                    $suggest .= "ALTER TABLE `" . $object['name'] . "` RENAME TO `plugin_" . $plugin . '_' . $rename . "`;\n";
                                }
                            }
                        }
                    }
                }
            }
            $report[$key]['suggest'] = $suggest;
        }
        return $report;
    }

    public static function scanUsagesInSql(&$report, &$models)
    {
        foreach ($report as $table => $details) {
            foreach ($models as $model){
                $qname = preg_quote($table, '/');
                //if (preg_match('/(\`|\'|"|\s)' . $qname . '(\`|\'|"|\s)/', $model['statements'])) {
                if (stripos($model['statements'], $qname) !== false) {
                    $report[$table]['usedBySql'][] = $model['file'];
                }
            }
        }
    }

    public static function scanUsagesInPhp(&$report, &$files)
    {
        $lf = fopen("/tmp/x.log", "w+");
        $fileCount = count($files);
        $fn = 1;
        foreach($files as $file) {
            $content = file_get_contents($file);
            /*if(preg_match('/\w' . preg_quote($name, '/') . '\w/', $content)){
                $usedBy[] = $file;
            }*/
            foreach ($report as $table => $details) {
                $qname = preg_quote($table, '/');
                if (preg_match('/(\`|\'|"|\s)' . $qname . '(\`|\'|"|\s)/', $content)) {
                    $report[$table]['usedByPhp'][] = $file;
                }

            }
            unset($content);
            ++$fn;
            fwrite($lf, "$fn / $fileCount : $file\n");
        }
        fclose($lf);
    }

    public static function getAccessiblePhpFileList(
        $exclude = array('application/vendor',
            'system/3-2',
            'application/logs',
            'application/cache',
            '_tcpdf'))
    {
        $paths = Kohana::include_paths();
        $phpFiles = array();
        while($path = array_pop($paths)){
            $files = scandir($path);
            foreach($files as $file){
                if($file == '.' || $file == '..'){
                } else {
                    $file = $path . '/' . $file;
                    $file = str_replace('//', '/', $file);
                    $skip = false;
                    foreach($exclude as $ex){
                        if(stripos($path, $ex) !== false){
                            $skip = true;
                            break;
                        }
                    }
                    if($skip){
                        continue;
                    }

                    if(is_dir($file)){
                        array_push($paths, $file);
                    } else {
                        if(preg_match( '/.*\.php$/i',$file)){
                            $phpFiles[] = $file;
                        }
                    }
                }
            }
        }
        return $phpFiles;
    }

    public static function ignoreQuery($modelId, $query)
    {
        DB::insert(
            self::STATEMENTS_TABLE,
            array('model_id', 'md5', 'statement', 'executed', 'ignored')
        )->values(array(
            $modelId,
            md5($query),
            $query,
            null,
            date('Y-m-d H:i:s')
        ))->execute();
        Model_DALM::clear_error($modelId);
    }

    public static function replaceViewSqlSecurityDefiners()
    {
        $views = DB::select('*')
            ->from('information_schema.VIEWS')
            ->where('TABLE_SCHEMA', '=', DB::expr('DATABASE()'))
            ->execute()
            ->as_array();
        foreach ($views as $view) {
            DB::query(null, 'DROP VIEW `' . $view['TABLE_NAME'] . '`')->execute();
            DB::query(
                null,
                'CREATE SQL SECURITY INVOKER VIEW `' . $view['TABLE_NAME'] . '` AS ' . $view['VIEW_DEFINITION']
            )->execute();
        }
    }

    public static function replaceRoutineSqlSecurityDefiners()
    {
        $routines = DB::select('*')
            ->from('information_schema.ROUTINES')
            ->where('ROUTINE_SCHEMA', '=', DB::expr('DATABASE()'))
            ->execute()
            ->as_array();
        foreach ($routines as $routine) {
            $parameters = DB::select('*')
                ->from('information_schema.PARAMETERS')
                ->where('SPECIFIC_SCHEMA', '=', DB::expr('DATABASE()'))
                ->and_where('SPECIFIC_NAME', '=', $routine['ROUTINE_NAME'])
                ->and_where('PARAMETER_NAME', 'IS NOT', null)
                ->order_by('ORDINAL_POSITION', 'ASC')
                ->execute()
                ->as_array();

            $paramq = array();
            foreach ($parameters as $parameter) {
                $paramq[] = ($parameter['ROUTINE_TYPE'] == 'PROCEDURE' ? $parameter['PARAMETER_MODE'] : '') .
                    '`' . $parameter['PARAMETER_NAME'] . '` ' .
                    $parameter['DTD_IDENTIFIER'];
            }

            $create = 'CREATE ' . $routine['ROUTINE_TYPE'] . ' `' . $routine['ROUTINE_NAME'] . '`';
            $create .= '(' . implode(', ', $paramq) . ')';
            if ($routine['ROUTINE_TYPE'] == 'FUNCTION') {
                $create .= "\n RETURNS " . $routine['DTD_IDENTIFIER'];
            }
            $create .= "\n " . $routine['SQL_DATA_ACCESS'];
            if ($routine['IS_DETERMINISTIC'] == 'YES') {
                $create .= "\n DETERMINISTIC";
            }
            $create .= "\n SQL SECURITY INVOKER";
            $create .= "\n" . $routine['ROUTINE_DEFINITION'] . "\n";
            DB::query(null, 'DROP ' . $routine['ROUTINE_TYPE'] . ' ' . $routine['ROUTINE_NAME'])->execute();
            DB::query(null, $create)->execute();
        }
    }

    public static function replaceEventSqlSecurityDefiners()
    {
        $events = DB::select('*')
            ->from('information_schema.EVENTS')
            ->where('EVENT_SCHEMA', '=', DB::expr('DATABASE()'))
            ->execute()
            ->as_array();

        foreach ($events as $event) {
            $create = 'CREATE EVENT `' . $event['EVENT_NAME'] . '`';
            $create .= "\n ON SCHEDULE EVERY " . $event['INTERVAL_VALUE'] . " " . $event['INTERVAL_FIELD'];
            if (strtotime($event['STARTS'])) {
                $create .= " STARTS '" . $event['STARTS'] . "'";
            }
            $create .= "\n ON COMPLETION " . $event['ON_COMPLETION'];
            $create .= "\n " . ($event['STATUS'] == 'ENABLED' ? 'ENABLE' : 'DISABLE');
            $create .= "\nDO";
            $create .= "\n" . $event['EVENT_DEFINITION'] . "\n";
            DB::query(null, 'DROP EVENT ' . $event['EVENT_NAME'])->execute();
            DB::query(null, $create)->execute();
        }
    }
}

/**
 * Class Model_DALM_Exception
 */
class Model_DALM_Exception extends Exception
{
    const UNABLE_TO_CONNECT_TO_MYSQL = 0x01;
    const UNABLE_TO_CREATE_REQUIRED_DATABASE = 0x02;
    const UNABLE_TO_SELECT_REQUIRED_DATABASE = 0x03;
    const UNABLE_TO_CREATE_REQUIRED_TABLES = 0x04;
    const UNABLE_TO_ACQUIRE_LOCK = 0x05;
    const UNABLE_TO_RELEASE_LOCK = 0x06;
    const UNABLE_TO_CREATE_MODEL_OBJECT = 0x07;
    const UNABLE_TO_REGISTER_MODELS = 0x08;
    const UNABLE_TO_GET_PROCESSED_MODELS = 0x09;
    const UNABLE_TO_GET_MODEL_ID = 0x0A;
    const UNABLE_TO_SET_MODEL_STATUS = 0x0B;
    const UNABLE_TO_GET_PREVIOUS_STATEMENTS = 0x0C;
    const UNABLE_TO_REGISTER_STATEMENTS = 0x0D;

    public function __construct($message, $exception_code)
    {
        parent::__construct(get_class() . ': ' . $message, $exception_code);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_CONNECT_TO_MYSQL_Exception
 */
final class Model_DALM_UNABLE_TO_CONNECT_TO_MYSQL_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to connect to MySQL.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_CONNECT_TO_MYSQL);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_CREATE_REQUIRED_DATABASE_Exception
 */
final class Model_DALM_UNABLE_TO_CREATE_REQUIRED_DATABASE_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to create the required database.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_CREATE_REQUIRED_DATABASE);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_SELECT_REQUIRED_DATABASE_Exception
 */
final class Model_DALM_UNABLE_TO_SELECT_REQUIRED_DATABASE_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to select the required database.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_SELECT_REQUIRED_DATABASE);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_CREATE_REQUIRED_TABLES_Exception
 */
final class Model_DALM_UNABLE_TO_CREATE_REQUIRED_TABLES_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to create the required tables.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_CREATE_REQUIRED_TABLES);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_ACQUIRE_LOCK_Exception
 */
final class Model_DALM_UNABLE_TO_ACQUIRE_LOCK_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to acquire lock.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_ACQUIRE_LOCK);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_RELEASE_LOCK_Exception
 */
final class Model_DALM_UNABLE_TO_RELEASE_LOCK_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to release lock.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_RELEASE_LOCK);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception
 */
final class Model_DALM_UNABLE_TO_CREATE_MODEL_OBJECT_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to create new model object.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_CREATE_MODEL_OBJECT);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_REGISTER_MODELS_Exception
 */
final class Model_DALM_UNABLE_TO_REGISTER_MODELS_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to register models.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_REGISTER_MODELS);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_GET_PROCESSED_MODELS_Exception
 */
final class Model_DALM_UNABLE_TO_GET_PROCESSED_MODELS_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to get processed models.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_GET_PROCESSED_MODELS);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception
 */
final class Model_DALM_UNABLE_TO_GET_MODEL_ID_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to get model id.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_GET_MODEL_ID);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_SET_MODEL_STATUS_Exception
 */
final class Model_DALM_UNABLE_TO_SET_MODEL_STATUS_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to set model status.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_SET_MODEL_STATUS);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_GET_PREVIOUS_STATEMENTS_Exception
 */
final class Model_DALM_UNABLE_TO_GET_PREVIOUS_STATEMENTS_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to get previous statements.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_GET_PREVIOUS_STATEMENTS);
    }
}

/**
 * Class Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception
 */
final class Model_DALM_UNABLE_TO_REGISTER_STATEMENTS_Exception extends Model_DALM_Exception
{
    protected $message = 'Unable to register statements.';

    public function __construct()
    {
        parent::__construct($this->message, Model_DALM_Exception::UNABLE_TO_REGISTER_STATEMENTS);
    }
}

