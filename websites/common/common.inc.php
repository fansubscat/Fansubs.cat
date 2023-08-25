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

function get_relative_date($time) {
	if (time()-$time<3600*24) {
		$hours = intval((time()-$time)/3600);
		if ($hours==1) {
			return "fa 1 hora";
		} else {
			return "fa $hours hores";
		}
	}
	else if (time()-$time<3600*24*30) {
		$days = intval((time()-$time)/(3600*24));
		if ($days==1) {
			return "fa 1 dia";
		} else {
			return "fa $days dies";
		}
	}
	else {
		return get_catalan_formatted_date($time);
	}
}

function get_catalan_formatted_date($date) {
	$day = date('j', $date);
	if ($day=='1') {
		$day.='r';
	}
	$month = date('m', $date);
	switch ($month) {
		case '01':
			$month = 'de gener';
			break;
		case '02':
			$month = 'de febrer';
			break;
		case '03':
			$month = 'de març';
			break;
		case '04':
			$month = 'd’abril';
			break;
		case '05':
			$month = 'de maig';
			break;
		case '06':
			$month = 'de juny';
			break;
		case '07':
			$month = 'de juliol';
			break;
		case '08':
			$month = 'd’agost';
			break;
		case '09':
			$month = 'de setembre';
			break;
		case '10':
			$month = 'd’octubre';
			break;
		case '11':
			$month = 'de novembre';
			break;
		case '12':
		default:
			$month = 'de desembre';
			break;
	}
	$year = date('Y', $date);
	return "$day $month del $year";
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

function get_status_css_icons($id){
	switch ($id){
		case 1:
			return "fa fa-fw fa-circle-check";
		case 2:
			return "fa fa-fw fa-circle-play";
		case 3:
			return "fa fa-fw fa-circle-check";
		case 4:
			return "fa fa-fw fa-circle-question";
		case 5:
			return "fa fa-fw fa-circle-stop";
		default:
			return "fa fa-fw fa-circle";
	}
}

function get_prepared_versions($fansub_info) {
	$fansubs = explode('|',$fansub_info);
	$versions = array();
	$current_version_id=-1;
	$current_version_status = -1;
	$current_version_fansubs = array();
	foreach ($fansubs as $fansub) {
		$fields = explode('___',$fansub);
		if ($fields[0]!=$current_version_id) {
			if ($current_version_id!=-1) {
				array_push($versions, array('id' => $current_version_id, 'status' => $current_version_status, 'fansubs' => $current_version_fansubs));
			}
			$current_version_id = $fields[0];
			$current_version_status = $fields[1];
			$current_version_fansubs = array();
		}
		array_push($current_version_fansubs, array('id' => $fields[4], 'name' => $fields[2], 'type' => $fields[3], 'icon' => STATIC_URL.'/images/icons/'.$fields[4].'.png'));
	}
	array_push($versions, array('id' => $current_version_id, 'status' => $current_version_status, 'fansubs' => $current_version_fansubs));
	return $versions;
}

function get_carousel_fansub_info($fansub_info, $versions, $specific_version_id) {
	if (!empty($specific_version_id)) {
		//We recreate the array with only one version (if not found, it stays the same)
		foreach ($versions as $version) {
			if ($version['id']==$specific_version_id) {
				$versions = array($version);
				break;
			}
		}
	}

	if (count($versions)!=1) {
		$fansub_name = count($versions).' versions';
	} else {
		$fansub_name = '';
		foreach ($versions[0]['fansubs'] as $fansub) {
			if ($fansub_name!='') {
				$fansub_name.=' + ';
			}
			$fansub_name.=($fansub['type']=='fandub' ? '<i class="fa fa-fw fa-microphone"></i>' : '').htmlspecialchars($fansub['name']);
		}
	}

	return '<div class="floating-info-versions-icons">'.get_fansub_icons($fansub_info, $versions, $specific_version_id).'</div><div class="fansub-name">'.$fansub_name."</div>";
}

function get_fansub_icons($fansub_info, $versions, $specific_version_id) {
	if (!empty($specific_version_id)) {
		//We recreate the array with only one version (if not found, it stays the same)
		foreach ($versions as $version) {
			if ($version['id']==$specific_version_id) {
				$versions = array($version);
				break;
			}
		}
	}
	$result_code='';
	foreach ($versions as $version) {
		$result_code.='<div class="fansubs">';
		foreach ($version['fansubs'] as $fansub) {
			$result_code.='<div class="fansub"><img src="'.$fansub['icon'].'" title="'.htmlspecialchars($fansub['name']).'"></div>'."\n";
		}
		$result_code.='<div class="version-status status-'.get_status($version['status']).' '.get_status_css_icons($version['status']).'" title="'.htmlspecialchars(get_status_description($version['status'])).'"></div>';
		$result_code.='</div>';
	}
	return $result_code;
}

function get_fansub_type($versions, $version_id) {
	foreach ($versions as $version) {
		if ($version['id']==$version_id) {
			return $version['fansubs'][0]['type'];
		}
	}
	return 'fansub';
}

function get_genre_names_from_array($genre_names) {
	if (empty($genre_names)) {
		return "";
	}
	$genres_array = explode(' • ',$genre_names);
	$result_code = '';

	foreach ($genres_array as $genre_data) {
		$genre = explode('|', $genre_data)[2];
		if ($result_code!='') {
			$result_code.=' • ';
		}
		$result_code.=htmlspecialchars($genre);
	}

	return $result_code;
}

function print_carousel_item($series, $specific_version, $show_new=TRUE) {
	global $user;
	$versions = get_prepared_versions($series['fansub_info']);
	$number_of_versions = count($versions);
	echo "\t\t\t\t\t\t\t".'<div class="thumbnail-outer">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="thumbnail thumbnail-'.$series['id'].'" data-series-id="'.$series['id'].'" onmouseenter="prepareFloatingInfo(this);">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="versions">'.get_fansub_icons($series['fansub_info'], $versions, $specific_version ? $series['version_id'] : NULL).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<a class="image-link" href="'.get_base_url_from_type_and_rating($series['type'], $series['rating']).'/'.$series['slug'].(($specific_version && $number_of_versions>1) ? "?v=".$series['version_id'] : "").'"><img src="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" alt="'.htmlspecialchars($series['name']).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="clickable-thumbnail" onclick="prepareClickableFloatingInfo(this);"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="floating-info">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-main">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-bookmark '.(in_array($series['id'], !empty($user) ? $user['series_list_ids'] : array()) ? 'fas' : 'far').' fa-fw fa-bookmark" data-series-id="'.$series['id'].'" onclick="toggleBookmark('.$series['id'].'); event.stopPropagation(); return false;"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-title">'.htmlspecialchars($series['name']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-versions">'.get_carousel_fansub_info($series['fansub_info'], $versions, $specific_version ? $series['version_id'] : NULL).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-synopsis-wrapper">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

	echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="floating-info-watch-now" href="'.get_base_url_from_type_and_rating($series['type'], $series['rating']).'/'.$series['slug'].(($specific_version && $number_of_versions>1) ? "?v=".$series['version_id'] : "").'" onclick="event.stopPropagation();">'.($series['type']=='manga' ? 'Llegeix-lo ara' : 'Mira’l ara').'</a>'."\n";
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
	echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-genres">'.get_genre_names_from_array($series['genre_names']).'</div>'."\n";
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
