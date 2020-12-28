<?php

class Controller_Admin_SafetySeeder extends Controller_Cms
{

    public function action_prechecks()
    {
        switch ($this->request->param('id')) {
            case 'rollback':
                self::unseed_prechecks();
                IbHelpers::set_message('Surveys deleted.', 'success popup_box');
                $this->request->redirect('/admin/surveys');
                break;

            default:
                self::seed_prechecks();
                IbHelpers::set_message('Surveys created.', 'success popup_box');
                $this->request->redirect('/admin/surveys');
                break;
        }

    }

    function seed_prechecks()
    {
        $db = Database::instance();
        $db->begin();

        try {
            $input = ORM::factory('Answertype')->where('stub', '=', 'input')->find_published();
            $yesno = ORM::factory('Answertype')->where('stub', '=', 'yes_or_no')->find_published();

            // Create the subsurveys for farm safety
            $farm_prechecks = self::get_farm_prechecks();

            foreach ($farm_prechecks as $precheck) {
                $survey = new Model_Survey();

                $survey->title = $precheck['heading'];
                $survey->pagination = 1;
                $survey->type = 'Pre-check';
                $survey->expiry = 0;
                $survey->store_answer = 1;
                $survey->result_pdf_download = 0;

                if ($precheck['type'] == 'location') {
                    $survey->course_selector   = 1;
                    $survey->schedule_selector = 1;
                }
                $survey->save_with_moddate();

                $group_number = 0;

                foreach ($precheck['questions'] as $question_group) {
                    $group = new Model_Group();
                    $group->title = $question_group['name'];
                    $group->save_with_moddate();

                    $shg = new Model_SurveyHasGroup();
                    $shg->survey_id = $survey->id;
                    $shg->group_id  = $group->id;
                    $shg->order_id  = $group_number + 1;
                    $shg->save_with_moddate();

                    foreach ($question_group['questions'] as $question_number => $question_name) {
                        $answer = new Model_Answer();
                        $answer->title = $question_name;
                        $answer->type_id = $yesno->id;
                        $answer->save_with_moddate();

                        $question = new Model_Question();
                        $question->title = $question_name;
                        $question->answer_id = $answer->id;
                        $question->save_with_moddate();

                        $shq = new Model_SurveyHasQuestion();
                        $shq->survey_id = $survey->id;
                        $shq->group_id = $group->id;
                        $shq->question_id = $question->id;
                        $shq->order_id = $question_number + 1;
                        $shq->save_with_moddate();
                    }

                    $group_number++;
                }
            }

            // Create the farm safety precheck
            $survey = new Model_Survey();

            $survey->title = 'Farm safety';
            $survey->pagination = 1;
            $survey->type = 'Pre-check';
            $survey->expiry = 0;
            $survey->store_answer = 1;
            $survey->save_with_moddate();

            $group = new Model_Group();
            $group->title = '';
            $group->save_with_moddate();

            $shg = new Model_SurveyHasGroup();
            $shg->survey_id = $survey->id;
            $shg->group_id = $group->id;
            $shg->order_id = 1;
            $shg->save_with_moddate();

            foreach ($farm_prechecks as $question_number => $precheck) {
                $answer = new Model_Answer();
                $answer->title = $precheck['question'];
                $answer->type_id = $yesno->id;
                $answer->save_with_moddate();

                $child_survey = ORM::factory('Survey')
                    ->where('title', '=', $precheck['heading'])
                    ->order_by('id', 'desc')
                    ->find();

                $question = new Model_Question();
                $question->title = $precheck['question'];
                $question->answer_id = $answer->id;
                $question->child_survey_id = $child_survey->id;
                $question->save_with_moddate();

                $shq = new Model_SurveyHasQuestion();
                $shq->survey_id   = $survey->id;
                $shq->group_id    = $group->id;
                $shq->question_id = $question->id;
                $shq->order_id    = $question_number + 1;
                $shq->save_with_moddate();
            }

            // Create the car safety precheck
            $questions = self::get_questions();
            $question_groups = $questions['cars'];

            $survey = new Model_Survey();

            $survey->title = 'Car safety';
            $survey->pagination = 1;
            $survey->type = 'Pre-check';
            $survey->expiry = 0;
            $survey->store_answer = 1;
            $survey->save_with_moddate();


            $group_number = 0;
            foreach ($question_groups as $question_group) {
                $group = new Model_Group();
                $group->title = $question_group['name'];
                $group->save_with_moddate();

                $shg = new Model_SurveyHasGroup();
                $shg->survey_id = $survey->id;
                $shg->group_id  = $group->id;
                $shg->order_id  = $group_number + 1;
                $shg->save_with_moddate();

                foreach ($question_group['questions'] as $question_number => $question_name) {
                    $answer = new Model_Answer();
                    $answer->title   = $question_name;
                    $answer->type_id = (@$question_group['type'] == 'input') ? $input->id : $yesno->id;
                    $answer->save_with_moddate();

                    $question = new Model_Question();
                    $question->title     = $question_name;
                    $question->answer_id = $answer->id;
                    $question->save_with_moddate();

                    $shq = new Model_SurveyHasQuestion();
                    $shq->survey_id   = $survey->id;
                    $shq->group_id    = $group->id;
                    $shq->question_id = $question->id;
                    $shq->order_id    = $question_number + 1;
                    $shq->save_with_moddate();
                }

                $group_number++;
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    function unseed_prechecks()
    {
        ORM::factory('Survey')
            ->where('title', '=', 'Car safety')
            ->order_by('id', 'desc')
            ->find_undeleted()
            ->expunge_survey();

        ORM::factory('Survey')
            ->where('title', '=', 'Farm safety')
            ->order_by('id', 'desc')
            ->find_undeleted()
            ->expunge_survey();

        $farm_prechecks = self::get_farm_prechecks();
        foreach ($farm_prechecks as $precheck) {
            ORM::factory('Survey')
                ->where('title', '=', $precheck['heading'])
                ->order_by('id', 'desc')
                ->find_undeleted()
                ->expunge_survey();

        }
    }

    public static function get_farm_prechecks()
    {
        $questions = self::get_questions();

        return [
            [
                'type'      => 'people',
                'heading'   => 'Children, Young persons and Older Farmers Safety Assessment',
                'question'  => 'Will there be children, young people or older farmers involved?',
                'questions' => $questions['people'],
            ], [
                'type'      => 'vehicles',
                'heading'   => 'Tractor, Vehicles and Quad Risk Assessment',
                'question'  => 'Will you be using tractors, farm vehicles or quads?',
                'questions' => @$questions['vehicles'],
                'columns_text' => 'Select all vehicles used on the farm',
                'columns'   => [1 => 'Deer', 2 => 'Ford', 3 => 'Massey Ferguson', 4 => 'Quad'],
            ], [
                'type'      => 'machinery',
                'heading'   => 'Machinery Risk Assessment',
                'question'  => 'Will you be using any type of machinery?',
                'questions' => @$questions['machinery'],
                'columns_text' => 'Select all machinery used on the farm',
                'columns'   => [1 => 'Hedge cutter', 2 => 'Log splitter', 3 => 'Plough', 4 => 'Welder'],
            ], [
                'type'         => 'livestock',
                'heading'      => 'Livestock Risk Assessment',
                'question'     => 'Will you be dealing with livestock?',
                'questions'    => @$questions['livestock'],
                'columns_text' => 'List the types of livestock on the farm',
                'columns'      => [
                    1 => 'Bull', 2 => 'Chicken', 3 => 'Cow', 4 => 'Donkey', 5 => 'Duck', 6 => 'Goat', 7 => 'Goose',
                    8 => 'Horse', 9 => 'Ox', 10 => 'Pig', 11 => 'Ram', 12 => 'Sheep', 13 => 'Swan', 14 => 'Turkey'
                ]
            ], [
                'type'        => 'locations',
                'heading'     => 'Farmyard, Buildings',
                'question'    => 'Will you be working in the farmyard and/or working at height?',
                'questions'   => @$questions['locations'],
                'column_text' => 'Select farmyards and farm buildings',
                'columns'     => [1 => 'Barn', 2 => 'Hen house', 3 => 'Milking parlor'],
            ], [
                'type'      => 'slurry',
                'heading'   => 'Slurry Handling',
                'question'  => 'Will you be handling slurry?',
                'questions' => @$questions['slurry'],
            ], [
                'type'      => 'harvesting',
                'heading'   => 'Harvesting Risk Assessment',
                'question'  => 'Will you be harvesting any type of crop?',
                'questions' => @$questions['harvesting'],
            ], [
                'type'        => 'equipment',
                'heading'     => 'Portable and Fixed Equipment Safety Assessment',
                'question'    => 'Will you be be performing workshop activities with timber or general repair?',
                'questions'   => @$questions['equipment'],
                'column_text' => 'Select the portable and fixed equipment used on the farm',
                'columns'     => [1 => 'Air compressor', 2 => 'Angle grinder', 3 => 'Hoist', 4 => 'Power washer', 5 =>'Welder']
            ]/*, [
                'type'      => 'electricity',
                'heading'   => 'Electricity Safety Assessment',
                'question'  => 'Will you be working around electricity?',
                'questions' => @$questions['electricity'],
            ], [
                'type'      => 'chemicals',
                'heading'   => 'Chemical Safety Assessment',
                'question'  => 'Will you be dealing with chemicals?',
                'questions' => @$questions['chemical'],
            ], [
                'type'      => 'health',
                'heading'   => 'Health Risk Assessment',
                'question'  => 'Will you be taking health precautions?',
                'questions' => @$questions['health'],
            ]*/,
        ];
    }

    public static function get_questions()
    {
        // https://www.hsa.ie/eng/Publications_and_Forms/Publications/Agriculture_and_Forestry/Farm_Safety_Code_of_Practice_Risk_Assessment_Document.pdf

        $cars = [
            [
                'name' => '',
                'type' => 'input',
                'questions' => [
                    'Registration number',
                    'Mileage (in km)'
                ],
            ],
            [
                'name' => 'External Vehicle Checks',
                'questions' => [
                    'Vehicle sitting square and not leaning to one side. No leaks underneath vehicle',
                    'Wheels in good condition and secure. All wheel nuts in place correctly fitted and secure',
                    'Tyres undamaged with correct inflation, tread depth and "E" mark',
                    'Bumpers, body work and exhaust secure and in good condition',
                    'Wiper blades in good condition',
                    'Number plates (front & rear), correct type and position, clean and visible',
                    'Lights, indicators, reflectors and hazard lights all in place, clean, correct colour, with no breaks or cracks',
                    'Mirrors secure, clean and in good condition',
                    'Doors open, close and lock correctly Valid insurance, NCT (where required) and tax discs displayed',
                    'Engine oil, coolant, water, windscreen washer reservoir, brake fluid and fuel levels checked and no leaks.',
                    'Fuel cap seal in place, in good condition and no leaks',
                ]
            ], [
                'name' => 'In-Vehicle Checks',
                'questions' => [
                    'Interior clean with no obstructions or loose material in the footwell(s), seats or parcel shelf behind rear seats',
                    'Hi-visibility triangle and vest readily accessible in the vehicle',
                    'Windows clean, in good condition, open and close properly and view not obstructed',
                    'Pedals have good anti-slip condition',
                    'Driving controls, seat and head rest positions adjusted correctly',
                    'Mirrors correctly aligned',
                    'Safety belts adjusted and working correctly',
                    'All instrument gauges and warning lights working correctly',
                    'Wipers, washers, horn, demister and temperature controls working correctly',
                ]
            ], [
                'name' => 'Prior to Driving',
                'questions' => [
                    'No excessive play in the steering wheel and brake pedal',
                    'No excessive smoke or noise from exhaust',
                    'Check goods/materials carried in vehicle [vehicle cabin or boot] are placed and secured appropriately',
                ],
            ], [
                'name' => 'On-the-Road',
                'questions' => [
                    'All warning lights off',
                ]
            ]
        ];

        $people = [
            [
                'name' => 'Children and young persons',
                'questions' => [
                    'A safe and secure play area for children is provided away from all work activities or children are supervised directly.',
                    'The dangers to children on the farm are explained to all children and young persons.',
                    'A high level of adult supervision will be in place when children are present on the farm.',
                    'Children under 14 are not allowed operate tractors or self-propelled machines.',
                    'Only children and young persons over 14 will be allowed to drive a tractor or self-propelled vehicle in line with legal requirements.',
                    'Children over 14 and young persons who drive tractors have attended safe tractor driving skills training and are closely supervised when driving the tractor.',
                    'Children under 7 are not allowed to be carried on a tractor or self-propelled vehicle in line with legal requirements.',
                    'Particular dangers to children on my farm are identified and controlled for example, tractor operation, slurry pits, falls.',
                    'Children over 7 are only carried if a seat with a lap-belt is provided.',
                    'Contractors are made aware of the possible presence of children and of these controls.'
                ]
            ], [
                'name' => 'Older farmers, household members',
                'questions' => [
                    'Physical ability, age related limitations and risks are identified, particularly when working with machinery, livestock and accessing heights.',
                    'The older farmer will consider his speed of movement and any other limitations before and during work activity.',
                    'The older farmer considers if he/she is physically able to carry out the work at hand.',
                    'Measures are taken to minimize risks to all including risks to visitors on the farm.'
                ]
            ]
        ];

        $vehicles = [
            [
                'name' => null,
                'questions' => [
                    'The Cab/Roll bar is in good condition.',
                    'The U guard is in place to cover the PTO stub.',
                    'All controls are in working order and are clearly marked/understood.',
                    'The brakes are in good working order and adequate for the work undertaken.',
                    'The handbrake/parking brake is fully operational.',
                    'The mirrors, lights, indicators and wipers are all functioning, clean and visible.',
                    'All hitching equipment is free of defects.',
                    'All visible defects in the vehicle are identified and rectified before starting work. (List defects on control sheet)'
                ]
            ], [
                'name' => 'Safety practices',
                'questions' => [
                    'Pre-checks are carried out on tractors and vehicles before use.',
                    'Regular maintenance will be carried out on all tractors and vehicles.',
                    'The tractor/ farm vehicle is only operated by drivers who are trained and competent.',
                    'Where the operator handbook is available it will be consulted.',
                    'Lifting equipment (material) is examined annually.',
                    'Lifting equipment (lifting people) is examined 6 monthly.',
                    'The vehicle is always started and operated from the correct position.',
                    'Passengers are only carried where the manufacturer has provided a seat and seat belt for this purpose.',
                    'A helmet is worn by the operator when driving a quad bike.',
                    'When starting and operating any vehicle, the driver looks out for bystanders.',
                    'The speed of all vehicles will be suitable for the ground or road conditions.',
                    'When a vehicle is stopped, the SAFE STOP procedure is used.',
                    'The cab floor is kept clear to allow safe use of brakes & clutch.',
                    'Where farmyard is close to farmhouse, traffic risks are assessed and controlled.'
                ]
            ]
        ];

        $machinery = [
            [
                'name '=> null,
                'questions' => [
                    'All safety guards/ devices are fitted, undamaged and in good working order.',
                    'The “O” guards are present on the machine end of the PTO drive.',
                    'The hydraulic systems and hoses are in good repair.',
                    'Pre-checks are carried out on machinery before use.',
                    'Regular maintenance is carried out.'
                ]
            ], [
                'name' => 'Safety Practices',
                'questions' => [
                    'Machinery is only operated by competent operators.',
                    'All machinery is pre-checked with any safety defects identified and rectified before use.',
                    'The operator hand book where available is read and understood.',
                    'Hydraulic equipment is supported with an adequate prop during maintenance or repair for example, jack stands.',
                    'Machinery, PTO’s and moving parts are stopped before attempting to carry out maintenance or free any blockage.',
                    'Passengers are not carried on machines unless designed to do so.',
                    'The controls for the safety of children and young person’s set out on page 7 are applied to machinery.',
                    'Loads are stable and well secured.',
                    'All trailers comply with Road Safety Authority (RSA) requirements.',
                    'Appropriate PPE and workwear is worn when operating machinery for example, quad helmet when driving a quad.',
                    'Ground conditions on slopes are assessed prior to machine work on steep ground.'
                ]
            ]
        ];

        $livestock = [
            [
                'name' => null,
                'questions' => [
                    'Pens, fencing, crush(es) and skulling gates and other handling facilities are adequate and allow safe animal handling.',
                    'Gates can be securely closed.',
                    'Fencing is adequate to contain stock.',
                    'Facilities for loading and unloading of animals are adequate.',
                    'A calving gate (which provides operator protection) is used for calving cows.',
                    'A physical barrier is established when handling calves with freshly calved cows.',
                    'A bull pen which prevents direct contact with the bull is provided when the bull is housed.',
                    'When outdoors the bull has a chain/rope attached to the ring.',
                    'A safe means of escape is available in the calving pen/bull pen.',
                    'All visible defects in livestock facilities are rectified. (List defects on control sheet).'
                ]
            ], [
                'name' => 'Safety Practices',
                'questions' => [
                    'Persons handling livestock, especially a bull, are competent and fit.',
                    'A vehicle is used when herding if a bull is running with the herd.',
                    'Signs warning of the presence of a bull are displayed beside public places.',
                    'Adequate assistance is in place when carrying out animal handling operations.',
                    'Aggressive animals are culled without delay.',
                    'Suitable PPE and gloves are worn when handling animals.'
                ]
            ]
        ];

        $locations = [
            [
                'name' => null,
                'questions' => [
                    'Farmyard and farm buildings are tidy and kept in good repair.',
                    'All maintenance work is planned and only undertaken by competent persons.',
                    'Loader buckets or similar are not used for work at height.',
                    'A Mobile Elevating Work Platform (MEWP) is considered for all maintenance work at height.',
                    'If not using an MEWP, safe means of access to heights is used for example, stairs, work platform, ladder secured and footed)',
                    'If not using an MEWP, work on roofs is only undertaken with proper roofing ladders/crawling boards.',
                    'Fragile roof signs are in place where appropriate.',
                    'Construction regulations are followed for buildings under construction and other construction work on the farm.',
                    'Bales are securely stacked.',
                    'Suitable fire safety equipment is available.',
                    'Swinging doors can be secured.',
                    'Exits onto public roads are safe.',
                    'All visible defects in the farmyard and buildings are rectified. (List defects on control sheet)'
                ]
            ]
        ];

        $slurry = [
            [
                'name' => null,
                'questions' => [
                    'Open slurry/water tanks are fenced to a height of 1.8 meters and secured (including gates) to prevent access.',
                    'Access (agitation) points to slatted tanks are kept secured.',
                    'Slurry agitation/spreading is planned taking account of weather forecasts choosing a windy day if possible.',
                    'Livestock are removed from sheds and pets are controlled before slurry agitation starts.',
                    'All doors and sheeted gates are opened to maximize ventilation.',
                    'During slurry agitation buildings and high risk areas are cordoned off to prevent access.',
                    'Persons will stay away from agitation area for 30 mins after commencement.',
                    'During slurry agitation and spreading agitation points are guarded and where possible safety grids are fitted.',
                    'Manhole covers are replaced as soon as possible.',
                    'Slurry gas warning signs are in place at agitation points.',
                    'Entry into an underground slurry/effluent tank is never undertaken without full risk assessment and safety controls in place.',
                    'Condition of slats is checked for damage regularly.',
                    'Work is carried out upwind of agitation with no reliance placed on slurry gas monitors'
                ]
            ]
        ];

        $harvesting = [
            [
                'name' => null,
                'questions' => [
                    'The harvest is carefully planned and machinery prepared to prevent any accidents or injury.',
                    'All persons, including family members not directly involved are kept away from the harvest, particularly children.',
                    'All involved with tractor and machinery operation are competent and supervised during the work.',
                    'Adequate rest breaks are planned for and given to all operators during the harvest.',
                    'All machinery is pre-checked before use and any safety defects identified are rectified. (Immediately where possible)',
                    'Regular maintenance is carried out to prevent breakdowns.',
                    'All safety guards, particularly PTO guards, are kept in place at all times.',
                    'Extra care is taken with machinery with exposed moving parts and crop intake points.',
                    'All blockages and stoppages are dealt with by following: ‘Engine off, Handbrake on’, safe working procedures.',
                    'Passengers are not carried unless a suitable passenger seat is provided. (Driver under instruction or training)',
                    'All persons are kept well away from operating mowers, mulchers and hedge-cutters due to risk of projectile stones and blades.',
                    'All operators and/or contractors are made aware of any electrical lines, phone lines, uneven surfaces and slopes.',
                    'No machine is overloaded.',
                    'RSA rules are adhered to on the public road.',
                    'I communicate the high risk of accidents to all operators and /or contractors during harvest time.',
                    'Good communication is maintained between operators of all machinery in the harvesting process.',
                    'Silage pits are not overfilled and their fill height must not put machinery operators at risk.',
                    'Operators on silage pits are sufficiently capable of carrying out their role safely and prevent overturns or collisions.',
                    'No person goes underneath the silage cover due to risk of smothering and/or gassing.',
                    'The sides and ends of earthen embankments are retained at a safe angle.',
                    'Sighting rails are re-installed and maintained along silage walls.',
                    'Silage pits are designed to Department of Agriculture, Food and the Marine specifications.',
                    'Bales are loaded, transported and stacked carefully to ensure they don’t roll or fall.',
                    'Safe means of tying down loaded bales is planned and carried out.',
                    'Stacks of bales are stacked safely and not positioned near overhead power lines.',
                    'Bale handling equipment is parked correctly following the “SAFE STOP” procedure, to eliminate risk of crushing or spiking.',
                    'The controls for the safety of children and young person’s set out on page 7 are applied to harvesting machinery.'
                ]
            ]
        ];

        $equipment = [
            [
                'name' => null,
                'questions' => [
                    'The equipment is only operated by competent operators.',
                    'All safety devices are in place and are in working order.',
                    'Manufacturer’s operation manuals are available and used.',
                    'Correct Personal Protective Equipment as specified by the manufacturer is used.',
                    'Tyres are inflated in a safe manner standing clear of the danger zone.',
                    'Tyres are changed in a safe manner by competent persons.',
                    'All visible defects in portable and fixed equipment are rectified. (List defects on control sheet)'
                ]
            ], [
                'name' => 'Chainsaws',
                'questions' => [
                    'The chainsaw is fitted with a full range of safety devices including a chain brake and a chain catcher.',
                    'The chainsaw is only used for work the operator is competent and trained to do.',
                    'Timber to be cross-cut is adequately secured and supported.',
                    'Tree felling is only carried out by a competent person who has appropriate certified training in tree felling operations.',
                    'All work with the chainsaw is carried out safely wearing correct personal protective equipment. (Helmet with visor, hearing protection, chainsaw gloves, chainsaw trousers, boots)'
                ]
            ]
        ];

        $courses = [
            [
                'name' => null,
                'questions' => [
                    'Item 1',
                    'Item 2',
                    'Item 3',
                ]
            ]
        ];

        return compact('cars', 'people', 'vehicles', 'machinery', 'livestock', 'locations', 'slurry', 'harvesting', 'equipment', 'courses');
    }

}