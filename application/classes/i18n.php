<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Functionality to manage allowed languages.
 *
 */

class I18n extends Kohana_I18n {
	public static $languageCodes = array(
		"aa" => "Afar",
		"ab" => "Abkhazian",
		"ae" => "Avestan",
		"af" => "Afrikaans",
		"ak" => "Akan",
		"am" => "Amharic",
		"an" => "Aragonese",
		"ar" => "Arabic",
		"as" => "Assamese",
		"av" => "Avaric",
		"ay" => "Aymara",
		"az" => "Azerbaijani",
		"ba" => "Bashkir",
		"be" => "Belarusian",
		"bg" => "Bulgarian",
		"bh" => "Bihari",
		"bi" => "Bislama",
		"bm" => "Bambara",
		"bn" => "Bengali",
		"bo" => "Tibetan",
		"br" => "Breton",
		"bz" => "Brazillion",
		"bs" => "Bosnian",
		"ca" => "Catalan",
		"ce" => "Chechen",
		"ch" => "Chamorro",
		"co" => "Corsican",
		"cr" => "Cree",
		"cs" => "Czech",
		"cu" => "Church Slavic",
		"cv" => "Chuvash",
		"cy" => "Welsh",
		"da" => "Danish",
		"de" => "German",
		"dv" => "Divehi",
		"dz" => "Dzongkha",
		"ee" => "Ewe",
		"el" => "Greek",
		"en" => "English",
		"eo" => "Esperanto",
		"es" => "Spanish",
		"et" => "Estonian",
		"eu" => "Basque",
		"fa" => "Persian",
		"ff" => "Fulah",
		"fi" => "Finnish",
		"fj" => "Fijian",
		"fo" => "Faroese",
		"fr" => "French",
		"fy" => "Western Frisian",
		"ga" => "Irish",
		"gd" => "Scottish Gaelic",
		"gl" => "Galician",
		"gn" => "Guarani",
		"gu" => "Gujarati",
		"gv" => "Manx",
		"ha" => "Hausa",
		"he" => "Hebrew",
		"hi" => "Hindi",
		"ho" => "Hiri Motu",
		"hr" => "Croatian",
		"ht" => "Haitian",
		"hu" => "Hungarian",
		"hy" => "Armenian",
		"hz" => "Herero",
		"ia" => "Interlingua (International Auxiliary Language Association)",
		"id" => "Indonesian",
		"ie" => "Interlingue",
		"ig" => "Igbo",
		"ii" => "Sichuan Yi",
		"ik" => "Inupiaq",
		"io" => "Ido",
		"is" => "Icelandic",
		"it" => "Italian",
		"iu" => "Inuktitut",
		"ja" => "Japanese",
		"jv" => "Javanese",
		"ka" => "Georgian",
		"kg" => "Kongo",
		"ki" => "Kikuyu",
		"kj" => "Kwanyama",
		"kk" => "Kazakh",
		"kl" => "Kalaallisut",
		"km" => "Khmer",
		"kn" => "Kannada",
		"ko" => "Korean",
		"kr" => "Kanuri",
		"ks" => "Kashmiri",
		"ku" => "Kurdish",
		"kv" => "Komi",
		"kw" => "Cornish",
		"ky" => "Kirghiz",
		"la" => "Latin",
		"lb" => "Luxembourgish",
		"lg" => "Ganda",
		"li" => "Limburgish",
		"ln" => "Lingala",
		"lo" => "Lao",
		"lt" => "Lithuanian",
		"lu" => "Luba-Katanga",
		"lv" => "Latvian",
		"mg" => "Malagasy",
		"mh" => "Marshallese",
		"mi" => "Maori",
		"mk" => "Macedonian",
		"ml" => "Malayalam",
		"mn" => "Mongolian",
		"mr" => "Marathi",
		"ms" => "Malay",
		"mt" => "Maltese",
		"my" => "Burmese",
		"na" => "Nauru",
		"nb" => "Norwegian Bokmal",
		"nd" => "North Ndebele",
		"ne" => "Nepali",
		"ng" => "Ndonga",
		"nl" => "Dutch",
		"nn" => "Norwegian Nynorsk",
		"no" => "Norwegian",
		"nr" => "South Ndebele",
		"nv" => "Navajo",
		"ny" => "Chichewa",
		"oc" => "Occitan",
		"oj" => "Ojibwa",
		"om" => "Oromo",
		"or" => "Oriya",
		"os" => "Ossetian",
		"pa" => "Panjabi",
		"pi" => "Pali",
		"pl" => "Polish",
		"ps" => "Pashto",
		"pt" => "Portuguese",
		"qu" => "Quechua",
		"rm" => "Raeto-Romance",
		"rn" => "Kirundi",
		"ro" => "Romanian",
		"ru" => "Russian",
		"rw" => "Kinyarwanda",
		"sa" => "Sanskrit",
		"sc" => "Sardinian",
		"sd" => "Sindhi",
		"se" => "Northern Sami",
		"sg" => "Sango",
		"si" => "Sinhala",
		"sk" => "Slovak",
		"sl" => "Slovenian",
		"sm" => "Samoan",
		"sn" => "Shona",
		"so" => "Somali",
		"sq" => "Albanian",
		"sr" => "Serbian",
		"ss" => "Swati",
		"st" => "Southern Sotho",
		"su" => "Sundanese",
		"sv" => "Swedish",
		"sw" => "Swahili",
		"ta" => "Tamil",
		"te" => "Telugu",
		"tg" => "Tajik",
		"th" => "Thai",
		"ti" => "Tigrinya",
		"tk" => "Turkmen",
		"tl" => "Tagalog",
		"tn" => "Tswana",
		"to" => "Tonga",
		"tr" => "Turkish",
		"ts" => "Tsonga",
		"tt" => "Tatar",
		"tw" => "Twi",
		"ty" => "Tahitian",
		"ug" => "Uighur",
		"uk" => "Ukrainian",
		"ur" => "Urdu",
		"uz" => "Uzbek",
		"ve" => "Venda",
		"vi" => "Vietnamese",
		"vo" => "Volapuk",
		"wa" => "Walloon",
		"wo" => "Wolof",
		"xh" => "Xhosa",
		"yi" => "Yiddish",
		"yo" => "Yoruba",
		"za" => "Zhuang",
		"zh" => "Chinese",
		"zu" => "Zulu",
		"nu" => "Test Number"
	);

	public static function get_allowed_languages() {

		if(
			(strpos($_SERVER['REQUEST_URI'], '/admin/') !== false && Settings::instance()->get('localisation_system_active') == 1)
			||
			Settings::instance()->get('localisation_content_active') == 1
		){
			$allowed_languages = array();
			self::$languageCodes = array();
			foreach(Model_Localisation::languages_list() as $language){
				self::$languageCodes[$language['code']] = $language['title'];
				$allowed_languages[] = $language['code'];
			}
		} else {
			$allowed_languages = Kohana::$config->load('i18n.allowed_languages');
			//IbHelpers::die_r(Kohana::$config->load('i18n.allowed_languages'));
		}
		sort($allowed_languages);
		return $allowed_languages;
	}

	public static function is_multi_language() {
		if(Settings::instance()->get('localisation_system_active') == 1 && count(Model_Localisation::languages_list_codes()) > 1){
			return true;
		} else {
			return count(Kohana::$config->load('i18n.allowed_languages')) > 1;
		}
	}

	public static function get_allowed_languages_as_options($selected_id = 'en') {

		$options = '';

		$allowed_languages = self::get_allowed_languages();
		$languages = array();
		foreach($allowed_languages as $lang_code){
			$languages[$lang_code] = self::$languageCodes[$lang_code];
		}
		if(preg_match('/^5\.3\./', phpversion())){
			asort($languages);
		} else {
			asort($languages, SORT_LOCALE_STRING | SORT_NATURAL | SORT_FLAG_CASE);
		}
		foreach ($languages as $lang_code => $language)
		{
			$options .= '<option value="' . $lang_code . '"' . ($lang_code == $selected_id
					? 'selected="selected"' : '') . '>' . $language . '</option>';
		}




		return $options;
	}

	/**
	 * Store information about user selected language (to cookie)
	 */
	public static function set_default_language($lang) {
		Cookie::set('lang', $lang);
		I18n::lang($lang);
		setlocale(LC_ALL, $lang);
	}


	public static function get_default_language() {

		// Default language if client cookie is not set, is 'en'
		$lang = Cookie::get('lang', 'en');

		// Check for allowed languages
		if (!in_array($lang, self::get_allowed_languages()))
		{
			// Force the default
			$lang = 'en';
		}
		return $lang;
	}

	public static function init_user_lang() {
		if (self::is_multi_language())
		{
			I18n::lang(self::get_default_language());
		}
	}
	
	public static function get_default_language1() {
	  return 'this';
	}
	
	public static function import_from_files($lang)
	{
		unset(self::$_cache[$lang]);
		Kohana_I18n::load($lang);
		foreach(self::$_cache[$lang] as $message => $translation){
			Model_Localisation::translation_set2($lang, $message, $translation);
		}
	}
	
	public static function load($lang)
	{
		if (isset(self::$_cache[$lang])) {
			return self::$_cache[$lang];
		}

		if(Settings::instance()->get('localisation_system_active') == '1' || (Settings::instance()->get('localisation_content_active') == '1' && strpos($_SERVER['SCRIPT_URL'], '/admin') === false)){
            $table = Model_Localisation::translations_load($lang);
			if($table){
				return self::$_cache[$lang] = $table;
			} else {
				return array();
			}
		} else {
			return Kohana_I18n::load($lang);
		}
	}

    // If more than one URL format is supported for a given language...
    // e.g. http://example.com/page-name and http://example.com/en/page-name
    // ... get the "canonical" link for the current page
    public static function get_canonical_link()
    {
        $allowed_languages = i18n::get_allowed_languages();
        $default_language  = i18n::get_default_language();

        // Get the page name without the language ID
        $generic_uri  = Request::$current->uri();
        $lang = Request::$current->param('localisation_lang');
        if ($lang) {
            $generic_uri = substr($generic_uri, strlen($lang)+1);
        }

        // If there is only one language, the canonical URL is the URL with no language ID
        // e.g. http://example.com/page-name
        if (count($allowed_languages) < 2) {
            $uri = $generic_uri;
        }

        // If the default language is explicitly used or if it has been fallen back to, the canonical URL is the URL with the default language ID
        // e.g. http://example.com/en/page-name
        elseif (i18n::$lang == $default_language || !in_array(i18n::$lang, $allowed_languages)) {
            $uri = $default_language.'/'.$generic_uri;
        }

        // If a different language is used, the canonical URL is the URL with that language ID
        // e.g. http://example.com/en/page-name
        else {
            $uri = i18n::$lang.'/'.$generic_uri;
        }

        $url = URL::site().$uri.URL::query();
        return '<link rel="canonical" href="'.html::entities($url).'" />'."\n";

    }

    // Get link tags that define which pages are localised versions of the current page
    public static function get_alternate_links()
    {
        $return = '';

        if (Settings::instance()->get('localisation_content_active') == '1') {
            $localisation_languages = Model_Localisation::languages_list();

            if (count($localisation_languages) > 1) {
                // Get the page name without the language ID
                $uri  = Request::$current->uri();
                $lang = Request::$current->param('localisation_lang');
                if ($lang) {
                    $uri = substr($uri, strlen($lang)+1);
                }

                $url_site = URL::site();

                foreach ($localisation_languages as $language) {
                    $return .= '<link rel="alternate" hreflang="'.$language['code'].'" href="'.$url_site.$language['code'].'/'.$uri.'" />'."\n";
                }

            }
        }

        return $return;
    }
}


	function __($string, array $values = NULL, $lang = 'en-us')
	{
		if (@$_GET['i18n_record']) {
			$_SESSION['i18n_record'] = 1;
		}
		if (@$_SESSION['i18n_record']) {
			$trc = debug_backtrace();
			DB::insert('engine_localisation_messages_tmp')
				->values(array(
					'message' => $string,
					'file' => $trc[0]['file']
				))->execute();
		}

		if ($lang !== I18n::$lang)
		{
			// The message and target languages are different
			// Get the translation for this message
			$string = I18n::get($string);
		}

		return empty($values) ? $string : strtr($string, $values);
	}
