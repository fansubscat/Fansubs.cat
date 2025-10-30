<?php
$languages = array('ca');
foreach ($languages as $language) {
	$strings = json_decode(file_get_contents(__DIR__.'/../common/languages/lang_'.$language.'.json'),TRUE);
	$javascript = "//Generated file, do not edit: change strings in lang_ca.json and then run services/rebuild_javascript_strings.php\n";
	$javascript .= 'window.LANGUAGE_STRINGS = '.json_encode(array_filter($strings, function($k){ return strpos($k, 'js.')===0 && strpos($k, 'js.admin.')!==0; }, ARRAY_FILTER_USE_KEY)).';';
	file_put_contents(__DIR__.'/../websites/static/js/lang_'.$language.'.js', $javascript);
	
	$javascript = "//Generated file, do not edit: change strings in lang_ca.json and then run services/rebuild_javascript_strings.php\n";
	$javascript .= 'Object.assign(window.LANGUAGE_STRINGS, '.json_encode(array_filter($strings, function($k){ return strpos($k, 'js.admin.')===0; }, ARRAY_FILTER_USE_KEY)).');';
	file_put_contents(__DIR__.'/../websites/static/js/admin_lang_'.$language.'.js', $javascript);
}
?>
