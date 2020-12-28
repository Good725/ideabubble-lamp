<?php defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML
{
    public static function optionsFromArray($values, $selected, $firstOption = null)
    {
        $html = '';
        if ($firstOption) {
            $html .= '<option value="' . $firstOption['value'] . '">' . $firstOption['label'] . '</option>';
        }
        foreach ($values as $key => $value) {
            $select = false;
            if (is_array($selected)) {
                if (in_array($key, $selected)) {
                    $select = true;
                }
            } else {
                if ($key == $selected || $selected == '{all}') {
                    $select = true;
                }
            }
            $html .= '<option value="' . self::chars($key) . '" ' . ($select ? 'selected="selected"' : '') . '>' . self::entities($value) . '</option>';
        }

        return $html;
    }

    public static function optionsFromRows($keyColumn, $valueColumn, $rows, $selected = null, $firstOption = null)
    {
        $html = '';
        if ($firstOption) {
            $html .= '<option value="' . $firstOption['value'] . '">' . $firstOption['label'] . '</option>';
        }
        foreach ($rows as $row) {
            $key = is_object($row)? $row->{$keyColumn} : $row[$keyColumn];
            $value = is_object($row) ? $row->{$valueColumn} : $row[$valueColumn];
            $select = false;
            if (is_array($selected)) {
                if (in_array($key, $selected)) {
                    $select = true;
                }
            } else {
                if ($key == $selected || $selected === '{all}') {
                    $select = true;
                }
            }
            $html .= '<option value="' . self::chars($key) . '" ' . ($select ? 'selected="selected"' : '') . '>' . self::entities($value) . '</option>';
        }

        return $html;
    }

	/**
	 * Remove HTML from elements in an array. User can specify fields where whitelisted HTML is permitted.
	 *
	 * @param       $array - array of values to be sanitised
	 * @param array $lax_fields - keys for array fields, where limited HTML is allowed
	 * @param null  $allowed_tags
	 * @param null  $allowed_attributes
	 * @param null  $allowed_properties
	 */
	public static function clean_array($array, $lax_fields = array(), $allowed_tags = NULL, $allowed_attributes = NULL, $allowed_properties = NULL)
	{
		foreach ($array as $key => $value)
		{
			if (in_array($key, $lax_fields)) {
				// Strip non-whitelisted HTML
				$array[$key] = html::clean($value, $allowed_tags, $allowed_attributes, $allowed_properties);
			}
			else {
				// Strip all HTML
				IbHelpers::strip_tags_array($array[$key]);
			}
		}
		return $array;
	}

    public static function clean($html, $allowed_tags = NULL, $allowed_attributes = NULL, $allowed_properties = NULL)
    {
		if (is_null($allowed_tags))
		{
			$allowed_tags = '<a><abbr><acronym><address><area><b><bdo><big><blockquote><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><ins><kbd><label><legend><li><map><menu><ol><optgroup><option><p><pre><q><s><samp><select><small><span><strike><strong><sub><sup><table><tbody><td><textarea><tfoot><th><thead><u><tr><tt><u><ul><var>';
		}
		if (is_null($allowed_attributes))
		{
			$allowed_attributes = array('align', 'alt', 'bgColor', 'border', 'cite', 'color', 'cols', 'colspan', 'datetime', 'dir', 'height', 'hidden', 'href', 'hreflang', 'lang', 'reversed', 'rowspan', 'scope', 'sizes', 'span', 'spellcheck', 'src', 'scrset', 'start', 'style', 'summary', 'target', 'title', 'translate', 'width');
		}
		if (is_null($allowed_properties))
		{
			$allowed_properties = array(
				// colour
				'background-color',
				'color',

				// border
				'border',
				'border-bottom',
				'border-bottom-color',
				'border-bottom-left-radius',
				'border-bottom-right-radius',
				'border-bottom-style',
				'border-bottom-width',
				'border-color',
				'border-left',
				'border-left-color',
				'border-left-style',
				'border-left-width',
				'border-radius',
				'border-right',
				'border-right-color',
				'border-right-style',
				'border-right-width',
				'border-style',
				'border-top',
				'border-top-color',
				'border-top-left-radius',
				'border-top-right-radius',
				'border-top-style',
				'border-top-width',
				'border-width',
				'box-shadow',
				'outline',
				'outline-color',
				'outline-offset',
				'outline-style',
				'outline-width',

				// box
				'clear',
				'float',
				'height',
				'margin',
				'margin-bottom',
				'margin-left',
				'margin-right',
				'margin-top',
				'padding',
				'padding-bottom',
				'padding-left',
				'padding-right',
				'padding-top',
				'width',
				'vertical-align',

				// text
				'hanging-punctuation',
				'hyphens',
				'letter-spacing',
				'line-break',
				'line-height',
				'overflow-wrap',
				'tab-size',
				'text-align',
				'text-align-last',
				'text-indent',
				'text-justify',
				'text-overflow',
				'text-transform',
				'white-space',
				'word-break',
				'word-spacing',
				'word-wrap',

				// font
				'font',
				'font-family',
				'font-kerning',
				'font-language-override',
				'font-size',
				'font-size-adjust',
				'font-stretch',
				'font-style',
				'font-synthesis',
				'font-variant',
				'font-variant-caps',
				'font-variant-east-asian',
				'font-variant-ligatures',
				'font-variant-numeric',
				'font-variant-position',

				// text effects
				'font-weight',
				'text-decoration',
				'text-decoration-color',
				'text-decoration-line',
				'text-decoration-style',
				'text-shadow',
				'text-underline-position',

				// writing mode
				'direction',
				'text-orientation',
				'text-combine-upright',
				'unicode-bidi',
				'writing-mode',

				// table
				'border-collapse',
				'border-spacing',
				'caption-side',
				'empty-cells',
				'table-layout',

				// lists
				'counter-increment',
				'counter-reset',
				'list-style',
				'list-style-position',
				'list-style-type',

				// speech
				'mark',
				'mark-after',
				'mark-before',
				'phonemes',
				'rest',
				'rest-after',
				'rest-before',
				'voice-balance',
				'voice-duration',
				'voice-pitch',
				'voice-pitch-range',
				'voice-rate',
				'voice-stress',
				'voice-volume',
			);
		}
        $html = trim($html);
        if ($html == '') {
            return '';
        } else {
            /* Remove non-whitelisted tags */
            $html = strip_tags($html, $allowed_tags);

            /* Remove non-whitelisted attributes */
            // Load HTML as DOMDocument object
            $dom = new DOMDocument;
            libxml_use_internal_errors(true); // do not generate errors for not wellformed htmls
            $dom->strictErrorChecking = false;
            $dom->formatOutput = true;
            $dom->loadHTML($html);
            // Find and loop through all elements
            $xpath = new DOMXPath($dom);
            $nodes = $xpath->query('//*');
            foreach ($nodes as $node) {
                // Loop through each attribute
                $attribute_count = isset($node->attributes) ? $node->attributes->length : 0;
                for ($i = $attribute_count - 1; $i >= 0; --$i) {
                    // If the attribute is not in the whitelist, remove it
                    $attribute = $node->attributes->item($i);
                    if ($attribute != NULL && !in_array($attribute->name, $allowed_attributes)) {
                        $node->removeAttribute($attribute->name);
                    }

					// Remove non-whitelisted CSS from the style attribute
					if ($allowed_properties AND $style = $node->getAttribute('style'))
					{
						preg_match_all("/([\w-]+)\s*:\s*([^;]+)\s*;?/", $style, $matches, PREG_SET_ORDER);
						foreach ($matches as $match)
						{
							if ( ! in_array(strtolower($match[1]), $allowed_properties))
							{
								$node->setAttribute('style', preg_replace('/^(.*)'.$match[1].'(\s*):(\s*)'.$match[2].'(\s*);(.*)$/i', "$1$5", $style));
							}

                         }

					}
                }
            }


            $cleaned = $dom->saveHTML();
            if (preg_match('#<body.*?>(.*?)</body>#is', $cleaned, $body)) {
                $cleaned = $body[1];
            }
            return $cleaned;
        }
    }

	public static function toggle_button($name, $label, $off_label, $checked = TRUE, $on_value = 1, $off_value = 0)
	{
		return View::factory('snippets/toggle_button')
			->set(array(
				'name'      => $name,
				'label'     => $label,
				'off_label' => $off_label,
				'checked'   => $checked,
				'on_value'  => $on_value,
				'off_value' => $off_value
			));
	}
}