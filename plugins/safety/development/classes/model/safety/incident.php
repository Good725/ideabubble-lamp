<?php defined('SYSPATH') or die('No direct script access.');

class Model_Safety_Incident extends ORM
{
    protected $_table_name = 'plugin_safety_incidents';

    protected $_belongs_to = [
        'location' => ['model' => 'Course_Location',   'foreign_key' => 'location_id'],
        'reporter' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'reporter_id'],
    ];

    public function get_injured_people()
    {
        // JSON encoded value is MVP. We may record these in a different table later.
        return json_decode($this->injured_people ? $this->injured_people : '[]');
    }

    public function get_witnesses()
    {
        // JSON encoded value is MVP. We may record these in a different table later.
        return json_decode($this->witnesses ? $this->witnesses : '[]');
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'safety_incident.id',
            'safety_incident.title',
            'safety_incident.datetime',
            'location.name',
            [DB::expr("CONCAT(`reporter`.`first_name`, ' ', `reporter`.`last_name`)"), 'reporter'],
            'safety_incident.severity',
            'safety_incident.status',
            'safety_incident.date_modified',
            null // actions
        ];

        $results = $this
            ->join(['plugin_contacts3_contacts', 'reporter'], 'left')->on('safety_incident.reporter_id', '=', 'reporter.id')
            ->join(['plugin_courses_locations',  'location'], 'left')->on('safety_incident.location_id', '=', 'location.id')
            ->apply_datatable_args($datatable_args, $column_definitions)
            ->where_undeleted();

        if (!empty($filters['severities'])) {
            $results->where('safety_incident.severity', 'in', $filters['severities']);
        }

        if (!empty($filters['statuses'])) {
            $results->where('safety_incident.status', 'in', $filters['statuses']);
        }

        if (!empty($filters['start_date'])) {
            $results->where('safety_incident.datetime', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $results->where('safety_incident.datetime', '<=', $filters['end_date'].' 23:59:59');
        }

        if (!empty($filters['show_mine_only'])) {
            $user_contact = Auth::instance()->get_contact();
            $results->where('reporter.id', '=', $user_contact->id);
        }

        $results->order_by('safety_incident.date_modified', 'desc');
        $q = clone $results;
        $results = $results->find_all();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlentities($result->title);
            $row[] = $result->datetime ? date('D H:i, d M Y', strtotime($result->datetime)) : '';
            $row[] = htmlentities($result->location->name);
            $row[] = htmlentities($result->reporter->get_full_name());
            $row[] = htmlentities($result->severity);
            $row[] = htmlentities($result->status);

            // List of other columns
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', [
                    ['type' => 'button', 'title' => 'Edit',   'attributes' => ['data-toggle' => 'modal', 'data-target' => '#incidents-report-modal',       'data-id' => $result->id, 'class' => 'edit-link']],
                    ['type' => 'button', 'title' => 'Delete', 'attributes' => ['data-toggle' => 'modal', 'data-target' => '#incidents-table-delete-modal', 'data-id' => $result->id]]
                ])->render();

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $q->count_all(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public function get_reports($filters = [])
    {
        $severities = self::get_enum_options('severity');

        $results = $this
            ->join(['plugin_contacts3_contacts', 'reporter'], 'left')->on('safety_incident.reporter_id', '=', 'reporter.id')
            ->join(['plugin_courses_locations',  'location'], 'left')->on('safety_incident.location_id', '=', 'location.id');

        if (!empty($filters['severities'])) {
            $results->where('safety_incident.severity', 'in', $filters['severities']);
        }

        if (!empty($filters['statuses'])) {
            $results->where('safety_incident.status', 'in', $filters['statuses']);
        }

        // Default range is the current year
        if (!empty($filters['start_date'])) {
            $results->where('safety_incident.datetime', '>=', $filters['start_date'].' 00:00:00');
        } else {
            $results->where('safety_incident.datetime', '>=', date('Y-01-01 00:00:00'));
        }

        if (!empty($filters['end_date'])) {
            $results->where('safety_incident.datetime', '<=', $filters['end_date'].' 23:59:59');
        } else {
            $results->where('safety_incident.datetime', '<=', date('Y-12-31 23:59:59'));
        }

        $clone = clone $results;

        $reports = [['text' => 'Reports', 'amount' => $clone->find_all_undeleted()->count()]];
        foreach ($severities as $severity) {
            $clone = clone $results;
            $reports[] = ['text' => $severity, 'amount' => $clone->where('severity', '=', $severity)->find_all_undeleted()->count()];
        }

        return $reports;
    }

    public function save_data($data)
    {
        $this->values($data);

        if (isset($data['time']) && isset($data['time'])) {
            $this->set('datetime', $data['date'].' '.$data['time']);
        }
        if (isset($data['injured_people'])) {
            $this->set('injured_people', json_encode((array) $data['injured_people']));
        }
        if (isset($data['witnesses'])) {
            $this->set('witnesses', json_encode((array) $data['witnesses']));
        }

        $this->save_with_moddate();
    }

    public function send_notifications($types = [])
    {
        $messaging = new Model_Messaging();
        $params = [
            'title'      => $this->title,
            'first_name' => $this->reporter->first_name,
            'last_name'  => $this->reporter->last_name,
            'email'      => $this->reporter->get_notification('email'),
            'mobile'     => $this->reporter->get_notification('mobile'),
        ];

        if (in_array('admin', $types)) {
            $messaging->send_template('incident_reported_email_admin', null, null, [], $params);
            $messaging->send_template('incident_reported_alert_admin', null, null, [], $params);
        }

        if (in_array('reporter', $types)) {
            $extra_recipients = [['target_type' => 'CMS_CONTACT', 'target' => $this->reporter->id]];
            $messaging->send_template('incident_reported_email_user', null, null, $extra_recipients, $params);
        }
    }
}