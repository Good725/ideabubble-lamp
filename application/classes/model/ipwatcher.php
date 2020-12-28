<?php defined('SYSPATH') or die('No direct script access.');

	/*this is for 5.3 compatibility*/
	if (!interface_exists('JsonSerializable')) {
		interface JsonSerializable
		{
			public function jsonSerialize();
		}
	}

	require_once APPPATH . '/vendor/geoip2/geoip2/src/ProviderInterface.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Exception/GeoIp2Exception.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Exception/AuthenticationException.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Exception/OutOfQueriesException.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Exception/HttpException.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Exception/InvalidRequestException.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Exception/AddressNotFoundException.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Database/Reader.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/AbstractModel.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/Domain.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/Country.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/City.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/Insights.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/Isp.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/AnonymousIp.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Model/ConnectionType.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/WebService/Client.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/AbstractRecord.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/AbstractPlaceRecord.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/Subdivision.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/Traits.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/Location.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/Country.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/Postal.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/City.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/RepresentedCountry.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/Continent.php';
	require_once APPPATH . '/vendor/geoip2/geoip2/src/Record/MaxMind.php';
	require_once APPPATH . '/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/Decoder.php';
	require_once APPPATH . '/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/Metadata.php';
	require_once APPPATH . '/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/InvalidDatabaseException.php';
	require_once APPPATH . '/vendor/maxmind-db/reader/src/MaxMind/Db/Reader/Util.php';
	require_once APPPATH . '/vendor/maxmind-db/reader/src/MaxMind/Db/Reader.php';

class Model_Ipwatcher extends Model
{
	public static $blacklist_file = '/tmp/ip_blacklist';

	protected static $gethostbyaddr_cache = array();
	protected static $location_by_ip_cache = array();
	
	public static function save_log($ip = null, $uri = null, $requested = null)
	{
		if($ip == null){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if($uri == null){
			$uri = $_SERVER['REQUEST_URI'];
		}
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		} else {
			$user_agent = 'Unknown';
		}
		if($requested == null){
			$requested = time();
		}
		DB::insert('engine_ipwatcher_log_tmp', array('ip','user_agent', 'uri', 'requested'))->values(array(ip2long($ip),$user_agent, $uri, $requested))->execute();
	}
	
	public static function check_thresholds($ip = null, $requested = null, $uri = null)
	{
		if($ip == null){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if($requested == null){
			$requested = time();
		}
		if($uri == null){
			$uri = $_SERVER['REQUEST_URI'];
		}
		
		$treshold_minute = Settings::instance()->get('ipwatcher_treshold_minute');
		if($treshold_minute > 0){
			$cnt = DB::select(DB::expr('count(*) as cnt'))
						->from('engine_ipwatcher_log_tmp')
						->where('ip', '=', ip2long($ip))
						->execute()
						->get('cnt');
			if($cnt > $treshold_minute){
				return array('OK' => false, 'reason' => 'Threshold Minute Reached');
			}
		}
		
		$treshold_hour = Settings::instance()->get('ipwatcher_treshold_hour');
		if($treshold_hour > 0){
			$cnt = DB::select(DB::expr('count(*) as cnt'))
						->from('engine_ipwatcher_log')
						->where('ip', '=', ip2long($ip))
						->and_where('requested', '>=', time() - 3600)
						->execute()
						->get('cnt');
			if($cnt > $treshold_hour){
				return array('OK' => false, 'reason' => 'Threshold Hour Reached');
			}
		}
		
		$threshold_day = Settings::instance()->get('ipwatcher_treshold_day');
		if($threshold_day > 0){
			$cnt = DB::select(DB::expr('count(*) as cnt'))
						->from('engine_ipwatcher_log')
						->where('ip', '=', ip2long($ip))
						->and_where('requested', '>=', time() - 86400)
						->execute()
						->get('cnt');
			if($cnt > $threshold_day){
				return array('OK' => false, 'reason' => 'Threshold Day Reached');
			}
		}

		$treshold_minute_url = Settings::instance()->get('ipwatcher_treshold_minute_url');
		if($treshold_minute_url > 0){
			$cnt = DB::select(DB::expr('count(*) as cnt'))
					->from('engine_ipwatcher_log_tmp')
					->where('ip', '=', ip2long($ip))
                    ->and_where('uri', '=', $uri)
					->execute()
					->get('cnt');
			if($cnt > $treshold_minute_url){
				return array('OK' => false, 'reason' => 'Threshold Minute Same Url Reached');
			}
		}

		$treshold_hour_url = Settings::instance()->get('ipwatcher_treshold_hour_url');
		if($treshold_hour_url > 0){
			$cnt = DB::select(DB::expr('count(*) as cnt'))
					->from('engine_ipwatcher_log')
					->where('ip', '=', ip2long($ip))
					->and_where('requested', '>=', time() - 3600)
                    ->and_where('uri', '=', $uri)
					->execute()
					->get('cnt');
			if($cnt > $treshold_hour_url){
				return array('OK' => false, 'reason' => 'Threshold Hour Same Url Reached');
			}
		}

		$threshold_day_url = Settings::instance()->get('ipwatcher_treshold_day_url');
		if($threshold_day_url > 0){
			$cnt = DB::select(DB::expr('count(*) as cnt'))
					->from('engine_ipwatcher_log')
					->where('ip', '=', ip2long($ip))
					->and_where('requested', '>=', time() - 86400)
                    ->and_where('uri', '=', $uri)
					->execute()
					->get('cnt');
			if($cnt > $threshold_day_url){
				return array('OK' => false, 'reason' => 'Threshold Day Same Url Reached');
			}
		}
		
		return array('OK' => true, 'reason' => null);
	}

	public static function save_check()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$uri = $_SERVER['REQUEST_URI'];
		$requested = time();

		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$ua_whitelist = DB::select('*')->from('engine_ipwatcher_ua_whitelist')->execute()->as_array();
			foreach($ua_whitelist as $user_agent){
				if(stripos($_SERVER['HTTP_USER_AGENT'], $user_agent['user_agent']) !== false){
					self::save_log($ip, $uri, $requested);
					return true;
				}
			}
		}

		if(DB::select(DB::expr('count(*) as cnt'))->from('engine_ipwatcher_blacklist')->where('ip', '=', ip2long($ip))->execute()->get('cnt') == 0){
			self::save_log($ip, $uri, $requested);
			
			if(DB::select(DB::expr('count(*) as cnt'))->from('engine_ipwatcher_whitelist')->where('ip', '=', ip2long($ip))->execute()->get('cnt') == 0){
				$check = self::check_thresholds($ip, $requested, $uri);
				if(!$check['OK']){
					self::gethostbyaddr(ip2long($ip));
					self::block($ip, null, $check['reason']);
				}
				return $check['OK'];
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public static function gethostbyaddr($ip)
	{
		$host = @self::$gethostbyaddr_cache[$ip];
		if($host == null){
			$host = gethostbyaddr(long2ip($ip));
			self::$gethostbyaddr_cache[$ip] = $host;
		}
		DB::update('engine_ipwatcher_log_tmp')
			->set(array('gethostbyaddr' => $host))
			->where('ip', '=', $ip)->execute();
	}
	
	public static function location_by_ip($ip)
	{
		if(!file_exists(Kohana::$cache_dir . '/geoip/GeoIP2-City.mmdb')){
			return;
		}
		$location = @self::$location_by_ip_cache[$ip];
		if($location == null){
			$location = $ip;
			self::$location_by_ip_cache[$ip] = $location;
		}
		
		DB::update('engine_ipwatcher_log_tmp')
			->set(array('location_by_ip' => $location))
			->where('ip', '=', $ip)->execute();
	}
	
	public static function block($ip, $blocked_by, $reason)
	{
		self::$blacklist_file = Settings::instance()->get('ipwatcher_blacklist_file');
		DB::insert('engine_ipwatcher_blacklist', array('ip', 'blocked', 'blocked_by', 'reason', 'gethostbyaddr', 'location_by_ip'))
			->values(array(ip2long($ip), date('Y-m-d H:i:s'), $blocked_by, $reason, gethostbyaddr($ip), self::get_geoip($ip)))
			->execute();
		file_put_contents(self::$blacklist_file, $ip . "\n", FILE_APPEND);
	}
	
	public static function unblock($ip)
	{
		self::$blacklist_file = Settings::instance()->get('ipwatcher_blacklist_file');
		DB::delete('engine_ipwatcher_blacklist')
			->where('ip', '=', ip2long($ip))
			->execute();
		$blocks = file(self::$blacklist_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach($blocks as $i => $bip){
			$bip = trim($bip);
			if($bip == $ip){
				unset($blocks[$i]);
			}
		}
		file_put_contents(self::$blacklist_file, implode("\n", $blocks) . "\n", LOCK_EX);
	}
	
	public static function get_log_datatable($filters)
	{
		$scolumns = array(0 => 'ip','user_agent', 'uri', 'requested', 'gethostbyaddr', 'location_by_ip');

        $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS id'), 'ip','user_agent', 'uri', 'requested', 'gethostbyaddr', 'location_by_ip')
		            ->from('engine_ipwatcher_log');
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			if(preg_match('/\d+\.\d+\.\d+\.\d+/', $filters['sSearch'])){
				$query->where('ip', '=', ip2long($filters['sSearch']));
			} else {
				$query->and_where_open();
				for ($i = 0; $i < count($scolumns); $i++)
				{
					if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $scolumns[$i] != '')
					{
						$query->or_where($scolumns[$i],'like','%'.$filters['sSearch'].'%');
					}
				}
				$query->and_where_close();
			}
		}
		if (!isset($filters['iDisplayLength']) || @$filters['iDisplayLength'] == -1) { // "ALL" is dangerous. Hangs browsers when 1000s logs.
			$filters['iDisplayLength'] = 100;
		}
		// Limit. Only show the number of records for this paginated page
        $query->limit(intval($filters['iDisplayLength']));
        if (isset($filters['iDisplayStart']))
        {
            $query->offset(intval($filters['iDisplayStart']));
        }
		// Order
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($scolumns[$filters['iSortCol_'.$i]] != '')
				{
					$query->order_by($scolumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$query->order_by('requested', 'desc');
	
        $results = $query->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['sEcho'] = intval($filters['sEcho']);
		
		$output['aaData'] = array();
		foreach($results as $result)
		{
			$row   = array();
			$row[] = long2ip($result['ip']);
			$row[] = $result['user_agent'];
			$row[] = $result['uri'];
			$row[] = date('Y-m-d H:i:s', $result['requested'] );
			$row[] = $result['gethostbyaddr'];
			$row[] = $result['location_by_ip'];
			$output['aaData'][] = $row;
		}
        return json_encode($output);
	}

	public static function get_blacklist_datatable($filters)
	{
		$scolumns = array(0 => 'ip', 'blocked', 'gethostbyaddr', 'location_by_ip', 'reason');

        $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS id'), 'ip', 'blocked', 'gethostbyaddr', 'location_by_ip', 'reason')
		            ->from('engine_ipwatcher_blacklist');
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$query->and_where_open();
			for ($i = 0; $i < count($scolumns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $scolumns[$i] != '')
				{
					$query->or_where($scolumns[$i],'like','%'.$filters['sSearch'].'%');
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
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($scolumns[$filters['iSortCol_'.$i]] != '')
				{
					$query->order_by($scolumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$query->order_by('blocked', 'desc');
	
        $results = $query->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['sEcho'] = intval($filters['sEcho']);
		
		$output['aaData'] = array();
		foreach($results as $result)
		{
			$row   = array();
			$row[] = long2ip($result['ip']);
			$row[] = $result['blocked'];
			$row[] = $result['gethostbyaddr'];
			$row[] = $result['location_by_ip'];
			$row[] = $result['reason'];
			$row[] = '<a href="/admin/settings/ipwatcher_unblock?ip=' . long2ip($result['ip']) . '" onclick="return confirm(\'Are you sure you want to unblock\')">unblock</a>';
			$output['aaData'][] = $row;
		}

        return json_encode($output);
	}

	public static function update_geoip_db()
	{
		//Kohana::$cache_dir . '/geoip/GeoIP2-City.mmdb'
		if(!file_exists(Kohana::$cache_dir . '/geoip/')){
			mkdir(Kohana::$cache_dir . '/geoip/');
		}
		$cu = curl_init('http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz');
		curl_setopt($cu, CURLOPT_HEADER, false);
		$tmp_fd = fopen(Kohana::$cache_dir . '/geoip/tmp.db.gz', 'wb');
		curl_setopt($cu, CURLOPT_FILE, $tmp_fd);
		curl_exec($cu);
		$inf = curl_getinfo($cu);
		curl_close($cu);
		fclose($tmp_fd);
		if(@$inf['http_code'] == '200'){
			$gz = gzopen(Kohana::$cache_dir . '/geoip/tmp.db.gz', 'rb');
			$dest = fopen(Kohana::$cache_dir . '/geoip/GeoLite2-City.mmdb', 'wb');
			stream_copy_to_stream($gz, $dest);
			gzclose($gz);
			fclose($dest);
			return true;
		} else {
			return false;
		}
	}
	
	public static function geoip_db_status()
	{
		return array('file' => Kohana::$cache_dir . '/geoip/GeoLite2-City.mmdb', 'updated' => @filemtime(Kohana::$cache_dir . '/geoip/GeoLite2-City.mmdb'));
	}
	
	public static function get_geoip($ip)
	{
			try{
				$geoip_reader = new GeoIp2\Database\Reader(Kohana::$cache_dir . '/geoip/GeoLite2-City.mmdb');
				$record = $geoip_reader->city($ip);
				return $record->country->name . ' ' . $record->city->name;
			} catch(Exception $exc){
				return '';
			}
	}
	
	public static function whitelist_add($ip, $allowed_by, $reason)
	{
		self::unblock($ip);
		DB::insert('engine_ipwatcher_whitelist', array('ip', 'allowed', 'allowed_by', 'reason'))
			->values(array(ip2long($ip), date('Y-m-d H:i:s'), $allowed_by, $reason))
			->execute();
	}
	
	public static function whitelist_remove($ip)
	{
		DB::delete('engine_ipwatcher_whitelist')
			->where('ip', '=', ip2long($ip))
			->execute();
	}
	
	public static function get_whitelist_datatable($filters)
	{
		$scolumns = array(0 => 'ip', 'allowed', 'reason');

        $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS id'), 'ip', 'allowed', 'reason')
		            ->from('engine_ipwatcher_whitelist');
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$query->and_where_open();
			for ($i = 0; $i < count($scolumns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $scolumns[$i] != '')
				{
					$query->or_where($scolumns[$i],'like','%'.$filters['sSearch'].'%');
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
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($scolumns[$filters['iSortCol_'.$i]] != '')
				{
					$query->order_by($scolumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$query->order_by('allowed', 'desc');
	
        $results = $query->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['sEcho'] = intval($filters['sEcho']);
		
		$output['aaData'] = array();
		foreach($results as $result)
		{
			$row   = array();
			$row[] = long2ip($result['ip']);
			$row[] = $result['allowed'];
			$row[] = $result['reason'];
			$row[] = '<a href="/admin/settings/ipwatcher_whitelist_remove?ip=' . long2ip($result['ip']) . '" onclick="return confirm(\'Are you sure you want to remove\')">remove</a>';
			$output['aaData'][] = $row;
		}

        return json_encode($output);
	}
	
	public static function ua_whitelist_add($user_agent, $allowed_by, $reason)
	{
		DB::insert('engine_ipwatcher_ua_whitelist', array('user_agent', 'allowed', 'allowed_by', 'reason'))
			->values(array($user_agent, date('Y-m-d H:i:s'), $allowed_by, $reason))
			->execute();
	}
	
	public static function ua_whitelist_remove($user_agent)
	{
		DB::delete('engine_ipwatcher_ua_whitelist')
			->where('user_agent', '=', $user_agent)
			->execute();
	}
	
	public static function get_ua_whitelist_datatable($filters)
	{
		$scolumns = array(0 => 'user_agent', 'allowed', 'reason');

        $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS id'), 'user_agent', 'allowed', 'reason')
		            ->from('engine_ipwatcher_ua_whitelist');
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$query->and_where_open();
			for ($i = 0; $i < count($scolumns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $scolumns[$i] != '')
				{
					$query->or_where($scolumns[$i],'like','%'.$filters['sSearch'].'%');
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
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($scolumns[$filters['iSortCol_'.$i]] != '')
				{
					$query->order_by($scolumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$query->order_by('allowed', 'desc');
	
        $results = $query->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['sEcho'] = intval($filters['sEcho']);
		
		$output['aaData'] = array();
		foreach($results as $result)
		{
			$row   = array();
			$row[] = $result['user_agent'];
			$row[] = $result['allowed'];
			$row[] = $result['reason'];
			$row[] = '<a href="/admin/settings/ipwatcher_ua_whitelist_remove?user_agent=' . urlencode( $result['user_agent'] ) . '" onclick="return confirm(\'Are you sure you want to remove\')">remove</a>';
			$output['aaData'][] = $row;
		}

        return json_encode($output);
	}

	public static function is_ignored($action)
	{
		$actions_to_check = DB::select('*')->from('engine_ipwatcher_ignore_actions')->execute()->as_array();
		foreach ($actions_to_check as $check) {
			if (preg_match('#' . preg_quote($check['action'], "#") .'#', $action)) {
				return true;
			}
		}
		return false;
	}
}
?>
