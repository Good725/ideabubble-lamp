<?php

/**
 * Unit test. Testbox fillcolor.
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

class TestTextBox_color extends UnitTestCase
{

    function testTextGenerate()
    {
        @unlink('test_text.docx');
        $docx = new CreateDocx();
        $paramsBox = array(
            'fillcolor' => 'red'
        );

        $docx->addTextBox(
            array('text' => $text, 'args' => $paramsText),
            $paramsBox
        );

        $docx->createDocx('test_text');
        $this->assertTrue(file_exists('test_text.docx'));
        @unlink('test_text.docx');
    }

}
