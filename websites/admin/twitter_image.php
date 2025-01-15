<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/../../common/libraries/linebreaks4imagettftext.php');

ob_start();

define('IMAGE_WIDTH', 1200);
define('IMAGE_HEIGHT', 675);
define('COVER_WIDTH', 82);
define('COVER_HEIGHT', 116);
define('LOGO_WIDTH', 178);
define('LOGO_HEIGHT', 50);
define('FANSUB_LOGO_WIDTH', 50);
define('FANSUB_LOGO_HEIGHT', 50);
define('FONT_REGULAR', STATIC_DIRECTORY.'/fonts/lexend_deca_regular.ttf');
define('FONT_BOLD', STATIC_DIRECTORY.'/fonts/lexend_deca_bold.ttf');
define('FONT_LIGHT', STATIC_DIRECTORY.'/fonts/lexend_deca_light.ttf');
define('FONT_NUMBERS', STATIC_DIRECTORY.'/fonts/lexend_deca_numbers.ttf');

function scale_smallest_side($image, $desired_width, $desired_height) {
	$width = imagesx($image);
	$height = imagesy($image);

	if ($width/$height < $desired_width/$desired_height) {
		$output_width = $desired_width;
		$output_height = $desired_width*$height/$width;
		$image = imagescale($image, $output_width, $output_height, IMG_QUADRATIC);
		$image = imagecrop($image, ['x' => 0, 'y' => ($output_height-$desired_height)/2, 'width' => $desired_width, 'height' => $desired_height]);
	} else {
		$output_width = $desired_height*$width/$height;
		$output_height = $desired_height;
		$image = imagescale($image, $output_width, $output_height, IMG_QUADRATIC);
		$image = imagecrop($image, ['x' => ($output_width-$desired_width)/2, 'y' => 0, 'width' => $desired_width, 'height' => $desired_height]);
	}
	
	return $image;
}

function get_change_in_position_for_series($current_month, $id, $new_position, $series_previous_month) {
	$max_pos = 1;
	foreach ($series_previous_month as $series) {
		if ($series['position']>$max_pos) {
			$max_pos=$series['position'];
		}
		if ($series['id']==$id){
			return $series['position']-$new_position;
		}
	}
	$result = query("SELECT IFNULL(MIN(f.created),'".STARTING_DATE." 00:00:00') min FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.id=$id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	if ($row['min']>=($current_month.'-01 00:00:00')) {
		return "N/A";
	} else {
		return $max_pos;
	}
}

function get_change_in_views_for_series($current_month, $id, $new_views, $series_previous_month) {
	foreach ($series_previous_month as $series) {
		if ($series['id']==$id){
			return $new_views-$series['max_views'];
		}
	}
	$result = query("SELECT IFNULL(MIN(f.created),'".STARTING_DATE." 00:00:00') min FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.id=$id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	if ($row['min']>=($current_month.'-01 00:00:00')) {
		return "N/A";
	} else {
		return 1;
	}
}

function round_corners($source, $radius) {
	$ws = imagesx($source);
	$hs = imagesy($source);

	$corner = $radius + 2;
	$s = $corner*2;

	$src = imagecreatetruecolor($s, $s);
	imagecopy($src, $source, 0, 0, 0, 0, $corner, $corner);
	imagecopy($src, $source, $corner, 0, $ws - $corner, 0, $corner, $corner);
	imagecopy($src, $source, $corner, $corner, $ws - $corner, $hs - $corner, $corner, $corner);
	imagecopy($src, $source, 0, $corner, 0, $hs - $corner, $corner, $corner);

	$q = 8; # change this if you want
	$radius *= $q;

	# find unique color
	do {
		$r = rand(0, 255);
		$g = rand(0, 255);
		$b = rand(0, 255);
	} while (imagecolorexact($src, $r, $g, $b) < 0);

	$ns = $s * $q;

	$img = imagecreatetruecolor($ns, $ns);
	$alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
	imagealphablending($img, false);
	imagefilledrectangle($img, 0, 0, $ns, $ns, $alphacolor);

	imagefill($img, 0, 0, $alphacolor);
	imagecopyresampled($img, $src, 0, 0, 0, 0, $ns, $ns, $s, $s);
	imagedestroy($src);

	imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
	imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
	imagearc($img, $ns - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
	imagefilltoborder($img, $ns - 1, 0, $alphacolor, $alphacolor);
	imagearc($img, $radius - 1, $ns - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
	imagefilltoborder($img, 0, $ns - 1, $alphacolor, $alphacolor);
	imagearc($img, $ns - $radius, $ns - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
	imagefilltoborder($img, $ns - 1, $ns - 1, $alphacolor, $alphacolor);
	imagealphablending($img, true);
	imagecolortransparent($img, $alphacolor);

	# resize image down
	$dest = imagecreatetruecolor($s, $s);
	imagealphablending($dest, false);
	imagefilledrectangle($dest, 0, 0, $s, $s, $alphacolor);
	imagecopyresampled($dest, $img, 0, 0, 0, 0, $s, $s, $ns, $ns);
	imagedestroy($img);

	# output image
	imagealphablending($source, false);
	imagecopy($source, $dest, 0, 0, 0, 0, $corner, $corner);
	imagecopy($source, $dest, $ws - $corner, 0, $corner, 0, $corner, $corner);
	imagecopy($source, $dest, $ws - $corner, $hs - $corner, $corner, $corner, $corner, $corner);
	imagecopy($source, $dest, 0, $hs - $corner, 0, $corner, $corner, $corner);
	imagealphablending($source, true);
	imagedestroy($dest);

	return $source;
}

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if ((!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) || $_GET['token']==INTERNAL_TOKEN) {

	$type = escape($_GET['type']);
	$first_month = escape($_GET['first_month']);
	$last_month = escape($_GET['last_month']);
	$mode = escape($_GET['mode']);
	$is_hentai = !empty($_GET['is_hentai']);
	if (!empty($_GET['fansub_id'])) {
		$resultf = query("SELECT * FROM fansub WHERE id=".escape($_GET['fansub_id']));
		$fansub = mysqli_fetch_assoc($resultf);
		mysqli_free_result($resultf);
	} else {
		$fansub = NULL;
	}

	switch($mode) {
		case 'all':
			$first_previous_month = $first_month;
			$last_previous_month = $last_month;
			break;
		case 'year':
			$first_previous_month = date('Y-m', strtotime($first_month.'-01 first day of -1 year'));
			$last_previous_month = date('Y-m', strtotime($last_month.'-01 last day of -1 year'));
			break;
		case 'month':
		default:
			$first_previous_month = date('Y-m', strtotime($first_month.'-01 first day of -1 month'));
			$last_previous_month = date('Y-m', strtotime($last_month.'-01 last day of -1 month'));
			break;
	}

	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, b.default_version_id, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.default_version_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.default_version_id, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_month-01' AND vi.day<='$last_month-31' AND vi.views>0".($is_hentai ? " AND s.rating='XXX'" : " AND s.rating<>'XXX'")." AND f.episode_id IS NOT NULL AND s.type='$type'".(!empty($fansub) ? " AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")" : '')." GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, total_length DESC, b.series_name ASC LIMIT 10");

	$result_previous_month = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, b.default_version_id, IFNULL(MAX(b.total_views),0) max_views, SUM(b.total_length) total_length, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.default_version_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.total_length) total_length, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.total_length) total_length, f.version_id, f.episode_id, s.id series_id, defv.title series_name, s.default_version_id, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id LEFT JOIN version defv ON s.default_version_id=defv.id WHERE vi.day>='$first_previous_month-01' AND vi.day<='$last_previous_month-31' AND vi.views>0".($is_hentai ? " AND s.rating='XXX'" : " AND s.rating<>'XXX'")." AND f.episode_id IS NOT NULL AND s.type='$type'".(!empty($fansub) ? " AND f.version_id IN (SELECT version_id FROM rel_version_fansub WHERE fansub_id=".$fansub['id'].")" : '')." GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, total_length DESC, b.series_name ASC");
	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	$series_previous_month = array();
	while ($row = mysqli_fetch_assoc($result_previous_month)) {
		if ($row['max_views']!=$prev_views) {
			$prev_views = $row['max_views'];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}

		$current_series = array(
			'id' => $row['series_id'],
			'position' => $position,
			'max_views' => $row['max_views']
		);

		array_push($series_previous_month, $current_series);
	}
	mysqli_free_result($result_previous_month);

	$prev_views = 0;
	$position = 0;
	$current_positions = 0;
	$series = array();
	while ($row = mysqli_fetch_assoc($result)) {
		if ($row['max_views']!=$prev_views) {
			$prev_views = $row['max_views'];
			$position=$position+$current_positions+1;
			$current_positions = 0;
		} else {
			$current_positions++;
		}

		$current_series = array(
			'id' => $row['series_id'],
			'default_version_id' => $row['default_version_id'],
			'name' => $row['series_name'],
			'position' => $position,
			'rating' => $row['rating'],
			'fansubs' => implode(' / ',array_unique(explode(' / ',$row['fansubs']))),
			'views' => $row['max_views'].($row['max_views']==1 ? ' seguidor' : ' seguidors'),
			'change_in_position' => get_change_in_position_for_series($first_month, $row['series_id'], $position, $series_previous_month),
			'change_in_views' => get_change_in_views_for_series($first_month, $row['series_id'], $row['max_views'], $series_previous_month),
		);

		array_push($series, $current_series);
	}
	mysqli_free_result($result);

	//Empty canvas - we will draw here
	$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

	//Load bg and scale it as needed
	if (count($series)>0) {
		$background = imagecreatefromjpeg(STATIC_DIRECTORY."/images/featured/".$series[0]['default_version_id'].".jpg");
		$background = scale_smallest_side($background, IMAGE_WIDTH, IMAGE_HEIGHT);

		//Darken and blur bg
		$semi_transparent = imagecolorallocatealpha($background,0,0,0,30);
		imagefilledrectangle($background, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT, $semi_transparent);
		for ($i=0; $i<15; $i++) {
			imagefilter($background, IMG_FILTER_GAUSSIAN_BLUR);
		}

		//Paste into canvas
		imagecopy($image, $background, 0, 0, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT);
	} else {
		imagefttext($image, 26, 0, 300, 350, imagecolorallocate($image, 0xCC, 0xCC, 0xCC), FONT_REGULAR, "No hi ha res vist en aquest període.");
	}


	//Load logo
	$logo = imagecreatefrompng(STATIC_DIRECTORY."/images/site/logo_rasterized".($is_hentai ? '_hentai' : '').".png");
	$logo = scale_smallest_side($logo, LOGO_WIDTH, LOGO_HEIGHT);
	imagecopy($image, $logo, IMAGE_WIDTH - LOGO_WIDTH - 24, 10, 0, 0, LOGO_WIDTH, LOGO_HEIGHT);
	
	if (!empty($fansub)) {
		//Load fansub logo
		$fansub_logo = imagecreatefrompng(STATIC_DIRECTORY."/images/icons/".$fansub['id'].".png");
		$fansub_logo = scale_smallest_side($fansub_logo, FANSUB_LOGO_WIDTH, FANSUB_LOGO_HEIGHT);
		$fansub_logo = round_corners($fansub_logo, 8);
		imagecopy($image, $fansub_logo, IMAGE_WIDTH - LOGO_WIDTH - FANSUB_LOGO_WIDTH - 24 * 2, 10, 0, 0, FANSUB_LOGO_WIDTH, FANSUB_LOGO_HEIGHT);
	}

	switch ($type){
		case 'manga':
			$title="Mangues".($is_hentai ? ' hentai' : '')." més populars".(!empty($fansub) ? ' '.get_fansub_preposition_name($fansub['name']) : '');
			break;
		case 'liveaction':
			$title="Continguts d’imatge real més populars".(!empty($fansub) ? ' '.get_fansub_preposition_name($fansub['name']) : '');
			break;
		case 'anime':
		default:
			$title="Animes".($is_hentai ? ' hentai' : '')." més populars".(!empty($fansub) ? ' '.get_fansub_preposition_name($fansub['name']) : '');
			break;
	}
	setlocale(LC_ALL, 'ca_AD.utf8');

	switch($mode) {
		case 'all':
			$subtitle.='Total '.STARTING_YEAR.'-'.date('Y');
			break;
		case 'year':
			$subtitle.='Any '.strftime("%Y", strtotime(date($last_month.'-01')));
			break;
		case 'month':
		default:
			$subtitle.=ucfirst(str_replace('d’','', str_replace('de ','', strftime("%B %Y", strtotime(date($last_month.'-01'))))));
			break;
	}

	$current_height = 34;
	$gray = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
	$color = $is_hentai ? imagecolorallocate($image, 0xD9, 0x18, 0x83) : imagecolorallocate($image, 0x6A, 0xA0, 0xF8);
	$secondary_color = imagecolorallocate($image, 0xF9, 0xC0, 0x2B);
	$darker_gray = imagecolorallocate($image, 0x66, 0x66, 0x66);
	$not_so_darker_gray = imagecolorallocate($image, 0x99, 0x99, 0x99);
	$green = imagecolorallocate($image, 0x22, 0x99, 0x22);
	$red = imagecolorallocate($image, 0xBB, 0x44, 0x44);
	$yellow = imagecolorallocate($image, 0xBB, 0xBB, 0x44);
	$bbox = imageftbbox(26, 0, FONT_BOLD, $title);
	//$center = (imagesx($image) / 2) - (($bbox[2] - $bbox[0]) / 2);
	imagefttext($image, 26, 0, 24, $current_height, $gray, FONT_BOLD, $title);
	$current_height+=26;
	$bbox = imageftbbox(18, 0, FONT_BOLD, $subtitle);
	//$center = (imagesx($image) / 2) - (($bbox[2] - $bbox[0]) / 2);
	imagefttext($image, 18, 0, 24, $current_height, $gray, FONT_REGULAR, $subtitle);
	$current_height = 71;

	for ($i=0;$i<count($series);$i++) {
		switch($series[$i]['position']) {
			case 1:
				$extra_left=12;
				break;
			case 2:
				$extra_left=14;
				break;
			case 3:
				$extra_left=14;
				break;
			case 10:
				$extra_left=-10;
				break;
			default:
				$extra_left=14;
				break;
		}
		if($series[$i]['change_in_position']==='N/A') {
			$change_in_position_color=$yellow;
			$change_in_position_text="NOU";
		} else if ($series[$i]['change_in_position']>0) {
			$change_in_position_color=$green;
			$change_in_position_text="▲".$series[$i]['change_in_position'];
		} else if ($series[$i]['change_in_position']<0) {
			$change_in_position_color=$red;
			$change_in_position_text="▼".abs($series[$i]['change_in_position']);
		} else {
			$change_in_position_color=$gray;
			$change_in_position_text="=";
		}
		if($series[$i]['change_in_views']==='N/A') {
			$change_in_views_color=$yellow;
			$change_in_views_text="nou";
		} else if ($series[$i]['change_in_views']>0) {
			$change_in_views_color=$green;
			$change_in_views_text="▲".$series[$i]['change_in_views'];
		} else if ($series[$i]['change_in_views']<0) {
			$change_in_views_color=$red;
			$change_in_views_text="▼".abs($series[$i]['change_in_views']);
		} else {
			$change_in_views_color=$gray;
			$change_in_views_text="=";
		}
		imagefttext($image, 52, 0, ($i>4 ? 624 : 24)+$extra_left, $current_height+84, $color, FONT_NUMBERS, $series[$i]['position']);
		$bbox = imageftbbox(16, 0, FONT_NUMBERS, $change_in_position_text);
		$center = ($i>4 ? 624 : 24) + 72/2 - (($bbox[2]-$bbox[0])/2);
		if ($mode!='all') {
			imagefttext($image, 16, 0, $center-2, $current_height+112, $change_in_position_color, FONT_NUMBERS, $change_in_position_text);
		}
		$text = \andrewgjohnson\linebreaks4imagettftext(24, 0, FONT_REGULAR, $series[$i]['name'], 380);
		if (substr_count($text, "\n")>0) {
			$text = explode("\n", $text)[0].'...';
		}
		imagefttext($image, 24, 0, $i>4 ? 624+172 : 24+172, $current_height+40, $secondary_color, FONT_REGULAR, $text);
		$text = \andrewgjohnson\linebreaks4imagettftext(17, 0, FONT_REGULAR, $series[$i]['fansubs'], 380);
		if (substr_count($text, "\n")>0) {
			$text = explode("\n", $text)[0].'...';
		}
		imagefttext($image, 17, 0, $i>4 ? 624+172 : 24+172, $current_height+68, $not_so_darker_gray, FONT_REGULAR, $text);
		imagefttext($image, 20, 0, $i>4 ? 624+172 : 24+172, $current_height+102, $gray, FONT_REGULAR, $series[$i]['views']/*.($mode!='all' ? ' (' : '')*/);
		/*if ($mode!='all') {
			$bbox = imageftbbox(20, 0, FONT_REGULAR, $series[$i]['views'].' (');
			$views_change_position = ($i>4 ? 624+172 : 24+172) + ($bbox[2]-$bbox[0]);
			imagefttext($image, 20, 0, $views_change_position, $current_height+102, $change_in_views_color, FONT_NUMBERS, $change_in_views_text);
			$bbox = imageftbbox(20, 0, FONT_NUMBERS, $change_in_views_text);
			$views_change_position = $views_change_position + ($bbox[2]-$bbox[0]);
			imagefttext($image, 20, 0, $views_change_position, $current_height+102, $gray, FONT_REGULAR, ')');
		}*/

		//Load cover and scale it as needed
		$cover = imagecreatefromjpeg(STATIC_DIRECTORY."/images/covers/".$series[$i]['default_version_id'].".jpg");
		$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);
		$cover = round_corners($cover, 4);
		imagecopy($image, $cover, $i>4 ? 624+72 : 24+72, $current_height, 0, 0, COVER_WIDTH, COVER_HEIGHT);
		$current_height = $current_height+COVER_HEIGHT;
		if (($i>=0 && $i<4) || ($i>=5 && $i<9)) {
			//imageline($image, $i>4 ? 624 : 24, $current_height, $i>4 ? IMAGE_WIDTH-24 : 600-24, $current_height, $darker_gray);
			$current_height = $current_height+5;
		} else if ($i==4){
			$current_height = 71;
		}
	}

	header('Content-Type: image/jpeg');
	imagejpeg($image, NULL, 100);
	imagedestroy($image);
} else {
	header("Location: login.php");
}

ob_flush();
mysqli_close($db_connection);
?>
