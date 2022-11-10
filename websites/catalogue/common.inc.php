<?php
//Versions to avoid site caching
const JS_VER=57;
const CS_VER=22;
const VS_VER=6;
const PL_VER=6;

//Regexp used for determining types of links
const REGEXP_MEGA='/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_GOOGLE_DRIVE='/https:\/\/drive\.google\.com\/(?:file\/d\/|open\?id=)?([^\/]*)(?:preview|view)?/';
const REGEXP_YOUTUBE='/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})?/';
const REGEXP_DL_LINK='/^https:\/\/(?:drive\.google\.com|mega\.nz|mega\.co\.nz).*/';
const REGEXP_STORAGE='/^storage:\/\/.*/';

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
	global $config;
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
	global $storages;
	if (count($storages)>0 && strpos($url, "storage://")===0) {
		$rand = rand(0, count($storages)-1);
		if ($clean) {
			return str_replace("storage://", $storages[$rand], $url);
		} else {
			return generate_storage_url(str_replace("storage://", $storages[$rand], $url));
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
	global $storages;
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

	if (count($links_storage)>0 && count($storages)>0) {
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
	global $config;
	if ($series['name']==$episode_title || ($series['subtype']==$config['filmsoneshots_db'] && !$is_extra)){
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
	global $config,$google_drive_api_key;
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
				'url' => "https://www.googleapis.com/drive/v3/files/".$matches[1]."?key=".$google_drive_api_key."&alt=media",
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else if (preg_match(REGEXP_YOUTUBE,$link['url'],$matches)){
			$elements[]=array(
				'url' => "https://www.youtube.com/embed/".$matches[1]."?origin=${config['base_url']}&iv_load_policy=3&modestbranding=1&playsinline=1showinfo=0&rel=0&enablejsapi=1",
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
	global $config, $default_fansub_id;
	if (!empty($row['linked_episode_id'])) {
		log_action("SELECT f.* FROM file f WHERE f.episode_id=".$row['linked_episode_id']." AND f.version_id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']}) ORDER BY f.variant_name ASC, f.id ASC");
		$result = query("SELECT f.* FROM file f WHERE f.episode_id=".$row['linked_episode_id']." AND f.version_id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']}) ORDER BY f.variant_name ASC, f.id ASC");
		$results = query("SELECT s.* FROM episode e LEFT JOIN series s ON e.series_id=s.id WHERE e.id=${row['linked_episode_id']}");
		$series = mysqli_fetch_assoc($results);
		mysqli_free_result($results);
		$resultv=query("SELECT v.*, GROUP_CONCAT(DISTINCT IF(v.version_author IS NULL OR f.id<>$default_fansub_id, f.name, CONCAT(f.name, ' (', v.version_author, ')')) ORDER BY IF(v.version_author IS NULL OR f.id<>$default_fansub_id, f.name, CONCAT(f.name, ' (', v.version_author, ')')) SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']})");
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
		} else if ($series['subtype']==$config['filmsoneshots_db']) {
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
	global $config, $static_url;
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
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-fansub="'.htmlspecialchars($fansub_names).'" data-cover="'.$static_url.'/images/covers/'.$series['id'].'.jpg" data-title="'.get_episode_player_title(htmlspecialchars($fansub_names), $series, $episode_title, $is_extra).'" data-file-id="'.$vrow['id'].'" data-position="'.$position.'" data-sources="'.htmlspecialchars(base64_encode(get_video_sources($links))).'" data-method="'.htmlspecialchars(get_display_method($links)).'"><span class="fa fa-fw fa-play icon-play"></span>'.(!empty($vrow['variant_name']) ? htmlspecialchars($vrow['variant_name']) : 'Reprodueix-lo').'</a> '."\n";
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
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="video-player" data-fansub="'.htmlspecialchars($fansub_names).'" data-cover="'.$static_url.'/images/covers/'.$series['id'].'.jpg" data-title="'.get_episode_player_title(htmlspecialchars($fansub_names), $series, $episode_title, $is_extra).'" data-file-id="'.$vrow['id'].'" data-position="'.$position.'" data-sources="'.htmlspecialchars(base64_encode(get_video_sources($links))).'" data-method="'.htmlspecialchars(get_display_method($links)).'"><span class="fa fa-fw fa-play icon-play"></span>'.$episode_title.'</a> '."\n";
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
	$result = query("SELECT COUNT(*) cnt FROM version WHERE series_id=$series_id AND is_hidden=0");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);	
	return ($row['cnt']>1);
}

function get_recommended_fansub_info($fansub_name, $fansub_type, $manga=FALSE) {
	if (strpos($fansub_name,"|")!==FALSE){
		if (strpos($fansub_type,"|")!==FALSE) {
			return 'Versions de diversos fansubs';
		} else if ($fansub_type=='fandub') {
			return 'Doblatge de diversos fansubs';
		} else if ($manga){
			return 'Editat per diversos fansubs';
		} else {
			return 'Subtítols de diversos fansubs';
		}
	} else if ($fansub_type=='fandub') {
		return 'Doblatge '.get_fansub_preposition_name($fansub_name);
	} else if ($manga){
		return 'Editat per '.$fansub_name;
	} else {
		return 'Subtítols '.get_fansub_preposition_name($fansub_name);
	}
}

function print_carousel_item($series, $specific_version, $show_new=TRUE) {
	if ($series['type']=='manga') {
		print_carousel_item_manga($series, $specific_version, $show_new);
	} else {
		print_carousel_item_generic($series, $specific_version, $show_new);
	}
}

function print_carousel_item_generic($series, $specific_version, $show_new=TRUE) {
	global $config, $anime_url, $liveaction_url, $static_url;
	echo "\t\t\t\t\t\t\t".'<a class="thumbnail" data-series-id="'.$series['slug'].'" href="'.($series['type']=='liveaction' ? $liveaction_url : $anime_url).'/'.($series['subtype']=='movie' ? "films" : "series").'/'.$series['slug'].(($specific_version && exists_more_than_one_version($series['id'])) ? "?v=".$series['version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($series['best_status']).'"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$static_url.'/images/covers/'.$series['id'].'.jpg" alt="'.$series['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-play"></span>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	if ($show_new && !empty($series['last_file_created']) && $series['last_file_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat fa poc"><span class="fa fa-fw fa-certificate"></span></div>'."\n";
	}
	if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.$series['number_of_episodes'].' films</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Film</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie, '.$series['divisions'].' temporades</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="ellipsized-title">'.$series['name'].'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	$fansub_type='';
	if ($series['fansub_type']=='fandub') {
		$fansub_type = '<span class="fansub-type" title="Versió doblada"><span class="fa fa-fw fa-microphone"></span></span>'."\n";
	} else if ($series['fansub_type']=='fansub') {
		//$fansub_type = '<span class="fansub-type" title="Versió subtitulada"><span class="fa-stack" style="font-size:0.63em;"><span class="far fa-fw fa-square fa-stack-2x"></span><span class="fa fa-fw fa-minus fa-stack-1x" style="margin-top: 0.2em;"></span></span></span>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub"><span class="fansub-name-th">'.(strpos($series['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $series['fansub_name']).'</span>'.(!empty($fansub_type) ? ' '.$fansub_type : '').'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>';
}

function print_carousel_item_manga($manga, $specific_version, $show_new=TRUE) {
	global $manga_url, $static_url;
	echo "\t\t\t\t\t\t\t".'<a class="thumbnail data-series-id="'.$manga['slug'].'" href="'.$manga_url.'/'.($manga['subtype']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug'].(($specific_version && exists_more_than_one_version($manga['id'])) ? "?v=".$manga['version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($manga['best_status']).'"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$static_url.'/images/covers/'.$manga['id'].'.jpg" alt="'.$manga['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-book-open"></span>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	if ($show_new && !empty($manga['last_file_created']) && $manga['last_file_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat fa poc"><span class="fa fa-fw fa-certificate"></span></div>'."\n";
	}
	if ($manga['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.$manga['divisions'].' volums</div>'."\n";
	} else if ($manga['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">One-shot</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">1 volum</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="ellipsized-title">'.$manga['name'].'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub"><span class="fansub-name-th">'.(strpos($manga['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $manga['fansub_name']).'</span></div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>'."\n";
}

function print_featured_item($series, $specific_version=TRUE, $show_new=TRUE) {
	if ($series['type']=='manga') {
		print_featured_item_manga($series, $specific_version, $show_new);
	} else {
		print_featured_item_generic($series, $specific_version, $show_new);
	}
}

function print_featured_item_generic($series, $specific_version=TRUE) {
	global $anime_url, $liveaction_url, $static_url;
	echo "\t\t\t\t\t\t\t".'<a class="recommendation data-series-id="'.$series['slug'].'" href="'.($series['type']=='liveaction' ? $liveaction_url : $anime_url).'/'.($series['subtype']=='movie' ? "films" : "series").'/'.$series['slug'].(($specific_version && exists_more_than_one_version($series['id'])) ? "?v=".$series['version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$static_url.'/images/featured/'.$series['id'].'.jpg" alt="'.$series['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status" title="'.get_status_description($series['best_status']).'">'.get_status_description_short($series['best_status']).'</div>'."\n";
	
	if (!empty($series['last_file_created']) && $series['last_file_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat!</div>'."\n";
	}
	if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.$series['number_of_episodes'].' films</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Film</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie '.($series['divisions']==11 ? "d'11" : 'de '.$series['divisions']).' temporades, '.($series['number_of_episodes']==-1 ? 'en emissió' : $series['number_of_episodes'].' capítols').'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie '.($series['number_of_episodes']==-1 ? 'en emissió' : ($series['number_of_episodes']==1 ? "d'1 capítol" : ($series['number_of_episodes']==11 ? "d'11 capítols" : 'de '.$series['number_of_episodes'].' capítols'))).'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-play"></span> Mira\'l ara'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".$series['name']."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(false)->line($series['synopsis']);

	echo "\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub">'.get_recommended_fansub_info($series['fansub_name'], $series['fansub_type']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>'."\n";
}

function print_featured_item_manga($manga, $specific_version=TRUE) {
	global $manga_url, $static_url;
	echo "\t\t\t\t\t\t\t".'<a class="recommendation data-series-id="'.$manga['slug'].'" href="'.$manga_url.'/'.($manga['subtype']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug'].(($specific_version && exists_more_than_one_version($manga['id'])) ? "?v=".$manga['version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$static_url.'/images/featured/'.$manga['id'].'.jpg" alt="'.$manga['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status" title="'.get_status_description($manga['best_status']).'">'.get_status_description_short($manga['best_status']).'</div>'."\n";
	
	if (!empty($manga['last_file_created']) && $manga['last_file_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat!</div>'."\n";
	}
	if ($manga['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">One-shot</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="divisions">Manga '.($manga['divisions']==1 ? "d'1 volum" : ($manga['divisions']==11 ? "d'11 volums" : 'de '.$manga['divisions'].' volums')).', '.($manga['number_of_episodes']==-1 ? 'en edició' : $manga['number_of_episodes'].' capítols').'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-book-open"></span> Llegeix-lo ara'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="title">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".$manga['name']."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(false)->line($manga['synopsis']);

	echo "\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub">'.get_recommended_fansub_info($manga['fansub_name'], $manga['fansub_type'], TRUE).'</div>'."\n";
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

function get_cookie_viewed_files_ids() {
	$file_ids = array();
	if (!empty($_COOKIE['viewed_files'])) {
		$exploded = explode(',',$_COOKIE['viewed_files']);
		foreach ($exploded as $id) {
			if (intval($id)) {
				array_push($file_ids, intval($id));
			}
		}
	}
	return $file_ids;
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
