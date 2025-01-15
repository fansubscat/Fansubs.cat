<?php
require_once(__DIR__."/../common/config/config.inc.php");

function lang($string) {
	if (!array_key_exists($string, LANGUAGE_STRINGS)) {
		die('Missing string: '.$string);
	}
	return LANGUAGE_STRINGS[$string];
}

setlocale(LC_ALL, SITE_LOCALE);

//Use this if language does not have all strings available:
//$fallback_language = json_decode(file_get_contents(__DIR__.'/../common/languages/lang_en.json'),TRUE) or die('Cannot load English language');
//$default_language = json_decode(file_get_contents(__DIR__.'/../common/languages/lang_'.SITE_LANGUAGE.'.json'),TRUE) or die('Cannot load default language');
//$merged_language = array_merge($fallback_language, $default_language);
//define('LANGUAGE_STRINGS', $merged_language);

//Use this if language has all strings available:
//define('LANGUAGE_STRINGS', json_decode(file_get_contents(__DIR__.'/../common/languages/lang_'.SITE_LANGUAGE.'.json'),TRUE));
define('LANGUAGE_STRINGS', json_decode(file_get_contents(__DIR__.'/../common/languages/lang_'.SITE_LANGUAGE.'.json'),TRUE));

//Website URLs (no final slash)
define('ADMIN_URL', 'https://'.ADMIN_SUBDOMAIN.'.'.MAIN_DOMAIN);
?>
