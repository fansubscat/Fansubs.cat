<?php
$languages = array('ca');
foreach ($languages as $language) {
	$strings = json_decode(file_get_contents(__DIR__.'/../common/languages/lang_'.$language.'.json'),TRUE);
	$javascript = "//Generated file, do not edit: change strings in lang_ca.json and then run tools/rebuild_javascript_strings.php\n";
	$javascript .= 'window.LANGUAGE_STRINGS = '.json_encode(array_filter($strings, function($k){ return strpos($k, 'js.')===0; }, ARRAY_FILTER_USE_KEY)).';';
	file_put_contents(__DIR__.'/../websites/static/js/lang_'.$language.'.js', $javascript);
}
?>
