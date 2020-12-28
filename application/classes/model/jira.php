<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 22/09/2014
 * Time: 09:13
 */

class Model_JIRA extends Model
{

    /*** STATIC VARIABLES ***/
    private static $username    = '';
    private static $password    = '';
    private static $projectid   = '';
    private static $url         = '';

    /*** CONSTANTS ***/
    const API       = '/rest/api/2/';
    const API_ISSUE = 'issue/';
    const AGILE_API = '/rest/agile/1.0/'; 

    /*** PRIVATE MEMBER DATA ***/
    private $login      = '';
    private $enabled    = false;

    public function __construct()
    {
        self::$username     = Settings::instance()->get('jira_username');
        self::$password     = Settings::instance()->get('jira_password');
        self::$projectid    = Settings::instance()->get('jira_project_id');
        self::$url          = rtrim(Settings::instance()->get('jira_url'), '/');
        $this->enabled      = Settings::instance()->get('jira_dashboard_show');
        $this->login        = self::$username . ':' . self::$password;

        if ($this->enabled == "TRUE") {
            if (!@$GLOBALS['JIRA_ERROR_MSG']) {
                $GLOBALS['JIRA_ERROR_MSG'] = true;
                if (self::$username == '' || self::$password == '' || self::$url == '') {
                    IbHelpers::set_message("Jira connection parameters not set. Check Settings", 'warning');
                }
            }
        }
    }

    public function jira_enabled()
    {
        return $this->enabled == "TRUE" && self::$username != '' && self::$password != '' && self::$url != '' ? true : false;
    }

    public function create_issue($project = 'WPPROD',$summary = '',$description = '',$type = 'Bug')
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            return false;
        }
        $ch         = curl_init();
        $data = array(
            'fields' => array(
                'project' => array('key' => $project),
                'summary' => $summary,
                'description' => $description,
                'components' => array(array('id' => '12180')),
                'duedate' => date('Y-m-d', strtotime("+1 Month")),
                'issuetype' => array('name' => $type)
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_URL, self::$url.self::API.self::API_ISSUE.'');
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        $result = curl_exec($ch);
        return json_decode($result, true) == null ? false : true;
    }

    public function get_projects()
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $furl = self::$url . self::API . 'project';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $furl);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200') {
            $result = json_decode($response, true);
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $furl . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        curl_close($ch);
        return $result;
    }

    public function get_sprint_project_name($sprint_id){
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $url = self::$url . self::API . "search?jql=sprint={$sprint_id}&maxResults=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200') {
            $result = json_decode($response, true);
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $url . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        curl_close($ch);
        return $result['issues'];
    }
    public function get_sprints(){
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $furl = "https://ideabubble.atlassian.net/rest/agile/1.0/board/53/sprint?state=ACTIVE,FUTURE";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $furl);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200') {
            $result = json_decode($response, true);
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $furl . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        curl_close($ch);
        return $result;
    }

    public function get_sprint($jira_sprint_id)
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $furl = self::$url . self::AGILE_API . "sprint/{$jira_sprint_id}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $furl);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200' || $inf['http_code'] == '404') {
            $result = json_decode($response, true);
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $furl . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        curl_close($ch);
        return $result;
    }
    public function get_sprint_issues($sprint_id, $starting_point = 0)
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $url = self::$url . self::API . "search?jql=sprint={$sprint_id}&startAt={$starting_point}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200') {
            $result = json_decode($response, true);
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $url . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        curl_close($ch);
        if(count($result['issues']) == 49) {
            $starting_point = $starting_point + 49;
            $result['issues'] = array_merge($result['issues'], $this->get_sprint_issues($sprint_id, $starting_point));
        }
        return $result['issues'];
    }

    public function active_sprint_checker($sprint_id){
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $url = self::$url . "/rest/agile/1.0/sprint/{$sprint_id}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200') {
            $result = json_decode($response, true);
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $url . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        if($result['state'] !== "active" || $result['state'] !== "future"){
            return true;
        }
        else{
            return false;
        }
    }
    public function get_time_spent_by_issues($sprint_issues){
        $total_time_spent = 0;
        foreach($sprint_issues as $sprint_issue){
            $total_time_spent += $sprint_issue['fields']['timespent'];
        }
        $total_time_spent = floor($total_time_spent / 3600);
        return $total_time_spent;
    }
    public function get_rapidviews($frapidview_id = null, $fsprint_id = null)
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $furl = self::$url . '/rest/greenhopper/1.0/rapidview';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $furl);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        if ($inf['http_code'] == '200') {
            $rapid_views = json_decode($response, true);
            $rapid_views = $rapid_views['views'];
        } else {
            throw new Exception(
                "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                "Curl Error: " . curl_errno($ch) .
                "Url:" . $furl . "\n" .
                "Response:" . $response . "\n" .
                print_r($inf, true)
            );
        }
        $sprints_cache = array();

        foreach ($rapid_views as $ri => $rapid_view) {
            if ($frapidview_id != null && $frapidview_id != $rapid_view['id']) {
                continue;
            }
            DB::select(DB::expr('1'))->execute()->as_array(); // prevent mysql from closing connection due to timeout(default 30 seconds)

            $furl = self::$url . '/rest/greenhopper/1.0/sprintquery/' . $rapid_view['id'];
            curl_setopt($ch, CURLOPT_URL, $furl);
            $response = curl_exec($ch);
            if ($inf['http_code'] == '200') {
                $sprints1 = json_decode($response, true);//header('content-type: text/plain');print_r(func_get_args());print_r($sprints1);exit;
            } else {
                throw new Exception(
                    "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                    "Url:" . $furl . "\n" .
                    "Response:" . $response . "\n" .
                    print_r($inf, true)
                );
            }
            $rapid_views[$ri]['sprints'] = $sprints1['sprints'];
            foreach ($rapid_views[$ri]['sprints'] as $si => $sprint) {
                if ($fsprint_id != null && $fsprint_id != $sprint['id']) {
                    continue;
                }

                if (!isset($sprints_cache[$sprint['id']])) {
                    $furl = self::$url . '/rest/greenhopper/1.0/rapid/charts/sprintreport?rapidViewId=' . $rapid_view['id'] . '&sprintId=' . $sprint['id'];
                    curl_setopt($ch, CURLOPT_URL, $furl);
                    $response = curl_exec($ch);
                    if ($inf['http_code'] == '200') {
                        $issues1 = json_decode($response, true);
                        $sprints_cache[$sprint['id']] = $issues1;
                    } else {
                        throw new Exception(
                            "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                            "Url:" . $furl . "\n" .
                            "Response:" . $response . "\n" .
                            print_r($inf, true)
                        );
                    }
                } else {
                    $issues1 = $sprints_cache[$sprint['id']];
                }
                $rapid_views[$ri]['sprints'][$si]['issues'] = array();
                $keys = array(
                    'completedIssues',
                    'incompletedIssues',
                    'issuesNotCompletedInCurrentSprint',
                    'puntedIssues',
                    'issuesCompletedInAnotherSprint'
                );
                foreach ($keys as $key) {
                    if (isset($issues1['contents'][$key])) {
                        foreach ($issues1['contents'][$key] as $issue) {
                            $rapid_views[$ri]['sprints'][$si]['issues'][] = $issue['key'];
                        }
                    }
                }
            }
        }
        curl_close($ch);
        return $rapid_views;
    }

    public function get_issues($project_id = null)
    {
        try {
            if (self::$username == '' || self::$password == '' || self::$url == '') {
                throw new Exception("Jira connection parameters not set. Check Settings");
            }
            if ($project_id === null) {
                $project_id = self::$projectid;
            }
            $url = self::$url . self::API . 'search?jql=' . ($project_id ? 'project=' . $project_id : '') . '+order+by+duedate+desc';
            $fields = '&fields=id,key,issuetype,timespent,status,description,summary,updated,worklog,timeoriginalestimate,duedate,resolution';
            if (!$project_id) {
                $fields .= ',project';
            }
            $url .= $fields;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_USERPWD, $this->login);
            $furl = $url;
            $received = 0;
            $start_at = 0;
            $max_results = 1000;
            $issues = array();
            do {
                DB::select(DB::expr('1'))->execute()->as_array(); // prevent mysql from closing connection due to timeout(default 30 seconds)

                $furl = $url;
                if ($start_at !== null) {
                    $furl .= '&startAt=' . $start_at;
                }
                if ($max_results !== null) {
                    $furl .= '&maxResults=' . $max_results;
                }

                curl_setopt($ch, CURLOPT_URL, $furl);
                $response = curl_exec($ch);
                $inf = curl_getinfo($ch);
                if ($inf['http_code'] == '200') {
                    $result = json_decode($response, true);
                    if (!isset($result['total'])) {
                        break;
                    }
                } else {
                    // Display error messages

                    $response_decoded = json_decode($response);

                    if ($response_decoded->errorMessages) {
                        foreach ($response_decoded->errorMessages as $error_message) {
                            IbHelpers::set_message('JIRA server issue. Check your settings. ' . $error_message,
                                'error');
                        }
                        return false;
                    } else {
                        $result = array();
                        $result['total'] = 0;
                        $result['startAt'] = 0;
                        $result['issues'] = array();
                        $result['error'] = "Jira Server Returned HTTP Status: " . $inf['http_code'];
                        return $result;
                        /*throw new Exception(
                            "Jira Server Returned HTTP Status: " . $inf['http_code'] . "\n" .
                                "Curl Error: " . curl_errno($ch) .
                                "Url:" . $furl . "\n" .
                                "Response:" . $response . "\n" .
                                print_r($inf, true)
                        );*/
                    }
                }
                $total = $result['total'];
                $received += count($result['issues']);
                $start_at = $result['startAt'] + count($result['issues']);
                $max_results = $result['maxResults'];
                $issues = array_merge($issues, $result['issues']);
            } while ($received < $total);
            curl_close($ch);
            $result['startAt'] = 0;
            $result['issues'] = $issues;
        } catch (Exception $exc) {
            $result = array();
            $result['total'] = 0;
            $result['startAt'] = 0;
            $result['issues'] = array();
            $result['error'] = "Unexpected jira error: " . $exc->getMessage();
        }
        return $result;
    }

    public static function create()
    {
        return new self();
    }

    public static function make_description($file, $line, $trace)
    {
        $result =  'File: '.$file.' \\n Line: '.$line.' \\n';
        $result.='\\n'.$trace;

        return $result;
    }

    public function add_worklog($task, $started, $timespent, $comment)
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $furl = self::$url . self::API . 'issue/' . $task . '/worklog';
        $started = new DateTime($started);
        $data = array(
            "id" => null,
            "self" => null,
            'author' => null,
            "updated" => null,
            "timeSpent" => $timespent,
            "comment" => $comment,
            "started" => $started->format("Y-m-d\TH:i:s").'.000'.$started->format('O'),
            "timeSpentSeconds" => null,
            /*'visibility' => array(
                'type' => 'group',
                'value' => 'idea-bubble-5'
            )*/
        );
        //print_r($data);
        //echo "\n". json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $furl);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        $result = json_decode($response, true);
        if (!$result) {
            $result = array(
                'error' => $inf,
                'e' => curl_error($ch)
            );
        }
        curl_close($ch);
        return $result;
    }


    public function delete_worklog($task, $worklog)
    {
        if (self::$username == '' || self::$password == '' || self::$url == '') {
            throw new Exception("Jira connection parameters not set. Check Settings");
        }
        $furl = self::$url . self::API . 'issue/' . $task . '/worklog/' . $worklog;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login);
        curl_setopt($ch, CURLOPT_URL, $furl);
        $response = curl_exec($ch);
        $inf = curl_getinfo($ch);
        $result = json_decode($response, true);
        curl_close($ch);
        return $result;
    }
}

?>