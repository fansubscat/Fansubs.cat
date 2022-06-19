<?php
require_once('config.inc.php');

$last_month = date('Y-m', strtotime(date('Y-m').'-01 first day of -1 month'));

$types = array(
		array(
			'anime', 'Anime', 'Animes amb més visualitzacions'
		),
		array(
			'manga', 'Manga', 'Mangues amb més lectures'
		),
		array(
			'liveaction', 'Acció real', 'Contingut d\'acció real amb més visualitzacions'
		)
);

foreach ($types as $type) {
	file_get_contents("https://api.telegram.org/bot$telegram_bot_api_key/sendMessage?chat_id=$telegram_bot_chat_id&parse_mode=markdown&text=*${type[2]} durant el mes passat* (incloent hentai / sense incloure'l):");

	file_put_contents("/tmp/fansubscat_monthly_rankings_1.png", file_get_contents("https://admin.fansubs.cat/twitter_image.php?type=${type[0]}&month=$last_month&hide_hentai=0&token=$internal_token"));
	file_put_contents("/tmp/fansubscat_monthly_rankings_2.png", file_get_contents("https://admin.fansubs.cat/twitter_image.php?type=${type[0]}&month=$last_month&hide_hentai=1&token=$internal_token"));

	$post_content = [
		'chat_id' => $telegram_bot_chat_id,
		'media' => json_encode([
			['type' => 'photo', 'media' => 'attach://file_1', 'caption' => "Rànquing del mes - ${type[1]} - Amb hentai" ],
			['type' => 'photo', 'media' => 'attach://file_2', 'caption' => "Rànquing del mes - ${type[1]} - Sense hentai" ],
		]),
		'file_1' => new CURLFile(realpath("/tmp/fansubscat_monthly_rankings_1.png")),
		'file_2' => new CURLFile(realpath("/tmp/fansubscat_monthly_rankings_2.png")),
	];

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot$telegram_bot_api_key/sendMediaGroup");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
	curl_exec($curl);
	curl_close($curl);

	unlink("/tmp/fansubscat_monthly_rankings_1.png");
	unlink("/tmp/fansubscat_monthly_rankings_2.png");
}
?>
