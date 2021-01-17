<?php
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

function get_hours_or_minutes_formatted($time){
	if ($time>=3600) {
		$hours = floor($time/3600);
		$time = $time-$hours*3600;
		echo $hours." h ".round($time/60)." min";
	} else {
		echo round($time/60)." min";
	}
}

function print_chapter($row,$version_id,$manga){
	$result = query("SELECT f.* FROM file f WHERE f.chapter_id=".$row['id']." AND f.manga_version_id=$version_id ORDER BY f.id ASC");

	if (mysqli_num_rows($result)==0 && $manga['show_unavailable_chapters']!=1){
		return;
	}

	$chapter_title='';
	
	if ($manga['show_chapter_numbers']==1 && !empty($row['number'])) {
		if (!empty($row['title'])){
			$chapter_title.='Capítol '.str_replace('.',',',floatval($row['number'])).': '.htmlspecialchars($row['title']);
		}
		else {
			$chapter_title.='Capítol '.str_replace('.',',',floatval($row['number']));
		}
	} else {
		if (!empty($row['title'])){
			$chapter_title.=htmlspecialchars($row['title']);
		} else if ($manga['type']=='oneshot') {
			$chapter_title.=$manga['name'];
		} else {
			$chapter_title.='Capítol sense nom';
		}
	}

	internal_print_chapter($chapter_title, $result);
	mysqli_free_result($result);
}

function print_extra($row,$version_id){
	$result = query("SELECT f.* FROM file f WHERE f.chapter_id IS NULL AND f.extra_name='".escape($row['extra_name'])."' AND f.manga_version_id=$version_id ORDER BY f.id ASC");

	$chapter_title=htmlspecialchars($row['extra_name']);
	
	internal_print_chapter($chapter_title, $result);
	mysqli_free_result($result);
}

function internal_print_chapter($chapter_title, $result) {
	if (mysqli_num_rows($result)==0){
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$chapter_title."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
	} else if (mysqli_num_rows($result)>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode-multiple">'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title no-indent">'.$chapter_title."</div>\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
		echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";

		while ($vrow = mysqli_fetch_assoc($result)){
			if (!empty($vrow['original_filename'])) {
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				if (in_array($vrow['id'], get_cookie_viewed_files_ids())) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-file-id="'.$vrow['id'].'" title="Ja l\'has llegit"><span class="fa fa-fw fa-eye"></span></span>'."\n";
				} else {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-file-id="'.$vrow['id'].'" title="Encara no l\'has llegit"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="version episode-title">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="manga-reader" data-file-id="'.$vrow['id'].'"><span class="fa fa-fw fa-book-open icon-play"></span>'.(!empty($vrow['comments']) ? htmlspecialchars($vrow['comments']) : 'Llegeix-lo').'</a> '."\n";
				if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
					echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="new-episode tooltip'.(in_array($vrow['id'], get_cookie_viewed_files_ids()) ? ' hidden' : '').'" data-file-id="'.$vrow['id'].'" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>'."\n";
				}
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
			} else { //Empty file name -> lost file
				echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="version episode-title">'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.htmlspecialchars($vrow['comments'])."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost tooltip" title="Perdut, ens ajudes?"><span class="fa fa-fw fa-ghost"></span></span>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
				echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
			}
		}
	} else { //Only one file
		$vrow = mysqli_fetch_assoc($result);

		if (!empty($vrow['original_filename'])) {
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			if (in_array($vrow['id'], get_cookie_viewed_files_ids())) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator viewed" data-file-id="'.$vrow['id'].'" title="Ja l\'has llegit"><span class="fa fa-fw fa-eye"></span></span>'."\n";
			} else {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="viewed-indicator not-viewed" data-file-id="'.$vrow['id'].'" title="Encara no l\'has llegit"><span class="fa fa-fw fa-eye-slash"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<a class="manga-reader" data-file-id="'.$vrow['id'].'"><span class="fa fa-fw fa-book-open icon-play"></span>'.$chapter_title.'</a> '."\n";
			if (!empty($vrow['comments'])){
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-info tooltip" title="'.str_replace("\n", "<br />", htmlspecialchars($vrow['comments'])).'"><span class="fa fa-fw fa-info-circle"></span></span>'."\n";
			}
			if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
				echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="new-episode tooltip'.(in_array($vrow['id'], get_cookie_viewed_files_ids()) ? ' hidden' : '').'" data-file-id="'.$vrow['id'].'" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>'."\n";
			}
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
		} else { //Empty file name -> lost file
			echo "\t\t\t\t\t\t\t\t\t\t\t".'<tr class="episode episode-unavailable">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td></td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="episode-title">'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-ban icon-play"></span>'.$chapter_title."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="version-lost tooltip" title="Perdut, ens ajudes?"><span class="fa fa-fw fa-ghost"></span></span>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\t</div>\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</td>'."\n";
			echo "\t\t\t\t\t\t\t\t\t\t\t</tr>\n";
		}
	}
}

function exists_more_than_one_version($series_id){
	$result = query("SELECT COUNT(*) cnt FROM manga_version WHERE manga_id=$series_id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);	
	return ($row['cnt']>1);
}

function exists_more_than_one_version_anime($series_id){
	$result = query("SELECT COUNT(*) cnt FROM version WHERE series_id=$series_id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);	
	return ($row['cnt']>1);
}

function print_carousel_item_manga($manga, $tracking_class, $specific_version, $show_new=TRUE) {
	global $base_url;
	echo "\t\t\t\t\t\t\t".'<a class="thumbnail trackable-'.$tracking_class.'" data-manga-id="'.$manga['slug'].'" href="'.$base_url.'/'.($manga['type']=='oneshot' ? "one-shots" : "serialitzats").'/'.$manga['slug'].(($specific_version && exists_more_than_one_version($manga['id'])) ? "?v=".$manga['manga_version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($manga['best_status']).'"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="/images/manga/'.$manga['id'].'.jpg" alt="'.$manga['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-book-open"></span>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	if ($show_new && !empty($manga['last_link_created']) && $manga['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat!</div>'."\n";
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

function print_carousel_item_anime($anime, $tracking_class, $specific_version, $show_new=TRUE) {
	$base_url='https://anime.fansubs.cat';
	echo "\t\t\t\t\t\t\t".'<a class="thumbnail trackable-'.$tracking_class.'" data-anime-id="'.$anime['slug'].'" href="'.$base_url.'/'.($anime['type']=='movie' ? "films" : "series").'/'.$anime['slug'].(($specific_version && exists_more_than_one_version_anime($anime['id'])) ? "?v=".$anime['version_id'] : "").'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status-indicator" title="'.get_status_description($anime['best_status']).'"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img src="'.$base_url.'/images/series/'.$anime['id'].'.jpg" alt="'.$anime['name'].'" />'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="watchbutton">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<span class="fa fa-fw fa-play"></span>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	if ($show_new && !empty($anime['last_link_created']) && $anime['last_link_created']>=date('Y-m-d', strtotime("-1 week"))) {
		echo "\t\t\t\t\t\t\t\t\t".'<div class="new" title="Hi ha contingut publicat durant la darrera setmana">Novetat!</div>'."\n";
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
	echo "\t\t\t\t\t\t\t\t".'<div class="fansub">'.(strpos($anime['fansub_name'],"|")!==FALSE ? 'Diversos fansubs' : $anime['fansub_name']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>';
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
