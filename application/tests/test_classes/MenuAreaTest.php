<?php

class  MenuAreaTest extends PHPUnit_Framework_TestCase{


	public function testFactory(){

		$menuAreaObject = MenuArea::factory();
		$this->assertNotEmpty($menuAreaObject);

	}


	public function testRegisterLinks(){

		$menuAreaObject = new MenuArea();

		//    		Test data
		//    These  urls doesn't exist inside project
		//    and  will not have any sub menus
		$linksData = array();

		$linksData[0]['url'] = 'test-url-0';
		$linksData[0]['name'] = 'test-name-0';

		$linksData[1]['url'] = 'test-url-1';
		$linksData[1]['name'] = 'test-name-1';

		// register all links 
		foreach ($linksData as $link)
			 //  'void' should be returned
			$this->assertEmpty($menuAreaObject->register_link($link['url'], $link['name']));


		//    Compares generated links with the test input,
		//    and calls  the 'generate_links()" without "current_controller" parameter
		$generateLinkResult = $menuAreaObject->generate_links();
		$count  = count($generateLinkResult);
		for ($i = 0; $i < $count; $i++){

			// link template without 'active' class and without submenus 
			$linkTemplate =  '<li data-controller="' . $linksData[$i]['url'] .'" class="sidebar-menu-li ">' .
					  '<a class="maz" href="' . URL::Site('admin/' . $linksData[$i]['url']) . '">' . __($linksData[$i]['name']) . '</a></li>';

			$this->assertEquals(trim($generateLinkResult[$i]), trim($linkTemplate));
		}

	}
}

