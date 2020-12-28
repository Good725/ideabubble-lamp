<?php

/** Unit test. Parse Styles.
 *
 * @category   Phpdocx
 * @package    testing
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2.2
 * @link       http://www.phpdocx.com
 * @since      File available since Release 2.5
 */
require_once('simpletest/autorun.php');
require_once('../classes/CreateDocx.inc');

class TestPropertiesApp extends UnitTestCase
{

    function testPropertiesGenerate()
    {
        @unlink('test_parse_styles.docx');
        $docx = new CreateDocx();
        $docx->parseStyles();
        $docx->createDocx('test_parse_styles');
        $this->assertTrue(file_exists('test_parse_styles.docx'));
        @unlink('test_parse_styles.docx');
    }

}
