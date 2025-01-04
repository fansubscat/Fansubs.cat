<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/common.inc.php');

$last_month = date('Y-m', strtotime(date('Y-m').'-01 first day of -1 month'));
$curr_month = date('Y-m');

$result = query("SELECT IFNULL(SUM(views),0) views, IFNULL(SUM(total_length),0) total_length FROM views WHERE type='anime' AND day>='$last_month-01' AND day<'$curr_month-01'");
$row = mysqli_fetch_assoc($result);
$views_anime=$row['views'];
$time_anime=$row['total_length'];

$result = query("SELECT IFNULL(SUM(views),0) views, IFNULL(SUM(total_length),0) total_length FROM views WHERE type='manga' AND day>='$last_month-01' AND day<'$curr_month-01'");
$row = mysqli_fetch_assoc($result);
$views_manga=$row['views'];
$pages_manga=$row['total_length'];

$result = query("SELECT IFNULL(SUM(views),0) views, IFNULL(SUM(total_length),0) total_length FROM views WHERE type='liveaction' AND day>='$last_month-01' AND day<'$curr_month-01'");
$row = mysqli_fetch_assoc($result);
$views_liveaction=$row['views'];
$time_liveaction=$row['total_length'];

$result = query("SELECT (SELECT COUNT(DISTINCT v.id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.created>='$last_month-01 00:00:00' AND v.created<'$curr_month-01 00:00:00' AND s.type='anime' AND (SELECT COUNT(*) FROM file fsub WHERE fsub.version_id=v.id)>0) total_anime, (SELECT COUNT(DISTINCT v.id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.created>='$last_month-01 00:00:00' AND v.created<'$curr_month-01 00:00:00' AND s.type='manga' AND (SELECT COUNT(*) FROM file fsub WHERE fsub.version_id=v.id)>0) total_manga, (SELECT COUNT(DISTINCT v.id) FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.created>='$last_month-01 00:00:00' AND v.created<'$curr_month-01 00:00:00' AND s.type='liveaction' AND (SELECT COUNT(*) FROM file fsub WHERE fsub.version_id=v.id)>0) total_liveaction, (SELECT COUNT(DISTINCT id) FROM fansub WHERE created>='$last_month-01 00:00:00' AND created<'$curr_month-01 00:00:00') total_fansubs, (SELECT COUNT(DISTINCT id) FROM user WHERE created>='$last_month-01 00:00:00' AND created<'$curr_month-01 00:00:00') new_users, (SELECT COUNT(*) FROM news WHERE date>='$last_month-01 00:00:00' AND date<'$curr_month-01 00:00:00') total_news, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.created>='$last_month-01 00:00:00' AND f.created<'$curr_month-01 00:00:00' AND s.type='anime' AND f.is_lost=0) total_files_anime, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.created>='$last_month-01 00:00:00' AND f.created<'$curr_month-01 00:00:00' AND s.type='manga' AND f.is_lost=0) total_files_manga, (SELECT COUNT(DISTINCT f.id) FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE f.created>='$last_month-01 00:00:00' AND f.created<'$curr_month-01 00:00:00' AND s.type='liveaction' AND f.is_lost=0) total_files_liveaction, (SELECT COUNT(DISTINCT user_id) FROM view_session WHERE created>='$last_month-01 00:00:00' AND created<'$curr_month-01 00:00:00') total_users, (SELECT COUNT(DISTINCT anon_id) FROM view_session WHERE created>='$last_month-01 00:00:00' AND created<'$curr_month-01 00:00:00') total_anons");
$totals = mysqli_fetch_assoc($result);

$types = array(
		array(
			'anime', 'Anime', 'Animes amb més visualitzacions', FALSE
		),
		array(
			'manga', 'Manga', 'Mangues amb més visualitzacions', FALSE
		),
		array(
			'liveaction', 'Imatge real', 'Continguts d’imatge real amb més visualitzacions', FALSE
		),
		array(
			'anime', 'Anime hentai', 'Animes hentai amb més visualitzacions', TRUE
		),
		array(
			'manga', 'Manga hentai', 'Mangues hentai amb més visualitzacions', TRUE
		)
);

$message="*Totals del mes passat:*\n\n__Anime:__\n  • Visualitzacions: $views_anime\n  • Temps total: ".get_hours_or_minutes_formatted($time_anime)."\n  • Versions noves: ${totals['total_anime']}\n  • Fitxers nous: ${totals['total_files_anime']}\n\n__Manga:__\n  • Lectures: $views_manga\n  • Pàgines totals: $pages_manga\n  • Versions noves: ${totals['total_manga']}\n  • Fitxers nous: ${totals['total_files_manga']}\n\n__Imatge real:__\n  • Visualitzacions: $views_liveaction\n  • Temps total: ".get_hours_or_minutes_formatted($time_liveaction)."\n  • Versions noves: ${totals['total_liveaction']}\n  • Fitxers nous: ${totals['total_files_liveaction']}\n\n__Altres:__\n  • Notícies noves: ${totals['total_news']}\n  • Fansubs nous: ${totals['total_fansubs']}\n  • Usuaris nous: ${totals['new_users']}\n  • Usuaris actius: ${totals['total_users']}\n  • Usuaris anònims: ${totals['total_anons']}";

file_get_contents("https://api.telegram.org/bot".TELEGRAM_CONFIG[0]['TELEGRAM_BOT_API_KEY']."/sendMessage?chat_id=".TELEGRAM_CONFIG[0]['TELEGRAM_BOT_CHAT_ID']."&parse_mode=markdownv2&text=".urlencode($message));

foreach ($types as $type) {
	file_put_contents("/tmp/fansubscat_monthly_rankings.png", file_get_contents(ADMIN_URL."/twitter_image.php?type=${type[0]}&mode=month&first_month=$last_month&last_month=$last_month&is_hentai=".($type[3] ? '1' : '0')."&token=".INTERNAL_SERVICES_TOKEN));

	$post_content = [
		'chat_id' => TELEGRAM_CONFIG[0]['TELEGRAM_BOT_CHAT_ID'],
		'media' => json_encode([
			['type' => 'photo', 'media' => 'attach://file_1', 'caption' => "*${type[2]}*", 'parse_mode' => 'MarkdownV2' ],
		]),
		'file_1' => new CURLFile(realpath("/tmp/fansubscat_monthly_rankings.png")),
	];

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot".TELEGRAM_CONFIG[0]['TELEGRAM_BOT_API_KEY']."/sendMediaGroup");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
	curl_exec($curl);
	curl_close($curl);

	unlink("/tmp/fansubscat_monthly_rankings.png");
}

mysqli_close($db_connection);
?>
