<?php
class Model_Errorlogs extends ORM
{
    protected $_table_name = 'engine_errorlog';

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'id',
            'type',
            'file',
            'line',
            'host',
            'url',
            'referer',
            'dt',
            'ip',
            'browser',
            'details'
        ];

        $results = $this->order_by('dt', 'desc');

        if (!empty($filters['start_date'])) {
            $results->where('dt', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $results->where('dt', '<=', $filters['end_date'].' 23:59:59');
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
            $row[] = $result->type;
            $row[] = $result->file;
            $row[] = $result->line;
            $row[] = $result->host;
            $row[] = htmlentities($result->url);
            $row[] = htmlentities($result->referer);
            $row[] = $result->dt ? date('H:i l j F Y', strtotime($result->dt)) : '';
            $row[] = $result->ip;
            $row[] = html::entities($result->browser);
            $row[] = $result->details;
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