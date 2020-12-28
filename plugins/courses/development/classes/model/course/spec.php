<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Spec extends ORM
{
    protected $_table_name = 'plugin_courses_specs';
    protected $_publish_column = 'published';

    protected $_belongs_to = [
        'grading_schema'   => ['model' => 'Todo_GradingSchema', 'foreign_key' => 'grading_schema_id'],
        'qqi_component'    => ['model' => 'Lookup',             'foreign_key' => 'qqi_component_id'],
        'requirement_type' => ['model' => 'Lookup',             'foreign_key' => 'requirement_type_id'],
        'subject'          => ['model' => 'Course_Subject',     'foreign_key' => 'subject_id'],
    ];

    protected $_has_many = [
        // one-to-many
        'credits'  => ['model' => 'course_credit',        'foreign_key' => 'spec_id'],
        'marks'    => ['model' => 'course_spec_mark',     'foreign_key' => 'spec_id'],
        'material' => ['model' => 'course_spec_material', 'foreign_key' => 'spec_id'],

        // many-to-many
        'curriculums'            => ['model' => 'course_curriculum', 'far_key' => 'curriculum_id',             'foreign_key' => 'spec_id', 'through' => 'plugin_courses_curriculums_have_specs'],
        'learning_methodologies' => ['model' => 'Lookup',            'far_key' => 'learning_methodology_id',   'foreign_key' => 'spec_id', 'through' => 'plugin_courses_specs_have_learning_methodologies'],
    ];

    // Extension of the standard save_with_moddate to also save other data
    public function save_relationships($data = null)
    {
        $db = Database::instance();
        $db->commit();

        try {
            $this->values($data);

            // If a new QQI component is being created, save it and set its ID as the QQI Component for this spec
            if ($this->qqi_component_id == '' && !empty($data['qqi_component_new'])) {
                $lookup_field     = ORM::factory('Lookup_Field')->where('name', '=', 'QQI component')->find();
                $lookup           = new Model_Lookup();
                $lookup->label    = $data['qqi_component_new'];
                $lookup->field_id = $lookup_field->id;
                $lookup->save();

                $this->qqi_component_id = $lookup->id;
            }

            // Save the spec
            $this->save_with_moddate();

            // Save its learning-methodology relationships
            DB::delete('plugin_courses_specs_have_learning_methodologies')->where('spec_id', '=', $this->id)->execute();

            $lookup_field     = ORM::factory('Lookup_Field')->where('name', '=', 'Learning methodology')->find();
            if (!empty($data['learning_methodologies'])) {
                foreach ($data['learning_methodologies'] as $methodology) {
                    if (empty($methodology['id']) && isset($methodology['label'])) {
                        $new_methodology = new Model_Lookup();
                        $new_methodology->label = $methodology['label'];
                        $new_methodology->field_id = $lookup_field->id;
                        $new_methodology->save_with_moddate();
                        $methodology['id'] = $new_methodology->id;
                    }

                    DB::insert('plugin_courses_specs_have_learning_methodologies', ['spec_id', 'learning_methodology_id', 'order'])
                        ->values([$this->id, $methodology['id'], $methodology['order']])->execute();
                }
            }

            // Save reading-material relationships
            DB::delete('plugin_courses_specs_have_recommended_material')->where('spec_id', '=', $this->id)->execute();

            if (!empty($data['recommended_material'])) {
                foreach ($data['recommended_material'] as $material) {
                    $url = '';
                    // If the user did not select an existing product from the dropdown...
                    if (empty($material['id'])) {
                        // If the title is a URL, set a URL as the material, rather than a product
                        $url = (valid::url($material['title'])) ? $material['title'] : $url;
                        $url = (!$url && valid::url('http://'.$material['title'])) ? 'http://'.$material['title'] : $url;

                        // If something other than an existing product or URL is selected, save it as a new product
                        if (!$url) {
                            $product = new Model_Product_Product();
                            $product->title = $material['title'];
                            $product->url_title = Model_Pages::filter_name_tag($material['title']);
                            $product->save_with_moddate();
                            $material['id'] = $product->id;
                        }
                    }

                    DB::insert('plugin_courses_specs_have_recommended_material', ['spec_id', 'product_id', 'url', 'order'])
                        ->values([$this->id, $material['id'], $url, $material['order']])->execute();
                }
            }

            // Save allocated time relationships
            DB::delete('plugin_courses_credits')->where('spec_id', '=', $this->id)->execute();

            if (!empty($data['time_allocations'])) {
                $study_modes = ORM::factory('Course_StudyMode')->order_by('study_mode')->find_all_undeleted();

                foreach ($data['time_allocations'] as $time_allocation) {
                    foreach ($study_modes as $study_mode) {
                        if (isset($time_allocation['hours_'.$study_mode->id])) {
                            $credit = new Model_Course_Credit();
                            $credit->spec_id       = $this->id;
                            $credit->subject_id    = $this->subject_id;
                            $credit->study_mode_id = $study_mode->id;
                            $credit->type          = $time_allocation['type'];
                            $credit->hours         = $time_allocation['hours_'.$study_mode->id];
                            $credit->save_with_moddate();
                        }
                    }
                }
            }

            // Save allocated marks
            DB::delete('plugin_courses_spec_marks')->where('spec_id', '=', $this->id)->execute();

            if (!empty($data['mark_allocations'])) {
                foreach ($data['mark_allocations'] as $mark_allocation) {
                    $mark = new Model_Course_Spec_Mark();
                    $mark->spec_id = $this->id;
                    $mark->type    = $mark_allocation['type'];
                    $mark->mark    = $mark_allocation['mark'];
                    $mark->save();
                }
            }

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    // Could use $spec->learning_methodologies->find_all(), but that wouldn't get the "order" column
    public function get_learning_methodologies()
    {
        $learning_methodologies = [];
        $has_learning_methodologies = DB::select()->from('plugin_courses_specs_have_learning_methodologies')->where('spec_id', '=', $this->id)->order_by('order')->execute()->as_array();

        foreach ($has_learning_methodologies as $hlm) {
            $lm = new Model_Lookup($hlm['learning_methodology_id']);
            if ($lm->id) {
                $learning_methodologies[$hlm['order']] = $lm;
            }
        }

        return $learning_methodologies;
    }

    public function get_recommended_material()
    {
        $material = $this->material->order_by('order')->find_all();
        $return = [];

        foreach ($material as $item) {
            $product = $item->product->deleted ? new Model_Product_Product() : $item->product;
            $title   = $item->url ? $item->url : $product->title;

            $return[] = [
                'id' => $product->id,
                'order' => $item->order,
                'title' => $title,

                // For document generation
                'recommended_material_number' => $item->order,
                'recommended_material' => $title
            ];
        }

        return $return;
    }

    public function get_total_credit_hours()
    {
        $total = 0;
        $credits = $this->id ? $this->credits->find_all_undeleted() : [];
        foreach ($credits as $credit) {
            $total += (float) $credit->hours;
        }
        return $total;
    }

    public function get_total_marks($type = null)
    {
        if (!$this->id) {
            return 0;
        }

        $total = 0;
        $marks = ($type) ? $this->marks->where('type', '=', $type)->find_all() : $this->marks->find_all();

        foreach ($marks as $mark) {
            $total += (int) $mark->mark;
        }
        return $total;
    }

    public function get_credits_by_type($args = [])
    {
        // Empty spec has no credit distribution yet
        if (!$this->id) {
            return [];
        }

        $credit_types = ORM::factory('Course_Credit')->get_enum_options('type');
        $return = [];
        $totals = [];

        // Loop through each credit type, get all credits of that type for this spec
        foreach ($credit_types as $credit_type) {
            $credits = $this->credits->where('type', '=', $credit_type)->find_all_undeleted()->as_array();

            if (count($credits)) {
                $return_item = [];

                // Loop through each credit and form a single associative array
                // Some values for should be the same for each (course_id, spec_id, etc.)
                // "hours" and "credits" will vary between credit types. Create separate array keys for each one.
                foreach ($credits as $key => $credit) {
                    if (empty($args['doc_gen'])) {
                        $return_item['course_id']  = $credit->course_id;
                        $return_item['subject_id'] = $credit->subject_id;
                        $return_item['spec_id']    = $credit->spec_id;
                        $return_item['type'] = $credit->type;
                        $return_item['hours_'.$credit->study_mode_id]  = $credit->hours;
                        $return_item['credit_'.$credit->study_mode_id] = $credit->credit;
                    } else {
                        // For document generation
                        $study_mode_stub = preg_replace('/\W+/','',strtolower($credit->study_mode->study_mode));
                        $return_item[$study_mode_stub.'_label'] = $credit->type;
                        $return_item[$study_mode_stub.'_hours'] = $credit->hours.'h';

                        $totals[$study_mode_stub] = isset($totals[$study_mode_stub]) ? $totals[$study_mode_stub] + $credit->hours : $credit->hours;
                    }
                }

                $return[] = $return_item;
            }
        }

        if (!empty($args['doc_gen'])) {
            $return_totals = [];
            foreach ($totals as $key => $value) {
                $return_totals[$key.'_label'] = 'Total made up of';
                $return_totals[$key.'_hours'] = $value.'h';
            }

            $return = array_merge([$return_totals], $return);
        }

        return $return;
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'id',
            'title',
            'published',
            'date_modified',
            null // actions
        ];

        $results = $this->apply_datatable_args($datatable_args, $column_definitions)->where_undeleted();
        $q = clone $results;
        $results = $results->find_all_undeleted();

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->id;
            $row[] = htmlentities($result->title);
            $row[] = IbHelpers::relative_time_with_tooltip($result->date_modified);
            $row[] = View::factory('snippets/iblisting/publish_toggle')->set([
                'published' => $result->published,
                'id_prefix' => 'course-specs',
                'id' => $result->id
            ])->render();
            $row[] =  View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', [
                    ['type' => 'link',   'icon' => 'pencil', 'title' => 'Edit',   'attributes' => ['class' => 'edit_link edit-link', 'href' => '/admin/courses/edit_spec/'.$result->id]],
                    ['type' => 'button', 'icon' => 'close',  'title' => 'Delete', 'attributes' => ['class' => 'course-specs-table-delete', 'data-id' => $result->id, 'data-toggle="modal" data-target="#course-specs-table-delete-modal"']]
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
}