<?php

class ArrTest extends PHPUnit_Framework_TestCase {

	// 	check plugins in DB
	public function testConstruct(){

		$modelPlugin  = new Model_Plugin();
		$list = $modelPlugin->get_all();
		$this->assertNotEmpty($list);
	}
}