<?php

/**
 * Unit test. Chart radar simple.
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

class TestChart_radar_simple extends UnitTestCase
{

    function testTextGenerate()
    {
        @unlink('test_chart.docx');
        $docx = new CreateDocx();
        $legends = array(
            'legend' => array('sequence 1'),
            'legend1' => array(9.3),
            'legend2' => array(8.5),
            'legend3' => array(6.5)
        );
        $args = array(
            'data' => $legends,
            'type' => 'radar',
            'style' => 'filled',
        );
        $docx->addChart($args);
        $docx->createDocx('test_chart');
        $this->assertTrue(file_exists('test_chart.docx'));
        @unlink('test_chart.docx');
    }

}
