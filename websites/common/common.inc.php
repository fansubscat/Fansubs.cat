<?php
require_once("libraries/parsedown.inc.php");

function is_adult(){
	global $user;
	return (!empty($user) && date_diff(date_create_from_format('Y-m-d', $user['birthdate']), date_create(date('Y-m-d')))->format('%Y')>=18);
}

function is_robot(){
	return !empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']);
}

function get_nanoid($size=24) {
	//Adapted from: https://github.com/hidehalo/nanoid-php/blob/master/src/Core.php
	$alphabet = '_-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen($alphabet);
	$mask = (2 << (int) (log($len - 1) / M_LN2)) - 1;
	$step = (int) ceil(1.6 * $mask * $size / $len);
	$id = '';
	while (true) {
		$bytes = unpack('C*', random_bytes($step));
		foreach ($bytes as $byte) {
			$byte &= $mask;
			if (isset($alphabet[$byte])) {
				$id .= $alphabet[$byte];
				if (strlen($id) === $size) {
					return $id;
				}
			}
		}
	}
}

function get_cookie_blacklisted_fansub_ids() {
	$fansub_ids = array();
	if (!empty($_COOKIE['blacklisted_fansub_ids'])) {
		$exploded = explode(',',$_COOKIE['blacklisted_fansub_ids']);
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
	if (!empty($_COOKIE['viewed_file_ids'])) {
		$exploded = explode(',',$_COOKIE['viewed_file_ids']);
		foreach ($exploded as $id) {
			if (intval($id)) {
				array_push($file_ids, intval($id));
			}
		}
	}
	return $file_ids;
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
			return "Parcialment completat: Almenys una part de l’obra està completada";
		case 4:
			return "Abandonat: No hi ha tots els capítols disponibles";
		case 5:
			return "Cancel·lat: No hi ha tots els capítols disponibles";
		default:
			return "Estat desconegut";
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

function print_carousel_item($series, $specific_version, $show_new=TRUE) {
	global $user;
	$more_than_one_version = exists_more_than_one_version($series['id']);
	echo "\t\t\t\t\t\t\t".'<div class="thumbnail-outer">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="thumbnail thumbnail-'.$series['id'].'" data-series-id="'.$series['id'].'" onmouseenter="prepareFloatingInfo(this);">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="status-indicator"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<a class="image-link" href="'.get_base_url_from_type_and_rating($series['type'], $series['rating']).'/'.$series['slug'].(($specific_version && $more_than_one_version) ? "?v=".$series['version_id'] : "").'"><img src="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" alt="'.htmlspecialchars($series['name']).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="clickable-thumbnail" onclick="prepareClickableFloatingInfo(this);"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="floating-info">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-main">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($series['best_status']).'"></div>'."\n";
	if (!empty($user)) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-bookmark '.(in_array($series['id'], $user['series_list_ids']) ? 'fas' : 'far').' fa-fw fa-bookmark" data-series-id="'.$series['id'].'" onclick="toggleBookmark('.$series['id'].'); event.stopPropagation(); return false;"></div>'."\n";
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
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="floating-info-watch-now" href="'.get_base_url_from_type_and_rating($series['type'], $series['rating']).'/'.$series['slug'].(($specific_version && $more_than_one_version) ? "?v=".$series['version_id'] : "").'" onclick="event.stopPropagation();">'.($series['type']=='manga' ? 'Llegeix-lo ara' : 'Mira’l ara').'</a>'."\n";
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

function get_base_url_from_type_and_rating($type, $rating) {
	if ($type=='liveaction'){
		return LIVEACTION_URL;
	} else if ($type=='anime'){
		if ($rating=='XXX') {
			return HENTAI_ANIME_URL;
		} else {
			return ANIME_URL;
		}
	} else {
		if ($rating=='XXX') {
			return HENTAI_MANGA_URL;
		} else {
			return MANGA_URL;
		}
	}
	die("Unknown type passed");
}
?>
