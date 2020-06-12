<?php
const REGEXP_MEGA='/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_GOOGLE_DRIVE='/https:\/\/drive\.google\.com\/(?:file\/d\/|open\?id=)?([^\/]*)(?:preview|view)?/';
const REGEXP_YOUTUBE='/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})?/';

function get_status($id){
	switch ($id){
		case 1:
			return "completed";
		case 2:
			return "in-progress";
		case 3:
			return "partially-completed";
		case 4:
			return "abandoned";
		case 5:
			return "cancelled";
		default:
			return "unknown";
	}
}

function get_status_description($id){
	switch ($id){
		case 1:
			return "Completada";
		case 2:
			return "En procés: No hi ha tots els capítols disponibles";
		case 3:
			return "Parcialment completada: Almenys una part de l'obra està completada";
		case 4:
			return "Abandonada: No hi ha tots els capítols disponibles";
		case 5:
			return "Cancel·lada: No hi ha tots els capítols disponibles";
		default:
			return "Estat desconegut";
	}
}

function get_fansub_preposition_name($text){
	$first = substr($text, 0, 1);
	if (($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U') && substr($text, 0, 4)!='One '){ //Ugly...
		return "d'$text";
	}
	return "de $text";
}

function get_rating($text){
	switch ($text){
		case 'TP':
			return "Tots els públics";
		case '+7':
			return "Majors de 7 anys";
		case '+13':
			return "Majors de 13 anys";
		case '+16':
			return "Majors de 16 anys";
		case '+18':
			return "Majors de 18 anys";
		case 'XXX':
			return "Majors de 18 anys (contingut pornogràfic)";
		default:
			return $text;
	}
}

function get_provider($url){
	if (preg_match(REGEXP_MEGA,$url)){
		return "MEGA";
	}
	if (preg_match(REGEXP_GOOGLE_DRIVE,$url)){
		return "Google Drive";
	}
	if (preg_match(REGEXP_YOUTUBE,$url)){
		return "YouTube";
	}
	return "Vídeo incrustat";
}

function get_resolution_short($resolution){
	if ($resolution=='2160p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=2000) {
		return "4K";
	} else if ($resolution=='1080p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=1000) {
		return "HD";
	} else if ($resolution=='720p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=700) {
		return "HD";
	} else {
		return "SD";
	}
}

function get_resolution_css($resolution){
	if ($resolution=='2160p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=2000) {
		return "4k";
	} else if ($resolution=='1080p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=1000) {
		return "hd1080";
	} else if ($resolution=='720p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=700) {
		return "hd720";
	} else {
		return "sd";
	}
}

function get_display_method($url){
	if (preg_match(REGEXP_MEGA,$url)){
		return "embed";
	}
	if (preg_match(REGEXP_GOOGLE_DRIVE,$url)){
		return "embed";
	}
	if (preg_match(REGEXP_YOUTUBE,$url)){
		return "embed";
	}
	return "direct-video";
}

function get_display_url($url){
	$matches = array();
	if (preg_match(REGEXP_MEGA,$url,$matches)){
		return "https://mega.nz/embed/".$matches[1]."#".$matches[2];
	}
	if (preg_match(REGEXP_GOOGLE_DRIVE,$url,$matches)){
		return "https://drive.google.com/file/d/".$matches[1]."/preview";
	}
	if (preg_match(REGEXP_YOUTUBE,$url,$matches)){
		return "https://www.youtube.com/embed/".$matches[1]."?autoplay=1";
	}
	return $url;
}

function get_hours_or_minutes_formatted($time){
	if ($time>=3600) {
		$hours = floor($time/3600);
		$time = $time-$hours*3600;
		echo $hours." h ".round($time/60)." min";
	} else {
		echo round($time/60)." min";
	}
}

function print_episode($row,$version_id,$series){
	$result = query("SELECT l.* FROM link l WHERE l.episode_id=".$row['id']." AND l.version_id=$version_id ORDER BY l.resolution ASC, l.id ASC");

	if (mysqli_num_rows($result)==0 && $series['show_unavailable_episodes']!=1){
		return;
	}

	$episode_title='';
	
	if ($series['show_episode_numbers']==1 && !empty($row['number'])) {
		if (!empty($row['title'])){
			$episode_title.='Capítol '.$row['number'].': '.htmlspecialchars($row['title']);
		}
		else {
			$episode_title.='Capítol '.$row['number'];
		}
	} else {
		if (!empty($row['title'])){
			$episode_title.=htmlspecialchars($row['title']);
		} else if ($series['type']=='movie') {
			$episode_title.=$series['name'];
		} else {
			$episode_title.='Capítol sense nom';
		}
	}

	internal_print_episode($episode_title, $result);
	mysqli_free_result($result);
}

function print_extra($row,$version_id){
	$result = query("SELECT l.* FROM link l WHERE l.episode_id IS NULL AND l.extra_name='".escape($row['extra_name'])."' AND l.version_id=$version_id ORDER BY l.resolution ASC, l.id ASC");

	$episode_title=htmlspecialchars($row['extra_name']);
	
	internal_print_episode($episode_title, $result);
	mysqli_free_result($result);
}

function internal_print_episode($episode_title, $result) {
	if (mysqli_num_rows($result)==0){
		echo "\t\t\t\t\t\t\t\t\t".'<div class="episode episode-unavailable">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-times-circle icon-play"></span>'.$episode_title."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-unavailable" title="Aquest capítol no està disponible">No disponible</span>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
		echo "\t\t\t\t\t\t\t\t\t</div>\n";
	} else if (mysqli_num_rows($result)>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="episode">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title"><span class="fa fa-fw fa-list-ul icon-play"></span>'.$episode_title."</div>\n";

		while ($vrow = mysqli_fetch_assoc($result)){
			if (!empty($vrow['url'])) {
				echo "\t\t\t\t\t\t\t\t\t\t".'<div class="version">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-fw fa-play icon-play"></span>'.(!empty($vrow['comments']) ? htmlspecialchars($vrow['comments']) : 'Reprodueix').'</a> '."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="nowrap video-info">'."\n";
				$extra_info="Resolució del vídeo: ".$vrow['resolution']."\nTipus de streaming: ".get_provider($vrow['url']);
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($vrow['resolution']).' tooltip-container">'.htmlspecialchars(get_resolution_short($vrow['resolution'])).'<span class="tooltip hidden">'.str_replace("\n", "<br />", htmlspecialchars($extra_info)).'</span></span>'."\n";
				if (in_array($vrow['id'], get_cookie_viewed_links_ids())) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-link-id="'.$vrow['id'].'" title="Ja l\'has vist: prem per a marcar-lo com a no vist"><span class="fa fa-fw fa-eye"></span></span>'."\n";
				} else {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-link-id="'.$vrow['id'].'" title="Encara no l\'has vist: prem per a marcar-lo com a vist"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t</span>\n";
				echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
			} else { //Empty link -> lost link
				echo "\t\t\t\t\t\t\t\t\t\t".'<div class="version episode-unavailable">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-times-circle icon-play"></span>Reprodueix'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost" title="Aquest capítol està subtitulat, però no està disponible enlloc. Si ens pots ajudar a trobar-lo, prem aquí i envia\'ns un comentari!">Capítol perdut: ajuda\'ns!</span>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
			}
		}

		echo "\t\t\t\t\t\t\t\t\t</div>\n";
	} else { //Only one link
		$vrow = mysqli_fetch_assoc($result);

		if (!empty($vrow['url'])) {
			echo "\t\t\t\t\t\t\t\t\t".'<div class="episode">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-fw fa-play icon-play"></span>'.$episode_title.'</a> '."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="nowrap video-info">'."\n";
			$extra_info="Resolució del vídeo: ".$vrow['resolution']."\nTipus de streaming: ".get_provider($vrow['url']);
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($vrow['resolution']).' tooltip-container">'.htmlspecialchars(get_resolution_short($vrow['resolution'])).'<span class="tooltip hidden">'.str_replace("\n", "<br />", htmlspecialchars($extra_info)).'</span></span>'."\n";
			if (!empty($vrow['comments'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-info tooltip-container"><span class="fa fa-fw fa-info-circle"></span><span class="tooltip hidden">'.str_replace("\n", "<br />", htmlspecialchars($vrow['comments'])).'</span></span>'."\n";
			}
			if (in_array($vrow['id'], get_cookie_viewed_links_ids())) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-link-id="'.$vrow['id'].'" title="Ja l\'has vist: prem per a marcar-lo com a no vist"><span class="fa fa-fw fa-eye"></span></span>'."\n";
			} else {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-link-id="'.$vrow['id'].'" title="Encara no l\'has vist: prem per a marcar-lo com a vist"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t</span>\n";
			echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t</div>\n";
		} else { //Empty link -> lost link
			echo "\t\t\t\t\t\t\t\t\t".'<div class="episode episode-unavailable">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-times-circle icon-play"></span>'.$episode_title."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost" title="Aquest capítol està subtitulat, però no està disponible enlloc. Si ens pots ajudar a trobar-lo, prem aquí i envia\'ns un comentari!">Capítol perdut: ajuda\'ns!</span>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t</div>\n";
		}
	}
}

function get_cookie_fansub_ids() {
	$fansub_ids = array();
	if (!empty($_COOKIE['hidden_fansubs'])) {
		$exploded = explode(',',$_COOKIE['hidden_fansubs']);
		foreach ($exploded as $id) {
			if (is_numeric($id)) {
				array_push($fansub_ids, $id);
			}
		}
	}
	return $fansub_ids;
}

function get_cookie_viewed_links_ids() {
	$link_ids = array();
	if (!empty($_COOKIE['viewed_links'])) {
		$exploded = explode(',',$_COOKIE['viewed_links']);
		foreach ($exploded as $id) {
			if (is_numeric($id)) {
				array_push($link_ids, $id);
			}
		}
	}
	return $link_ids;
}

function get_tadaima_info($thread_id) {
	global $memcached, $memcached_expiry_time;

	$response = $memcached->get("tadaima_post_$thread_id");
	if ($response==FALSE) {
		$ch = curl_init("https://tadaima.cat/api/get_topic_detail/$thread_id");

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				'User-Agent: Fansubscat/Anime/1.0.0',
				'X-Tadaima-App-Id: TadaimaApp',
				'X-Tadaima-Api-Version: 1'
			)
		);

		$response = curl_exec($ch);
		if($response!==FALSE) {
			$memcached->set("tadaima_post_$thread_id", $response, $memcached_expiry_time);
		}
		curl_close($ch);
	}
	if($response===FALSE) {
		return "Tadaima.cat";
	} else {
		$json_response = json_decode($response);
		if ($json_response->status!='ok') {
			return "Tadaima.cat";
		} else {
			$number_of_posts = count($json_response->result->posts);
			if ($number_of_posts==1){
				return "Tadaima.cat (1 comentari)";
			} else {
				return "Tadaima.cat ($number_of_posts comentaris)";
			}
		}
	}
}
?>
