<?php
require_once('config.inc.php');
require_once("libs/simple_html_dom.php");
require_once("libs/codebird.php");
require_once('common.inc.php');
require_once("vendor/autoload.php");

\Codebird\Codebird::setConsumerKey($twitter_consumer_key, $twitter_consumer_secret);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken($twitter_access_token, $twitter_access_token_secret);

$status = "Hem afegit 4 capítols nous de «Seiren» (Ippantekina jimaku) al web d'anime.fansubs.cat!\nhttps://anime.fansubs.cat/series/seiren?version=405";

$params = array(
	'status' => $status
);
$cb->statuses_update($params);
?>
