<?php

/*
class adminEventsTest extends PHPUnit_Framework_TestCase{

    public function dataProviderForAjaxToggleEventPlugin(){

        return [
            0 => [ 0, 1, '1'], // id, publish, excepted output
            1 => [ 0, 0, '1'],
        ];

    }
*/
    /**
     *  Test for Controller_Admin_Events->action_ajax_toggle_event_publish()
     *  Creation of empty events are allowed
     *
     * @param $paramId : Event ID
     * @param $publishValue : value of 'publish' field
     * @param $expectedOutput
     *
     * @dataProvider dataProviderForAjaxToggleEventPlugin
     * @runInSeparateProcess
     */
    /*
    public function testActionAjaxToggleEventPublish($paramId, $publishValue, $expectedOutput){


        require_once(DOCROOT . "plugins/events/development/classes/controller/admin/events.php");
        require_once(DOCROOT . "plugins/events/development/classes/model/event.php");

        // generate mock object for request
        $requestMock = $this->getMockBuilder('\Request')
            ->setConstructorArgs(array("dummy_uri"))
            ->setMethods(array('param', 'method', 'uri'))
            ->getMock();


        $requestMock->expects($this->any())
            ->method('param')
            ->with('id')
            ->will($this->returnValue($paramId));


        $controllerAdminEventsObject = new Controller_Admin_Events($requestMock, new Response());
        $controllerAdminEventsObject->request->query('publish', (string)$publishValue);
        $this->expectOutputString($expectedOutput); // 	echo $event->save_with_moddate() ? 1 : 0;

        $db = Database::instance();
        $db->begin();

        // updates existing value or saves new empty Event into DB
        $controllerAdminEventsObject->action_ajax_toggle_event_publish();

        // remove test data from DB
        $db->rollback();

    }



    public function dataProviderForAjaxToggleEventPluginWithoutCreation(){

        return [
            0 => [ 41, 1, '1'], // id, publish, excepted output
            1 => [ 0, 0, '0'],
        ];

    }
    */


    /**
     *  Test for Controller_Admin_Events->action_ajax_toggle_event_publish()
     *  Creation of empty events are NOT allowed
     *
     * @param $paramId : Event ID
     * @param $publishValue : value of 'publish' field
     * @param $expectedOutput
     *
     * @dataProvider dataProviderForAjaxToggleEventPluginWithoutCreation
     * @runInSeparateProcess
     */
    /*
    public function testActionAjaxToggleEventPublishWithoutCreation($paramId, $publishValue, $expectedOutput){

        $this->markTestSkipped('This test requires fixes to the original method');

        require_once(DOCROOT . "plugins/events/development/classes/controller/admin/events.php");
        require_once(DOCROOT . "plugins/events/development/classes/model/event.php");
        require_once(DOCROOT . "plugins/contacts2/development/classes/model/contacts.php");


        // generate mock object for request
        $requestMock = $this->getMockBuilder('\Request')
            ->setConstructorArgs(array(NULL))
            ->setMethods(array('param'))
            ->getMock();


        $requestMock->expects($this->any())
            ->method('param')
            ->with('id')
            ->will($this->returnValue($paramId));


        $controllerAdminEventsObject = new Controller_Admin_Events($requestMock, new Response());
        $controllerAdminEventsObject->request->query('publish', (string)$publishValue);

        // Check if Event exists by ID.
        // If Event doesn't exist -> the method (action_ajax_toggle_event_publish()) should stop saving process
        $eventFromDb = Model_Event::eventLoad($paramId);
        if (empty($eventFromDb))
            $expectedOutput = '0';

        $this->expectOutputString($expectedOutput); // 	echo $event->save_with_moddate() ? 1 : 0;

        $db = Database::instance();
        $db->begin();

        // updates existing value or saves new empty Event int DB
        $controllerAdminEventsObject->action_ajax_toggle_event_publish();

        $db->rollback();

    }


}
    */