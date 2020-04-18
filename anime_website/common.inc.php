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
			return "on-hold";
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
			return "Pausada: No hi ha tots els capítols disponibles";
		case 4:
			return "Cancel·lada: No hi ha tots els capítols disponibles";
		default:
			return "Estat desconegut";
	}
}

function get_fansub_version_title($text){
	$first = substr($text, 0, 1);
	if ($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U'){ //Ugly...
		return "Versió d'$text";
	}
	return "Versió de $text";
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
	return "Enllaç";
}

function get_display_method($url){
	return "embed";
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

	echo "\t\t\t\t\t\t\t\t\t".'<div class="episode'.(mysqli_num_rows($result)==0 ? ' episode-unavailable' : '').'">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">';
	if (!empty($row['number'])) {
		if (!empty($row['title'])){
			if ($series['episodes']==1){
				echo htmlspecialchars($row['title']);
			} else {
				echo 'Capítol '.$row['number'].': '.htmlspecialchars($row['title']);
			}
		}
		else {
			if ($series['episodes']==1){
				echo htmlspecialchars($series['name']);
			} else {
				echo 'Capítol '.$row['number'];
			}
		}
	} else {
		if (!empty($row['title'])){
			echo htmlspecialchars($row['title']);
		}
		else {
			echo $row['name'];
		}
	}
	echo "</div>\n";

	if (mysqli_num_rows($result)==0){
			echo "\t\t\t\t\t\t\t\t\t\t".'<div class="version">Aquest contingut no està disponible.</div>'."\n";
	}
	else{
		while ($vrow = mysqli_fetch_assoc($result)){
			echo "\t\t\t\t\t\t\t\t\t\t".'<div class="version">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-play icon-play"></span>Reprodueix</a> '."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-method" title="Plataforma en què s\'allotja el vídeo">'.htmlspecialchars(get_provider($vrow['url'])).'</span>'."\n";
			if (!empty($vrow['resolution'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution" title="Resolució del vídeo">'.htmlspecialchars($vrow['resolution']).'</span>'."\n";
			}
			if (!empty($vrow['comments'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-comments" title="Notes addicionals de la versió">'.htmlspecialchars($vrow['comments']).'</span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
		}
	}
	echo "\t\t\t\t\t\t\t\t\t</div>\n";
}

function print_extra($row,$version_id){
	$result = query("SELECT l.* FROM link l WHERE l.episode_id IS NULL AND l.extra_name='".$row['extra_name']."' AND l.version_id=$version_id ORDER BY l.resolution ASC, l.id ASC");

	echo "\t\t\t\t\t\t\t\t\t".'<div class="episode">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">';
	echo htmlspecialchars($row['extra_name']);
	echo "</div>\n";
	while ($vrow = mysqli_fetch_assoc($result)){
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="version">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-play icon-play"></span>Reprodueix</a> '."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-method" title="Plataforma en què s\'allotja el vídeo">'.htmlspecialchars(get_provider($vrow['url'])).'</span>'."\n";
		if (!empty($vrow['resolution'])){
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution" title="Resolució del vídeo">'.htmlspecialchars($vrow['resolution']).'</span>'."\n";
		}
		if (!empty($vrow['comments'])){
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-comments" title="Notes addicionals de la versió">'.htmlspecialchars($vrow['comments']).'</span>'."\n";
		}
		echo "\t\t\t\t\t\t\t\t\t\t</div>\n";
	}
	echo "\t\t\t\t\t\t\t\t\t</div>\n";
}
?>
