<?php
require_once(__DIR__.'/queries.inc.php');

//Regexp used for determining types of links
const REGEXP_MEGA='/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_DL_LINK='/^https:\/\/(?:drive\.google\.com|mega\.nz|mega\.co\.nz).*/';
const REGEXP_STORAGE='/^storage:\/\/.*/';

function get_fansub_preposition_name($text){
	if (SITE_LANGUAGE=='ca') {
		$first = mb_strtoupper(substr($text, 0, 1));
		if (($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U') && substr($text, 0, 4)!='One '){ //Ugly...
			return "d’$text";
		}
	}
	return sprintf(lang('generic.preposition'), $text);
}

function get_rating($text){
	switch ($text){
		case 'TP':
			return lang('catalogue.generic.rating.all');
		case '+7':
			return lang('catalogue.generic.rating.7');
		case '+13':
			return lang('catalogue.generic.rating.13');
		case '+16':
			return lang('catalogue.generic.rating.16');
		case '+18':
			return lang('catalogue.generic.rating.18');
		case 'XXX':
			return lang('catalogue.generic.rating.explicit');
		default:
			return $text;
	}
}

function get_provider($links){
	$methods = array();
	foreach ($links as $link) {
		if (preg_match(REGEXP_MEGA,$link['url'])){
			array_push($methods, 'mega');
		} else if (preg_match(REGEXP_STORAGE,$link['url'])) {
			array_push($methods, 'storage');
		} else {
			array_push($methods, 'direct-video');
		}
	}
	$output = '';
	if (in_array('mega', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.=lang('catalogue.generic.source.mega');
	}
	if (in_array('direct-video', $methods) || in_array('storage', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.=lang('catalogue.generic.source.streaming');
	}
	return $output;
}

function get_storage_url($url, $clean=FALSE) {
	if (count(STORAGES)>0 && strpos($url, "storage://")===0) {
		$rand = rand(0, count(STORAGES)-1);
		if ($clean) {
			return str_replace("storage://", 'https://'.STORAGES[$rand].'/', $url);
		} else {
			return generate_storage_url(str_replace("storage://", 'https://'.STORAGES[$rand].'/', $url));
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

function filter_remote_files($files, $type) {
	if ($type=='audio') {
		$result = array();
		foreach ($files as $file) {
			if (preg_match('/.*\.(mp3|ogg)$/i', $file)) {
				array_push($result, $file);
			}
		}
		return $result;
	} else if ($type=='images') {
		$result = array();
		foreach ($files as $file) {
			if (preg_match('/.*\.(jpe?g|png)$/i', $file)) {
				array_push($result, $file);
			}
		}
		return $result;
	} else {
		return $files;
	}
}

function filter_links($links){
	$methods = array();
	$links_mega = array();
	$links_storage = array();
	$links_direct = array();
	foreach ($links as $link) {
		if (preg_match(REGEXP_MEGA,$link['url'])){
			array_push($links_mega, $link);
		} else if (preg_match(REGEXP_STORAGE,$link['url'])){
			array_push($links_storage, $link);
		} else {
			array_push($links_direct, $link);
		}
	}

	//This establishes the preferences order:
	//Storage > Direct video > MEGA

	if (count($links_storage)>0 && count(STORAGES)>0 && !DISABLE_STORAGE_STREAMING) {
		return $links_storage;
	}

	if (count($links_direct)>0) {
		return $links_direct;
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

function get_resolution_css($links){
	$resolution = str_replace('p', '', get_resolution_short($links));
	if ($resolution>=900) {
		return "hd1080";
	} else if ($resolution>=650) {
		return "hd720";
	} else {
		return "sd";
	}
}

function get_episode_player_title($fansub_name, $series_name, $series_subtype, $episode_title, $is_extra){
	if ($series_name==$episode_title || ($series_subtype==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID && !$is_extra)){
		if (!empty($episode_title)) {
			return $fansub_name . ' - ' . $episode_title;
		} else {
			return $fansub_name . ' - ' . $series_name;
		}
	} else {
		return $fansub_name . ' - ' . $series_name . ' - '. $episode_title;
	}
}

function get_episode_player_title_short($series_name, $series_subtype, $episode_title, $is_extra){
	if ($series_name==$episode_title || ($series_subtype==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID && !$is_extra)){
		if (!empty($episode_title)) {
			return $episode_title;
		} else {
			return $series_name;
		}
	} else {
		return $episode_title;
	}
}

function get_length_formatted($length){
	if (CATALOGUE_DURATION_SLIDER_FORMATTING=='pages') {
		return sprintf(lang('catalogue.manga.length_pages_short'), $length);
	}
	//Else, time:
	$secs = $length % 60;
	$mins = (int)($length / 60) % 60;
	$hrs = (int)($length / 3600);

	return ($hrs>0 ? $hrs.':' : '').($mins>9 ? $mins : '0'.$mins).':'.($secs>9 ? $secs : '0'.$secs);
}

function get_comic_type($comic_type){
	switch ($comic_type) {
		case 'manga':
			return lang('catalogue.manga.comic_type.manga');
		case 'manhwa':
			return lang('catalogue.manga.comic_type.manhwa');
		case 'manhua':
			return lang('catalogue.manga.comic_type.manhua');
		case 'novel':
			return lang('catalogue.manga.comic_type.light_novel');
		default:
			return lang('catalogue.manga.comic_type.comic');
	}
}

function get_type_depending_on_catalogue($series) {
	if ($series['type']=='manga') {
		return (CATALOGUE_ITEM_TYPE!='manga' ? get_comic_type($series['comic_type']).' • ' : '');
	} else if ($series['type']=='anime') {
		return (CATALOGUE_ITEM_TYPE!='anime' ? lang('catalogue.generic.type.anime').' • ' : '');
	} else {
		return (CATALOGUE_ITEM_TYPE!='liveaction' ? lang('catalogue.generic.type.liveaction').' • ' : '');
	}
}

function get_episode_title($series_subtype, $show_episode_numbers, $episode_number, $linked_episode_id, $title, $series_name, $extra_name, $is_extra) {
	if ($is_extra) {
		return $extra_name;
	}

	if ($show_episode_numbers && !empty($episode_number) && empty($linked_episode_id)) {
		if (!empty($title)){
			return sprintf(lang('catalogue.generic.episode_number'), str_replace('.',',',floatval($episode_number))).': '.$title;
		}
		else {
			return sprintf(lang('catalogue.generic.episode_number'), str_replace('.',',',floatval($episode_number)));
		}
	} else {
		if (!empty($title)){
			return $title;
		} else {
			return $series_name;
		}
	}
}

function get_episode_title_formatted($series_subtype, $show_episode_numbers, $episode_number, $linked_episode_id, $title, $series_name, $extra_name, $is_extra) {
	if ($is_extra) {
		return '<b>'.htmlspecialchars($extra_name).'</b>';
	}

	if ($show_episode_numbers && !empty($episode_number) && empty($linked_episode_id)) {
		if (!empty($title)){
			return '<b>'.sprintf(lang('catalogue.generic.episode_number'), str_replace('.',',',floatval($episode_number))).'</b><br>'.htmlspecialchars($title);
		}
		else {
			return '<b>'.sprintf(lang('catalogue.generic.episode_number'), str_replace('.',',',floatval($episode_number))).'</b>';
		}
	} else {
		if (!empty($title)){
			return '<b>'.htmlspecialchars($title).'</b>';
		} else {
			return '<b>'.htmlspecialchars($series_name).'</b>';
		}
	}
}

function print_episode($fansub_names, $row, $version_id, $series, $division_title, $version, $position){
	global $user;
	if (!empty($row['linked_episode_id'])) {
		$result = query_files_by_linked_episode_id_and_version_id(!empty($user) ? $user['id'] : -1, $row['linked_episode_id'], $version_id);
		$results = query_series_from_episode_id($row['linked_episode_id']);
		$series = mysqli_fetch_assoc($results);
		mysqli_free_result($results);
		$resultv=query_version_from_linked_episode_id_and_version_id($row['linked_episode_id'], $version_id);
		$version = mysqli_fetch_assoc($resultv);
		$fansub_names = $version['fansub_name'];
		mysqli_free_result($resultv);
	} else {
		$result = query_files_by_episode_id_and_version_id(!empty($user) ? $user['id'] : -1, $row['id'], $version_id);
	}

	if (mysqli_num_rows($result)==0){
		return;
	}

	$episode_title=get_episode_title($series['subtype'], $version['show_episode_numbers'],$row['number'],$row['linked_episode_id'],$row['title'],$series['name'], NULL, FALSE);
	$episode_title_formatted=get_episode_title_formatted($series['subtype'], $version['show_episode_numbers'],$row['number'],$row['linked_episode_id'],$row['title'],$series['name'], NULL, FALSE);

	internal_print_episode($fansub_names, $episode_title, $episode_title_formatted, $result, $series, $division_title, FALSE, $position, $row['number'], $version_id);
	mysqli_free_result($result);
}

function print_extra($fansub_names, $row, $version_id, $series, $division_title, $position){
	global $user;
	$result = query_extras_files_by_extra_name_and_version_id(!empty($user) ? $user['id'] : -1, $row['extra_name'], $version_id);

	$episode_title=get_episode_title($series['subtype'], NULL,NULL,NULL,NULL,NULL,$row['extra_name'], TRUE);
	$episode_title_formatted=get_episode_title_formatted($series['subtype'], NULL,NULL,NULL,NULL,NULL,$row['extra_name'], TRUE);
	
	internal_print_episode($fansub_names, $episode_title, $episode_title_formatted, $result, $series, $division_title, TRUE, $position, NULL, $version_id);
	mysqli_free_result($result);
}

function internal_print_episode($fansub_names, $episode_title, $episode_title_formatted, $result, $series, $division_title, $is_extra, $position, $number, $version_id) {
	global $user;
	//TABLE FORMAT: thumbnail, episode title + other data, seen
	$num_variants = mysqli_num_rows($result);
	if ($num_variants==0){ //Episode not available at all
?>
<div class="episode episode-unavailable">
	<div class="episode-thumbnail-cell">
		<div class="episode-thumbnail">
			<div class="play-button fa fa-fw fa-ban"></div>
		</div>
	</div>
	<div class="episode-title-cell">
		<div class="episode-title"><?php echo $episode_title_formatted; ?></div>
	</div>
	<div class="episode-info-seen-cell"></div>
</div>
<?php
	} else {
		//Iterate all variants
		while ($vrow = mysqli_fetch_assoc($result)){
			if ($vrow['is_lost']==0) {
				if ($series['type']!='manga') {
					$links = array();
					$resulti = query_links_by_file_id($vrow['id']);
					while ($lirow = mysqli_fetch_assoc($resulti)){
						array_push($links, $lirow);
					}
					mysqli_free_result($resulti);
					$links = filter_links($links);
				}
?>
<div class="file-launcher episode" data-episode-id="<?php echo $vrow['episode_id']; ?>" data-file-id="<?php echo $vrow['id']; ?>" data-title="<?php echo htmlspecialchars(get_episode_player_title($fansub_names, $division_title, $series['subtype'], $episode_title, $is_extra)); ?>" data-title-short="<?php echo htmlspecialchars(get_episode_player_title_short($division_title, $series['subtype'], $episode_title, $is_extra)); ?>" data-thumbnail="<?php echo file_exists(STATIC_DIRECTORY.'/images/files/'.$vrow['id'].'.jpg') ? STATIC_URL.'/images/files/'.$vrow['id'].'.jpg' : STATIC_URL.'/images/covers/version_'.$version_id.'.jpg'; ?>" data-position="<?php echo $position; ?>" data-is-special="<?php echo ($is_extra || empty($number)) ? 'true' : 'false'; ?>">
	<div class="episode-thumbnail-cell">
		<div class="episode-thumbnail">
			<img src="<?php echo file_exists(STATIC_DIRECTORY.'/images/files/'.$vrow['id'].'.jpg') ? STATIC_URL.'/images/files/'.$vrow['id'].'.jpg' : STATIC_URL.'/images/covers/version_'.$version_id.'.jpg'; ?>" alt="">
			<div class="length"><?php echo get_length_formatted($vrow['length']); ?></div>
			<span class="progress" style="width: <?php echo $vrow['progress_percent']*100; ?>%;"></span>
			<div class="play-button fa fa-fw <?php echo $series['type']=='manga' ? 'fa-book-open' : 'fa-play'; ?>"></div>
		</div>
	</div>
	<div class="episode-title-cell">
		<div class="episode-title">
			<?php echo $episode_title_formatted; ?><?php echo $num_variants>1 ? '<br><b>'.htmlspecialchars($vrow['variant_name']).'</b>': ''; ?>
<?php
				if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
?>
			<span class="new-episode tooltip<?php echo (!empty($user) && $vrow['is_seen']==1) ? ' hidden' : ''; ?>" data-file-id="<?php echo $vrow['id']; ?>" title="<?php echo lang('catalogue.generic.published_recently'); ?>"><span class="fa fa-fw fa-certificate"></span></span>
<?php
				}
?>
		</div>
	</div>
	<div class="episode-info-seen-cell">
		<div class="episode-info">
<?php
				if ($series['type']!='manga') {
?>
			<span class="version-resolution <?php echo get_resolution_css($links); ?> tooltip tooltip-right" title="<?php echo sprintf(lang('catalogue.generic.video_and_service'), get_resolution($links), get_provider($links)); ?>"><?php echo htmlspecialchars(get_resolution_short($links)); ?></span>
<?php
				}
				if (!empty($vrow['comments'])){
?>
			<span class="version-info tooltip" title="<?php echo str_replace("\n", "<br>", htmlspecialchars($vrow['comments'])); ?>"><span class="fa fa-fw fa-info"></span></span>
<?php
				}
				if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
?>
			<span class="new-episode tooltip<?php echo (!empty($user) && $vrow['is_seen']==1) ? ' hidden' : ''; ?>" data-file-id="<?php echo $vrow['id']; ?>" title="<?php echo lang('catalogue.generic.published_recently'); ?>"><span class="fa fa-fw fa-certificate"></span></span>
<?php
				}
?>
		</div>
		<label class="switch" onclick="event.stopPropagation();">
			<input type="checkbox"<?php echo (!empty($user) && $vrow['is_seen']==1) ? ' checked' : ''; ?> onchange="toggleFileSeen(this, <?php echo $vrow['id']; ?>);">
			<span class="viewed-slider"></span>
		</label>
	</div>
</div>
<?php
			} else { //Lost file
?>
<div class="episode episode-unavailable">
	<div class="episode-thumbnail-cell">
		<div class="episode-thumbnail">
			<div class="play-button fa fa-fw fa-ghost version-lost" title="<?php echo lang('catalogue.generic.lost_episode'); ?>"></div>
		</div>
	</div>
	<div class="episode-title-cell">
		<div class="episode-title"><?php echo $episode_title_formatted; ?></div>
	</div>
	<div class="episode-info-seen-cell"></div>
</div>
<?php
			}
		}
	}
}

function print_fake_episode($status) {
?>
<div class="episode episode-message">
	<div class="version-status-explanation">
<?php
	echo '<div class="version-status status-'.get_status($status).' '.get_status_css_icons($status).'" title="'.htmlspecialchars(get_status_description($status)).'"></div>';
	echo '<div class="version-status-explanation-text">'.get_status_description_long($status).'</div>';
?>
	</div>
</div>
<?php
}

function get_recommended_fansub_info($fansub_info, $versions, $specific_version_id) {
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

	foreach ($versions[0]['fansubs'] as $fansub) {
		$result_code.='<div class="fansub">'.($fansub['type']=='fandub' ? '<i class="fa fa-fw fa-microphone"></i>' : '').'<span class="text">'.htmlspecialchars($fansub['name']).'</span> <img src="'.$fansub['icon'].'" alt=""></div>'."\n";
	}

	return $result_code;
}

function print_chapter_item($row) {
	$subtitle = '';
	if (!empty($row['extra_name'])) {
		$subtitle .= lang('catalogue.generic.extra_prefix_short').$row['extra_name'];
	} else{
		if (!empty($row['division_name'])) {
			$subtitle .= $row['division_name'];
		}
		if ($subtitle!='') {
			$subtitle .= ' • ';
		}
		$subtitle .= $row['episode_title'];
	}
?>
	<div class="continue-watching-thumbnail-outer">
		<div class="continue-watching-thumbnail">
			<a class="image-link" href="<?php echo SITE_BASE_URL.'/'.$row['series_slug']."?f=".$row['file_id']; ?>">
				<div class="versions"><?php echo get_fansub_icons($row['fansub_info'], get_prepared_versions($row['fansub_info']), $row['version_id']); ?></div>
				<img src="<?php echo file_exists(STATIC_DIRECTORY.'/images/files/'.$row['file_id'].'.jpg') ? STATIC_URL.'/images/files/'.$row['file_id'].'.jpg' : STATIC_URL.'/images/covers/version_'.$row['version_id'].'.jpg'; ?>" alt="">
				<div class="length"><?php echo get_length_formatted($row['length']); ?></div>
				<span class="progress" style="width: <?php echo $row['progress_percent']*100; ?>%;"></span>
				<div class="play-button fa fa-fw <?php echo CATALOGUE_PLAY_BUTTON_ICON; ?>"></div>
				<div class="close-button fa fa-fw fa-times" onclick="removeFromContinueWatching(this, <?php echo $row['file_id']; ?>); return false;"></div>
			</a>
		</div>
		<a class="continue-watching-episode-data" href="<?php echo SITE_BASE_URL.'/'.$row['series_slug']; ?>">
			<span class="title">
				<?php echo htmlspecialchars($row['series_name']); ?>
			</span>
			<span class="subtitle">
				<?php echo htmlspecialchars($subtitle); ?>
			</span>
		</a>
	</div>
<?php
}

function get_relative_date_last_update($time) {
	if (time()-$time<60) {
		return lang('date.now');
	}
	if (time()-$time<3600) {
		$minutes = intval((time()-$time)/60);
		if ($minutes==1) {
			return lang('date.minute_ago');
		} else {
			return sprintf(lang('date.minutes_ago'), $minutes);
		}
	}
	else if (time()-$time<3600*24) {
		$hours = intval((time()-$time)/3600);
		if ($hours==1) {
			return lang('date.hour_ago');
		} else {
			return sprintf(lang('date.hours_ago'), $hours);
		}
	}
	else if (time()-$time<3600*24*30) {
		$days = intval((time()-$time)/(3600*24));
		if ($days==1) {
			return lang('date.day_ago');
		} else {
			return sprintf(lang('date.days_ago'), $days);
		}
	}
	else if (time()-$time<3600*24*30*12) {
		$months = intval((time()-$time)/(3600*24*30));
		if ($months==1) {
			return lang('date.month_ago');
		} else {
			return sprintf(lang('date.months_ago'), $months);
		}
	}
	else {
		$years = intval((time()-$time)/(3600*24*365));
		if ($years==1) {
			return lang('date.year_ago');
		} else {
			return sprintf(lang('date.years_ago'), $years);
		}
	}
}

function print_chapter_item_last_update($row) {
	if ($row['cnt']>1) {
		$link_url = SITE_BASE_URL.'/'.$row['series_slug'];
		$image_url = STATIC_URL.'/images/covers/version_'.$row['version_id'].'.jpg';
		$subtitle = $row['cnt'].' elements nous';
	} else {
		$link_url = SITE_BASE_URL.'/'.$row['series_slug']."?f=".$row['file_id'];
		$image_url = file_exists(STATIC_DIRECTORY.'/images/files/'.$row['file_id'].'.jpg') ? STATIC_URL.'/images/files/'.$row['file_id'].'.jpg' : STATIC_URL.'/images/covers/version_'.$row['version_id'].'.jpg';
		$subtitle = '';
		if (!empty($row['extra_name'])) {
			$subtitle .= lang('catalogue.generic.extra_prefix_short').$row['extra_name'];
		} else{
			if (!empty($row['division_name'])) {
				$subtitle .= $row['division_name'];
			}
			if ($subtitle!='') {
				$subtitle .= ' • ';
			}
			$subtitle .= $row['episode_title'];
		}
	}
?>
	<div class="continue-watching-thumbnail-outer">
		<div class="continue-watching-thumbnail">
			<a class="image-link" href="<?php echo $link_url; ?>">
				<div class="versions"><?php echo get_fansub_icons($row['fansub_info'], get_prepared_versions($row['fansub_info']), $row['version_id']); ?></div>
				<img src="<?php echo $image_url; ?>" alt="">
				<div class="date"><?php echo get_relative_date_last_update($row['file_created']); ?></div>
				<div class="play-button fa fa-fw <?php echo CATALOGUE_PLAY_BUTTON_ICON; ?>"></div>
			</a>
		</div>
		<a class="continue-watching-episode-data" href="<?php echo SITE_BASE_URL.'/'.$row['series_slug']; ?>">
			<span class="title">
				<?php echo htmlspecialchars($row['series_name']); ?>
			</span>
			<span class="subtitle">
				<?php echo htmlspecialchars($subtitle); ?>
			</span>
		</a>
	</div>
<?php
}

function get_genres_for_featured($genre_names, $type, $rating) {
	if (empty($genre_names)) {
		return "";
	}
	$genres_array = explode(' • ',$genre_names);
	$result_code = '';

	foreach ($genres_array as $genre_data) {
		$genre_id = explode('|', $genre_data)[0];
		$genre_type = explode('|', $genre_data)[1];
		$genre = explode('|', $genre_data)[2];
		$result_code.='<a class="genre" href="'.get_base_url_from_type_and_rating($type,$rating).lang('url.search').'?'.($genre_type=='demographics' ? 'demographics' : 'genres_include').'%5B%5D='.$genre_id.'">'.htmlspecialchars($genre).'</a>';
	}
	return '<i class="fa fa-fw fa-tag fa-flip-horizontal" title="'.lang('catalogue.generic.tags').'"></i> '.$result_code;
}

function print_featured_item($series, $special_day=NULL, $specific_version=TRUE, $use_version_param=TRUE, $show_special_day=TRUE) {
	$versions = get_prepared_versions($series['fansub_info']);
	echo "\t\t\t\t\t\t\t".'<div class="recommendation" data-series-id="'.$series['id'].'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img class="background" src="'.STATIC_URL.'/images/featured/version_'.$series['version_id'].'.jpg" alt="'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status" title="'.get_status_description($series['best_status']).'"><div class="status-indicator '.get_status_css_icons($series['best_status']).'"></div><span class="text">'.get_status_description_short($series['best_status']).'</span></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder" data-swiper-parallax="-30%">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="coverholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a href="'.get_base_url_from_type_and_rating($series['type'],$series['rating']).'/'.($specific_version ? $series['version_slug'] : $series['default_version_slug']).'"><img class="cover" src="'.STATIC_URL.'/images/covers/version_'.($specific_version ? $series['version_id'] : $series['default_version_id']).'.jpg" alt="'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="dataholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="title">'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'</div>'."\n";
	if ($series['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.($series['comic_type']=='novel' ? lang('catalogue.manga.light_novel') : lang('catalogue.manga.oneshot')).'</div>'."\n";
	} else if ($series['subtype']=='serialized') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.($series['comic_type']=='novel' ? lang('catalogue.manga.light_novel') : lang('catalogue.manga.serialized.single')).' • '.($series['divisions']==1 ? lang('catalogue.manga.number_of_volumes_one') : sprintf(lang('catalogue.manga.number_of_volumes_more'), $series['divisions'])).' • '.($series['number_of_episodes']==1 ? lang('catalogue.manga.number_of_chapters_one') : sprintf(lang('catalogue.manga.number_of_chapters_more'), $series['number_of_episodes'])).'</div>'."\n";
	} else if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.sprintf(lang('catalogue.generic.number_of_chapters.movie'), $series['number_of_episodes']).'</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.lang('catalogue.generic.movie').'</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.lang('catalogue.generic.series.single').' • '.sprintf(lang('catalogue.generic.number_of_seasons'), $series['divisions']).' • '.sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes']).'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.lang('catalogue.generic.series.single').' • '.($series['number_of_episodes']==1 ? lang('catalogue.generic.number_of_chapters_one') : sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes'])).'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line(($specific_version ? $series['version_synopsis'] : $series['default_version_synopsis']));

	echo "\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a class="watchbutton" href="'.get_base_url_from_type_and_rating($series['type'],$series['rating']).'/'.($specific_version ? $series['version_slug'] : $series['default_version_slug']).'">'.($series['type']=='manga' ? lang('catalogue.manga.read_now') : lang('catalogue.generic.watch_now')).'</a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansubs">'.get_recommended_fansub_info($series['fansub_info'], $versions, $versions[0]['id']).'</div>'."\n";
	if (!empty($special_day) && $show_special_day) {
		if ($special_day=='fools') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-trophy"></i><span class="text">'.lang('catalogue.featured.fools_day').'</span></div>'."\n";
		} else if ($special_day=='sant_jordi') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-heart"></i><span class="text">'.lang('catalogue.featured.sant_jordi').'</span></div>'."\n";
		} if ($special_day=='tots_sants') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-ghost"></i><span class="text">'.lang('catalogue.featured.halloween').'</span></div>'."\n";
		}
	} else if ($series['featurable_status']==3) {
		echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw '.CATALOGUE_SEASONAL_SERIES_ICON.'"></i><span class="text">'.CATALOGUE_SEASONAL_SERIES_STRING.'</span></div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="genres">'.get_genres_for_featured($series['genre_names'], $series['type'], $series['rating']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</div>'."\n";
}

function print_featured_item_single($series, $specific_version=TRUE, $use_version_param=TRUE) {
	$versions = get_prepared_versions($series['fansub_info']);
	echo "\t\t\t\t\t\t\t".'<a class="recommendation single-feature" data-series-id="'.$series['id'].'" href="'.get_base_url_from_type_and_rating($series['type'],$series['rating']).'/'.($specific_version ? $series['version_slug'] : $series['default_version_slug']).'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img class="background" src="'.STATIC_URL.'/images/featured/version_'.($specific_version ? $series['version_id'] : $series['default_version_id']).'.jpg" alt="'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="gradient"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="coverholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<img class="cover" src="'.STATIC_URL.'/images/covers/version_'.($specific_version ? $series['version_id'] : $series['default_version_id']).'.jpg" alt="'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="dataholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="title">'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'</div>'."\n";
	if ($series['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.($series['comic_type']=='novel' ? lang('catalogue.manga.light_novel') : lang('catalogue.manga.oneshot')).'</div>'."\n";
	} else if ($series['subtype']=='serialized') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.($series['comic_type']=='novel' ? lang('catalogue.manga.light_novel') : lang('catalogue.manga.serialized.single')).' • '.($series['divisions']==1 ? lang('catalogue.manga.number_of_volumes_one') : sprintf(lang('catalogue.manga.number_of_volumes_more'), $series['divisions'])).' • '.($series['number_of_episodes']==1 ? lang('catalogue.manga.number_of_chapters_one') : sprintf(lang('catalogue.manga.number_of_chapters_more'), $series['number_of_episodes'])).'</div>'."\n";
	} else if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.sprintf(lang('catalogue.generic.number_of_chapters.movie'), $series['number_of_episodes']).'</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.lang('catalogue.generic.movie').'</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.lang('catalogue.generic.series.single').' • '.sprintf(lang('catalogue.generic.number_of_seasons'), $series['divisions']).' • '.sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes']).'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.lang('catalogue.generic.series.single').' • '.($series['number_of_episodes']==1 ? lang('catalogue.generic.number_of_chapters_one') : sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes'])).'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line(($specific_version ? $series['version_synopsis'] : $series['default_version_synopsis']));

	echo "\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</a>'."\n";
}

function get_current_advent_day() {
	if (date('H:i:s')>='12:00:00') {
		return intval(date('d'));
	} else {
		return intval(date('d'))-1;
	}
}

function print_featured_advent() {
	echo "\t\t\t\t\t\t\t".'<div class="recommendation advent-recommendation" data-series-id="special-advent">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img class="background" src="'.STATIC_URL.'/images/advent/header_'.date('Y').'.jpg" alt="'.sprintf(lang('catalogue.featured.advent.title'), date('Y')).'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder" data-swiper-parallax="-30%">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="coverholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a href="'.ADVENT_URL.'"><img class="cover" src="'.STATIC_URL.'/images/advent/background_'.date('Y').'.jpg" alt="'.sprintf(lang('catalogue.featured.advent.title'), date('Y')).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="dataholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="title">'.sprintf(lang('catalogue.featured.advent.title'), date('Y')).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">'.sprintf(lang('catalogue.featured.advent.day'), get_current_advent_day()).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".lang('catalogue.featured.advent.description')."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a class="watchbutton" href="'.ADVENT_URL.'">'.lang('catalogue.featured.advent.go_button').'</a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</div>'."\n";
}

function print_comment($comment, $hidden){
	global $user;
	echo "\t\t\t\t\t\t\t\t\t".'<div class="comment'.(!empty($comment['reply_to_comment_id']) ? ' reply' : '').($hidden ? ' hidden' : '').'" data-episode-id="'.$comment['last_seen_episode_id'].'" data-version-id="'.$comment['version_id'].'">';
	echo "\t\t\t\t\t\t\t\t\t\t".'<img class="comment-avatar" src="'.(!empty($comment['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$comment['avatar_filename'] : ($comment['type']=='fansub' ? STATIC_URL.'/images/icons/'.$comment['fansub_id'].'.png' : ($comment['type']=='admin' ? STATIC_URL.'/images/site/default_fansub.png' : STATIC_URL.'/images/site/default_avatar.jpg'))).'">';
	if ($comment['has_spoilers']==1 && (empty($user) || $comment['user_id']!=$user['id'])) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="comment-message comment-with-spoiler'.($comment['is_seen_by_user']==1 ? ' comment-with-spoiler-shown' : '').'">';
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="comment-spoiler-warning'.($comment['is_seen_by_user']==1 ? ' hidden' : '').'"><span class="fa fa-warning"></span> '.lang('catalogue.comment.contains_spoilers').'<span class="spoiler-show-button"> • <a onclick="toggleCommentSpoiler(this);">'.lang('catalogue.comment.show_anyway').'</a></span></div>';
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="comment-message">';
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="comment-text">'.(!empty($comment['text']) ? str_replace("\n", "<br>", htmlentities($comment['text'])) : '<i>'.lang('catalogue.comment.comment_deleted').'</i>')."</div>";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="comment-author">';
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="comment-user">'.(!empty($comment['username']) ? htmlentities($comment['username']) : ($comment['type']=='fansub' ? htmlentities($comment['fansub_name']) : ($comment['type']=='admin' ? CURRENT_SITE_NAME : lang('catalogue.comment.user_deleted')))).'</span> • <span class="comment-date">'.get_relative_date($comment['created_timestamp']).'</span>';
	if (!empty($comment['episode_title'])) {
		echo ' • <span class="comment-episode">' . htmlentities($comment['episode_title']).'</span>';
	}
	if ($comment['has_spoilers']==1) {
		echo ' <span class="fa fa-warning" title="'.lang('catalogue.comment.marked_by_user_as_spoiler').'"></span>';
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'</div>';
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>';
	echo "\t\t\t\t\t\t\t\t\t".'</div>';
}

function print_comment_home($comment){
	global $user;
	echo "\t\t\t\t\t\t\t\t\t".'<div class="comment" data-episode-id="'.$comment['last_seen_episode_id'].'" data-version-id="'.$comment['version_id'].'">';
	echo "\t\t\t\t\t\t\t\t\t\t".'<a href="'.SITE_BASE_URL.'/'.$comment['version_slug'].'"><img class="comment-series" src="'.STATIC_URL.'/images/covers/version_'.$comment['version_id'].'.jpg"></a>';
	echo "\t\t\t\t\t\t\t\t\t\t".'<img class="comment-avatar" src="'.(!empty($comment['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$comment['avatar_filename'] : ($comment['type']=='fansub' ? STATIC_URL.'/images/icons/'.$comment['fansub_id'].'.png' : ($comment['type']=='admin' ? STATIC_URL.'/images/site/default_fansub.png' : STATIC_URL.'/images/site/default_avatar.jpg'))).'">';
	if ($comment['has_spoilers']==1 && (empty($user) || $comment['user_id']!=$user['id'])) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="comment-message comment-with-spoiler'.($comment['is_seen_by_user']==1 ? ' comment-with-spoiler-shown' : '').'">';
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="comment-message">';
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="comment-author">';
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<span class="comment-user">'.(!empty($comment['username']) ? htmlentities($comment['username']) : ($comment['type']=='fansub' ? htmlentities($comment['fansub_name']) : ($comment['type']=='admin' ? CURRENT_SITE_NAME : lang('catalogue.comment.user_deleted')))).'</span>'.(!empty($comment['replied_username']) ? sprintf(lang('catalogue.comment.in_reply_to'), htmlentities($comment['replied_username'])) : '').lang('catalogue.comment.in').'<a href="'.SITE_BASE_URL.'/'.$comment['version_slug'].'"><strong>'.$comment['version_title'].'</strong> '.get_fansub_preposition_name($comment['fansubs']).'</a> • <span class="comment-date"><a href="'.SITE_BASE_URL.'/'.$comment['version_slug'].'#'.lang('url.comments_anchor').'">'.get_relative_date($comment['created_timestamp']).'</a></span>';
	if (!empty($comment['episode_title'])) {
		echo ' • <span class="comment-episode">' . htmlentities($comment['episode_title']).'</span>';
	}
	if ($comment['has_spoilers']==1) {
		echo ' <span class="fa fa-warning" title="'.lang('catalogue.comment.marked_by_user_as_spoiler').'"></span>';
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'</div>';
	if ($comment['has_spoilers']==1 && (empty($user) || $comment['user_id']!=$user['id'])) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="comment-spoiler-warning'.($comment['is_seen_by_user']==1 ? ' hidden' : '').'"><span class="fa fa-warning"></span> '.lang('catalogue.comment.contains_spoilers').'<span class="spoiler-show-button"> • <a onclick="toggleCommentSpoiler(this);">'.lang('catalogue.comment.show_anyway').'</a></span></div>';
	}
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="comment-text">'.(!empty($comment['text']) ? str_replace("\n", "<br>", htmlentities($comment['text'])) : '<i>'.lang('catalogue.comment.comment_deleted').'</i>')."</div>";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>';
	echo "\t\t\t\t\t\t\t\t\t".'</div>';
}

function is_mobile_user_agent($user_agent) {
	return preg_match('/android|bb\d+|meego|mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$user_agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($user_agent,0,4));
}

function get_view_source_type($user_agent, $is_casted) {
	if ($is_casted) {
		return 'cast';
	}
	if(preg_match('/\[via API\]/', $user_agent)){
		return 'api';
	}
	if(is_mobile_user_agent($user_agent)){
		return 'mobile';
	}
	return 'desktop';
}

function is_fansub_blacklisted($fansub_id) {
	global $user;
	return ((!empty($user) && in_array($fansub_id, $user['blacklisted_fansub_ids'])) || (empty($user) && in_array($fansub_id, get_cookie_blacklisted_fansub_ids())));
}

function is_any_fansub_blacklisted($versions, $version_id) {
	foreach ($versions as $version) {
		if ($version['id']==$version_id) {
			foreach ($version['fansubs'] as $fansub) {
				if (is_fansub_blacklisted($fansub['id'])) {
					return TRUE;
				}
			}
			return FALSE;
		}
	}
	return FALSE;
}
?>
