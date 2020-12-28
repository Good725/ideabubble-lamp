<?php

/**
 * Searches for a string of text and replace it by another one.
 *
 * @category   Phpdocx
 * @package    examples
 * @subpackage easy
 * @copyright  Copyright (c) 2009-2011 Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    http://www.phpdocx.com/wp-content/themes/lightword/pro_license.php
 * @version    2.5.3
 * @link       http://www.phpdocx.com
 * @since      File available since Release 2.5.3
 */

require_once '../../classes/DocxUtilities.inc';

$newDocx = new DocxUtilities();
$options = array( 'document' => true,
									'endnotes' => true,
									'comments' => true,
									'headersAndFooters' => true,
									'footnotes' => true);

$newDocx->searchAndReplace('../files/second.docx', '../docx/replacedDocx.docx', 'data', 'required data', $options);

