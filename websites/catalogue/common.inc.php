<?php
require_once("queries.inc.php");

//Versions to avoid site caching
const JS_VER=57;
const CS_VER=24;
const VS_VER=6;
const PL_VER=6;

//Regexp used for determining types of links
const REGEXP_MEGA='/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_GOOGLE_DRIVE='/https:\/\/drive\.google\.com\/(?:file\/d\/|open\?id=)?([^\/]*)(?:preview|view)?/';
const REGEXP_YOUTUBE='/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})?/';
const REGEXP_DL_LINK='/^https:\/\/(?:drive\.google\.com|mega\.nz|mega\.co\.nz).*/';
const REGEXP_STORAGE='/^storage:\/\/.*/';

function validate_hentai() {
	global $user;

	if (!defined('SITE_IS_HENTAI')) {
		define('SITE_IS_HENTAI', !empty($_GET['hentai']));
	}

	if (SITE_IS_HENTAI) {
		if (empty($user) && !is_robot()) {
			header("Location: ".USERS_URL."/inicia-la-sessio");
			die();
		} else if (!is_robot() && !is_adult()) {
			$_GET['hentai']=0;
			$_GET['code']=403;
			http_response_code(403);
			include('error.php');
			die();
		}
	}
}

function validate_hentai_ajax() {
	global $user;

	if (!defined('SITE_IS_HENTAI')) {
		define('SITE_IS_HENTAI', !empty($_GET['hentai']));
	}

	if (SITE_IS_HENTAI) {
		if ((empty($user) && !is_robot()) || (!is_robot() && !is_adult())) {
			$_GET['hentai']=0;
			$_GET['code']=403;
			http_response_code(403);
			die();
		}
	}
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
			return "abandoned";
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
			return "Parcialment completat: Almenys una part de l'obra està completada";
		case 4:
			return "Abandonat: No hi ha tots els capítols disponibles";
		case 5:
			return "Cancel·lat: No hi ha tots els capítols disponibles";
		default:
			return "Estat desconegut";
	}
}

function get_fansub_preposition_name($text){
	$first = mb_strtoupper(substr($text, 0, 1));
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

function get_provider($links){
	$methods = array();
	foreach ($links as $link) {
		if (preg_match(REGEXP_MEGA,$link['url'])){
			array_push($methods, 'mega');
		} else if (preg_match(REGEXP_GOOGLE_DRIVE,$link['url'])){
			array_push($methods, 'google-drive');
		} else if (preg_match(REGEXP_YOUTUBE,$link['url'])){
			array_push($methods, 'youtube');
		} else if (preg_match(REGEXP_STORAGE,$link['url'])) {
			array_push($methods, 'storage');
		} else {
			array_push($methods, 'direct-video');
		}
	}
	$output = '';
	if (in_array('google-drive', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.="Google Drive";
	}
	if (in_array('mega', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.="MEGA";
	}
	if (in_array('youtube', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.="YouTube";
	}
	if (in_array('direct-video', $methods) || in_array('storage', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.="Vídeo incrustat";
	}
	return $output;
}

function get_storage_url($url, $clean=FALSE) {
	if (count(STORAGES)>0 && strpos($url, "storage://")===0) {
		$rand = rand(0, count(STORAGES)-1);
		if ($clean) {
			return str_replace("storage://", STORAGES[$rand], $url);
		} else {
			return generate_storage_url(str_replace("storage://", STORAGES[$rand], $url));
		}
	} else {
		return $url;
	}
}

function list_remote_files($url) {
	$contents = @file_get_contents($url);
	preg_match_all("|href=[\"'](.*?)[\"']|", $contents, $hrefs);
	$hrefs = array_slice($hrefs[1], 1);
	
	$files = array();
	foreach ($hrefs as $href) {
		array_push($files, $url.$href);
	}
	return $files;
}

function filter_links($links){
	$methods = array();
	$links_mega = array();
	$links_googledrive = array();
	$links_youtube = array();
	$links_storage = array();
	$links_direct = array();
	foreach ($links as $link) {
		if (preg_match(REGEXP_MEGA,$link['url'])){
			array_push($links_mega, $link);
		} else if (preg_match(REGEXP_GOOGLE_DRIVE,$link['url'])){
			array_push($links_googledrive, $link);
		} else if (preg_match(REGEXP_YOUTUBE,$link['url'])){
			array_push($links_youtube, $link);
		} else if (preg_match(REGEXP_STORAGE,$link['url'])){
			array_push($links_storage, $link);
		} else {
			array_push($links_direct, $link);
		}
	}

	//This establishes the preferences order:
	//Storage > Direct video > Google Drive > YouTube > MEGA

	if (count($links_storage)>0 && count(STORAGES)>0) {
		return $links_storage;
	}

	if (count($links_direct)>0) {
		return $links_direct;
	}

	if (count($links_googledrive)>0) {
		return $links_googledrive;
	}

	if (count($links_youtube)>0) {
		return $links_youtube;
	}

	if (count($links_mega)>0) {
		return $links_mega;
	}
}

function get_resolution($links){
	$max_res=0;
	$max_res_text = "";
	foreach ($links as $link) {
		if (count(explode('x',$link['resolution']))>1) {
			$cur_res = explode('x',$link['resolution'])[1];
		} else {
			$cur_res=preg_replace("/[^0-9]/", '', $link['resolution']);
		}
		if ($cur_res>$max_res) {
			$max_res = $cur_res;
			$max_res_text = $link['resolution'];
		}
	}
	return $max_res_text;
}

function get_resolution_short($links){
	$max_res=0;
	$max_res_text = "";
	foreach ($links as $link) {
		if (count(explode('x',$link['resolution']))>1) {
			$cur_res = explode('x',$link['resolution'])[1];
		} else {
			$cur_res=preg_replace("/[^0-9]/", '', $link['resolution']);
		}
		if ($cur_res>$max_res) {
			$max_res = $cur_res;
			$max_res_text = $link['resolution'];
		}
	}
	return $max_res.'p';
}

function get_resolution_single($resolution){
	if (count(explode('x',$resolution))>1) {
		return explode('x',$resolution)[1];
	} else {
		return preg_replace("/[^0-9]/", '', $resolution);
	}
}

function get_resolution_css($links){
	$resolution = str_replace('p', '', get_resolution_short($links));
	if ($resolution>=1800) {
		return "4k";
	} else if ($resolution>=900) {
		return "hd1080";
	} else if ($resolution>=650) {
		return "hd720";
	} else {
		return "sd";
	}
}

function get_display_method($links){
	//Since we will not have mixed methods, we can just check the first one
	if (preg_match(REGEXP_MEGA,$links[0]['url'])){
		return "mega";
	}
	if (preg_match(REGEXP_GOOGLE_DRIVE,$links[0]['url'])){
		return "google-drive";
	}
	if (preg_match(REGEXP_YOUTUBE,$links[0]['url'])){
		return "youtube";
	}
	if (preg_match(REGEXP_STORAGE,$links[0]['url'])){
		return "storage";
	}
	return "direct-video";
}

function get_episode_player_title($fansub_name, $series, $episode_title, $is_extra){
	if ($series['name']==$episode_title || ($series['subtype']==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID && !$is_extra)){
		if (!empty($episode_title)) {
			return $fansub_name . ' - ' . $episode_title;
		} else {
			return $fansub_name . ' - ' . $series['name'];
		}
	} else {
		return $fansub_name . ' - ' . $series['name'] . ' - '. $episode_title;
	}
}

function get_video_sources($links){
	$elements = array();
	foreach ($links as $link) {
		$matches = array();
		if (preg_match(REGEXP_MEGA,$link['url'],$matches)){
			$elements[]=array(
				'url' => $link['url'],
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else if (preg_match(REGEXP_GOOGLE_DRIVE,$link['url'],$matches)){
			$elements[]=array(
				'url' => "https://www.googleapis.com/drive/v3/files/".$matches[1]."?key=".GOOGLE_DRIVE_API_KEY."&alt=media",
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else if (preg_match(REGEXP_YOUTUBE,$link['url'],$matches)){
			$elements[]=array(
				'url' => "https://www.youtube.com/embed/".$matches[1]."?origin=".BASE_URL."&iv_load_policy=3&modestbranding=1&playsinline=1showinfo=0&rel=0&enablejsapi=1",
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else if (preg_match(REGEXP_STORAGE,$link['url'],$matches)){
			$elements[]=array(
				'url' => get_storage_url($link['url']),
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else {
			$elements[]=array(
				'url' => $link['url'],
				'resolution' => get_resolution_single($link['resolution'])
			);
		}
	}
	return json_encode($elements);
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

function print_episode($fansub_names, $row, $version_id, $series, $version, $position){
	if (!empty($row['linked_episode_id'])) {
		$result = query("SELECT f.* FROM file f WHERE f.episode_id=".$row['linked_episode_id']." AND f.version_id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']}) ORDER BY f.variant_name ASC, f.id ASC");
		$results = query("SELECT s.* FROM episode e LEFT JOIN series s ON e.series_id=s.id WHERE e.id=${row['linked_episode_id']}");
		$series = mysqli_fetch_assoc($results);
		mysqli_free_result($results);
		$resultv=query("SELECT v.*, GROUP_CONCAT(DISTINCT IF(v.version_author IS NULL OR f.id<>28, f.name, CONCAT(f.name, ' (', v.version_author, ')')) ORDER BY IF(v.version_author IS NULL OR f.id<>$default_fansub_id, f.name, CONCAT(f.name, ' (', v.version_author, ')')) SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']})");
		$version = mysqli_fetch_assoc($resultv);
		$fansub_names = $version['fansub_name'];
		mysqli_free_result($resultv);
	} else {
		$result = query("SELECT f.* FROM file f WHERE f.episode_id=".$row['id']." AND f.version_id=$version_id ORDER BY f.variant_name ASC, f.id ASC");
	}

	if (mysqli_num_rows($result)==0 && $version['show_unavailable_episodes']!=1){
		return;
	}

	$episode_title='';
	
	if ($version['show_episode_numbers']==1 && !empty($row['number']) && empty($row['linked_episode_id'])) {
		if (!empty($row['title'])){
			$episode_title.='Capítol '.str_replace('.',',',floatval($row['number'])).': '.htmlspecialchars($row['title']);
		}
		else {
			$episode_title.='Capítol '.str_replace('.',',',floatval($row['number']));
		}
	} else {
		if (!empty($row['title'])){
			$episode_title.=htmlspecialchars($row['title']);
		} else if ($series['subtype']==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID) {
			$episode_title.=$series['name'];
		} else {
			$episode_title.='Capítol sense nom';
		}
	}

	internal_print_episode($fansub_names, $episode_title, $result, $series, FALSE, $position);
	mysqli_free_result($result);
}

function print_extra($fansub_names, $row, $version_id, $series, $position){
	$result = query("SELECT f.* FROM file f WHERE f.episode_id IS NULL AND f.extra_name='".escape($row['extra_name'])."' AND f.version_id=$version_id ORDER BY f.id ASC");

	$episode_title=htmlspecialchars($row['extra_name']);
	
	internal_print_episode($fansub_names, $episode_title, $result, $series, TRUE, $position);
	mysqli_free_result($result);
}

function internal_print_episode($fansub_names, $episode_title, $result, $series, $is_extra, $position) {
	if (mysqli_num_rows($result)==0){
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$episode_title."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
		if ($series['type']!='manga') {
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
		}
		echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
	} else if (mysqli_num_rows($result)>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode-multiple">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title no-indent">'.$episode_title."</div>\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
		if ($series['type']!='manga') {
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
		}
		echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";

		while ($vrow = mysqli_fetch_assoc($result)){
			if ($vrow['is_lost']==0) {
				if ($series['type']!='manga') {
					$links = array();
					$resulti = query("SELECT l.* FROM link l WHERE l.file_id=${vrow['id']} ORDER BY l.url ASC");
					while ($lirow = mysqli_fetch_assoc($resulti)){
						array_push($links, $lirow);
					}
					mysqli_free_result($resulti);
					$links = filter_links($links);
				}
				
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				if (in_array($vrow['id'], get_cookie_viewed_files_ids())) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-file-id="'.$vrow['id'].'" title="Ja l\'has '.($series['type']=='manga' ? 'llegit' : 'vist').'"><span class="fa fa-fw fa-eye"></span></span>'."\n";
				} else {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-file-id="'.$vrow['id'].'" title="Encara no l\'has '.($series['type']=='manga' ? 'llegit' : 'vist').'"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="version episode-title">'."\n";
				if ($series['type']=='manga') {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="manga-reader" data-file-id="'.$vrow['id'].'"><span class="fa fa-fw fa-book-open icon-play"></span>'.(!empty($vrow['variant_name']) ? htmlspecialchars($vrow['variant_name']) : 'Llegeix-lo').'</a> '."\n";
				} else {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-fansub="'.htmlspecialchars($fansub_names).'" data-cover="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" data-title="'.get_episode_player_title(htmlspecialchars($fansub_names), $series, $episode_title, $is_extra).'" data-file-id="'.$vrow['id'].'" data-position="'.$position.'" data-sources="'.htmlspecialchars(base64_encode(get_video_sources($links))).'" data-method="'.htmlspecialchars(get_display_method($links)).'"><span class="fa fa-fw fa-play icon-play"></span>'.(!empty($vrow['variant_name']) ? htmlspecialchars($vrow['variant_name']) : 'Reprodueix-lo').'</a> '."\n";
				}
				if (!empty($vrow['comments'])){
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-info tooltip" title="'.str_replace("\n", "<br />", htmlspecialchars($vrow['comments'])).'"><span class="fa fa-fw fa-info-circle"></span></span>'."\n";
				}
				if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="new-episode tooltip'.(in_array($vrow['id'], get_cookie_viewed_files_ids()) ? ' hidden' : '').'" data-file-id="'.$vrow['id'].'" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				if ($series['type']!='manga') {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right">'."\n";
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($links).' tooltip tooltip-right" title="'."Vídeo: ".get_resolution($links).", servei: ".get_provider($links).'">'.htmlspecialchars(get_resolution_short($links)).'</span>'."\n";
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
			} else { //Lost file
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$episode_title."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost tooltip" title="Perdut, ens ajudes?"><span class="fa fa-fw fa-ghost"></span></span>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				if ($series['type']!='manga') {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
			}
		}
	} else { //Only one link
		$vrow = mysqli_fetch_assoc($result);

		if ($vrow['is_lost']==0) {
			if ($series['type']!='manga') {
				$links = array();
				$resulti = query("SELECT l.* FROM link l WHERE l.file_id=${vrow['id']} ORDER BY l.url ASC");
				while ($lirow = mysqli_fetch_assoc($resulti)){
					array_push($links, $lirow);
				}
				mysqli_free_result($resulti);
				$links = filter_links($links);
			}

			echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			if (in_array($vrow['id'], get_cookie_viewed_files_ids())) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-file-id="'.$vrow['id'].'" title="Ja l\'has '.($series['type']=='manga' ? 'llegit' : 'vist').'"><span class="fa fa-fw fa-eye"></span></span>'."\n";
			} else {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-file-id="'.$vrow['id'].'" title="Encara no l\'has '.($series['type']=='manga' ? 'llegit' : 'vist').'"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			if ($series['type']=='manga') {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="manga-reader" data-file-id="'.$vrow['id'].'"><span class="fa fa-fw fa-book-open icon-play"></span>'.$episode_title.'</a> '."\n";
			} else {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-fansub="'.htmlspecialchars($fansub_names).'" data-cover="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" data-title="'.get_episode_player_title(htmlspecialchars($fansub_names), $series, $episode_title, $is_extra).'" data-file-id="'.$vrow['id'].'" data-position="'.$position.'" data-sources="'.htmlspecialchars(base64_encode(get_video_sources($links))).'" data-method="'.htmlspecialchars(get_display_method($links)).'"><span class="fa fa-fw fa-play icon-play"></span>'.$episode_title.'</a> '."\n";
			}
			if (!empty($vrow['comments'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-info tooltip" title="'.str_replace("\n", "<br />", htmlspecialchars($vrow['comments'])).'"><span class="fa fa-fw fa-info-circle"></span></span>'."\n";
			}
			if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="new-episode tooltip'.(in_array($vrow['id'], get_cookie_viewed_files_ids()) ? ' hidden' : '').'" data-file-id="'.$vrow['id'].'" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			if ($series['type']!='manga') {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-resolution-'.get_resolution_css($links).' tooltip tooltip-right" title="'."Vídeo: ".get_resolution($links).", servei: ".get_provider($links).'">'.htmlspecialchars(get_resolution_short($links)).'</span>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
		} else { //Lost file
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$episode_title."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost tooltip" title="Perdut, ens ajudes?"><span class="fa fa-fw fa-ghost"></span></span>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			if ($series['type']!='manga') {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td class="right"></td>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
		}
	}
}

function exists_more_than_one_version($series_id){
	$result = query_number_of_versions_by_series_id($series_id);
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	return ($row['cnt']>1);
}

function get_version_fansubs($fansub_info, $version_id) {
	$fansubs = explode('|',$fansub_info);
	$version_fansubs = array();

	foreach ($fansubs as $fansub) {
		$fields = explode('___',$fansub);
		if ($fields[0]==$version_id) {
			array_push($version_fansubs, array('name' => $fields[1], 'type' => $fields[2], 'icon' => STATIC_URL.'/images/icons/'.$fields[3].'.png'));
		}
	}
	return $version_fansubs;
}

function get_recommended_fansub_info($fansub_info, $version_id) {
	$version_fansubs = get_version_fansubs($fansub_info, $version_id);
	$result_code='';

	foreach ($version_fansubs as $fansub) {
		$result_code.='<div class="fansub">'.($fansub['type']=='fandub' ? '<i class="fa fa-fw fa-microphone"></i>' : '').'<span class="text">'.htmlspecialchars($fansub['name']).'</span> <img src="'.$fansub['icon'].'" alt=""></div>'."\n";
	}

	return $result_code;
}

function get_carousel_fansub_info($fansub_info, $version_id) {
	$version_fansubs = get_version_fansubs($fansub_info, $version_id);
	$result_code='';

	foreach ($version_fansubs as $fansub) {
		if ($result_code!='') {
			$result_code.=' + ';
		}
		$result_code.=($fansub['type']=='fandub' ? '<i class="fa fa-fw fa-microphone"></i>' : '').htmlspecialchars($fansub['name']);
	}

	return $result_code;
}

function get_genres_for_featured($genre_names) {
	if (empty($genre_names)) {
		return "";
	}
	$genres_array = explode(' • ',$genre_names);
	$result_code = '';

	foreach ($genres_array as $genre) {
		$result_code.='<div class="genre">'.htmlspecialchars($genre).'</div>';
	}
	return '<i class="fa fa-fw fa-tag fa-flip-horizontal"></i> '.$result_code;
}

function print_chapter_item($row) {
	echo "TODO";
}

function print_carousel_item($series, $specific_version, $show_new=TRUE) {
	global $user;
	$more_than_one_version = exists_more_than_one_version($series['id']);
	echo "\t\t\t\t\t\t\t".'<div class="thumbnail-outer">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="thumbnail thumbnail-'.$series['id'].'" data-series-id="'.$series['id'].'" onmouseenter="prepareFloatingInfo(this);">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="status-indicator"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<a class="image-link" href="'.($series['type']=='liveaction' ? LIVEACTION_URL : ($series['type']=='anime' ? ANIME_URL : MANGA_URL)).'/'.($series['rating']=='XXX' ? 'hentai/' : '').$series['slug'].(($specific_version && $more_than_one_version) ? "?v=".$series['version_id'] : "").'"><img src="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" alt="'.htmlspecialchars($series['name']).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="clickable-thumbnail" onclick="prepareClickableFloatingInfo(this);"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="floating-info">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-main">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($series['best_status']).'"></div>'."\n";
	if (!empty($user)) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-bookmark '.(in_array($series['id'], $user['series_list_ids']) ? 'fas' : 'far').' fa-fw fa-bookmark" data-series-id="'.$series['id'].'" onclick="toggleBookmark('.$series['id'].'); return false;"></div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-title">'.htmlspecialchars($series['name']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-fansub">'.(($more_than_one_version && !$specific_version) ? "Diverses versions" : get_carousel_fansub_info($series['fansub_info'], $series['version_id'])).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-synopsis-wrapper">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

	echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="floating-info-watch-now" href="'.($series['type']=='liveaction' ? LIVEACTION_URL : ($series['type']=='anime' ? ANIME_URL : MANGA_URL)).'/'.($series['rating']=='XXX' ? 'hentai/' : '').$series['slug'].(($specific_version && $more_than_one_version) ? "?v=".$series['version_id'] : "").'">'.($series['type']=='manga' ? 'Llegeix-lo ara' : 'Mira’l ara').'</a>'."\n";
	if ($series['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">One-shot</div>'."\n";
	} else if ($series['subtype']=='serialized') {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">Serialitzat • '.($series['divisions']==1 ? "1 vol." : $series['divisions'].' vol.').' • '.($series['number_of_episodes']==-1 ? 'En publicació' : ($series['number_of_episodes']==1 ? "1 capítol" : $series['number_of_episodes'].' capítols')).'</div>'."\n";
	} else if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">Conjunt de '.$series['number_of_episodes'].' films</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">Film</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">Sèrie • '.$series['divisions'].' temp. • '.($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols').'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">Sèrie • '.($series['number_of_episodes']==-1 ? 'En emissió' : ($series['number_of_episodes']==1 ? "1 capítol" : $series['number_of_episodes'].' capítols')).'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-genres-score-wrapper">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-genres-wrapper">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-genres">'.htmlspecialchars($series['genre_names']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-score">'.(!empty($series['score']) ? number_format($series['score'],2,","," ") : '-').'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>';
	echo "\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="ellipsized-title">'.htmlspecialchars($series['name']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</div>'."\n";
}

function print_featured_item($series, $special_day=NULL, $specific_version=TRUE) {
	$more_than_one_version = exists_more_than_one_version($series['id']);
	echo "\t\t\t\t\t\t\t".'<div class="recommendation" data-series-id="'.$series['id'].'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img class="background" src="'.STATIC_URL.'/images/featured/'.$series['id'].'.jpg" alt="'.$series['name'].'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status" title="'.get_status_description($series['best_status']).'"><div class="status-indicator"></div><span class="text">'.get_status_description_short($series['best_status']).'</span></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="coverholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a href="'.($series['type']=='liveaction' ? LIVEACTION_URL : ($series['type']=='anime' ? ANIME_URL : MANGA_URL)).'/'.($series['rating']=='XXX' ? 'hentai/' : '').$series['slug'].(($specific_version && $more_than_one_version) ? "?v=".$series['version_id'] : "").'"><img class="cover" src="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" alt="'.$series['name'].'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="dataholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="title">'.htmlspecialchars($series['name']).'</div>'."\n";
	if ($series['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">One-shot</div>'."\n";
	} else if ($series['subtype']=='serialized') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Serialitzat • '.($series['divisions']==1 ? "1 volum" : $series['divisions'].' volums').' • '.($series['number_of_episodes']==-1 ? 'En publicació' : ($series['number_of_episodes']==1 ? "1 capítol" : $series['number_of_episodes'].' capítols')).'</div>'."\n";
	} else if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Conjunt de '.$series['number_of_episodes'].' films</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Film</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie • '.$series['divisions'].' temporades • '.($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols').'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie • '.($series['number_of_episodes']==-1 ? 'En emissió' : ($series['number_of_episodes']==1 ? "1 capítol" : $series['number_of_episodes'].' capítols')).'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

	echo "\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a class="watchbutton" href="'.($series['type']=='liveaction' ? LIVEACTION_URL : ($series['type']=='anime' ? ANIME_URL : MANGA_URL)).'/'.($series['rating']=='XXX' ? 'hentai/' : '').$series['slug'].(($specific_version && $more_than_one_version) ? "?v=".$series['version_id'] : "").'">'.($series['type']=='manga' ? 'Llegeix-lo ara' : 'Mira’l ara').'</a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansubs">'.get_recommended_fansub_info($series['fansub_info'], $series['version_id']).'</div>'."\n";
	if (!empty($special_day)) {
		if ($special_day=='fools') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-trophy"></i><span class="text">Els millors de l’any</span></div>'."\n";
		} else if ($special_day=='sant_jordi') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-dragon"></i><span class="text">Especial Sant Jordi</span></div>'."\n";
		} if ($special_day=='tots_sants') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-ghost"></i><span class="text">Especial Tots Sants</span></div>'."\n";
		}
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="genres">'.get_genres_for_featured($series['genre_names']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</div>'."\n";
}

function get_tadaima_info($thread_id) {
	global $memcached;

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
			$memcached->set("tadaima_post_$thread_id", $response, MEMCACHED_EXPIRY_TIME);
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
