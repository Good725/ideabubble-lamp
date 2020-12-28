<?php

/*
class modelEventTest extends PHPUnit_Framework_TestCase  {

    // TABLE_EVENTS = 'plugin_events_events';


    public $testUserEmail = 'test@ideabubble.com';
    public $testUserPassword = "2016password";

    // also the app doesn't check if these fields was set
    public $eventSaveMandatoryFields = array(
        'id', 'name', 'description', 'is_public'
    );


    // ------------ event save post data --------------------
    // POST data
    // ---------------------------------------------------


    // data sets to test EventSave
    public function eventSavePostDataProvider(){

        $returnArray = array();
        // set user's email and pass
        $defaultTestUser = array(
            "email" => $this->testUserEmail,
            "password" => $this->testUserPassword,
        );
        $countOfMandatoryFields = count($this->eventSaveMandatoryFields) - 1;
        // generate test data which includes not full set of Event fields
        // add expected Exceptions
        for ($i = 0; $i < $countOfMandatoryFields; $i++){
            $returnArray[] = array(
                                    $defaultTestUser,
                                    array_combine(
                                                    array_slice($this->eventSaveMandatoryFields, 0, $i+1),
                                                    array_pad(array(), $i+1, "")
                                                 ),
                                    array(
                                        "exceptionFlag" => true,
                                        "exceptionObject" =>  new Exception('Undefined index: ' . $this->eventSaveMandatoryFields[$i + 1] ),
                                    )
                                 );
        }

        return $returnArray;


    }


    // @return user ID
    public function mockUserDataProvider(){

        // return user ID for mocked Auth
        return array(

            '0' => ['1'],
        );
    }


    //  Generate Mock for AuthInstance
    // @dataProvider mockUserDataProvider
    public function testGenerateAuthInstance($userId){

        $id = 1;

        $AuthInstanceMock = $this->getMockBuilder('\Auth')
            ->disableOriginalConstructor()
            ->setMethods(array('get_user', 'has_access','_login', 'password', 'check_password', 'session_type', '_login_with_external_provider'))
            ->getMock();


        $AuthInstanceMock->expects($this->any())
            ->method('get_user')
            ->will($this->returnValue(array('id' => $userId)));

        $userData = $AuthInstanceMock->get_user();
        // test Auth Mock
        $this->assertNotEmpty($userData, "User data from AuthInstanceMock is empty");

        return $AuthInstanceMock ;

    }



    public function userDataProvider(){

        // init test user
        $querySelect = DB::select()->from('engine_users')
                                   ->where('email', '=', 'test@ideabubble.com')
                                   ->and_where('password', '=', '7a46ac225fdc315fce0192a5017238ae39676fe7b75eaf0b848dbfb4ad519593') ;
        $result = $querySelect->execute()->as_array();

        if (empty($result)){

            $insertResult = DB::query(Database::INSERT,
                "INSERT IGNORE INTO `engine_users` (`role_id`,`email`,`password`,`name`,`surname`,`registered`,`email_verified`,`can_login`,`deleted`,`status`)
                SELECT `engine_project_role`.`role`, 'test@ideabubble.com','7a46ac225fdc315fce0192a5017238ae39676fe7b75eaf0b848dbfb4ad519593','Test','Ideabubble',CURRENT_TIMESTAMP,1,1,0,1
                FROM `engine_project_role` WHERE `role` = 'Administrator'")
                    ->execute();

            $this->assertNotEmpty($insertResult);
        }



        return array(

               0 => array(
                         array(
                             "email"    => $this->testUserEmail,
                             "password" => $this->testUserPassword,
                         )
                  ),
            );
    }

     // @param userData : email and password
     // @param postData : event data
     // @param exeptionData : data about possible exception
     //
     // @dataProvider eventSavePostDataProvider
     // @runInSeparateProcess
    public function testEventSaveWithoutAuth($userData, $postData,  $exceptionData){


        //$this->markTestSkipped('This test is not finished yet');
        require_once(DOCROOT . "plugins/events/development/classes/model/event.php");


        //$this->assertNotEmpty($userData['id']);
        try {
           $id = Model_Event::eventSave($postData);
        } catch (Exception $e){

           // check Exception with fail data
           if ($exceptionData['exceptionFlag'])
               $this->assertEquals(
                                $exceptionData['exceptionObject']->getMessage(),
                                $e->getMessage()
                                );

        }


    }

     * @param $userData : array which contains 'email' and 'password'
     * @dataProvider userDataProvider
     * @runInSeparateProcess
    public function testAccessToEventFolder($userData){

        $this->markTestSkipped('This test is not finished yet');

        require_once(DOCROOT . "plugins/files/development/classes/model/files.php");

        Auth::instance()->login($userData['email'],$userData['password']);
        $user = Auth::instance()->get_user();
        $this->assertNotEmpty($user, "User session data is empty (testAccessToEventFolder)");

        $iDirId = Model_Files::get_directory_id('/events');
        if (!$iDirId) {
            $this->assertNull($iDirId);
            Model_Files::create_directory(1, 'events');
            $iDirId = Model_Files::get_directory_id('/events');
        }
        $this->assertNotEmpty($iDirId, print_r($iDirId, true));


    }
     * @param $userData : array which contains 'email' and 'password'
     * @dataProvider userDataProvider
     * @runInSeparateProcess
    public function testAccessToVenueFolder($userData){


        $this->markTestSkipped('This test is not finished yet');

        require_once(DOCROOT . "plugins/files/development/classes/model/files.php");

        Auth::instance()->login($userData['email'],$userData['password']);
        $user = Auth::instance()->get_user();
        $this->assertNotEmpty($user, "User session data is empty (testAccessToVenueFolder)");


        $iDirId = Model_Files::get_directory_id('/venues');
        if (!$iDirId) {
            $this->assertNull($iDirId);
            Model_Files::create_directory(1, 'venues');
            $iDirId = Model_Files::get_directory_id('/venues');
        }
        $this->assertNotEmpty($iDirId, print_r($iDirId, true));


    }


    public function eventDescriptionDataProvider(){

        return array (

            [
                "<p onload=alert('test1')> Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Curabitur suscipit, turpis ut dictum ornare, enim augue faucibus libero,
                    eget pellentesque velit quam suscipit ex. Mauris id interdum velit.</p>",

                "onload=alert('test1')"
            ],

            [
                "<script type='text/javascript'>
                        var adr = '../evil.php?cakemonster=' + escape(document.cookie);
                 </script>
                  Curabitur suscipit, turpis ut dictum ornare, enim augue faucibus libero,
                    eget pellentesque velit quam suscipit ex. Mauris id interdum velit
                    "
                ,
                "<script>"
            ]




        );

    }


     *  Check description for XSS vulnerabilities
     *
     * @param $description event description
     * @dataProvider eventDescriptionDataProvider
    public function testEventSaveDescriptionClean($description, $xssCode){

        $parsedDescription = html::clean($description);
        $strposResult = strpos($parsedDescription, $xssCode);
        $this->assertFalse($strposResult, 'Description can contain XSS vulnerabilities , like : ' . $xssCode );

    }


    // --------------------------------------- ----------------------------------------------------

    // Check if VatRate is a valid float and not NULL
    public function testIfVatRateExists(){

        $vatRate = (float)Settings::instance()->get('vat_rate');
        $this->assertNotNull($vatRate);

    }



    public function dataProviderForOrderCalculation(){

        $ticketNames = array("TestName1", "TestName2", "TestName3");
        $minPerOrder = array(2, 5, 8);
        $maxPerOrder = array(4, 6, 10);
        $ticketQuantities = array(1, 10, 9 );


        return array(

                '0' => [
                    // event
                    array( "id" => 0, "is_onsale" => '', 'currency' => 'EUR' ),
                    // buyTicketTypes
                    array(),
                    // expected Error
                    "Event is not on sale!"
                ],

                '1' => [
                    // event
                    array( "id" => 0, "is_onsale" => 0, 'currency' => 'EUR' ),
                    // buyTicketTypes
                    array(),
                    // expected Error
                    "Event is not on sale!"

                ],

                '2' => [
                    // event
                    array(
                            "id" => 0, "is_onsale" => 1,
                            "one_ticket_for_all_dates" => 0, "owned_by" => 0,
                            "ticket_types" => array(
                                array( "id" => '1', "name" => $ticketNames[0],
                                        "min_per_order" => $minPerOrder[0], "max_per_order" => $maxPerOrder[0]
                                    )
                            ),
                            'currency' => 'EUR'
                        ),
                    // buyTicketTypes
                    array(

                           array( "quantity" => $ticketQuantities[0], "ticket_type_id" => "1", "dates" => array(NULL)),

                    ),
                    // expected Error
                    trim("You need to buy at least " . $minPerOrder[0]. " of " . $ticketNames[0]),

                ],

                '3' => [

                    // event
                    array(
                        "id" => 0, "is_onsale" => 1,
                        "one_ticket_for_all_dates" => 0, "owned_by" => 0,
                        "ticket_types" => array(
                            array( "id" => '1', "name" => $ticketNames[1],
                                    "min_per_order" => $minPerOrder[1], "max_per_order" => $maxPerOrder[1]
                                )
                        ),
                        'currency' => 'GBP'
                    ),
                    // buyTicketTypes
                    array(

                        array( "quantity" => $ticketQuantities[1], "ticket_type_id" => "1", "dates" => array(NULL)),

                    ),
                    // expected Error
                    trim("You can not buy more than " . $maxPerOrder[1]. " of " . $ticketNames[1]),

                 ],

                '4' => [

                    // event
                    array(
                        "id" => 0, "is_onsale" => 1,
                        "one_ticket_for_all_dates" => 0, "owned_by" => 0,

                        "ticket_types" => array(
                            array( "id" => '1', "name" => $ticketNames[2],
                                "min_per_order" => $minPerOrder[2], "max_per_order" => $maxPerOrder[2],
                                "sale_starts" => date("Y-m-d H:i:s", strtotime('+5 hours')),
                            )
                        ),
                        'currency' => 'EUR'
                    ),
                    // buyTicketTypes
                    array(

                        array( "quantity" => $ticketQuantities[2], "ticket_type_id" => "1", "dates" => array(NULL)),

                    ),
                    // expected Error
                    trim($ticketNames[2] . " is not on sale"),


                ],

                '5' => [

                        // event
                        array(
                            "id" => 0, "is_onsale" => 1,
                            "one_ticket_for_all_dates" => 0, "owned_by" => 0,

                            "ticket_types" => array(
                                array( "id" => '1', "name" => $ticketNames[2],
                                    "min_per_order" => $minPerOrder[2], "max_per_order" => $maxPerOrder[2],
                                    "sale_starts" => date("Y-m-d H:i:s", strtotime('-10 hours')),
                                    "sale_ends" => date("Y-m-d H:i:s", strtotime('-5 hours')),
                                )
                            ),
                            'currency' => 'EUR'
                        ),
                        // buyTicketTypes
                        array(

                            array( "quantity" => $ticketQuantities[2], "ticket_type_id" => "1", "dates" => array(NULL)),

                        ),
                        // expected Error
                        trim($ticketNames[2] . " is not on sale"),


                    ]
        );


    }

     *  Test Model_Event::orderCalculate for error responses
     *
     * @dataProvider dataProviderForOrderCalculation
    public function testCheckErrorResponsesFromOrderCalculation($event, $buyTicketTypes, $expectedError){

        //$this->markTestSkipped('This test is not finished yet');
        require_once(DOCROOT . "plugins/events/development/classes/model/event.php");
        $response = Model_Event::orderCalculate($event, $buyTicketTypes, $discountCode = '');

        $this->assertEquals(trim($response['error']), $expectedError);

    }

    // ---------------------------------------------------------------------------------------

     // Check refund option for payment which doesn't exist
    public function testCheckNullResponseForPayment(){

        require_once(DOCROOT . "plugins/events/development/classes/model/event.php");
        $paymentRefundResponse = Model_Event::paymentRefund(0);
        $this->assertNull($paymentRefundResponse);
    }


    public function dataProviderForStripeApi(){

        return array(

            0 => [1], // check $stripe_testing
            1 => [2], // check $stripe_testing and $secret_key
            2 => [3], // check $stripe_testing and $secret_key and $publishable_key
        );
    }


     * Check stripe Api Key
     * @dataProvider dataProviderForStripeApi
    public function testStripeApiKeysNotEmpty($checkStep){


        require_once (DOCROOT . 'application/vendor/stripe/lib/Stripe.php');
        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
        $secret_key = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
        $publishable_key = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');

        // step 1
        if ($checkStep >= 1)
            $this->assertNotNull($stripe_testing);

        // step 2
        if ($checkStep >= 2){
            $this->markTestSkipped("Stripe keys can be empty for stage");
            $this->assertNotEmpty($secret_key, "Stripe private key is empty");
        }


        // step 3
        if ($checkStep >= 3){
            $this->markTestSkipped("Stripe keys can be empty for stage");
            $this->assertNotEmpty($publishable_key, "Stripe public key is empty");
        }




    }




}
*/