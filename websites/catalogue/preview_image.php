<?php
require_once('../common.fansubs.cat/db.inc.php');
require_once('common.inc.php');
require_once('queries.inc.php');
require_once('libraries/linebreaks4imagettftext.php');
define('IMAGE_WIDTH', 1200);
define('IMAGE_HEIGHT', 628);
define('COVER_WIDTH', 444);
define('COVER_HEIGHT', 628);
define('TEXT_MARGIN', 46);
define('FONT_REGULAR', STATIC_DIRECTORY.'/fonts/lexend_deca_regular.ttf');
define('FONT_BOLD', STATIC_DIRECTORY.'/fonts/lexend_deca_bold.ttf');
define('FONT_LIGHT', STATIC_DIRECTORY.'/fonts/lexend_deca_light.ttf');

//Obtained from running: `fc-query --format='%{charset}\n' font.ttf`
define('SUPPORTED_CHARS_REGEX', '/[^\n\x{20}-\x{7e}\x{a0}-\x{17e}\x{18f}\x{192}\x{19d}\x{1a0}-\x{1a1}\x{1af}-\x{1b0}\x{1c4}-\x{1d4}\x{1e6}-\x{1e7}\x{1ea}-\x{1eb}\x{1f1}-\x{1f2}\x{1fa}-\x{21b}\x{22a}-\x{22d}\x{230}-\x{233}\x{237}\x{259}\x{272}\x{2bb}-\x{2bc}\x{2be}-\x{2bf}\x{2c6}-\x{2c8}\x{2cc}\x{2d8}-\x{2dd}\x{300}-\x{304}\x{306}-\x{30c}\x{30f}\x{311}-\x{312}\x{31b}\x{323}-\x{324}\x{326}-\x{328}\x{32e}\x{331}\x{335}\x{394}\x{3a9}\x{3bc}\x{3c0}\x{1e08}-\x{1e09}\x{1e0c}-\x{1e0f}\x{1e14}-\x{1e17}\x{1e1c}-\x{1e1d}\x{1e20}-\x{1e21}\x{1e24}-\x{1e25}\x{1e2a}-\x{1e2b}\x{1e2e}-\x{1e2f}\x{1e36}-\x{1e37}\x{1e3a}-\x{1e3b}\x{1e42}-\x{1e49}\x{1e4c}-\x{1e53}\x{1e5a}-\x{1e5b}\x{1e5e}-\x{1e69}\x{1e6c}-\x{1e6f}\x{1e78}-\x{1e7b}\x{1e80}-\x{1e85}\x{1e8e}-\x{1e8f}\x{1e92}-\x{1e93}\x{1e97}\x{1e9e}\x{1ea0}-\x{1ef9}\x{2007}-\x{200b}\x{2010}\x{2012}-\x{2015}\x{2018}-\x{201a}\x{201c}-\x{201e}\x{2020}-\x{2022}\x{2026}\x{2030}\x{2033}\x{2039}-\x{203a}\x{2044}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{20a1}\x{20a3}-\x{20a4}\x{20a6}-\x{20a7}\x{20a9}\x{20ab}-\x{20ad}\x{20b1}-\x{20b2}\x{20b5}\x{20b9}-\x{20ba}\x{20bc}-\x{20bd}\x{2113}\x{2116}\x{2122}\x{2126}\x{212e}\x{215b}-\x{215e}\x{2202}\x{2205}-\x{2206}\x{220f}\x{2211}-\x{2212}\x{2215}\x{2219}-\x{221a}\x{221e}\x{222b}\x{2248}\x{2260}\x{2264}-\x{2265}\x{25ca}\x{fb01}-\x{fb02}]/u');

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
			return imagecolorallocate($image, 0xFF, 0x7F, 0x50);
		case 5:
			return imagecolorallocate($image, 0xFF, 0x00, 0x00);
		default:
			return imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
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

function get_text_without_missing_glyphs($text) {
	return preg_replace(SUPPORTED_CHARS_REGEX, ' ', $text);
}

function get_series_type_summary($series) {
	$text='';
	if ($series['type']=='manga') {
		if ($series['subtype']=='oneshot') {
			$text = get_comic_type($series['comic_type'])." • One-shot";
		} else if ($series['divisions']>1) {
			$text = get_comic_type($series['comic_type'])." • Serialitzat • ".$series['divisions']." volums • ".($series['number_of_episodes']==-1 ? 'En publicació' : $series['number_of_episodes']." capítols");
		} else {
			$text = get_comic_type($series['comic_type'])." • Serialitzat • 1 volum • ".($series['number_of_episodes']==-1 ? 'En publicació' : $series['number_of_episodes']." capítols");
		}
	} else {
		if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
			$text = ($series['type']=='anime' ? 'Anime' : 'Imatge real')." • Conjunt de ".$series['number_of_episodes']." films";
		} else if ($series['subtype']=='movie') {
			$text = ($series['type']=='anime' ? 'Anime' : 'Imatge real')." • Film";
		} else if ($series['divisions']>1) {
			$text = ($series['type']=='anime' ? 'Anime' : 'Imatge real')." • Sèrie • ".$series['divisions']." temporades • ".($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes']." capítols");
		} else {
			$text = ($series['type']=='anime' ? 'Anime' : 'Imatge real')." • Sèrie • ".($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes']." capítols");
		}
	}
	return $text;
}

$result = query_series_data_for_preview_image_by_slug(!empty($_GET['slug']) ? $_GET['slug'] : '');
$series = mysqli_fetch_assoc($result) or $failed=TRUE;
mysqli_free_result($result);

if (empty($failed)) {
	$id = $series['id'];
	if (TRUE) { //!file_exists(STATIC_DIRECTORY."/social/series_".$id.".jpg") || filemtime(STATIC_DIRECTORY."/social/series_".$id.".jpg")<(date('U')-3600*8)) {
		$result = query_version_data_for_preview_image_by_series_id($id);
		$versions = array();
		while ($version = mysqli_fetch_assoc($result)) {
			$versions[] = $version;
		}
		mysqli_free_result($result);

		//Empty canvas - we will draw here
		$image = imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT);

		//Load cover and scale it as needed
		$cover = imagecreatefromjpeg(STATIC_DIRECTORY."/images/covers/$id.jpg");
		$cover = scale_smallest_side($cover, COVER_WIDTH, COVER_HEIGHT);

		//Load bg and scale it as needed
		$background = imagecreatefromjpeg(STATIC_DIRECTORY."/images/featured/$id.jpg");
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
		$text = get_series_type_summary($series);

		$gray = imagecolorallocate($image, 0xDC, 0xDC, 0xDC);
		imagefttext($image, 23.5, 0, TEXT_MARGIN, $current_height, $gray, FONT_REGULAR, get_text_without_missing_glyphs($text));
		$current_height = $current_height+90;

		//Name
		$text = \andrewgjohnson\linebreaks4imagettftext(42, 0, FONT_BOLD, get_text_without_missing_glyphs($series['name']), IMAGE_WIDTH-COVER_WIDTH-(TEXT_MARGIN+4)*2);
		if (substr_count($text, "\n")>2) {
			$text = implode("\n",array_slice(explode("\n", $text), 0, 3)).'…';
		}
		$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		for ($i=0;$i<=substr_count($text, "\n");$i++) {
			imagefttext($image, 42, 0, TEXT_MARGIN-4, $current_height, $white, FONT_BOLD, explode("\n", get_text_without_missing_glyphs($text))[$i]);
			$current_height = $current_height+54;
		}
			$current_height = $current_height-2;

		//Alternate names
		if (!empty($series['alternate_names'])) {
			$text = \andrewgjohnson\linebreaks4imagettftext(23.5, 0, FONT_LIGHT, get_text_without_missing_glyphs($series['alternate_names']), IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2);
			if (substr_count($text, "\n")>1) {
				$text = implode("\n",array_slice(explode("\n", $text), 0, 2)).'…';
			}
			$yellow = imagecolorallocate($image, 0xA3, 0xA3, 0xA3);
			for ($i=0;$i<=substr_count($text, "\n");$i++) {
				imagefttext($image, 23.5, 0, TEXT_MARGIN, $current_height, $yellow, FONT_LIGHT, explode("\n", get_text_without_missing_glyphs($text))[$i]);
				$current_height = $current_height+38;
			}
		} else {
			$current_height = $current_height-12;
		}

		//Score
		$score=$series['score'];

		if (!empty($score)) {
			$orange = imagecolorallocate($image, 0xFF, 0xC1, 0x00);
			imagefttext($image, 48, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-150, IMAGE_HEIGHT-TEXT_MARGIN, $orange, FONT_BOLD, number_format($score, 2, ',',' '));
			imagefttext($image, 17, 0, IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN-10, IMAGE_HEIGHT-TEXT_MARGIN-14, $white, FONT_REGULAR, "/10");
		}

		//Genres
		$current_width = TEXT_MARGIN+10;
		$genres = !empty($series['genres']) ? explode(', ',$series['genres']) : array();

		$tag_bg_color = imagecolorallocatealpha($image, 0xFF, 0xFF, 0xFF, 95);
		$lines = 1;
		//First iteration to know the number of lines
		foreach ($genres as $genre) {
			$bbox = imagettfbbox(14, 0, FONT_LIGHT, $genre);
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
			$bbox = imagettfbbox(14, 0, FONT_LIGHT, $genre);
			$fits = ($current_width+$bbox[2])<(IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN*2-8*2-10-140);
			if (!$fits) {
				$current_width = TEXT_MARGIN+10;
				$current_height = $current_height+36;
			}
			imageroundedrectangle($image, $current_width-8, $current_height-20, $current_width+$bbox[2]+8, $current_height+8, 14, $tag_bg_color);
			imagefttext($image, 14, 0, $current_width+2, $current_height, $white, FONT_LIGHT, get_text_without_missing_glyphs($genre));
			$current_width+=$bbox[2]+6*2+12;
		}

		//Fansubs
		$current_height = $current_height-36*($lines-1)-20;
		$current_fansub_line = 0;

		foreach ($versions as $version) {
			$text = \andrewgjohnson\linebreaks4imagettftext(17, 0, FONT_REGULAR, get_text_without_missing_glyphs($version['fansub_name']), IMAGE_WIDTH-COVER_WIDTH-TEXT_MARGIN);
			$current_height = $current_height - 30;
			imagefttext($image, 50.5, 0, TEXT_MARGIN, $current_height+16, imagecolorallocate($image, 0xFF, 0xFF, 0xFF), FONT_REGULAR, "•");
			imagefttext($image, 42, 0, TEXT_MARGIN+2, $current_height+12, get_status_color($image, $version['status']), FONT_REGULAR, "•");
			if (substr_count($text, "\n")>0) {
				$text = implode("\n",array_slice(explode("\n", $text), 0, 1)).'…';
			}
			imagefttext($image, 17, 0, TEXT_MARGIN+28, $current_height, $white, FONT_REGULAR, get_text_without_missing_glyphs($text));
			$current_fansub_line++;
		}
		imagejpeg($image, STATIC_DIRECTORY."/social/series_".$id.".jpg", 80);
		imagedestroy($image);
	}

	header('Content-Type: image/jpeg');
	readfile(STATIC_DIRECTORY."/social/series_".$id.".jpg");
} else {
	echo "Aquest element no existeix.";
}
?>
