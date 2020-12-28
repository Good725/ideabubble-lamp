<?php defined('SYSPATH') or die('No direct script access.');

class Model_Logistics_Transfer extends ORM
{
    protected $_table_name = 'plugin_logistics_transfers';

    protected $_belongs_to = [
        'passenger' => ['model' => 'contacts3_contact'],
        'driver'    => ['model' => 'contacts3_contact'],
        'pickup'    => ['model' => 'location'],
        'dropoff'   => ['model' => 'location']
    ];

    public function get_note($column = null)
    {
        if ($this->id) {
            $notes = Model_Notes::search(['type' => 'Logistic transfer', 'reference_id' => $this->id]);
        }

        $note = (!empty($notes)) ? $notes[0] : ['id' => null, 'note' => null];

        if ($column) {
            return isset($note[$column]) ? $note[$column] : null;
        } else {
            return $note;
        }
    }

    public function find_all_filtered($filters = [], $args)
    {
        $this->select([DB::expr("CONCAT(`passenger`.`first_name`, ' ', `passenger`.`last_name`)"), 'passenger_name']);
        $this->select([DB::expr("CONCAT(`driver`.`first_name`, ' ', `driver`.`last_name`)"), 'driver_name']);
        $this->select(['pickup.title',  'pickup_name']);
        $this->select(['dropoff.title', 'dropoff_name']);

        if (!empty($filters['keyword'])) {
            $this->where_open();
                $this->where('logistics_transfer.id',    'like', '%'.$filters['keyword'].'%');
                $this->where('logistics_transfer.title', 'like', '%'.$filters['keyword'].'%');
                $this->where('logistics_transfer.type',  'like', '%'.$filters['keyword'].'%');
                $this->where(DB::expr("CONCAT(`passenger`.`first_name`, ' ', `passenger`.`last_name`)"), 'like', '%'.$filters['keyword'].'%');
                $this->where(DB::expr("CONCAT(`driver`.`first_name`, ' ', `driver`.`last_name`)"), 'like', '%'.$filters['keyword'].'%');
            $this->where_close();
        }

        $this->where('logistics_transfer.deleted', '=', 0);

        if (!empty($filters['start_date'])) {
            $this->where('logistics_transfer.scheduled_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->where('logistics_transfer.scheduled_date', '<=', $filters['end_date']);
        }

        $this->join(['plugin_contacts3_contacts', 'passenger'], 'left')->on('logistics_transfer.passenger_id', '=', 'passenger.id');
        $this->join(['plugin_contacts3_contacts',    'driver'], 'left')->on('logistics_transfer.driver_id',    '=', 'driver.id');
        $this->join(['plugin_locations_location',    'pickup'], 'left')->on('logistics_transfer.pickup_id',    '=', 'pickup.id');
        $this->join(['plugin_locations_location',   'dropoff'], 'left')->on('logistics_transfer.dropoff_id',   '=', 'dropoff.id');

        if (!empty($args['datatable_args'])) {
            $this->apply_datatable_args($args['datatable_args'], $args['column_definitions']);
        }

        // Count all records, ignoring limit and offset
        $this->reset(false);
        $count_all = $this->count_all();

        $return = $this->find_all();
        $return->_count_all = $count_all;

        return $return;
    }

    public static function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'logistics_transfer.id',
            'logistics_transfer.title',
            'logistics_transfer.type',
            DB::expr("CONCAT(`passenger`.`first_name`, ' ', `passenger`.`last_name`)"),
            DB::expr("CONCAT(`driver`.`first_name`, ' ', `driver`.`last_name`)"),
            'pickup.title',
            'dropoff.title',
            'logistics_transfer.scheduled_date',
            'logistics_transfer.date_modified',
            ''
        ];

        $model   = new Model_Logistics_Transfer();
        $results = $model->order_by('logistics_transfer.date_modified', 'desc')->find_all_filtered($filters, ['datatable_args' => $datatable_args, 'column_definitions' => $column_definitions]);

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlentities($result->title);
            $row[] = htmlentities($result->type);
            $row[] = htmlentities($result->passenger->get_full_name());
            $row[] = htmlentities($result->driver->get_full_name());
            $row[] = htmlentities($result->pickup->title);
            $row[] = htmlentities($result->dropoff->title);
            $row[] = '<span title="'.date('Y-m-d H:i', strtotime($result->scheduled_date)).'">'.htmlentities(date('H:i D j M', strtotime($result->scheduled_date))).'</span>';
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] = '<div class="action-btn">
                    <a class="btn" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                    </a>
                    <ul class="dropdown-menu">
                       <li><button type="button" class="edit-link transfer-modal-toggle" data-id="'.$result->id.'">'. __('Edit') . '</button></li>
                    </ul>
                </div>';

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $results->_count_all,
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }
}