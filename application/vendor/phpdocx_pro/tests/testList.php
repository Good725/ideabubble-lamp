<?php

/**
 * Unit test. List.
 *
 * @category   Phpdocx
 * @package    testing
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    1.0
 * @link       http://www.phpdocx.com
 * @since      File available since Release 1.0
 */
require_once('simpletest/autorun.php');
require_once('../classes/CreateDocx.inc');

class TestList extends UnitTestCase
{

    function testListGenerate()
    {
        @unlink('test_list.docx');
        $docx = new CreateDocx();
        $values = array(
            'Line 1',
            'Line 2',
            'Line 3',
            'Line 4',
            'Line 5'
        );

        $paramsList = array(
            'val' => 1
        );

        $docx->addList($values, $paramsList);
        $docx->createDocx('test_list');
        $this->assertTrue(file_exists('test_list.docx'));
    }

}
