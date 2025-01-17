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
			'anime', lang('service.rankings.anime.title'), lang('service.rankings.anime.description'), FALSE
		),
		array(
			'manga', lang('service.rankings.manga.title'), lang('service.rankings.manga.description'), FALSE
		),
		array(
			'liveaction', lang('service.rankings.liveaction.title'), lang('service.rankings.liveaction.description'), FALSE
		),
		array(
			'anime', lang('service.rankings.anime.title.hentai'), lang('service.rankings.anime.description.hentai'), TRUE
		),
		array(
			'manga', lang('service.rankings.manga.title.hentai'), lang('service.rankings.manga.description.hentai'), TRUE
		)
);

$message="*".lang('service.rankings.totals_for_last_month')."*\n\n__".lang('service.rankings.anime')."__\n  • ".lang('service.rankings.views')."$views_anime\n  • ".lang('service.rankings.total_time').get_hours_or_minutes_formatted($time_anime)."\n  • ".lang('service.rankings.new_versions')."${totals['total_anime']}\n  • ".lang('service.rankings.new_files')."${totals['total_files_anime']}\n\n__".lang('service.rankings.manga')."__\n  • ".lang('service.rankings.reads')."$views_manga\n  • ".lang('service.rankings.total_pages')."$pages_manga\n  • ".lang('service.rankings.new_versions')."${totals['total_manga']}\n  • ".lang('service.rankings.new_files')."${totals['total_files_manga']}\n\n__".lang('service.rankings.liveaction')."__\n  • ".lang('service.rankings.views')."$views_liveaction\n  • ".lang('service.rankings.total_time').get_hours_or_minutes_formatted($time_liveaction)."\n  • ".lang('service.rankings.new_versions')."${totals['total_liveaction']}\n  • ".lang('service.rankings.new_files')."${totals['total_files_liveaction']}\n\n__".lang('service.rankings.others')."__\n  • ".lang('service.rankings.new_news')."${totals['total_news']}\n  • ".lang('service.rankings.new_fansubs')."${totals['total_fansubs']}\n  • ".lang('service.rankings.new_users')."${totals['new_users']}\n  • ".lang('service.rankings.active_users')."${totals['total_users']}\n  • ".lang('service.rankings.anon_users')."${totals['total_anons']}";

file_get_contents("https://api.telegram.org/bot".TELEGRAM_CONFIG[0]['TELEGRAM_BOT_API_KEY']."/sendMessage?chat_id=".TELEGRAM_CONFIG[0]['TELEGRAM_BOT_CHAT_ID']."&parse_mode=markdownv2&text=".urlencode($message));

foreach ($types as $type) {
	file_put_contents("/srv/fansubscat/temporary/monthly_rankings.png", file_get_contents(ADMIN_URL."/twitter_image.php?type=${type[0]}&mode=month&first_month=$last_month&last_month=$last_month&is_hentai=".($type[3] ? '1' : '0')."&token=".INTERNAL_SERVICES_TOKEN));

	$post_content = [
		'chat_id' => TELEGRAM_CONFIG[0]['TELEGRAM_BOT_CHAT_ID'],
		'media' => json_encode([
			['type' => 'photo', 'media' => 'attach://file_1', 'caption' => "*${type[2]}*", 'parse_mode' => 'MarkdownV2' ],
		]),
		'file_1' => new CURLFile(realpath("/srv/fansubscat/temporary/monthly_rankings.png")),
	];

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://api.telegram.org/bot".TELEGRAM_CONFIG[0]['TELEGRAM_BOT_API_KEY']."/sendMediaGroup");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
	curl_exec($curl);
	curl_close($curl);

	unlink("/srv/fansubscat/temporary/monthly_rankings.png");
}

mysqli_close($db_connection);
?>
