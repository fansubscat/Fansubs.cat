<?php
require_once('db.inc.php');
require_once('common.inc.php');

$last_month = date('Y-m', strtotime(date('Y-m').'-01 first day of -1 month'));
$curr_month = date('Y-m');

$result = mysqli_query($db_connection, "SELECT IFNULL(SUM(views),0) views, IFNULL(SUM(time_spent),0) time_spent FROM views WHERE type='anime' AND day>='$last_month-01' AND day<'$curr_month-01'") or die(mysqli_error($db_connection));
$row = mysqli_fetch_assoc($result);
$views_anime=$row['views'];
$time_anime=$row['time_spent'];
mysqli_free_result($result);

$result = mysqli_query($db_connection, "SELECT IFNULL(SUM(views),0) views, IFNULL(SUM(pages_read),0) pages_read FROM views WHERE type='manga' AND day>='$last_month-01' AND day<'$curr_month-01'") or die(mysqli_error($db_connection));
$row = mysqli_fetch_assoc($result);
$views_manga=$row['views'];
$pages_manga=$row['pages_read'];
mysqli_free_result($result);

$result = mysqli_query($db_connection, "SELECT IFNULL(SUM(views),0) views, IFNULL(SUM(time_spent),0) time_spent FROM views WHERE type='liveaction' AND day>='$last_month-01' AND day<'$curr_month-01'") or die(mysqli_error($db_connection));
$row = mysqli_fetch_assoc($result);
$views_liveaction=$row['views'];
$time_liveaction=$row['time_spent'];
mysqli_free_result($result);

$result = mysqli_query($db_connection, "SELECT (SELECT COUNT(DISTINCT v.id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.created>='$last_month-01 00:00:00' AND v.created<'$curr_month-01 00:00:00' AND s.type='anime' AND (SELECT COUNT(*) FROM file fsub WHERE fsub.version_id=v.id)>0) total_anime, (SELECT COUNT(DISTINCT v.id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.created>='$last_month-01 00:00:00' AND v.created<'$curr_month-01 00:00:00' AND s.type='manga' AND (SELECT COUNT(*) FROM file fsub WHERE fsub.version_id=v.id)>0) total_manga, (SELECT COUNT(DISTINCT v.id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.created>='$last_month-01 00:00:00' AND v.created<'$curr_month-01 00:00:00' AND s.type='liveaction' AND (SELECT COUNT(*) FROM file fsub WHERE fsub.version_id=v.id)>0) total_liveaction, (SELECT COUNT(DISTINCT id) FROM fansub WHERE created>='$last_month-01 00:00:00' AND created<'$curr_month-01 00:00:00') total_fansubs, (SELECT COUNT(*) FROM news WHERE date>='$last_month-01 00:00:00' AND date<'$curr_month-01 00:00:00') total_news, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.created>='$last_month-01 00:00:00' AND f.created<'$curr_month-01 00:00:00' AND s.type='anime' AND f.is_lost=0) total_files_anime, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.created>='$last_month-01 00:00:00' AND f.created<'$curr_month-01 00:00:00' AND s.type='manga' AND f.is_lost=0) total_files_manga, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.created>='$last_month-01 00:00:00' AND f.created<'$curr_month-01 00:00:00' AND s.type='liveaction' AND f.is_lost=0) total_files_liveaction, (SELECT COUNT(DISTINCT ip) FROM view_session WHERE created>='$last_month-01 00:00:00' AND created<'$curr_month-01 00:00:00') total_users") or die(mysqli_error($db_connection));
$totals = mysqli_fetch_assoc($result);
mysqli_free_result($result);

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
	file_put_contents("/tmp/fansubscat_monthly_rankings_1.png", file_get_contents("https://admin.fansubs.cat/twitter_image.php?type=${type[0]}&month=$last_month&hide_hentai=0&token=$internal_token"));
	file_put_contents("/tmp/fansubscat_monthly_rankings_2.png", file_get_contents("https://admin.fansubs.cat/twitter_image.php?type=${type[0]}&month=$last_month&hide_hentai=1&token=$internal_token"));

	$post_content = [
		'chat_id' => $telegram_bot_chat_id,
		'media' => json_encode([
			['type' => 'photo', 'media' => 'attach://file_1', 'caption' => "*${type[2]} durant el mes passat*\n\\(incloent contingut explícit / sense incloure'l\\)", 'parse_mode' => 'MarkdownV2' ],
			['type' => 'photo', 'media' => 'attach://file_2' ],
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

$message="*Totals del mes passat:*\n\n__Anime:__\n  • Visualitzacions: $views_anime\n  • Temps total: ".get_hours_or_minutes_formatted($time_anime)."\n  • Versions noves: ${totals['total_anime']}\n  • Fitxers nous: ${totals['total_files_anime']}\n\n__Manga:__\n  • Visualitzacions: $views_manga\n  • Pàgines totals: $pages_manga\n  • Versions noves: ${totals['total_manga']}\n  • Fitxers nous: ${totals['total_files_manga']}\n\n__Acció real:__\n  • Visualitzacions: $views_liveaction\n  • Temps total: ".get_hours_or_minutes_formatted($time_liveaction)."\n  • Versions noves: ${totals['total_liveaction']}\n  • Fitxers nous: ${totals['total_files_liveaction']}\n\n__Altres:__\n  • Notícies noves: ${totals['total_news']}\n  • Fansubs nous: ${totals['total_fansubs']}\n  • Usuaris únics: ${totals['total_users']}\n\n_\\(Als missatges anteriors hi ha els continguts més populars\\)_";

file_get_contents("https://api.telegram.org/bot$telegram_bot_api_key/sendMessage?chat_id=$telegram_bot_chat_id&parse_mode=markdownv2&text=".urlencode($message));

mysqli_close($db_connection);
?>
