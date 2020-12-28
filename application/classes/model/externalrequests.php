<?php
class Model_ExternalRequests extends Model
{
    const EXTERNAL_REQUESTS_TABLE = 'engine_external_requests';
    public static function create($host, $url, $data, $response, $httpStatus, $requested = null, $requestedBy = null, $duration = null)
    {
        if ($requested == null) {
            $requested = date('Y-m-d H:i:s');
            $loggedInUser = Auth::instance()->get_user();
            if ($loggedInUser) {
                $requestedBy = $loggedInUser['id'];
            }
        }
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }
        $result = DB::insert(
            self::EXTERNAL_REQUESTS_TABLE,
            array('host', 'url', 'data', 'response', 'http_status', 'requested', 'requested_by', 'duration')
        )
            ->values(array($host, $url, $data, $response, $httpStatus, $requested, $requestedBy, $duration))
            ->execute();
        return $result[0];
    }
}