<?php
require_once("db.inc.php");
require_once("common.inc.php");

function get_embed_episode_name($episode_number, $episode_title, $series_name, $series_type, $show_episode_numbers){
	$final_episode_title='';
	
	if ($show_episode_numbers==1 && !empty($episode_number)) {
		if (!empty($episode_title)){
			$final_episode_title.='Capítol '.str_replace('.',',',floatval($episode_number)).': '.htmlspecialchars($episode_title);
		}
		else {
			$final_episode_title.='Capítol '.str_replace('.',',',floatval($episode_number));
		}
	} else {
		if (!empty($episode_title)){
			$final_episode_title.=htmlspecialchars($episode_title);
		} else if ($series_type=='movie') {
			$final_episode_title.=htmlspecialchars($series_name);
		} else {
			$final_episode_title.='Capítol sense nom';
		}
	}
	return $final_episode_title;
}

function get_embed_episode_player_title($fansub_name, $episode_title, $series_name, $series_type, $is_extra){
	if ($series_name==$episode_title || ($series_type=='movie' && !$is_extra)){
		if (!empty($episode_title)) {
			return $fansub_name . ' - ' . $episode_title;
		} else {
			return $fansub_name . ' - ' . $series_name;
		}
	} else {
		return $fansub_name . ' - ' . $series_name . ' - '. $episode_title;
	}
}

$link_id = (!empty($_GET['link_id']) ? intval($_GET['link_id']) : 0);

$result = query("SELECT l.* , IF(l.episode_id IS NULL,1,0) is_extra, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_name, s.name series_name, s.type series_type, ve.show_episode_numbers, et.title episode_title, e.number, s.id series_id FROM link l LEFT JOIN version ve ON l.version_id=ve.id LEFT JOIN rel_version_fansub vf ON ve.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON ve.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN episode_title et ON l.episode_id=et.episode_id AND ve.id=et.version_id WHERE l.id=$link_id AND l.lost=0 GROUP BY vf.fansub_id");
$link = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

$page_title = get_embed_episode_player_title($link['fansub_name'], $link['is_extra'] ? $link['extra_name'] : get_embed_episode_name($link['number'], $link['episode_title'], $link['series_name'], $link['series_type'], $link['show_episode_numbers']), $link['series_name'], $link['series_type'], $link['is_extra']);

$link_instances = array();
$resulti = query("SELECT li.* FROM link_instance li WHERE li.link_id=${link['id']} ORDER BY li.url ASC");
while ($lirow = mysqli_fetch_assoc($resulti)){
	array_push($link_instances, $lirow);
}
mysqli_free_result($resulti);
$link_instances = filter_link_instances($link_instances);
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php ?><?php echo $page_title; ?> | Fansubs.cat - Anime en català</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" />
		<link rel="stylesheet" href="/js/videojs/video-js.css?v=<?php echo PL_VER; ?>" />
		<link rel="stylesheet" href="/js/videojs/videojs-chromecast.css?v=<?php echo PL_VER; ?>" />
		<link rel="stylesheet" href="/style/anime.css?v=<?php echo CS_VER; ?>" />
		<script>
			window.SILVERMINE_VIDEOJS_CHROMECAST_CONFIG = {
				preloadWebComponents: true,
			};
		</script>
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-628107-14"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script src="/js/videojs/video.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/common.js?v=<?php echo JS_VER; ?>"></script>
		<script src="/js/megajs.js?v=<?php echo MG_VER; ?>"></script>
		<script src="/js/videostream.js?v=<?php echo VS_VER; ?>"></script>
		<script src="/js/videojs/lang_ca.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-chromecast.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-youtube.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-landscape-fullscreen.min.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-hotkeys.js?v=<?php echo PL_VER; ?>"></script>
		<script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
	</head>
	<body>
		<input type="hidden" id="embed-page" value="1" />
		<input type="hidden" id="data-title" value="<?php echo $page_title; ?>" />
		<input type="hidden" id="data-link-id" value="<?php echo $link['id']; ?>" />
		<input type="hidden" id="data-method" value="<?php echo htmlspecialchars(get_display_method($link_instances)); ?>" />
		<input type="hidden" id="data-sources" value="<?php echo htmlspecialchars(base64_encode(get_video_sources($link_instances))); ?>" />
		<input type="hidden" id="data-series" value="<?php echo $link['series_name']; ?>" />
		<input type="hidden" id="data-episode-title" value="<?php echo $link['is_extra'] ? htmlspecialchars($link['extra_name']) : get_embed_episode_name($link['number'], $link['episode_title'], $link['series_name'], $link['series_type'], $link['show_episode_numbers']); ?>" />
		<input type="hidden" id="data-cover" value="<?php echo 'https://anime.fansubs.cat/images/series/'.$link['series_id'].'.jpg'; ?>" />
		<input type="hidden" id="data-fansub" value="<?php echo htmlspecialchars($link['fansub_name']); ?>" />
		<div id="overlay">
			<div id="overlay-content"></div>
		</div>
		<div data-nosnippet id="alert-overlay" class="hidden flex">
			<div id="alert-overlay-content">
				<h2 class="section-title" id="alert-title">S'ha produït un error</h2>
				<div id="alert-message">S'ha produït un error desconegut.</div>
				<div id="alert-buttonbar">
					<button id="alert-ok-button">D'acord</button>
				</div>
			</div>
		</div>
	</body>
</html>
