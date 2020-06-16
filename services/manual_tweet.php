<?php
require_once('config.inc.php');
require_once("libs/simple_html_dom.php");
require_once("libs/codebird.php");
require_once('common.inc.php');
require_once("vendor/autoload.php");

global $twitter_consumer_key;
global $twitter_consumer_secret;
global $twitter_access_token;
global $twitter_access_token_secret;

\Codebird\Codebird::setConsumerKey($twitter_consumer_key, $twitter_consumer_secret);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken($twitter_access_token, $twitter_access_token_secret);

$status = "Nova notícia de @LlPnF: «[Anime] Phoenix Wright: El Gran Advocat 16-24 FINAL»";
$url = "https://llunaplenanofansub.blogspot.com/2020/06/anime-phoenix-wright-el-gran-advocat-16.html";

$params = array(
	'status' => (strlen($status)>254 ? substr($status, 0, 250)."...\"" : $status)."\n".$url
);
$cb->statuses_update($params);
?>
