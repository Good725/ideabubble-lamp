<?php
final class Model_Keyword extends Model{

    CONST KEYWORDS_TABLE        = 'plugin_reports_keywords';
    CONST API_URL               = 'https://serpbook.com/serp/api/';
    private $id                 = NULL;
    private $url                = '';
    private $keyword            = '';
    private $last_updated       = '';
    private $last_position      = 0;
    private $current_position   = 0;
    private $delete             = 0;
    private $report_id          = NULL;
    private $serpbook           = NULL;

    function __construct($id = NULL)
    {
        $this->serpbook = new SerpBook();
    }

    public function set_id($id)
    {
        $this->id = is_numeric($id) ? (int) $id : NULL;
    }

    public function validate()
    {
        $this->last_updated = (($this->last_updated == '' OR $this->last_updated == '0000-00-00') ? date('Y-m-d',time()) : $this->last_updated);
    }

    public function load($autoload = FALSE)
    {
        $data = $this->_sql_load_keyword();

        if($autoload)
        {
            $this->set($data);
        }

        return $data;
    }

    public function set($data)
    {
        foreach($data AS $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public function delete()
    {
        $result = TRUE;
        try{
            $ok = $this->used_by_single_report();
            if($ok)
            {
                $this->_sql_delete_keyword();
                $this->serpbook->delete_keyword($this->get_instance());
            }
            else
            {
                $this->_sql_delete_keyword();
            }
        }
        catch(Exception $e)
        {
            $result = FALSE;
        }
        return $result;
    }

    public function set_report_id($report_id)
    {
        $this->report_id = is_numeric($report_id) ? intval($report_id) : NULL;
        return $this;
    }

    public function save()
    {
        if(is_numeric($this->id))
        {
            $this->_sql_update_keyword();
        }
        else
        {
            $this->_sql_insert_keyword();
        }
        $this->serpbook->add_keyword($this->get_instance());
    }

    public function get_instance()
    {
        return array('id' => $this->id,'url' => $this->url,'keyword' => $this->keyword,'last_updated' => $this->last_updated,'last_position' => $this->last_position,'current_position' => $this->current_position,'delete' => $this->delete,'report_id' => $this->report_id);
    }

    public static function factory($id = NULL)
    {
        return new self();
    }

    public static function get_all_keywords()
    {
        return DB::select('id','url','keyword','last_updated','last_position','current_position','report_id')->from(self::KEYWORDS_TABLE)->where('delete','=',0)->execute()->as_array();
    }

    private function _sql_delete_keyword()
    {
        DB::delete(self::KEYWORDS_TABLE)->where('id','=',$this->id)->execute();
    }

    private function _sql_update_keyword()
    {

        try{
            Database::instance()->begin();
            DB::update(self::KEYWORDS_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function used_by_single_report()
    {
        $q = DB::select('id')->from(self::KEYWORDS_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return count($q) > 1 ? FALSE : TRUE;
    }

    private function _sql_insert_keyword()
    {
        $this->validate();

        try {
            Database::instance()->begin();
            DB::insert(self::KEYWORDS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
            Database::instance()->commit();
        } catch (Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_load_keyword()
    {
        $q = DB::select()->from(self::KEYWORDS_TABLE)->where('id','=',$this->id)->and_where('report_id','=',$this->report_id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }
}

class SerpBook{
    CONST API_URL = 'https://serpbook.com/serp/api/';

    public function add_keyword($data)
    {
        $search_engine = $this->get_report_search_engine($data['report_id']);
        if(strpos($search_engine,'google') == FALSE)
        {
            $search_engine = 'google.ie';
        }
        $disallowed_strings = array('http://','https://','/');
        $http = '?viewkey=addkeyword&auth='.Settings::instance()->get('serp_token').'&e='.Settings::instance()->get('serp_email').'&category='.Settings::instance()->get('serp_category').'&url='.str_replace($disallowed_strings,'',$data['url']).'&kw='.urlencode($data['keyword']).'&region='.$search_engine.'&language=en&exact=1&near=&ignore_local=0';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL.$http);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_exec($ch);
        curl_close($ch);
    }

    public function delete_keyword($data)
    {
        $search_engine = $this->get_report_search_engine($data['report_id']);
        if(strpos($search_engine,'google') == FALSE)
        {
            $search_engine = 'google.ie';
        }
        $disallowed_strings = array('http://','https://','/');
        $http = '?viewkey=delkeyword&auth='.Settings::instance()->get('serp_token').'&e='.Settings::instance()->get('serp_email').'&category='.Settings::instance()->get('serp_category').'&url='.str_replace($disallowed_strings,'',$data['url']).'&kw='.urlencode($data['keyword']).'&region='.$search_engine.'&language=en&exact=1&near=&ignore_local=0';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL.$http);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_exec($ch);
        curl_close($ch);
    }

    public function get_report_search_engine($report_id)
    {
        $report = new Model_Reports($report_id);
        $report->get(true);
        $data = $report->get_parameters('search_engine','google.ie');
        return $data[0]['value'];
    }
}
?>