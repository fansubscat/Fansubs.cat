<?php
require_once('config.inc.php');
require_once("vendor/autoload.php");

use Abraham\TwitterOAuth\TwitterOAuth;

$tweet = "Hem afegit 4 capítols nous de «Seiren» (Ippantekina jimaku) al web d'anime.fansubs.cat!\nhttps://anime.fansubs.cat/seiren";

$connection = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret, $twitter_access_token, $twitter_access_token_secret);
$connection->setApiVersion('2');
$content = $connection->post("tweets", ["text" => $tweet], TRUE);
?>
