<?php defined('SYSPATH') or die('No direct script access.');

class Model_Localisation extends Model
{
	public static function language_add($code, $title)
	{
		$existing_id = self::language_get_id($code);
		if ($existing_id) {
			return array($existing_id);
		} else {
			return DB::insert('engine_localisation_languages', array('code', 'title', 'created_on', 'updated_on'))
					->values(array($code, $title, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')))
					->execute();
		}
	}
	
	public static function language_remove($id)
	{
		return DB::delete('engine_localisation_languages')->where('id', '=', $id)->execute();
	}
	
	public static function language_get_id($code)
	{
		static $cache = array();
        $database = isset(Kohana::$config->load('database')->site_db) ? 'site_db' : NULL;
		if(!isset($cache[$database.'_'.$code])){
			$id = DB::select('id')->from('engine_localisation_languages')->where('code', '=', $code)->execute($database)->get('id');
			if($id){
				$cache[$database.'_'.$code] = $id;
			}
		}
		return @$cache[$database.'_'.$code];
	}

	public static function languages_list()
	{
		$languages = DB::select('*')->from('engine_localisation_languages')->order_by('updated_on', 'desc')->execute()->as_array();
		return $languages;
	}
	
	public static function get_languages_list_options($selected = null)
	{
		$languages = DB::select('*')->from('engine_localisation_languages')->order_by('updated_on', 'desc')->execute()->as_array();
		$options = '<option value="" ' . ($selected == '' ? 'selected="selected"' : '') . '>' . __('Select from Login') . '</option>';
		foreach($languages as $language){
			$options .= '<option value="' . $language['code'] . '"' . ($selected == $language['code'] ? 'selected="selected"' : '') . '>' . $language['title'] . '</option>';
		}
		return $options;
	}
	
	public static function languages_list_codes()
	{
		$codes = array();
		foreach(DB::select('*')->from('engine_localisation_languages')->order_by('updated_on', 'desc')->execute()->as_array() as $lang){
			$codes[] = $lang['code'];
		}
		
		return $codes;
	}
	
	public static function ctag_add($ctag, $language)
	{
		return DB::insert('settings_localisation_ctags', array('ctag', 'language', 'created_on', 'updated_on'))
					->values(array($ctag, $language, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')))
					->execute();
	}
	
	public static function ctag_remove($id)
	{
		return DB::delete('settings_localisation_ctags')->where('id', '=', $id)->execute();
	}

	public static function ctags_list()
	{
		$ctags = DB::select('*')->from('settings_localisation_ctags')->order_by('updated_on', 'desc')->execute()->as_array();
		return $ctags;
	}
	
	public static function split_by_locale_tag($content)
	{
		$offset = 0;
		$translations = array();
		while(preg_match('#(<[^>]+>\s*)?\[locale:([a-z\-]+)\](\s*</[^>]+>)?#is', $content, $locales, PREG_OFFSET_CAPTURE, $offset)){
			$translation = array('offset' => $locales[0][1], 'lang' => $locales[2][0], 'tag' => $locales[0][0]);
			$translations[] = $translation;
			$offset = $locales[0][1] + strlen($locales[0][0]);
		}
		
		$last_translation = count($translations) - 1;
		for($i = 0 ; $i <= $last_translation ; ++$i){
			if($i < $last_translation){
				$translations[$i]['translation'] = substr($content, $translations[$i]['offset'] + strlen($translations[$i]['tag']), $translations[$i + 1]['offset'] - ($translations[$i]['offset'] + strlen($translations[$i]['tag'])));
			} else {
				$translations[$i]['translation'] = substr($content, $translations[$i]['offset'] + strlen($translations[$i]['tag']));
			}
		}
		return $translations;
	}

	public static function get_ctag_translation($content, $language = null)
	{
        if (!$language) {
            $language = I18n::$lang;
        }

		$translations = self::split_by_locale_tag($content);
		foreach($translations as $translation){
			if(strtolower($language) == strtolower($translation['lang'])){
				return $translation['translation'];
			}
		}
		if(isset($translations[0])){
			return $translations[0]['translation'];
		} else {
			return $content;
		}
	}
	
	public static function message_add($message)
	{
		$exists = self::message_get($message);
		if($exists){
			return $exists[0]['id'];
		} else {
			$result = DB::insert('engine_localisation_messages', array('message', 'created_on', 'updated_on'))
							->values(array($message, date('Y-m-h H:i:s'), date('Y-m-h H:i:s')))
							->execute();
			return $result[0];
		}
	}
	
	public static function message_remove($id)
	{
		return DB::delete('engine_localisation_messages')->where('id', '=', $id)->execute();
	}

	public static function message_get($message)
	{
		return DB::select('*')->from('engine_localisation_messages')->where('message', '=', $message)->execute()->as_array();
	}
	
	public static function message_get_id($message)
	{
		static $cache = array();
		if(!isset($cache[$message])){
			$id = DB::select('id')->from('engine_localisation_messages')->where('message', '=', $message)->execute()->get('id');
			if($id){
				$cache[$message] = $id;
			}
		}
		return @$cache[$message];
	}
	
	public static function message_list()
	{
		$messages = DB::select('*')->from('engine_localisation_messages')->order_by('updated_on', 'desc')->execute()->as_array();
		return $messages;
	}
	
	
	public static function message_scan_file($file)
	{
		try{
			$content = @file_get_contents($file);
		} catch(Exception $exc){
			return array();
		}
		$tokens = token_get_all($content);
		//print_r($tokens);
		$func_names = array('_', '__');
		$messages = array();
		$state = 0;
		foreach($tokens as $token){
			if($state == 0){
				if(is_array($token) && $token[0] == T_STRING && in_array($token[1], $func_names)){
					$state = 1;
				}
			}
			if($state == 1){
				if(is_array($token)){
					if($token[0] == T_WHITESPACE){
						continue;
					} else if($token[0] == T_CONSTANT_ENCAPSED_STRING){
						$messages[] = trim(stripslashes($token[1]), "\"'");
						$state = 0;
					}
				} else {
					if($token == '('){
						continue;
					}
				}
			}
		}
		
		return $messages;
	}

	public static function message_scan($exclude = array('application/vendor'))
	{
		ini_set('max_execution_time', 0);
		session_commit();

		try{
			Database::instance()->begin();
			$paths = Kohana::include_paths();
			$scanned_files = array();
			while($path = array_pop($paths)){
                if (file_exists($path)) {
                    $files = scandir($path);
                    foreach($files as $file){
                        if($file == '.' || $file == '..'){
                        } else {
                            $file = $path . '/' . $file;
                            $scan = true;
                            foreach ($exclude as $exludePath) {
                                if (stripos($file, $exludePath) !== false) {
                                    $scan = false;
                                    break;
                                }
                            }
                            if (!$scan) {
                                continue;
                            }
                            if(is_dir($file)){
                                array_push($paths, $file);
                            } else {
                                if(preg_match( '/.*\.php$/i',$file) && !isset($scanned_files[$file])){
                                    $scanned_files[$file] = true;
                                    $messages = self::message_scan_file($file);
                                    foreach($messages as $message){
                                        self::message_add($message);
                                    }
                                }
                            }
                        }
                    }
                }
			}
			
			$custom_scanners = DB::select('*')->from('engine_localisation_custom_scanners')->execute()->as_array();
			foreach($custom_scanners as $custom_scanner){
				try{
					$messages = call_user_func($custom_scanner['scanner']);
					foreach($messages as $message){
						self::message_add($message);
					}
				} catch(Exception $exc) {
				}
			}
			Database::instance()->commit();
			return true;
        } catch(Exception $e) {
			Database::instance()->rollback();
			throw $e;
        }
	}
	
	public static function translation_set($language_id, $message_id, $translation)
	{
		$data = array('language_id' => $language_id,
						'message_id' => $message_id,
						'translation' => $translation);
		$exists = DB::select('*')
						->from('engine_localisation_translations')
						->where('language_id', '=', $language_id)
						->and_where('message_id', '=', $message_id)
						->execute()
						->as_array();
		if(count($exists)){
			$id = $exists[0]['id'];
			$result = DB::update('engine_localisation_translations')
							->set($data)
							->where('id', '=', $exists[0]['id'])
							->execute();
		} else {
			$result = DB::insert('engine_localisation_translations', array_keys($data))
							->values($data)
							->execute();
			$id = $result[0];
		}
		if($result){
			return $id;
		} else {
			return false;
		}
	}
	
	public static function translation_set2($language, $message, $translation)
	{
		$language_id = self::language_get_id($language);
		$message_id = self::message_get_id($message);
		if(!$message_id){
			$message_id = self::message_add($message);
		}
		if($language_id && $message_id){
			return self::translation_set($language_id, $message_id, $translation);
		} else {
			return false;
		}
	}
	
	public static function translations_bulk_update($changes)
	{
		foreach($changes as $name => $translation){
			preg_match('/translation\[(\d+)\]\[(\d+)\]/', $name, $language_message);
			$language_id = $language_message[1];
			$message_id = $language_message[2];
			self::translation_set($language_id, $message_id, $translation);
		}
	}
	
	public static function translations_load($lang)
	{
		$language_id = self::language_get_id($lang);
		if($language_id)
        {
            $database = isset(Kohana::$config->load('database')->site_db) ? 'site_db' : NULL;

			$result = DB::select('messages.message', 'translations.translation')
							->from(array('engine_localisation_messages', 'messages'))
								->join(array('engine_localisation_translations', 'translations'))->on('messages.id', '=', 'translations.message_id')->on('translations.language_id', '=', DB::expr($language_id))
							->execute($database)
							->as_array();
			$table = array();
			foreach($result as $tr){
				$table[$tr['message']] = $tr['translation'];
			}
			return $table;
		} else {
			return false;
		}
	}
	
	public static function get_translations_datatable($filters)
	{
		$languages = self::languages_list();
		$scolumns = array(0 => 'messages.id', 'messages.message');
		$select_params = array(DB::expr('SQL_CALC_FOUND_ROWS messages.id'), 'messages.message');
        foreach($languages as $language){
			$scolumns[] = 'translations_' . $language['code'] . '.translation';
			$select_params[] = DB::expr('translations_' . $language['code'] . '.translation as translation_' . $language['code']);
		}
		
		$query = call_user_func_array('DB::select', $select_params);
		$query->from(array('engine_localisation_messages', 'messages'));
		foreach($languages as $language){
			$query->join(array('engine_localisation_translations', 'translations_' . $language['code']), 'left')->on('messages.id', '=', 'translations_' . $language['code'] . '.message_id')->on('translations_' . $language['code'] . '.language_id', '=', DB::expr($language['id']));
		}
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$query->and_where_open();
			for ($i = 0; $i < count($scolumns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $scolumns[$i] != '')
				{
					if ($scolumns[$i] == 'messages.message') {
						$query->or_where(DB::expr($scolumns[$i] . ' collate utf8_general_ci '), 'like',
								'%' . $filters['sSearch'] . '%');
					} else {
						$query->or_where($scolumns[$i], 'like', '%' . $filters['sSearch'] . '%');
					}
				}
			}
			$query->and_where_close();
		}
		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$query->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$query->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'] != '')
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($scolumns[$filters['iSortCol_'.$i]] != '')
				{
					$query->order_by($scolumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$query->order_by('messages.message', 'desc');
		$results = $query->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['sEcho'] = intval($filters['sEcho']);
		
		$output['aaData'] = array();
		foreach($results as $result)
		{
			$row   = array();
            if (@$filters['noinput']) {
                $row['default'] = $result['message'];
            } else {
                $row[] = $result['id'];
                $row[] = $result['message'];
            }
			foreach($languages as $language){
				if (@$filters['noinput']) {
					$row[$language['code']] = $result['translation_' . $language['code']];
				} else {
					$row[] = '<input type="text" data-language-id="' . $language['id'] . '" data-message-id="' . $result['id'] . '" name="translation[' . $language['id'] . '][' . $result['id'] . ']" value="' . htmlspecialchars($result['translation_' . $language['code']]) . '"/>';
				}
			}
			$output['aaData'][] = $row;
		}

        return $output;
	}
	
	public static function userAgentPreferedLanguages()
	{
		if( !isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ){
			return array();
		} else {
			$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			$pLangs = array();
			foreach( $langs as $lang ){
				$lang=explode( ';q=', $lang );
				if( count( $lang ) == 2 ){
					$pLangs[$lang[0]] = $lang[1];
				} else {
					$pLangs[$lang[0]] = 1;
				}
			}
		}
		return $pLangs;
	}
	
	public static function preferedLanguage( $supportedLanguages, $def = null )
	{
		if($def == null){
			$uri = Request::$current->uri();
			if(strpos($uri, '/admin') !== false){
				$def = Settings::instance()->get('localisation_system_default_language');
			} else {
				$def = Settings::instance()->get('localisation_content_default_language');
			}
		}
		$pLangs = self::userAgentPreferedLanguages();
		$fLang="";
		$fPoint=0;
		
		//look up fully matched preferred languages
		// en=en; en-us=en-us,tr=tr
		foreach( $pLangs as $pLang => $point ){
			if( array_search( $pLang, $supportedLanguages ) !== false ){
				if( $point > $fPoint ){
					$fLang = $pLang;
					$fPoint = $point;
				}
			}
		}
		
		//look up partially matched preferred languages
		//en:en-us,en:en-uk,tr:tr-tr,en-uk:en-us
		if( $fPoint == 0 ){
			foreach( $pLangs as $pLang => $point ){
				$p = strpos( $pLang, '-' );
				if( $p !== false ){
					$pLang = substr( $pLang, 0, $p );
				}
				foreach( $supportedLanguages as $sLang )
				{
					$p = strpos( $sLang, '-' );
					if($p !== false ){
						$sLang2 = substr( $sLang, 0, $p );
						if( $sLang2 == $pLang ){
							if( $point > $fPoint ){
								$fLang = $sLang;
								$fPoint = $point;
							}
						}
					}
				}
			}
		}
		
		if( $fPoint == 0 ){
			return $def;
		} else {
			return $fLang;
		}
	}

	public static function clearAll()
	{
		DB::query(null, 'TRUNCATE engine_localisation_translations')->execute();
		DB::query(null, 'TRUNCATE engine_localisation_messages')->execute();
		DB::query(null, 'TRUNCATE engine_localisation_languages')->execute();
	}
}
?>