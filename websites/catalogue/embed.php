<?php
require_once("db.inc.php");
require_once("common.inc.php");

function get_embed_episode_name($episode_number, $episode_title, $series_name, $series_type, $show_episode_numbers){
	global $config;
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
		} else if ($series_type==$config['filmsoneshots_db']) {
			$final_episode_title.=htmlspecialchars($series_name);
		} else {
			$final_episode_title.='Capítol sense nom';
		}
	}
	return $final_episode_title;
}

function get_embed_episode_player_title($fansub_name, $episode_title, $series_name, $series_type, $is_extra){
	global $config;
	if ($series_name==$episode_title || ($series_type==$config['filmsoneshots_db'] && !$is_extra)){
		if (!empty($episode_title)) {
			return $fansub_name . ' - ' . $episode_title;
		} else {
			return $fansub_name . ' - ' . $series_name;
		}
	} else {
		return $fansub_name . ' - ' . $series_name . ' - '. $episode_title;
	}
}

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);

$result = query("SELECT fi.* , IF(fi.episode_id IS NULL,1,0) is_extra, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_name, s.name series_name, s.subtype series_type, ve.show_episode_numbers, et.title episode_title, e.number, s.id series_id FROM file fi LEFT JOIN version ve ON fi.version_id=ve.id LEFT JOIN rel_version_fansub vf ON ve.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON ve.series_id=s.id LEFT JOIN episode e ON fi.episode_id=e.id LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND ve.id=et.version_id WHERE s.type='${config['items_type']}' AND fi.id=$file_id AND fi.is_lost=0 GROUP BY vf.fansub_id");
$file = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

$page_title = get_embed_episode_player_title($file['fansub_name'], $file['is_extra'] ? $file['extra_name'] : get_embed_episode_name($file['number'], $file['episode_title'], $file['series_name'], $file['series_type'], $file['show_episode_numbers']), $file['series_name'], $file['series_type'], $file['is_extra']);

$links = array();
$resulti = query("SELECT l.* FROM link l WHERE l.file_id=${file['id']} ORDER BY l.url ASC");
while ($lirow = mysqli_fetch_assoc($resulti)){
	array_push($links, $lirow);
}
mysqli_free_result($resulti);
$links = filter_links($links);
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php ?><?php echo $page_title; ?> | <?php echo $config['site_title']; ?></title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" />
		<link rel="stylesheet" href="/js/videojs/video-js.min.css?v=<?php echo PL_VER; ?>" />
		<link rel="stylesheet" href="/js/videojs/videojs-chromecast.css?v=<?php echo PL_VER; ?>" />
		<link rel="stylesheet" href="/style/main.css?v=<?php echo CS_VER; ?>" />
		<script>
			window.SILVERMINE_VIDEOJS_CHROMECAST_CONFIG = {
				preloadWebComponents: true,
			};
		</script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
		<script src="https://unpkg.com/megajs@1.0.4/dist/main.browser-umd.js"></script>
		<script src="/js/videojs/video.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/common.js?v=<?php echo JS_VER; ?>"></script>
		<script src="/js/megajs.js?v=<?php echo MG_VER; ?>"></script>
		<script src="/js/videostream.js?v=<?php echo VS_VER; ?>"></script>
		<script src="/js/videojs/lang_ca.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-chromecast.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-youtube.min.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-landscape-fullscreen.min.js?v=<?php echo PL_VER; ?>"></script>
		<script src="/js/videojs/videojs-hotkeys.min.js?v=<?php echo PL_VER; ?>"></script>
		<script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
	</head>
	<body>
		<input type="hidden" id="embed-page" value="1" />
		<input type="hidden" id="data-title" value="<?php echo $page_title; ?>" />
		<input type="hidden" id="data-file-id" value="<?php echo $file['id']; ?>" />
		<input type="hidden" id="data-method" value="<?php echo htmlspecialchars(get_display_method($links)); ?>" />
		<input type="hidden" id="data-sources" value="<?php echo htmlspecialchars(base64_encode(get_video_sources($links))); ?>" />
		<input type="hidden" id="data-series" value="<?php echo $file['series_name']; ?>" />
		<input type="hidden" id="data-episode-title" value="<?php echo $file['is_extra'] ? htmlspecialchars($file['extra_name']) : get_embed_episode_name($file['number'], $file['episode_title'], $file['series_name'], $file['series_type'], $file['show_episode_numbers']); ?>" />
		<input type="hidden" id="data-cover" value="<?php echo $static_url.'/images/covers/'.$file['series_id'].'.jpg'; ?>" />
		<input type="hidden" id="data-fansub" value="<?php echo htmlspecialchars($file['fansub_name']); ?>" />
		<input type="hidden" id="data-item-type" value="<?php echo $config['items_type']; ?>" />
		<div id="overlay">
			<a id="overlay-close" style="display: none;"><span class="fa fa-times"></span></a>
			<div id="overlay-content"></div>
		</div>
		<div data-nosnippet id="alert-overlay" class="hidden flex">
			<div id="alert-overlay-content">
				<h2 class="section-title" id="alert-title">S'ha produït un error</h2>
				<div id="alert-message">S'ha produït un error desconegut.</div>
				<div id="alert-buttonbar">
					<button id="alert-refresh-button" class="hidden">Actualitza</button>
					<button id="alert-ok-button">D'acord</button>
				</div>
			</div>
		</div>
	</body>
</html>
