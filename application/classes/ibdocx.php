<?php
class IbDocx
{
    protected $zip;

    public $variables = array();

    public function __construct()
    {

    }

    public function processDocx($path, $variables, $save_to = null)
    {
        if ($save_to != null) {
            copy($path, $save_to);
            $this->open($save_to);
        } else {
            $this->open($path);
        }

        $this->setVariables($variables);

        $this->process();

        $this->close();

        return true;
    }

    public function open($path)
    {
        $this->zip = new ZipArchive();
        $this->zip->open($path);
    }

    public function close()
    {
        $this->zip->close();
    }

    protected function escape($string)
    {
        $map = array(
            '&' => '&amp;',
            '"' => '&quot;',
            "'" => '&apos;',
            '<' => '&lt;',
            '>' => '&gt;',
            "\r\n" => '<w:br/>',
            "\r" => '<w:br/>',
            "\n" => '<w:br/>'
        );
        $string = str_replace('$', '#0024;', $string);
        $string = str_replace('@', '#0064;', $string);
        $string = str_replace(array_keys($map), array_values($map), $string);
        return $string;
    }


    public function setVariables($arr)
    {
        $this->variables = $arr;
    }

    public function get_variables()
    {
        $xml = $this->zip->getFromName('word/document.xml');
        $xml = $this->cleanup_split_vars($xml);
        $matches = null;
        preg_match_all('/\$([a-z0-9_]+)\$/i', $xml, $matches1);
        preg_match_all('/\@([a-z0-9_]+)\@/i', $xml, $matches2);
        return array_unique(array_merge($matches1[1], $matches2[1]));
    }

   /*this should simple be <w:t>$EVENTNAME$</w:t>
         * <w:p w:rsidR="00E51A91" w:rsidRPr="00B56644" w:rsidRDefault="00E51A91" w:rsidP="00E51A91">
            <w:pPr>
                <w:ind w:right="42"/>
                <w:rPr>
                    <w:rFonts w:ascii="Roboto Regular" w:hAnsi="Roboto Regular"/>
                    <w:b/>
                    <w:sz w:val="36"/>
                    <w:szCs w:val="36"/>
                </w:rPr>
            </w:pPr>
            <w:r>
                <w:rPr>
                    <w:rFonts w:ascii="Roboto Regular" w:hAnsi="Roboto Regular"/>
                    <w:b/>
                    <w:sz w:val="36"/>
                    <w:szCs w:val="36"/>
                </w:rPr>
                <w:t>$EVENTN</w:t>
            </w:r>
            <w:bookmarkStart w:id="0" w:name="_GoBack"/>
            <w:bookmarkEnd w:id="0"/>
            <w:r>
                <w:rPr>
                    <w:rFonts w:ascii="Roboto Regular" w:hAnsi="Roboto Regular"/>
                    <w:b/>
                    <w:sz w:val="36"/>
                    <w:szCs w:val="36"/>
                </w:rPr>
                <w:t>AME$</w:t>
            </w:r>
        </w:p>*/
    protected function cleanup_split_vars($xml)
    {
        //$xml = '<w:p>aaaa</w:p>';
        $new_xml = $xml;
        // cleanup tags like $foo$
        if (preg_match_all('#\<w\:p(\s+[^\>]+)?\>(.+?)\<\/w\:p\>#im', $xml, $p_tags)) {
            foreach ($p_tags[0] as $i => $p_tag) {
                if (preg_match_all('#\<w\:r(\s+[^\>]+)?\>(.+?)\<\/w\:r\>#im', $p_tags[2][$i], $r_tags)) {
                    $split_var_start = false;
                    $joined_tag = '';
                    $join_tags = array();
                    foreach ($r_tags[0] as $j => $r_tag) {
                        if ($split_var_start == false) {
                            if (preg_match('#\<w\:t(\s+[^\>]+)?\>([^\$]*\$[^\$]*)\<\/w\:t\>#im', $r_tag, $t_tag)) {
                                $split_var_start = true;
                                $join_tags[] = $t_tag;
                                $joined_tag = $t_tag[2];
                            }
                        } else if ($split_var_start) {
                            if (preg_match('#\<w\:t(\s+[^\>]+)?\>([^\$]+)\<\/w\:t\>#im', $r_tag, $t_tag)) {
                                $join_tags[] = $t_tag;
                                $joined_tag .= $t_tag[2];
                            }
                            if (preg_match('#\<w\:t(\s+[^\>]+)?\>([^\$]*\$[^\$]*)\<\/w\:t\>#im', $r_tag, $t_tag)) {
                                $join_tags[] = $t_tag;
                                $joined_tag .= $t_tag[2];
                                $split_var_start = false;
                                foreach ($join_tags as $x => $join_tag) {
                                    if ($x == 0) {
                                        $p_tag = preg_replace('#' . preg_quote($join_tag[0], '#') . '#', '<w:t>' . $joined_tag . '</w:t>', $p_tag, 1);
                                    } else {
                                        $p_tag = str_replace($join_tag[0], '<w:t></w:t>', $p_tag);
                                    }
                                }
                                $new_xml = str_replace($p_tags[0][$i], $p_tag, $new_xml);
                            }
                        }
                    }
                }
            }

            // cleanup tags like @foo@
            foreach ($p_tags[0] as $i => $p_tag) {
                if (preg_match_all('#\<w\:r(\s+[^\>]+)?\>(.+?)\<\/w\:r\>#im', $p_tags[2][$i], $r_tags)) {
                    $split_var_start = false;
                    $joined_tag = '';
                    $join_tags = array();
                    foreach ($r_tags[0] as $j => $r_tag) {
                        if ($split_var_start == false) {
                            if (preg_match('#\<w\:t(\s+[^\>]+)?\>([^\@]*\@[^\@]*)\<\/w\:t\>#im', $r_tag, $t_tag)) {
                                $split_var_start = true;
                                $join_tags[] = $t_tag;
                                $joined_tag = $t_tag[2];
                            }
                        } else if ($split_var_start) {
                            if (preg_match('#\<w\:t(\s+[^\>]+)?\>([^\@]+)\<\/w\:t\>#im', $r_tag, $t_tag)) {
                                $join_tags[] = $t_tag;
                                $joined_tag .= $t_tag[2];
                            }
                            if (preg_match('#\<w\:t(\s+[^\>]+)?\>([^\@]*\@[^\@]*)\<\/w\:t\>#im', $r_tag, $t_tag)) {
                                $join_tags[] = $t_tag;
                                $joined_tag .= $t_tag[2];
                                $split_var_start = false;
                                foreach ($join_tags as $x => $join_tag) {
                                    if ($x == 0) {
                                        $p_tag = preg_replace('#' . preg_quote($join_tag[0], '#') . '#', '<w:t>' . $joined_tag . '</w:t>', $p_tag, 1);
                                    } else {
                                        $p_tag = str_replace($join_tag[0], '<w:t></w:t>', $p_tag);
                                    }
                                }
                                $new_xml = str_replace($p_tags[0][$i], $p_tag, $new_xml);
                            }
                        }
                    }
                }
            }
        }
        return $new_xml;
    }

    public function process()
    {
        $content_types = $this->zip->getFromName('[Content_Types].xml');

        $xml = $this->zip->getFromName('word/document.xml');
        $new_xml = $this->cleanup_split_vars($xml);
        $rels = $this->zip->getFromName('word/_rels/document.xml.rels');

        foreach ($this->variables as $variable => $value) {
            if (!is_array($value)) { // replace simple texts: $company$ => 'ideabubble'
                $new_xml = str_replace('$' . $variable . '$', $this->escape($value), $new_xml);
                $new_xml = str_replace('@' . $variable . '@', $this->escape($value), $new_xml);
            } else {
                if (@$value['type'] == 'image' && $value['file'] != '') {
                    $x = strpos($new_xml, '$' . $variable . '$');
                    if (!$x) {
                        $x = strpos($new_xml, '@' . $variable . '@');
                    }
                    if ($x) {
                        $d_start = strrpos(substr($new_xml, 0, $x), '<w:drawing');
                        //echo substr($new_xml, 0, $x);
                        if ($d_start) {
                            $d_end = strpos($new_xml, '</w:drawing>', $x);
                            $d_tag = substr($new_xml, $d_start, $d_end - $d_start + strlen('</w:drawing>'));
                            if (preg_match('#r\:embed\="(.*?)"#', $d_tag, $embed)) {
                                $embed_id = $embed[1];
                                //$this->zip = new ZipArchive();
                                $x = strpos($rels, 'Id="' . $embed_id . '"');
                                if ($x) {
                                    $r_start = strrpos(substr($rels, 0, $x), '<Relationship');
                                    if ($r_start) {
                                        $r_end = strpos($rels, '/>', $x);
                                        $r_tag = substr($rels, $r_start, $r_end - $r_start + strlen('/>'));
                                        if(preg_match('#Target\="(.+?)"#i', $r_tag, $target)) {
                                            $target = $target[1];
                                            @$this->zip->deleteName('word/' . $target);
                                            $this->zip->addFile($value['file'], 'word/' . $target);
                                        }
                                    }
                                }

                            }
                        }
                    }
                } else if (@$value['html'] != '') {
                    $embed_name = $variable . '.html';

                    // Remove content outside of the {document-start} and {document-end} tags
                    // Perform other page parsing then.
                    if (isset($value['styles'])) {
                        $html = IbHelpers::parse_page_content(self::strip_untagged($value['html'], $value['styles']));
                    } else {
                        $html = IbHelpers::parse_page_content(self::strip_untagged($value['html']));
                    }
                    $this->zip->addFromString('word/' . $embed_name, '<!DOCTYPE html>
<html><head></head><body>' . $html . '</body></html>');

                    $content_types = str_replace('</Types>', '<Override PartName="/word/' . $embed_name . '" ContentType="text/html"/></Types>', $content_types);
                    $this->zip->deleteName('[Content_Types].xml');
                    $this->zip->addFromString('[Content_Types].xml', $content_types);
                    
                    $rels = str_replace('</Relationships>', '<Relationship Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/aFChunk" Target="/word/' . $embed_name . '" Id="' . $variable . '" /></Relationships>', $rels);
                    $this->zip->deleteName('word/_rels/document.xml.rels');
                    $this->zip->addFromString('word/_rels/document.xml.rels', $rels);

                    $new_xml = str_replace('$' . $variable . '$', '<w:altChunk r:id="' . $variable . '" />', $new_xml);
                    $new_xml = str_replace('@' . $variable . '@', '<w:altChunk r:id="' . $variable . '" />', $new_xml);
                } else if (@$value['type'] == 'table' || (isset($value[0]) && is_array($value[0]))) { // replace table

                    /*preg_match_all('#(\<w\:tr([^>]+)?>(.+?)\<\/w\:tr\>)#mis', $new_xml, $rows); // find tags like <w:tr>....</w:tr>, <w:tr attribs="12345">....</w:tr>
                    header('content-type: text/plain');print_R($rows);exit;
                    $template_row = null;
                    foreach ($rows[0] as $tr) {
                        if (preg_match('#\$([a-z0-9\_]+\$)#i', $tr)) { // find a row containing variables like $aaaa$, $bbbb$ to be used as row template
                            $template_row = $tr;
                            break;
                        }
                    }*/

                    $new_rows_xml = '';
                    foreach ($value as $row) {
                        $columns = array_keys($row);
                        foreach ($columns as $column) {
                            $x = strpos($new_xml, '$' . $column . '$');
                            if (!$x) {
                                $x = strpos($new_xml, '@' . $column . '@');
                            }
                            if ($x) {
                                $tr_start = strrpos(substr($new_xml, 0, $x), '<w:tr>');
                                if (!$tr_start) {
                                    $tr_start = strrpos(substr($new_xml, 0, $x), '<w:tr ');
                                }
                                $t_end = strpos($new_xml, '</w:tr>', $x);
                                $template_row = substr($new_xml, $tr_start, $t_end - $tr_start + strlen('</w:tr>'));
                            }
                        }
                        $new_row_xml = $template_row;
                        foreach ($row as $column => $cvalue) {
                            $new_row_xml = str_replace('$' . $column . '$', $this->escape($cvalue), $new_row_xml);
                            $new_row_xml = str_replace('@' . $column . '@', $this->escape($cvalue), $new_row_xml);
                        }
                        $new_rows_xml .= $new_row_xml;
                    }
                    $new_xml = str_replace($template_row, $new_rows_xml, $new_xml);
                } else if (@$value['type'] == 'list' || (isset($value[0]) && !is_array($value[0]))) { // replace list

                    $x = strpos($new_xml, '$' . $variable . '$');
                    if (!$x) {
                        $x = strpos($new_xml, '@' . $variable . '@');
                    }
                    if ($x) {
                        $p_start = strrpos(substr($new_xml, 0, $x), '<w:p>');
                        if (!$p_start) {
                            $p_start = strrpos(substr($new_xml, 0, $x), '<w:p ');
                        }
                        $p_end = strpos($new_xml, '</w:p>', $x);
                        $template_line = substr($new_xml, $p_start, $p_end - $p_start + strlen('</w:p>'));

                        $new_lines_xml = '';
                        foreach ($value as $line) {
                            $new_line_xml = str_replace('$' . $variable . '$', $this->escape($line), $template_line);
                            $new_lines_xml .= $new_line_xml;
                        }
                        $new_xml = str_replace($template_line, $new_lines_xml, $new_xml);
                    }
                } else if (@$value['type'] == 'multiline' && is_array(@$value['lines']) ) { // replace multiline text
                    $x = strpos($new_xml, '$' . $variable . '$');
                    if (!$x) {
                        $x = strpos($new_xml, '@' . $variable . '@');
                    }
                    if ($x) {
                        $p_start = strrpos(substr($new_xml, 0, $x), '<w:p>');
                        if (!$p_start) {
                            $p_start = strrpos(substr($new_xml, 0, $x), '<w:p ');
                        }
                        $p_end = strpos($new_xml, '</w:p>', $x);
                        $template_line = substr($new_xml, $p_start, $p_end - $p_start + strlen('</w:p>'));

                        $new_lines_xml = '';
                        foreach ($value['lines'] as $line) {
                            $new_line_xml = str_replace('$' . $variable . '$', $this->escape($line), $template_line);
                            $new_line_xml = str_replace('@' . $variable . '@', $this->escape($line), $new_line_xml);
                            $new_lines_xml .= $new_line_xml;
                        }
                        $new_xml = str_replace($template_line, $new_lines_xml, $new_xml);
                    }
                }
            }
        }

        $new_xml = preg_replace('/\$[a-z0-9_]+\$/i', '', $new_xml);
        $new_xml = preg_replace('/\@[a-z0-9_]+\@/i', '', $new_xml);
        $new_xml = str_replace('#0024;', '$', $new_xml);
        $new_xml = str_replace('#0064;', '@', $new_xml);
        $this->zip->deleteName('word/document.xml');
        $this->zip->addFromString('word/document.xml', $new_xml);
    }

    public function generate_pdf($fileToConvert, $pathToSaveOutputFile)
    {
        try {
            $local_service = Settings::instance()->get("word2pdf_local_active");
            $third_party = Settings::instance()->get("word2pdf_thirdparty_active");

            if ($local_service) {
                $apiKey = Settings::instance()->get("word2pdf_local_api");
                $postdata = array('OutputFileName' => 'MyFile.pdf', 'ApiKey' => $apiKey, 'file' => "@" . $fileToConvert);
                if (class_exists('CURLFile')) {
                    $postdata['file'] = new CURLFile($fileToConvert);
                }
                $ch = curl_init(Settings::instance()->get("word2pdf_local_url"));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                $pdf = curl_exec($ch);
            } else if ($third_party) {
                $apiKey = Settings::instance()->get("word2pdf_thirdparty_api");
                $postdata = array('OutputFileName' => 'MyFile.pdf', 'ApiKey' => $apiKey, 'File' => "@" . $fileToConvert);
                if (class_exists('CURLFile')) {
                    $postdata['File'] = new CURLFile($fileToConvert);
                }
                $ch = curl_init(Settings::instance()->get("word2pdf_thirdparty_url"));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                $pdf = curl_exec($ch);

            } else {
                $message = "No settings set for PDF Generation. Please check settings.";
                Log::instance()->add(Log::ERROR, $message);

                throw new Exception($message);
            }
            $headers = curl_getinfo($ch);
            curl_close($ch);

            if (0 < $headers['http_code'] && $headers['http_code'] < 400) {
                // Check content type
                if ($headers['content_type'] <> "application/pdf" || strlen($pdf) == 0) {
                    Model_Errorlog::save(new Exception("Invalid pdf response"), 'PHP');
                    return false;
                }
                $fp = fopen($pathToSaveOutputFile, "wbx");
                fwrite($fp, $pdf);
                fclose($fp);
                return true;

            } else {
                $message = "PDF : Exception Message : Status Code :" . $headers['http_code'] . ".<br />";
                Log::instance()->add(Log::ERROR, $message);
                return false;
            }
        } catch (Exception $e) {

            /**
             * set message for email - include file, line and full message
             */
            $message = "PDF : There was an error creating a PDF from this address : ".URL::base()." . The PDF has not been created. There was an error in ".$e->getFile()." on line ".$e->getLine()."  The full error message is ".$e->getMessage();

            Log::instance()->add(Log::ERROR, $message);
            return false;
        }
    }

    /**
     * Strip content that is placed outside the {document-start} and {document-end} tags
     * Currently only works if there is 1 or 0 of each tag.
     */
    public function strip_untagged($html, $styles = '')
    {

        // If no tags, return everything
        if (strpos($html, '{document-start}') === false) {
            return $styles . '<div class="sans-serif">' .$html . '</div>';
        }

        // Otherwise, get content between the tags


        // This Regex is the ideal solution, but seems to be blocked by a rate limit (string too long).
        // preg_match('/<[^>].*>\{document-start\}<\/[^>]*>((\n.*)*?)<[^>].*>\{document-end\}<\/[^>]*>/m', $html, $matches);
        // $new_html = $matches[1];

        // As a temporary solution, use substring functions.
        $start = '<p>{document-start}</p>';
        $end = '<p>{document-end}</p>';
        $html = ' ' . $html;
        $ini = strpos($html, $start);
        $ini += strlen($start);
        $len = strpos($html, $end, $ini) - $ini;
        $new_html = trim(substr($html, $ini, $len));
        $doc = new DOMDocument();
        $new_html_obj = $doc->loadHTML($new_html);
        //using xPath to remove buttons from edit and empty divs with subdivs but no content
        $xpath = new DOMXPath($doc);
        $root_elements = $xpath->query('//div[@class="simplebox"]');
        foreach ($root_elements as $root_element) {
            $elements = $xpath->query('//div[@class="simplebox-content-toolbar"]',$root_element);
            foreach ($elements as $el) {
                $el->parentNode->removeChild($el);
            }
            $element_contents = $xpath->query('//div[@class="simplebox-content"]',$root_element);
            foreach ($element_contents as $element_content) {
                if (empty(trim(html_entity_decode($element_content->textContent), " \t\n\r\0\x0B\xC2\xA0"))) {
                    $element_content->parentNode->removeChild($element_content);
                }
            }
            $element_columns = $xpath->query('//div[contains(@class, "simplebox-column")]',$root_element);
            foreach ($element_columns as $element_column) {
                if (empty(trim(html_entity_decode($element_column->textContent), " \t\n\r\0\x0B\xC2\xA0"))) {
                    $element_column->parentNode->removeChild($element_column);
                }

            }
        }
        $new_html = $styles . '<div class="sans-serif">' . $doc->saveHTML() . '</div>';
        return $new_html;

    }
}