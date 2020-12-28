<?php
class Model_ExternalRequest extends ORM
{
    protected $_table_name = 'engine_external_requests';

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'id',
            'requested',
            [DB::expr("'request'"), 'type'],
            'url',
            'http_status',
            'data',
            'response',
            'duration'
        ];

        $results = $this->order_by('requested', 'desc');

        if (!empty($filters['start_date'])) {
            $results->where('requested', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $results->where('requested', '<=', $filters['end_date'].' 23:59:59');
        }

        $q = clone $results;
        $results = $results->apply_datatable_args($datatable_args, $column_definitions)->find_all();

        // Count unpaginated
        $datatable_args['unlimited'] = true;
        unset($datatable_args['iDisplayLength']);
        $q->apply_datatable_args($datatable_args, $column_definitions);


        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = $result->requested ? date('H:i l j F Y', strtotime($result->requested)) : '';
            $row[] = 'request';
            $row[] = '<a href="'.$result->url.'" target="_blank">'.$result->url.'</a>';
            $row[] = htmlentities($result->http_status);
            $row[] = htmlentities($result->data);
            $row[] = htmlentities($result->response);
            $row[] = $result->duration ? $result->duration.'s' : '';

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $q->count_all(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }
}