<?php
class Model_PageRedirect extends Model{
    const REDIRECT_TABLE = 'engine_page_redirects';
    private $id = NULL;
    private $from = NULL;
    private $to = NULL;
    private $type = 301;
    private $redirect_types = array(301,302);
    private $has_redirect = 0;
    private $delete = 0;

    function __construct($page_name)
    {
        if(strlen(trim($page_name)) > 0)
        {
            $this->from = $page_name;
        }
    }

    public function check_redirect()
    {
		$q = DB::select('id','to','type')->from(self::REDIRECT_TABLE)->where('from', '=', $this->from)->and_where('delete', '=', 0)->execute()->as_array();
        if(count($q) > 0)
        {
            $this->set_id($q[0]['id']);
            $this->set_redirect_status(1);
            $this->set_to($q[0]['to']);
            $this->set_type($q[0]['type']);
        }
        else
        {
			$url = @$_SERVER['SCRIPT_URL'];
        	$q = DB::select('id','to','type')->from(self::REDIRECT_TABLE)->where('from', '=', $url)->and_where('delete', '=', 0)->execute()->as_array();
			if(count($q) > 0)
			{
				$this->set_id($q[0]['id']);
				$this->set_redirect_status(1);
				$this->set_to($q[0]['to']);
				$this->set_type($q[0]['type']);
			}
			else
			{
				// check directories
				if(preg_match('#/#', ltrim($url, '/'))){
					$check_dirs = array();
					$request_dirs = explode('/', ltrim($url, '/'));
					$last_request_dirs = count($request_dirs) - 1;
					$request_dir_full = '/';
					foreach($request_dirs as $i => $request_dir){
						if($i == $last_request_dirs && $request_dir != ''){
							break;
						}
						$request_dir_full .= $request_dir . '/';
						$check_dirs[] = $request_dir_full;
					}
					$check_dirs = array_reverse($check_dirs);
					$q = DB::select('id', 'from', 'to', 'type')
						->from(self::REDIRECT_TABLE)
						->where('from', 'in', $check_dirs)
						->and_where('delete','=',0)
						->order_by(DB::expr('length(`from`) desc'))
						->limit(1)
						->execute()
						->as_array();
					if(count($q) > 0){
						$to_url = str_replace($q[0]['from'], $q[0]['to'], $url);
						$this->set_id($q[0]['id']);
						$this->set_redirect_status(1);
						$this->set_to($to_url);
						$this->set_type($q[0]['type']);
					} else {
						$this->set_redirect_status(0);
						$this->set_to(NULL);
					}
				} else {
					$this->set_redirect_status(0);
					$this->set_to(NULL);
				}
			}
        }
		if($this->to != '' && @$_SERVER['QUERY_STRING'] != ''){
			$this->to .= '?' . @$_SERVER['QUERY_STRING'];
			$this->to = ltrim($this->to, '/');
		}
        return $this;
    }

    public function get_redirect()
    {
        if($this->has_redirect === 1)
        {
            return array('to' => $this->to,'type' => $this->type);
        }
        else
        {
            return NULL;
        }
    }

    public function save()
    {
        if(is_numeric($this->id))
        {
            $this->_sql_update_redirect();
        }
        else
        {
            $this->_sql_save_redirect();
        }
    }

    public function delete()
    {
        $this->delete = 1;
        $this->save();
    }

    public function get()
    {
        return array('from'=>$this->from, 'to'=>$this->to, 'type' => $this->type, 'has_redirect' => $this->has_redirect, 'delete' => $this->delete);
    }

    public static function save_redirect($from, $to, $type, $has_redirect = 0)
    {
        DB::insert(self::REDIRECT_TABLE, array('from', 'to', 'type', 'has_redirect'))
            ->values(array($from, $to, $type, $has_redirect))
            ->execute();
    }

    public static function update_redirect($id, $from, $to, $type)
    {
        DB::update(self::REDIRECT_TABLE)
            ->set(array(
                'from' => $from,
                'to' => $to,
                'type' => $type,
            ))
            ->where('id', '=', $id)
            ->execute();
    }
    public static function delete_redirect($id)
    {
        DB::delete(self::REDIRECT_TABLE)->where('id', '=', $id)->execute();
    }

    public static function save_redirects($values)
    {
        if(!empty($values)){
            $array = explode('|',rtrim($values,'|'));
            if(count($array) > 0) {
                DB::update(self::REDIRECT_TABLE)->set(array('delete' => 1))->execute();
            }

            foreach($array AS $key=>$redirects) {
                $redirects = ltrim($redirects,',');
                DB::insert(self::REDIRECT_TABLE, array('from','to','type'))
                    ->values(explode(',',$redirects))
                    ->execute();
            }
        } else {
            DB::update(self::REDIRECT_TABLE)->set(array('delete' => 1))->execute();
        }
    }

	public static function get_active_redirect()
    {
        return DB::select()->from(self::REDIRECT_TABLE)->where('delete','=',0)->execute()->as_array();
    }

    private function _sql_update_redirect()
    {

        try{
            Database::instance()->begin();
            DB::update(self::REDIRECT_TABLE)->set($this->get())->where('id','=',$this->id)->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_save_redirect()
    {

        try{
            Database::instance()->begin();
            DB::insert(self::REDIRECT_TABLE,array_keys($this->get()))->values($this->get())->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function set_redirect_status($status)
    {
        $this->has_redirect = ($status === 1) ? 1 : 0;
    }

    private function set_to($to)
    {
        if(is_string($to))
        {
            $this->to = $to;
        }
        else
        {
            $this->to = NULL;
        }
    }

    private function set_type($type)
    {
        $this->type = ($type === 302) ? 302 : 301;
    }

    private function set_id($id)
    {
        if(is_numeric($id))
        {
            $this->id = (int) $id;
        }
    }
}
?>