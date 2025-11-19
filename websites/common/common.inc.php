<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__."/initialization.inc.php");
require_once(__DIR__.'/libraries/phpmailer/Exception.php');
require_once(__DIR__.'/libraries/phpmailer/PHPMailer.php');
require_once(__DIR__.'/libraries/phpmailer/SMTP.php');
require_once(__DIR__.'/libraries/parsedown.inc.php');

function is_adult(){
	global $user;
	return (!empty($user) && date_diff(date_create_from_format('Y-m-d H:i:s', $user['birthdate'].' 00:00:00'), date_create(date('Y-m-d').' 00:00:00'))->format('%Y')>=18);
}

function is_robot(){
	return !empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']);
}

function is_user_birthday(){
	global $user;
	return (!empty($user) && date_create_from_format('Y-m-d', $user['birthdate'])->format('m-d')==date('m-d'));
}

function get_user_age(){
	global $user;
	return date_diff(date_create_from_format('Y-m-d H:i:s', $user['birthdate'].' 00:00:00'), date_create(date('Y-m-d').' 00:00:00'))->format('%Y');
}

function force_hentai_logout() {
	global $user;
	$_SESSION = array();
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 60*60*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	setcookie('hentai_warning_accepted', '', time() - 60*60*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	$_COOKIE['hentai_warning_accepted']='';
	session_destroy();
	unset($GLOBALS['user']);
}

function validate_hentai() {
	global $user;
	if (SITE_IS_HENTAI && !empty($user) && !is_adult()) {
		force_hentai_logout();
	}
}

function validate_hentai_ajax() {
	global $user;
	if (SITE_IS_HENTAI && !empty($user) && !is_adult()) {
		force_hentai_logout();
	}
}

function get_redirect_from_referrer() {
	$unsafe_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$host = parse_url($unsafe_url, PHP_URL_HOST);
	if (str_ends_with($host, '.'.MAIN_DOMAIN) || str_ends_with($host, '.'.HENTAI_DOMAIN)) {
		return 'https://'.$host;
	}
	return MAIN_URL;
}

function send_email($recipient_address, $recipient_name, $subject, $text, $html_text=NULL) {
	$mail = new PHPMailer(true);

	try {
		$mail->CharSet = "UTF-8";
		$mail->isSMTP();
		$mail->Host = SMTP_HOST;
		$mail->SMTPAuth = true;
		$mail->Username = SMTP_USERNAME;
		$mail->Password = SMTP_PASSWORD;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = SMTP_PORT;

		//Recipients
		$mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
		$mail->addAddress($recipient_address, $recipient_name);

		//Content
		$mail->Subject = $subject;

		if (!empty($html_text)) {
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = $html_text;
			$mail->AltBody = $text;
		} else {
			$mail->isHTML(false);
			$mail->Body = $text;
		}

		$mail->send();
	} catch (Exception $e) {
		log_action('mail-error', "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n");
	}
}

function get_opposite_url() {
	$path = strtok($_SERVER["REQUEST_URI"], '?');
	if ($path==lang('url.fansubs') || $path==lang('url.privacy_policy') || $path==lang('url.contact_us') || $path==lang('url.my_list') || $path==lang('url.settings')
			|| $path==lang('url.edit_profile') || $path==lang('url.delete_profile') || $path==lang('url.change_password')) {
		return 'https://'.str_replace(CURRENT_DOMAIN,OTHER_DOMAIN,$_SERVER['HTTP_HOST']).$path;
	} else if (str_starts_with($path,lang('url.search'))) {
		return 'https://'.str_replace(CURRENT_DOMAIN,OTHER_DOMAIN,$_SERVER['HTTP_HOST']).lang('url.search');
	} else {
		return 'https://'.str_replace(CURRENT_DOMAIN,OTHER_DOMAIN,$_SERVER['HTTP_HOST']);
	}
}

function get_user_avatar_url($user) {
	if (!empty($user['avatar_filename'])) {
		return STATIC_URL.'/images/avatars/'.$user['avatar_filename'];
	}
	else if (!empty($user['fansub_id'])) {
		return STATIC_URL.'/images/icons/'.$user['fansub_id'].'.png';
	}
	else {
		return STATIC_URL.'/images/site/default_avatar.jpg';
	}
}

function get_nanoid($size=24, $alphabet='_-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
	//Adapted from: https://github.com/hidehalo/nanoid-php/blob/master/src/Core.php
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
	} else if (time()-$time<3600*24) {
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
	else {
		return get_custom_formatted_date($time);
	}
}

function get_custom_formatted_date($date) {
	if (SITE_LANGUAGE=='ca') {
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
	} else {
		return date(lang('date.short_format'));
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
			return lang('status.complete.public.short');
		case 2:
			return lang('status.inprogress.public.short');
		case 3:
			return lang('status.partiallycomplete.public.short');
		case 4:
			return lang('status.abandoned.public.short');
		case 5:
			return lang('status.cancelled.public.short');
		default:
			return lang('status.unknown.public.short');
	}
}

function get_status_description($id){
	switch ($id){
		case 1:
			return lang('status.complete.public.medium');
		case 2:
			return lang('status.inprogress.public.medium');
		case 3:
			return lang('status.partiallycomplete.public.medium');
		case 4:
			return lang('status.abandoned.public.medium');
		case 5:
			return lang('status.cancelled.public.medium');
		default:
			return lang('status.unknown.public.medium');
	}
}

function get_status_description_long($id){
	switch ($id){
		case 1:
			return lang('status.complete.public.long');
		case 2:
			return lang('status.inprogress.public.long');
		case 3:
			return lang('status.partiallycomplete.public.long');
		case 4:
			return lang('status.abandoned.public.long');
		case 5:
			return lang('status.cancelled.public.long');
		default:
			return lang('status.unknown.public.long');
	}
}

function get_status_css_icons($id){
	switch ($id){
		case 1:
			return "fa fa-fw fa-circle-check";
		case 2:
			return "fa fa-fw fa-circle-arrow-right";
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

function print_carousel_item($series, $specific_version, $use_version_param, $show_new=TRUE) {
	global $user;
	$versions = get_prepared_versions($series['fansub_info']);
	$number_of_versions = $series['total_versions'];
	echo "\t\t\t\t\t\t\t".'<div class="thumbnail-outer">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="thumbnail thumbnail-'.$series['id'].'" data-series-id="'.$series['id'].'" onmouseenter="prepareFloatingInfo(this);">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="versions">'.get_fansub_icons($series['fansub_info'], $versions, $specific_version ? $versions[0]['id'] : NULL).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<a class="image-link" href="'.get_base_url_from_type_and_rating($series['type'], $series['rating']).'/'.($specific_version ? $series['version_slug'] : $series['default_version_slug']).'"><img src="'.STATIC_URL.'/images/covers/version_'.($specific_version ? $series['version_id'] : $series['default_version_id']).'.jpg" alt="'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="clickable-thumbnail" onclick="prepareClickableFloatingInfo(this);"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="floating-info">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-main">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-bookmark '.(in_array($series['id'], !empty($user) ? $user['series_list_ids'] : array()) ? 'fas' : 'far').' fa-fw fa-bookmark" data-series-id="'.$series['id'].'" onclick="toggleBookmark('.$series['id'].'); event.stopPropagation(); return false;"></div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-title">'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-versions">'.get_carousel_fansub_info($series['fansub_info'], $versions, $specific_version ? $versions[0]['id'] : NULL).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-synopsis-wrapper">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line(($specific_version ? $series['version_synopsis'] : $series['default_version_synopsis']));

	echo "\t\t\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t\t".'<a class="floating-info-watch-now" href="'.get_base_url_from_type_and_rating($series['type'], $series['rating']).'/'.($specific_version ? $series['version_slug'] : $series['default_version_slug']).'" onclick="event.stopPropagation();">'.($series['type']=='manga' ? lang('catalogue.manga.read_now') : lang('catalogue.generic.watch_now')).'</a>'."\n";
	if ($series['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">'.($series['comic_type']=='novel' ? lang('catalogue.manga.light_novel') : lang('catalogue.manga.oneshot')).'</div>'."\n";
	} else if ($series['subtype']=='serialized') {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">'.($series['comic_type']=='novel' ? lang('catalogue.manga.light_novel') : lang('catalogue.manga.serialized.single')).' • '.($series['divisions']==1 ? lang('catalogue.manga.number_of_volumes_one.short') : sprintf(lang('catalogue.manga.number_of_volumes_more.short'), $series['divisions'])).' • '.($series['number_of_episodes']==1 ? lang('catalogue.manga.number_of_chapters_one') : sprintf(lang('catalogue.manga.number_of_chapters_more'), $series['number_of_episodes'])).'</div>'."\n";
	} else if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">'.sprintf(lang('catalogue.generic.number_of_chapters.movie'), $series['number_of_episodes']).'</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">'.lang('catalogue.generic.movie').'</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">'.lang('catalogue.generic.series.single').' • '.sprintf(lang('catalogue.generic.number_of_seasons.short'), $series['divisions']).' • '.sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes']).'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t\t\t".'<div class="floating-info-divisions">'.lang('catalogue.generic.series.single').' • '.($series['number_of_episodes']==1 ? lang('catalogue.generic.number_of_chapters_one') : sprintf(lang('catalogue.generic.number_of_chapters_more'), $series['number_of_episodes'])).'</div>'."\n";
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
	echo "\t\t\t\t\t\t\t\t\t".'<div class="ellipsized-title">'.htmlspecialchars(($specific_version ? $series['version_title'] : $series['default_version_title'])).'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</div>'."\n";
}

function get_base_url_from_type_and_rating($type, $rating) {
	if ($type=='liveaction'){
		return LIVEACTION_URL;
	} else if ($type=='anime'){
		return ANIME_URL;
	} else {
		return MANGA_URL;
	}
	die("Unknown type passed");
}

function get_special_day() {
	if (date('m-d')=='12-28' && !DISABLE_FOOLS_DAY) {
		return 'fools';
	} else if (date('m-d')=='04-23' && !DISABLE_SANT_JORDI_DAY) { // Sant Jordi
		return 'sant_jordi';
	} else if (date('m-d')>='10-31' && date('m-d')<='11-01' && !DISABLE_HALLOWEEN_DAYS) {
		return 'tots_sants';
	} else if (((date('m-d')>='12-05' && date('m-d')<='12-31') || (date('m-d')>='01-01' && date('m-d')<='01-06')) && !DISABLE_CHRISTMAS_DAYS) {
		return 'nadal';
	}
	return NULL;
}

function is_advent_days() {
	return strcmp(date('m-d H:i:s'),'12-01 12:00:00')>=0 && strcmp(date('m-d H:i:s'),'12-25 11:59:59')<=0 && !DISABLE_ADVENT;
}
?>
