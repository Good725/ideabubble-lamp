<?php

class DashBoardTest extends PHPUnit_Framework_TestCase{

	public function testRegisterWidget(){

		$dashBoardObject =  new DashBoard();

		// test data (url should be unique to prevent the rewriting)
		$widgets[0] = array(

			'url'  	     => "url0",
			'title' 	     => 'title0',
			'title_tag'   => '',
			'floatright'  => false,
			'id'	     => '',
		);

		$widgets[1] = array(

			'url'  	     => "url1",
			'title' 	     => 'title1',
			'title_tag'   => '',
			'floatright'  => false,
			'id'	     => '',
		);

		$widgets[2] = array(

			'url'  	     => "url2",
			'title' 	     => 'title2',
			'title_tag'   => '',
			'floatright'  => false,
			'id'	     => '',
		);
		
		foreach ($widgets as $widget) 
			// return void
			$this->assertEmpty($dashBoardObject->factory()->register_widget($widget['url'],  $widget['title'], $widget['title_tag'], $widget['floatright'], $widget['id']));
		

		// check amount of widgets
		$widgetsCount = count($widgets);
		$this->assertEquals($widgetsCount, $dashBoardObject->count_registered_widgets());
	}

}