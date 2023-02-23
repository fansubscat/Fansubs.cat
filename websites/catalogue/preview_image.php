<?php
require_once('../common.fansubs.cat/db.inc.php');
require_once('libraries/linebreaks4imagettftext.php');
const IMAGE_WIDTH = 1200;
const IMAGE_HEIGHT = 628;
const COVER_WIDTH = 444;
const COVER_HEIGHT = 628;
const TEXT_MARGIN = 46;
$font = $static_directory.'/common/fonts/lexend_deca_regular.ttf';
$font_bold = $static_directory.'/common/fonts/lexend_deca_bold.ttf';
$font_light = $static_directory.'/common/fonts/lexend_deca_light.ttf';

ob_start();

function scale_smallest_side($image, $desired_width, $desired_height) {
	$width = imagesx($image);
	$height = imagesy($image);

	if ($width/$height < $desired_width/$desired_height) {
		$output_width = $desired_width;
		$output_height = $desired_width*$height/$width;
		$image = imagescale($image, $output_width, $output_height, IMG_BILINEAR_FIXED);
		$image = imagecrop($image, ['x' => 0, 'y' => ($output_height-$desired_height)/2, 'width' => $desired_width, 'height' => $desired_height]);
	} else {
		$output_width = $desired_height*$width/$height;
		$output_height = $desired_height;
		$image = imagescale($image, $output_width, $output_height, IMG_BILINEAR_FIXED);
		$image = imagecrop($image, ['x' => ($output_width-$desired_width)/2, 'y' => 0, 'width' => $desired_width, 'height' => $desired_height]);
	}
	
	return $image;
}

function get_status_color($image, $id){
	switch ($id){
		case 1:
			return imagecolorallocate($image, 0x00, 0x80, 0x00);
		case 2:
			return imagecolorallocate($image, 0xFF, 0xFF, 0x00);
		case 3:
			return imagecolorallocate($image, 0xAD, 0xFF, 0x2F);
		case 4:
		case 5:
			return imagecolorallocate($image, 0xFF, 0x00, 0x00);
		default:
			return imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	}
}

function get_comic_type($comic_type){
	switch ($comic_type) {
		case 'manga':
			return 'Manga';
		case 'manhwa':
			return 'Manhwa';
		case 'manhua':
			return 'Manhua';
		default:
			return 'Còmic';
	}
}

//Taken from (and modified): https://gist.github.com/mistic100/9301c0eebaef047bfdc8?permalink_comment_id=3043071#gistcomment-3043071
function imageroundedrectangle(&$img, $x1, $y1, $x2, $y2, $r, $color) {
	$r = min($r, floor(min(($x2 - $x1) / 2, ($y2 - $y1) / 2)));
	// render corners
	imagefilledarc($img, $x1 + $r-1, $y1 + $r-1, $r * 2, $r * 2, 180, 270, $color, IMG_ARC_PIE);
	imagefilledarc($img, $x2 - $r+1, $y1 + $r-1, $r * 2, $r * 2, 270, 0, $color, IMG_ARC_PIE);
	imagefilledarc($img, $x2 - $r+1, $y2 - $r, $r * 2, $r * 2, 0, 90, $color, IMG_ARC_PIE);
	imagefilledarc($img, $x1 + $r-1, $y2 - $r, $r * 2, $r * 2, 90, 180, $color, IMG_ARC_PIE);
	imagefilledrectangle($img, $x1+$r, $y1-1, $x2-$r, $y2, $color);
}

//Taken from (and modified): https://www.php.net/manual/en/function.imagefilledrectangle.php#65557
function gradient_region(&$img, $x, $y, $width, $height,$src_color, $dest_color, $src_alpha, $dest_alpha){
	$src_red = ($src_color & 0xFF0000) >> 16;
	$src_green = ($src_color & 0x00FF00) >> 8;
	$src_blue = ($src_color & 0x0000FF);

	$dest_red = ($dest_color & 0xFF0000) >> 16;
	$dest_green = ($dest_color & 0x00FF00) >> 8;
	$dest_blue = ($dest_color & 0x0000FF);

	$inc_alpha = ($dest_alpha - $src_alpha) / $width;
	$inc_red = ($dest_red - $src_red)/$width;
	$inc_green = ($dest_green - $src_green)/$width;
	$inc_blue = ($dest_blue - $src_blue)/$width;

	// If you need more performance, the step can be increased
	for ($i=0;$i<$width;$i++) {
		$src_alpha += $inc_alpha;
		$src_blue += $inc_blue;
		$src_green += $inc_green;
		$src_red += $inc_red;
		imagefilledrectangle($img, $x+$i,$y, $x+$i,$y+$height, imagecolorallocatealpha($img, $src_red,$src_green,$src_blue,$src_alpha));
	}
}

$result = query("SELECT s.*, YEAR(s.publish_date) year, GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') genres, (SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions FROM series s LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='${cat_config['items_type']}' AND slug='".escape(!empty($_GET['slug']) ? $_GET['slug'] : '')."' GROUP BY s.id");
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);

if (empty($failed)) {
	$id = $series['id'];
	if (TRUE) {//!file_exists($static_directory."/social/series_v5_".$id.".jpg") || filemtime($static_directory."/social/series_v5_".$id.".jpg")<(date('U')-3600*8)) {
		$result = query("SELECT v.*, GROUP_CONCAT(DISTINCT IF(v.version_author IS NULL OR f.id<>$default_fansub_id, f.name, CONCAT(f.name, ' (', v.version_author, ')')) ORDER BY IF(v.version_author IS NULL OR f.id<>$default_fansub_id, f.name, CONCAT(f.name, ' (', v.version_author, ')')) SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE v.is_hidden=0 AND v.series_id=$id GROUP BY v.id ORDER BY v.status DESC, v.created DESC");
		$versions = array();
		while ($version = mysqli_fetch_assoc($result)) {
			$versions[] = $version;
		}
		mysqli_free_result($result);

		//Empty canvas - we will draw here
		$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

		//Load cover and scale it as needed
		$cover = imagecreatefromjpeg($static_directory."/images/covers/$id.jpg");
		$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);

		//Load bg and scale it as needed
		$background = imagecreatefromjpeg($static_directory."/images/featured/$id.jpg");
		$background = scale_smallest_side($background, IMAGE_WIDTH, IMAGE_HEIGHT);

		//Darken and blur bg
		$semi_transparent = imagecolorallocatealpha($background,0,0,0,30);
		imagefilledrectangle($background, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT, $semi_transparent);
		for ($i=0; $i<15; $i++) {
			imagefilter($background, IMG_FILTER_GAUSSIAN_BLUR);
		}

		//Paste into canvas
		imagecopy($image, $background, 0, 0, 0, 0, IMAGE_WIDTH, IMAGE_HEIGHT);
		imagecopy($image, $cover, IMAGE_WIDTH-COVER_WIDTH, 0, 0, 0, COVER_WIDTH, IMAGE_HEIGHT);

		$gradient_from_color = imagecolorallocatealpha($image,0,0,0,100);
		$gradient_to_color = imagecolorallocatealpha($image,0,0,0,0);
		gradient_region($image, IMAGE_WIDTH-COVER_WIDTH-80, 0, 80, IMAGE_HEIGHT, $gradient_from_color, $gradient_to_color, 128, 0);

		$current_height = TEXT_MARGIN+34;

		//Type
		if ($series['type']=='manga') {
			if ($series['subtype']=='oneshot') {
				$text = get_comic_type($series['comic_type'])." · One-shot";
			} else if ($series['divisions']>1) {
				$text = get_comic_type($series['comic_type'])." · Serialitzat · ".$series['divisions']." volums · ".($series['number_of_episodes']==-1 ? 'En publicació' : $series['number_of_episodes'].' capítols');
			} else {
				$text = get_comic_type($series['comic_type'])." · Serialitzat · 1 volum · ".($series['number_of_episodes']==-1 ? 'En publicació' : $series['number_of_episodes'].' capítols');
			}
		} else {
			if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
				$text = $cat_config['preview_prefix']." · Conjunt de ".$series['number_of_episodes']." films";
			} else if ($series['subtype']=='movie') {
				$text = $cat_config['preview_prefix']." · Film";
			} else if ($series['divisions']>1) {
				$text = $cat_config['preview_prefix']." · Sèrie · ".$series['divisions']." temporades · ".($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols');
			} else {
				$text = $cat_config['preview_prefix']." · Sèrie · ".($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols');
			}
		}

		$gray = imagecolorallocate($image, 0xDC, 0xDC, 0xDC);
		imagefttext($image, 23.5, 0, TEXT_MARGIN, $current_height, $gray, $font, $text);
		$current_height = $current_height+90;

		//Name
		$text = \andrewgjohnson\linebreaks4imagettftext(42, 0, $font_bold, $series['name'], IMAGE_WIDTH-COVER_WIDTH-(TEXT_MARGIN+4)*2);
		if (substr_count($text, "\n")>2) {
			$text = implode("\n",array_slice(explode("\n", $text), 0, 3)).'…';
		}
		$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		for ($i=0;$i<=substr_count($text, "\n");$i++) {
			imagefttext($image, 42, 0, TEXT_MARGIN-4, $current_height, $white, $font_bold, explode("\n", $text)[$i]);
			$current_height = $current_height+54;
		}
			$current_height = $current_height-2;

		//Alternate names
		if (!empty($series['alternate_names'])) {
			$text = \andrewgjohnson\linebreaks4imagettftext(23.5, 0, $font_light, $series['alternate_names'], IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2);
			if (substr_count($text, "\n")>1) {
				$text = implode("\n",array_slice(explode("\n", $text), 0, 2)).'…';
			}
			$yellow = imagecolorallocate($image, 0xA3, 0xA3, 0xA3);
			for ($i=0;$i<=substr_count($text, "\n");$i++) {
				imagefttext($image, 23.5, 0, TEXT_MARGIN, $current_height, $yellow, $font_light, explode("\n", $text)[$i]);
				$current_height = $current_height+38;
			}
		} else {
			$current_height = $current_height-12;
		}

		//Score
		$score=$series['score'];

		if (!empty($score)) {
			$orange = imagecolorallocate($image, 0xFF, 0xC1, 0x00);
			imagefttext($image, 48, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-150, IMAGE_HEIGHT-TEXT_MARGIN, $orange, $font_bold, number_format($score, 2, ',',' '));
			imagefttext($image, 17, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-10, IMAGE_HEIGHT-TEXT_MARGIN-14, $white, $font, "/10");
		}

		//Genres
		$current_width = TEXT_MARGIN+10;
		$genres = !empty($series['genres']) ? explode(', ',$series['genres']) : array();

		$tag_bg_color = imagecolorallocatealpha($image, 0xFF, 0xFF, 0xFF, 95);
		$lines = 1;
		//First iteration to know the number of lines
		foreach ($genres as $genre) {
			$bbox = imagettfbbox(14, 0, $font_light, $genre);
			$fits = ($current_width+$bbox[2])<(IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2-8*2-10-140);
			if (!$fits) {
				$current_width = TEXT_MARGIN+10;
				$lines++;
			}
			$current_width+=$bbox[2]+6*2+12;
		}

		$current_height = IMAGE_HEIGHT-TEXT_MARGIN-9-36*($lines-1);
		$current_width = TEXT_MARGIN+10;
		foreach ($genres as $genre) {
			$bbox = imagettfbbox(14, 0, $font_light, $genre);
			$fits = ($current_width+$bbox[2])<(IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2-8*2-10-140);
			if (!$fits) {
				$current_width = TEXT_MARGIN+10;
				$current_height = $current_height+36;
			}
			imageroundedrectangle($image, $current_width-8, $current_height-20, $current_width+$bbox[2]+8, $current_height+8, 14, $tag_bg_color);
			imagefttext($image, 14, 0, $current_width+2, $current_height, $white, $font_light, $genre);
			$current_width+=$bbox[2]+6*2+12;
		}

		//Fansubs
		$current_height = $current_height-36*($lines-1)-20;
		$current_fansub_line = 0;

		foreach ($versions as $version) {
			$text = \andrewgjohnson\linebreaks4imagettftext(17, 0, $font, $version['fansub_name'], IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN);
			$current_height = $current_height - 30;
			imagefttext($image, 46, 0, TEXT_MARGIN, $current_height+13, get_status_color($image, $version['status']), $font, "•");
			if (substr_count($text, "\n")>0) {
				$text = implode("\n",array_slice(explode("\n", $text), 0, 1)).'…';
			}
			imagefttext($image, 17, 0, TEXT_MARGIN+24, $current_height, $white, $font, $text);
			$current_fansub_line++;
		}
		imagejpeg($image, $static_directory."/social/series_v5_".$id.".jpg", 80);
		imagedestroy($image);
	}

	header('Content-Type: image/jpeg');
	readfile($static_directory."/social/series_v5_".$id.".jpg");
} else {
	echo "Aquest element no existeix.";
}
?>
