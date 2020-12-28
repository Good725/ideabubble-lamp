<?php

/**
 * Unit test. Chart area.
 *
 * @category   Phpdocx
 * @package    testing
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2.4
 * @link       http://www.phpdocx.com
 * @since      File available since Release 2.4
 */
require_once('simpletest/autorun.php');
require_once('../classes/CreateDocx.inc');

class TestChart_area_simple extends UnitTestCase 
{

    function testTextGenerate() 
    {
        @unlink('test_chart.docx');
        $docx = new CreateDocx();
        
		$legends = array(
		    'legend' => array('sequence 1', 'sequence 2', 'sequence 3'),
		    'Category 1' => array(9.3, 2.4, 2),
		    'Category 2' => array(2.5, 4.4, 1),
		    'Category 3' => array(3.5, 1.8, 0.5),
		    'Category 4' => array(1.5, 8, 1)
		);
		
        $args = array(
            'data' => $legends,
            'type' => 'areaChart'
        );

        $docx->addChart($args);
        $docx->createDocx('test_chart');
        $this->assertTrue(file_exists('test_chart.docx'));
        @unlink('test_chart.docx');
    }

}
