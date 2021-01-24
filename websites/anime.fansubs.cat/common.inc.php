<?php
const REGEXP_MEGA='/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_GOOGLE_DRIVE='/https:\/\/drive\.google\.com\/(?:file\/d\/|open\?id=)?([^\/]*)(?:preview|view)?/';
const REGEXP_YOUTUBE='/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})?/';
const REGEXP_DL_LINK='/^https:\/\/(?:drive\.google\.com|mega\.nz|mega\.co\.nz).*/';

function is_robot(){
	return !empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']);
}

function get_status($id){
	switch ($id){
		case 1:
			return "completed";
		case 2:
			return "in-progress";
		case 3:
			return "partially-completed";
		case 4:
		case 5:
			return "cancelled";
		default:
			return "unknown";
	}
}

function get_status_description_short($id){
	switch ($id){
		case 1:
			return "Completat";
		case 2:
			return "En procés";
		case 3:
			return "Parcialment completat";
		case 4:
			return "Abandonat";
		case 5:
			return "Cancel·lat";
		default:
			return "Estat desconegut";
	}
}

function get_status_description($id){
	switch ($id){
		case 1:
			return "Completat";
		case 2:
			return "En procés: No hi ha tots els capítols disponibles";
		case 3:
			return "Parcialment completat: Almenys una part de l'anime està completat";
		case 4:
			return "Abandonat: No hi ha tots els capítols disponibles";
		case 5:
			return "Cancel·lat: No hi ha tots els capítols disponibles";
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
	if (count(explode('x',$resolution))>1) {
		return explode('x',$resolution)[1]."p";
	} else {
		return $resolution;
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
			$episode_title.='Capítol '.str_replace('.',',',floatval($row['number'])).': '.htmlspecialchars($row['title']);
		}
		else {
			$episode_title.='Capítol '.str_replace('.',',',floatval($row['number']));
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
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$episode_title."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
	} else if (mysqli_num_rows($result)>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode-multiple">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title no-indent">'.$episode_title."</div>\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";

		while ($vrow = mysqli_fetch_assoc($result)){
			if (!empty($vrow['url'])) {
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				if (in_array($vrow['id'], get_cookie_viewed_links_ids())) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-link-id="'.$vrow['id'].'" title="Ja l\'has vist"><span class="fa fa-fw fa-eye"></span></span>'."\n";
				} else {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-link-id="'.$vrow['id'].'" title="Encara no l\'has vist"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="version episode-title">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-fw fa-play icon-play"></span>'.(!empty($vrow['comments']) ? htmlspecialchars($vrow['comments']) : 'Reprodueix-lo').'</a> '."\n";
				if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="new-episode tooltip'.(in_array($vrow['id'], get_cookie_viewed_links_ids()) ? ' hidden' : '').'" data-link-id="'.$vrow['id'].'" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right">'."\n";
				if (!empty($vrow['resolution'])) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($vrow['resolution']).' tooltip tooltip-right" title="'."Vídeo: ".$vrow['resolution'].", servei: ".get_provider($vrow['url']).'">'.htmlspecialchars(get_resolution_short($vrow['resolution'])).'</span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
			} else { //Empty link -> lost link
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$episode_title."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost tooltip" title="Perdut, ens ajudes?"><span class="fa fa-fw fa-ghost"></span></span>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
			}
		}
	} else { //Only one link
		$vrow = mysqli_fetch_assoc($result);

		if (!empty($vrow['url'])) {
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			if (in_array($vrow['id'], get_cookie_viewed_links_ids())) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-link-id="'.$vrow['id'].'" title="Ja l\'has vist"><span class="fa fa-fw fa-eye"></span></span>'."\n";
			} else {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-link-id="'.$vrow['id'].'" title="Encara no l\'has vist"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-link-id="'.$vrow['id'].'" data-url="'.htmlspecialchars(base64_encode(get_display_url($vrow['url']))).'" data-method="'.htmlspecialchars(get_display_method($vrow['url'])).'"><span class="fa fa-fw fa-play icon-play"></span>'.$episode_title.'</a> '."\n";
			if (!empty($vrow['comments'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-info tooltip" title="'.htmlspecialchars($vrow['comments']).'"><span class="fa fa-fw fa-info-circle"></span></span>'."\n";
			}
			if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="new-episode tooltip'.(in_array($vrow['id'], get_cookie_viewed_links_ids()) ? ' hidden' : '').'" data-link-id="'.$vrow['id'].'" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($vrow['resolution']).' tooltip tooltip-right" title="'."Vídeo: ".$vrow['resolution'].", servei: ".get_provider($vrow['url']).'">'.htmlspecialchars(get_resolution_short($vrow['resolution'])).'</span>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
		} else { //Empty link -> lost link
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$episode_title."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost tooltip" title="Perdut, ens ajudes?"><span class="fa fa-fw fa-ghost"></span></span>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
		}
	}
}

function exists_more_than_one_version($series_id){
	$result = query("SELECT COUNT(*) cnt FROM version WHERE series_id=$series_id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);	
	return ($row['cnt']>1);
}

function exists_more_than_one_version_manga($series_id){
	$result = query("SELECT COUNT(*) cnt FROM manga_version WHERE manga_id=$series_id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);	
	return ($row['cnt']>1);
}

function get_recommended_fansub_info($fansub_name, $fansub_type) {
	if (strpos($fansub_name,"|")!==FALSE){
		if (strpos($fansub_type,"|")!==FALSE) {
			return 'Versions de diversos fansubs';
		} else if ($fansub_type=='fandub') {
			return 'Doblatge de diversos fansubs';
		} else {
			return 'Subtítols de diversos fansubs';
		}
	} else if ($fansub_type=='fandub') {
		return 'Doblatge '.get_fansub_preposition_name($fansub_name);
	} else {
		return 'Subtítols '.get_fansub_preposition_name($fansub_name);
	}
}

function print_carousel_item_anime($anime, $tracking_class, $specific_version, $show_new=TRUE) {
	global $base_url;
	echo "\t\t\t\t\t\t\t".'<a class="thumbnail trackable-'.$tracking_class.'" data-series-id="'.$anime['slug'].'" href="'.$base_url.'/'.($anime['type']=='movie' ? "films" : "series").'/'.$anime['slug'].(($specific_version && exists_more_than_one_version($anime['id'])) ? "?v=".$anime['version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($anime['best_status']).'"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$base_url.'/images/series/'.$anime['id'].'.jpg" alt="'.$anime['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-play"></span>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	if ($show_new && !empty($anime['last_link_created']) && $anime['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat fa poc"><span class="fa fa-fw fa-certificate"></span></div>'."\n";
	}
	if ($anime['type']=='movie' && $anime['episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="seasons">'.$anime['episodes'].' films</div>'."\n";
	} else if ($anime['type']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="seasons">Film</div>'."\n";
	} else if ($anime['seasons']>1 && $anime['show_seasons']==1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="seasons">'.$anime['seasons'].' temporades</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="ellipsized-title">'.$anime['name'].'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	$fansub_type='';
	if ($anime['fansub_type']=='fandub') {
		$fansub_type = '<span class="fansub-type" title="Versió doblada"><span class="fa fa-fw fa-microphone"></span></span>'."\n";
	} else if ($anime['fansub_type']=='fansub') {
		//$fansub_type = '<span class="fansub-type" title="Versió subtitulada"><span class="fa-stack" style="font-size:0.63em;"><span class="far fa-fw fa-square fa-stack-2x"></span><span class="fa fa-fw fa-minus fa-stack-1x" style="margin-top: 0.2em;"></span></span></span>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub">'.(strpos($anime['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $anime['fansub_name']).(!empty($fansub_type) ? ' '.$fansub_type : '').'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>';
}

function print_carousel_item_manga($manga, $tracking_class, $specific_version, $show_new=TRUE) {
	$base_url='https://manga.fansubs.cat';
	echo "\t\t\t\t\t\t\t".'<a class="thumbnail trackable-'.$tracking_class.'" data-manga-id="'.$manga['slug'].'" href="'.$base_url.'/'.($manga['type']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug'].(($specific_version && exists_more_than_one_version_manga($manga['id'])) ? "?v=".$manga['manga_version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($manga['best_status']).'"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$base_url.'/images/manga/'.$manga['id'].'.jpg" alt="'.$manga['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-book-open"></span>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	if ($show_new && !empty($manga['last_link_created']) && $manga['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat fa poc"><span class="fa fa-fw fa-certificate"></span></div>'."\n";
	}
	if ($manga['volumes']>1 && $manga['show_volumes']==1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="seasons">'.$manga['volumes'].' volums</div>'."\n";
	} else if ($manga['type']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="seasons">One-shot</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="seasons">1 volum</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="ellipsized-title">'.$manga['name'].'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub">'.(strpos($manga['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $manga['fansub_name']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>'."\n";
}

function get_cookie_fansub_ids() {
	$fansub_ids = array();
	if (!empty($_COOKIE['hidden_fansubs'])) {
		$exploded = explode(',',$_COOKIE['hidden_fansubs']);
		foreach ($exploded as $id) {
			if (intval($id)) {
				array_push($fansub_ids, intval($id));
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
			if (intval($id)) {
				array_push($link_ids, intval($id));
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
		return "Comenta-ho a Tadaima.cat";
	} else {
		$json_response = json_decode($response);
		if ($json_response->status!='ok') {
			return "Comenta-ho a Tadaima.cat";
		} else {
			$number_of_posts = count($json_response->result->posts);
			if ($number_of_posts==1){
				return "Comenta-ho a Tadaima.cat (1 comentari)";
			} else {
				return "Comenta-ho a Tadaima.cat ($number_of_posts comentaris)";
			}
		}
	}
}
?>
