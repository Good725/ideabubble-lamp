<?php
final class Model_Reports extends Model
{
    /* -- PRIVATE MEMBER DATA -- */

    const MAIN_TABLE          = 'plugin_reports_reports';
    const CATEGORIES_TABLE    = 'plugin_reports_categories';
    const CACHE_TABLE         = 'plugin_reports_saved_reports';
    const WIDGET_TABLE        = 'plugin_reports_widgets';
    const PARAMETERS_TABLE    = 'plugin_reports_parameters';
    const SERP_URL            = 'https://serpbook.com/serp/api/';
    const KEYWORDS_TABLE      = 'plugin_reports_keywords';
    const KEYWORDS_DATA_TABLE = 'plugin_reports_keyword_data';
    const FAVORITES_TABLE     = 'plugin_reports_favorites';
    const SHARING_TABLE       = 'plugin_reports_report_sharing';
    const USER_OPTIONS_TABLE  = 'plugin_reports_user_options';

    private $id = null;
    private $name = '';
    private $_sql = '';
    private $_widget_sql = '';
    private $category = 0;
    private $sub_category = 0;
    private $summary = '';
    private $dashboard = 0;
    private $publish = 1;
    private $delete = 0;
    private $created_date = '';
    private $modified_date = '';
    private $report_columns = array();
    private $conditions = array();
    private $widget_id = null;
    private $widget_name = '';
    private $widget_type = '';
    private $widget_x_axis = '';
    private $widget_y_axis = '';
    private $widget_fill_color = '';
    private $widget_html = '';
    private $widget_extra_text = '';
    private $widget_publish = 0;
    private $chart_id = null;
    private $chart;
    private $parameter_fields = '';
    private $is_favorite = false;
    private $shared_with_groups = array();
    private $report_data = array();
    private $is_google_analytics_report = false;
    private $profile_info;
    private $link_column = '';
    private $link_url = '';
    private $report_type = 'sql';
    private $temporary_keywords = '';
    private $autoload = 0;
    private $checkbox_column = 0;
    private $checkbox_column_label = '';
    private $action_button = 0;
    private $action_button_label = '';
    private $action_event = '';
    private $autosum = 0;
    private $column_value = '';
    private $autocheck = 0;
    private $custom_report_rules = '';
    private $bulk_message_sms_number_column = '';
    private $bulk_message_email_column = '';
    private $bulk_message_subject_column = '';
    private $bulk_message_subject = '';
    private $bulk_message_body_column = '';
    private $bulk_message_body = '';
    private $bulk_message_interval = '';
    private $rolledback_to_version = null;
    private $php_modifier = null;
    private $php_post_filter = null;
    private $generate_documents = null;
    private $generate_documents_template_file_id = null;
    private $generate_documents_pdf = null;
    private $generate_documents_office_print = null;
    private $generate_documents_office_print_bulk = null;
    private $generate_documents_tray = null;
    private $generate_documents_helper_method = null;
    private $generate_documents_link_to_contact = null;
    private $generate_documents_link_by_template_variable = null;
    private $generate_documents_mode = null;
    private $generate_documents_row_variable = null;
    private $totals_columns = '';
    private $totals_group = '';
    private $csv_columns = '';
    private $screen_columns = '';
    private $show_results_counter = 0;
    private $results_counter_text = '';
    private $bulk_messages_per_minute = '';
    
    public $tables = array();
    public $table_columns = array();
    public $sparkline;

    /* -- CONSTRUCTOR -- */
    public function __construct($id = null)
    {
        $this->set_id($id);
    }

    public static function splitSql($str)
    {
        $delimiter = ';';
        $specials = array('"', "'");

        $tokens = array();
        $state = 'read';
        $pos = 0;
        $len = strlen($str);
        $token = '';
        $special = false;
        while ($pos < $len) {
            $char = $str[$pos];
            if (in_array($char, $specials)) {
                if ($special === false) {
                    $special = $char;
                } else {
                    if ($special == $char) {
                        $special = false;
                    }
                }
            }
            if (!$special && $char == $delimiter) {
                $tokens[] = $token;
                $token = '';
            } else {
                $token .= $char;
            }
            ++$pos;
        }
        if ($token != '') {
            $tokens[] = $token;
        }
        return $tokens;
    }

    public static function executeMultiSql($sql, $returnLastSelect = true)
    {
        $t1 = microtime(1);
        $queries = self::splitSql($sql);
        $results = array();
        foreach ($queries as $i => $query) {
            $query = trim($query);
            if ((preg_match('/^select\s+/i', $query) OR preg_match('/^\(select\s+/i', $query)) AND ! preg_match('/\s+into\s+/i', $query)) {
                $results[] = DB::query(Database::SELECT, $query)->execute()->as_array();
            } else {
                DB::query(null, $query)->execute();
            }
        }
        $resultCount = count($results);
        $t2 = microtime(1);
        if (0)
        if (($t2 - $t1) > 1) {
            header('content-type: text/plain');
            ob_clean();
            echo $sql . "\n\n\n\n";
            $trs = debug_backtrace();
            foreach ($trs as $tr) {
                echo @$tr['file'] . ':' . @$tr['line'] . "\n";
            }
            exit;
        }
        if ($returnLastSelect) {
            return $results[$resultCount - 1];
        } else {
            return $results;
        }
    }

    /* -- PUBLIC MEMBER FUNCTIONS -- */

    public function set_id($id)
    {
        if(is_numeric($id))
        {
            $this->id = (int) $id;
        }
        elseif(is_string($id) AND !is_numeric($id))
        {
            $this->id = null;
        }
        elseif($id === null)
        {
            $this->id = null;
        }
        else
        {
            $this->id = null;
        }
        return $this->id;
    }

    public function get_id()
    {
        return (is_null($this->id)) ? null : $this->id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_report_type()
    {
        return $this->report_type;
    }

    public function get_sql($autoimplement = false)
    {
        //return Model_SqlFormatter::format($this->_sql);
        if($autoimplement)
        {
            $this->implement_parameters();
        }
        return $this->_sql;
    }

	public function get_widget_sql($autoimplement = false)
	{
		if ($autoimplement)
		{
			$this->implement_parameters();
		}
		return $this->_widget_sql;
	}

    public function get_category()
    {
        return $this->category;
    }

    public function get_sub_category()
    {
        return $this->sub_category;
    }

    public function get_autoload()
    {
        return $this->autoload;
    }

    public function get_checkbox_column()
    {
        return $this->checkbox_column;
    }

    public function get_checkbox_column_label()
    {
        return $this->checkbox_column_label;
    }

    public function get_action_button()
    {
        return $this->action_button;
    }

    public function get_action_button_label()
    {
        return $this->action_button_label;
    }

    public function get_action_event()
    {
        return $this->action_event;
    }

    public function get_autosum()
    {
        return $this->autosum;
    }

    public function get_column_value()
    {
        return $this->column_value;
    }
	
	public function get_bulk_message_sms_number_column()
    {
        return $this->bulk_message_sms_number_column;
    }
	
	public function get_bulk_message_email_column()
    {
        return $this->bulk_message_email_column;
    }
	
	public function get_bulk_message_subject_column()
    {
        return $this->bulk_message_subject_column;
    }
	
	public function get_bulk_message_subject()
    {
        return $this->bulk_message_subject;
    }
	
	public function get_bulk_message_body_column()
    {
        return $this->bulk_message_body_column;
    }
	
	public function get_bulk_message_body()
    {
        return $this->bulk_message_body;
    }
	
	public function get_bulk_message_interval()
    {
        return $this->bulk_message_interval;
    }
	
	public function get_php_modifier()
    {
        return $this->php_modifier;
    }

    public function get_php_post_filter()
    {
        return $this->php_post_filter;
    }

    public function get_generate_documents()
    {
        return $this->generate_documents;
    }

    public function get_generate_documents_template_file_id()
    {
        return $this->generate_documents_template_file_id;
    }

    public function get_generate_documents_pdf()
    {
        return $this->generate_documents_pdf;
    }

    public function get_generate_documents_office_print()
    {
        return $this->generate_documents_office_print;
    }

    public function get_generate_documents_office_print_bulk()
    {
        return $this->generate_documents_office_print_bulk;
    }

    public function get_generate_documents_tray()
    {
        return $this->generate_documents_tray;
    }

    public function get_generate_documents_helper_method()
    {
        return $this->generate_documents_helper_method;
    }

    public function get_generate_documents_link_to_contact()
    {
        return $this->generate_documents_link_to_contact;
    }

    public function get_generate_documents_link_by_template_variable()
    {
        return $this->generate_documents_link_by_template_variable;
    }

    public function get_generate_documents_mode()
    {
        return $this->generate_documents_mode;
    }

    public function get_generate_documents_row_variable()
    {
        return $this->generate_documents_row_variable;
    }

    public function get_totals_columns()
    {
        return $this->totals_columns;
    }

    public function get_totals_group()
    {
        return $this->totals_group;
    }

    public function get_publish()
    {
        return $this->publish;
    }

    public function get_summary()
    {
        return $this->summary;
    }

    public function get_widget_id()
    {
        return $this->widget_id;
    }

	public function get_is_favorite()
	{
		return $this->is_favorite;
	}

	public function get_shared_with_groups()
	{
		return $this->shared_with_groups;
	}
	
	public function get_rolledback_to_version()
	{
		return $this->rolledback_to_version;
	}

    public function get_csv_columns()
    {
        return $this->csv_columns;
    }

    public function get_screen_columns()
    {
        return $this->screen_columns;
    }

    public function get_show_results_counter()
    {
        return $this->show_results_counter;
    }

    public function get_results_counter_text()
    {
        return $this->results_counter_text;
    }

    public function get_bulk_messages_per_minute()
    {
        return $this->bulk_messages_per_minute;
    }

    public function validate($data)
    {
        if(!isset($data['name']))
        {
            throw new Exception("Name is not set.");
        }
    }

    public function set_name($name)
    {
        $this->name = trim($name);
        return $this->name;
    }

    public function set_report_type($report_type)
    {
        $this->report_type = is_string($report_type) ? $report_type : '';
        return $this->report_type;
    }

    public function set_sql($sql)
    {
        if(is_string($sql) AND trim($sql) != '' AND !is_null($sql))
        {
            $this->_sql = rtrim(trim($sql),';');
        }
        return $this->_sql;
    }

	public function set_widget_sql($widget_sql)
	{
		if (is_string($widget_sql) AND trim($widget_sql) != '' AND ! is_null($widget_sql))
		{
			$this->_widget_sql = rtrim(trim($widget_sql),';');
		}
		return $this->_widget_sql;
	}

    public function set_category($category)
    {
        $this->category = (int) trim($category);
        return $this->category;
    }

    public function set_sub_category($sub_category)
    {
        $this->sub_category = (int) trim($sub_category);
        return $this->sub_category;
    }

    public function set_dashboard($dashboard)
    {
        $this->dashboard = ($dashboard == 1) ? 1 : 0;
        return $this->dashboard;
    }

    public function set_publish($publish)
    {
        $this->publish = ($publish == 0) ? 0 : 1;
        return $this->publish;
    }

    public function set_delete($delete)
    {
        $this->delete = ($delete == 1) ? 1 : 0;
        return $this->delete;
    }

    public function set_summary($summary)
    {
        $this->summary = trim($summary);
        return $this->summary;
    }

    public function set_widget_id($id)
    {
        if(is_numeric($id))
        {
            $this->widget_id = (int) trim($id);
        }
        else
        {
            $this->widget_id = null;
        }

        return $this->widget_id;
    }

    public function set_widget_name($name)
    {
        $this->widget_name = trim($name);

        return $this->widget_name;
    }

    public function set_widget_type($type)
    {
        $this->widget_type = $type;

        return $this->widget_type;
    }

    public function set_widget_x_axis($field)
    {
        $this->widget_x_axis = $field;
        return $this->widget_x_axis;
    }

    public function set_widget_y_axis($field)
    {
        $this->widget_y_axis = $field;
        return $this->widget_y_axis;
    }

	public function set_widget_html($field)
	{
		$this->widget_html = $field;
		return $this->widget_html;
	}

    public function set_widget_extra_text($value)
    {
        $this->widget_extra_text = trim($value);

        return $this->widget_extra_text;
    }

    public function set_widget_fill_color($value)
    {
        $this->widget_fill_color = trim($value);

        return $this->widget_fill_color;
    }

    public function set_widget_publish($publish)
    {
        $this->widget_publish = ($publish == 0) ? 0 : 1;
        return $this->widget_publish;
    }

    public function set_link_column($link_column)
    {
        $this->link_column = is_string($link_column) ? $link_column : '';
        return $this->link_column;
    }

    public function set_link_url($link_url)
    {
        $this->link_url = is_string($link_url) ? $link_url : '';
        return $this->link_url;
    }


    public function set_csv_columns($csv_columns)
    {
        $this->csv_columns = $csv_columns;
        return $this->csv_columns;
    }

    public function set_screen_columns($screen_columns)
    {
        $this->screen_columns = $screen_columns;
        return $this->screen_columns;
    }

    public function set_bulk_messages_per_minute($bulk_messages_per_minute)
    {
        $this->bulk_messages_per_minute = $bulk_messages_per_minute;
        return $this->bulk_messages_per_minute;
    }

    public function load($data)
    {
		// issue with the "select all" checkbox
		if (isset($data['shared_with_groups'][0]) AND ! is_numeric($data['shared_with_groups'][0])) unset($data['shared_with_groups'][0]);



        $this->id               = (isset($data['id']))              ? $this->set_id($data['id'])                        : $this->id;
        $this->name             = (isset($data['name']))            ? $this->set_name($data['name'])                    : $this->name;
        $this->_sql             = (isset($data['sql']))             ? $this->set_sql($data['sql'])                      : $this->_sql;
		$this->_widget_sql      = (isset($data['widget_sql']))      ? $this->set_widget_sql($data['widget_sql'])        : $this->_widget_sql;
        $this->category         = (isset($data['category']))        ? $this->set_category($data['category'])            : $this->category;
        $this->sub_category     = (isset($data['sub_category']))    ? $this->set_sub_category($data['sub_category'])    : $this->sub_category;
        $this->dashboard        = (isset($data['dashboard']))       ? $this->set_dashboard($data['dashboard'])          : $this->dashboard;
        $this->publish          = (isset($data['publish']))         ? $this->set_publish($data['publish'])              : $this->publish;
        $this->delete           = (isset($data['delete']))          ? $this->set_delete($data['delete'])                : $this->delete;
        $this->summary          = (isset($data['summary']))         ? $this->set_summary($data['summary'])              : $this->summary;
        $this->widget_id        = (isset($data['widget_id']))       ? $this->set_widget_id($data['widget_id'])          : $this->widget_id;
        $this->widget_name      = (isset($data['widget_name']))     ? $this->set_widget_name($data['widget_name'])      : $this->widget_name;
        $this->widget_type      = (isset($data['widget_type']))     ? $this->set_widget_type($data['widget_type'])      : $this->widget_type;
        $this->widget_x_axis    = (isset($data['widget_x_axis']))   ? $this->set_widget_x_axis($data['widget_x_axis'])  : $this->widget_x_axis;
        $this->widget_y_axis    = (isset($data['widget_y_axis']))   ? $this->set_widget_y_axis($data['widget_y_axis'])  : $this->widget_y_axis;
		$this->widget_html      = (isset($data['widget_html']))     ? $this->set_widget_html($data['widget_html'])      : $this->widget_html;
        $this->widget_extra_text = (isset($data['widget_extra_text'])) ? $this->set_widget_extra_text($data['widget_extra_text']) : $this->widget_extra_text;
        $this->widget_fill_color = (isset($data['widget_fill_color'])) ? $this->set_widget_fill_color($data['widget_fill_color']) : $this->widget_fill_color;
        $this->widget_publish    = (isset($data['widget_publish']))    ? $this->set_widget_publish($data['widget_publish'])       : $this->widget_publish;
        $this->chart_id         = (isset($data['chart_id']))        ? $this->set_chart_id($data['chart_id'])            : $this->chart_id;
        $this->created_date     = $this->get_created_date();
        $this->modified_date    = date("Y-m-d H:i:s",time());
        $this->report_columns   = $this->set_report_columns();
        $this->chart            = new Model_Charts($this->chart_id);
        $this->chart->set($data);
        $this->parameter_fields      = (isset($data['parameter_fields']))      ? $data['parameter_fields'] : '';
        $this->link_column           = (isset($data['link_column']))           ? $this->set_link_column($data['link_column'])      : $this->link_column;
        $this->link_url              = (isset($data['link_url']))              ? $this->set_link_url($data['link_url'])            : $this->link_url;
        $this->report_type           = (isset($data['report_type']))           ? $this->set_report_type($data['report_type'])      : $this->report_type;
		$this->is_favorite           = (isset($data['is_favorite']))           ? $data['is_favorite']                              : FALSE;
		$this->shared_with_groups    = (isset($data['shared_with_groups']))    ? $data['shared_with_groups'] : array();
        $this->temporary_keywords    = (isset($data['temporary_keywords']))    ? json_decode($data['temporary_keywords'],true): $this->temporary_keywords;
        $this->autoload              = (isset($data['autoload']))              ? $data['autoload'] : 0;
        $this->checkbox_column       = (isset($data['checkbox_column']))       ? $data['checkbox_column'] : 0;
        $this->checkbox_column_label = (isset($data['checkbox_column_label'])) ? $this->checkbox_column_label = $data['checkbox_column_label'] : $this->checkbox_column_label;
        $this->action_button         = (isset($data['action_button']))         ? $this->action_button = $data['action_button'] : $this->action_button;
        $this->action_button_label   = (isset($data['action_button_label']))   ? $this->action_button_label = $data['action_button_label'] : $this->action_button_label;
        $this->action_event          = (isset($data['action_event']))          ? $this->action_event = $data['action_event'] : $this->action_event;
        $this->autosum               = (isset($data['autosum']))               ? $data['autosum'] : 0;
        $this->column_value          = (isset($data['column_value']))          ? $this->column_value = $data['column_value'] : $this->column_value;
		$this->autocheck             = (isset($data['autocheck']))             ? $this->autocheck = $data['autocheck'] : $this->autocheck;
        $this->custom_report_rules   = (isset($data['custom_report_rules']))   ? $this->custom_report_rules = $data['custom_report_rules'] : $this->custom_report_rules;
        $this->csv_columns           = (isset($data['csv_columns']))           ? $this->csv_columns = $data['csv_columns'] : $this->csv_columns;
        $this->screen_columns        = (isset($data['screen_columns']))        ? $this->screen_columns = $data['screen_columns'] : $this->screen_columns;
        $this->show_results_counter  = isset($data['show_results_counter'])    ? $data['show_results_counter'] : $this->show_results_counter;
        $this->results_counter_text  = isset($data['results_counter_text'])    ? $data['results_counter_text'] : $this->results_counter_text;
        $this->bulk_messages_per_minute  = isset($data['bulk_messages_per_minute'])    ? $data['bulk_messages_per_minute'] : $this->bulk_messages_per_minute;
		
		$this->bulk_message_sms_number_column     = (isset($data['bulk_message_sms_number_column']))     ? $this->bulk_message_sms_number_column = $data['bulk_message_sms_number_column'] : $this->bulk_message_sms_number_column;
        $this->bulk_message_email_column          = (isset($data['bulk_message_email_column']))     ? $this->bulk_message_email_column = $data['bulk_message_email_column'] : $this->bulk_message_email_column;
        $this->bulk_message_subject_column        = (isset($data['bulk_message_subject_column']))     ? $this->bulk_message_subject_column = $data['bulk_message_subject_column'] : $this->bulk_message_subject_column;
        $this->bulk_message_subject               = (isset($data['bulk_message_subject']))     ? $this->bulk_message_subject = $data['bulk_message_subject'] : $this->bulk_message_subject;
        $this->bulk_message_body_column           = (isset($data['bulk_message_body_column']))     ? $this->bulk_message_body_column = $data['bulk_message_body_column'] : $this->bulk_message_body_column;
        $this->bulk_message_body                  = (isset($data['bulk_message_body']))     ? $this->bulk_message_body = $data['bulk_message_body'] : $this->bulk_message_body;
		if(isset($data['bulk_message_interval'])){
			if(is_string($data['bulk_message_interval'])){
				$this->bulk_message_interval      = $data['bulk_message_interval'];
			} else {
				if(isset($data['bulk_message_interval']['minute']) && isset($data['bulk_message_interval']['hour']) && isset($data['bulk_message_interval']['day_of_month']) && isset($data['bulk_message_interval']['month']) && isset($data['bulk_message_interval']['day_of_week']) && $data['bulk_message_interval']['minute'] != '' && $data['bulk_message_interval']['hour'] != '' && $data['bulk_message_interval']['day_of_month'] != '' && $data['bulk_message_interval']['month'] != '' && $data['bulk_message_interval']['day_of_week'] != ''){
					$this->bulk_message_interval  = implode(' ', array( implode(',', $data['bulk_message_interval']['minute']),
																		implode(',', $data['bulk_message_interval']['hour']),
																		implode(',', $data['bulk_message_interval']['day_of_month']),
																		implode(',', $data['bulk_message_interval']['month']),
																		implode(',', $data['bulk_message_interval']['day_of_week'])));
				} else {
					$this->bulk_message_interval  = '';
				}
			}
		} else {
			$this->bulk_message_interval          = '';
		}
		
		$this->rolledback_to_version = isset($data['rolledback_to_version']) ? $data['rolledback_to_version'] : null;
		$this->php_modifier = isset($data['php_modifier']) ? $data['php_modifier'] : null;
        $this->php_post_filter = isset($data['php_post_filter']) ? $data['php_post_filter'] : null;
        $this->generate_documents = isset($data['generate_documents']) ? $data['generate_documents'] : null;
        $this->generate_documents_template_file_id = isset($data['generate_documents_template_file_id']) ? $data['generate_documents_template_file_id'] : null;
        $this->generate_documents_pdf = isset($data['generate_documents_pdf']) ? $data['generate_documents_pdf'] : null;
        $this->generate_documents_office_print = isset($data['generate_documents_office_print']) ? $data['generate_documents_office_print'] : null;
        $this->generate_documents_office_print_bulk = isset($data['generate_documents_office_print_bulk']) ? $data['generate_documents_office_print_bulk'] : null;
        $this->generate_documents_tray = isset($data['generate_documents_tray']) ? $data['generate_documents_tray'] : null;
        $this->generate_documents_helper_method = isset($data['generate_documents_helper_method']) ? $data['generate_documents_helper_method'] : null;
        $this->generate_documents_link_to_contact = isset($data['generate_documents_link_to_contact']) ? $data['generate_documents_link_to_contact'] : null;
        $this->generate_documents_link_by_template_variable = isset($data['generate_documents_link_by_template_variable']) ? $data['generate_documents_link_by_template_variable'] : null;
        $this->generate_documents_mode = isset($data['generate_documents_mode']) ? $data['generate_documents_mode'] : null;
        $this->generate_documents_row_variable = isset($data['generate_documents_row_variable']) ? $data['generate_documents_row_variable'] : null;
        $this->totals_columns = isset($data['totals_columns']) ? (is_array($data['totals_columns']) ? implode(',', $data['totals_columns']) : $data['totals_columns']) : '';
        $this->totals_group = isset($data['totals_group']) ? $data['totals_group'] : '';
    }

    public function load_widget($data)
    {
        $this->widget_name       = (isset($data['name']))       ? $this->set_widget_name($data['name'])             : $this->widget_name;
        $this->widget_type       = (isset($data['type']))       ? $this->set_widget_type($data['type'])             : $this->widget_type;
        $this->widget_x_axis     = (isset($data['x_axis']))     ? $this->set_widget_x_axis($data['x_axis'])         : $this->widget_x_axis;
        $this->widget_y_axis     = (isset($data['y_axis']))     ? $this->set_widget_y_axis($data['y_axis'])         : $this->widget_y_axis;
        $this->widget_html       = (isset($data['html']))       ? $this->set_widget_html($data['html'])             : $this->widget_html;
        $this->widget_extra_text = (isset($data['extra_text'])) ? $this->set_widget_extra_text($data['extra_text']) : $this->widget_extra_text;
        $this->widget_fill_color = (isset($data['fill_color'])) ? $this->set_widget_html($data['fill_color'])       : $this->widget_fill_color;
        $this->widget_publish    = (isset($data['publish']))    ? $this->set_widget_publish($data['publish'])       : $this->widget_publish;
    }

    public function get($autoload = false, $version_id = null)
    {
        $data = $this->_sql_load_report();
		$data['is_favorite'] = $this->_sql_is_favorite();
		$data['shared_with_groups'] = $this->_sql_get_shared_with_groups();
		$data['shared_with'] = 1;
		$this->sparkline = ORM::factory('Reports_Sparkline')->where('report_id', '=', $this->id)->where('deleted', '=', 0)->find();
		
		if($version_id !== null){
			$version = DB::select('*')
							->from('plugin_reports_versions')
							->where('id', '=', $version_id)
							->and_where('report_id', '=', $this->id)
							->execute()
							->as_array();
			$version = json_decode($version[0]['data_json'], true);
			$data = array_merge($data, $version['plugin_reports_reports'][0]);
			$data['shared_with_groups'] = array();
			foreach($version['plugin_reports_report_sharing'] as $rshare){
				$data['shared_with_groups'][] = $rshare['group_id'];
			}
		}

        if($autoload)
        {
            $this->load($data);
        }

		return $data;
    }

    public function get_category_name()
    {
        $q = DB::select('name')->from(self::CATEGORIES_TABLE)->where('id','=',$this->category)->execute()->as_array();
        return $q['name'];
    }

    public function get_widget($autoload = false)
    {
        $data = $this->_sql_load_widget();
        if($autoload)
        {
            $this->load_widget($data);
        }

        return $data;
    }

    public function get_widget_type()
    {
        return $this->widget_type;
    }

    public function run_report($widget = FALSE)
    {
		$sql = ($widget AND $this->_widget_sql != '') ? $this->_widget_sql : $this->_sql;

        $data = array();
        //we need to hack this function for the Google API hookup....
        if (strpos($sql,'google_analytics'))
        {
            $google_project_id = Settings::instance()->get('google_project_id');
            $google_client_id = Settings::instance()->get('google_client_id');
            if(!empty($google_project_id) AND !empty($google_client_id))
            {
                $data = $this->execute_google_api();
            }
        }
        elseif($this->report_type == 'serp')
        {
            $data = $this->execute_serp_api();
        }
        else
        {
            try{
                $this->sort_table_columns();
                $data = $this->execute_sql($widget);
                if(!empty($data))
                {
                    $this->cache_report($data);
                }
            }
            catch(Exception $e)
            {
                //$this->report_failed();
                throw $e;
            }
            //$this->after_report();
        }

        $this->report_data = $data;
        return $data;
    }

    public function execute_serp_api()
    {
        if(!$this->serp_executed_today())
        {
            $url = $this->get_serp_lookup_url();
            $data = $this->get_serp_data($url);
            if(count($data) > 0)
            {
                $data = $this->format_serp_data($data);
                $data = $this->filter_serp_data($data);
                $this->save_serp_data($data);
            }
            else
            {
                $data = array();
            }
        }
        else
        {
            $data = $this->get_todays_keyword_results();
        }
        return $data;
    }

    public function filter_serp_data($data)
    {
        $allowed_keywords = $this->get_keywords();
        $result = array();
        foreach($allowed_keywords AS $key=>$value)
        {
            foreach($data AS $list=>$item)
            {
                if($value['keyword'] == $item['keyword'])
                {
                    $item['keyword_id'] = $value['id'];
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    public function save_serp_data($data)
    {
        if(count($data) > 0)
        {
            $q = DB::insert(self::KEYWORDS_DATA_TABLE,array('date_run','keyword_id','grank','brank','yrank','report_id'));
            foreach($data AS $key=>$value)
            {
                $q->values(array(date('Y-m-d H:i:s',time()),$value['keyword_id'],$value['google'],$value['bing'],$value['yahoo'],$this->id));
            }
            $q->execute();
        }
    }

    public function get_todays_keyword_results()
    {
        return DB::select('t1.keyword','t2.grank','t2.brank','t2.yrank','t1.id')->from(array(self::KEYWORDS_TABLE,'t1'))->join(array(self::KEYWORDS_DATA_TABLE,'t2'),'LEFT')->on('t1.id','=','t2.keyword_id')->where('t2.report_id','=',$this->id)->execute()->as_array();
    }

    public function serp_executed_today()
    {
        $q = DB::query(Database::SELECT,'SELECT `id` FROM '.self::KEYWORDS_DATA_TABLE.' WHERE `report_id` = '.$this->id.' AND DATE_FORMAT(`date_run`,"%m-%d-%Y") = DATE_FORMAT(NOW(),"%m-%d-%Y")')->execute()->as_array();
        return count($q) > 0 ? TRUE : FALSE;
    }

    public function get_instance()
    {
        return $this->get_data();
    }

    public function format_serp_data($data)
    {
        $result = array();
        foreach($data AS $key=>$obj)
        {
            $result[] = array('keyword'=>$obj->kw,'google'=>$obj->grank,'bing'=>$obj->brank,'yahoo'=>$obj->yrank);
        }
        return $result;
    }

    public function get_serp_data($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        return $result;
    }

    public function get_serp_lookup_url()
    {
        $disallowed_strings = array('http://','https://','/');
        $http = '?viewkey=getcategories&auth=4400c593537a9892076fc4d355e53e2b&e='.Settings::instance()->get('serp_email');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::SERP_URL.$http);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        $result = (array)json_decode(curl_exec($ch));
        curl_close($ch);
        return count($result) > 0 ? $result[Settings::instance()->get('serp_category')] : '';
    }

    public function get_widget_name()
    {
        return $this->widget_name;
    }

    public function get_widget_extra_text()
    {
        return $this->widget_extra_text;
    }

    public function get_widget_fill_color()
    {
        return $this->widget_fill_color;
    }

    public function get_dashboard()
    {
        return $this->dashboard;
    }

    public function get_report_data()
    {
        return $this->report_data;
    }

    public function get_widget_x_axis()
    {
        return $this->widget_x_axis;
    }

	public function get_widget_y_axis()
	{
		return $this->widget_y_axis;
	}

	public function get_widget_html()
	{
		return $this->widget_html;
	}

    public function get_widget_publish()
    {
        return $this->widget_publish;
    }

    public function get_serp_widget_keywords($engine = 'grank')
    {
        //first get a list of the keywords to do with this report.
        $keywords = DB::select('id','keyword')->from(self::KEYWORDS_TABLE)->where('delete','=',0)->and_where('report_id','=',$this->id)->limit(5)->execute()->as_array();
        $result = array();
        $returns = false;
        //then (unfortunatley) select each keyword, and it's previous day's data (if it exists) - to a limit of 5).
        foreach($keywords AS $key=>$value)
        {
            $q = DB::select('grank','brank','yrank')->from(self::KEYWORDS_DATA_TABLE)->where('keyword_id','=',$value['id'])->and_where('report_id','=',$this->id)->order_by('date_run','DESC')->limit(2)->execute()->as_array();
            if(count($q) == 1)
            {
                $result[] = array('keyword' => $value['keyword'],'last_position' => 'n/a','current_position' => $q[0][$engine],'change' => 'n/a');
                $returns = true;
            }
            else if(count($q) == 2)
            {
                $result[] = array('keyword' => $value['keyword'],'last_position' => $q[1][$engine],'current_position' => $q[0][$engine],'change' => ($q[1][$engine] == 0 AND $q[0][$engine] == 0) ? 'n/a' : $q[1][$engine] - $q[0][$engine]);
                $returns = true;
            }
            else
            {
                $result[] = array('keyword' => $value['keyword'],'last_position' => 'n/a','current_position' => 'n/a','change' => 'n/a');
            }
        }

        return array('keywords' => $result,'results' => $returns);
    }

    public function get_keywords()
    {
        return DB::query(Database::SELECT,'SELECT t1.id,t1.url,t1.keyword,t1.last_updated,t1.last_position,t1.current_position,(SELECT t2.`grank` FROM '.self::KEYWORDS_DATA_TABLE.' AS t2 WHERE t2.keyword_id = t1.id ORDER BY date_run DESC LIMIT 1) AS `grank` FROM '.self::KEYWORDS_TABLE.' AS t1 WHERE t1.report_id = "'.$this->id.'" and t1.delete = 0')->execute()->as_array();
    }

    public function save($create_version = true)
    {
        $new_report = is_numeric($this->id) ? false : true;
		if(is_numeric($this->id) AND !is_null($this->id))
        {
			if($create_version){
	            self::save_version($this->id);
			}
        	$this->_sql_update_existing_report();
        }
        else
        {
            $this->_sql_add_new_report();
        }

        DB::update(self::PARAMETERS_TABLE)->set(array('delete' => 1))->where('report_id','=',$this->id)->execute();

        if($this->parameter_fields != '')
        {
            $params = $this->prepare_parameters();
            foreach($params AS $key=>$param)
            {
                if ($param['type'] == 'date') {
                    if (date::dmy_to_ymd($param['value']) == date::today()) {
                        $param['value'] = '';
                    }
                    if ($param['always_today'] == 1) {
                        $param['value'] = '';
                    }
                }
                $parameter = new Model_Parameter($param['id']);
                $parameter->set_report_id($param['report_id']);
                $parameter->set_type($param['type']);
                $parameter->set_name($param['name']);
                $parameter->set_value($param['value']);
				$parameter->set_is_multiselect($param['is_multiselect'] ? 1 : 0);
                $parameter->set_delete(0);
                $parameter->save();
            }
        }

        if ($new_report)
        {
            foreach($this->temporary_keywords AS $key=>$value)
            {
                Model_Keyword::factory()->set($value)->set_report_id($this->id)->save();
            }
        }

		$this->save_favorite_data();
		$this->save_sharing_data();

        Log::instance()->add(Log::NOTICE, 'Report Saved.');

		return $this;
    }

	/*
	 * Saves / Removes a record saying if the logged-in user has favorited the report
	 */
	private function save_favorite_data()
	{
		// Delete relationship when unfavourited
		// Also delete the relationship just before favouriting, to ensure it doesn't get two records
		$user = Auth::instance()->get_user();
		DB::delete(self::FAVORITES_TABLE)->where('user_id', '=', $user['id'])->where('report_id', '=', $this->id)->execute();

		// Add relationship, when favourited
		if ($this->is_favorite)
		{
			DB::insert(self::FAVORITES_TABLE)->columns(array('user_id', 'report_id'))->values(array($user['id'], $this->id))->execute();
		}
	}

	/*
	 * Saves which user groups have access to the report
	 * When empty, everyone has access
	 */
	private function save_sharing_data()
	{
		// Remove all existing records
		DB::delete(self::SHARING_TABLE)->where('report_id', '=', $this->id)->execute();

		// Add new records
		if (count($this->shared_with_groups) > 0)
		{
			$q = DB::insert(self::SHARING_TABLE)->columns(array('report_id', 'group_id'));
			foreach ($this->shared_with_groups as $group_id)
			{
				$q->values(array($this->id, $group_id));
			}
			$q->execute();
		}
	}

    public function get_widget_url()
    {
        return URL::site().'admin/reports/add_edit_report/'.$this->id;
    }

    public function delete()
    {
        $this->set_delete(1);
        $this->set_publish(0);
        $this->set_widget_publish(0);
        $this->chart->set_publish(0);
        $this->save();

		// Log the action
		$activity = new Model_Activity;
		$activity->set_item_type('report')->set_action('delete')->set_item_id($this->id)->save();
	}

    public function get_categories_as_dropdown($sub_categories)
    {
        $result = '';
        if(!$sub_categories)
        {
            $categories = self::get_all_categories();
            foreach($categories AS $category)
            {
                if($this->category == $category['id'])
                {
                    $result.'<option value="'.$category['id'].'" selected="selected">'.$category['name'].'</option>';
                }
                else
                {
                    $result.'<option value="'.$category['id'].'">'.$category['name'].'</option>';

                }
            }
        }
        else
        {
            $sub_categories = self::get_all_sub_categories();
            foreach($sub_categories AS $category)
            {
                if($this->sub_category == $category['id'])
                {
                    $result.'<option value="'.$category['id'].'" selected="selected">'.$category['name'].'</option>';
                }
                else
                {
                    $result.'<option value="'.$category['id'].'">'.$category['name'].'</option>';

                }
            }
        }
        return $result;
    }

    public function format_sql()
    {
        return Model_SqlFormatter::format($this->_sql);
    }

    public function get_widget_json($data = null)
    {
		$widget_type = ORM::factory('Reports_WidgetType', $this->widget_type);
        // Make sure this widget has an x and y axis, unless it is a type that does not require these parameters
        if (in_array($widget_type->stub, array('gannt_chart', 'table', 'raw_html')) OR ($this->widget_x_axis != '' AND $this->widget_y_axis != ''))
        {
			try
			{
				switch($widget_type->stub)
				{
					case 'line_graph':
						return $this->line_report();
						break;
					case 'bar_chart':
						return $this->bar_report_widget($data);
						break;
					case 'pie_chart':
						return $this->pie_report_widget();
						break;
					case 'donut_chart':
						return $this->donut_report_widget();
						break;
					case 'quick_stats':
						return $this->quick_stats_widget();
						break;
					case 'table':
						return $this->table_widget();
						break;
					case 'gannt_chart':
						return $this->gannt_report_widget();
						break;
					case 'serp_table':
					case 'serp_google':
					case 'serp_bing':
					case 'serp_yahoo':
						return $this->serp_table_widget();
						break;
					case 'calendar':
						return $this->calendar_widget();
                    case 'survey_question_group':
                        return $this->survey_question_group_widget();
                        break;
					default:
						break;
				}
			}
			catch (Exception $e)
			{
				Log::instance()->add(Log::ERROR, "Error rendering widget.\nReport ID: " . $this->id . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
				return '';
			}
        }
        else
        {
            //go to hell.
            return 'undefined';
        }
    }

    public function get_chart_json()
    {
        //first make sure this widget has an x and y axis.
        if(($this->chart->get_x_axis() != '' OR $this->chart->get_type() == 4) AND ($this->chart->get_y_axis() != '' OR $this->chart->get_type() == 4))
        {
            $result = '';
            if(!is_object($this->chart))
            {
                $this->chart = new Model_Charts($this->chart_id);
            }

            switch($this->chart->get_type())
            {
                case 1:
                    return $this->line_report_chart();
                    break;
                case 2:
                    return $this->bar_report_chart();
                    break;
                case 3:
                    return $this->pie_report_chart();
                    break;
                case 4:
                    return $this->gannt_report_chart();
                    break;
                default:
                    break;
            }
        }
        else
        {
            //
            return 'undefined';
        }
    }

    public function report_data_json($data)
    {
        //echo Debug::vars($data);

        $report = new Model_Reports($data['id']);
        $report_details = $report->get(true);
        $screen_columns = $this->screen_columns != '' ? explode("\n", $this->screen_columns) : false;
        if ($screen_columns)
        foreach ($screen_columns as $i => $screen_column) { // clean up any potential unwanted blanks
            $screen_columns[$i] = trim($screen_column);
        }


        //echo Debug::vars($report_details);

        $report = $this->run_report();
        $columns = $this->set_report_columns($report);
        $header = '<thead>';
        $footer = '<tfoot class="report_datatable_search"><tr class="column_sum">';
		$result = '<tbody>';

        //it adds an empty <td> if the checkboxes column is active
        if($report_details['checkbox_column']==1) {
            $footer .= '<td></td>';
        }

        foreach ($columns AS $column)
		{
            if ($screen_columns) {
                if (!in_array($column, $screen_columns)) {
                    continue;
                }
            }
            $footer.='<td></td>';
            $report_details['column_value'] == $column ? $id = 'id="selected"' : $id='';
            $header.='<th '.$id.' title="'.htmlentities($column).'">'.$column.'</th>';
        }


        if($report_details['checkbox_column']==1) {
            $header.='<th>'.$report_details['checkbox_column_label'].'</th>';

        }

        $header.='</thead>';

        $row_count = count($report);
        $last_row_index = $row_count - 1;
        foreach($report AS $row_index => $row)
        {
            if ($screen_columns) {
                foreach ($row as $column => $data) {
                    if (!in_array($column, $screen_columns)) {
                        unset ($row[$column]);
                    }
                }
                $report[$row_index] = $row;
            }
        }
        foreach($report AS $key=>$row){
            $result.='<tr>';

            foreach ($row AS $columnName => $element) {
                // if payment admin fee minus, change it to space
//                if ($columnName == 'Payment Admin Fee') {
//                    $element = ($element[0] == '-') ? 'Journal: Write Off Amount' : $element;
//                }
				$icon = $element;
				$image = URL::get_engine_plugin_assets_base("reports")."images/reports-img.png";
				$class = '';
				if($columnName == 'Reason for Cancellation')
				{
					$icon = '<img src="'.$image.'"><div class="dropdown">'.$element.'</div>';
					$class = 'report-dropdown';
				}
				elseif($columnName == 'Mooring Location')
				{
					$icon = '<img src="'.$image.'"><div class="dropdown"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2419.73102333456!2d-8.621919884187136!3d52.66483577984183!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b5c6388dc6549%3A0xe3502f591a512142!2sideabubble!5e0!3m2!1sen!2sin!4v1487154686539" width="200" height="100" frameborder="0" style="border:0" allowfullscreen></iframe></div>';
					$class = 'report-dropdown';
				}
				elseif($columnName == 'Client Email')
				{
					$icon = '<img src="'.$image.'"><div class="dropdown">'.$element.'</div>';
					$class = 'report-dropdown';
				}
                $class_html = $class ? ' class="'.$class.'"' : '';
                if (stripos($element, 'input') !== false) {
                    $element = $columnName;
                }
				$result.='<td'.$class_html.' title="'.htmlentities($element).'">'.$icon.'</td>';
                //$result.='<td>'.$element.'</td>';
            }

            if ($report_details['checkbox_column']==1) {
                if ($this->totals_columns != '' && $last_row_index == $key) {
                    $result .= '<td>&nbsp;</td>';
                } else {
                    $result .= '<td><input type="checkbox" class="row_check" /></td>';
                }
            }
            $result.='</tr>';
        }

        $result.='</tbody>';
        $footer.='</tr><tr>';

        foreach($columns AS $column)
        {
            if ($screen_columns) {
                if (!in_array($column, $screen_columns)) {
                    continue;
                }
            }
            $footer.='<td><input type="text" class="form-control search_init search_'.$column.'" placeholder="Search '.$column.'"/></td>';
        }

        //it adds an empty <td> if the checkboxes column is active
        if($report_details['checkbox_column']==1) {
            $footer .= '<td></td>';
        }

        $footer.= '</tr></tfoot>';
        return $header.$footer.$result;
    }

    public function get_report_columns()
    {
        return $this->report_columns;
    }

    public function get_chart_id()
    {
        return $this->chart_id;
    }

    public function get_link_column()
    {
        return $this->link_column;
    }

    public function get_link_url()
    {
        return $this->link_url;
    }

    public function set_chart_id($id)
    {
        if(is_numeric($id) AND $id > 0)
        {
            $this->chart_id = (int) $id;
        }
        else
        {
            $this->chart_id = null;
        }

        return $this->chart_id;
    }

    public function sort_table_columns()
    {
        $result = array();
        preg_match_all('/\s+FROM\s+(\S+)/i',$this->_sql,$result);
        if(count($result) > 0) {
            foreach ($result[1] as $key => $value) {
                $result[1][$key] = trim(trim(trim($value, '`'), "'"), '"');
            }
            $tables = $result[1];
            foreach ($tables as $table) {
                try {
                    $q = DB::query(Database::SELECT, 'SHOW COLUMNS FROM ' . $table)->execute()->as_array();
                    $this->tables[] = $table;
                } catch (Exception $exc) { // skip temporary tables

                }

            }
        }
        $this->get_columns_from_tables();
    }

    public function get_columns_from_tables()
    {
        $results = array();
        foreach($this->tables as $key=>$table)
        {
            if($table == 'google_analytics'){
				$results[$table] = array('Month', 'Hits');
			} else {
				$q = DB::query(Database::SELECT,'SHOW COLUMNS FROM '.$table)->execute()->as_array();
				foreach($q as $index => $value)
				{
					$results[$table][] = str_replace(',','',$value['Field'].'<br/>');
				}
			}
        }
        $this->table_columns = $results;
    }

    public function get_table_columns()
    {
        return $this->table_columns;
    }

    public function get_report_csv()
    {
        $data = $this->execute_sql();
        $output = fopen('php://output', 'w');
        ob_start();
        $csv_columns = $this->csv_columns != '' ? explode("\n", $this->csv_columns) : false;
        foreach ($csv_columns as $i => $csv_column) { // clean up any potential unwanted blanks
            $csv_columns[$i] = trim($csv_column);
        }
        foreach($data AS $row)
        {
            if ($csv_columns) {
                foreach ($row as $column => $data) {
                    if (!in_array($column, $csv_columns)) {
                        unset ($row[$column]);
                    }
                }
            }
            fputcsv($output, $row);
        }
        fclose($output);
        $csv = ob_get_clean();
        return $csv;
    }

    public function is_analytics_report()
    {
        return (strpos($this->_sql,'google_analytics') !== FALSE) ? true : false;
    }

    public function get_parameters($like,$default = '')
    {
        $q = DB::select('value')->from(self::PARAMETERS_TABLE)->where('report_id','=',$this->id)->and_where('delete','=',0)->and_where('name','LIKE','%'.$like.'%')->execute()->as_array();
        if(count($q) > 0)
        {
            return $q;
        }
        else
        {
            return array(0 => array('value' => $default));
        }
    }

    public function set_parameters($array)
    {
        $this->parameter_fields = $array;
    }

    /* -- PUBLIC STATIC FUNCTIONS -- */

    public static function toggle_dashboard($report_id)
    {
        DB::query(Database::UPDATE,'UPDATE plugin_reports_reports SET dashboard = 1 - dashboard WHERE id = '.$report_id)->execute();
    }

	public static function toggle_favorite($report_id, $is_favorite)
	{
		$user = Auth::instance()->get_user();
		DB::delete(self::FAVORITES_TABLE)->where('report_id', '=', $report_id)->where('user_id', '=', $user['id'])->execute();
		if ($is_favorite)
		{
			DB::insert(self::FAVORITES_TABLE)->columns(array('report_id', 'user_id'))->values(array($report_id, $user['id']))->execute();
		}
	}

    public static function get_report_widget_type($type = 6)
    {
        $types = array(5 => 'grank',6 => 'grank',7 => 'brank',8 => 'yrank');
        return $types[$type];
    }

    //The purpose of getting all reports is to get all reports, not some.
    public static function get_all_reports()
    {
		$user = Auth::instance()->get_user();
        return DB::select(self::MAIN_TABLE.'.id',self::MAIN_TABLE.'.name',self::MAIN_TABLE.'.category',self::MAIN_TABLE.'.dashboard',self::MAIN_TABLE.'.date_created',self::MAIN_TABLE.'.date_modified',array(self::CATEGORIES_TABLE.'.name','category_name'),array(self::FAVORITES_TABLE.'.user_id','is_favorite'))
			->from(self::MAIN_TABLE)
			->join(self::CATEGORIES_TABLE,'LEFT')->on(self::CATEGORIES_TABLE.'.id','=',self::MAIN_TABLE.'.category')
			->join(self::FAVORITES_TABLE,'LEFT')->on(self::FAVORITES_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')->on(self::FAVORITES_TABLE.'.user_id', '=', DB::expr($user['id']))
			->where(self::MAIN_TABLE.'.delete','=',0)
			->order_by(self::MAIN_TABLE.'.date_modified','DESC')
			->execute()->as_array();
    }

	// Get a list of reports that the user has permission to view
	public static function get_all_accessible_reports()
	{
		$user = Auth::instance()->get_user();
        $role = ORM::factory('Roles', $user['role_id']);
        if($role->master_group || in_array($role->id, ['1', '2'])){
            $reports = DB::select(DB::expr('DISTINCT ' . self::MAIN_TABLE.'.id'),self::MAIN_TABLE.'.name',self::MAIN_TABLE.'.category',self::MAIN_TABLE.'.dashboard',self::MAIN_TABLE.'.date_created',self::MAIN_TABLE.'.date_modified',array(self::CATEGORIES_TABLE.'.name','category_name'),array(self::FAVORITES_TABLE.'.user_id','is_favorite'))
                ->from(self::MAIN_TABLE)
                ->join(self::CATEGORIES_TABLE,'LEFT')->on(self::CATEGORIES_TABLE.'.id','=',self::MAIN_TABLE.'.category')
                ->join(self::FAVORITES_TABLE,'LEFT')->on(self::FAVORITES_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')->on(self::FAVORITES_TABLE.'.user_id', '=', DB::expr($user['id']))
                ->join(self::SHARING_TABLE,'LEFT')->on(self::SHARING_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')
                ->where(self::MAIN_TABLE.'.delete','=',0)
                ->order_by(self::MAIN_TABLE.'.date_modified','DESC')
                ->execute()->as_array();
        } else {
            $reports = DB::select(DB::expr('DISTINCT ' . self::MAIN_TABLE.'.id'),self::MAIN_TABLE.'.name',self::MAIN_TABLE.'.category',self::MAIN_TABLE.'.dashboard',self::MAIN_TABLE.'.date_created',self::MAIN_TABLE.'.date_modified',array(self::CATEGORIES_TABLE.'.name','category_name'),array(self::FAVORITES_TABLE.'.user_id','is_favorite'))
                ->from(self::MAIN_TABLE)
                ->join(self::CATEGORIES_TABLE,'LEFT')->on(self::CATEGORIES_TABLE.'.id','=',self::MAIN_TABLE.'.category')
                ->join(self::FAVORITES_TABLE,'LEFT')->on(self::FAVORITES_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')->on(self::FAVORITES_TABLE.'.user_id', '=', DB::expr($user['id']))
                ->join(self::SHARING_TABLE,'INNER')->on(self::SHARING_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')
                ->where(self::MAIN_TABLE.'.delete','=',0)
                ->where_open()
                ->where(self::SHARING_TABLE.'.group_id', '=', null)
                ->or_where(self::SHARING_TABLE.'.group_id', '=', $user['role_id'])
                ->where_close()
                ->order_by(self::MAIN_TABLE.'.date_modified','DESC')
                ->execute()->as_array();
        }

        return $reports;
	}

    public static function get_reports($filter = array())
    {
        $query = DB::select('report.id','report.name','report.category','report.dashboard','report.date_created','report.date_modified',array('category.name','category_name'))
            ->from(array(self::MAIN_TABLE, 'report'))
            ->join(array(self::CATEGORIES_TABLE, 'category'),'LEFT')->on('category.id','=','report.category')
            ->where('report.delete','=',0)
            ->order_by('report.date_modified','DESC');

        $query = self::where_clauses($query, $filter);
        return $query->execute()->as_array();
    }

    public static function get_top_menus()
    {
        return array(
            'Reports' => array(
                array(
                    'icon' => 'reports',
                    'name' => 'Reports',
                    'link' => '/admin/reports'
                ),
                array(
                    'icon' => 'category',
                    'name' => 'Categories',
                    'link' => '/admin/reports/categories'
                )
            )
        );
    }

    public static function get_breadcrumbs()
    {
        return array(
            array('name' => 'Home',    'link' => '/admin'),
            array('name' => 'Reports', 'link' => '/admin/reports')
        );
    }

    public static function get_all_categories()
    {
        return DB::select('id','name')->from(self::CATEGORIES_TABLE)->where('delete','=',0)->and_where('publish','=',1)->execute()->as_array();
    }

    public static function get_all_sub_categories()
    {
        return DB::select('id','name')->from(self::CATEGORIES_TABLE)->where('delete','=',0)->and_where('publish','=',1)->and_where('parent','>',0)->execute()->as_array();
    }

    public static function get_all_reports_widgets()
    {
        $q = DB::select(self::MAIN_TABLE.'.id')->from(self::MAIN_TABLE)->join(self::WIDGET_TABLE,'LEFT')->on(self::MAIN_TABLE.'.widget_id','=',self::WIDGET_TABLE.'.id')->where(self::MAIN_TABLE.'.publish','=',1)->and_where(self::MAIN_TABLE.'.delete','=',0)->and_where(self::WIDGET_TABLE.'.publish','=',1)->and_where(self::WIDGET_TABLE.'.delete','=',0)->execute()->as_array();
        return $q;
    }

	// Get all widgets that the user has permission to view that are set to show on the dashboard
	public static function get_all_accessible_dashboard_reports_widgets()
	{
		$user = Auth::instance()->get_user();

		// Filter the options to only options for this user, before the join is performed
		$user_options = DB::select()->from(self::USER_OPTIONS_TABLE)->where('user_id', '=', $user['id']);
		return DB::select(self::MAIN_TABLE.'.id')
			->from(self::MAIN_TABLE)
			->join(self::WIDGET_TABLE,'LEFT')->on(self::MAIN_TABLE.'.widget_id','=',self::WIDGET_TABLE.'.id')
			->join(array('plugin_reports_sparklines', 'sparkline'), 'left')->on('sparkline.report_id', '=', self::MAIN_TABLE.'.id')
			->join(self::SHARING_TABLE,'LEFT')->on(self::SHARING_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')
			->join(array($user_options, 'user_options'), 'LEFT')->on('user_options.report_id', '=', self::MAIN_TABLE.'.id')
			->where(self::MAIN_TABLE.'.publish','=',1)
			->where(self::MAIN_TABLE.'.dashboard','=',1)
			->and_where(self::MAIN_TABLE.'.delete','=',0)
			->and_where_open()
				->and_where(self::WIDGET_TABLE.'.delete','=',0)
				->or_where('sparkline.deleted','=',0)
			->and_where_close()
			->where_open()
				->where(self::SHARING_TABLE.'.group_id', '=', null)
				->or_where(self::SHARING_TABLE.'.group_id', '=', $user['role_id'])
			->where_close()
			->order_by('user_options.order')
			->execute()
			->as_array();
	}

	// Get all widgets that the user has permission to view that are set to show on the dashboard
	public static function get_all_accessible_report_widgets()
	{
		$user = Auth::instance()->get_user();

		// Filter the options to only options for this user, before the join is performed
		$user_options = DB::select()->from(self::USER_OPTIONS_TABLE)->where('user_id', '=', $user['id']);
		return DB::select(
            self::MAIN_TABLE . '.id',
            self::MAIN_TABLE . '.dashboard'
        )
			->from(self::MAIN_TABLE)
			->join(self::WIDGET_TABLE,'LEFT')->on(self::MAIN_TABLE.'.widget_id','=',self::WIDGET_TABLE.'.id')
			->join(self::SHARING_TABLE,'LEFT')->on(self::SHARING_TABLE.'.report_id', '=', self::MAIN_TABLE.'.id')
			->join(array($user_options, 'user_options'), 'LEFT')->on('user_options.report_id', '=', self::MAIN_TABLE.'.id')
			->where(self::MAIN_TABLE.'.publish','=',1)
			->and_where_open()
				->where(self::MAIN_TABLE.'.dashboard','=',1)
				->or_where(self::MAIN_TABLE.'.widget_id', '!=', '')
			->and_where_close()
			->and_where(self::MAIN_TABLE.'.delete','=',0)
			->and_where(self::WIDGET_TABLE.'.delete','=',0)
			->where_open()
				->where(self::SHARING_TABLE.'.group_id', '=', null)
				->or_where(self::SHARING_TABLE.'.group_id', '=', $user['role_id'])
			->where_close()
			->order_by('user_options.order')
			->execute()
			->as_array();
	}
    public static function get_sql_parameter_options($selected = '', $sdisplay = '')
    {
        $result = '<select class="value_input input_select"' . $sdisplay . '>';
        $options = array(
            '1 Year Ago' => "DATE_SUB(NOW(), INTERVAL 1 YEAR)",
            '1 Month Ago' => 'DATE_SUB(NOW(), INTERVAL 1 MONTH)',
            'Start of this Month' => "DATE_FORMAT(NOW() ,'%Y-%m-01')",
            'Today' => 'CURDATE()',
            'Now' => 'NOW()',
			'End of this Month' => 'LAST_DAY(CURDATE())',
            '30 days from today' => 'ADDDATE(CURDATE(), INTERVAL 30 DAY)',
            '90 days from today' => 'ADDDATE(CURDATE(), INTERVAL 90 DAY)'
		);
        foreach($options AS $key=>$option)
        {
            //show a preview of the date in select
            $query = DB::select(DB::expr($option.' as res'));
            $result1 = $query->execute()->current();

            $result.='<option value="'.$option.'" '.($selected == $option ? 'selected' : '').'>'.$key.' <i>('.$result1["res"].')</i></option>';
        }
        $result.='</select>';
        return  $result;
    }

    /* -- PRIVATE MEMBER FUNCTIONS -- */
    private function _sql_update_existing_report()
    {

        try{
            Database::instance()->begin();
            $this->save_widget();
            if(!is_object($this->chart))
            {
                $this->chart = new Model_Charts($this->chart_id);
            }
            $this->set_chart_id($this->chart->save());

            //echo '_sql_update_existing_report(): '.Debug::vars($this->get_data()); die();
			$data = $this->get_data();
			$data['rolledback_to_version'] = null;
            DB::update(self::MAIN_TABLE)->set($data)->where('id','=',$this->id)->execute();

			// Log the action
			$activity = new Model_Activity;
			$activity->set_item_type('report')->set_action('update')->set_item_id($this->id)->save();

            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_add_new_report()
    {

        try{
            Database::instance()->begin();
            $this->save_widget();
            if(!is_object($this->chart))
            {
                $this->chart = new Model_Charts($this->chart_id);
            }
            $this->set_chart_id($this->chart->save());
            $q = DB::insert(self::MAIN_TABLE,array_keys($this->get_data()))->values($this->get_data())->execute();

			// Log the action
			$activity = new Model_Activity;
			$activity->set_item_type('report')->set_action('create')->set_item_id($q[0])->save();

            Database::instance()->commit();
        } catch(Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
        $this->set_id($q[0]);
    }

    private function _sql_load_report()
    {
        $q = DB::select(
            'id',
            'name',
            'summary',
            'sql',
            'widget_sql',
            'category',
            'sub_category',
            'dashboard',
            'date_created',
            'date_modified',
            'publish',
            'delete',
            'widget_id',
            'chart_id',
            'link_url',
            'link_column',
            'report_type',
            'autoload',
            'checkbox_column',
            'checkbox_column_label',
            'action_button',
            'action_button_label',
            'action_event',
            'autosum',
            'column_value',
            'autocheck',
            'custom_report_rules',
            'bulk_message_sms_number_column',
            'bulk_message_email_column',
            'bulk_message_subject_column',
            'bulk_message_subject',
            'bulk_message_body_column',
            'bulk_message_body',
            'bulk_message_interval',
            'php_modifier',
            'php_post_filter',
            'rolledback_to_version',
            'generate_documents',
            'generate_documents_template_file_id',
            'generate_documents_pdf',
            'generate_documents_office_print',
            'generate_documents_office_print_bulk',
            'generate_documents_tray',
            'generate_documents_helper_method',
            'generate_documents_link_to_contact',
            'generate_documents_link_by_template_variable',
            'generate_documents_mode',
            'generate_documents_row_variable',
            'totals_columns',
            'totals_group',
            'csv_columns',
            'screen_columns',
            'show_results_counter',
            'results_counter_text',
            'bulk_messages_per_minute'
        )->from(self::MAIN_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return (count($q) > 0) ? $q[0] : array();
    }

	private function _sql_is_favorite()
	{
		$user = Auth::instance()->get_user();
		$q = DB::select()->from(self::FAVORITES_TABLE)->where('report_id', '=', $this->id)->where('user_id', '=', $user['id'])->execute()->as_array();
		return (count($q) > 0);
	}

	private function _sql_get_shared_with_groups()
	{
		$return = ['1', '2'];
		$results = DB::select('group_id')->from(self::SHARING_TABLE)->where('report_id', '=', $this->id)->execute()->as_array();
		foreach ($results as $result) {
		    $return[] = $result['group_id'];
        }
		return $return;
	}
	
	public function has_access($user_id)
	{
		$role =
		DB::select('r.*')
			->from(array(Model_Users::MAIN_TABLE, 'u'))
				->join(array(Model_Roles::MAIN_TABLE, 'r'), 'INNER')->on('u.role_id', '=', 'r.id')
			->where('u.id', '=', $user_id)
			->execute()
			->as_array();
		$role = $role[0];
		if($role['master_group'] == 1){
			return true;
		} else {
			$shared = $this->_sql_get_shared_with_groups();
			if(count($shared) == 0 || in_array($role['id'], $shared)){
				return true;
			} else {
				return false;
			}
		}
	}


    private function _sql_load_widget()
    {
        $q = DB::select('id', 'name', 'type', 'x_axis', 'y_axis', 'html', 'extra_text', 'fill_color', 'publish')
            ->from(self::WIDGET_TABLE)
            ->where('id', '=', $this->widget_id)
            ->execute()
            ->as_array();

        return (count($q) > 0) ? $q[0] : array();
    }

    private function _sql_save_report_cache($data)
    {

        try{
            Database::instance()->begin();
            DB::insert(self::CACHE_TABLE,array('name','report_id','data','publish','delete'))->values(array());
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function get_data()
    {
        return array(
			'id' => $this->id, 'name' => $this->name, 'sql' => $this->_sql, 'widget_sql' => $this->_widget_sql, 'summary' => $this->summary,
			'category' => $this->category, 'sub_category' => $this->sub_category, 'dashboard' => $this->dashboard,
			'publish' => $this->publish, 'delete' => $this->delete, 'date_created' => $this->get_created_date(),
			'date_modified' => date('Y-m-d H:i:s', time()), 'widget_id' => $this->widget_id, 'chart_id' => $this->chart_id,
			'link_url' => $this->link_url, 'link_column' => $this->link_column, 'report_type' => $this->report_type,
			'autoload' => $this->autoload, 'checkbox_column' => $this->checkbox_column,
			'checkbox_column_label' => $this->checkbox_column_label, 'action_button' => $this->action_button,
			'action_button_label' => $this->action_button_label, 'action_event' => $this->action_event,
			'autosum' => $this->autosum, 'column_value' => $this->column_value, 'autocheck' => $this->autocheck, 'custom_report_rules' => $this->custom_report_rules,
			'bulk_message_sms_number_column' => $this->bulk_message_sms_number_column,
			'bulk_message_email_column' => $this->bulk_message_email_column,
			'bulk_message_subject_column' => $this->bulk_message_subject_column,
			'bulk_message_subject' => $this->bulk_message_subject,
			'bulk_message_body_column' => $this->bulk_message_body_column,
			'bulk_message_body' => $this->bulk_message_body,
			'bulk_message_interval' => $this->bulk_message_interval,
			'php_modifier' => $this->php_modifier,
            'php_post_filter' => $this->php_post_filter,
            'generate_documents' => $this->generate_documents,
            'generate_documents_template_file_id' => $this->generate_documents_template_file_id,
            'generate_documents_pdf' => $this->generate_documents_pdf,
            'generate_documents_office_print' => $this->generate_documents_office_print,
            'generate_documents_office_print_bulk' => $this->generate_documents_office_print_bulk,
            'generate_documents_tray' => $this->generate_documents_tray,
            'generate_documents_helper_method' => $this->generate_documents_helper_method,
            'generate_documents_link_to_contact' => $this->generate_documents_link_to_contact,
            'generate_documents_link_by_template_variable' => $this->generate_documents_link_by_template_variable,
            'generate_documents_mode' => $this->generate_documents_mode,
            'generate_documents_row_variable' => $this->generate_documents_row_variable,
            'totals_columns' => $this->totals_columns,
            'totals_group' => $this->totals_group,
            'csv_columns' => $this->csv_columns,
            'screen_columns' => $this->screen_columns,
            'show_results_counter' => $this->show_results_counter,
            'results_counter_text' => $this->results_counter_text,
            'bulk_messages_per_minute' => $this->bulk_messages_per_minute
		);
    }

    private function get_widget_data()
    {
        return array(
            'id'         => $this->widget_id,
            'name'       => $this->widget_name,
            'type'       => $this->widget_type,
            'x_axis'     => $this->widget_x_axis,
            'y_axis'     => $this->widget_y_axis,
            'html'       => $this->widget_html,
            'fill_color' => $this->widget_fill_color,
            'extra_text' => $this->widget_extra_text,
            'publish'    => $this->widget_publish,
            'delete'     => 0
        );
    }

    public function get_autocheck()
    {
        return $this->autocheck;
    }

    public function get_custom_report_rules()
    {
        return $this->custom_report_rules;
    }

    private function execute_google_api()
    {
        $array1 = array();
        $array2 = array();
        preg_match_all('/metrics\.(\S+)/', $this->_sql, $array1);
        preg_match_all('/dimensions\.(\S+)/', $this->_sql, $array2);
        $metrics = implode(',',$array1[1]);
        $dimensions = implode(',',$array2[1]);
        $sort = $this->google_get_sorting();
        $filters = $this->google_get_filters();
		$date_from = $this->get_parameter('date_from', date('Y-m-d',strtotime('-6 month')));
		$date_from = self::get_sql_value($date_from);
        $date_to = $this->get_parameter('date_to', date('Y-m-d',time()));
		$date_to = self::get_sql_value($date_to);
        try{
            require_once Kohana::find_file('vendor/gapi','index');
            $options = $this->set_google_options($dimensions,$filters,$sort);
            $values = get_google_data($date_from,$date_to,$metrics,$options);
            $rows = $this->google_assign_array_keys($values);
            $limit = $this->google_get_limit();
            $offset = $this->google_get_offset();
            $this->splice_array($rows,$limit,$offset);
            $this->is_google_analytics_report = true;
            $this->profile_info = $values->getProfileInfo();
        }
        catch(Exception $e)
        {
            Log::instance()->add(Log::ERROR, 'Google API Failed.'.$e->getMessage()."\n".$e->getTraceAsString());
            $rows = array();
        }

        return $rows;
    }

    private function set_google_options($dimensions,$filters,$sort)
    {
        $result = array();
        if(!empty($dimensions))
        {
            $result['dimensions'] = $dimensions;
        }
        if(!empty($filters))
        {
            $result['filters'] = $filters;
        }
        if(!empty($sort))
        {
            $result['sort'] = $sort;
        }

        return $result;
    }

    private function google_get_sorting()
    {
        $sorting = $this->get_parameters('sort-');
        if(count($sorting) > 0)
        {
            $result = '';
            foreach($sorting AS $key=>$sort)
            {
                $result.=$sort['value'].',';
            }
            return rtrim($result,',');
        }
        else
        {
            return '';
        }
    }

    private function google_get_filters()
    {
        $filters = $this->get_parameters('filter-');
        if(count($filters) > 0)
        {
            $result = '';
            foreach($filters AS $key=>$filter)
            {
                $result.=$filter['value'].';';
            }
            return rtrim($result,';');
        }
        else
        {
            return '';
        }
    }

    public function get_parameter($parameter, $default = '')
    {
        if(!empty($this->parameter_fields) AND is_array($this->parameter_fields))
        {
            $q = array();
            foreach($this->parameter_fields AS $field)
            {
                if($field['name'] == $parameter)
                {
					$q['type'] = $field['type'];
					$q['value'] = $field['value'];
                }
            }
        }
        else
        {
			$session = Session::instance();
			$dashboard_from = $session->get('dashboard-from');
			$dashboard_to   = $session->get('dashboard-to');

			if ($parameter == 'date_from' AND $dashboard_from != '' AND $dashboard_to != '')
			{
				$q['value'] = $dashboard_from;
				$q['type']  = 'date';
			}
			elseif ($parameter == 'date_to' AND $dashboard_from != '' AND $dashboard_to != '')
			{
				$q['value'] = $dashboard_to;
				$q['type']  = 'date';
			}
			else
			{
				$q = DB::select('value', 'type')->from(self::PARAMETERS_TABLE)->where('report_id','=',$this->id)->and_where('delete','=',0)->and_where('name','=',$parameter)->execute()->current();
			}
        }

		if (isset($q['type']) AND $q['type'] == 'user_role')
		{
			// If the parameter is a user role, replace its value with the role of the logged-in user
			$user = Auth::instance()->get_user();
			$role = new Model_Roles($user['role_id']);
			$q['value'] = $role->role;
		}
        else if (@$q['type'] == 'user_id' && $q['value'] == 'logged')
        {
            // If the parameter is a user id and value is logged replace its value with the id of the logged-in user
            $user = Auth::instance()->get_user();
            $q['value'] = $user['id'];
        }
        elseif (isset($q['value']) AND ( ! isset($q['type'] ) OR $q['type'] == 'date') AND $q['value'] != '')
        {
            if ($q['type'] == 'date') {
                $date_format = Settings::instance()->get('date_format');
                if (!$date_format) {
                    $date_format = 'd-m-Y';
                }

                $date_format = Settings::instance()->get('date_format');
                $dt = @DateTime::createFromFormat($date_format, $q['value']);
                if ($dt) {
                    $q['value'] = $dt->format('Y-m-d');
                } else {
                    $q['value'] = ibhelpers::mysql_date($q['value']);
                }
            }
        }
        return (isset($q['value'])) ? $q['value'] : $default;
    }

    private function google_get_limit()
    {
        $limits = array();
        preg_match_all('/(?i)LIMIT *([0-9]+)/', $this->_sql, $limits);
        return isset($limits[1][0]) ? $limits[1][0] : 0;
    }

    private function google_get_offset()
    {
        $offset = array();
        preg_match_all('/(?i)OFFSET *([0-9]+)/', $this->_sql, $offset);
        return isset($offset[1][0]) ? $offset[1][0] : 0;
    }

    private function splice_array(&$array,$limit = 0,$offset = 0)
    {
        if($limit > 0 OR $offset > 0)
        {
            $array = array_slice($array,$offset,$limit);
        }
    }

    private function google_assign_array_keys(&$data)
    {
        $rows = array();
        if ($data['rows'])
        {
            foreach($data['rows'] AS $key=>$value)
            {
                $rows[] = array_combine($this->report_columns,$value);
            }
        }

        return $rows;
    }

    private function get_created_date()
    {
        if($this->created_date == '' OR !strtotime($this->created_date))
        {
            return date('Y-m-d H:i:s',time());
        }
        else
        {
            return $this->created_date;
        }
    }

    private function set_report_columns($result = null)
    {
        if($this->report_type == 'serp')
        {
            return array('keyword','google','bing','yahoo','id');
        }
        else
        {
            return $this->parse_columns($result);
        }
    }

    public static function set_logged_in_for_mysql()
    {
        $logged_in_contact_id = null;
        $logged_in_user_id = null;
        $logged_in_user = Auth::instance()->get_user();
        if ($logged_in_user) {
            $logged_in_user_id = $logged_in_user['id'];
            $logged_in_contact = Model_Contacts3::get_linked_contact_to_user($logged_in_user_id);
            if ($logged_in_contact) {
                $logged_in_contact_id = $logged_in_contact['id'];
            }
        }

        if ($logged_in_user_id) {
            DB::query(null, "SET @logged_in_user_id=" . $logged_in_user_id)->execute();
        }
        if ($logged_in_contact_id) {
            DB::query(null, "SET @logged_in_contact_id=" . $logged_in_contact_id)->execute();
        }
    }

    public function execute_sql($widget = FALSE)
    {
        self::set_logged_in_for_mysql();
        $widget = ($widget AND trim($this->_widget_sql) != '');
        $this->implement_parameters($widget);
        if($this->php_modifier && $widget == false){
			eval($this->php_modifier);
		}

		$sql = ($widget) ? $this->_widget_sql : $this->_sql;

        $data = array();
		if (strpos($sql,'google_analytics'))
		{
			$google_project_id = Settings::instance()->get('google_project_id');
			$google_client_id = Settings::instance()->get('google_client_id');
			if ( ! empty($google_project_id) AND ! empty($google_client_id))
			{
				$data = $this->execute_google_api();
			}
		}
		else
		{
			$data = self::executeMultiSql($sql);
		}
        if($this->php_post_filter){
            eval($this->php_post_filter);
        }

        if ($this->totals_columns) {
            $totals_columns = explode(',', $this->totals_columns);
            $totals_grouped = array();
            $totals = array();
            if (count($data)) {
                foreach ($data as $row) {
                    if ($this->totals_group != '') {
                        if (!isset($totals_grouped[$row[$this->totals_group]])) {
                            $totals_grouped[$row[$this->totals_group]] = array();
                        }
                    }

                    foreach ($row as $column => $value) {
                        if (in_array($column, $totals_columns)) {
                            if (!isset($totals[$column])) {
                                $totals[$column] = 0;
                            }
                            $totals[$column] += (float)$value;

                            if ($this->totals_group != '') {
                                if (!isset($totals_grouped[$row[$this->totals_group]][$column])) {
                                    $totals_grouped[$row[$this->totals_group]][$column] = 0;
                                }
                                $totals_grouped[$row[$this->totals_group]][$column] += (float)$value;
                            }
                        } else {
                            if ($this->totals_group != '') {
                                if ($this->totals_group == $column) {
                                    $totals_grouped[$row[$this->totals_group]][$column] = $row[$column] . ' Total';
                                    $totals[$column] = 'Grand Total';
                                } else {
                                    $totals_grouped[$row[$this->totals_group]][$column] = '';
                                    $totals[$column] = '';
                                }
                            } else {
                                $totals[$column] = '';
                            }
                        }
                    }
                }
                if ($this->totals_group != '') {
                    ksort($totals_grouped);
                    foreach ($totals_grouped as $grouped => $total_grouped) {
                        $data[] = $total_grouped;
                    }
                }
                $data[] = $totals;
            }
        }

        return $data;
        //return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    private function implement_parameters($widget = FALSE)
    {
		$session = Session::instance();
		$widget = ($widget AND $this->_widget_sql != '');

		$sql = ($widget) ? $this->_widget_sql : $this->_sql;

		// Prevent doubling up of quotes
		$sql = str_replace('"{!DASHBOARD-TO!}"',   '{!DASHBOARD-TO!}',   str_replace("'{!DASHBOARD-TO!}'",   '{!DASHBOARD-TO!}',   $sql));
		$sql = str_replace('"{!DASHBOARD-FROM!}"', '{!DASHBOARD-FROM!}', str_replace("'{!DASHBOARD-FROM!}'", '{!DASHBOARD-FROM!}', $sql));
		if ($widget)
		{
			$this->_widget_sql = $sql;
		}
		else
		{
			$this->_sql = $sql;
		}

        preg_match_all('/{!(.*?)!}/', $sql, $matches);
        if (isset($matches[1]) AND isset($matches[0]))
        {
			foreach($matches[0] AS $key=>$match)
            {
				switch ($match)
				{
					case '{!DASHBOARD-FROM!}':
						if ($session->get('dashboard-from'))
						{
							$value = "'".$session->get('dashboard-from')."'";
						}
						elseif (isset($_GET['dashboard-from'])) // Use the dashboard date range filter, if set
						{
							$value = "'".date('Y-m-d',strtotime(Kohana::sanitize($_GET['dashboard-from'])))."'";
						}
						elseif ($this->get_parameter($matches[1][$key]) != '') // Use the variable, if set
						{
							$value = '"'.$this->get_parameter($matches[1][$key]).'"';
						}
						else // default to one year ago
						{
							$value = "(NOW() - INTERVAL 1 YEAR)";
						}
						break;

					case '{!DASHBOARD-TO!}':
						if ($session->get('dashboard-to'))
						{
							$value = "'".$session->get('dashboard-to')."'";
						}
						elseif (isset($_GET['dashboard-to'])) // Use the dashboard date range filter, if set
						{
							$value = "'".date('Y-m-d',strtotime(Kohana::sanitize($_GET['dashboard-to'])))."'";
						}
						elseif ($this->get_parameter($matches[1][$key]) != '') // Use the variable, if set
						{
							$value = '"'.$this->get_parameter($matches[1][$key]).'"';
						}
						else // default to now
						{
							$value = "NOW()";
						}
						break;

					default:
						$value = $this->get_parameter($matches[1][$key]);
						break;
				}
                if (is_array($value)) {
                    foreach ($value as $key => $item) {
                        $value[$key] = Database::instance()->quote($item);
                    }
                    $value = implode(', ', $value);
                }
				if ($widget)
				{
					$this->_widget_sql = str_replace($match,$value,$this->_widget_sql);
				}
				else
				{
					$this->_sql = str_replace($match,$value,$this->_sql);
				}
            }
        }
    }

    private function parse_columns($result = null)
    {
		if(stripos(ltrim($this->_sql), 'call ') === 0){
			$sql = $this->_sql;
	        $this->implement_parameters();
	        $result = DB::query(Database::SELECT,$this->_sql)->execute()->fields();
			$this->_sql = $sql;
		} else {
			if($result && isset($result[0])){
				$result = array_keys($result[0]);
			} else {
				$result = array();
				$sql = trim(ltrim(str_replace(array('select','from'),array('SELECT','FROM'),$this->_sql),"SELECT"));
				$from_index = self::get_main_from_pos('SELECT '.$sql) - strlen('SELECT ');
				$sql = substr($sql,0,$from_index);
		
				// Temporarily replace spaces enclosed in ``, '' or "", with an obscurity, so they are not lost later
				// ``
				preg_match_all('/(`[^`]*`)|[^`]*/',$sql,$matches);
				$sql = '';
				foreach($matches[0] as $entry)
				{
					$sql .= preg_replace("/\s(?=.*?`)/ims",'___',$entry);
				}
		
				// ''
				preg_match_all("/('[^']*')|[^']*/",$sql,$matches);
				$sql = '';
				foreach($matches[0] as $entry)
				{
					$sql .= preg_replace("/\s(?=.*?')/ims",'___',$entry);
				}
		
				// ""
				preg_match_all('/("[^"]*")|[^"]*/',$sql,$matches);
				$sql = '';
				foreach($matches[0] as $entry)
				{
					$sql .= preg_replace('/\s(?=.*?")/ims','___',$entry);
				}
		
				$buffer = '';
				$stack = array();
				$depth = 0;
				$len = strlen($sql);
				for($i=0; $i<$len; $i++)
				{
					$char = $sql[$i];
					switch ($char)
					{
						case '(':
							$depth++;
							break;
						case ',':
							if(!$depth)
							{
								if($buffer !== '')
								{
									$stack[] = $buffer;
									$buffer = '';
								}
								continue 2;
							}
							break;
						case ' ':
							if(!$depth)
							{
								continue 2;
							}
							break;
						case ')':
							if($depth)
							{
								$depth--;
							}
							else
							{
								$stack[] = $buffer.$char;
								$buffer = '';
								continue 2;
							}
							break;
					}
					$buffer .= $char;
				}
				if($buffer !== '')
				{
					$stack[] = $buffer;
				}
		
				foreach($stack AS $column)
				{
					if(strrpos($column,".") !== FALSE)
					{
						$table_column = explode('.',$column);
						if(strpos(strtoupper($table_column[count($table_column)-1]),'AS') !== FALSE)
						{
							$split = explode(' ',trim(preg_replace ( '/[^a-zA-Z0-9_]/', ' ', $table_column[count($table_column)-1])));
							$result[] = str_replace('___', ' ', $split[count($split)-1]);
						}
						else
						{
							$result[] = str_replace('___', ' ', $table_column[1]);
						}
					}
					else
					{
						if(strrpos(strtoupper($column),'AS') !== FALSE)
						{
							preg_match_all('/\`(.+?)\`/s', $column, $matches);
							if($matches && count($matches[1])){
								$result[] = str_replace('___', ' ', $matches[1][0]);
							}
						}
						else
						{
							$result[] = str_replace('___', ' ', $column);
						}
					}
				}
			}
		}
        return $result;
    }

	// If a query can have subqueries, get the "FROM" that is part of the main query
	private function get_main_from_pos($sql)
	{
		$main_from_found = FALSE;
		$selects = $froms = array();

		// Get positions of all "SELECT"
		$lastpos = 0;
		while (($lastpos = strpos($sql, 'SELECT', $lastpos)) !== FALSE)
		{
			$selects[] = $lastpos;
			$lastpos += strlen('SELECT');
		}
		// Get positions of all "FROM"
		$lastpos = 0;
		while (($lastpos = strpos($sql, 'FROM', $lastpos)) !== FALSE)
		{
			$froms[] = $lastpos;
			$lastpos += strlen('FROM');
		}

		// If the nth "FROM" comes before the (n+1)th "SELECT",  there is an equal number of "SELECT" and "FROM" between that "FROM" and the first "SELECT".
		// Therefore that FROM corresponds to the first SELECT
		for ($i = 0; $i < count($froms) AND $i < count($selects) - 1 AND ! $main_from_found;)
		{
			if ($froms[$i] < $selects[$i+1])
			{
				$main_from_found = TRUE;
			}
			else
			{
				$i++;
			}
		}
		return isset($froms[$i]) ? $froms[$i] : 0;
	}

    private function cache_report($data)
    {
        $this->_sql_save_report_cache($data);
    }

    private function save_widget()
    {
        if($this->widget_id === null)
        {
            $q = DB::insert(self::WIDGET_TABLE,array_keys($this->get_widget_data()))->values($this->get_widget_data())->execute();
            $this->widget_id = $q[0];
        }
        elseif(is_numeric($this->widget_id))
        {
            DB::update(self::WIDGET_TABLE)->set($this->get_widget_data())->where('id','=',$this->widget_id)->execute();
        }
        else
        {
            return false;
        }
    }

    public function prepare_parameters()
    {
        $result = array();
        $lines = json_decode($this->parameter_fields, true);


        if ($lines)
        {
            foreach($lines AS $fields)
            {
                $id = (ltrim($fields[0],'parameter_id_') != '') ? ltrim($fields[0],'parameter_id_') : null;
                $result[] = array('id' => $id,
                    'report_id' => $this->id,
                    'type' => $fields[1],
                    'name' => $fields[2],
                    'value' => $fields[3],
                    'delete' => 0,
                    'is_multiselect' => (isset($fields[4]) ? $fields[4] : 0),
                    'always_today' => (isset($fields[5]) ? $fields[5] : 0)
                );
            }
        }

        return $result;
    }

    private function get_domain_name_ga()
    {
        if(isset($this->profile_info->profileName))
        {
            return 'Address: '.$this->profile_info->profileName;
        }
        else
        {
            return '';
        }
    }

    private function line_report()
    {
        $sql_result = $this->run_report(TRUE);

        if (count($sql_result) == 0) {
            return '';
        }

        $merge  = [];
        $x_axis = [];
        $y_axis = [];

        foreach ($sql_result as $result) {
            $merge[]  = [$result[$this->widget_x_axis], (is_numeric($result[$this->widget_y_axis]) ? (float) $result[$this->widget_y_axis] : $result[$this->widget_y_axis])];
            $x_axis[] = $result[$this->widget_x_axis];
            $y_axis[] = (is_numeric($result[$this->widget_y_axis]) ? (float) $result[$this->widget_y_axis] : $result[$this->widget_y_axis]);
        }

        $json = '$("#url").text("'.$this->get_domain_name_ga().'");';

        $fill_color = $this->widget_fill_color ? $this->widget_fill_color : '#00c7ef';

        $highcharts_data = [
            'chart'  => [
                'renderTo'   => 'widget_'.$this->widget_id,
                'styledMode' => true,
                'type'       => 'area'
            ],
            'title'  => [
                'text' => '',
            ],
            'colors' => ['#7ee2f6'],
            'xAxis'  => [
                'title'  => [
                    'align' => 'high',
                    'style' => ['font-style' => 'italic', 'font-weight' => 'normal'],
                    'text'  => $this->get_name()
                ],
                'categories'         => $x_axis,
                'gridLineColor'      => 'transparent',
                'labels'             => ['enabled' => false],
                'lineColor'          => 'transparent',
                'lineWidth'          => 0,
                'minorGridLineWidth' => 0,
                'minorTickLength'    => 0,
                'tickLength'         => 0,
                'visible'            => false
            ],
            'yAxis'  => [
                'title'         => ['text' => null],
                'labels'        => ['enabled' => false],
                'gridLineColor' => 'transparent',
                'visible'       => false
            ],
            'plotLines' => [
                ['value' => 0, 'width' => 1, 'lineWidth' => 1, 'color' => '#00c7ef']
            ],
            'tooltip' => ['formatter' => ''],
            'legend' => [
                'enabled'       => false,
                'layout'        => 'vertical',
                'align'         => 'right',
                'verticalAlign' => 'top',
                'x'             => -10,
                'y'             => 100,
                'borderWidth'   => 0
            ],
            'plotOptions' => [
                'series' => ['shadow' => false, 'threshold' => 0, 'lineWidth' => 1, 'color' => $fill_color, 'fillOpacity' => .5]
            ],
            'series' => [
                ['data' => $merge]
            ]
        ];

        $json .= 'var widget_'.$this->widget_id.' = new Highcharts.Chart('.json_encode($highcharts_data).');';

        return trim(preg_replace('/\s+/', ' ', $json));
    }

    private function serp_table_widget()
    {
        $keywords = $this->get_keywords();
        if(count($keywords) > 0)
        {
            return '$.post("/admin/reports/get_serp_widget/'.$this->widget_id.'",{report_id:'.$this->id.',report_type: '.$this->widget_type.'},function(result){
            $("#widget_'.$this->widget_id.'").html(result);
            });';
        }
		else
		{
			return '';
		}
    }

    private function line_report_chart()
    {
        $sql_result = $this->run_report(TRUE);
        $x_axis = '';
        $y_axis = '';
        $merge = '';
        foreach($sql_result AS $key=>$result)
        {
            $merge.='['.("'".$result[$this->chart->get_x_axis()]."'").','.((is_numeric($result[$this->chart->get_y_axis()])) ? $result[$this->chart->get_y_axis()] : "'".$result[$this->chart->get_y_axis()]."'").'],';
            $x_axis.=("'".$result[$this->chart->get_x_axis()]."'").',';
            $y_axis.=((is_numeric($result[$this->chart->get_y_axis()])) ? $result[$this->chart->get_y_axis()] : "'".$result[$this->chart->get_y_axis()]."'").',';
        }
        $merge = rtrim($merge,',');
        $x_axis = rtrim($x_axis,',');
        $y_axis = rtrim($y_axis,',');

        $json = '
$("#url").text("'.$this->get_domain_name_ga().'");
        var chart_'.$this->chart->get_id().';';
        $json .= 'chart_'.$this->chart->get_id().' = new Highcharts.Chart({
                    chart: {
                            renderTo: "chart_'.$this->chart_id.'",
                            type: "line"
                            },
                    title: {text:""},
                    colors: [
                        "#044077"
                    ],
                    xAxis: {categories: [' . $x_axis . ']},
                    yAxis: {title: {text: ""},
                    plotLines: [{value: 0,width: 1,color: "#044077"}]
                    },
                    tooltip: {formatter: this.y},
                    legend: {
                    enabled:false,
                    layout: "vertical",
                    align: "right",
                        verticalAlign: "top",
                        x: -10,
                        y: 100,
                        borderWidth: 0
                    },
                    plotOptions: {
                        series: {
                            threshold: 0
                    }
                    },
                    series: [{data: [' . $merge . ']}]})';

        return trim(preg_replace('/\s+/', ' ', $json));
    }

    private function bar_report_widget($data = null)
    {
        if ($data !== null) {
            $sql_result = $data;
        } else {
            $sql_result = $this->run_report(TRUE);
        }

        $x_axis = '';
        $y_axis = '';
        $merge = array();
        $labels = '';

        foreach($sql_result AS $key=>$result)
        {
			$result[$this->widget_x_axis] = str_replace('\'', "\\'", $result[$this->widget_x_axis]);
			$result[$this->widget_y_axis] = str_replace('\'', "\\'", $result[$this->widget_y_axis]);
			$merge[] = array($result[$this->widget_x_axis], ((is_numeric($result[$this->widget_y_axis])) ? (float) $result[$this->widget_y_axis] : $result[$this->widget_y_axis]));

            $x_axis.=("'".$result[$this->widget_x_axis]."'").',';
            $y_axis.=((is_numeric($result[$this->widget_y_axis])) ? $result[$this->widget_y_axis] : "'".$result[$this->widget_y_axis]."'").',';
        }
		$merge = json_encode($merge);
        $x_axis = rtrim($x_axis,',');
        $y_axis = rtrim($y_axis,',');
        if(isset($key) && $key > 5){
            $labels = "labels:{
            style:
            {fontSize: '8px',
            fontFamily: 'Verdana, sans-serif'
            }}";
        }
        $json = "
$('#url').text('".$this->get_domain_name_ga()."');
        var widget_".$this->widget_id.";
        if(document.getElementById('widget_" . $this->widget_id . "')) {
                chart = new Highcharts.Chart({
                    chart:{
                        renderTo:'widget_".$this->widget_id."',
                        type:'column'
                    },
                    title:{
                        text: ''
                    },
                    colors: [
                        '#044077'
                    ],
                    subtitle:{
                        text:''
                    },
                    xAxis:{
                        categories:[".$x_axis ."],
                        title:{
                            text:''
                        },
                        ".$labels."
                    },
                    yAxis:{
                        min:0,
                        title:{
                            text:''
                        },
                        labels: {
                             overflow: false
                        }
                    },
                    legend:{
                        enabled:false,
                        layout:'vertical',
                        backgroundColor:'#FFFFFF',
                        align:'left',
                        verticalAlign:'top',
                        x:100,
                        y:70,
                        floating:true,
                        shadow:true
                    },
                    tooltip:{
                        formatter:function ()
                        {
                            return '' +
                                    this.x + ': ' + this.y + ' ';
                        }
                    },
                    plotOptions:{
                        column:{
                            pointPadding:0,
                            borderWidth:0
                        }
                    },
                    series:[
                        {
                            name:'',
                            data: $merge
                        }
                    ]
                });}
	";
        return trim(preg_replace('/\s+/', ' ', $json));
    }

    private function bar_report_chart()
    {

        $sql_result = $this->run_report(TRUE);

		if (count($sql_result) == 0)
		{
			return '';
		}

        $x_axis = '';
        $y_axis = '';
        $merge = '';
        foreach($sql_result AS $key=>$result)
        {
			$result[$this->widget_x_axis] = str_replace('\'', "\\'", $result[$this->widget_x_axis]);
			$result[$this->widget_y_axis] = str_replace('\'', "\\'", $result[$this->widget_y_axis]);

            $merge.='['.("'".$result[$this->chart->get_x_axis()]."'").','.((is_numeric($result[$this->chart->get_y_axis()])) ? $result[$this->chart->get_y_axis()] : "'".$result[$this->chart->get_y_axis()]."'").'],';
            $x_axis.=("'".$result[$this->chart->get_x_axis()]."'").',';
            $y_axis.=((is_numeric($result[$this->chart->get_y_axis()])) ? $result[$this->chart->get_y_axis()] : "'".$result[$this->chart->get_y_axis()]."'").',';
        }
        $merge = rtrim($merge,',');
        $x_axis = rtrim($x_axis,',');
        $y_axis = rtrim($y_axis,',');
        $json = "
        $('#url').text('".$this->get_domain_name_ga()."');
        var chart_".$this->chart_id.";
        if(document.getElementById('widget_" . $this->chart_id . "')) {
                chart = new Highcharts.Chart({
                    chart:{
                        renderTo:'chart_".$this->chart_id."',
                        type:'column'
                    },
                    title:{
                        text:''
                    },
                    colors: [
                        '#044077'
                    ],
                    subtitle:{
                        text:''
                    },
                    xAxis:{
                        categories:[".$x_axis ."],
                        title:{
                            text:''
                        }
                    },
                    yAxis:{
                        min:0,
                        title:{
                            text:''
                        }
                    },
                    legend:{
                        enabled:false,
                        layout:'vertical',
                        backgroundColor:'#FFFFFF',
                        align:'left',
                        verticalAlign:'top',
                        x:100,
                        y:70,
                        floating:true,
                        shadow:true
                    },
                    tooltip:{
                        formatter:function ()
                        {
                            return '' +
                                    this.x + ': ' + this.y + ' ';
                        }
                    },
                    plotOptions:{
                        column:{
                            pointPadding:0,
                            borderWidth:0
                        }
                    },
                    series:[
                        {
                            name:'',
                            data:[". $merge ."]
                        }
                    ]
                });}
	";
        return trim(preg_replace('/\s+/', ' ', $json));
    }

    private function pie_report_chart()
    {

        $sql_result = $this->run_report(TRUE);
        $x_axis = '';
        $y_axis = '';
        $merge = '';
        foreach($sql_result AS $key=>$result)
        {
			$result[$this->widget_x_axis] = str_replace('\'', "\\'", $result[$this->widget_x_axis]);
			$result[$this->widget_y_axis] = str_replace('\'', "\\'", $result[$this->widget_y_axis]);
            $merge.='['.("'".$result[$this->chart->get_x_axis()]."'").','.((is_numeric($result[$this->chart->get_y_axis()])) ? $result[$this->chart->get_y_axis()] : "'".$result[$this->chart->get_y_axis()]."'").'],';
            $x_axis.=("'".$result[$this->chart->get_x_axis()]."'").',';
            $y_axis.=((is_numeric($result[$this->chart->get_y_axis()])) ? $result[$this->chart->get_y_axis()] : "'".$result[$this->chart->get_y_axis()]."'").',';
        }
        $merge = rtrim($merge,',');
        $x_axis = rtrim($x_axis,',');
        $y_axis = rtrim($y_axis,',');
        $json = "
$('#url').text('".$this->get_domain_name_ga()."');
        var chart_".$this->chart_id.";
        if(document.getElementById('widget_" . $this->chart_id . "')) {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo:'chart_".$this->chart_id."',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
                    percentageDecimals: 1
                },
                legend: {
                    itemWidth: 100
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        size: 150,
                        dataLabels: {
                            enabled: false,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function()
                            {
                                return '<b>' + this.point.name + '</b>: ' + this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [".$merge."]
                }]});
        }
";
        return trim(preg_replace('/\s+/', ' ', $json));
    }

    private function pie_report_widget()
    {

        $sql_result = $this->run_report(TRUE);

		if (count($sql_result) == 0)
		{
			return '';
		}

        $x_axis = '';
        $y_axis = '';
        $merge = '';
        foreach($sql_result AS $key=>$result)
        {
			$result[$this->widget_x_axis] = str_replace('\'', "\\'", $result[$this->widget_x_axis]);
			$result[$this->widget_y_axis] = str_replace('\'', "\\'", $result[$this->widget_y_axis]);

            $merge.='['.((is_numeric($result[$this->widget_x_axis])) ? $result[$this->widget_x_axis] : "'".substr($result[$this->widget_x_axis],0,10)."'").','.((is_numeric($result[$this->widget_y_axis])) ? $result[$this->widget_y_axis] : "'".substr($result[$this->widget_y_axis],0,10)."'").'],';
            $x_axis.=("'".$result[$this->widget_x_axis]."'").',';
            $y_axis.=((is_numeric($result[$this->widget_y_axis])) ? $result[$this->widget_y_axis] : "'".$result[$this->widget_y_axis]."'").',';
        }
        $merge = rtrim($merge,',');
        $x_axis = rtrim($x_axis,',');
        $y_axis = rtrim($y_axis,',');
        $json = "
        $('#url').text('".$this->get_domain_name_ga()."');
        var widget_".$this->widget_id.";
        if(document.getElementById('widget_" . $this->widget_id . "')) {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo:'widget_".$this->widget_id."',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
                    percentageDecimals: 1
                },
                legend: {
                    itemWidth: 75
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        size: 100,
                        dataLabels: {
                            enabled: false,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>' + this.point.name + '</b>: ' + this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    type: 'pie',
                    data: [".$merge."]
                }]
            });
        }
        ";
        return trim(preg_replace('/\s+/', ' ', $json));
    }

    private function donut_report_widget()
    {

        $sql_result = $this->run_report(TRUE);

        if (count($sql_result) == 0) return '';

        $x_axis = '';
        $y_axis = '';
        $merge = '';
        foreach($sql_result AS $result)
        {
            $result[$this->widget_x_axis] = str_replace('\'', "\\'", $result[$this->widget_x_axis]);
            $result[$this->widget_y_axis] = str_replace('\'', "\\'", $result[$this->widget_y_axis]);

            $merge.='['.((is_numeric($result[$this->widget_x_axis])) ? $result[$this->widget_x_axis] : "'".substr($result[$this->widget_x_axis],0,10)."'").','.((is_numeric($result[$this->widget_y_axis])) ? $result[$this->widget_y_axis] : "'".substr($result[$this->widget_y_axis],0,10)."'").'],';
            $x_axis.=("'".$result[$this->widget_x_axis]."'").',';
            $y_axis.=((is_numeric($result[$this->widget_y_axis])) ? $result[$this->widget_y_axis] : "'".$result[$this->widget_y_axis]."'").',';
        }
        $merge = rtrim($merge,',');
        $json = "
        $('#url').text('".$this->get_domain_name_ga()."');
        var widget_".$this->widget_id.";
        if(document.getElementById('widget_" . $this->widget_id . "')) {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo:'widget_".$this->widget_id."',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: ''
                },
                tooltip: {
                    backgroundColor: 'transparent',
                    borderWidth: 0,
                    shadow: false,
                    formatter:  function() { return '<div><strong>' + this.key + '</strong><br />' + this.y+'</div>'; },
                    useHTML: true,
                    style: {
                        textAlign: 'center'
                    }
                },
                legend: {
                    align: 'right',
                    verticalAlign: 'top',
                    layout: 'vertical',
                    borderWidth: 0,
                    itemStyle: {'fontSize': '18px', 'fontWeight': '300'},
                    itemMarginBottom: 20,
                    symbolHeight: 18
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        size: 200,
                        dataLabels: {
                            enabled: false,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>' + this.point.name + '</b>: ' + this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    type: 'pie',
                    innerSize: '50%',
                    data: [".$merge."]
                }]
            });
        }
        ";
        return trim(preg_replace('/\s+/', ' ', $json));
    }

	private function quick_stats_widget()
	{
		try
		{
			$sql_result = $this->run_report(TRUE);

			if (count($sql_result) == 0)
			{
				return '';
			}

			$result = '<div class="quick_stats">';
			foreach ($sql_result as $stat)
			{
				$result .= '<div class="quick_stat">'.
					'<div class="quick_stat-header">'.$stat[$this->widget_x_axis].'</div>'.
					'<div class="quick_stat-body">'.$stat[$this->widget_y_axis].'</div>'.
				'</div>';
			}

			$result = '$("#widget_'.$this->widget_id.'").html('.json_encode($result).')';
		}
		catch (Exception $e)
		{
			$result = 'Quick stats generation failed';
		}
		return $result;
	}

    private function survey_question_group_widget()
    {
        try {
            $questions = $this->run_report(TRUE);

            foreach ($questions as $key => $question) {

                $question_orm = new Model_Question($question['id']);
                $answer_type  = $question_orm->answer->type->stub;
                $answers_data = Model_Survey::get_answer_responses($question['survey_id'], $question['id']);

                $questions[$key]['answer_type']     = $answer_type;
                $questions[$key]['answers_data']    = $answers_data;
                $questions[$key]['total_responses'] = 0;

                if (in_array($answer_type, array('checkbox', 'radio', 'select')))
                {
                    $total  = 0;
                    $x_axis = array();
                    $series_data = array();
                    foreach ($answers_data as $answer_data) {
                        $total        += $answer_data['count'];
                        $x_axis[]      = htmlentities($answer_data['answer']);
                        $series_data[] = array($answer_data['answer'], (int) $answer_data['count']);
                    }
                    $series = array(array('name' => '', 'data' => $series_data));

                    $questions[$key]['total_responses'] = $total;
                    $questions[$key]['highchart_data']  = array(
                        'chart'       => array(
                            'renderTo'   => 'survey-question-group-'.($question['order_id'] + 1),
                            'type'       => 'column'
                        ),
                        'title'       => array('text' => ''),
                        'subtitle'    => array('text' => ''),
                        'xAxis'       => array(
                            'categories' => $x_axis,
                            'title'      => array('text' => '')
                        ),
                        'yAxis'       => array(
                            'min'        => 0,
                            'title'      => array('text' => ''),
                            'lineColor'  => 'transparent',
                            'lineWidth'  => 0,
                            'labels'     => array('overflow' => false, 'enabled' => false),
                            'gridLineColor' => 'transparent',
                        ),
                        'legend'      => array('enabled' => false),
                        'tooltip'     => array('formatter' => null),
                        'plotOptions' => array(
                            'column'     => array('pointPadding' => 0, 'borderWidth' => 0, 'minPointLength' => 0),
                            'series'     => array('dataLabels' => array('enabled' => true))
                        ),
                        'series'      => $series
                    );
                }
                else if (in_array($answer_type, array('input', 'textarea')))
                {
                    $questions[$key]['total_responses'] = count($answers_data);
                }
            }

            $result = View::factory('widgets/survey_question_group')
                ->set('report', $this)
                ->set('questions', $questions)
                ->render();

            $result  = "$('#widget_".$this->widget_id."').removeAttr('style').removeClass('flex-center').html(".json_encode($result).").parent().css('height', 'auto')";

        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, $e->getMessage()."\n".$e->getTraceAsString());
            $result = 'Survey question group widget failed.<pre>'.$e->getMessage()."\n".$e->getTraceAsString().'</pre>';
        }
        return $result;
    }

	private function table_widget()
	{
		try
		{
			$sql_result = $this->run_report(TRUE);

			if (count($sql_result) == 0)
			{
				return '';
			}

			$columns = $this->parse_columns($sql_result);
			$result     = '<div class="widget_table_wrapper"><table class="table table-striped widget_table"><thead>';
			foreach ($columns as $column)
			{
				$result .= '<th scope="col">'.$column.'</th>';
			}
			$result .= '</thead><tbody>';
			foreach ($sql_result as $row)
			{
				$result .= '<tr>';
				foreach ($row as $cell)
				{
					$result .= '<td>'.$cell.'</td>';
				}
				$result .= '</tr>';
			}
			$result .= '</tbody></table></div>';
			$result  = '$("#widget_'.$this->widget_id.'").html('.json_encode($result).')';

		}
		catch (Exception $e)
		{
			$result = 'Table generation failed';
		}
		return $result;
	}

    private function gannt_report_widget()
    {
        try
		{
            $sql_result = $this->run_report(TRUE);
            $result = '
            var ganttData;
            ganttData = [';
            foreach($sql_result AS $key=>$series)
            {
                $forecast_start = explode('-',date("Y-m-d",strtotime($series['forecast_start'])));
                $forecast_end = explode('-',date("Y-m-d",strtotime($series['forecast_end'])));
                $actual_start = explode('-',date("Y-m-d",strtotime($series['actual_start'])));
                $actual_end = explode('-',date("Y-m-d",strtotime($series['actual_end'])));
                $result.= '{';
                $result.='id: '.($key+1).',name: "'.$series['name'].'", series: [
			{ name: "Forecast", start: new Date('.$forecast_start[0].','.($forecast_start[1]-1).','.$forecast_start[2].'), end: new Date('.$forecast_end[0].','.($forecast_end[1]-1).','.$forecast_end[2].') },
			{ name: "Actual", start: new Date('.$actual_start[0].','.($actual_start[1]-1).','.$actual_start[2].'), end: new Date('.$actual_end[0].','.($actual_end[1]-1).','.$actual_end[2].'), color: "#f0f0f0" }
		    ]';
                $result.= '}'.(($key < count($sql_result) -1) ? ',' : '');
            }
            $result.='];';
        }catch(Exception $e)
        {
            $result = 'Gannt Generation Failed';
        }

        $result.='
        $("#widget_'.$this->widget_id.'").html("");
        $("#widget_'.$this->widget_id.'").ganttView({
				data: ganttData,
				slideWidth: 500,
				behavior: {
					onClick: function (data) {
						var msg = "You clicked on an event: { start: " + data.start.toString("M/d/yyyy") + ", end: " + data.end.toString("M/d/yyyy") + " }";
						$("#eventMessage").text(msg);
					},
					onResize: function (data) {
						var msg = "You resized an event: { start: " + data.start.toString("M/d/yyyy") + ", end: " + data.end.toString("M/d/yyyy") + " }";
						$("#eventMessage").text(msg);
					},
					onDrag: function (data) {
						var msg = "You dragged an event: { start: " + data.start.toString("M/d/yyyy") + ", end: " + data.end.toString("M/d/yyyy") + " }";
						$("#eventMessage").text(msg);
					}
				}
			});';
        return $result;
    }

    private function gannt_report_chart()
    {
        try{
            $sql_result = $this->run_report(TRUE);

			if (count($sql_result) == 0)
			{
				return '';
			}

            $result = '
            var ganttData;
            ganttData = [';
            foreach($sql_result AS $key=>$series)
            {
                $forecast_start = explode('-',date("Y-m-d",strtotime($series['forecast_start'])));
                $forecast_end = explode('-',date("Y-m-d",strtotime($series['forecast_end'])));
                $actual_start = explode('-',date("Y-m-d",strtotime($series['actual_start'])));
                $actual_end = explode('-',date("Y-m-d",strtotime($series['actual_end'])));
                $result.= '{';
                $result.='id: '.($key+1).',name: "'.$series['name'].'", series: [
			{ name: "Forecast", start: new Date('.$forecast_start[0].','.($forecast_start[1]-1).','.$forecast_start[2].'), end: new Date('.$forecast_end[0].','.($forecast_end[1]-1).','.$forecast_end[2].') },
			{ name: "Actual", start: new Date('.$actual_start[0].','.($actual_start[1]-1).','.$actual_start[2].'), end: new Date('.$actual_end[0].','.($actual_end[1]-1).','.$actual_end[2].'), color: "#f0f0f0" }
		    ]';
                $result.= '}'.(($key < count($sql_result) -1) ? ',' : '');
            }
            $result.='];';
        }
		catch(Exception $e)
        {
            $result = 'Gannt Generation Failed';
        }

        $result.='
        $("#chart_'.$this->chart_id.'").html("");
        $("#chart_'.$this->chart_id.'").ganttView({
				data: ganttData,
				slideWidth: 717,
				behavior: {
					onClick: function (data) {
						var msg = "You clicked on an event: { start: " + data.start.toString("M/d/yyyy") + ", end: " + data.end.toString("M/d/yyyy") + " }";
						$("#eventMessage").text(msg);
					},
					onResize: function (data) {
						var msg = "You resized an event: { start: " + data.start.toString("M/d/yyyy") + ", end: " + data.end.toString("M/d/yyyy") + " }";
						$("#eventMessage").text(msg);
					},
					onDrag: function (data) {
						var msg = "You dragged an event: { start: " + data.start.toString("M/d/yyyy") + ", end: " + data.end.toString("M/d/yyyy") + " }";
						$("#eventMessage").text(msg);
					}
				}
			});';
        return $result;
    }

	private function calendar_widget()
	{
		try
		{
			$events  = $this->run_report(TRUE);
			$result  = View::factory('widgets/calendar')->set('events', $events)->render();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, "Error rendering calendar\n".$e->getTraceAsString());
			$result = 'Calendar generation failed.';
		}
		return json_encode($result);
	}

    private static function where_clauses($query, $where_clauses)
    {
        foreach ($where_clauses as $clause)
        {
            if     ($clause == 'open')                        $query = $query->where_open ();
            elseif ($clause == 'close')                       $query = $query->where_close();
            elseif (isset($clause[3]) AND $clause[3] == 'or') $query = $query->or_where ($clause[0], $clause[1], $clause[2]);
            else                                              $query = $query->and_where($clause[0], $clause[1], $clause[2]);
        }
        return $query;
    }

	/* Return the HTML for the report's widget */
	public function render_widget()
	{
		$return = '';
		$widget_type = ORM::factory('Reports_WidgetType', $this->widget_type);

		$per_row = Settings::instance()->get('reports_widgets_per_row');
		// Ensure that the setting contains a whole-number value, otherwise use 3
		$per_row = ((string)(int)$per_row == $per_row) ? $per_row : 2;

		// @TODO: Better option for custom reports than hardcoded references to their names
		$is_map = ($this->name == 'Business Map' AND Kohana::find_file('views', 'widget_active_policies_map'.EXT, FALSE, EXT));
		$is_rab = ($this->name == 'RAB'          AND Kohana::find_file('views', 'widget_rab'.EXT, FALSE, EXT) AND class_exists('Model_Courses'));

		$custom_body = FALSE;
		if ($is_map)
		{
			$custom_body = View::factory('widget_active_policies_map');
		}
		elseif ($is_rab)
		{
			$locations   = Model_Locations::get_locations_with_sublocation_ids();
			$trainers    = Model_Contacts3::get_teachers();
			$custom_body = View::factory('widget_rab')->set('report', $this)->set('locations', $locations)->set('trainers', $trainers);
		}
		elseif ($widget_type->stub == 'raw_html')
		{
			$custom_body = IbHelpers::expand_short_tags($this->get_widget_html());
		}
		elseif ($widget_type->stub == 'calendar')
		{
			$custom_body = json_decode($this->get_widget_json());
		}

		if ($this->get_widget_json() != 'undefined' OR $is_map OR $is_rab)
		{
			$return = View::factory('dashboard_reports_widget')
				->set('is_map',      $is_map)
				->set('is_rab',      $is_rab)
				->set('report',      $this)
				->set('widget_type', $widget_type)
				->set('custom_body', $custom_body)
				->set('per_row',     $per_row);
		}
		return $return;
	}

	public static function clone_report($id)
	{
		if(!is_numeric($id)){
			return false;
		}
		$new_id = null;
		try{
            Database::instance()->begin();
			$clone_query = 'INSERT INTO plugin_reports_reports
(`name`,
summary,
`sql`,
category,
sub_category,
dashboard,
created_by,
modified_by,
date_created,
date_modified,
publish,
`delete`,
widget_id,
chart_id,
link_url,
link_column,
report_type,
autoload,
checkbox_column,
action_button_label,
action_button,
action_event,
checkbox_column_label,
autosum,
column_value,
autocheck,
custom_report_rules,
bulk_message_sms_number_column,
bulk_message_email_column,
bulk_message_subject_column,
bulk_message_subject,
bulk_message_body_column,
bulk_message_body,
bulk_message_interval,
php_modifier,
php_post_filter,
generate_documents,
generate_documents_template_file_id,
generate_documents_pdf,
generate_documents_office_print,
generate_documents_office_print_bulk,
generate_documents_tray,
generate_documents_helper_method,
generate_documents_link_to_contact,
generate_documents_link_by_template_variable,
generate_documents_mode,
generate_documents_row_variable,
totals_columns,
totals_group,
show_results_counter,
results_counter_text,
bulk_messages_per_minute)

(SELECT
CONCAT(\'CLONE - \', `name`),
summary,
`sql`,
category,
sub_category,
dashboard,
created_by,
modified_by,
NOW(),
NOW(),
publish,
`delete`,
widget_id,
chart_id,
link_url,
link_column,
report_type,
autoload,
checkbox_column,
action_button_label,
action_button,
action_event,
checkbox_column_label,
autosum,
column_value,
autocheck,
custom_report_rules,
bulk_message_sms_number_column,
bulk_message_email_column,
bulk_message_subject_column,
bulk_message_subject,
bulk_message_body_column,
bulk_message_body,
bulk_message_interval,
php_modifier,
php_post_filter,
generate_documents,
generate_documents_template_file_id,
generate_documents_pdf,
generate_documents_office_print,
generate_documents_office_print_bulk,
generate_documents_tray,
generate_documents_helper_method,
generate_documents_link_to_contact,
generate_documents_link_by_template_variable,
generate_documents_mode,
generate_documents_row_variable,
totals_columns,
totals_group,
show_results_counter,
results_counter_text,
bulk_messages_per_minute
FROM
plugin_reports_reports
WHERE id=' . $id . ')';
			$result = DB::query(Database::INSERT, $clone_query)->execute();
			if($result){
				$new_id = $result[0];
				
				$clone_parameters_query = 'INSERT INTO plugin_reports_parameters 
(report_id,
type,
`name`,
`value`,
`delete`)

(SELECT
' . $new_id . ',
type,
`name`,
`value`,
`delete`
FROM plugin_reports_parameters
WHERE report_id=' . $id . ' and `delete` = 0)';
				DB::query(null, $clone_parameters_query)->execute();
				
				$clone_sharing_query = 'INSERT INTO plugin_reports_report_sharing 
(report_id,
group_id)

(SELECT
' . $new_id . ',
group_id
FROM
plugin_reports_report_sharing
WHERE report_id=' . $id . ')';
				DB::query(null, $clone_sharing_query)->execute();
				
				$clone_user_options_query = 'INSERT INTO plugin_reports_user_options 
(user_id,
report_id,
`order`,
date_created,
date_modified)

(SELECT
user_id,
' . $new_id . ',
`order`,
NOW(),
NOW()
FROM
plugin_reports_user_options
WHERE report_id=' . $id . ')';
				DB::query(null, $clone_user_options_query)->execute();
				
				$clone_keywords_query = 'INSERT INTO plugin_reports_keywords 
(url,
keyword,
last_updated,
last_position,
current_position,
`delete`,
report_id)

(SELECT
url,
keyword,
last_updated,
last_position,
current_position,
`delete`,
' . $new_id . '
FROM
plugin_reports_keywords
WHERE report_id=' . $id . ' and `delete` = 0)';
				DB::query(null, $clone_keywords_query)->execute();
				
				$clone_favorites_query = 'INSERT INTO plugin_reports_favorites 
(report_id,
user_id)

(SELECT
' . $new_id . ',
user_id
FROM
plugin_reports_favorites
WHERE report_id=' . $id . ')';
				DB::query(null, $clone_favorites_query)->execute();
			}
            Database::instance()->commit();
			
		} catch(Exception $exc){
            Database::instance()->rollback();
			throw $exc;
		}
		return $new_id;
	}
	
	public function list_versions()
	{
		return DB::select('v.*', 'u.name', 'u.surname')
					->from(array('plugin_reports_versions', 'v'))
						->join(array('engine_users', 'u'), 'inner')
							->on('v.created_by', '=', 'u.id')
					->where('v.report_id', '=', $this->id)
					->execute()
					->as_array();
	}
	
	public static function save_version($id)
	{
		$user = Auth::instance()->get_user();
		$data = array();
		$data['plugin_reports_reports'] = DB::select('*')->from('plugin_reports_reports')->where('id', '=', $id)->execute()->as_array();
		$data['plugin_reports_report_sharing'] = DB::select('*')->from('plugin_reports_report_sharing')->where('report_id', '=', $id)->execute()->as_array();
		$data['plugin_reports_parameters'] = DB::select('*')->from('plugin_reports_parameters')->where('report_id', '=', $id)->execute()->as_array();
		$data['plugin_reports_user_options'] = DB::select('*')->from('plugin_reports_user_options')->where('report_id', '=', $id)->execute()->as_array();
		$data['plugin_reports_favorites'] = DB::select('*')->from('plugin_reports_favorites')->where('report_id', '=', $id)->execute()->as_array();
		$data_json = json_encode($data);
		$previous = DB::select('*')->from('plugin_reports_reports')->where('id', '=', $id)->execute()->as_array();
		$idata = array('report_id' => $id,
						'created_date' => date('Y-m-d H:i:s'),
						'created_by' => $user['id'],
						'data_json' => $data_json);
		$result = DB::insert('plugin_reports_versions', array_keys($idata))->values($idata)->execute();
		if($result){
			return $result[0];
		} else {
			return false;
		}
	}
	
	public static function rollback_to_version($report_id, $version_id)
	{
		$previous = DB::select('*')
						->from('plugin_reports_versions')
						->where('id', '=', $version_id)
						->and_where('report_id', '=', $report_id)
						->execute()
						->as_array();
		if($previous){
			$already_rolledback = DB::select('*')->from('plugin_reports_reports')->where('id', '=', $report_id)->execute()->as_array();
			if(@$already_rolledback[0]['rolledback_to_version'] == null){
				self::save_version($report_id);
			}
			$data = json_decode($previous[0]['data_json'], true);

			try{
                Database::instance()->begin();
				//DB::delete()->from('plugin_reports_reports')->where('id', '=', $report_id)->execute();
				DB::delete('plugin_reports_report_sharing')->where('report_id', '=', $report_id)->execute();
				DB::delete('plugin_reports_parameters')->where('report_id', '=', $report_id)->execute();
				DB::delete('plugin_reports_user_options')->where('report_id', '=', $report_id)->execute();
				DB::delete('plugin_reports_favorites')->where('report_id', '=', $report_id)->execute();
				
				$data['plugin_reports_reports'][0]['rolledback_to_version'] = $version_id;
				DB::update('plugin_reports_reports')
					->set($data['plugin_reports_reports'][0])
					->where('id', '=', $report_id)
					->execute();
				
				unset($data['plugin_reports_reports']);
				foreach($data as $table => $rows){
					foreach($rows as $row){
						unset($row['id']);
						DB::insert($table, array_keys($row))->values($row)->execute();
					}
				}
                Database::instance()->commit();
				return true;
			} catch(Exception $exc){

                Database::instance()->rollback();
                throw $exc;
			}
		}
		return false;
	}
	
	public static function get_sql_value($value)
	{
		if(in_array($value, array("NOW()", "CURDATE()", "DATE_SUB(NOW(), INTERVAL 1 MONTH)", "DATE_FORMAT(NOW() ,'%Y-%m-01')", "LAST_DAY(CURDATE())", "DATE_SUB(NOW(), INTERVAL 1 YEAR)"))){
			return DB::select(DB::expr($value . ' AS val'))->execute()->get('val');
		} else {
			return $value;
		}
	}

    public static function replaceSqlParams($sql, $params)
    {
        foreach ($params as $param => $value) {
            $sql = str_replace('{!' . $param . '!}', $value, $sql);
        }
        return $sql;
    }

    public function generate_documents_bulk($filledParams, $noprint = false)
    {
        $this->get(true);

        foreach ($filledParams as $param => $value) {
            if (is_array($value)) {

            } else {
                $value = trim($value);
                if ($value == '') {
                    unset($filledParams[$param]);
                    continue;
                }
                if ($param == 'date' || preg_match('/\d\d\-\d\d\-\d\d\d\d/', $value)) {
                    $value = Date::dmy_to_ymd($value);
                }
                $filledParams[$param] = $value;
            }
        }
        $reportParams = DB::select('*')
            ->from(self::PARAMETERS_TABLE)
            ->where('report_id','=',$this->id)
            ->and_where('delete','=',0)
            ->execute()
            ->as_array();
        $error = false;
        $requiredParams = array();
        $iterateParams = array();
        foreach ($reportParams as $reportParam) {
            if (in_array($reportParam['type'], array('text', 'date', 'sql', 'month')) && !isset($filledParams[$reportParam['name']]) && !is_array($filledParams[$reportParam['name']])) {
                $requiredParams[] = $reportParam['name'];
                $error = true;
            }
            if (!isset($filledParams[$reportParam['name']]) || is_array($filledParams[$reportParam['name']])) {
                $iterateParams[] = $reportParam;
            }
        }
        if ($error) {
            echo json_encode(
                array(
                'error' => true,
                'requiredParams' => $requiredParams
                )
            );
            exit();
        } else {
            $results = array();
            /*
             * only two parameters can be handled for now
             * requires complex logic for more
             */
            $iValues1 = array();
            $iLabels1 = array();
            $iValues2 = array();
            if (isset($iterateParams[0])) {
                if ($iterateParams[0]['type'] == 'dropdown') {
                    $iValues1 = explode(',', $iterateParams[0]['value']);
                } else {
                    if ($iterateParams[0]['type'] == 'custom') {
                        $psql = $iterateParams[0]['value'];
                        foreach ($filledParams as $param => $value) {
                            if (!is_array($value)) {
                                $psql = str_replace('{!' . $param . '!}', $value, $psql);
                            }
                        }
                        foreach (DB::query(Database::SELECT, $psql)->execute()->as_array() as $iValue) {
                            if (isset($filledParams[$iterateParams[0]['name']])) {
                                if (in_array(current($iValue), $filledParams[$iterateParams[0]['name']])) {
                                    $iValues1[] = current($iValue);
                                    $iLabels1[] = next($iValue);
                                }
                            } else {
                                $iValues1[] = current($iValue);
                                $iLabels1[] = next($iValue);
                            }
                        }
                    }
                }
            }

            foreach ($iValues1 as $i1 => $iValue1) {
                $eParams = $filledParams;
                $eParams[$iterateParams[0]['name']] = $iValue1;
                if (strpos($iterateParams[0]['name'], '_id') !== false) {
                    $eParams[str_replace('_id', '', $iterateParams[0]['name'])] = $iLabels1[$i1];
                }

                if (isset($iterateParams[1])) {
                    if ($iterateParams[1]['type'] == 'dropdown') {
                        $iValues2 = explode(',', $iterateParams[1]['value']);
                        foreach ($iValues2 as $iValue2) {
                            $eParams[$iterateParams[1]['name']] = $iValue2;
                            $results[] = array(
                                'params' => $eParams,
                                'sql' => self::replaceSqlParams($this->_sql, $eParams),
                                'data' => array()
                            );
                        }
                    } else {
                        if ($iterateParams[1]['type'] == 'custom') {
                            $sql = $iterateParams[1]['value'];
                            foreach ($filledParams as $param => $value) {
                                if (!is_array($value)) {
                                    $sql = str_replace('{!' . $param . '!}', $value, $sql);
                                }
                            }
                            $sql = str_replace('{!' . $iterateParams[0]['name'] . '!}', $iValue1, $sql);
                            //echo json_encode(array('sql' => $sql));exit();
                            $iValues2 = DB::query(Database::SELECT, $sql)->execute()->as_array();
                            if (count($iValues2)) {
                                foreach ($iValues2 as $iValue) {
                                    if ((isset($filledParams[$iterateParams[1]['name']]) &&
                                            in_array(current($iValue), $filledParams[$iterateParams[1]['name']])) ||
                                        !isset($filledParams[$iterateParams[1]['name']])) {
                                        $eParams[$iterateParams[1]['name']] = current($iValue);
                                        if (strpos($iterateParams[1]['name'], '_id') !== false) {
                                            $eParams[str_replace('_id', '', $iterateParams[1]['name'])] = next($iValue);
                                        }
                                        $results[] = array(
                                            'params' => $eParams,
                                            'sql' => self::replaceSqlParams($this->_sql, $eParams),
                                            'data' => array()
                                        );
                                    }
                                }
                            } else {
                                $results[] = array(
                                    'params' => $eParams,
                                    'sql' => self::replaceSqlParams($this->_sql, $eParams),
                                    'data' => array()
                                );
                            }
                        }
                    }
                } else {
                    $results[] = array(
                        'params' => $eParams,
                        'sql' => self::replaceSqlParams($this->_sql, $eParams),
                        'data' => array()
                    );
                }
            }

            foreach ($results as $ri => $result) {
                $this->_sql = $result['sql'];
                $this->parameter_fields = array();
                foreach($result['params'] as $param => $value) {
                    $this->parameter_fields[] = array('name' => $param, 'value' => $value, 'type' => 'text');
                }

                if($this->php_modifier){
                    try {
                        eval($this->php_modifier);
                    } catch (Exception $exc) {
                        $results[$ri]['exc'] = $exc->getMessage();
                    }
                }

                $results[$ri]['sqlp'] = $this->_sql;
                $data = self::executeMultiSql($this->_sql);

                if($this->php_post_filter){
                    try {
                        eval($this->php_post_filter);
                    } catch (Exception $exc) {
                        $results[$ri]['exc'] = $exc->getMessage();
                    }
                }

                $results[$ri]['data'] = $data;
            }

            foreach ($results as $ri => $result) {
                if (count($results[$ri]['data'])) {
                    foreach ($results[$ri]['data'] as $wi => $row) {
                        $results[$ri]['x'] = 1;
                        foreach ($row as $column => $cvalue) {
                            if (strpos($cvalue, '<input') !== false) {
                                preg_match('/value="(.*?)"/', $cvalue, $match);
                                $cvalue = @$match[1];
                            } else {
                                if (strpos($cvalue, '<var') !== false) {
                                    preg_match('/>(.*?)</', $cvalue, $match);
                                    $cvalue = @$match[1];
                                }
                            }
                            $results[$ri]['data'][$wi][$column] = $cvalue;
                        }
                    }
                    $results[$ri]['print'] = $this->generate_documents_from_data(
                        $results[$ri]['params'],
                        $results[$ri]['data'],
                        $noprint
                    );
                }
            }

            return array(
                'error' => false,
                'results' => $results
            );
        }
    }

    public function generate_documents_from_data($params, $data, $noprint = false, $extra_params = array())
    {
        $this->get(true);
        $docTemplateValues = $params;
        if ($this->generate_documents_helper_method) {
            $generate_documents_helper = explode('->', $this->generate_documents_helper_method);
            $generate_documents_helper_class = $generate_documents_helper[0];
            $generate_documents_helper_method = $generate_documents_helper[1];
            $docParameters = array();
            $docValues = array();
            foreach (get_class_methods($generate_documents_helper_class) as $docHelper) {
                if ($docHelper == $generate_documents_helper_method) {
                    $rm = new ReflectionMethod($generate_documents_helper_class, $docHelper);
                    foreach ($rm->getParameters() as $param) {
                        $docParameters[] = $param->getName();
                    }
                }
            }
            $firstDocParam = null;
            foreach ($docParameters as $pname) {
                if (isset($params[$pname])) {
                    $docValues[$pname] = $params[$pname];
                }
            }
            $docValues['table'] = &$data;
            $dah = new $generate_documents_helper_class();
            if ($this->generate_documents_row_variable) {
                $docTemplateValues = call_user_func_array(array($dah, $generate_documents_helper_method), array_merge(array($params[$this->generate_documents_row_variable]), $extra_params));
            } else {
                $docTemplateValues = call_user_func_array(array($dah, $generate_documents_helper_method), $docValues);
            }
        } else {
            $docTemplateValues['table'] = $data;
        }

        $nparams = $params;
        foreach ($nparams as $param => $value) {
            if (is_array($value)) {
                $nparams[$param] = implode('-', $value);
            }
        }
        $docName = preg_replace('/[^a-z0-9\-]+/', '', strtolower($this->name . '-' . implode('-', $nparams)));
        $docDir = '';
        $document = new Model_Document();
        $document->doc_gen_and_storage(
            $this->generate_documents_template_file_id,
            $docTemplateValues,
            'report',
            $docName,
            0,
            '',
            1,
            $this->generate_documents_pdf == 1
        );

        $generatedFiles = array();
        $messageCreated = false;
        try {
            if ($this->generate_documents_link_by_template_variable != '') {
                try {
                    $docDir = Model_Document::get_destination_path();

                    $docDir .= '/contacts';
                    $dirId = Model_Files::get_directory_id($docDir);
                    if ($dirId == null) {
                        throw new Exception("no directory");
                    }
                } catch (Exception $exc) {
                    $dirId0 = Model_Files::get_directory_id($docDir);
                    Model_Files::create_directory($dirId0, $docDir);
                    $dirId = Model_Files::get_directory_id($docDir);
                }
                $docDir .= '/' . $docTemplateValues[$this->generate_documents_link_by_template_variable];
                try {
                    $dirId2 = Model_Files::get_directory_id($docDir);
                    if ($dirId2 == null) {
                        throw new Exception("no directory");
                    }
                } catch (Exception $exc) {
                    Model_Files::create_directory($dirId, $docTemplateValues[$this->generate_documents_link_by_template_variable]);
                    $dirId2 = Model_Files::get_directory_id($docDir);
                }
            } else if (isset($params[$this->generate_documents_link_to_contact])) {
                if (is_array($params[$this->generate_documents_link_to_contact]) && count($params[$this->generate_documents_link_to_contact]) == 1) {
                    $params[$this->generate_documents_link_to_contact] = $params[$this->generate_documents_link_to_contact][0];
                }
                try {
                    $docDir = Model_Document::get_destination_path();

                    $docDir .= '/contacts';
                    $dirId = Model_Files::get_directory_id($docDir);
                    if ($dirId == null) {
                        throw new Exception("no directory");
                    }
                } catch (Exception $exc) {
                    $dirId0 = Model_Files::get_directory_id($docDir);
                    Model_Files::create_directory($dirId0, $docDir);
                    $dirId = Model_Files::get_directory_id($docDir);
                }
                $docDir .= '/' . $params[$this->generate_documents_link_to_contact];
                try {
                    $dirId2 = Model_Files::get_directory_id($docDir);
                    if ($dirId2 == null) {
                        throw new Exception("no directory");
                    }
                } catch (Exception $exc) {
                    Model_Files::create_directory($dirId, $params[$this->generate_documents_link_to_contact]);
                    $dirId2 = Model_Files::get_directory_id($docDir);
                }
            } else {
                $docDir = '/Report Documents';
                try {
                    $dirId = Model_Files::get_directory_id($docDir);
                    if ($dirId == null) {
                        throw new Exception("no directory");
                    }
                } catch (Exception $exc) {
                    Model_Files::create_directory(1, 'Report Documents');
                    $dirId = Model_Files::get_directory_id($docDir);
                }

                $docDir .= '/' . $this->name;
                try {
                    $dirId2 = Model_Files::get_directory_id($docDir);
                    if ($dirId2 == null) {
                        throw new Exception("no directory");
                    }
                } catch (Exception $exc) {
                    Model_Files::create_directory($dirId, $this->name);
                    $dirId2 = Model_Files::get_directory_id($docDir);
                }
            }

            if (Settings::instance()->get("doc_save") == 1 && $document->generated_documents['url_docx'] != '') {
                $fileInfo = array(
                    'name' => $docName . '.docx',
                    'type' => mime_content_type($document->generated_documents['url_docx']),
                    'size' => filesize($document->generated_documents['url_docx']),
                    'tmp_name' => $document->generated_documents['url_docx'],
                );

                $fileId = Model_Files::create_file($dirId2, $docName . '.docx', $fileInfo, null);
                $generatedFiles[] = array(
                    'file_id' => $fileId,
                    'filename' => $docDir . '/' . $docName . '.docx'
                );
            }

            if ($this->generate_documents_pdf == 1 && Settings::instance()->get("word2pdf_savepdf") == 1 && $document->generated_documents['url_pdf'] != '') {
                $fileInfo = array(
                    'name' => $docName . '.pdf',
                    'type' => mime_content_type($document->generated_documents['url_pdf']),
                    'size' => filesize($document->generated_documents['url_pdf']),
                    'tmp_name' => $document->generated_documents['url_pdf'],
                );

                $fileId = Model_Files::create_file($dirId2, $docName . '.pdf', $fileInfo, null);
                $generatedFiles[] = array(
                    'file_id' => $fileId,
                    'filename' => $docDir . '/' . $docName . '.pdf'
                );
            }

            if (Settings::instance()->get("print_office") == 1 && !$noprint) {
                if ($this->generate_documents_office_print) {
                    $attach = '';
                    if ($this->generate_documents_pdf == 1) {
                        $attach = $document->generated_documents['url_pdf'];
                    } else {
                        $attach = $document->generated_documents['url_docx'];
                    }

                    $recipients = array();
                    if ($this->generate_documents_tray != '') {
                        $recipients[] = array(
                            'target_type' => 'EMAIL',
                            'target' => $this->generate_documents_tray
                        );
                    }
                    if (Settings::instance()->get("print_backup_email") != '') {
                        $recipients[] = array(
                            'target_type' => 'EMAIL',
                            'target' => Settings::instance()->get("print_backup_email")
                        );
                    }
                    $templateParams = array(
                        'reportname' => $this->name
                    );

                    $mm = new Model_Messaging();
                    $messageCreated = $mm->send_template(
                        'report-document-print',
                        array(
                            'attachments' => array(
                                array(
                                    'path' => $attach,
                                    'name' => basename($attach),
                                    'type' => $this->generate_documents_pdf == 1 ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                )
                            )
                        ),
                        date('Y-m-d H:i:s'),
                        $recipients,
                        $templateParams
                    );
                }
                if ($this->generate_documents_office_print_bulk) {

                }
            }

            if (file_exists(@$document->generated_documents['url_pdf'])) {
                unlink(@$document->generated_documents['url_pdf']);
            }
            if (file_exists(@$document->generated_documents['url_docx'])) {
                unlink(@$document->generated_documents['url_docx']);
            }
        } catch (Exception $exc) {
            return array('exception' => $exc->getMessage() . ':' . $exc->getTraceAsString());
        }

        return array(
            'messageCreated' => $messageCreated ? Model_Messaging::getMessageIdFromNotification($messageCreated) : false,
            'noprint' => $noprint,
            'files' => $generatedFiles
        );
        return $data;
    }
}
?>
