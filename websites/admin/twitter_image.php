<?php
const IMAGE_WIDTH = 1200;
const IMAGE_HEIGHT = 675;
const COVER_WIDTH = 85;
const COVER_HEIGHT = 120;
const TEXT_MARGIN = 48;
const FONT = 'style/patinio_neue.ttf';

ob_start();
require_once("db.inc.php");
require_once('libraries/linebreaks4imagettftext.php');

function scale_smallest_side($image, $desired_width, $desired_height) {
	$width = imagesx($image);
	$height = imagesy($image);

	if ($width/$height < $desired_width/$desired_height) {
		$output_width = $desired_width;
		$output_height = $desired_width*$height/$width;
		$image = imagescale($image, $output_width, $output_height, IMG_BICUBIC);
		$image = imagecrop($image, ['x' => 0, 'y' => ($output_height-$desired_height)/2, 'width' => $desired_width, 'height' => $desired_height]);
	} else {
		$output_width = $desired_height*$width/$height;
		$output_height = $desired_height;
		$image = imagescale($image, $output_width, $output_height, IMG_BICUBIC);
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
	$result = query("SELECT IFNULL(MIN(f.created),'2020-06-01 00:00:00') min FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.id=$id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	if ($row['min']>=($current_month.'-01 00:00:00')) {
		return "N/A";
	} else {
		return $max_pos+1;
	}
}

function get_change_in_views_for_series($current_month, $id, $new_views, $series_previous_month) {
	foreach ($series_previous_month as $series) {
		if ($series['id']==$id){
			return $new_views-$series['max_views'];
		}
	}
	$result = query("SELECT IFNULL(MIN(f.created),'2020-06-01 00:00:00') min FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.id=$id");
	$row = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	if ($row['min']>=($current_month.'-01 00:00:00')) {
		return "N/A";
	} else {
		return 0;
	}
}

session_start();

if ((!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) || $_GET['token']==$internal_token) {

	$type = escape($_GET['type']);
	$month = escape($_GET['month']);
	$previous_month = date('Y-m', strtotime($month.'-01 first day of -1 month'));
	$hide_hentai = FALSE;
	if (isset($_GET['hide_hentai']) && $_GET['hide_hentai']==1) {
		$hide_hentai = TRUE;
	}

	$result = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.time_spent) time_spent, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.time_spent) time_spent, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.time_spent) time_spent, f.version_id, f.episode_id, s.id series_id, s.name series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE vi.day>='$month-01' AND vi.day<='$month-31' AND vi.views>0".($hide_hentai ? " AND (s.rating IS NULL OR s.rating<>'XXX')" : '')." AND f.episode_id IS NOT NULL AND s.type='$type' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, b.series_name ASC LIMIT 10");

	if (mysqli_num_rows($result)==0) {
		echo "No hi ha cap sèrie vista.";
	} else {
		$result_previous_month = query("SELECT GROUP_CONCAT(DISTINCT b.fansubs SEPARATOR ' / ') fansubs, b.series_id, b.series_name, IFNULL(MAX(b.total_views),0) max_views, SUM(b.time_spent) time_spent, b.rating FROM (SELECT GROUP_CONCAT(DISTINCT a.fansubs SEPARATOR ' / ') fansubs, a.series_id, a.series_name, a.episode_id, SUM(a.views) total_views, SUM(a.time_spent) time_spent, a.rating FROM (SELECT (SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ') FROM rel_version_fansub svf LEFT JOIN fansub sf ON sf.id=svf.fansub_id WHERE svf.version_id=f.version_id) fansubs, SUM(vi.views) views, SUM(vi.time_spent) time_spent, f.version_id, f.episode_id, s.id series_id, s.name series_name, s.rating rating FROM file f LEFT JOIN views vi ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE vi.day>='$previous_month-01' AND vi.day<='$previous_month-31' AND vi.views>0".($hide_hentai ? " AND (s.rating IS NULL OR s.rating<>'XXX')" : '')." AND f.episode_id IS NOT NULL AND s.type='$type' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, b.series_name ASC");
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
				'name' => $row['series_name'],
				'position' => $position,
				'rating' => $row['rating'],
				'fansubs' => implode(' / ',array_unique(explode(' / ',$row['fansubs']))),
				'views' => $row['max_views'].($row['max_views']==1 ? ' seguidor' : ' seguidors'),
				'change_in_position' => get_change_in_position_for_series($month, $row['series_id'], $position, $series_previous_month),
				'change_in_views' => get_change_in_views_for_series($month, $row['series_id'], $row['max_views'], $series_previous_month),
			);

			array_push($series, $current_series);
		}
		mysqli_free_result($result);

		//Empty canvas - we will draw here
		$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

		//Load bg and scale it as needed
		$background = imagecreatefromjpeg($static_directory."/images/featured/".$series[0]['id'].".jpg");
		$background = scale_smallest_side($background, IMAGE_WIDTH, IMAGE_HEIGHT);

		//Darken and blur bg
		$semi_transparent = imagecolorallocatealpha($background,0,0,0,30);
		imagefilledrectangle($background, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT, $semi_transparent);
		for ($i=0; $i<15; $i++) {
			imagefilter($background, IMG_FILTER_GAUSSIAN_BLUR);
		}

		//Paste into canvas
		imagecopy($image, $background, 0, 0, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT);

		switch ($type){
			case 'manga':
				$title="Els mangues més populars a Fansubs.cat - ";
				break;
			case 'liveaction':
				$title="Els continguts d'acció real més populars a Fansubs.cat - ";
				break;
			case 'anime':
			default:
				$title="Els animes més populars a Fansubs.cat - ";
				break;
		}
		setlocale(LC_ALL, 'ca_ES.utf8');
		$title.=ucfirst(str_replace('d’','', str_replace('de ','', strftime("%B %Y", strtotime(date($month.'-01'))))));

		$current_height = TEXT_MARGIN;
		$gray = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
		$gold = imagecolorallocate($image, 0xE4, 0xBF, 0x47);
		$silver = imagecolorallocate($image, 0xCC, 0xCC, 0xCC);
		$bronze = imagecolorallocate($image, 0xCD, 0x7F, 0x32);
		$other = imagecolorallocate($image, 0x88, 0x88, 0xBB);
		$darker_gray = imagecolorallocate($image, 0x66, 0x66, 0x66);
		$not_so_darker_gray = imagecolorallocate($image, 0x99, 0x99, 0x99);
		$green = imagecolorallocate($image, 0x22, 0x99, 0x22);
		$red = imagecolorallocate($image, 0xBB, 0x44, 0x44);
		$yellow = imagecolorallocate($image, 0xBB, 0xBB, 0x44);
		$reddish = imagecolorallocate($image, 0xFF, 0xAA, 0xAA);
		$bbox = imageftbbox(32, 0, FONT, $title);
		$center = (imagesx($image) / 2) - (($bbox[2] - $bbox[0]) / 2);
		imagefttext($image, 32, 0, $center, $current_height, $gray, FONT, $title);
		$current_height = 71;

		for ($i=0;$i<count($series);$i++) {
			switch($series[$i]['position']) {
				case 1:
					$color = $gold;
					$extra_left=12;
					break;
				case 2:
					$color = $silver;
					$extra_left=14;
					break;
				case 3:
					$color = $bronze;
					$extra_left=14;
					break;
				case 10:
					$color = $other;
					$extra_left=-10;
					break;
				default:
					$color = $other;
					$extra_left=14;
					break;
			}
			if($series[$i]['change_in_position']==='N/A') {
				$change_in_position_color=$yellow;
				$change_in_position_text="NOU";
			} else if ($series[$i]['change_in_position']>0) {
				$change_in_position_color=$green;
				$change_in_position_text="▲ ".$series[$i]['change_in_position'];
			} else if ($series[$i]['change_in_position']<0) {
				$change_in_position_color=$red;
				$change_in_position_text="▼ ".abs($series[$i]['change_in_position']);
			} else {
				$change_in_position_color=$gray;
				$change_in_position_text="=";
			}
			if($series[$i]['change_in_views']==='N/A') {
				$change_in_views_color=$yellow;
				$change_in_views_text="nou";
			} else if ($series[$i]['change_in_views']>0) {
				$change_in_views_color=$green;
				$change_in_views_text="▲ ".$series[$i]['change_in_views'];
			} else if ($series[$i]['change_in_views']<0) {
				$change_in_views_color=$red;
				$change_in_views_text="▼ ".abs($series[$i]['change_in_views']);
			} else {
				$change_in_views_color=$gray;
				$change_in_views_text="=";
			}
			imagefttext($image, 60, 0, ($i>4 ? 624 : 24)+$extra_left, $current_height+88, $color, FONT, $series[$i]['position']);
			$bbox = imageftbbox(16, 0, FONT, $change_in_position_text);
			$center = ($i>4 ? 624 : 24) + 72/2 - (($bbox[2]-$bbox[0])/2);
			imagefttext($image, 16, 0, $center-2, $current_height+112, $change_in_position_color, FONT, $change_in_position_text);
			$text = \andrewgjohnson\linebreaks4imagettftext(28, 0, FONT, $series[$i]['name'], 380);
			if (substr_count($text, "\n")>0) {
				$text = explode("\n", $text)[0].'...';
			}
			imagefttext($image, 28, 0, $i>4 ? 624+172 : 24+172, $current_height+40, ($series[$i]['rating']=='XXX' ? $reddish : $gray), FONT, $text);
			$text = \andrewgjohnson\linebreaks4imagettftext(22, 0, FONT, $series[$i]['fansubs'], 380);
			if (substr_count($text, "\n")>0) {
				$text = explode("\n", $text)[0].'...';
			}
			imagefttext($image, 22, 0, $i>4 ? 624+172 : 24+172, $current_height+74, $not_so_darker_gray, FONT, $text);
			imagefttext($image, 22, 0, $i>4 ? 624+172 : 24+172, $current_height+108, $gray, FONT, $series[$i]['views'].' (');
			
			$bbox = imageftbbox(22, 0, FONT, $series[$i]['views'].' (');
			$views_change_position = ($i>4 ? 624+172 : 24+172) + ($bbox[2]-$bbox[0]);
			imagefttext($image, 22, 0, $views_change_position, $current_height+108, $change_in_views_color, FONT, $change_in_views_text);
			$bbox = imageftbbox(22, 0, FONT, $change_in_views_text);
			$views_change_position = $views_change_position + ($bbox[2]-$bbox[0]);
			imagefttext($image, 22, 0, $views_change_position, $current_height+108, $gray, FONT, ')');

			//Load cover and scale it as needed
			$cover = imagecreatefromjpeg($static_directory."/images/covers/".$series[$i]['id'].".jpg");
			$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);
			imagecopy($image, $cover, $i>4 ? 624+72 : 24+72, $current_height, 0, 0, COVER_WIDTH, COVER_HEIGHT);
			$current_height = $current_height+COVER_HEIGHT;
			if (($i>=0 && $i<4) || ($i>=5 && $i<9)) {
				imageline($image, $i>4 ? 624 : 24, $current_height, $i>4 ? IMAGE_WIDTH-24 : 600-24, $current_height, $darker_gray);
				$current_height = $current_height+1;
			} else if ($i==4){
				$current_height = 71;
			}
		}

		header('Content-Type: image/jpeg');
		imagejpeg($image, NULL, 100);
		imagedestroy($image);
	}
} else {
	header("Location: login.php");
}

ob_flush();
mysqli_close($db_connection);
?>
