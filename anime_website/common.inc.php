<?php
const REGEXP_MEGA='/https:\/\/mega\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_GOOGLE_DRIVE='/https:\/\/drive\.google\.com\/(?:file\/d\/|open\?id=)?([^\/]*)(?:preview|view)?/';
const REGEXP_YOUTUBE='/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})?/';

function get_status($id){
	switch ($id){
		case 1:
			return "completed";
		case 2:
			return "in-progress";
		case 3:
			return "abandoned";
		case 4:
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
			return "Abandonada: No hi ha tots els capítols disponibles";
		case 4:
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

function get_provider_short($url){
	if (preg_match(REGEXP_MEGA,$url)){
		return "M";
	}
	if (preg_match(REGEXP_GOOGLE_DRIVE,$url)){
		return "G";
	}
	if (preg_match(REGEXP_YOUTUBE,$url)){
		return "Y";
	}
	return "V";
}

function get_resolution_short($resolution){
	if ($resolution=='2160p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=2000) {
		return "UHD";
	} else if ($resolution=='1080p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=1000) {
		return "FHD";
	} else if ($resolution=='720p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=700) {
		return "HD";
	} else {
		return "SD";
	}
}

function get_resolution_css($resolution){
	if ($resolution=='720p' || $resolution=='1080p' || $resolution=='2160p' || count(explode('x',$resolution))>1 && intval(explode('x',$resolution)[1])>=700) {
		return "hd";
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

function print_episode($row,$version_id,$series){
	$result = query("SELECT l.* FROM link l WHERE l.episode_id=".$row['id']." AND l.version_id=$version_id ORDER BY l.resolution ASC, l.id ASC");

	$episode_title='';
	
	if (!empty($row['number'])) {
		if (!empty($row['title'])){
			if ($series['episodes']==1){
				$episode_title.=htmlspecialchars($row['title']);
			} else {
				$episode_title.='Capítol '.$row['number'].': '.htmlspecialchars($row['title']);
			}
		}
		else {
			if ($series['episodes']==1){
				$episode_title.=htmlspecialchars($series['name']);
			} else {
				$episode_title.='Capítol '.$row['number'];
			}
		}
	} else {
		if (!empty($row['title'])){
			$episode_title.=htmlspecialchars($row['title']);
		}
		else {
			$episode_title.=$row['name'];
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
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title"><span class="fa fa-fw fa-times-circle icon-play"></span>'.$episode_title.' <span class="version-unavailable" title="Aquest capítol no està disponible">No disponible</span></div>'."\n";
		echo "\t\t\t\t\t\t\t\t\t</div>\n";
	} else if (mysqli_num_rows($result)>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="episode">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'.$episode_title."</div>\n";

		while ($vrow = mysqli_fetch_assoc($result)){
			echo "\t\t\t\t\t\t\t\t\t\t".'<div class="version">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-fw fa-play icon-play"></span>Reprodueix</a> '."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="nowrap">'."\n";
			if (in_array($vrow['id'], get_cookie_viewed_links_ids())) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-link-id="'.$vrow['id'].'" title="Ja l\'has vist: prem per a marcar-lo com a no vist"><span class="fa fa-fw fa-eye"></span></span>'."\n";
			} else {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-link-id="'.$vrow['id'].'" title="Encara no l\'has vist: prem per a marcar-lo com a vist"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-method" title="Plataforma en què s\'allotja el vídeo: '.htmlspecialchars(get_provider($vrow['url'])).'">'.htmlspecialchars(get_provider_short($vrow['url'])).'</span>'."\n";
			if (!empty($vrow['resolution'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($vrow['resolution']).'" title="Resolució del vídeo: '.htmlspecialchars($vrow['resolution']).'">'.htmlspecialchars(get_resolution_short($vrow['resolution'])).'</span>'."\n";
			}
			if (!empty($vrow['comments'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-comments" title="Notes addicionals de la versió">'.htmlspecialchars($vrow['comments']).'</span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t</span>\n";
			echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
		}

		echo "\t\t\t\t\t\t\t\t\t</div>\n";
	} else { //Only one link
		echo "\t\t\t\t\t\t\t\t\t".'<div class="episode">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
		
		$vrow = mysqli_fetch_assoc($result);

		echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-fw fa-play icon-play"></span>'.$episode_title.'</a> '."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="nowrap">'."\n";
		if (in_array($vrow['id'], get_cookie_viewed_links_ids())) {
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-link-id="'.$vrow['id'].'" title="Ja l\'has vist: prem per a marcar-lo com a no vist"><span class="fa fa-fw fa-eye"></span></span>'."\n";
		} else {
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-link-id="'.$vrow['id'].'" title="Encara no l\'has vist: prem per a marcar-lo com a vist"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
		}
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-method" title="Plataforma en què s\'allotja el vídeo: '.htmlspecialchars(get_provider($vrow['url'])).'">'.htmlspecialchars(get_provider_short($vrow['url'])).'</span>'."\n";
		if (!empty($vrow['resolution'])){
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($vrow['resolution']).'" title="Resolució del vídeo: '.htmlspecialchars($vrow['resolution']).'">'.htmlspecialchars(get_resolution_short($vrow['resolution'])).'</span>'."\n";
		}
		if (!empty($vrow['comments'])){
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-comments" title="Notes addicionals de la versió">'.htmlspecialchars($vrow['comments']).'</span>'."\n";
		}
			echo "\t\t\t\t\t\t\t\t\t\t\t</span>\n";
		echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
		echo "\t\t\t\t\t\t\t\t\t</div>\n";
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
?>
