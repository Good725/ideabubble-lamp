<?php
class Model_Errorlog
{
    const TABLE = 'engine_errorlog';

    public static function save($exc = null, $type = null)
    {

        $data = array();
        $data['get'] = serialize(ibhelpers::clear_cc($_GET));
        $data['post'] = serialize(ibhelpers::clear_cc($_POST));
        $data['cookie'] = serialize(ibhelpers::clear_cc($_COOKIE));
        $data['env'] = serialize(ibhelpers::clear_cc($_ENV));
        $data['server'] = serialize(ibhelpers::clear_cc($_SERVER));
        $data['dt'] = date::now();
        $data['ip'] = @$_SERVER['REMOTE_ADDR'];
        $data['browser'] = @$_SERVER['HTTP_USER_AGENT'];
        $data['session'] = serialize(@$_SESSION);
        $data['host'] = @$_SERVER['HTTP_HOST'];
        $data['url'] = @$_SERVER['REQUEST_URI'];
        $data['referer'] = @$_SERVER['HTTP_REFERER'];
        $data['trace'] = ($exc != null ? $exc->getTrace() : debug_backtrace());
        //header('content-type: text/plain');print_r(array_keys($data['trace']));exit;
        if ($data['trace']) {
            foreach ($data['trace'] as $trace) {
                if (isset($trace['file'])) {
                    if (stripos($trace['file'], '/kohana/') !== false) {
                        continue;
                    }
                    $data['file'] = @$trace['file'];
                    $data['line'] = @$trace['line'];
                    break;
                }

            }
        }
        $data['trace'] = ibhelpers::clear_cc(print_r($data['trace'], 1));
        if ($exc) {
            $data['details'] = $exc->getMessage();

            if (is_a($exc, 'Database_Exception')) {
                $data['type'] = 'SQL';
            } else if (stripos(get_class($exc), 'HTTP_Exception') !== false) {
                $data['type'] = 'HTTP';
                $data['details'] = str_replace('HTTP_Exception_', '', get_class($exc));
            } else {
                $data['type'] = 'PHP';
            }
        } else {
            $data['type'] = 'HTTP';
        }

        if ($type) {
            $data['type'] = $type;
        }

        if (isset($data['details'])) {
            $data['details'] = ibhelpers::clear_cc($data['details']);
        }

        if ($data['type'] == 'HTTP') {
            $data['file'] = null;
            $data['line'] = null;
        }

        try {
            if (class_exists('DB')) {
                $inserted = DB::insert(self::TABLE)->values($data)->execute();
                return @$inserted[0];
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function delete_old($type = null, $before = null)
    {
        $q = DB::delete(self::TABLE);
        if ($type) {
            $q->where('type', '=', $type);
        }
        if ($before) {
            $q->where('dt', '<', $before);
        }
        $q->execute();
    }
}