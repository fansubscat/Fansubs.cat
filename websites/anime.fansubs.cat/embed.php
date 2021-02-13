<?php
require_once("db.inc.php");
require_once("common.inc.php");

function get_embed_episode_name($episode_number, $episode_title, $series_name, $series_type, $series_show_episode_numbers){
	$final_episode_title='';
	
	if ($series_show_episode_numbers==1 && !empty($episode_number)) {
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
			$final_episode_title.=$series_name;
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

$result = query("SELECT l.* , IF(l.episode_id IS NULL,1,0) is_extra, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_name, s.name series_name, s.type series_type, s.show_episode_numbers series_show_episode_numbers, et.title episode_title, e.number FROM link l LEFT JOIN version ve ON l.version_id=ve.id LEFT JOIN rel_version_fansub vf ON ve.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON ve.series_id=s.id LEFT JOIN episode e ON l.episode_id=e.id LEFT JOIN episode_title et ON l.episode_id=et.episode_id AND ve.id=et.version_id WHERE l.id=$link_id AND l.url IS NOT NULL GROUP BY vf.fansub_id");
$link = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);
if (isset($failed)) {
	http_response_code(404);
	include('error.php');
	die();
}

$page_title = get_embed_episode_player_title($link['fansub_name'], $link['is_extra'] ? $link['extra_name'] : get_embed_episode_name($link['number'], $link['episode_title'], $link['series_name'], $link['series_type'], $link['series_show_episode_numbers']), $link['series_name'], $link['series_type'], $link['is_extra']);
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php ?><?php echo $page_title; ?> | Fansubs.cat - Anime en català</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css" />
		<link rel="stylesheet" href="<?php echo $base_url; ?>/style/anime.css?v=16" media="screen" />
		<link rel="stylesheet" href="https://cdn.plyr.io/3.6.4/plyr.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script src="https://cdn.plyr.io/3.6.4/plyr.js"></script>
		<script src="<?php echo $base_url; ?>/js/common.js?v=16"></script>
		<script src="<?php echo $base_url; ?>/js/megajs.js"></script>
		<script src="<?php echo $base_url; ?>/js/videostream.js"></script>
	</head>
	<body>
		<input type="hidden" id="embed-page" value="1" />
		<input type="hidden" id="data-title" value="<?php echo $page_title; ?>" />
		<input type="hidden" id="data-link-id" value="<?php echo $link['id']; ?>" />
		<input type="hidden" id="data-method" value="<?php echo htmlspecialchars(get_display_method($link['url'])); ?>" />
		<input type="hidden" id="data-url" value="<?php echo htmlspecialchars(base64_encode(get_display_url($link['url']))); ?>" />
		<div id="overlay">
			<div id="overlay-content"></div>
		</div>
	</body>
</html>
